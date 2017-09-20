<template>
    <div class="side-fixed"  id="v-sidebar">
        <div class="sidebar-offer-wrapper">
        	<h3>
        		新用户?
        	</h3>

        	<p>
				前往<a href="https://adsolj.mlinks.cc/A0k8">下载APP</a>
        	</p>

        </div>

        <aside class="menu">
        	<div class="flex-space">
	            <p class="menu-label">
	                #推荐<span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
	            </p>

				<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
	            	<i class="v-icon v-config" @click="mustBeLogin"></i>
				</div>
        	</div>

            <div class="ui category search side-box-search">
                <div class="ui mini icon input">
                  <input class="prompt" type="text" placeholder="频道..." spellcheck="false"
						 v-model="subscribedFilter">
                  <i class="v-icon v-search search icon"></i>
                </div>
            </div>

            <div class="no-subsciption" v-if="!Store.subscribedCategories.length && !Store.loading">
				<svg class="icon-inwehub v-icon" aria-hidden="true">
					<use xlink:href="#icon-zanwushuju"></use>
				</svg>
            	没有频道
            </div>

            <ul class="menu-list" v-else>
                <li v-for="category in sortedSubscribeds">
    				<router-link :to="'/c/' + category.name">
    					<img class="square" v-bind:src="category.avatar" v-bind:alt="category.name">
                        <span class="v-channels-text">{{ category.name }}</span>
    				</router-link>
                </li>
            </ul>
        </aside>

        <hr>

        <ul class="sidebar-copyright">
        	<li>&copy; 2017</li>

        </ul>
    </div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
	mixins: [Helpers],

    data: function () {
        return {
            subscribedFilter: '',
            auth,
            Store
        };
    },

	watch: {
		'$route': function () {
			this.subscribedFilter = '';
		}
	},

	created() {
		axios.get(this.authUrl('sidebar-categories')).then((response) => {
	    	Store.subscribedCategories = response.data;
	    });
	},

    computed: {
        submitURL(){
            if (this.$route.params.name)
            	return "/submit?channel=" + this.$route.params.name

            return "/submit"
        },

    	/**
    	 * The sorted version of comments
    	 *
    	 * @return {Array} comments
    	 */
    	sortedSubscribeds () {
			var self = this

    		return _.orderBy(Store.subscribedCategories.filter(function (category) {
				return category.name.indexOf(self.subscribedFilter.toLowerCase()) !== -1
			}), 'subscribers', 'desc').slice(0, 5)
    	},
    },

    methods: {
        changeRoute(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },

        signUp() {
        	if (this.isMobile) {
        		this.$eventHub.$emit('toggle-sidebar');
        	}

            this.mustBeLogin();
        }
    },
}
</script>
