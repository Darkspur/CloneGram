<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    public function __construct()
    {
        //protects the /p/create page from being accessed without login
        $this->middleware('auth'); 
    }

    public function index()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');

        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->     paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts/create');
    }

    public function store()
    {
        //validates the data request. If want to pass another field in it without it being a required one follow the comment.
        $data = request()->validate([
            //'extraField' => '',
            'caption' => 'required',
            'image' => 'required|image',
        ]);

        //uploads the image in an uploads directory in storage. Use php artisan storage:link to make it accessible to the public directory
        $imagePath = request('image')->store('uploads','public'); 


        //crops the image into the given pixel size
        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200,1200); 
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);   

        return redirect('/profile/'.auth()->user()->id);
    }

    public function show( \App\Post $post)
        {
            return view('posts.show', compact('post'));
        }
}











