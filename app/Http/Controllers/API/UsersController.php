<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\Users\StoreUsersRequest;
use App\Http\Requests\Users\UpdateUsersRequest;
use App\Http\Traits\ApiMessagesTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersCollection;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class UsersController extends Controller
{
    use ApiMessagesTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
           {
            $users=new UsersCollection(User::latest()->get());

            return $this->responseSuccess($users);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function userEmployees(Request $request){
        //dd('sjdb');
        $employees = DB::select(DB::raw("select  users.*, designations.designation_name from users join designations
                            on (users.designation_id = designations.id);"));
        foreach ($employees as $emp){
            $emp->img =  asset('user_images/' . $emp->img);
        }

        return response()->json([
            $employees
        ], 200);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     try{
        // dd('here');
        // dd($request->designation_id);
         $designation_name  = Designation::select('designation_name')->where('id', $request->designation_id)->first();
         $d_name = $designation_name->designation_name;

        $user= new User();
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->by_company=$request->by_company;
        $user->manager_type=$request->manager_type;
        $user->phone=$request->phone;
        $user->gender=$request->gender;
        $user->designation_id=$request->designation_id;
        $user->designation=$d_name;
        $user->email=$request->email;
        $user->password= Hash::make($request->password);
        $user->user_type=$request->user_type;

        if (!empty($request->img)) {
                $imageName = time() . '.' . $request->img->extension();
                $request->img->move(public_path('user_images'), $imageName);
                $user->img = $imageName;
            }
        $user->save();

        if($user->user_type==2){
            $role= Role::where('name','manager')->first();
            $user->assignRole($role);
        }
        if($user->user_type==3){
            $role= Role::where('name','employee')->first();
            $user->assignRole($role);
        }
        if($user->user_type==4){
            $role= Role::where('name','customer')->first();
            $user->assignRole($role);
        }
        if($user->user_type==5){
             $role= Role::where('name','company_worker')->first();
             $user->assignRole($role);
         }
        $user->img=asset('user_images/' . $user->img);

        return $this->responseSuccess($user);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
         try
            {
                $user->img=asset('user_images/' . $user->img);
              return $this->responseSuccess($user);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // dd($user);
        try
            {
                $user->img=asset('user_images/' . $user->img);
              return $this->responseSuccess($user);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  User $user)
    {
        try{
       // dd($request->password);
        $designation_name  = Designation::select('designation_name')->where('id', $request->designation_id)->first();

        // dd($request->designation_id);
        $d_name = $designation_name->designation_name;
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->by_company=$request->by_company;
        $user->manager_type=$request->manager_type;
        $user->phone=$request->phone;
        $user->gender=$request->gender;
        $user->designation_id=$request->designation_id;
        $user->designation=$d_name;
        if( $user->email != $request->email){
            $user->email=$request->email;
        }
        if($request->password!=NULL){
            $user->password= Hash::make($request->password);
           // dd('id');
        }

        $user->user_type=$request->user_type;

        if (!empty($request->img)){
             $base64_image = $request->img; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = time().'.'.'png';
            \File::put(public_path('user_images/').$imageName, base64_decode($file_data));
                        $user->img = $imageName;
        }
      //  dd($user);
        $user->save();
        //dd($user);
        $user->roles()->detach();
        if($user->user_type==2){
            $role= Role::where('name','manager')->first();
            $user->assignRole($role);
        }
        if($user->user_type==3){
            $role= Role::where('name','employee')->first();
            $user->assignRole($role);
        }
        if($user->user_type==4){
            $role= Role::where('name','customer')->first();
            $user->assignRole($role);
        }
        if($user->user_type==5){
            $role= Role::where('name','company_worker')->first();
            $user->assignRole($role);
        }
        $user->img=asset('user_images/' . $user->img);
            return response()->json([
                $user
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try
            {
              $user->delete();
             $img_path = public_path('/user_images/').$user->image;
                if (File::exists($img_path)) {
                    File::delete($img_path);
                }
                $msg="deleted";
              return $this->responseSuccess($msg);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

     public function getallemployee()
    {
        try
           {
            $employee=new UsersCollection(User::where('user_type',3)->get());

            return $this->responseSuccess($employee);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
     public function getallcustomer()
    {
        try
           {
            $customers=new UsersCollection(User::where('user_type',4)->get());

            return $this->responseSuccess($customers);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
     public function getallmanagers()
    {
        try
           {
            $managers=new UsersCollection(User::where('user_type',2)->get());

            return $this->responseSuccess($managers);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function getallCompanyWorkers()
    {
        try {
            $company_workers = new UsersCollection(User::where('user_type', 5)->get());

            return $this->responseSuccess($company_workers);
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }

    public function getCompanyWorkerEmployees($company_worker)
    {
        try {
            $company_worker_employees = User::where('by_company', $company_worker)->get();
            $img_path=asset('user_images/');
            foreach ($company_worker_employees as $key => $value) {
                $value->img=$img_path.'/'.$value->img;
            }
            return $this->responseSuccess($company_worker_employees);
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }
}
