@extends('layouts.guest')


@section('content')
	<router-view></router-view>
@endsection


@section('head')
	<title>阅读发现</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Readhub - Social Bookmarking For The 21st Century" />
	<meta property="og:url" content="{{ config('app.url') }}" />
	<meta property="og:site_name" content="Readhub" />

	<meta name="description" content="A Modern, real-time, open-source, beautiful, deadly simple and warm community."/>
	<meta property="og:description" content="A Modern, real-time, open-source, beautiful, deadly simple and warm community." />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/voten-circle.png">

	<script type="application/ld+json">
	{
	    "@context": "http://schema.org",
	    "@type": "WebSite",
	    "url": "https://www.inwehub.com",
	    "name": "Readhub",
	    "logo": "https://voten.co/imgs/voten-circle.png",
	    "sameAs": [
	        "https://www.facebook.com/voten.co/",
	        "https://twitter.com/voten_co"
	    ]
	}
	</script>
@endsection



@section('script')
	<script>
		var preload = {
			submissions: {!! $submissions->toJson() !!}
		};
	</script>
@endsection
