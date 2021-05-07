<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationResource;
use App\Http\Resources\TaskResource;
use App\v2\Models\Education;
use App\v2\Models\EducationAttachment;
use App\v2\Models\Task;
use App\v2\Models\TaskAttachment;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index() {
        return EducationResource::collection(
            Education::with(['author:name,id', 'attachments'])
                ->get()
        );
    }

    public function store(Request $request) {
        $attachments = $request->get('attachments');
        $education = $request->except('attachments');
        $_education = Education::create(array_merge($education, ['author_id' => $request->header('user_id')]));
        foreach ($attachments as $attachment) {
            EducationAttachment::create([
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'education_id' => $_education['id']
            ]);
        }
        return new EducationResource(Education::find($_education['id']));
    }

    public function destroy($id) {
        EducationAttachment::whereEducationId($id)->delete();
        Education::find($id)->delete();
    }

    public function update(Request $request, $id) {
        EducationAttachment::whereEducationId($id)->delete();
        $education = Education::find($id);
        $education->update($request->except('attachments'));
        $attachments = $request->get('attachments');
        foreach ($attachments as $attachment) {
            $_attachment = EducationAttachment::create([
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'education_id' => $id,
            ]);
        }

        return new EducationResource(Education::find($id));
    }
}
