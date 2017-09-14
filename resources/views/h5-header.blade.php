<header class="header-inwehub" :path="$route.path" v-if="$route.path !== '/share'">
    <vui-back-button v-if="$route.path !== '/h5'"></vui-back-button>
    <div class="title" v-text="title"></div>
    <a class="shareBtn" @click="share()" v-if="/^\/c\//.test($route.path)">
        <svg aria-hidden="true" class="icon icon-inwehub" >
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-fenxiang"></use>
        </svg>
    </a>
</header>


<div class="categoryMenu flex-space" v-if="$route.path === '/h5'">
    <div class="item" @tap.stop.prevent="categoryMenuClick(1)">
        <svg class="icon icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-wendashequ"></use>
        </svg>
        问答社区
    </div>
    <div class="item" @tap.stop.prevent="categoryMenuClick(2)">
        <svg class="icon icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-chengchangye-baominghuodong"></use>
        </svg>
        活动报名
    </div>
    <div class="item" @tap.stop.prevent="categoryMenuClick(3)">
        <svg class="icon icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-xiangmujiyu"></use>
        </svg>
        项目机遇
    </div>
    <div class="item" @tap.stop.prevent="categoryMenuClick(4)">
        <svg class="icon icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-fujinqiye"></use>
        </svg>
        附近企业
    </div>
    <div class="item" @tap.stop.prevent="categoryMenuClick(5)">
        <svg class="icon icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-gengduozhuanjia"></use>
        </svg>
        更多专家
    </div>
</div>

<div class="listBanner" id="listBanner" v-if="$route.path === '/h5'" style="top:-500px;">
    <swiper :options="swiperOption">
        <swiper-slide>
            <img src="/imgs/newguwen@2x.png"/>
        </swiper-slide>
        <swiper-slide>
            <img src="/imgs/neirongjingxuan@2x.png"/>
        </swiper-slide>
        <swiper-slide>
            <img src="/imgs/jiyuhuodong.png"/>
        </swiper-slide>
    </swiper>
</div>

<div class="menu-inwehub" id="menu-inwehub" v-if="$route.path === '/h5'">
    <div class="left">
        <router-link tag="div" :to="{ path: '/h5' }" class="menu-item" :class="{ 'active': sort == 'hot' }">
            热门
        </router-link>
        <router-link tag="div" :to="{ path: '/h5?sort=new' }" class="menu-item" :class="{ 'active': sort == 'new' }">
            最新
        </router-link>
    </div>
    <div class="right">
        <div class="menu-item ui dropdown top pointing">
            <svg class="icon-inwehub" aria-hidden="true">
                <use xlink:href="#icon-wode1"></use>
            </svg>
            <div class="menu">
                <div @click="goLink('/bookmarks/submissions')" class="item">
                我的收藏
            </div>
            <div @click="goLink('/' + '@' + auth.id)" class="item">
            我的发布
        </div>
    </div>
</div>

<div class="menu-item">
    <svg class="icon-inwehub" aria-hidden="true" @click="refresh">
    <use xlink:href="#icon-shuaxin"></use>
    </svg>
</div>

{{--<div class="menu-item desktop-only">--}}
    {{--<svg class="icon-inwehub" aria-hidden="true" @click="changeRoute('notifications')">--}}
    {{--<use xlink:href="#icon-xiaoxi1"></use>--}}
    {{--</svg>--}}
    {{--<span class="notification-number" v-show="getUnreadNotifications()" v-text="getUnreadNotifications()"></span>--}}
{{--</div>--}}

<router-link tag="div" :to="{ path: '/submit' }" class="menu-item">
    <svg class="icon-inwehub modify" aria-hidden="true">
        <use xlink:href="#icon-xiugai"></use>
    </svg>
</router-link>
</div>
</div>


