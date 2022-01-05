<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CompanyTeam;
use App\Models\ExtraWork;
use App\Models\OrderDetail;
use App\Models\Project;
use App\Models\ProjectPicture;
use App\Models\ProjectTeam;
use App\Models\Step;
use App\Models\User;
use App\Models\Task;
use App\Models\Pinproject;
use Illuminate\Http\Request;
use App\Http\Traits\ApiMessagesTrait;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\StorePinProjectRequest;
use App\Http\Resources\ProjectCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use DB;
use File;
use function Symfony\Component\String\b;

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
        $projects=Project::with('customer','tasks','pinnedproject', 'manager')->get();
          //checking status of projects (completed, in-progress) on basis of its tasks)
        foreach($projects as $prj){
              $tasks = Task::where('project_id', $prj->id)
                  ->whereIn('task_status', [0,1])
                  ->get();

              if(count($tasks)>0){
                  $prj->status = 1;
                  $prj->update();
              }
              else{
                  $prj->status = 2;
                  $prj->update();
              }
        }
        $img_path=asset('user_images/');
        foreach ($projects as $key => $value) {
          /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
          $employee=[];
          foreach ($value->tasks as $key => $value1) {
            if(isset($value1->employee->img)){
              $value1->employee->img=$img_path.'/'.$value1->employee->img;
            }
            $employee[]=$value1->employee;
          }
          $value->employee=$employee;
        }


       // $projects=new ProjectCollection($projects);
       // dd($projects);
          return response()->json([
              $projects
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

        $project= new Project();
        $project->customer_id=$request->customer_id;
        //$project->manager_id=$request->manager_id;
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
    public function show($project)
    {
      try
      {

        $img_path=asset('user_images/');
        $project=Project::with('customer','manager', 'tasks', 'company_worker')->where('id',$project)->first();

        if( !empty($project->customer))
        $project->customer->img=$img_path.'/'.$project->customer->img;
        if( !empty($project->manager))
        $project->manager->img=$img_path.'/'.$project->manager->img;
        if( !empty($project->company_worker))
        $project->company_worker->img=$img_path.'/'.$project->company_worker->img;
      //  $project->progress=$project->tasks()->where('task_status',1)->sum('percentage');

        $employee=[];
        foreach ($project->tasks as $key => $value1) {
          if (isset($value1->employee->img)) {
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }

          $employee[]=$value1->employee;
        }
        $project->employee=$employee;
        foreach ($project->employee as $key => $value) {
          $employee_total_tasks=Task::where('project_id',$project->id)->where('employee_id',$value->id)->count();
          $employee_completed_tasks=Task::where('project_id',$project->id)->where('employee_id',$value->id)->where('task_status',1)->count();
          if(isset($value->id)){
            $value->total_tasks=$employee_total_tasks;
            $value->completed_tasks=$employee_completed_tasks;
          }

        }
        $project_employee=collect($project->employee)->groupBy('id')->map(function ($item) {
          return array_merge(...$item->toArray());
        });
        $decode_employee=json_decode($project_employee);
        $project_employees=[];
        foreach ($decode_employee as $key => $value) {
          $project_employees[]=$value;
        }
        $project->employee=$project_employees;
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
        //$project->manager_id=$request->manager_id;
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

    //assign project manager to a project
    public function addProjectManager(Request $request, $pid){
        //dd($request->all());

        try{
            $project  = Project::find($pid);
            $project->manager_id = $request->manager_id;
            $project->save();
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //assign company worker to a project
    public function addCompanyWorker(Request $request, $pid){
        try{
            $project  = Project::find($pid);
            $project->company_worker_id = $request->company_worker_id;
            $project->save();
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //adding project documents
    public function uploadProjectOffer(Request $request, $project_id){
        try{
            $project = Project::find($project_id);
            //dd($request->project_offer);
            if (!empty($request->project_offer)) {
                $doc_name = time().'.'.$request->project_offer->extension();
               // dd($doc_name);
                $request->project_offer->move(public_path('project_files'), $doc_name);
                $project->project_offer = $doc_name;
            }
            $project->save();
            $project->project_offer=asset('project_files/' . $project->project_offer);
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    public function uploadProjectDrawing(Request $request, $project_id){
        try{
            $project = Project::find($project_id);
            //dd($request->project_offer);
            if (!empty($request->project_drawing)) {
                $doc_name = time().'.'.$request->project_drawing->extension();
                //dd($doc_name);
                $request->project_drawing->move(public_path('project_files'), $doc_name);
                $project->project_drawing = $doc_name;
            }
            $project->save();
            $project->project_drawing=asset('project_files/'.$project->project_drawing);
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function uploadProjectImages(Request $request){
        try{

            $project_image= new ProjectPicture();
            $project_image->project_id = $request->project_id;
            $project_image->employee_id = $request->employee_id;
            if (!empty($request->image)) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('project_images'), $imageName);
                $project_image->image = $imageName;
            }
            //dd($project_image);
            $project_image->save();
            $project_image->image=asset('project_images/' .  $project_image->image);
            return response()->json([
                $project_image
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get project files
    public function getProjectOffer(Request $request, $id){
        $name = Project::find($id);
        $name = $name->project_offer;
         $project_offer  =asset('project_files/' .$name);
       // dd($project_offer);
        return response()->json([
            $project_offer
        ], 200);
    }

    //get project files
    public function getProjectDrawing(Request $request, $id){
        $name = Project::find($id);
        $name = $name->project_drawing;
        $project_drawing  =asset('project_files/' .$name);
        //dd($project_drawing);
        return response()->json([
            $project_drawing
        ], 200);
    }

    //get project images
    public function getProjectImage(Request $request, $id){
        $project_pictures = ProjectPicture::where('project_id', $id)->get();
        foreach($project_pictures as $pj){
            $pj->image  =asset('project_images/' .$pj->image);
        }
        return response()->json([
            $project_pictures
        ], 200);
    }

    //delete project image
    public function deleteProjectImage(Request $request, $id){
        try
        {
            $project_pictures = ProjectPicture::find($id);
            $img_path = public_path('/project_images/').$project_pictures->image;
            if (File::exists($img_path)) {
                File::delete($img_path);
            }
            $project_pictures->delete();
            $msg="deleted";
            return response()->json([
                $msg
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //add project extra work
    public function addExtraWork(Request $request){
        try{
            $extrawork= new ExtraWork();
            $extrawork->project_id=$request->project_id;
            $extrawork->employee_id=$request->employee_id;
            $extrawork->date=$request->date;
            $extrawork->hours=$request->hours;
            $extrawork->task_details=$request->task_details;
            $extrawork->created_by=$request->created_by;
           // dd($extrawork);
            $extrawork->save();

            //return $this->responseSuccess($step);

            return response()->json([
                $extrawork
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get extra tasks
    public function getExtraWork(Request $request, $pid){
        try{
            $extrawork= ExtraWork::where('project_id', $pid)->get();
            return response()->json([
                $extrawork
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //add project order details
    public function addOrderDetails(Request $request){
        try{
            //dd($request->all());
            $order_details= new OrderDetail();
            $order_details->order_detail=$request->order_detail;
            $order_details->price=$request->price;
            $order_details->created_by=$request->created_by;
            $order_details->project_id=$request->project_id;
            $order_details->employee_id=$request->employee_id;
            // dd($extrawork);
            $order_details->save();

            //return $this->responseSuccess($step);

            return response()->json([
                $order_details
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get orderDetails
    public function getOrderDetails(Request $request, $pid){
        try{
            $order_details= OrderDetail::where('project_id', $pid)->get();
            return response()->json([
                $order_details
            ], 200);
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
          //getting all steps in the project
          $steps = Step::where('project_id', $project->id)->get();
          $steps_ids = array();
          foreach($steps as $step){
              $steps_ids[]= $step->id;
          }
          $tasks = Task::whereIn('step_id', $steps_ids)->get();
          //$tasks = \Illuminate\Support\Facades\DB::select(DB::raw("select  * from tasks where step_id in ($steps_ids[0])"));
          //deleting tasks and steps
          foreach($tasks as $task){
              $task->delete();
          }
          foreach($steps as $step){
              $step->delete();
          }

          $project->delete();
          return response()->json([
              "deleted"
          ], 200);
      }catch (\Exception $e)
      {
        return $this->responseFail();
      }
    }

    //customer accepts a project
    public function acceptProject(Request $request, $project_id){
        try
        {
            $project = Project::find($project_id);
            $project->active = 1;
            $project->save();
           // dd($project);
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //customer rejects a project
    public function rejectProject(Request $request, $project_id){
        try
        {
            //getting all steps in the project
            $steps = Step::where('project_id', $project_id)->get();
            $steps_ids = array();
            foreach($steps as $step){
                $steps_ids[]= $step->id;
            }
            $tasks = Task::whereIn('step_id', $steps_ids)->get();
            //$tasks = \Illuminate\Support\Facades\DB::select(DB::raw("select  * from tasks where step_id in ($steps_ids[0])"));
            //deleting tasks and steps
            foreach($tasks as $task){
                $task->delete();
            }
            foreach($steps as $step){
                $step->delete();
            }

            $project = Project::find($project_id);
            $project->delete();
            return response()->json([
                "deleted"
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    public function pinProject(StorePinProjectRequest $request){
      try{
        $check_pin_project = Pinproject::where('user_id',$request->user_id)->where('project_id',$request->project_id)->first();
        if(!empty($check_pin_project)){
          $check_pin_project->delete();
          $pin_project="project unpined successfuly !";
          return $this->responseSuccess($pin_project);
        }else{
          $pin_project=new Pinproject();
          $pin_project->user_id= $request->user_id;
          $pin_project->project_id=$request->project_id;
          $pin_project->save();

          return $this->responseSuccess($pin_project);
        }
      }catch (\Exception $e)
      {
        return $this->responseFail();
      }
    }
    public function getUserPinProject($user_id){

      try
      {
        $user_pined_projects=Pinproject::where('user_id', $user_id)->get();



        $project_id=[];

        foreach ($user_pined_projects as $key => $value) {
          $project_id[]=$value->project_id;
        }
        $pined_projects=Project::with('customer')->whereIn('id',$project_id)->get();
        //checking status of projects (completed, in-progress) on basis of its tasks)
        foreach($pined_projects as $prj){
               $tasks = Task::where('project_id', $prj->id)
                   ->whereIn('task_status', [0,1])
                   ->get();

               if(count($tasks)>0){
                   $prj->status = 1;
                   $prj->update();
               }
               else{
                   $prj->status = 2;
                   $prj->update();
               }
        }
          $img_path=asset('user_images/');
          foreach ($pined_projects as $key => $prj) {
              if(isset($prj->customer->img)){
                  $prj->customer->img=$img_path.'/'.$prj->customer->img;
              }
          }

        return $this->responseSuccess($pined_projects);
      }catch (\Exception $e)
      {
        return $this->responseFail();
      }
    }
    public function getAdminProjects($admin_id){ try
      {
         // dd($admin_id);
        $projects=Project::with('customer','tasks','pinnedproject', 'manager')->get();
        $pin_status=0;
        $img_path=asset('user_images/');
        $projects_with_manager = Project::where('manager_id',  '!=', NULL)->get();

        //checking status of projects (completed, in-progress) on basis of its tasks)
        foreach($projects as $prj){
            $tasks = Task::where('project_id', $prj->id)
                    ->whereIn('task_status', [0,1])
                    ->get();

            if(count($tasks)>0){
                $prj->status = 1;
                $prj->update();
            }
            else{
                $prj->status = 2;
                $prj->update();
            }
        }


        foreach ($projects as $key => $value) {
           // $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
            $employee=[];
            foreach ($value->tasks as $key => $value1) {
              if(isset($value1->employee->img)){
                $value1->employee->img=$img_path.'/'.$value1->employee->img;
              }
              $employee[]=$value1->employee;
            }
            $admin_pined_project=Pinproject::where('user_id', $admin_id)->where('project_id',$value->id)->first();
            if(isset($admin_pined_project->id)){
              $value->pin_status=1;
            }else{
             $value->pin_status=0;
           }
           // dd($projects);
           $value->employee=$employee;
             $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
                return array_merge(...$item->toArray());
                });
                $decode_employee=json_decode($project_employee);
                $project_employees=[];
                foreach ($decode_employee as $key => $value2) {
                  $project_employees[]=$value2;
                }
                $value->employee=$project_employees;
         }

        //$projects=new ProjectCollection($projects);
        //dd($projects);
        return $this->responseSuccess($projects);
      }catch (\Exception $e)
      {
        return $this->responseFail();
      }
    }
    public function adminOngoingProjects($admin_id){
        try {
            $get_projects=Project::all();

            //checking status of projects (completed, in-progress) on basis of its tasks)
            foreach($get_projects as $prj){
                $tasks = Task::where('project_id', $prj->id)
                    ->whereIn('task_status', [0,1])
                    ->get();

                if(count($tasks)>0){
                    $prj->status = 1;
                    $prj->update();
                }
                else{
                    $prj->status = 2;
                    $prj->update();
                }
            }
            $project_ids=[];
            foreach ($get_projects as $key => $value1) {
                if($value1->status ==0 || $value1->status ==1 ){
                    $project_ids[]=$value1->id;
                }
            }
           // dd($get_projects);
            $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$project_ids)->get();
            $img_path=asset('user_images/');
            $pin_status=0;
            foreach ($projects as $key => $value) {
                /* $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
                $employee=[];
                foreach ($value->tasks as $key => $value1) {
                    if(isset($value1->employee->img)){
                        $value1->employee->img=$img_path.'/'.$value1->employee->img;
                    }
                    $employee[]=$value1->employee;
                }
                $manager_pined_project=Pinproject::where('user_id', $admin_id)->where('project_id',$value->id)->first();
                if(isset($manager_pined_project->id)){
                    $value->pin_status=1;
                }else{
                    $value->pin_status=0;
                }
                $value->employee=$employee;
                $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
                    return array_merge(...$item->toArray());
                });
                $decode_employee=json_decode($project_employee);
                $project_employees=[];
                foreach ($decode_employee as $key => $value2) {
                    $project_employees[]=$value2;
                }
                $value->employee=$project_employees;
            }
            // $projects=new ProjectCollection($projects);
            return $this->responseSuccess($projects);

        } catch (\Exception $e) {
            return $this->responseFail();
        }

    }
    public function employeeForProject($project_id){
      try
      {
        $team=ProjectTeam::where('project_id', $project_id)->get();
        $employee_ids=[];
        foreach ($team as $key => $task) {
          $employee_ids[]=$task->employee_id;
        }
        $employees=User::whereNotIn('id', $employee_ids)->where('user_type',3)->get();
        $img_path=asset('user_images/');
        foreach ($employees as $key => $value) {
          $value->img=$img_path.'/'.$value->img;
        }
        return $this->responseSuccess($employees);
      }catch (\Exception $e)
      {
        return $this->responseFail();
      }
    }
    public function employeeForCompany(Request $request, $project_id){
        try
        {
            $by_company = intval($_GET['by_company']);

            $team=CompanyTeam::where('project_id', $project_id)->get();
            $employee_ids=[];
            foreach ($team as $key => $task) {
                $employee_ids[]=$task->employee_id;
            }

            $employees=User::whereNotIn('id', $employee_ids)->where('user_type',3)->where('by_company', $by_company)->get();
            $img_path=asset('user_images/');
            foreach ($employees as $key => $value) {
                $value->img=$img_path.'/'.$value->img;
            }
            return $this->responseSuccess($employees);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    public function employeeProjectDetails($project_id){
      try {
        $img_path=asset('user_images/');
        $project_details=Project::with('customer')->where('id',$project_id)->first();
        $project_details->customer->img=$img_path.'/'.$project_details->customer->img;
        return $this->responseSuccess($project_details);
      } catch (\Exception $e) {
        return $this->responseFail();
      }
    }
    public function customerTotalProjects($customer_id){
      try {

       /*   $projects_managers = DB::table('projects')
              ->leftJoin('users', 'users.id', '=', 'projects.manager_id')
              ->get();*/
        $projects=Project::with('customer','tasks','pinnedproject', 'manager')->where('customer_id',$customer_id)->get();
     //  dd($projects);
        /*$managers = array();
        foreach ($projects as $project){
            $managers[] = $project->manager_id;
        }
          $manager_id[0] = implode(',', $managers);
          $manager_name_designations = \Illuminate\Support\Facades\DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id IN ($manager_id[0])"));
          foreach ($manager_name_designations as $end){
              $end->img=asset('user_images/' . $end->img);
          }
          $manager_info = $manager_name_designations;*/
          foreach($projects as $prj){
              $tasks = Task::where('project_id', $prj->id)->get();
              if(count($tasks)>0){
                  foreach ($tasks as $tk){
                      if($tk->task_status == 0 || $tk->task_status == 1){
                          $prj->status = 1;
                          $prj->update();
                          break;
                      }
                      elseif($tk->task_status == 2){
                          $prj->status = 2;
                          $prj->update();
                      }
                  }
              }
          }
        $img_path=asset('user_images/');
        $pin_status=0;
        foreach ($projects as $key => $value) {
        /*  $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
          $employee=[];
          foreach ($value->tasks as $key => $value1) {
            if(isset($value1->employee->img)){
              $value1->employee->img=$img_path.'/'.$value1->employee->img;
            }
            $employee[]=$value1->employee;
          }
          $customer_pined_project=Pinproject::where('user_id', $customer_id)->where('project_id',$value->id)->first();
          if(isset($customer_pined_project->id)){
            $value->pin_status=1;
          }else{
           $value->pin_status=0;
         }
         $value->employee=$employee;
         $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
            foreach (  $value->employee as $emp){
                $emp->img =  $emp->img;
            }
       }


          //$projects=new ProjectCollection($projects);
      //  dd($projects);
          return response()->json([
              $projects,
             // $manager_info
          ], 200);

     } catch (\Exception $e) {
      return $this->responseFail();
    }

  }
  public function customerCompletedProjects($customer_id){
    try {
      $get_projects=Project::with('tasks')->where('customer_id',$customer_id)->get();
      /*foreach ($get_projects as $key => $value) {
        $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
      }*/
      $project_ids=[];
      foreach ($get_projects as $key => $value1) {
        if($value1->progress==100){
          $project_ids[]=$value1->id;
        }
      }
      $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$project_ids)->get();
      $img_path=asset('user_images/');
      $pin_status=0;
      foreach ($projects as $key => $value) {
       /* $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
        $employee=[];
        foreach ($value->tasks as $key => $value1) {
          if(isset($value1->employee->img)){
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }
          $employee[]=$value1->employee;
        }
        $customer_pined_project=Pinproject::where('user_id', $customer_id)->where('project_id',$value->id)->first();
        if(isset($customer_pined_project->id)){
          $value->pin_status=1;
        }else{
         $value->pin_status=0;
       }
       $value->employee=$employee;
       $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
     }
     $projects=new ProjectCollection($projects);
     return $this->responseSuccess($projects);

   } catch (\Exception $e) {
    return $this->responseFail();
  }

}
public function customerOngoingProjects($customer_id){
 try {
  $get_projects=Project::with('tasks')->where('customer_id',$customer_id)->get();
  /*foreach ($get_projects as $key => $value) {
    $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
  }*/
  $project_ids=[];
  foreach ($get_projects as $key => $value1) {
    if($value1->progress<100){
      $project_ids[]=$value1->id;
    }
  }
  $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$project_ids)->get();
  $img_path=asset('user_images/');
  $pin_status=0;
  foreach ($projects as $key => $value) {
    /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
    $employee=[];
    foreach ($value->tasks as $key => $value1) {
      if(isset($value1->employee->img)){
        $value1->employee->img=$img_path.'/'.$value1->employee->img;
      }
      $employee[]=$value1->employee;
    }
    $customer_pined_project=Pinproject::where('user_id', $customer_id)->where('project_id',$value->id)->first();
    if(isset($customer_pined_project->id)){
      $value->pin_status=1;
    }else{
     $value->pin_status=0;
   }
   $value->employee=$employee;
   $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
 }
 $projects=new ProjectCollection($projects);
 return $this->responseSuccess($projects);

} catch (\Exception $e) {
  return $this->responseFail();
}

}
public function getManagerProjects($manager_id){

  try
  {
    $projects=Project::with('customer','tasks','pinnedproject')->where('manager_id',$manager_id)->get();
    $pin_status=0;

      //checking status of projects (completed, in-progress) on basis of its tasks)
      foreach($projects as $prj){
          $tasks = Task::where('project_id', $prj->id)
              ->whereIn('task_status', [0,1])
              ->get();

          if(count($tasks)>0){
              $prj->status = 1;
              $prj->update();
          }
          else{
              $prj->status = 2;
              $prj->update();
          }
      }
    $img_path=asset('user_images/');
    foreach ($projects as $key => $value) {
        /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
        $employee=[];
        foreach ($value->tasks as $key => $value1) {
          if(isset($value1->employee->img)){
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }
          $employee[]=$value1->employee;
        }
        $manager_pined_project=Pinproject::where('user_id', $manager_id)->where('project_id',$value->id)->first();
        if(isset($manager_pined_project->id)){
          $value->pin_status=1;
        }else{
         $value->pin_status=0;
       }
       $value->employee=$employee;
       $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
     }

    //$projects=new ProjectCollection($projects);

    return $this->responseSuccess($projects);
  }catch (\Exception $e)
  {
    return $this->responseFail();
  }

}
public function getCompanyWorkerProjects($company_worker_id){

  try
  {
    $projects=Project::with('customer','tasks','pinnedproject')->where('company_worker_id',$company_worker_id)->get();
    $pin_status=0;

      //checking status of projects (completed, in-progress) on basis of its tasks)
      foreach($projects as $prj){
          $tasks = Task::where('project_id', $prj->id)
              ->whereIn('task_status', [0,1])
              ->get();

          if(count($tasks)>0){
              $prj->status = 1;
              $prj->update();
          }
          else{
              $prj->status = 2;
              $prj->update();
          }
      }
    $img_path=asset('user_images/');
    foreach ($projects as $key => $value) {
        /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
        $employee=[];
        foreach ($value->tasks as $key => $value1) {
          if(isset($value1->employee->img)){
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }
          $employee[]=$value1->employee;
        }
        $company_worker_pined_project=Pinproject::where('user_id', $company_worker_id)->where('project_id',$value->id)->first();
        if(isset($company_worker_pined_project->id)){
          $value->pin_status=1;
        }else{
         $value->pin_status=0;
       }
       $value->employee=$employee;
       $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
     }

    //$projects=new ProjectCollection($projects);

    return $this->responseSuccess($projects);
  }catch (\Exception $e)
  {
    return $this->responseFail();
  }

}

 public function managerCompletedProjects($manager_id){
    try {
      $get_projects=Project::with('tasks')->where('manager_id',$manager_id)->get();

      foreach ($get_projects as $key => $value) {
        /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
      }
        //checking status of projects (completed, in-progress) on basis of its tasks)
        foreach($get_projects as $prj){
            $tasks = Task::where('project_id', $prj->id)
                ->whereIn('task_status', [0,1])
                ->get();

            if(count($tasks)>0){
                $prj->status = 1;
                $prj->update();
            }
            else{
                $prj->status = 2;
                $prj->update();
            }
        }
      $project_ids=[];
      foreach ($get_projects as $key => $value1) {
        if($value1->status==2){
          $project_ids[]=$value1->id;
        }
      }
       // dd($project_ids);
      $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$project_ids)->get();

      $img_path=asset('user_images/');
      $pin_status=0;
      foreach ($projects as $key => $value) {
        /*$value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
        $employee=[];
        foreach ($value->tasks as $key => $value1) {
          if(isset($value1->employee->img)){
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }
          $employee[]=$value1->employee;
        }
        $manager_pined_project=Pinproject::where('user_id', $manager_id)->where('project_id',$value->id)->first();
        if(isset($manager_pined_project->id)){
          $value->pin_status=1;
        }else{
         $value->pin_status=0;
       }
       $value->employee=$employee;
       $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
            return array_merge(...$item->toArray());
            });
            $decode_employee=json_decode($project_employee);
            $project_employees=[];
            foreach ($decode_employee as $key => $value2) {
              $project_employees[]=$value2;
            }
            $value->employee=$project_employees;
     }
     //$projects=new ProjectCollection($projects);
       // dd($projects);
     return $this->responseSuccess($projects);

   } catch (\Exception $e) {
    return $this->responseFail();
  }

}
public function managerOngoingProjects($manager_id){
     try {
      $get_projects=Project::with('tasks')->where('manager_id',$manager_id)->get();
      /*foreach ($get_projects as $key => $value) {
        $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
      }*/

         //checking status of projects (completed, in-progress) on basis of its tasks)
         foreach($get_projects as $prj){
             $tasks = Task::where('project_id', $prj->id)
                 ->whereIn('task_status', [0,1])
                 ->get();

             if(count($tasks)>0){
                 $prj->status = 1;
                 $prj->update();
             }
             else{
                 $prj->status = 2;
                 $prj->update();
             }
         }
      $project_ids=[];
      foreach ($get_projects as $key => $value1) {
        if($value1->status ==0 || $value1->status ==1 ){
          $project_ids[]=$value1->id;
        }
      }
      $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$project_ids)->get();
      $img_path=asset('user_images/');
      $pin_status=0;
      foreach ($projects as $key => $value) {
       /* $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');*/
        $employee=[];
        foreach ($value->tasks as $key => $value1) {
          if(isset($value1->employee->img)){
            $value1->employee->img=$img_path.'/'.$value1->employee->img;
          }
          $employee[]=$value1->employee;
        }
        $manager_pined_project=Pinproject::where('user_id', $manager_id)->where('project_id',$value->id)->first();
        if(isset($manager_pined_project->id)){
          $value->pin_status=1;
        }else{
         $value->pin_status=0;
       }
       $value->employee=$employee;
       $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
                return array_merge(...$item->toArray());
                });
                $decode_employee=json_decode($project_employee);
                $project_employees=[];
                foreach ($decode_employee as $key => $value2) {
                  $project_employees[]=$value2;
                }
                $value->employee=$project_employees;
     }
        // $projects=new ProjectCollection($projects);
     return $this->responseSuccess($projects);

    } catch (\Exception $e) {
      return $this->responseFail();
    }

    }
}
