<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $companies = Company::all();
           //dd($companies);
            return response()->json([
                $companies
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
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
     * @param  \App\Http\Requests\StoreCompanyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $company = new Company();
            $company->name = $request->name ;
            $company->organization_number = $request->organization_number ;
            $company->address = $request->address ;
            $company->contact_number = $request->contact_number ;
            $company->email = $request->email ;
            $company->password= Hash::make($request->password);
            $company->confirm_password = Hash::make($request->confirm_password);
            $company->image = $request->image ;
            $company->manager_name = $request->manager_name ;
            $company->manager_email = $request->manager_email ;
            $company->manager_phone = $request->manager_phone ;
            //return $this->responseSuccess($step);
            if (!empty($request->image)) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('company_images'), $imageName);
                $company->image = $imageName;
            }
            //dd($company);
            $company->save();
            return response()->json([
                $company
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompanyRequest  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        try{
            $company->name = $request->name ;
            $company->organization_number = $request->organization_number ;
            $company->address = $request->address ;
            $company->contact_number = $request->contact_number ;
            $company->email = $request->email ;
            $company->password= Hash::make($request->password);
            $company->confirm_password = Hash::make($request->confirm_password);
            $company->image = $request->image ;
            $company->manager_name = $request->manager_name ;
            $company->manager_email = $request->manager_email ;
            $company->manager_phone = $request->manager_phone ;
            //return $this->responseSuccess($step);
            if (!empty($request->image)) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('company_images'), $imageName);
                $company->image = $imageName;
            }
            //dd($company);
            $company->update();
            return response()->json([
                $company
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        try{
            $company->delete();
            return response()->json([
                'deleted'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
}
