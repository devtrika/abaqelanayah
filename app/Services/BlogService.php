<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogReaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class BlogService
{
    public function __construct(protected Blog $model)
    {

    }

    public function index($categoryId = null, $sort = 'desc')
    {
        $query = $this->model->where('is_active', true)
            ->with(['comments' => function($query) {
                $query->where('is_approved', true)->with('user');
            }]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $sortKey = strtolower($sort);
        if ($sortKey === 'old_to_new') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc'); // default and for 'new_to_old'
        }

        return $query->get();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a comment for a blog
     */
    public function storeComment(array $data)
    {


            $data['user_id'] = Auth::id();
            $comment = BlogComment::create($data);
            return $comment;

    }

    /**
     * Store or update a reaction for a blog
     */
    public function storeReaction(array $data)
    {
        try {
            DB::beginTransaction();

            $data['user_id'] = Auth::id();

            // Check if user already reacted to this blog
            $existingReaction = BlogReaction::where('user_id', $data['user_id'])
                ->where('blog_id', $data['blog_id'])
                ->first();

            if ($existingReaction) {
                if ($existingReaction->reaction === $data['reaction']) {
                    // Same reaction - remove it (toggle off)
                    $existingReaction->delete();
                    $reaction = null;
                } else {
                    // Different reaction - update it
                    $existingReaction->update(['reaction' => $data['reaction']]);
                    $reaction = $existingReaction;
                }
            } else {
                // New reaction
                $reaction = BlogReaction::create($data);
            }

            DB::commit();
            return $reaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}