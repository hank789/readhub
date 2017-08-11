<header class="header-inwehub">
	<vui-back-button v-if="$route.path !== '/h5'"></vui-back-button>
	<div class="title">发现</div>
	<div class="flex-display">
		<div class="dropdown head-notification-icons">
			@if(Auth::check())
				<button type="button" class="btn-nth relative" aria-haspopup="true"
						data-toggle="tooltip" data-placement="bottom" title="Notifications" aria-expanded="false" @click="changeRoute('notifications')">

					<i><svg class="icon-inwehub v-icon" aria-hidden="true">
							<use xlink:href="#icon-xiaoxi1"></use>
						</svg></i>
					<span class="queue-number" v-show="unreadNotifications" v-text="unreadNotifications"></span>
				</button>
			@endif
		</div>
	</div>
</header>
