<?php

namespace App\Http\Controllers\api\v3;

use App\Category;
use App\Http\Controllers\api\BaseApiController;
use App\Jobs\Revision\GenerateRevisionExcelFile;
use App\Jobs\Revision\ProcessRevisionFile;
use App\v2\Models\Revision;
use App\v2\Models\RevisionFile;
use Illuminate\Http\Request;

class RevisionController extends BaseApiController
{
    public function create(Request $request)
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
        return $revision;
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
}
