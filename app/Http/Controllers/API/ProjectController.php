<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController
{
    public function index(Request $request)
    {
        $project = Project::query();
        $project->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->join('users', 'project_user.user_id', '=', 'users.id')
            ->where('users.id', '=', $request->user()->id)
            ->select('projects.name', 'projects.id', 'users.name as user_name', 'users.id as user_id', 'users.country_id', 'users.email');
        $userEmail = $request->filter['user']['email'];
        if (!empty($userEmail)) {
            $project->where('users.email', '=', $userEmail);
        }
        $continentId = $request->filter['user']['continent_id'];
        if (!empty($continentId)) {
            $project->join('countries', 'users.country_id', '=', 'countries.id')
                ->join('continents', 'countries.id', '=', 'continents.country_id')
                ->where('continents.country_id', '=', $continentId);
        }
        $projects = $project->get();
        return ProjectResource::collection($projects);
    }

    public function create(Request $request)
    {
        $project = Project::create($request->all());
        $project->users()->attach($request->user());
        return 'Created successfully';
    }

    public function linkUser(Request $request)
    {

        $project = Project::find($request->projectId);
        if (!empty($project)) {
            $project->users()->attach($request['users']);
            return "Users where linked to poject";
        }
        return "Operation failed";
    }

    public function delete(Request $request)
    {
        $project = Project::query();
        $project->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->where('projects.id', '=', $request->projectId)
            ->where('project_user.user_id', '=', $request->user()->id)
            ->select('project_user.is_author');
        $projects = $project->get();
        if ($projects[0]->is_author == 1) {
            $projectToDelete = Project::find($request->projectId);
            $projectToDelete->delete();
            return 'Deleted successfylly';
        }
        return 'Fail to delete';
    }
}
