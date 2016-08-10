@extends('main')

@section('title', "| $tag->name Tag")

@section('content')

<div class="row">
	<div class="col-md-8">
		<h1>{{ $tag->name }} Tag <small>{{ $tag->posts->count() }} Posts</small></h1>
	</div>
	<div class="col-md-2 col-md-offset-2">
		<a href="#" class="btn btn-primary pull-right btn-block btn-h1-spacing">Edit</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Tags</th>
						<th></th>
					</tr>
					<tbody>
						@foreach($tag->posts as $post)
						<tr>
							<th>{{ $post->id }}</th>
							<td>{{ $post->title }}</td>
							<td>@foreach ($post->tags as $ptag)
								<span class="label label-default">{{ $ptag->name }}</span>
								@endforeach
								</td>
							<td><a href="{{ route('posts.show', $post->id) }}" class="btn btn-default btn-xs">View</a></td>
						</tr>
						@endforeach
					</tbody>
				</thead>
			</table>
		</div>
	</div>
</div>

@stop
