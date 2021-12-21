<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Step;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\ApiMessagesTrait;
use App\Http\Requests\Tasks\StoreTasksRequest;
use App\Http\Requests\Tasks\ChangeTaskStatusRequest;
use App\Http\Requests\Tasks\getEmployeeTasksRequest;
use App\Http\Resources\TaskCollection;
use App\Models\Calenderevent;
use Illuminate\Support\Facades\DB;

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
    public function getTasks($s_id){
        try{
            $tasks = Task::where('step_id', $s_id)->get();

            return response()->json([
                $tasks
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $task= new Task();
            $task->step_id=$request->step_id;
            $task->project_id=$request->project_id;
            $task->employee_id=$request->employee_id;
            $task->title=$request->title;
            $task->task_status=$request->task_status;
            $task->is_important=$request->is_important;
            $task->active=$request->active;
            $task->deadline=$request->deadline;
            $task->save();
           // dd($task);
            $calenderevent= new Calenderevent();
            $calenderevent->task_id=$task->id;
            $calenderevent->title=$request->title;
            $calenderevent->color=json_encode($request->color);
            $calenderevent->allDay=$request->allDay;
            $calenderevent->start=$request->start;
            $calenderevent->end=$request->end;
            $calenderevent->save();

            return response()->json([
                $task
            ], 200);
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
    public function update(Request $request, Task $task)
    {
        try{
          //  dd('here');
            $task->step_id=$request->step_id;
            $task->project_id=$request->project_id;
            $task->employee_id=$request->employee_id;
            $task->title=$request->title;
            $task->task_status=$request->task_status;
            $task->is_important=$request->is_important;
            $task->active=$request->active;
            $task->deadline=$request->deadline;
            $task->save();
            $calenderevent=Calenderevent::where('task_id',$task->id)->first();
            if(!empty($calenderevent)){
            $calenderevent->task_id=$task->id;
            $calenderevent->title=$request->title;
            $calenderevent->color=json_encode($request->color);
            $calenderevent->allDay=$request->allDay;
            // $calenderevent->draggable=$request->draggable;
            // $calenderevent->resizable=json_encode($request->resizable);
            $calenderevent->start=$request->start;
            $calenderevent->end=$request->end;
            $calenderevent->save();
            }

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
          $calenderevent=Calenderevent::where('task_id',$task->id)->first();
          $calenderevent->delete();
          $task->delete();
          $msg="deleted";
          return $this->responseSuccess($msg);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
}

public function updateTaskStatus(ChangeTaskStatusRequest $request){
        try
        {
           $task= Task::where('id',$request->task_id)->first();
           if($request->task_status==0){
            $task->task_status=0;
            }elseif ($request->task_status==1) {
                $task->task_status=1;
            }
            $task->save();
            return $this->responseSuccess($task);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    public function getEmployeeTasks(getEmployeeTasksRequest $request){
        try
        {
           $tasks= Task::where('employee_id',$request->employee_id)->where('project_id',$request->project_id)->get();
            return $this->responseSuccess($tasks);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function employeeTotalTasks($employee_id){

        try {

            $total_tasks=Task::where('employee_id',$employee_id)->get();
            $projects = DB::select(DB::raw("select projects.*, concat (users.first_name,' ',users.last_name) as customer_name  from  tasks left join projects  on
                        (tasks.project_id = projects.id) left join users on (projects.customer_id = users.id)
                         WHERE tasks.employee_id = $employee_id GROUP BY projects.id"));
            $tasks_projects = array(
                'tasks' => $total_tasks,
                'projects' => $projects
            );

            return response()->json([
                $tasks_projects
            ], 200);

        } catch (\Exception $e) {

        }
  }
  public function employeeCompletedTasks($employee_id){

        try {
              $completed_tasks=Task::where('employee_id',$employee_id)->with('project.customer')->where('task_status',1)->orderBy('created_at')->get();
          return $this->responseSuccess($completed_tasks);

        } catch (\Exception $e) {

        }
  }
  public function employeeOngoingTasks($employee_id){

        try {
              $ongoing_tasks=Task::where('employee_id',$employee_id)->with('project.customer')->where('task_status',0)->orderBy('created_at')->get();
          return $this->responseSuccess($ongoing_tasks);

        } catch (\Exception $e) {

        }
  }
}


