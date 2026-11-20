from pathlib import Path

script = r'''#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Audit file DBF aplikasi ATK lama sebelum migrasi ke Laravel.
Tidak mengubah database atau file DBF. Hanya membaca DBF dan membuat laporan.

Instalasi:
    python -m pip install dbfread

Contoh:
    python audit_dbf.py --source "C:\\xampp\\htdocs\\atk-tes\\tools\\dbf_migration\\source"
    python audit_dbf.py --source "D:\\Data\\DBF" --output "D:\\Data\\Audit"
"""

from __future__ import annotations

import argparse
import csv
import json
import sys
from collections import Counter, defaultdict
from datetime import date, datetime
from decimal import Decimal, InvalidOperation
from pathlib import Path
from typing import Any

try:
    from dbfread import DBF
except ImportError:
    print("ERROR: Modul dbfread belum terpasang.")
    print("Jalankan: python -m pip install dbfread")
    sys.exit(1)


TABLES = ["inv", "sup", "bag", "msk", "msd", "klr", "kld", "tmp", "rin"]


def norm(value: Any) -> str:
    if value is None:
        return ""
    return str(value).strip()


def as_decimal(value: Any) -> Decimal | None:
    if value is None or norm(value) == "":
        return None
    try:
        return Decimal(str(value).strip().replace(",", "."))
    except (InvalidOperation, ValueError):
        return None


def json_value(value: Any) -> Any:
    if isinstance(value, (date, datetime)):
        return value.isoformat()
    if isinstance(value, Decimal):
        return str(value)
    if isinstance(value, bytes):
        return value.decode("latin-1", errors="replace").strip()
    return value


def write_csv(path: Path, rows: list[dict[str, Any]], fieldnames: list[str] | None = None) -> None:
    if fieldnames is None:
        fieldnames = list(rows[0].keys()) if rows else ["info"]
    with path.open("w", newline="", encoding="utf-8-sig") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames, extrasaction="ignore")
        writer.writeheader()
        for row in rows:
            writer.writerow({k: json_value(v) for k, v in row.items()})


def load_dbf(path: Path) -> tuple[list[dict[str, Any]], list[str]]:
    # char_decode latin-1 membantu mempertahankan karakter dari DBF lama.
    table = DBF(str(path), load=True, char_decode_errors="replace", ignore_missing_memofile=True)
    records = []
    for raw in table:
        # Normalisasi nama field ke huruf besar agar konsisten.
        records.append({str(k).upper(): v for k, v in raw.items()})
    fields = [str(name).upper() for name in table.field_names]
    return records, fields


def field_values(records: list[dict[str, Any]], field: str) -> list[str]:
    return [norm(r.get(field)) for r in records if norm(r.get(field)) != ""]


def duplicate_values(records: list[dict[str, Any]], field: str) -> list[dict[str, Any]]:
    counter = Counter(field_values(records, field))
    return [{"field": field, "value": value, "count": count}
            for value, count in sorted(counter.items()) if count > 1]


def orphan_rows(
    child_rows: list[dict[str, Any]],
    child_field: str,
    parent_rows: list[dict[str, Any]],
    parent_field: str,
    source_name: str,
    relation_name: str,
) -> list[dict[str, Any]]:
    parent_keys = {norm(r.get(parent_field)) for r in parent_rows if norm(r.get(parent_field))}
    result = []
    for index, row in enumerate(child_rows, start=1):
        key = norm(row.get(child_field))
        if key and key not in parent_keys:
            result.append({
                "source": source_name,
                "source_row": index,
                "relation": relation_name,
                "child_value": key,
                "record": json.dumps({k: json_value(v) for k, v in row.items()},
                                     ensure_ascii=False, default=str),
            })
    return result


def date_summary(records: list[dict[str, Any]], candidates: list[str]) -> dict[str, Any]:
    for field in candidates:
        values = [r.get(field) for r in records if r.get(field) not in (None, "")]
        if values:
            parsed = [v for v in values if isinstance(v, (date, datetime))]
            if parsed:
                return {
                    "field": field,
                    "min": min(parsed).isoformat(),
                    "max": max(parsed).isoformat(),
                    "parsed_count": len(parsed),
                    "nonempty_count": len(values),
                }
            return {
                "field": field,
                "min": min(norm(v) for v in values),
                "max": max(norm(v) for v in values),
                "parsed_count": 0,
                "nonempty_count": len(values),
                "note": "Nilai bukan tipe tanggal DBF; perlu pemeriksaan format.",
            }
    return {"field": None, "note": "Kolom tanggal yang dikenal tidak ditemukan."}


def sum_field(records: list[dict[str, Any]], field: str) -> dict[str, Any]:
    total = Decimal("0")
    valid = 0
    blank = 0
    invalid = 0
    negatives = 0
    zeros = 0
    for row in records:
        raw = row.get(field)
        if raw is None or norm(raw) == "":
            blank += 1
            continue
        value = as_decimal(raw)
        if value is None:
            invalid += 1
            continue
        valid += 1
        total += value
        if value < 0:
            negatives += 1
        elif value == 0:
            zeros += 1
    return {
        "field": field,
        "total": str(total),
        "valid_count": valid,
        "blank_count": blank,
        "invalid_count": invalid,
        "negative_count": negatives,
        "zero_count": zeros,
    }


def unique_values(records: list[dict[str, Any]], field: str) -> list[str]:
    return sorted(set(field_values(records, field)))


def main() -> int:
    parser = argparse.ArgumentParser(description="Audit DBF aplikasi ATK lama.")
    parser.add_argument("--source", required=True, help="Folder yang berisi file DBF.")
    parser.add_argument("--output", default="reports", help="Folder keluaran laporan.")
    args = parser.parse_args()

    source = Path(args.source).expanduser()
    output = Path(args.output).expanduser()
    output.mkdir(parents=True, exist_ok=True)

    if not source.is_dir():
        print(f"ERROR: Folder sumber tidak ditemukan: {source}")
        return 2

    data: dict[str, list[dict[str, Any]]] = {}
    fields: dict[str, list[str]] = {}
    table_report: dict[str, Any] = {}
    missing_files = []

    print("\n=== AUDIT DBF ATK ===")
    print(f"Sumber : {source}")
    print(f"Output : {output}\n")

    for name in TABLES:
        matches = list(source.glob(f"{name}.dbf")) + list(source.glob(f"{name.upper()}.DBF"))
        # Fallback case-insensitive untuk variasi kapitalisasi ekstensi/nama.
        if not matches:
            matches = [p for p in source.iterdir()
                       if p.is_file() and p.stem.lower() == name and p.suffix.lower() == ".dbf"]
        if not matches:
            missing_files.append(name + ".dbf")
            table_report[name] = {"exists": False, "row_count": 0}
            print(f"[TIDAK ADA] {name}.dbf")
            continue

        path = matches[0]
        try:
            rows, cols = load_dbf(path)
            data[name] = rows
            fields[name] = cols
            table_report[name] = {
                "exists": True,
                "file": str(path),
                "row_count": len(rows),
                "fields": cols,
            }
            print(f"[OK] {path.name:<12} {len(rows):>10,} baris | {len(cols)} kolom")
        except Exception as exc:
            table_report[name] = {"exists": True, "file": str(path), "error": str(exc)}
            print(f"[GAGAL] {path.name}: {exc}")

    issues: dict[str, list[dict[str, Any]]] = defaultdict(list)

    # Duplikasi kunci header dan master.
    key_fields = {
        "inv": "INV", "sup": "SUP", "bag": "BAG",
        "msk": "MSK", "klr": "KLR",
    }
    for table_name, key in key_fields.items():
        if table_name in data and key in fields.get(table_name, []):
            issues["duplicate_master_or_header_keys"].extend(
                [{"table": table_name, **x} for x in duplicate_values(data[table_name], key)]
            )

    # Relasi detail ke header dan barang.
    relations = [
        ("msd", "MSK", "msk", "MSK", "MSD.MSK -> MSK.MSK"),
        ("kld", "KLR", "klr", "KLR", "KLD.KLR -> KLR.KLR"),
        ("msd", "INV", "inv", "INV", "MSD.INV -> INV.INV"),
        ("kld", "INV", "inv", "INV", "KLD.INV -> INV.INV"),
        ("msk", "SUP", "sup", "SUP", "MSK.SUP -> SUP.SUP"),
    ]
    for child, child_field, parent, parent_field, label in relations:
        if child in data and parent in data and child_field in fields.get(child, []) \
                and parent_field in fields.get(parent, []):
            issues["orphan_relations"].extend(
                orphan_rows(data[child], child_field, data[parent], parent_field, child, label)
            )

    # Ringkasan tanggal dan kuantitas/nilai.
    summaries = {}
    date_candidates = {
        "msk": ["DATE", "TGL"], "msd": ["DATE", "TGL"],
        "klr": ["DATE", "TGL"], "kld": ["DATE", "TGL"],
        "tmp": ["TGL", "DATE"], "rin": ["TGL", "DATE"],
    }
    numeric_candidates = {
        "msd": ["QTY", "PRC", "VAL"],
        "kld": ["QTY", "PRICE", "VAL"],
        "tmp": ["QTY", "PRICE"],
        "rin": ["QTY", "PRICE"],
    }
    for table_name, rows in data.items():
        summaries[table_name] = {
            "dates": date_summary(rows, date_candidates.get(table_name, [])),
            "numeric": {
                field: sum_field(rows, field)
                for field in numeric_candidates.get(table_name, [])
                if field in fields.get(table_name, [])
            },
        }

    # Pemeriksaan nilai transaksi: VAL dibanding QTY x harga.
    value_checks = []
    for table_name, price_field in [("msd", "PRC"), ("kld", "PRICE")]:
        if table_name not in data:
            continue
        for index, row in enumerate(data[table_name], start=1):
            qty = as_decimal(row.get("QTY"))
            price = as_decimal(row.get(price_field))
            val = as_decimal(row.get("VAL"))
            if qty is not None and price is not None and val is not None:
                expected = qty * price
                if val == 0 and expected != 0:
                    value_checks.append({
                        "table": table_name, "source_row": index,
                        "qty": str(qty), "price": str(price),
                        "val": str(val), "qty_x_price": str(expected),
                        "issue": "VAL nol padahal QTY x harga tidak nol",
                    })
                elif val != expected:
                    value_checks.append({
                        "table": table_name, "source_row": index,
                        "qty": str(qty), "price": str(price),
                        "val": str(val), "qty_x_price": str(expected),
                        "issue": "VAL berbeda dari QTY x harga",
                    })

    # Analisis kode transaksi dan jurnal pada TMP.
    tmp_summary = {}
    if "tmp" in data:
        for field in ["DK", "JURNAL", "DIV", "PERIODE"]:
            if field in fields.get("tmp", []):
                tmp_summary[field] = unique_values(data["tmp"], field)
        tmp_summary["movement_counts"] = {}
        if "DK" in fields.get("tmp", []):
            tmp_summary["movement_counts"] = dict(Counter(norm(r.get("DK")) for r in data["tmp"]))

    # RIN hanya diprofilkan; tidak diperlakukan sebagai transaksi/saldo import.
    rin_summary = None
    if "rin" in data:
        rin_summary = {
            "row_count": len(data["rin"]),
            "fields": fields["rin"],
            "dates": date_summary(data["rin"], ["TGL", "DATE"]),
            "distinct_periods": unique_values(data["rin"], "PERIODE")
                if "PERIODE" in fields["rin"] else [],
            "note": "RIN hanya diaudit. Tidak akan diimpor pada tahap ini.",
        }

    # Duplikasi detail pada kombinasi parent + INV: dilaporkan, tidak otomatis dianggap salah.
    detail_repeats = []
    for table_name, parent_field in [("msd", "MSK"), ("kld", "KLR")]:
        if table_name not in data:
            continue
        counts = Counter(
            (norm(r.get(parent_field)), norm(r.get("INV")))
            for r in data[table_name]
            if norm(r.get(parent_field)) and norm(r.get("INV"))
        )
        for (parent_value, item_code), count in sorted(counts.items()):
            if count > 1:
                detail_repeats.append({
                    "table": table_name, "transaction_no": parent_value,
                    "item_code": item_code, "line_count": count,
                    "note": "Perlu ditinjau; item sama bisa saja muncul lebih dari sekali secara sah.",
                })

    # Cross-check TMP dengan header transaksi berdasarkan nomor dokumen.
    tmp_links = {}
    if "tmp" in data:
        tmp_values = unique_values(data["tmp"], "TMP") if "TMP" in fields.get("tmp", []) else []
        msk_numbers = set(field_values(data.get("msk", []), "MSK"))
        klr_numbers = set(field_values(data.get("klr", []), "KLR"))
        tmp_links = {
            "tmp_unique_document_count": len(tmp_values),
            "tmp_documents_matching_msk": sum(1 for x in tmp_values if x in msk_numbers),
            "tmp_documents_matching_klr": sum(1 for x in tmp_values if x in klr_numbers),
            "tmp_documents_matching_neither": sum(1 for x in tmp_values if x not in msk_numbers and x not in klr_numbers),
            "note": "Pencocokan awal berdasarkan nomor dokumen TMP; belum membuktikan seluruh detail identik.",
        }

    # Simpan laporan.
    (output / "audit_summary.json").write_text(
        json.dumps({
            "source_folder": str(source),
            "target_branch_id": 1,
            "migration_scope": "all_history",
            "replace_mode": True,
            "tables": table_report,
            "summaries": summaries,
            "tmp_summary": tmp_summary,
            "tmp_document_crosscheck": tmp_links,
            "rin_profile_only": rin_summary,
            "missing_files": missing_files,
            "counts": {
                "duplicate_master_or_header_keys": len(issues["duplicate_master_or_header_keys"]),
                "orphan_relations": len(issues["orphan_relations"]),
                "value_mismatches": len(value_checks),
                "repeated_item_lines": len(detail_repeats),
            },
        }, ensure_ascii=False, indent=2, default=str),
        encoding="utf-8"
    )

    write_csv(output / "duplicate_keys.csv", issues["duplicate_master_or_header_keys"])
    write_csv(output / "orphan_relations.csv", issues["orphan_relations"])
    write_csv(output / "value_checks.csv", value_checks)
    write_csv(output / "repeated_item_lines.csv", detail_repeats)

    print("\n=== RINGKASAN AUDIT ===")
    print(f"Duplikasi kode master/nomor header : {len(issues['duplicate_master_or_header_keys']):,}")
    print(f"Relasi orphan                    : {len(issues['orphan_relations']):,}")
    print(f"Ketidaksesuaian VAL               : {len(value_checks):,}")
    print(f"Item berulang dalam satu transaksi: {len(detail_repeats):,}")
    if tmp_links:
        print(f"Nomor TMP cocok ke MSK             : {tmp_links['tmp_documents_matching_msk']:,}")
        print(f"Nomor TMP cocok ke KLR             : {tmp_links['tmp_documents_matching_klr']:,}")
        print(f"Nomor TMP tidak cocok              : {tmp_links['tmp_documents_matching_neither']:,}")

    print("\nLaporan tersimpan di:", output.resolve())
    print("File utama: audit_summary.json")
    print("CSV: duplicate_keys.csv, orphan_relations.csv, value_checks.csv, repeated_item_lines.csv")
    print("\nCATATAN: Skrip ini hanya audit. Tidak mengubah database maupun file DBF.")
    print("Cabang tujuan untuk rancangan migrasi: branch_id = 1.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
'''
path = Path("/mnt/data/audit_dbf.py")

print(f"Created: {path} ({path.stat().st_size} bytes)")
