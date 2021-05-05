<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\v2\Models\Task;
use App\v2\Models\TaskAttachment;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() {
        return TaskResource::collection(
            Task::with(['author:name,id', 'store:id,name', 'attachments'])
                ->get()
        );
    }

    public function getCurrentTasks(Request $request) {
        $store_id = $request->header('store_id');
        return TaskResource::collection(
            Task::with(['author:name,id', 'store:id,name', 'attachments'])
                ->whereStoreId($store_id)
                ->whereDate('date_start', '>=', now())
                ->whereDate('date_finish', '<=', now())
                ->get()
        );
    }

    public function store(Request $request) {
        $stores = $request->get('store_ids');
        $attachments = $request->get('attachments');
        $task = $request->except(['store_ids', 'attachments']);
        $tasks = [];
        foreach ($stores as $store) {
            $_task = Task::create(array_merge($task, ['store_id' => $store, 'author_id' => $request->header('user_id')]));
            foreach ($attachments as $attachment) {
                $_attachment = TaskAttachment::create([
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'task_id' => $_task['id']
                ]);
            }
            $tasks[] = $_task['id'];
        }
        return TaskResource::collection(Task::with(['author:name,id', 'store:id,name', 'attachments'])->whereIn('id', $tasks)->get());
    }

    public function destroy($id) {
        TaskAttachment::whereTaskId($id)->delete();
        Task::find($id)->delete();
    }

    public function update(Request $request, $id) {
        TaskAttachment::whereTaskId($id)->delete();
        $task = Task::find($id);
        $task->update($request->except('attachments'));
        $attachments = $request->get('attachments');
        foreach ($attachments as $attachment) {
            $_attachment = TaskAttachment::create([
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'task_id' => $id,
            ]);
        }

        return new TaskResource(Task::find($id));
    }

    public function editTaskStatus($id) {
        $task = Task::find($id);
        $task->is_completed = !$task->is_completed;
        $task->save();
    }
}
