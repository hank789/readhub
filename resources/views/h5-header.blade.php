<header class="header-inwehub">
	<vui-back-button></vui-back-button>
	<div class="title">发现</div>
	<div class="flex-display">
		<div class="dropdown head-notification-icons">
			@if(Auth::check())
				<button type="button" class="btn-nth relative" aria-haspopup="true"
						data-toggle="tooltip" data-placement="bottom" title="Notifications" aria-expanded="false" @click="changeRoute('notifications')">
					<i class="v-icon v-bell-2" aria-hidden="true"></i>
					<span class="queue-number" v-show="unreadNotifications" v-text="unreadNotifications"></span>
				</button>
			@endif
		</div>
	</div>
</header>
