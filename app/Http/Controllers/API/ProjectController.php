<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Traits\ApiMessagesTrait;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Resources\ProjectCollection;

class ProjectController extends Controller
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
            $projects=new ProjectCollection(Project::with('customer')->latest()->get());

            return $this->responseSuccess($projects);
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
    public function store(StoreProjectRequest $request)
    {
        try{   
        $project= new Project();
        $project->customer_id=$request->customer_id;
        $project->name=$request->name;
        $project->description=$request->description;
        $project->street=$request->street;
        $project->postal_code=$request->postal_code;
        $project->city=$request->city;
        $project->start_date= $request->start_date;
        $project->end_date=$request->end_date;
        $project->save();

            return $this->responseSuccess($project);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        try
            {
              return $this->responseSuccess($project);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        try
            {
              return $this->responseSuccess($project);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProjectRequest $request, Project $project)
    {
        try{   
        $project->customer_id=$request->customer_id;
        $project->name=$request->name;
        $project->description=$request->description;
        $project->street=$request->street;
        $project->postal_code=$request->postal_code;
        $project->city=$request->city;
        $project->start_date= $request->start_date;
        $project->end_date=$request->end_date;
        $project->save();

            return $this->responseSuccess($project);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        try
            {
              $project->delete();
                $msg="deleted";
              return $this->responseSuccess($msg);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }
}
