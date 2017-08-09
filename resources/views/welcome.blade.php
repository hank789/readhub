@extends('layouts.app')


@section('content')
	<router-view></router-view>
@endsection


@section('head')
	<title>Readhub</title>
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
		    "@type": "Organization",
		    "url": "https://www.inwehub.com",
		    "name": "Inwehub",
		    "logo": {
	            "@type": "ImageObject",
	            "url": "https://read.inwehub.com/imgs/voten-circle.png",
	            "width": "512",
	            "height": "512"
	        },
		    "sameAs": [
		        "https://zhuanlan.zhihu.com/inwehub"
		    ]
		}
	</script>
@endsection
