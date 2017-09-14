<template>
	<div class="home-wrapper"  :class="{'home-wrapper-h5':Store.is_h5}">
		<home-submissions></home-submissions>
	</div>
</template>

<script>
	import HomeSubmissions from '../components/HomeSubmissions.vue';
	import Announcement from '../components/Announcement.vue';
	import Helpers from '../mixins/Helpers';
	import LocalStorage from '../mixins/LocalStorage';

    export default {
    	mixins: [Helpers, LocalStorage],

	    components: {
	        HomeSubmissions,
	        Announcement
	    },

        created() {
            this.setPageTitle('Inwehub - 阅读', true);
            this.askNotificationPermission();
        },

		mounted () {

			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
	        	this.$root.loadSemanticDropdown();
			})
		},

        computed: {
        	filter() {
        	    return Store.feedFilter;
        	},

        	/**
    	 	 * the sort of the page
	    	 *
	    	 * @return mixed
	    	 */
	    	sort() {
	    	    if (this.$route.query.sort == 'new')
	    	    	return 'new';

	    	    if (this.$route.query.sort == 'rising')
	    	    	return 'rising';

	    	    return 'hot';
	    	},
        },

        methods: {
    	    goLink(url) {
    	        setTimeout(() => {
                    this.$router.push(url);
				}, 100);
			},
            getUnreadNotifications(){
                this.$eventHub.$emit('getUnreadNotifications')
			},
            changeRoute: function(newRoute) {
                this.$eventHub.$emit('new-route', newRoute)
            },
        	/**
        	 * changes the filter for home feed
        	 *
        	 * @return void
        	 */
        	changeFilter(filter) {
        		if (Store.feedFilter == filter) return;

        	    Store.feedFilter = filter;

        	    this.putLS('feed-filter', filter);

        	    this.refresh();
        	},

        	/**
        	 * fires the refresh event
        	 *
        	 * @return void
        	 */
        	refresh() {
        	    this.$eventHub.$emit('refresh-home');
        	},

        	/**
        	 * In case the user has just joined to the Voten community let's ask them for the awesome Desktop notifications permission.
        	 *
        	 * @return void
        	 */
        	askNotificationPermission() {
                 if (this.$route.query.newbie == 1) {
                     if ('Notification' in window) {
                         Notification.requestPermission()
                     } else {
                         console.log('Your browser does not support desktop notifications. ')
                     }
                 }
        	}
        },
    }
</script>
