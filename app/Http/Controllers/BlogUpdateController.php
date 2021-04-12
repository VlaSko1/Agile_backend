<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Http\Requests\Blog\AddBlogRequest;
use App\Http\Controller\AuthController;

class BlogUpdateController extends Controller
{
    public function updateBlog(addBlogRequest $request, $id)
    {
        $blog = Blog::get()->where('id', $id)->first();

        $title = $request->input('title');
        $text = $request->input('text');
        $public = $request->input('public');
        $blog_img = $request->input('blog_img');
        $category_id = $request->input('category_id');
        $user_id = $this->getUserId();

        if ($user_id !== $blog->user_id) {
            return response()->json(['message' => "You don't have an article with an id = {$id}"], 401);
        }
       
        $blog->title = $title;
        $blog->text = $text; 
        $blog->category_id = $category_id;  
        if (!is_null($public)) {
            $blog->public = $public; 
        }
        
        if(!is_null($blog_img)) {
            $blog->blog_img = $blog_img;
        }
        
        $blog->save();

        $blog_join = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.id', $blog->id)
                                ->get()->first();
        return response()->json($blog_join);
        
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
