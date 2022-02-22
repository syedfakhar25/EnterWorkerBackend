<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTemplate;
use App\Models\Step;
use App\Models\Task;
use App\Models\Template;
use App\Models\TemplateStep;
use App\Models\TemplateTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            //delete templates steps
                $steps = TemplateStep::where('template_id', $id)->get();
                //dd($steps);
                foreach ($steps as $step){
                    $this->destroyTempStep($step->id);
                }
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
            $templates  = Template::where('submit', 1)->get();
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
            $step->percentage = $request->percentage;
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

    public function editTempStep(Request $request, $id){
        try{
                $step = TemplateStep::find($id);
                $step->percentage = $request->percentage;
                $step->save();
            return response()->json([
                $step
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function getTempSteps($id)
    {
        try{
            $steps = TemplateStep::where('template_id', $id)->orderBy('step_order')->get();
            $step_details = array();
            foreach ($steps as $step){
                $tasks = TemplateTask::where('step_id', $step->id)->get();
                $task_details= array();
                foreach ($tasks as $task){
                    $task_details[] = $task;
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

    //delete template's step
    public function destroyTempStep($id){
        try{
            $step=TemplateStep::find($id);
            $step_order = $step->step_order;
            $template_id = $step->template_id;
            $tasks = TemplateTask::where('step_id', $id)->get();
            foreach ($tasks as $task){
                $this->destroyTempTask($task->id);
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
        }catch (\Exception $e)
        {
        return $this->responseFail();
        }

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
            if($task != NULL)
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
    public function addTemplateinProject(Request $request, $project_id){
        try{
            $project_id = $project_id;
            $project = Project::find($project_id);
            $templates = $request->temp_id;
            $step_order =0;
            foreach($templates as $key=>$value){
                $steps = TemplateStep::where('template_id', $value)->get();
                //add template steps in project steps
                foreach ($steps as $step_temp){
                    $step_order+=1;
                    $step = new Step();
                    $step->step_order = $step_order;
                    $step->percentage = $step_temp->percentage;
                    $step->project_id = $project_id;
                    $step->save();
                    $tasks = TemplateTask::where('step_id', $step_temp->id)->get();

                    foreach ($tasks as $task_temp){
                        $task = new Task();
                        $task->title = $task_temp->title;
                        $task->step_id = $step->id;
                        $task->deadline = $project->start_date;
                        $task->project_id = $project_id;
                        $task->save();
                    }
                }
                //add template tasks in project tasks

                $project_template = new ProjectTemplate();
                $project_template->project_id = $project_id;
                $project_template->template_id = $value;
                $project_template->save();
            }
            /*$steps = TemplateStep::where('template_id', $temp_id)->get();
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
            */



            return response()->json([
                'template added in project'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //template of a project
    public function getTemplateofProject($id){
        try{
            $p_templates = ProjectTemplate::where('project_id', $id)->get();
            $temp_ids = array();
            foreach ($p_templates as $temp){
                $temp_ids[]=$temp->template_id;
            }
            $templates = Template::whereIn('id', $temp_ids)->get();
            return response()->json([
                $templates
            ], 200);
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }

    public function SubmitTemplate($id){
        try{
            $template = Template::find($id);
            $template->submit = 1;
            $template->save();
            return response()->json([
                'submitted'
            ], 200);
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }

}
