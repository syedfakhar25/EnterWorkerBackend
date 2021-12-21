<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\CalendereventController;
use App\Http\Controllers\API\ChatController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'auth'],function(){
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/verify', [AuthController::class,'verify']);
//     Route::post('/resend-otp', [AuthController::class,'resendOtp']);
       Route::post('/login', [AuthController::class,'login']);
      // Route::post('/login', [AuthController::class,'logintest']);
//     Route::get('/logout', [AuthController::class,'logout'])->middleware('auth:api');
        Route::get('/users-list', [ChatController::class, 'usersList'])->middleware('auth:api');
        Route::get('/chats', [ChatController::class, 'index'])->middleware('auth:api');
        Route::post('/fetch-messages', [ChatController::class,'fetchAllMessages'])->middleware('auth:api');
        Route::post('/messages', [ChatController::class,'sendMessage'])->middleware('auth:api');

    });

Route::group(['prefix'=>'admin'],function(){
    Route::resource('projects', ProjectController::class);
    Route::resource('users', UsersController::class);
    Route::get('dashboard/{id}', [DashboardController::class,'adminDashboard'])->name('admin.dashboard');
    Route::get('employees', [UsersController::class,'userEmployees']);
    Route::get('/get-projects/{admin_id}', [ProjectController::class,'getAdminProjects'])->name('admin.get.project');
    Route::get('get-events', [CalendereventController::class,'adminEvents'])->name('admin.events');
    Route::post('/event-notification', [CalendereventController::class,'adminEventNotification'])->name('admin.event.notification');

    //routes related to adding tasks, steps and manager in a Project
    Route::resource('tasks', TaskController::class);
    Route::get('get-tasks/{id}',  [\App\Http\Controllers\API\TaskController::class,'getTasks']);
    Route::resource('steps', \App\Http\Controllers\API\StepController::class);
    Route::get('get-steps/{id}',  [\App\Http\Controllers\API\StepController::class,'getSteps']);
    Route::put('/step-automation/{id}', [\App\Http\Controllers\API\StepController::class, 'stepAutomation']);
    Route::put('/add-manager/{id}', [ProjectController::class,'addProjectManager']);

    //routes related to adding project team in a Project
    Route::resource('project-team', \App\Http\Controllers\API\ProjectTeamController::class);
    Route::get('get-team/{id}', [\App\Http\Controllers\API\ProjectTeamController::class, 'getTeam']);

    //add project documents
    Route::post('upload-project-offer/{id}', [ProjectController::class, 'uploadProjectOffer']);
    Route::get('get-project-offer/{id}', [ProjectController::class, 'getProjectOffer']);
    Route::post('upload-project-drawing/{id}', [ProjectController::class, 'uploadProjectDrawing']);
    Route::get('get-project-drawing/{id}', [ProjectController::class, 'getProjectDrawing']);

    //routes for Designations CRUD
    Route::resource('designations', \App\Http\Controllers\DesignationController::class);

    });


Route::group(['prefix'=>'manager'],function(){
    Route::resource('projects', ProjectController::class);
    Route::get('dashboard/{manager_id}', [DashboardController::class,'managerDashboard'])->name('manager.dashboard');
    Route::get('/get-manager-projects/{manager_id}', [ProjectController::class,'getManagerProjects'])->name('manager.get.project');
    Route::get('completed-projects/{manager_id}', [ProjectController::class,'managerCompletedProjects'])->name('manager.completed.projects');
    Route::get('ongoing-projects/{manager_id}', [ProjectController::class,'managerOngoingProjects'])->name('manager.ongoing.projects');

    //routes related to adding tasks, steps and manager in a Project
    Route::resource('tasks', TaskController::class);
    Route::get('get-tasks/{id}',  [\App\Http\Controllers\API\TaskController::class,'getTasks']);
    Route::resource('steps', \App\Http\Controllers\API\StepController::class);
    Route::get('get-steps/{id}',  [\App\Http\Controllers\API\StepController::class,'getSteps']);
    Route::put('/add-manager/{id}', [ProjectController::class,'addProjectManager']);

    //routes related to adding project team in a Project
    Route::resource('project-team', \App\Http\Controllers\API\ProjectTeamController::class);
    Route::get('get-team/{id}', [\App\Http\Controllers\API\ProjectTeamController::class, 'getTeam']);

    //add project documents
    Route::post('upload-project-offer/{id}', [ProjectController::class, 'uploadProjectOffer']);
    Route::post('upload-project-drawing/{id}', [ProjectController::class, 'uploadProjectDrawing']);


    Route::resource('calenderevents', CalendereventController::class);
    Route::get('events/{manager_id}', [CalendereventController::class,'managerEvents'])->name('manager.events');
    Route::post('/event-notification', [CalendereventController::class,'managerEventNotification'])->name('manager.event.notification');
    });


Route::group(['prefix'=>'employee'],function(){
    Route::get('dashboard/{employee_id}', [DashboardController::class,'employeeDashboard'])->name('employee.dashboard');
    Route::get('total-tasks/{employee_id}', [TaskController::class,'employeeTotalTasks'])->name('employee.total.tasks');
    Route::get('completed-tasks/{employee_id}', [TaskController::class,'employeeCompletedTasks'])->name('employee.completed.tasks');
    Route::get('ongoing-tasks/{employee_id}', [TaskController::class,'employeeOngoingTasks'])->name('employee.ongoing.tasks');
    Route::get('project_details/{project_id}', [ProjectController::class,'employeeProjectDetails'])->name('employee.project.details');
    Route::get('events/{employee_id}', [CalendereventController::class,'employeeEvents'])->name('employee.events');
    Route::post('/event-notification', [CalendereventController::class,'employeeEventNotification'])->name('employee.event.notification');
    });

Route::group(['prefix'=>'customer'],function(){
    Route::get('dashboard/{customer_id}', [DashboardController::class,'customerDashboard'])->name('customer.dashboard');
    Route::get('total-projects/{customer_id}', [ProjectController::class,'customerTotalProjects'])->name('customer.total.projects');
    Route::get('completed-projects/{customer_id}', [ProjectController::class,'customerCompletedProjects'])->name('customer.completed.projects');
    Route::get('ongoing-projects/{customer_id}', [ProjectController::class,'customerOngoingProjects'])->name('customer.ongoing.projects');
    Route::get('events/{customer_id}', [CalendereventController::class,'customerEvents'])->name('customer.events');
    Route::post('/event-notification', [CalendereventController::class,'customerEventNotification'])->name('customer.event.notification');

    //customer click Ok on pop up to active a project
    Route::put('accept-project/{project_id}', [ProjectController::class, 'acceptProject']);
    Route::put('reject-project/{project_id}', [ProjectController::class, 'rejectProject']);
    });


Route::get('/all-employee', [UsersController::class,'getallemployee'])->name('allemployee');
Route::get('/all-customer', [UsersController::class,'getallcustomer'])->name('allcustomer');
Route::get('/all-manager', [UsersController::class,'getallmanagers'])->name('allmanagers');
Route::post('/update-task-status', [TaskController::class,'updateTaskStatus'])->name('update.task.status');
Route::post('/pin-project', [ProjectController::class,'pinProject'])->name('pin.project');
Route::get('/get-user-pin-project/{user_id}', [ProjectController::class,'getUserPinProject'])->name('get.user.pin.project');
Route::get('/employee-for-project/{project_id}', [ProjectController::class,'employeeForProject'])->name('demployee.for.project');
Route::post('/employee-tasks', [TaskController::class,'getEmployeeTasks'])->name('get.employee.tasks');


// chat section
