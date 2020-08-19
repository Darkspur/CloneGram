@extends('layouts.app')

@section('content')
<div class="container">
    @foreach($posts as $post)
    <div class="row col-6 offset-3">
      <div class="d-flex align-items-center pb-3">
        <div class="pr-3">
          <img src="{{ $post->user->profile->profileImage() }}" class="rounded-circle w-100" style="max-width: 40px;">
        </div>
        <div>
          <div class="font-weight-bold"><a href="/profile/{{ $post->user->id }}"><span class="text-dark">{{ $post->user->username }}</span></a></div>
        </div>
      </div>
    </div> 
    <div class="row">
        <div class="col-6 offset-3">
            <img src="/storage/{{ $post->image }}" class="w-100">
        </div>
    </div>
    <div class="row col-6 offset-3 pt-2 pb-4">
      <p>
        <span class="font-weight-bold"><a href="/profile/{{ $post->user->id }}"><span class="text-dark">{{ $post->user->username }}</span></a></span>
        <span>{{ $post->caption }}</span>
      </p>
    </div>
    </div>
    @endforeach
    <div class="row">
      <div class="col-12  d-flex justify-content-center">
        {{ $posts->links() }}
      </div>
    </div>
@endsection 
