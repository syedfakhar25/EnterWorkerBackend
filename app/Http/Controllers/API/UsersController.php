<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\Users\StoreUsersRequest;
use App\Http\Requests\Users\UpdateUsersRequest;
use App\Http\Traits\ApiMessagesTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersCollection; 
use File;
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
    public function store(StoreUsersRequest $request)
    {
        // dd($request->all());
       

     try{   
        $user= new User();
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->phone=$request->phone;
        $user->gender=$request->gender;
        $user->designation=$request->designation;
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
    public function update(UpdateUsersRequest $request,  User $user)
    {
            try{   
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->phone=$request->phone;
        $user->gender=$request->gender;
        $user->designation=$request->designation;
        $user->email=$request->email;
        $user->password= Hash::make($request->password);
        $user->user_type=$request->user_type;
        if (!empty($request->img)) {
                $imageName = time() . '.' . $request->img->extension();
                $request->img->move(public_path('user_images'), $imageName);
                $user->img = $imageName;
            }
        $user->save();
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
        $user->img=asset('user_images/' . $user->img);
            return $this->responseSuccess($user);
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
}
