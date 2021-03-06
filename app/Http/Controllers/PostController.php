<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
use App\Post;
use App\Tag;
use Session;
use Purifier;

class PostController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$posts = Post::orderBy('id', 'desc')->paginate(10);
		return view('posts.index')->withPosts($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		// Get all the categories.
		$categories = Category::all();
		$tags = Tag::all();
		
        return view('posts.create')->withCategories($categories)->withTags($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate the data
		$this->validate($request, array(
			'title' => 'required|max:255',
			'slug' => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
			'category_id' => 'required|integer',
			'body' => 'required'
		));
		
		// store in the database
		$post = new Post;
		
		$post->title = $request->title;
		$post->slug = $request->slug;
		$post->category_id = $request->category_id;
		$post->body = Purifier::clean($request->body);
		
		$post->save();
		
		$post->tags()->sync($request->tags, false);
		
		Session::flash('success', 'The blog post was successfully saved!');
		
		return redirect()->route('posts.show', $post->id);
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$post = Post::find($id);
        return view('posts.show')->withPost($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // find the post in the database and save it as a variable
		$post = Post::find($id);
		
		// Get all the categories.
		$categories = Category::all();
		$cats = [];
		foreach($categories as $category){
			$cats[$category->id] = $category->name;
		}
		
		// Get all the tags.
		$tags = Tag::all();
		$tags2 = [];
		foreach($tags as $tag){
			$tags2[$tag->id] = $tag->name;
		}
		
		// return the view and pass in the above variable
		return view('posts.edit')->withPost($post)->withCategories($cats)->withTags($tags2);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the data
		$post = Post::find($id);
		if($post->slug === $request->slug){
			$this->validate($request, array(
				'title' => 'required|max:255',
				'category_id' => 'required|integer',
				'body' => 'required'
			));
		}else{
			$this->validate($request, array(
				'title' => 'required|max:255',
				'slug' => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
				'category_id' => 'required|integer',
				'body' => 'required'
			));
		}
		
		// Save the data to the database
		
		$post->title = $request->title;
		$post->slug = $request->slug;
		$post->category_id = $request->category_id;
		$post->body = Purifier::clean($request->body);
		
		$post->save();
		
		if(isset($request->tags)){
			$post->tags()->sync($request->tags);
		}else{
			$post->tags()->sync([]);
		}
		
		// Set flash data with success message
		Session::flash('success', 'This post was successfully saved!');
		
		// Redirect with flash data to posts.show
		return redirect()->route('posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
		$post->tags()->detach();
		$post->delete();
		Session::flash('success', 'The post was successfully deleted.');
		return redirect()->route('posts.index');
    }
}
