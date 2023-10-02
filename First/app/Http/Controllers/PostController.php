<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function update (Post $post, Request $request){
        $incomingData = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingData['title'] = strip_tags($incomingData['title']);
        $incomingData['body'] = strip_tags($incomingData['body']);

        $post->update($incomingData);

        return back()->with('success','Post Updated ');
    }

    public function EditForm(Post $post){
        return view('edit-post',['post'=> $post]);
    }

    public function delete(Post $post){
        $post->delete();
        return redirect('/profile/'. auth()->user()->username)->with('success','the post has deleted ');
    }

    public function ShowCreateForm(){
        return view('create-post');
    }

    public function AddNewPost (Request $request){
        $incomingData = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $incomingData['title'] = strip_tags($incomingData['title']);
        $incomingData['body'] = strip_tags($incomingData['body']);
        $incomingData['user_id'] = auth()->id();

        $newpost= Post::create($incomingData); 

        return redirect("/post/{$newpost->id}")->with('success','newpost successfully created ');
    }

    public function ShowSinglePost(Post $post){
        return view('single-post',['post'=> $post]);
    }
}
