@extends('layouts.guest')


@section('head')
	<title>{{ '@' . $user->username }} - Readhub</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ '@' . $user->username }} - Readhub" />
	<meta property="og:url" content="{{ config('app.url') }}/{{ '@' . $user->name }}" />
	<meta property="og:site_name" content="Readhub" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@voten_co" />
	<meta name="twitter:title" content="{{ '@' . $user->username }} - Readhub" />

	@if ($user->bio)
		<meta name="description" content="{{ $user->bio }}"/>
		<meta property="og:description" content="{{ $user->bio }}" />
		<meta name="twitter:description" content="{{ $user->bio }}" />
	@endif

	<meta property="og:image" content="{{ $user->avatar }}" />
	<meta name="twitter:image" content="{{ $user->avatar }}" />
@stop


@section('content')
	<router-view></router-view>
@endsection


@section('script')
	<script>
		var preload = <?php echo json_encode([
            'user' => $user,
            'submissions' => $submissions
        ]); ?>
	</script>
@endsection