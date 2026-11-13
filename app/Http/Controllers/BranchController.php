<?php

namespace App\Http\Controllers;

use App\Models\branch;
use App\Http\Requests\StorebranchRequest;
use App\Http\Requests\UpdatebranchRequest;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorebranchRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatebranchRequest $request, branch $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(branch $branch)
    {
        //
    }
}
