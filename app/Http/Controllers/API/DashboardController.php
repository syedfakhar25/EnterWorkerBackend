<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Traits\ApiMessagesTrait;
use App\Models\Project;
use App\Models\Pinproject;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  use ApiMessagesTrait;

  public function adminDashboard($id){

    try
    {
      $projects=Project::get();
      $total_projects=$projects->count();
     /* foreach ($projects as $key => $value) {
        $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
      }*/
      $completed_projects=$projects->where('progress',100)->count();
      $ongoing_projects=$projects->where('progress','<',100)->count();
      $employees=User::where('user_type',3)->get();
      $total_employee=$employees->count();
     // $by_designation = $employees->groupBy('designation')->first();
      $by_designation = DB::table('users')->select('designation', DB::raw('count(*) as total_designation'))
          ->groupBy('designation')
          ->get();
     // DB::enableQueryLog();
//      $raaaa =DB::select(DB::raw("select count(*), users.designation_id, designations.designation_name, designations.color from users inner join
//                designations on (users.designation_id = designations.id) GROUP BY users.designation_id"))->get();
        $user_designations =DB::select(DB::raw("select designations.* , count(*) as total from
                            users inner join designations  on (users.designation_id = designations.id) GROUP BY users.designation_id"));
        $designations = Designation::all();
      //dd($designations);
      /*$user_des = User::select('users.designation_id, designations.color, designations.designation_name, users.count(*)')
            ->join('designations', 'designations.id', '=', 'users.designation_id' )
            ->groupBy('users.designation_id')
            ->get();*/


      $total_carpenter=$employees->where('designation','carpenter')->count();
      $total_tiler=$employees->where('designation','tiler')->count();
      $total_electrician=$employees->where('designation','electrician')->count();
      $total_customer=User::where('user_type',4)->count();

      $user_pined_projects=Pinproject::where('user_id', $id)->get();
      $project_id=[];

      foreach ($user_pined_projects as $key => $value) {
        $project_id[]=$value->project_id;
      }
      $pined_projects=Project::whereIn('id',$project_id)->get();

      foreach ($pined_projects as $key => $value) {
        $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
      }

      $admin_dashboard_data=[
        'total_projects'=>$total_projects,
        'completed_projects'=>$completed_projects,
        'ongoing_projects'=>$ongoing_projects,
        'total_employee'=>$total_employee,
        'total_carpenter'=>$total_carpenter,
        'total_tiler'=>$total_tiler,
        'total_electrician'=>$total_electrician,
        'total_customer'=>$total_customer,
        'pined_projects'=>$pined_projects,
        'user_designations'=>$user_designations,
      ];

      return $this->responseSuccess($admin_dashboard_data);
    }catch (\Exception $e)
    {
      return $this->responseFail();
    }

  }
  public function managerDashboard($manager_id){
     try {
            $projects=Project::where('manager_id',$manager_id)->get();
            $total_projects=$projects->count();
            /*foreach ($projects as $key => $value) {
              $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
            }*/
            $completed_projects=$projects->where('progress',100)->count();
            $ongoing_projects=$projects->where('progress','<',100)->count();
            $user_pined_projects=Pinproject::where('user_id', $manager_id)->get();
            $project_id=[];
            foreach ($user_pined_projects as $key => $value) {
                $project_id[]=$value->project_id;
            }
            $pined_projects=Project::whereIn('id',$project_id)->get();

            foreach ($pined_projects as $key => $value) {
                  $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
                   }
                       $customer_dashboard_data=[
                          'total_projects'=>$total_projects,
                          'completed_projects'=>$completed_projects,
                          'ongoing_projects'=>$ongoing_projects,
                          'pined_projects'=>$pined_projects,
                        ];
                return $this->responseSuccess($customer_dashboard_data);
          } catch (\Exception $e) {
            return $this->responseFail();
          }
  }
  public function employeeDashboard($employee_id){

        try {
              $recent_tasks=Task::where('employee_id',$employee_id)->with('project')->orderBy('created_at')->get();
              $completed_tasks=Task::where('employee_id',$employee_id)->where('task_status',1)->get();
              $ongoing_tasks=Task::where('employee_id',$employee_id)->where('task_status',0)->get();
              $active_tasks=Task::where('employee_id',$employee_id)->where('active',1)->get();
             $projects = DB::select(DB::raw("select projects.*  from  tasks left join projects  on
                        (tasks.project_id = projects.id) WHERE tasks.employee_id = $employee_id GROUP BY projects.id"));
              $employee_dashboard_data=[
                  'total_projects' => count($projects),
                'total_tasks'=>$recent_tasks->count(),
                'completed_tasks'=>$completed_tasks->count(),
                'pending_tasks'=>$ongoing_tasks->count(),
                  'active_tasks' => $active_tasks->count(),
                'recent_tasks'=>$recent_tasks,
              ];
          return $this->responseSuccess($employee_dashboard_data);

        } catch (\Exception $e) {
          return $this->responseFail();
        }
  }

  public function customerDashboard($customer_id){
        try {
            $projects=Project::where('customer_id',$customer_id)->get();
            $total_projects=$projects->count();
           /* foreach ($projects as $key => $value) {
              $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
            }*/
            $completed_projects=$projects->where('progress',100)->count();
            $ongoing_projects=$projects->where('progress','<',100)->count();
            $user_pined_projects=Pinproject::where('user_id', $customer_id)->get();
            $project_id=[];
            foreach ($user_pined_projects as $key => $value) {
                $project_id[]=$value->project_id;
            }
            $pined_projects=Project::whereIn('id',$project_id)->get();

            foreach ($pined_projects as $key => $value) {
                  $value->progress=$value->tasks()->where('task_status',1)->sum('percentage');
                   }
                       $customer_dashboard_data=[
                          'total_projects'=>$total_projects,
                          'completed_projects'=>$completed_projects,
                          'ongoing_projects'=>$ongoing_projects,
                          'pined_projects'=>$pined_projects,
                        ];
                return $this->responseSuccess($customer_dashboard_data);
          } catch (\Exception $e) {
            return $this->responseFail();
          }
  }
}
