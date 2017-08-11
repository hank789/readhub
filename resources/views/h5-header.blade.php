<header class="header-inwehub" :path="$route.path">
	<vui-back-button v-if="$route.path !== '/h5'"></vui-back-button>
	<div class="title" v-text="title"></div>
</header>
