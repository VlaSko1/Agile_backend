<?php

namespace App\Http\Controllers;


use App\Models\Blog;
use App\Models\User;
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
        // Проверка аунтентификации
        if (empty(auth()->user())) {
            return response()->json(['message' =>"You are not logged in"], 401);
        }
        
        $user_id = $this->getUserId();
        $blog = Blog::get()->where('user_id', $user_id);
        
        // Преобразование объекта с объектами в массив
        $arrBlog = [];
        foreach ($blog as $item) {
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
        $user_id = $this->getUserId();
        //$blog_img = $this->getBlogImg('blog_img', $request);
        $blog_img = $request->input('blog_img');

        if (is_null($user_id)) {
            return response()->json(['message' => 'Please sing up'], 401);
        }
        $blog = new Blog();
        $blog->title = $title;
        $blog->text = $text; 
        $blog->blog_img = $blog_img;            
        if (is_null($public)) {
            $public = '0';
        }
        $blog->public = $public; 
        $blog->user_id = $user_id; 
        
        $blog->save();
        
        return response()->json($blog);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        $user_id = $this->getUserId();
        if ($user_id == $blog->user_id) {
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
        $title = $request->input('title');
        $text = $request->input('text');
        $public = $request->input('public');
        $user_id = $this->getUserId();
        $blog->title = $title;
        $blog->text = $text; 
        if (!is_null($public)) {
            $blog->public = $public; 
        }
        
        // Работа с изображением
        //$blog_img = $this->getBlogImg('blog_img', $request);
        $blog_img = $request->input('blog_img');
        if(!is_null($blog_img)) {
            $blog->blog_img = $blog_img;
        }
        
        $blog->save();
        return response()->json($blog);
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


    /**
     * Return img to base64 for insert argument src and table
     * 
     * 
     * @return null or string
     * 
     */
    public function getBlogImg($string, $request)
    {
        if ($request->hasFile($string)) {
            $file = $request->file($string);

            //TODO кодируем файл в base64
            $fp = fopen($file, 'rb', 0);
                
            $gambar = fread($fp, filesize($file));
            $type = mime_content_type($fp);
            fclose($fp);
            $based64 = base64_encode($gambar);
            $blog_img = 'data:' . $type . ';base64,' . $based64;
            return $blog_img;
            
        } else {
            return null;
        }
    }

}