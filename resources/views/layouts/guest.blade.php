<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('head')
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="/vendor/js/socket.io.min.js"></script>

    <!-- CSRF Token -->
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
            'env' => config('app.env'),
            'is_h5' => session('is_h5'),
            'inwehub_url' => config('app.inwehub_url'),
            'deep_mlink'  => config('app.deep_link'),
            'echo_address' => config('broadcasting.connections.echo.app_address'),
            'pusherKey' => config('broadcasting.connections.pusher.key'),
            'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.ico">
    @include('user.user-style')
</head>

<body>
@include('google-analytics')

<div id="voten-app" class="guest-app" :class="{ 'background-white': Store.contentRouter != 'content', isWechat:isWechat(), isWebview:$route.path.match(/webview/)}">
    @if (session('is_h5'))
        @include('h5-header')
    @else
        @include('app-header')
    @endif

    <div class="v-content-wrapper @if (session('is_h5')) v-content-wrapper-h5  @endif  v-content-wrapper-guest">
		<div class="v-side" v-show="sidebar">
		    <guest-sidebar></guest-sidebar>
		</div>

		<search-modal v-if="Store.contentRouter == 'search'" :sidebar="sidebar"></search-modal>

        <div class="v-content {{ session('is_h5') ? 'v-content-inwehub' : '' }}" id="v-content" v-show="Store.contentRouter == 'content'" v-infinite-scroll="scrolledToBottom" infinite-scroll-disabled="scrolledBusy" infinite-scroll-distance="10">
            <transition name="fade">
                <rules v-if="modalRouter == 'rules'" :sidebar="sidebar"></rules>
                <moderators v-if="modalRouter == 'moderators'" :sidebar="sidebar"></moderators>
                <keyboard-shortcuts-guide v-if="modalRouter == 'keyboard-shortcuts-guide'" :sidebar="sidebar"></keyboard-shortcuts-guide>
                <markdown-guide v-if="modalRouter == 'markdown-guide'" :sidebar="sidebar"></markdown-guide>
                <login-modal v-if="modalRouter == 'login'" :sidebar="sidebar"></login-modal>
            </transition>

            <div :class="{ 'v-blur-blackandwhite': smallModal }">
                @yield('content')
            </div>
        </div>
    </div>

    <scroll-button></scroll-button>
</div>

<script>
    var auth = {
        font: 'Lato',
        nsfw: {{ 'false' }},
        nsfwMedia: {{ 'false' }},
        sidebar_color: 'Gray',
        isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
        <?php
            if (isMobileDevice()) {
                $submission_small_thumbnail = 'false';
            } else {
                $submission_small_thumbnail = 'true';
            }
        ?>
        submission_small_thumbnail: {{ $submission_small_thumbnail }},
        isGuest: {{ 'true' }},
        isAdmin: {{ 'false' }}
    };

    var preload = {};
</script>

@yield('script')
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>
@yield('footer')

</body>
</html>
