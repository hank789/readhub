<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('title')
    @yield('head')

    <meta property="og:locale" content="en_US" />
    <meta property="og:site_name" content="Voten.co" />

    <link href="https://fonts.googleapis.com/css?family=Dosis:300,400,700" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>

    <link href="/icons/css/fontello.7.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- start Mixpanel -->
	<script type="text/javascript">
        (function(e,a){if(!a.__SV){var b=window;try{var c,l,i,j=b.location,g=j.hash;c=function(a,b){return(l=a.match(RegExp(b+"=([^&]*)")))?l[1]:null};g&&c(g,"state")&&(i=JSON.parse(decodeURIComponent(c(g,"state"))),"mpeditor"===i.action&&(b.sessionStorage.setItem("_mpcehash",g),history.replaceState(i.desiredHash||"",e.title,j.pathname+j.search)))}catch(m){}var k,h;window.mixpanel=a;a._i=[];a.init=function(b,c,f){function e(b,a){var c=a.split(".");2==c.length&&(b=b[c[0]],a=c[1]);b[a]=function(){b.push([a].concat(Array.prototype.slice.call(arguments,
            0)))}}var d=a;"undefined"!==typeof f?d=a[f]=[]:f="mixpanel";d.people=d.people||[];d.toString=function(b){var a="mixpanel";"mixpanel"!==f&&(a+="."+f);b||(a+=" (stub)");return a};d.people.toString=function(){return d.toString(1)+".people (stub)"};k="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config reset people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
            for(h=0;h<k.length;h++)e(d,k[h]);a._i.push([b,c,f])};a.__SV=1.2;b=e.createElement("script");b.type="text/javascript";b.async=!0;b.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";c=e.getElementsByTagName("script")[0];c.parentNode.insertBefore(b,c)}})(document,window.mixpanel||[]);
	</script>
	<!-- end Mixpanel -->

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'is_h5' => session('is_h5'),
            'echo_address' => config('broadcasting.connections.echo.app_address'),
            'env' => config('app.env')
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
</head>
<body>
@include('google-analytics')
	<div id="landing">


		<div class="header user-select">
			<div class="logo">
				<a href="/">
					<img src="/imgs/voten-logo.png" alt="Voten.co">
					Readhub
				</a>
				<small>BETA</small>
			</div>

			<div v-if="false" class="right-menu">
				<a href="https://medium.com/voten" target="_blank" class="item desktop-only">
					<i class="v-icon v-blog go-yellow"></i>
					Blog
				</a>
				<a href="mailto:info@voten.co" class="item desktop-only">
					<i class="v-icon v-letter go-green"></i>
					Contact
				</a>
				<a href="mailto:press@voten.co" class="item desktop-only">
					<i class="v-icon v-press go-red"></i>
					Press
				</a>
				<a href="/credits" class="item desktop-only">
					<i class="v-icon v-credits go-primary"></i>
					Credits
				</a>

				@if(Auth::check())
					<a href="/" class="v-button v-button--primary">
			            {{ Auth::user()->username }}
			        </a>
				@else
					<a href="/login" class="v-button v-button--primary">
			            Sign in
			        </a>
				@endif

			</div>

		</div>


		@yield('content')


		<footer v-if="false" class="user-select">
			<div class="flex1">
				<h3 class="go-primary">Readhub 	&#10084;</h3>
				<ul>
					<li><a href="/about">About</a></li>

					@if(Auth::check())
						<li><a href="/logout">Sign out</a></li>
					@else
						<li><a href="/register">Sign Up</a></li>
					@endif

					<li><a href="/tos">Terms of Service</a></li>
					<li><a href="/privacy-policy">Privacy Policy</a></li>
				</ul>
			</div>
			<div class="flex1">
				<h3 class="go-red">Get to know Readhub </h3>
				<ul>
					<li><a href="mailto:info@voten.co">Contact Us</a></li>
					<li><a href="mailto:press@voten.co">Press</a></li>
					<li><a href="/credits">Credits</a></li>
					<li><a href="/help">Help Center</a></li>
				</ul>
			</div>
			<div class="flex1">
				<h3 class="go-green">Follow Readhub</h3>
				<ul>
					<li><a href="https://medium.com/voten" target="_blank">Blog</a></li>
					<li><a href="https://github.com/voten-co/voten" target="_blank">Github</a></li>
					<li><a href="https://twitter.com/voten_co/" target="_blank">Twitter</a></li>
					<li><a href="https://facebook.com/voten.co/" target="_blank">Facebook</a></li>
				</ul>
			</div>
		</footer>
    </div>

	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('js/landing.js') }}"></script>
    @yield('script')
</body>
</html>
