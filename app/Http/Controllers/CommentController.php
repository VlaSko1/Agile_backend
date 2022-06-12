<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Http\Controller\AuthController;
use App\Http\Requests\Comment\CommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(CommentRequest $request)
    {
        $user_id = getUserId();
         
        $comment = $request->input('comment');
        $blog_id = $request->input('blog_id');
        $blog = Blog::select('public')->where('id', $blog_id)->first();
 
        if (empty($blog)) {
            return response()->json(['message' => 'Article not found'], 404);
        } else if ($blog->public === 0) {
            return response()->json(['message' => 'You can only comment on public articles'], 404);
        }
        
        $feedback = new Comment();
        $feedback->comment = $comment;
        $feedback->user_id = $user_id;
        $feedback->blog_id = $blog_id;
        $feedback->save();

        $user_name = User::select('name')->where('id', $user_id)->first();

        $feedback->user_name = $user_name->name;
        
        return response()->json($feedback);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $commentId = $comment->id;
        $commentIdUser = $comment->user_id;
        $userId = auth()->user();
        if ($commentIdUser !== $userId->id) {
            return response()->json(['message' => "You don't have such an comment"], 404);
        }
        if ($comment->delete()) {
            
            return response()->json(["message" => "Comment with id {$commentId} deleted"]);
        }
    }
}
