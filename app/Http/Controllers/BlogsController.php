<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Http\Requests\Blog\AddBlogRequest;
use App\Http\Controller\AuthController;
use App\Models\Comment;

class BlogsController extends Controller
{
    public function show()
    {
        $this->chechLogin();
        $user_id = getUserIdChecked();
        
        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->orderBy('updated_at', 'desc')
                                ->limit(20)
                                ->get();

        $arrBlog = getArrBlog($blog, $user_id);

        return response()->json($arrBlog);
    }

    public function showPage($page)
    {
        $this->chechLogin();
        $user_id = getUserIdChecked();
        $limit = 20;
        $start = ($page - 1) * $limit;
        $allPublicPage = \DB::table('blogs')->where('public', 1)->count();
        $pages = ceil($allPublicPage / $limit); 

        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->orderBy('updated_at', 'desc')
                                ->offset($start)
                                ->limit($limit)
                                ->get();
        $dataArr = array(
            'pages' => $pages,
            'blogs' => getArrBlog($blog, $user_id),
        );
        
        $dataObj = (object)$dataArr;
        
        return response()->json($dataObj);
    }

    public function showPublicCategory($category_id)
    {
        $this->chechLogin();
        $user_id = getUserIdChecked();

        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->where('blogs.category_id', $category_id)
                                ->orderBy('updated_at', 'desc')
                                ->limit(20)
                                ->get();
        
        $arrBlog = getArrBlog($blog, $user_id);

        return response()->json($arrBlog);
    }

    public function showPublicBlog($id) 
    {
        $this->chechLogin();

        $user_id = getUserIdChecked();
        \DB::table('blogs')
                    ->where('id', $id)
                    ->increment('views');

        

        $blog = \DB::table('blogs')
                    ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                    ->select('blogs.*', 'blog_categories.category_name')
                    ->where('blogs.public', '1')
                    ->where('blogs.id', $id)
                    ->get()->first();

        // Обогащаем статью лайками и проверяем лайкнул ли пользователь свою запись
        $likes = \DB::table('likes')
             ->select(\DB::raw('count(*) as count'))
             ->where('isLike', '=', 1)
             ->where('blog_id', '=', $blog->id)
             ->get();

        $userLike = \DB::table('likes')
             ->select(\DB::raw('count(*) as count'))
             ->where('isLike', '=', 1)
             ->where('blog_id', '=', $blog->id)
             ->where('user_id', $user_id)
             ->get();
        
        $blog->likes = $likes[0]->count;
        $blog->userLike = (boolean) $userLike[0]->count;

        // Добавление комментариев
        
        $comments = \DB::table('comments')
                            ->join('users', 'comments.user_id', '=', 'users.id')
                            ->select('comments.id', 'comments.comment', 'users.name', 'comments.created_at', 'comments.updated_at', 'comments.user_id', 'comments.blog_id')
                            ->where('comments.blog_id', '=', $blog->id)
                            ->orderBy('comments.updated_at', 'desc')
                            ->get();

        $commentsArr = [];
        if (sizeof($comments) !== 0) {
            foreach($comments as $comment) {
                modifiedDateTimeString($comment);
                $commentsArr[] = $comment;
            }
            $blog->comments = $commentsArr;
        } else {
            $blog->comments = [];
        }

        modifiedDateTimeString($blog);
        
        return response()->json($blog);
    }

    public function chechLogin()
    {
        abort_if(! auth()->user(), 401, "You are not logged in");
    }

}
