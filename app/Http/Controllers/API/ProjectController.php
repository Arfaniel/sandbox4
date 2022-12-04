<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;


class ProjectController
{
    /**
     * @OA\Get(
     * path="/api/projects?filter[user][continent_id]=&filter[user][email]=&filter[labels]=",
     * summary="Gets projects by specified filters",
     * description="Multiple filters can be provided to filter the rezult list of projects",
     * operationId="list",
     * tags={"project"},
     * security={ {"bearer": {} }},
     * @OA\Parameter(
     *    name="filter[user][continent_id]",
     *    description="Id of a continent",
     *    in = "path",
     *    required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * @OA\Parameter(
     *    name="filter[user][email]",
     *    description="email of a user",
     *    in = "path",
     *    required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * @OA\Parameter(
     *    name="filter[labels]",
     *    description="label id that is linked to a project",
     *    in = "path",
     *    required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *        )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/api/projects/create",
     * summary="Adds a project",
     * description="Add a project by a user",
     * operationId="create",
     * tags={"project"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Provide project name",
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="Project 1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, you need to give a name of a project")
     *        )
     *     )
     * )
     */
    public function create(Request $request): string
    {
        $project = Project::create($request->all());
        $project->users()->attach($request->user());
        return 'Created successfully';
    }

    /**
     * @OA\Post(
     * path="/api/projects/linkUser",
     * summary="Link a project",
     * description="Links a project to specified users",
     * operationId="link",
     * tags={"project"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Provide project id, and user id-s that need to be linked",
     *    @OA\JsonContent(
     *       required={"name", "users"},
     *       @OA\Property(property="projectId", type="integer", example=1),
     *       @OA\Property(
     *          property="users",
     *          type="array",
     *          @OA\Items(
     *             type="integer",
     *             example="1, 2",
     *          )
     *       )
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Users where linked to poject")
     *        )
     *     )
     * )
     */
    public function linkUser(Request $request)
    {

        $project = Project::find($request->projectId);
        if (!empty($project)) {
            $project->users()->attach($request['users']);
            return "Users where linked to poject";
        }
        return "Operation failed";
    }
    /**
     * @OA\Delete(
     * path="/api/projects/delete",
     * summary="Deletes a project",
     * description="Deletes a specified project",
     * operationId="delete",
     * tags={"project"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Provide project id, that need to be deleted",
     *    @OA\JsonContent(
     *       required={"projectId"},
     *       @OA\Property(property="projectId", type="integer", example=1),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Deleted successfylly")
     *        )
     *     )
     * )
     */
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
