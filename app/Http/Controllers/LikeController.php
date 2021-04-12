<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Like;
use App\Http\Controller\AuthController;

class LikeController extends Controller
{

    public function clickLike($idBlog)
    {
        $user_id = $this->getUserId();

        if (is_null($user_id)) {
            return response()->json(['message' => 'Please sing up'], 401);
        }
        
        $like = Like::get()->where('user_id', $user_id)->where('blog_id', $idBlog)->first();

        if (is_null($like)) {
            $like = new Like();
            $like->user_id = $user_id;
            $like->blog_id = $idBlog;
        } else {
            $like->isLike = ($like->isLike === 1) ? 0 : 1;
        }

        if ($like->save()) {
            return response()->json(['message' => "Like create or change"]);
        } else {
            return response()->json(['message' => "Like don't change or create"], 500);
        }

    }


     /**
     * Return user id for this Blog or Null.
     *
     * 
     * @return id or null;
     */
    public function getUserId()
    {
        $user = auth()->user();
        if (is_null($user)) {
            return null;
        }
        return $user_id = $user->id;
    }
}

