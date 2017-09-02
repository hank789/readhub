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
