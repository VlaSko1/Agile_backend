<?php

namespace App\Http\Controllers;


use App\Models\Blog;
use App\Models\User;
use App\Models\Blog_category;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Requests\Blog\AddBlogRequest;
use App\Http\Controller\AuthController;


class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = getUserId();
        // Проверка аунтентификации
        if (is_null($user_id)) {
            return response()->json(['message' =>"You are not logged in"], 401);
        }
        
        $blog = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->where('blogs.user_id', $user_id)
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->orderBy('updated_at', 'desc')
                                ->get();
        
        
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

         
        // Преобразование объекта с объектами в массив объектов
        $arrBlog = [];
        foreach ($blog as $item) {
            
            modifiedDateTimeString($item);
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

        return response()->json($arrBlog);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddBlogRequest $request)
    {
        $title = $request->input('title');
        $text = $request->input('text');
        $public = $request->input('public');
        $category_id = $request->input('category_id');
        $user_id = getUserId();
        $blog_img = $request->input('blog_img');

        if (is_null($user_id)) {
            return response()->json(['message' => 'Please sing up'], 401);
        }
        $blog = new Blog();
        $blog->title = $title;
        $blog->text = $text; 
        $blog->blog_img = $blog_img;     
        $blog->category_id = $category_id;       
        if (is_null($public)) {
            $public = '0';
        }
        $blog->public = $public; 
        $blog->user_id = $user_id; 
        
        $blog->save();

        $blog_join = \DB::table('blogs')
                                ->join('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                                ->select('blogs.*', 'blog_categories.category_name')
                                ->where('blogs.id', $blog->id)
                                ->get()->first();
        
        modifiedDateTimeString($blog_join);
        return response()->json((object) $blog_join);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        $user_id = getUserId();
        if ($user_id == $blog->user_id) {
            \DB::table('blogs')
                    ->where('id', $blog->id)
                    ->increment('views');
            $category_name_obj = Blog_category::select('category_name')->where('id', $blog->category_id)->first();
            $blog->category_name = $category_name_obj->category_name;
            $blog->views = ++$blog->views;

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

            modifiedDateTime($blog);

            return response()->json($blog);
        }
        
        return response()->json(['message' => "You don't have such an article"], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(AddBlogRequest $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        $blogId = $blog->id;
        $blogIdUser = $blog->user_id;
        $userId = auth()->user();
        if ($blogIdUser !== $userId->id) {
            return response()->json(['message' => "You don't have such an article"], 404);
        }
        if ($blog->delete()) {
            
            return response()->json(["message" => "Article with id {$blogId} deleted"]);
        }
        
    }

}
