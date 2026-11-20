import argparse
import csv
import sys
from datetime import date, datetime
from decimal import Decimal
from pathlib import Path

from dbfread import DBF


def convert_value(value):
    """Mengubah tipe data DBF menjadi format yang aman untuk CSV."""

    if value is None:
        return ""

    if isinstance(value, (datetime, date)):
        return value.isoformat()

    if isinstance(value, Decimal):
        return str(value)

    if isinstance(value, bytes):
        return value.decode("utf-8", errors="replace")

    return str(value)


def convert_dbf_to_csv(input_file, output_file, encoding=None):
    input_path = Path(input_file)
    output_path = Path(output_file)

    if not input_path.exists():
        raise FileNotFoundError(
            f"File DBF tidak ditemukan: {input_path}"
        )

    if input_path.suffix.lower() != ".dbf":
        raise ValueError("File sumber harus berekstensi .DBF")

    print(f"Membaca DBF : {input_path}")

    options = {
        "char_decode_errors": "replace",
    }

    if encoding:
        options["encoding"] = encoding

    table = DBF(
        str(input_path),
        **options
    )

    columns = table.field_names

    if not columns:
        raise ValueError("File DBF tidak memiliki kolom.")

    output_path.parent.mkdir(
        parents=True,
        exist_ok=True
    )

    total = 0

    with output_path.open(
        "w",
        newline="",
        encoding="utf-8-sig"
    ) as csv_file:

        writer = csv.DictWriter(
            csv_file,
            fieldnames=columns,
            extrasaction="ignore"
        )

        writer.writeheader()

        for record in table:
            row = {
                column: convert_value(record.get(column))
                for column in columns
            }

            writer.writerow(row)
            total += 1

    print("\nKonversi berhasil!")
    print(f"Jumlah kolom : {len(columns)}")
    print(f"Jumlah data  : {total}")
    print(f"File CSV     : {output_path.resolve()}")


def main():
    parser = argparse.ArgumentParser(
        description="Universal DBF to CSV Converter"
    )

    parser.add_argument(
        "input",
        help="Lokasi file DBF sumber"
    )

    parser.add_argument(
        "-o",
        "--output",
        help="Lokasi file CSV tujuan (opsional)"
    )

    parser.add_argument(
        "--encoding",
        help="Encoding DBF, misalnya cp1252 atau cp850"
    )

    args = parser.parse_args()

    input_path = Path(args.input)

    if args.output:
        output_path = Path(args.output)
    else:
        output_path = input_path.with_suffix(".csv")

    try:
        convert_dbf_to_csv(
            input_path,
            output_path,
            args.encoding
        )

    except Exception as error:
        print(f"\nGAGAL: {error}")
        sys.exit(1)


if __name__ == "__main__":
    main()
