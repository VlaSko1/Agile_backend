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
        $user_id = $this->getUserIdChecked();
        
        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->orderBy('updated_at', 'desc')
                                ->limit(20)
                                ->get();

        $arrBlog = $this->getArrBlog($blog, $user_id);

        return response()->json($arrBlog);
    }

    public function showPublicCategory($category_id)
    {
        $this->chechLogin();
        $user_id = $this->getUserIdChecked();

        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.public', '1')
                                ->where('blogs.category_id', $category_id)
                                ->orderBy('updated_at', 'desc')
                                ->limit(20)
                                ->get();
        
        $arrBlog = $this->getArrBlog($blog, $user_id);

        return response()->json($arrBlog);
    }

    public function showPublicBlog($id) 
    {
        $this->chechLogin();

        $user_id = $this->getUserIdChecked();
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
        // TODO сделай выборку комментариев по id статьи с именем пользователя и датой изменения комментария, посмотри как у Оли на сайте и добавь их в статью
        $comments = \DB::table('comments')
                            ->join('users', 'comments.user_id', '=', 'users.id')
                            ->select('comments.id', 'comments.comment', 'users.name', 'comments.created_at', 'comments.updated_at')
                            ->where('comments.blog_id', '=', $blog->id)
                            ->orderBy('comments.updated_at', 'desc')
                            ->get();

        $commentsArr = [];
        if (sizeof($comments) !== 0) {
            foreach($comments as $comment) {
                $this->modifiedDateTimeString($comment);
                $commentsArr[] = $comment;
            }
            $blog->comments = $commentsArr;
        } else {
            $blog->comments = [];
        }

        $this->modifiedDateTimeString($blog);
        
        return response()->json($blog);
    }

    public function chechLogin()
    {
        abort_if(! auth()->user(), 401, "You are not logged in");
    }

    /**
     * Return user id for this Blog or Null.
     *
     * 
     * @return id or null;
     */
    public function getUserIdChecked()
    {
        $user = auth()->user();
        
        return $user_id = $user->id;
    }


    /**
     * Returns $arrBlog 
     *
     * @param  $blog - object from table 'likes'
     * @param  $user_id - id user loggeg
     * @return $arrBlog - array objectes with 'likes' and 'userLike'
     */
    public function getArrBlog($blog, $user_id) {

        // Выбираем id статей для сокращение запроса к таблице likes
        $blogIdArr = [];
        foreach($blog as $key=>$value) {
            array_push($blogIdArr, $value->id);
        }
        sort($blogIdArr, SORT_NUMERIC);

        $likes = \DB::table('likes')
             ->select(\DB::raw('count(*) as count, blog_id'))
             ->where('isLike', '=', 1)
             ->whereIn('blog_id', $blogIdArr)
             ->groupBy('blog_id')
             ->get();
        
        $userLike = \DB::table('likes')
             ->select('blog_id')
             ->where('isLike', '=', 1)
             ->where('user_id', $user_id)
             ->whereIn('blog_id', $blogIdArr)
             ->get();


        $arrBlog = [];
        foreach ($blog as $item) {
            $this->modifiedDateTimeString($item);
            $item->likes = 0;
            $item->userLike = false;
            for ($i = 0; $i < count($likes); $i++) {
                if ($item->id === $likes[$i]->blog_id) {
                    $item->likes = $likes[$i]->count;
                    break;
                }
            }
            for ($j = 0; $j < count($userLike); $j++) {
                if ($item->id === $userLike[$j]->blog_id) {
                    $item->userLike = true;
                    break;
                }
            }
            $arrBlog[] = $item;
        }

        return $arrBlog;
    }


      /**
     * Modify object->created_at and object->updated_at (in format string): +3 hour
     * 
     * @param \App\Models\Blog  $blog
     * @return \App\Models\Blog  $blog - modified
     */

    public function modifiedDateTimeString($item) {
        $created_at_date = new \DateTime($item->created_at);
        $created_at_date->modify('+3 hour');
        $created_at = $created_at_date->format('Y-m-d H:i:s');

        $updated_at_date = new \DateTime($item->updated_at);
        $updated_at_date->modify('+3 hour');
        $updated_at = $updated_at_date->format('Y-m-d H:i:s');

        $item->created_at = $created_at;
        $item->updated_at = $updated_at;
    }


}
