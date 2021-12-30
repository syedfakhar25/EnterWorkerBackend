<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CompanyTeam;
use App\Models\Project;
use App\Models\ProjectTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $company_team = new CompanyTeam();
            $company_team->project_id = $request->project_id;
            $company_team->employee_id = $request->employee_id;
            $company_team->save();

            return response()->json([
                $company_team
            ], 200);
        }catch (\Exception $e)
        {
            return $this->responseFail();
        }
    }

    //get project Team of Specific Project
    public function getCompanyTeam(Request $request, $project_id){
        try{
            //dd($project_id);
            $team_members = CompanyTeam::where('project_id', $project_id)->get();
            $manager_id = Project::select('manager_id')->where('id', $project_id)->get();

            if($manager_id[0]->manager_id != NULL){
                $manager = User::find($manager_id);
                $m_id = $manager[0]->id;
                $manager_name_designation =DB::select(DB::raw("select  users.id as manager_id, users.first_name,users.last_name, users.img, designations.designation_name from users
                          join designations on designations.id = users.designation_id where users.id = $m_id"));
                $manager_name_designation[0]->img=asset('user_images/' .$manager_name_designation[0]->img);
            }
            else{
                $manager_name_designation = NULL;
            }


            $employees= array();
            foreach ($team_members as $team){
                $employees[]= $team->employee_id;
            }
            //  dd($employees);
            $employees_id= array();
            $employees_id[0] = implode(',', $employees);
            $emp_name_designations =DB::select(DB::raw("select  users.id, users.first_name, users.last_name, users.img, designations.designation_name from users
                      join designations on designations.id = users.designation_id where users.id IN ($employees_id[0])"));
            $team_with_manager = array(
                'team' => $emp_name_designations,
                'manger' => $manager_name_designation
            );

            foreach ($emp_name_designations as $end){
                $end->img=asset('user_images/' . $end->img);
            }
            return response()->json([
                $team_with_manager
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
