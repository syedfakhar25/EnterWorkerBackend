<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\Task;
use App\Models\Template;
use App\Models\TemplateStep;
use App\Models\TemplateTask;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    //create a template
    public function addTemplate(Request $request){
        try{
            $template  = new Template();
            $template->name = $request->name;
            $template->save();
            return response()->json([
                $template
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //edit a temaple
    public function updateTemplate(Request $request, $id){
        try{
            $template = Template::find($id);
            $template->name = $request->name;
            $template->update();

            return response()->json([
                $template
            ],
                200
            );
        }catch (\Exception $e){
            return $this->responseFail();
        }
    }

    //show a  specific temaplate
    public function getTemplate($id){
        try{
            $template = Template::find($id);
            $steps = TemplateStep::where('template_id', $id)->get();
            $step_ids = array();
            foreach ($steps as $step){
                $step_ids[] = $step->id;
            }
            $tasks = TemplateTask::whereIn('step_id', $step_ids)->get();

            $template = array(
              'template' => $template,
              'steps' => $steps,
              'tasks' => $tasks
            );
            return response()->json([
                $template
            ],
                200
            );
        }catch (\Exception $e){
            return $this->responseFail();
        }
    }

    //delete a  specific temaplate
    public function destroyTemplate($id){
        try{
            $template = Template::find($id);
            $template->delete();
            return response()->json([
                'deleted'
            ],
                200
            );
        }catch (\Exception $e){
            return $this->responseFail();
        }
    }


    //getting list of templates
    public function showTemplates(){
        try{
            $templates  = Template::all();
            return response()->json([
                $templates
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }


    //Step CRUD for templates
    //create a template
    public function addTempStep(Request $request){
        try{
            $current_step_order = intval($request->step_order);
            $all_steps = TemplateStep::where('template_id', $request->template_id)->get();
            $next_step_orders = array();
            foreach ($all_steps as $step){
                //dd($current_step_order);
                if($step->step_order >= $current_step_order){
                    $next_step_orders[] = $step->step_order;
                }
            }
            if(count($next_step_orders)>0){
                $steps_next = TemplateStep::whereIn('step_order', $next_step_orders)->where('template_id', $request->template_id)->get();
                foreach ($steps_next as $snext){
                    $snext->step_order +=1;
                    $snext->save();
                }
            }
            ///////////////////////////
            $step= new TemplateStep();
            $step->step_order = $request->step_order;
            $step->template_id = $request->template_id;
            $step->save();
            return response()->json([
                $step
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //delete template's step
    public function destroyTempStep($id){
        $step=TemplateStep::find($id);
        $step_order = $step->step_order;
        $template_id = $step->template_id;
        if(!empty($step)){
            $tasks = TemplateTask::where('step_id', $id)->get();
            if ($tasks->isNotEmpty()){
                foreach ($tasks as $task){
                    $task->delete();
                }
            }
        }
        $step->delete();

        //reset step order
        $current_step_order = $step_order;
        $all_steps = TemplateStep::where('template_id', $template_id)->get();
        $next_step_orders = array();
        foreach ($all_steps as $step){
            //dd($current_step_order);
            if($step->step_order >= $current_step_order){
                $next_step_orders[] = $step->step_order;
            }
        }
        if(count($next_step_orders)>0){
            $steps_next = TemplateStep::whereIn('step_order', $next_step_orders)->where('template_id', $template_id)->get();
            foreach ($steps_next as $snext){
                $snext->step_order -=1;
                $snext->save();
            }
        }
        $msg="deleted";
        return response()->json([
            $msg
        ], 200);

    }

    // adding a task in step of template
    public function addTempTask(Request $request){
        try{
            $task  = new TemplateTask();
            $task->title = $request->title;
            $task->template_id = $request->template_id;
            $task->step_id = $request->step_id;
            $task->save();
            return response()->json([
                $task
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    // updating a task in step of template
    public function updateTempTask(Request $request, $id){
        try{
            $task  = TemplateTask::find($id);
            if($request->title !=NULL)
            $task->title = $request->title;

            if($request->template_id !=NULL)
            $task->template_id = $request->template_id;

            if($request->step_id !=NULL)
            $task->step_id = $request->step_id;

            $task->update();
            return response()->json([
                $task
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    // deleting a task in step of template
    public function destroyTempTask($id){
        try{
            $task  = TemplateTask::find($id);

            $task->delete();
            return response()->json([
                'deleted'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //populating template in a project
    public function addTemplateinProject(Request $request, $temp_id){
        try{
            $project_id = $request->project_id;
            $steps = TemplateStep::where('template_id', $temp_id)->get();
            $step_ids = array();
            foreach ($steps as $step){
                $step_ids[] = $step->id;
            }
            $tasks = TemplateTask::whereIn('step_id', $step_ids)->get();

            //add template steps in project steps
            foreach ($steps as $step_temp){
                $step = new Step();
                $step->step_order = $step_temp->step_order;
                $step->project_id = $project_id;
                $step->save();
            }
            //add template tasks in project tasks
            foreach ($tasks as $task_temp){
                $task = new Task();
                $task->title = $task_temp->title;
                $task->step_id = $task_temp->step_id;
                $task->project_id = $project_id;
                $task->save();
            }
            return response()->json([
                'template added in project'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
}
