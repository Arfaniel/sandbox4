<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\LabelResource;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController
{
    public function index(Request $request)
    {
        $label = Label::query();
        $label->join('label_project', 'label_project.label_id', '=', 'labels.id')
            ->join('project_user', 'project_user.project_id', '=', 'label_project.project_id')
            ->where('labels.user_id', '=', $request->user()->id)
            ->where('project_user.user_id', '=', $request->user()->id);
        $userEmail = $request->filter['user']['email'];
        if (!empty($userEmail)) {
            $label->join('users', 'users.id', '=', 'labels.user_id')
                ->where('users.email', '=', $userEmail);
        }
        $projectId = $request->filter['projects'];
        if (!empty($projectId)) {
            $label->join('label_project', 'label_project.label_id', '=', 'labels.id')
                ->where('label_project.project_id', '=', $projectId);
        }

        $labels = $label->get();
        return LabelResource::collection($labels);
    }

    public function create(Request $request)
    {
        Label::create([
            'name' => $request->name,
            'user_id' => $request->user()->id
        ]);
        return 'Created successfully';
    }

    public function linkProject(Request $request)
    {
        $label = Label::find($request->labelId);
        if (!empty($label)) {
            $label->projects()->attach($request['projects']);
            return 'Projects were linked to label';
        }
        return 'Operation failed';
    }

    public function delete(Request $request)
    {
        $label = Label::find($request->labelId);
        if ($label->user_id == $request->user()->id) {
            $label->delete();
            return 'Deleted successfully';
        }
        return 'Fail to delete';
    }
}
