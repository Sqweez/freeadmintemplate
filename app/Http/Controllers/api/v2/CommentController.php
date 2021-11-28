<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCommentResource;
use App\v2\Models\ProductComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function createComment(Request $request) {
        $comment = ProductComment::create($request->all());
        return self::parseComments(ProductComment::with(['user', 'client'])->whereProductId($comment->product_id)->get());
    }

    public function delete($id) {
        ProductComment::destroy($id);
    }

    public static function parseComments($comments): \Illuminate\Support\Collection {
        $allComments = collect(ProductCommentResource::collection($comments));
        $mainComments = $allComments->filter(function ($i) { return $i['parent_id'] === null; });
        return $mainComments->map(function ($comment) use ($allComments) {
            $parent_ids = [$comment['id']];
            $hasChildren = true;
            $childComments = [];
            while ($hasChildren) {
                $children = collect($allComments)->filter(function ($item) use ($parent_ids) {
                    return in_array($item['parent_id'], $parent_ids);
                })->values();
                if ($children->count() > 0) {
                    foreach ($children->toArray() as $item) {
                        array_push($childComments, $item);
                    }
                    $parent_ids = $children->pluck('id')->toArray();
                } else {
                    $hasChildren = false;
                }
            }

            $comment['children'] = $childComments;
            return $comment;
        })->values();
    }
}
