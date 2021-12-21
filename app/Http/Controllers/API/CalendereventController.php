<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calenderevent;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\Calenderevents\StoreCalendereventRequest;
use App\Http\Traits\ApiMessagesTrait;
use App\Http\Resources\CalendereventCollection;
use Validator;


class CalendereventController extends Controller
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
        $calenderevents=new CalendereventCollection(Calenderevent::latest()->get());

        return $this->responseSuccess($calenderevents);
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
    public function store(StoreCalendereventRequest $request)
    {
        try{
            $calenderevent= new Calenderevent();
            $calenderevent->title=$request->title;
            $calenderevent->color=json_encode($request->color);
            $calenderevent->allDay=$request->allDay;
            $calenderevent->draggable=$request->draggable;
            $calenderevent->resizable=json_encode($request->resizable);
            $calenderevent->start=$request->start;
            $calenderevent->end=$request->end;
            $calenderevent->save();
            $calenderevent->resizable=json_decode($calenderevent->resizable);
            $calenderevent->color=json_decode($calenderevent->color);

            return $this->responseSuccess($calenderevent);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calenderevent  $calenderevent
     * @return \Illuminate\Http\Response
     */
    public function show(Calenderevent $calenderevent)
    {
     try
     {
        $calenderevent->resizable=json_decode($calenderevent->resizable);
        $calenderevent->color=json_decode($calenderevent->color);
        return $this->responseSuccess($calenderevent);
    }catch (\Exception $e)
    {
        return $this->responseFail();
    }
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Calenderevent  $calenderevent
     * @return \Illuminate\Http\Response
     */
    public function edit(Calenderevent $calenderevent)
    {
        try
        {
            $calenderevent->resizable=json_decode($calenderevent->resizable);
            $calenderevent->color=json_decode($calenderevent->color);
            return $this->responseSuccess($calenderevent);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calenderevent  $calenderevent
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCalendereventRequest $request, Calenderevent $calenderevent)
    {
        try{
            $calenderevent->title=$request->title;
            $calenderevent->color=json_encode($request->color);
            $calenderevent->allDay=$request->allDay;
            $calenderevent->draggable=$request->draggable;
            $calenderevent->resizable=json_encode($request->resizable);
            $calenderevent->start=$request->start;
            $calenderevent->end=$request->end;
            $calenderevent->save();
            $calenderevent->resizable=json_decode($calenderevent->resizable);
            $calenderevent->color=json_decode($calenderevent->color);
            return $this->responseSuccess($calenderevent);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calenderevent  $calenderevent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calenderevent $calenderevent)
    {
        try
        {
          $calenderevent->delete();
          $msg="deleted";
          return $this->responseSuccess($msg);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
}

    public function employeeEvents($employee_id){
                try {
                    $tasks=Task::with('event.task.project')->where('employee_id',$employee_id)->get();
                    $events=[];
                    foreach ($tasks as $key => $value) {
                        $value->event->resizable=json_decode($value->event->resizable);
                        $value->event->color=json_decode($value->event->color);
                        $events[]=$value->event;
                    }
                    return $this->responseSuccess($events);
                    
                } catch (Exception $e) {
                    
                }
        }

    public function customerEvents($customer_id){
                try {
                    $projects=Project::with('tasks.event.task.project')->where('customer_id',$customer_id)->get();
                    $events=[];
                    foreach ($projects as $key => $value) {
                        foreach ($value->tasks as $key => $value1) {
                            // return $value1;
                        $value1->event->resizable=json_decode($value1->event->resizable);
                        $value1->event->color=json_decode($value1->event->color);
                        $events[]=$value1->event;
                        }
                        
                    }
                    return $this->responseSuccess($events);
                    
                } catch (Exception $e) {
                    
                }
        }

    public function managerEvents($manager_id){
                try {
                    $projects=Project::with('tasks.event.task.project')->where('manager_id',$manager_id)->get();
                    $events=[];
                    foreach ($projects as $key => $value) {
                        foreach ($value->tasks as $key => $value1) {
                            // return $value1;
                        $value1->event->resizable=json_decode($value1->event->resizable);
                        $value1->event->color=json_decode($value1->event->color);
                        $events[]=$value1->event;
                        }
                        
                    }
    
                    return $this->responseSuccess($events);
                    
                } catch (Exception $e) {
                    
                }
        }
    public function adminEvents(){
                try {
                    $projects=Project::with('tasks.event.task.project')->get();
                    $events=[];
                    foreach ($projects as $key => $value) {
                        foreach ($value->tasks as $key => $value1) {
                            // return $value1;
                        $value1->event->resizable=json_decode($value1->event->resizable);
                        $value1->event->color=json_decode($value1->event->color);
                        $events[]=$value1->event;
                        }
                        
                    }
                    return $events;
                    return $this->responseSuccess($events);
                    
                } catch (Exception $e) {
                    
                }
        }
        public function adminEventNotification(Request $request){
        $request->validate([
            'current_date' => 'required|date',
        ]);
        try
        {
          $current_events=Calenderevent::with('task.project')->where('start', '<=', $request->current_date)
          ->where('end', '>=', $request->current_date)
          ->get();
          foreach ($current_events as $key => $value) {
              $value->resizable=json_decode($value->resizable);
              $value->color=json_decode($value->color);
          }
          return $this->responseSuccess($current_events);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
    }

        public function managerEventNotification(Request $request){
        $request->validate([
            'current_date' => 'required|date',
            'manager_id' => 'required|numeric',
        ]);
        try
        {
            $projects=Project::with('tasks.event.task.project')->where('manager_id',$request->manager_id)->get();
                    $task_ids=[];
                    foreach ($projects as $key => $value) {
                        foreach ($value->tasks as $key => $value1) {
                        $task_ids[]=$value1->id;
                        }
                        
                    }
                    // return $task_ids;
          $current_events=Calenderevent::with('task.project')->whereIn('task_id',$task_ids)->where('start', '<=', $request->current_date)
          ->where('end', '>=', $request->current_date)
          ->get();
          foreach ($current_events as $key => $value) {
              $value->resizable=json_decode($value->resizable);
              $value->color=json_decode($value->color);
          }
          return $this->responseSuccess($current_events);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
    }

        public function customerEventNotification(Request $request){
        $request->validate([
            'current_date' => 'required|date',
            'customer_id' => 'required|numeric',
        ]);
        try
        {
          $projects=Project::with('tasks.event.task.project')->where('customer_id',$request->customer_id)->get();
                    $task_ids=[];
                    foreach ($projects as $key => $value) {
                        foreach ($value->tasks as $key => $value1) {
                        $task_ids[]=$value1->id;
                        }
                        
                    }
          $current_events=Calenderevent::with('task.project')->whereIn('task_id',$task_ids)->where('start', '<=', $request->current_date)
          ->where('end', '>=', $request->current_date)
          ->get();
          foreach ($current_events as $key => $value) {
              $value->resizable=json_decode($value->resizable);
              $value->color=json_decode($value->color);
          }
          return $this->responseSuccess($current_events);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
    } 
     public function employeeEventNotification(Request $request){
        $request->validate([
            'current_date' => 'required|date',
            'employee_id' => 'required|numeric',
        ]);
        try
        {
            $tasks=Task::where('employee_id',$request->employee_id)->get();
            $task_ids=[];
                        foreach ($tasks as $key => $value) {
                        $task_ids[]=$value->id;
                        }
                        
          $current_events=Calenderevent::with('task.project')->whereIn('task_id',$task_ids)->where('start', '<=', $request->current_date)
          ->where('end', '>=', $request->current_date)
          ->get();
          foreach ($current_events as $key => $value) {
              $value->resizable=json_decode($value->resizable);
              $value->color=json_decode($value->color);
          }
          return $this->responseSuccess($current_events);
      }catch (\Exception $e)
      {
        return $this->responseFail();
    }
    }      
}
