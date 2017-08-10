@extends('layouts.guest')


@section('head')
	<title>{{ $submission->title }} - Readhub</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ $submission->title }} - Readhub" />
	<meta property="og:url" content="{{ config('app.url') }}/c/{{ $submission->category_name }}/{{ $submission->slug }}" />
	<meta property="og:site_name" content="Readhub" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@voten_co" />
	<meta name="twitter:title" content="{{ $submission->title }} - Readhub" />

	@if ($submission->type == "text")
		<meta name="description" content="{{ $submission->data['text'] }}"/>
		<meta property="og:description" content="{{ $submission->data['text'] }}" />
		<meta name="twitter:description" content="{{ $submission->data['text'] }}" />
		<meta property="og:image" content="/imgs/voten-circle.png">
		<meta name="twitter:image" content="/imgs/voten-circle.png" />
	@else
		<meta property="og:image" content="{{ $submission->data['thumbnail'] ?? $submission->data['path'] ?? '/imgs/voten-circle.png' }}" />
		<meta name="twitter:image" content="{{ $submission->data['thumbnail'] ?? $submission->data['path'] ?? '/imgs/voten-circle.png' }}" />
	@endif
@stop


@section('content')
	<router-view></router-view>
@endsection


@section('script')
	<script>
		var preload = {
			submission: {!! $submission !!}
		};
	</script>
@endsection