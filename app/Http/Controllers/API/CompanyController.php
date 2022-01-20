<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\PorjectCompanyWorker;
use App\Models\User;
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
            $img_path=asset('company_images/');
            foreach ($companies as $key => $value) {
                $value->image=$img_path.'/'.$value->image;
            }
           //dd($companies);
            return response()->json([
                $companies
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function companyForProject(Request $request, $pid)
    {
        try {
            //dd($pid);
            $companies = Company::all();
           $project_company = PorjectCompanyWorker::where('project_id', $pid)->get();
           $company_ids= array();
           foreach($project_company as $pc){
               $company_ids[] = $pc->company_worker_id;
           }
           // dd($company_ids);
           $companies = Company::whereNotIn('id', $company_ids)->get();
            $img_path=asset('company_images/');
           foreach ($companies as $cmp){
               $cmp->image=$img_path.'/'.$cmp->image;
           }
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
            }else{
                $company->image = 'dummy_image.png';
            }

            //dd($company);
            $company->save();
           // dd($request->all());

            $user = new User();
            $user->email = $request->email;
            $user->first_name = $request->name;
            $user->password = Hash::make($request->password);
            $user->company = $company->id;
            $user->save();
           // dd($company->id);
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
        try {
            $company->image=asset('company_images/' . $company->image);
            return response()->json([
                $company
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
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

            if(!empty($request->name))
                //dd($request->name);
            $company->name = $request->name ;

            if(!empty($request->organization_number))
            $company->organization_number = $request->organization_number ;

            if(!empty($request->address))
            $company->address = $request->address ;

            if(!empty($request->contact_number))
            $company->contact_number = $request->contact_number ;

            if(!empty($request->email))
            $company->email = $request->email ;

            if(!empty($request->password))
            $company->password= Hash::make($request->password);

            if(!empty($request->confirm_password))
            $company->confirm_password = Hash::make($request->confirm_password);

            if(!empty($request->image))
            $company->image = $request->image ;

            if(!empty($request->manager_name))
            $company->manager_name = $request->manager_name ;

            if(!empty($request->manager_email))
            $company->manager_email = $request->manager_email ;

            if(!empty($request->manager_phone))
            $company->manager_phone = $request->manager_phone ;

           // dd($request->name);
            //return $this->responseSuccess($step);
            if (!empty($request->image)) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('company_images'), $imageName);
                $company->image = $imageName;
            }

            //dd($company->id);
            $company->update();
            $user = User::where('company', $company->id)->first();
            if(!empty($request->password))
            $user->password = Hash::make($request->password);
            if(!empty($request->name))
            $user->first_name = $request->name;

            $user->update();
          //  dd($user);

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
            $user = User::where('company', $company->id)->first();
            $user->delete();

            return response()->json([
                'deleted'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
}
