<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Models\Blog_category;
use App\Models\Like;
use App\Http\Controller\AuthController;
use App\Http\Requests\Find\FindTitleRequest;

class FindController extends Controller
{
    /**
     * Return articles containing the line from the $text parameter.
     *
     * @param  (string) $text
     * @return articles containing the line from the $text parameter.
     */
    public function findTitle(FindTitleRequest $request)
    {   
        $user_id = getUserId();
        // Проверка аунтентификации
        if (is_null($user_id)) {
            return response()->json(['message' =>"You are not logged in"], 401);
        }
        $text = $request->input('titleFind');
        $blogs = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->where('blogs.title', 'like', '%' . $text . '%')
                                ->orderBy('updated_at', 'desc')
                                ->limit(10)
                                ->get();

        $arrBlog = getArrBlog($blogs, $user_id);

        return response()->json($arrBlog);
    }

   
}
