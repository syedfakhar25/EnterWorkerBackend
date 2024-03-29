<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyTeam;
use App\Models\ExtraWork;
use App\Models\OrderDetail;
use App\Models\PorjectCompanyWorker;
use App\Models\Project;
use App\Models\ProjectManager;
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
            //project actual address
            $project->street=$request->street;
            $project->postal_code=$request->postal_code;
            $project->city=$request->city;

            //customer's address
            $project->cus_address=$request->cus_address;
            $project->cus_postal_code=$request->cus_postal_code;
            $project->cus_city=$request->cus_city;

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
            $files_path = asset('project_files/');
            $project=Project::with('customer','manager', 'tasks', 'company_worker')->where('id',$project)->first();

            ///////////////////////////////////////////////////////
            if( !empty($project->customer))
                $project->customer->img=$img_path.'/'.$project->customer->img;
            if( !empty($project->manager))
                $project->manager->img=$img_path.'/'.$project->manager->img;
            if( !empty($project->company_worker))
                $project->company_worker->img=$img_path.'/'.$project->company_worker->img;
            //  $project->progress=$project->tasks()->where('task_status',1)->sum('percentage');

            if(!empty($project->project_offer)){
                $project->project_offer=$files_path.'/'.$project->project_offer;
            }
            if(!empty($project->project_drawing)){
                $project->project_drawing=$files_path.'/'.$project->project_drawing;
            }
            if(!empty($project->offer_with_price)){
                $project->offer_with_price=$files_path.'/'.$project->offer_with_price;
            }
            if(!empty($project->contract)){
                $project->contract=$files_path.'/'.$project->contract;
            }
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
            //project actual address
            $project->street=$request->street;
            $project->postal_code=$request->postal_code;
            $project->city=$request->city;

            //customer's address
            $project->cus_address=$request->cus_address;
            $project->cus_postal_code=$request->cus_postal_code;
            $project->cus_city=$request->cus_city;
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
        try{
            $managers= $request->manager_id;
            foreach($managers as $key=>$value){
                $project_manager = new ProjectManager();
                $project_manager->project_id = $pid;
                $project_manager->manager_id = $value;
                $project_manager->save();
            }

            $project = Project::find($pid);
            $project_manager = ProjectManager::where('project_id', $pid)->get();
            return response()->json([
                $project,
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    //delete manager from project
    public function removeManager(Request $request, $pid){
        try {
            $manager = $request->manager;
            $project_manager = DB::table('project_managers')->where('project_id', $pid)->where('manager_id', $manager)->delete();
            return response()->json([
                'removed'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    //delete company from project
    public function removeCompany(Request $request, $id){
        try {
            $company = $request->company;
            $project_company = DB::table('porject_company_workers')->where('project_id', $id)->where('company_worker_id', $company)->delete();
            return response()->json([
                'removed'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }
    //delete company from project
    public function removeTeamMember(Request $request, $id){
        try {
            $employee = $request->employee;
            $project_team = DB::table('project_teams')->where('project_id', $id)->where('employee_id', $employee)->delete();
            return response()->json([
                'removed'
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get all managers of Projects
    public function getProjectManagers($id){
        try{
            $project_managers = ProjectManager::select('manager_id')->where('project_id', $id)->get();
            if(count($project_managers)>0){
                $managers= array();
                foreach ($project_managers as $team){
                    $managers[]= $team->manager_id;
                }
                //dd($managers);
                $managers_id[0] = implode(',', $managers);
                $emp_name_designations = \Illuminate\Support\Facades\DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, users.manager_type, users.user_type,
                        designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id IN ($managers_id[0])"));
                foreach ($emp_name_designations as $end){
                    $end->img=asset('user_images/' . $end->img);
                }
                $project_managers = $emp_name_designations;
            }else{
                $project_managers = [];
            }
            return response()->json([
                $project_managers

            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //assign company worker to a project
    public function addCompanyWorker(Request $request, $pid){
        try{
            $company_workers= $request->company_worker_id;
            foreach($company_workers as $key=>$value){
                $company_worker = new PorjectCompanyWorker();
                $company_worker->project_id = $pid;
                $company_worker->company_worker_id = $value;
                $company_worker->save();
            }
            $project = Project::find($pid);
            $company_workers = PorjectCompanyWorker::where('project_id', $pid)->get();
            return response()->json([
                $project,
                $company_workers
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function getProjectsCompanyWorker(Request $request, $id){
        try{
            $project_c_workers = PorjectCompanyWorker::where('project_id', $id    )->get();

            $company_ids= array();
            foreach ($project_c_workers as $cw){
                $company_ids[] = $cw->company_worker_id;
            }
            $company_workers = Company::whereIn('id', $company_ids)->get();
            foreach ($company_workers as $end){
                $end->image=asset('company_images/' . $end->image);
            }
            return response()->json([
                $company_workers
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

    public function uploadProjectOfferPrice(Request $request, $project_id){
        try{
            $project = Project::find($project_id);
            //dd($request->project_offer);
            if (!empty($request->offer_with_price)) {
                $doc_name = time().'.'.$request->offer_with_price->extension();
                // dd($doc_name);
                $request->offer_with_price->move(public_path('project_files'), $doc_name);
                $project->offer_with_price = $doc_name;
            }
            $project->save();
            $project->offer_with_price=asset('project_files/' . $project->offer_with_price);
            return response()->json([
                $project
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function uploadProjectContract(Request $request, $project_id){
        try{
            $project = Project::find($project_id);
            //dd($request->project_offer);
            if (!empty($request->contract)) {
                $doc_name = time().'.'.$request->contract->extension();
                // dd($doc_name);
                $request->contract->move(public_path('project_files'), $doc_name);
                $project->contract = $doc_name;
            }
            $project->save();
            $project->contract=asset('project_files/' . $project->contract);
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

    //get project offer for client
    public function getProjectOfferClient(Request $request, $id){
        try{
            $name = Project::where('customer_id', $id)->get();
            //  dd($name);
            $name = $name[0]->project_offer;
            $project_offer  =asset('project_files/' .$name);
            // dd($project_offer);
            return response()->json([
                $project_offer
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get project offer for client
    public function getProjectOfferPriceClient(Request $request, $id){
        try{
            $name = Project::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
            //  dd($name);
            $name = $name[0]->offer_with_price;
            $offer_with_price  =asset('project_files/' .$name);
            // dd($project_offer);
            return response()->json([
                $offer_with_price
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get project offer for client
    public function getProjectContractClient(Request $request, $id){
        try{
            $name = Project::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
            //  dd($name);
            $name = $name[0]->contract;
            $contract  =asset('project_files/' .$name);
            // dd($project_offer);
            return response()->json([
                $contract
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function OfferComment(Request $request, $id){
        try{
            $project = Project::where('customer_id', $id)->get();
            $project[0]->offer_comment = $request->offer_comment;
            $project[0]->save();
            return response()->json([
                $project[0]
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function DrawingComment(Request $request, $id){
        try{
            $project = Project::where('customer_id', $id)->get();
            $project[0]->drawing_comment = $request->drawing_comment;
            $project[0]->save();
            return response()->json([
                $project[0]
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function TimelineComment(Request $request, $id){
        try{
            $project = Project::where('customer_id', $id)->get();
            $project[0]->timline_comment = $request->timline_comment;
            $project[0]->save();
            return response()->json([
                $project[0]
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function ProjectTimeline(Request $request, $id){
        $project = Project::where('customer_id', $id)->get();
        $start_date = $project[0]->start_date;
        $end_date = $project[0]->end_date;
        $timeline = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        return response()->json([
            $timeline
        ], 200);
    }

    //get project files
    public function getProjectDrawingClient(Request $request, $id){
        $name = Project::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $name = $name[0]->project_drawing;
        $project_drawing  =asset('project_files/' .$name);
        //dd($project_drawing);
        return response()->json([
            $project_drawing
        ], 200);
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
    public function removeProjectOffer(Request $request, $id){
        $project = Project::find($id);
        $project_offer = public_path('/project_files/').$project->project_offer;
        if (File::exists($project_offer)) {
            File::delete($project_offer);
        }
        $project->project_offer = NULL;
        $project->save();
        return response()->json([
            'removed'
        ], 200);
    }
    public function removeProjectOfferPrice(Request $request, $id){
        $project = Project::find($id);
        $offer_with_price = public_path('/project_files/').$project->offer_with_price;
        if (File::exists($offer_with_price)) {
            File::delete($offer_with_price);
        }
        $project->offer_with_price = NULL;
        $project->save();
        return response()->json([
            'removed'
        ], 200);
    }

    public function removeProjectDrawing(Request $request, $id){
        $project = Project::find($id);
        $drawing = public_path('/project_files/').$project->project_drawing;
        if (File::exists($drawing)) {
            File::delete($drawing);
        }
        $project->project_drawing = NULL;
        $project->save();
        return response()->json([
            'removed'
        ], 200);
    }

    public function removeProjectContract(Request $request, $id){
        $project = Project::find($id);
        $contract = public_path('/project_files/').$project->contract;
        if (File::exists($contract)) {
            File::delete($contract);
        }
        $project->contract = NULL;
        $project->save();
        return response()->json([
            'removed'
        ], 200);
    }

    //get project files
    public function getProjectOfferPrice(Request $request, $id){
        $name = Project::find($id);
        $name = $name->offer_with_price;
        $offer_with_price  =asset('project_files/' .$name);
        // dd($project_offer);
        return response()->json([
            $offer_with_price
        ], 200);
    }

    //get project files
    public function getProjectContract(Request $request, $id){
        $name = Project::find($id);
        $name = $name->contract;
        $contract  =asset('project_files/' .$name);
        // dd($project_offer);
        return response()->json([
            $contract
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
            $teams= ProjectTeam::where('project_id', $project->id)->get();
            $managers = ProjectManager::where('project_id', $project->id)->get();
            $company_workers = PorjectCompanyWorker::where('project_id', $project->id)->get();
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
            foreach ($teams as $team){
                $team->delete();
            }
            foreach ($managers as $manager){
                $manager->delete();
            }
            foreach ($company_workers as $cw){
                $cw->delete();
            }

            $project->delete();
            $offer = public_path('/project_files/').$project->project_offer;
            $offer_price = public_path('/project_files/').$project->offer_with_price;
            $drawing = public_path('/project_files/').$project->project_drawing;
            $contract = public_path('/project_files/').$project->contract;
            if (File::exists($offer_price)) {
                File::delete($offer_price);
            }
            if (File::exists($offer)) {
                File::delete($offer);
            }
            if (File::exists($drawing)) {
                File::delete($drawing);
            }
            if (File::exists($contract)) {
                File::delete($contract);
            }
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
        $projects=Project::with('customer','tasks','pinnedproject', 'manager')->where('submit', 1)->get();
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
            $this->ProjectPercentage($prj->id);
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
            $employees=User::whereNotIn('id', $employee_ids)->where('user_type',3)
                            ->where('by_company',0)->get();
            /*$employees=DB::table('users')
                ->orWhere(function($query) {
                    $query->where('by_company','null')
                        ->orWhere('by_company',0);
                })
                ->where('user_type',3)
                ->whereNotIn('id',$employee_ids)->get();*/
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

    public function managerForProject($project_id){
        try
        {
            $team=ProjectManager::where('project_id', $project_id)->get();
            $manager_ids=[];
            foreach ($team as $key => $task) {
                $manager_ids[]=$task->manager_id;
            }
            $managers = User::whereNotIn('id', $manager_ids)->where('user_type', 2)->get();
            $img_path=asset('user_images/');
            foreach ($managers as $key => $value) {
                $value->img=$img_path.'/'.$value->img;
            }
            return $this->responseSuccess($managers);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    public function allUsersForProject($project_id){
        try
        {
            $project = Project::find($project_id);
            // team of project
            $team_members = ProjectTeam::where('project_id', $project_id)->get();
            if(count($team_members)>0){
                $employees= array();
                foreach ($team_members as $team){
                    $employees[]= $team->employee_id;
                }
                $employees_id= array();
                $employees_id[0] = implode(',', $employees);
                $emp =DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, users.user_type,
                                designations.designation_name from users join designations on
                                designations.id = users.designation_id
                                where users.id IN ($employees_id[0])"));
            }else{
                $emp = '';
            }

            //managers of project
            $managers = $this->getProjectManagers($project_id);

            //companies
            $project_c_workers = PorjectCompanyWorker::where('project_id', $project_id)->get();
            if(count($project_c_workers)>0){
                $company_ids= array();
                foreach ($project_c_workers as $cw){
                    $company_ids[] = $cw->company_worker_id;
                }
                $company_workers = Company::whereIn('id', $company_ids)->get();
                foreach ($company_workers as $end){
                    $end->image=asset('company_images/' . $end->image);
                }
                $company = $company_workers;
            }else{
                $company = '';
            }

            //project's customer
            $customer_id = $project->customer_id;
            $customer =DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, users.user_type,
                    designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id=$customer_id"));
            $all_users = [
                'employees' => $emp,
                'managers' => $managers,
                'company' => $company,
                'customer' => $customer
            ];
            return $this->responseSuccess($all_users);
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

            $employees=User::whereNotIn('id', $employee_ids)->where('user_type',5)->where('by_company', $by_company)->get();
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

    public function employeeForThisCompany(Request $request, $company){
        try
        {
            $users = User::where('by_company', $company)->get();

            $employee_ids=[];
            foreach ($users as $key => $task) {
                $employee_ids[]=$task->id;
            }

            $employees=User::whereIn('id', $employee_ids)->where('user_type',5)->where('by_company', $company)->get();
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
    public function customerLatestProject($customer_id){
        try {
            $files_path = asset('project_files/');
            $project=Project::with('customer','tasks','pinnedproject')->where('customer_id',$customer_id)->orderBy('created_at', 'desc')->first();

            //getting documents with their paths
            if(!empty($project->project_offer)){
                $project->project_offer=$files_path.'/'.$project->project_offer;
            }
            if(!empty($project->project_drawing)){
                $project->project_drawing=$files_path.'/'.$project->project_drawing;
            }
            if(!empty($project->offer_with_price)){
                $project->offer_with_price=$files_path.'/'.$project->offer_with_price;
            }
            if(!empty($project->contract)){
                $project->contract=$files_path.'/'.$project->contract;
            }

            // getting employees from project team of project
            $team_ids= array();
            $project_team = ProjectTeam::where('project_id', $project->id)->get();
            foreach ($project_team as $pt){
                $team_ids[] = $pt->employee_id;
            }
            $team = User::whereIn('id', $team_ids)->get();
            $img_path=asset('user_images/');
            foreach ($team as $tm){
                $tm->img =$img_path.'/'.$tm->img;
            }

            //getting managers from project managers of project
            $manager_ids =array();
            $project_managers = ProjectManager::where('project_id', $project->id)->get();
            foreach ($project_managers as $pm){
                $manager_ids[] = $pm->manager_id;
            }
            $manager = User::whereIn('id', $manager_ids)->get();
            foreach ($manager as $m){
                $m->img =$img_path.'/'.$m->img;
            }

            return response()->json([
                $project,
                'team' => $team,
                'manager' => $manager
            ], 200);

        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }
    public function customerTotalProjects($customer_id){
        try {
            $files_path = asset('project_files/');
            $projects=Project::with('customer','tasks','pinnedproject', 'projectTeam')->where('customer_id',$customer_id)->get();
            $managers = array();
            foreach ($projects as $project){
                $project_managers = ProjectManager::where('project_id', $project->id)->get();
                foreach($project_managers as $pm){
                    if($pm!=NULL)
                        $managers[]= $pm->manager_id;
                }
                $team = array();
                $project_team = ProjectTeam::where('project_id', $project->id)->get();
                foreach ($project_team as $pt){
                   $team[] = User::where('id', $pt->employee_id)->get();
                }

                if(!empty($project->project_offer)){
                    $project->project_offer=$files_path.'/'.$project->project_offer;
                }
                if(!empty($project->project_drawing)){
                    $project->project_drawing=$files_path.'/'.$project->project_drawing;
                }
                if(!empty($project->offer_with_price)){
                    $project->offer_with_price=$files_path.'/'.$project->offer_with_price;
                }
                if(!empty($project->contract)){
                    $project->contract=$files_path.'/'.$project->contract;
                }
            }
            //dd($managers);
            $managers = User::whereIn('id', $managers)->get();
            $img_path=asset('user_images/');
            foreach ($managers as $mm){
                $mm->img = $img_path.'/'.$mm->img;
            }
            $managers = [
                'managers' => $managers
            ];
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
                $employee=[];
                foreach ($value->tasks as $key => $value1) {
                    if(isset($value1->employee->img)){
                        $value1->employee->img=$img_path.'/'.$value1->employee->img;
                    }
                    if (in_array($value1->employee, $employee)) {
                        $employee[] = $value1->employee;
                    }else{
                        $employee[] = $value1->employee;
                    }

                }
                $customer_pined_project=Pinproject::where('user_id', $customer_id)->where('project_id',$value->id)->first();
                if(isset($customer_pined_project->id)){
                    $value->pin_status=1;
                }else{
                    $value->pin_status=0;
                }
                $value->employee=$employee;
                if($value->employee !=NULL){
                    $project_employee=collect($value->employee)->groupBy('id')->map(function ($item) {
                        return array_merge(...$item->toArray());
                    });
                    if($project_employee!=NULL){
                        $decode_employee=json_decode($project_employee);
                        $project_employees=[];
                        if($project_employees!=NULL){
                            foreach ($decode_employee as $key => $value2) {
                                $project_employees[]=$value2;
                            }
                            $value->employee=$project_employees;
                            foreach (  $value->employee as $emp){
                                $emp->img =  $emp->img;
                            }
                        }

                    }

                }

            }


            //$projects=new ProjectCollection($projects);
            //  dd($projects);
            return response()->json([
                $projects,
                $managers,
                $team
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
            $projectManagers = ProjectManager::select('project_id')->where('manager_id', $manager_id)->get();
            $ids = [];
            foreach($projectManagers as $pc){
                $ids[] = $pc->project_id;
            }
            $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$ids)->where('submit', 1)->get();
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
            $projectCompany = PorjectCompanyWorker::where('company_worker_id', $company_worker_id)->get();
            $ids = [];
            foreach($projectCompany as $pc){
                $ids[] = $pc->project_id;
            }

            $projects=Project::with('customer','tasks','pinnedproject')->whereIn('id',$ids)->where('submit', 1)->get();
            //dd($projects);
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
            $project_id = ProjectManager::select('project_id')->whereIn('manager_id', $manager_id)->get();
            $get_projects=Project::with('tasks')->where('id',$project_id)->get();

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
            $project_id = ProjectManager::select('project_id')->where('manager_id', $manager_id)->get();
            $get_projects=Project::with('tasks')->whereIn('id',$project_id)->get();
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

    public function SubmitProject($id){
        try{
            $project = Project::find($id);
            $project->submit = 1;
            //checking percentage of project w.r.t steps' percentage
            $project_percentage = 0;
            $steps = Step::where('project_id', $project->id)->get();
            foreach( $steps as $step){
                $tasks = Task::where('step_id', $step->id)->get();
                $total_tasks = $tasks->count();
                if($total_tasks>0){
                    $task_percentage =($step->percentage/$total_tasks);
                    foreach($tasks as $task){
                        if($task->task_status==2){
                            $project_percentage+=$task_percentage;
                        }
                    }
                }
            }
            $project_percentage = intval($project_percentage);
            $project->percentage = $project_percentage;
            ///////////////////////////
            $project->save();
            return $this->responseSuccess(
                [
                    'submitted',
                    'percentage' => $project_percentage
                ]
            );
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }

    public function ProjectPercentage($id){
        try{
            $project = Project::find($id);
            //checking percentage of project w.r.t steps' percentage
            $project_percentage = 0;
            $steps = Step::where('project_id', $project->id)->get();
            foreach( $steps as $step){
                $tasks = Task::where('step_id', $step->id)->get();
                $total_tasks = $tasks->count();
                if($total_tasks>0){
                    $task_percentage =($step->percentage/$total_tasks);
                    foreach($tasks as $task){
                        if($task->task_status==2){
                            $project_percentage+=$task_percentage;
                        }
                    }
                }
            }
            $project_percentage = intval($project_percentage);
            $project->percentage = $project_percentage;
            ///////////////////////////
            $project->save();
            return $this->responseSuccess(
                $project_percentage
            );
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }

    public function CheckPercentage(Request $request, $id){
        try{
            $steps = Step::where('project_id', $id)->get();
            $current_step_percentage =0;
            $step_id = $request->step_id;
            if(!empty($step_id)){
                $current_step = Step::find($step_id);
                $current_step_percentage = $current_step->percentage;
            }
            $percentage = 0;
            if(count($steps)>0){
                foreach ($steps as $step){
                    if($step->percentage!=NULL)
                        $percentage+= $step->percentage;
                }
            }
            if($current_step_percentage  > 0){
                $percentage = $percentage - $current_step_percentage;
            }else{
                $percentage =$percentage;
            }
            return $this->responseSuccess(
                $percentage
            );
        } catch (\Exception $e) {
            return $this->responseFail();
        }
    }
}
