<header class="header-voten user-select">
	<div class="left-header" style="margin-left:-0.5rem">
		<vui-back-button></vui-back-button>
	</div>

    <div class="flex-display">
        <div class="dropdown head-notification-icons">
        	@if(Auth::check())
				<button type="button" class="btn-nth relative" id="messages-btn" @click="changeRoute('messages')"
	            data-toggle="tooltip" data-placement="bottom" title="Messages">
	                <i class="v-icon v-inbox-1" aria-hidden="true"></i>
	                <span class="queue-number" v-show="unreadMessages" v-text="unreadMessages"></span>
	            </button>

	            <button type="button" class="btn-nth relative" aria-haspopup="true"
				data-toggle="tooltip" data-placement="bottom" title="Notifications" aria-expanded="false" @click="changeRoute('notifications')">
	           		<i class="v-icon v-bell-2" aria-hidden="true"></i>
	               	<span class="queue-number" v-show="unreadNotifications" v-text="unreadNotifications"></span>
	            </button>
        	@endif

        	@if (!Auth::check())
        		<button class="v-button v-button--green relative" @click="mustBeLogin">
	        		登陆
	        	</button>
        	@endif

            <button type="button" class="btn-nth relative" aria-haspopup="true"
			data-toggle="tooltip" data-placement="bottom" title="Search" aria-expanded="false" @click="changeRoute('search')">
           		<i class="v-icon v-search-2" aria-hidden="true"></i>
            </button>

			<router-link :to="'/h5'" class="btn-nth relative" aria-haspopup="true"
			data-toggle="tooltip" data-placement="bottom" title="Home" aria-expanded="false">
           		<i class="v-icon v-home" aria-hidden="true" @click="homeRoute"></i>
            </router-link>
        </div>

		@if(Auth::check())
	        <div class="ui icon top right green pointing dropdown pull-right">
	            <img src="{{Auth::user()->avatar}}" alt="{{Auth::user()->username}}" class="header-avatar">

	            <div class="menu">
	                <div class="header">我的阅读</div>

					<router-link :to="'/submit'" class="item">
	                    提交文章
	                </router-link>

	                <router-link :to="{ path: '/bookmarks' }" class="item">
	                    我的收藏
	                </router-link>

					<router-link :to="{ path: '/subscribed-channels' }" class="item">
	                    我的订阅
	                </router-link>

					<router-link :to="'/find-channels'" class="item">
						寻找频道
					</router-link>

					<router-link :to="'/channel'" class="item">
	                    新建频道
	                </router-link>

	                <div class="ui divider"></div>

					@if(!isMobileDevice())
						<div class="header" v-if="Store.moderatingCategories.length">Moderating Channels</div>
						<router-link :to="'/c/' + item.name" class="item" v-for="(item, index) in Store.moderatingCategories"
									 :key="item.id" v-if="Store.moderatingCategories.length && index < 6">
							<img class="square" :src="item.avatar" :alt="item.name">
							@{{ item.name }}
						</router-link>
						<div class="ui divider" v-if="Store.moderatingCategories.length && Store.moderatingCategories.length < 6"></div>

						<div class="item" v-if="Store.moderatingCategories.length && Store.moderatingCategories.length > 6">
							<i class="v-icon v-more"></i>
							<span class="text">More</span>
							<div class="left menu">
								<router-link :to="'/c/' + item.name" class="item" v-for="(item, index) in Store.moderatingCategories"
											 :key="item.id" v-if="index > 6">
									<img class="square" :src="item.avatar" :alt="item.name">
									@{{ item.name }}
								</router-link>
							</div>
						</div>
						<div class="ui divider" v-if="Store.moderatingCategories.length && Store.moderatingCategories.length > 6"></div>

						@if( Auth::user()->isVotenAdministrator() )
							<div class="header">Voten Administrators</div>

							<router-link :to="'/big-daddy'" class="item">
								Big Daddy
							</router-link>

							<a href="/backend" class="item">
								Backend
							</a>

							<div class="ui divider"></div>
						@endif
					@endif

	                <a class="item desktop-only" @click="changeModalRoute('keyboard-shortcuts-guide')">
	                    Keyboard Shortcuts
	                </a>

	                <router-link class="desktop-only" :to="'/help'" class="item">
	                    Help
	                </router-link>

	            </div>
	        </div>
		@endif
    </div>
</header>
