<?php

namespace App\Http\Controllers;

use App\Models\TemplateTask;
use App\Http\Requests\StoreTemplateTaskRequest;
use App\Http\Requests\UpdateTemplateTaskRequest;

class TemplateTaskController extends Controller
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
     * @param  \App\Http\Requests\StoreTemplateTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTemplateTaskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TemplateTask  $templateTask
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateTask $templateTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemplateTask  $templateTask
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateTask $templateTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTemplateTaskRequest  $request
     * @param  \App\Models\TemplateTask  $templateTask
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTemplateTaskRequest $request, TemplateTask $templateTask)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TemplateTask  $templateTask
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplateTask $templateTask)
    {
        //
    }
}
