<?php

namespace App\Http\Controllers;

use App\Models\ProjectPicture;
use App\Http\Requests\StoreProjectPictureRequest;
use App\Http\Requests\UpdateProjectPictureRequest;

class ProjectPictureController extends Controller
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
     * @param  \App\Http\Requests\StoreProjectPictureRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectPictureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProjectPicture  $projectPicture
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectPicture $projectPicture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProjectPicture  $projectPicture
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectPicture $projectPicture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectPictureRequest  $request
     * @param  \App\Models\ProjectPicture  $projectPicture
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectPictureRequest $request, ProjectPicture $projectPicture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectPicture  $projectPicture
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectPicture $projectPicture)
    {
        //
    }
}
