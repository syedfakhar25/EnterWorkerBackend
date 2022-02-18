<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pid)
    {
        try{
            $steps = Step::where('project_id', $pid)->get();

            return response()->json([
                $steps
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function getSteps($pid)
    {
        try{
            $steps = Step::where('project_id', $pid)->orderBy('step_order')->get();
           //dd($steps);
            $step_details = array();
            foreach ($steps as $step){

                $tasks = Task::where('step_id', $step->id)->get();
                $task_details= array();
                foreach ($tasks as $task){
                    $employees[]= $task->employee_id;
                    $employee_detail =DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id = $task->employee_id"));
                   //dd($employee_detail);
                    if($employee_detail!=NULL) {
                        $employee_detail[0]->img = asset('user_images/' . $employee_detail[0]->img);
                        $task['employee_details'] = $employee_detail[0];
                    }
                    $task_details[] = $task;
                   //dd($task_details);
                }


                $step_info = $step;
                $step_info['task_details'] = $task_details;
                $step_details[] = $step_info;
            }
            //dd('dsg');
            return response()->json([
                $step_details
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function getCompanyWorkerSteps(Request $request, $pid)
    {
        try{
            $company_worker = $request->company_worker;
            //dd($company_worker);
            $steps = Step::where('project_id', $pid)->where('company_worker', $company_worker)->orderBy('step_order')->get();
           // dd($steps);
            $step_details = array();
            foreach ($steps as $step){
                $tasks = Task::where('step_id', $step->id)->get();
                //dd($tasks);
                $task_details= array();
                foreach ($tasks as $task){

                    $employees[]= $task->employee_id;

                    $employee_detail =DB::select(DB::raw("select  users.id, users.first_name, users.last_name , users.img, designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id = $task->employee_id"));
                   dd($employee_detail);
                    if(!empty($employee_detail)){
                       dd('dd');
                       $employee_detail[0]->img = asset('user_images/' . $employee_detail[0]->img);
                       $task['employee_details']=$employee_detail[0];
                       $task_details[] = $task;
                   }else{
                       $task_details[] = $task;
                   }

                    // dd($task_details);
                }

                $step_info = $step;
                $step_info['task_details'] = $task_details;
                $step_details[] = $step_info;
            }
            //dd('dsg');
            return response()->json([
                $step_details
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //getting next step of project
    public function stepAutomation(Request $request, $pid)
    {
        try{
            $steps = Step::where('project_id', $pid)->get();
            $user_step_order = $request->step_order;
            $step_orders = array();
            foreach($steps as $step){
                $step_orders[] = $step->step_order;
            }
            $step_id = $request->step_id;

            //getting next step order from current step
            $current_step = Step::where('id',$step_id)->get();
            $current_step_order = $current_step[0]->step_order;
            $next_step_order = $current_step_order+1;
            $next_step = Step::where('step_order', $next_step_order)->where('project_id', $pid)->get();
            $tasks = Task::where('step_id', $step_id)->where('is_important', 1)->get();
            $count = 0;
            foreach($tasks as $task){
                if($task->task_status == 0 || $task->task_status==1){
                    $count =1;
                    break;
                }
            }
           // dd($count);
            if($count == 0){
                if(count($next_step)>0){
                    $next_step[0]->active = 1;
                    $next_step[0]->update();
                }
                else{
                    $next_step = 'no next step exists';
                }
            }
            else{
                if(count($next_step)>0){
                    $next_step[0]->active = 0;
                    $next_step[0]->update();
                }
                else{
                    $next_step = 'no next step exists';
                }
            }
           /* if(count($next_step)>0){
                if($next_step[0]->active == 0 || $next_step[0]->active == NULL){
                    if($next_step[0]->task_status == 0 || $next_step[0]->task_status == 1) {
                        $next_step[0]->active = 0;
                        $next_step[0]->update();
                    }
                    elseif($next_step[0]->task_status == 1){
                        $next_step[0]->active = 1;
                        $next_step[0]->update();
                    }
                  //  dd($next_step[0]);
                }
                elseif($next_step[0]->active == 1){
                    $next_step[0]->active = 0;
                    $next_step[0]->update();
                   // dd($next_step[0]);
                }
            }*/

            return response()->json([
                $next_step,
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
    public function store(Request $request){
        try{
            $step= new Step();
            $step->project_id=$request->project_id;
            $step->task_status=$request->task_status;
            $step->active=$request->active;
            $step->step_order=$request->step_order;
            $step->company_worker=$request->company_worker;
            $step->percentage=$request->percentage;
            //dd($step);
            $step->save();

            //return $this->responseSuccess($step);

            return response()->json([
                $step
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //create step in between any step
    public function stepIn(Request $request){
        try{
            $current_step_order = intval($request->step_order);
            $all_steps = Step::where('project_id', $request->project_id)->get();
            $next_step_orders = array();
            foreach ($all_steps as $step){
                //dd($current_step_order);
                if($step->step_order >= $current_step_order){
                    $next_step_orders[] = $step->step_order;
                }
            }
            if(count($next_step_orders)>0){
                $steps_next = Step::whereIn('step_order', $next_step_orders)->where('project_id', $request->project_id)->get();
                foreach ($steps_next as $snext){
                    $snext->step_order +=1;
                    $snext->save();
                }
            }

            ///////////////////////////
            $step= new Step();
            $step->project_id=$request->project_id;
            $step->task_status=$request->task_status;
            $step->active=$request->active;
            $step->step_order=$request->step_order;
            $step->company_worker=$request->company_worker;
            $step->percentage=$request->percentage;

            $step->save();
           // dd($step);


            return response()->json([
                $step
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Step $step)
    {
        try{
            $step->project_id=$request->project_id;
            $step->percentage=$request->percentage;
          // dd($step);
            $step->save();

            //return $this->responseSuccess($step);

            return response()->json([
                $step
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $step=Step::find($id);
            $step_order = $step->step_order;
            $project_id = $step->project_id;
            // dd($step);
            if(!empty($step)){
                $tasks = Task::where('step_id', $id)->get();
                if ($tasks->isNotEmpty()){
                    foreach ($tasks as $task){
                        $task->delete();
                    }
                }
            }
            $step->delete();
            //reset step order

            $current_step_order = $step_order;
            $all_steps = Step::where('project_id', $project_id)->get();
            $next_step_orders = array();
            foreach ($all_steps as $step){
                //dd($current_step_order);
                if($step->step_order >= $current_step_order){
                    $next_step_orders[] = $step->step_order;
                }
            }
            if(count($next_step_orders)>0){
                $steps_next = Step::whereIn('step_order', $next_step_orders)->where('project_id', $project_id)->get();
                foreach ($steps_next as $snext){
                    $snext->step_order -=1;
                    $snext->save();
                }
            }
            $msg="deleted";
            return response()->json([
                $msg
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
}
