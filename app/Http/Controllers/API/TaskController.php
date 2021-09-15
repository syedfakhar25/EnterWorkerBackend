<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Traits\ApiMessagesTrait;
use App\Http\Requests\Tasks\StoreTasksRequest;
use App\Http\Resources\TaskCollection;

class TaskController extends Controller
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
            $tasks=new TaskCollection(Task::with('project.customer','employee')->latest()->get());

            return $this->responseSuccess($tasks);
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
    public function store(StoreTasksRequest $request)
    {
        try{   
        $task= new Task();
        $task->project_id=$request->project_id;
        $task->employee_id=$request->employee_id;
        $task->title=$request->title;
        $task->percentage=$request->percentage;
        $task->deadline=$request->deadline;
        $task->save();

            return $this->responseSuccess($task);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
         try
            {
              return $this->responseSuccess($task);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        try
            {
              return $this->responseSuccess($task);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTasksRequest $request, Task $task)
    {
        try{   
        $task->project_id=$request->project_id;
        $task->employee_id=$request->employee_id;
        $task->title=$request->title;
        $task->percentage=$request->percentage;
        $task->deadline=$request->deadline;
        $task->save();

            return $this->responseSuccess($task);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        try
            {
              $task->delete();
                $msg="deleted";
              return $this->responseSuccess($msg);
          }catch (\Exception $e)
          {
            return $this->responseFail();
          }
    }
}
