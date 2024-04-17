<?php

namespace App\Http\Controllers\api\v3;

use App\Category;
use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Revision\RevisionResource;
use App\Http\Resources\Revision\RevisionTable;
use App\Http\Resources\Revision\SingleRevisionResource;
use App\Jobs\Revision\GenerateRevisionExcelFile;
use App\Jobs\Revision\ProcessRevisionFile;
use App\v2\Models\Revision;
use App\v2\Models\RevisionFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevisionController extends BaseApiController
{

    public function index(Request $request): JsonResponse
    {
        $user = $this->retrieveAnyUser();
        abort_if(!$user, 403);
        $revisions = Revision::query()
            ->with(['user:id,name', 'store:id,name'])
            ->withCount(['files', 'filesUploaded'])
            ->when(!$user->is_super_user, function ($query) use ($user) {
                return $query->where('store_id', $user->store_id);
            })
            ->orderByDesc('created_at')
            ->get();
        return $this->respondSuccess([
            'revisions' => RevisionResource::collection($revisions)
        ]);
    }

    public function show(Revision $revision)
    {
        $revision->load('files.category');
        $revision->load('store');
        $revision->load('user');
        return $this->respondSuccess([
            'revision' => new SingleRevisionResource($revision)
        ]);
    }

    public function create(Request $request): RevisionResource
    {
        $revision = new Revision();
        $revision->user_id = $request->get('user_id');
        $revision->store_id = $request->get('store_id');
        $revision->status = 'creating';
        $revision->save();
        $categories = Category::select(['id'])->orderBy('id')->get();
        $categories->map(function ($category) use ($revision) {
            return dispatch(new GenerateRevisionExcelFile($revision, $category->id));
        });
        return new RevisionResource($revision);
    }

    public function process(Revision $revision, Request $request, RevisionFile $revisionFile): string
    {
        $file = $request->file('file');
        $path = $file->storeAs("revisions/uploaded/{$revision->id}", $file->getClientOriginalName(), ['disk' => 'public']);
        $revisionFile->uploaded_file_path = $path;
        $revisionFile->uploaded_at = now();
        $revisionFile->save();
        dispatch(new ProcessRevisionFile($revisionFile));
        return $path;
    }

    public function getRevisionTable(Revision $revision, Request $request): JsonResponse
    {
        $fileId = $request->get('file_id', null);
        $revision
            ->load(['products' => function ($query) use ($fileId) {
                $query
                    ->when($fileId, function ($query) use ($fileId) {
                        return $query->where('revision_file_id', $fileId);
                    })
                    ->with([
                        'sku' => function ($query) {
                            return $query->with('attributes');
                        },
                        'product' => function ($query) {
                            return $query->with('attributes')->with('manufacturer');
                        }
                    ])
                    ->get();
            }]);

        return $this->respondSuccess([
            'table' => new RevisionTable($revision)
        ]);
    }
}
