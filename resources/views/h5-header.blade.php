<header class="header-inwehub" :path="$route.path" v-if="$route.path !== '/share'">
    <vui-back-button v-if="$route.path !== '/h5'"></vui-back-button>
    <div class="title" v-text="title"></div>
    <a class="shareBtn" @click="share()" v-if="/^\/c\//.test($route.path)">
        <svg aria-hidden="true" class="icon icon-inwehub" >
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-fenxiang"></use>
        </svg>
    </a>
</header>



