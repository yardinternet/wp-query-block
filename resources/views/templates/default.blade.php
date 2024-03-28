@foreach ($posts as $post)
	<p>{{ $post->post_title ?? 'hello' }}</p>
@endforeach
