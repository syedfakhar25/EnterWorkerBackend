<?php

namespace App\Http\Controllers;

use App\Models\CompanyTeam;
use App\Http\Requests\StoreCompanyTeamRequest;
use App\Http\Requests\UpdateCompanyTeamRequest;

class CompanyTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCompanyTeamRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompanyTeamRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyTeam  $companyTeam
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyTeam $companyTeam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyTeam  $companyTeam
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyTeam $companyTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompanyTeamRequest  $request
     * @param  \App\Models\CompanyTeam  $companyTeam
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompanyTeamRequest $request, CompanyTeam $companyTeam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyTeam  $companyTeam
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyTeam $companyTeam)
    {
        //
    }
}
