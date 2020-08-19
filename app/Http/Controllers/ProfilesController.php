<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;


class ProfilesController extends Controller
{
    public function index(\App\User $user)
    {
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;
        //This is just another method to create the 404 not found error rather than dramatic errors. (Alternate is \App\User)
        //$user = User::findOrFail($user); 

        $postCount = Cache::remember(
            'count.posts'.$user->id,
            now()->addSeconds(30),
            function() use ($user) {
                return $user->posts->count();
            }
        );

        $followerCount = Cache::remember(
            'count.followers'.$user->id,
            now()->addSeconds(30),
            function() use ($user) {
                return $user->profile->followers->count();
            }
        );
        
        $followingCount = Cache::remember(
            'count.following'.$user->id,
            now()->addSeconds(30),
            function() use ($user) {
                return $user->following->count();
            }
        );

        return view('profiles.index',compact('user','follows','postCount','followerCount','followingCount'));
        
        
    }

    public function edit(\App\User $user)
    {
        //authorizes the policy to not let anyone edit the profile without loggin in the specific account
        $this->authorize('update', $user->profile);

        return view('profiles.edit',compact('user'));
    }

    public function update(\App\User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        if (request('image'))
        {
            //uploads the image in an profiles directory in storage. Use php artisan storage:link to make it accessible to the public directory
            $imagePath = request('image')->store('profiles','public'); 

            //crops the image into the given pixel size
            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000,1000); 
            $image->save();

            $imageArr = ['image' => $imagePath];
        }

        auth()->user()->profile->update(array_merge(
        $data,
        $imageArr ?? []
        ));

        return redirect('/profile/'.auth()->user()->id);
    }
}
