<template>
<div>
	<div v-bind:style="{ background: coverBackground }" class="profile-cover">
	    <div class="container padding-top-3 user-select full-width">
	        <div class="cols-flex">
	            <div v-if="false" class="category-header-left">
	            	<!-- avatar -->
		                <div class="profile-avatar avatar-preview" v-if="$route.name == 'category-settings'">
		                    <button type="button">
					            <img v-bind:alt="Store.category.name" v-bind:src="Store.category.avatar" />

					            <div class="update">
					                <i class="v-icon v-photo" aria-hidden="true"></i>
					                上传图片
					            </div>
					        </button>

			                <input type="file" id="fileUploadFile" @change="passToCropModal">
	                    </div>

	                    <div class="profile-avatar" v-else>
	        				<router-link :to="'/c/' + Store.category.id">
			                    <img v-bind:src="Store.category.avatar" v-bind:alt="Store.category.name" />
		                	</router-link>
		                </div>
	                <!-- end avatar -->
	            </div>

	            <div class="category-header-middle">
                    <h1 v-if="$route.name == 'category'">
        				<router-link :to="'/c/' + Store.category.id" class="flex-center-inline">
	                        <i class="v-icon v-channel" aria-hidden="true"></i>{{ Store.category.name }}
	                	</router-link>
                    </h1>

                    <h2 v-else>
        				<router-link :to="'/c/' + Store.category.id" class="flex-center-inline">
	                        <i v-if="false" class="v-icon v-channel" aria-hidden="true"></i>{{ Store.category.name }}
	                	</router-link>
                    </h2>

	                <p>
	                    {{ Store.category.description }}
	                </p>

					<span class="inline-block">
						<i class="v-icon v-submissions" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Submissions"></i>{{ Store.category.stats.submissionsCount }}
					</span>

					<span class="inline-block">
						<i class="v-icon v-chat" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Comments"></i>{{ Store.category.stats.commentsCount }}
					</span>

	                <span v-if="false">
                        <i class="v-icon v-calendar" aria-hidden="true"></i>
	                	创建于: {{ date }}
	                </span>
	            </div>

				<div v-if="false" class="category-header-right">
					<div class="karma">
						<div class="karma-number">
							{{ Store.category.stats.subscribersCount }}
						</div>

						<div class="karma-text">
							订阅人数
						</div>
					</div>
				</div>
	        </div>
	    </div>
	</div>

	<nav class="nav has-shadow user-select">
	    <div class="container">
	        <div class="nav-left">
	        	<router-link :to="{ path: '/c/' + $route.params.name }" class="nav-item is-tab" :class="{ 'is-active': sort == 'hot' }">
					热门
				</router-link>

				<router-link :to="{ path: '/c/' + $route.params.name + '?sort=new' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'new' }">
					最新
				</router-link>

				<router-link v-if="false" :to="{ path: '/c/' + $route.params.name + '?sort=rising'  }" class="nav-item is-tab" :class="{ 'is-active': sort == 'rising' }">
					上升中
				</router-link>
	        </div>

	        <div class="channel-admin-btn">

				<svg v-if="false" class="icon-inwehub v-icon" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" @click="bookmark"
					 data-toggle="tooltip" data-placement="bottom" title="Bookmark" aria-hidden="true">
					<use xlink:href="#icon-shoucangxingxing"></use>
				</svg>

				<div class="ui icon top right green pointing dropdown" data-toggle="tooltip" data-placement="bottom" title="More"
					 id="more-button">
					<i class="v-icon v-more" aria-hidden="true"></i>

					<div class="menu menu-inwehub-menu">
						<button class="item" @click="emitModerators">
							管理者
						</button>

						<button v-if="false" class="item" @click="emitRules">
							频道规则
						</button>
					</div>
				</div>

            	<router-link :to="{ path: '/c/' + $route.params.name + '/mod' }" class="v-button v-button--primary"
				v-if="isModerator">
					管理
				</router-link>

            	<button class="v-button" @click="submitButton">
            		提交文章
            	</button>

            	<subscribe v-if="!isGuest && false"></subscribe>
	        </div>
	    </div>
	</nav>
</div>
</template>

<script>
import Subscribe from '../components/Subscribe-button.vue'
import Helpers from '../mixins/Helpers';

export default {
	mixins: [Helpers],

    components: {
    	Subscribe
    },

    data: function () {
        return {
    		fileUploadFormData: new FormData(),
        	Store,
        	bookmarked: false
        }
    },

    created () {
    	this.setBookmarked();
    },

    watch: {
        '$route' () {
            this.setBookmarked();
        },

        'Store.categoryBookmarks' () {
            this.setBookmarked();
        },
    },

	mounted () {
		this.$nextTick(function () {
        	this.$root.loadSemanticTooltip();
        	this.$root.loadSemanticDropdown();
		})
	},

    methods: {
    	submitButton() {
    	    if (this.isGuest) {
    	    	this.mustBeLogin();
    	    	return;
    	    }

    	    this.$router.push('/submit?channel=' + Store.category.name);
    	},

		emitRules(){
			this.$eventHub.$emit('rules')
		},

		emitModerators() {
			this.$eventHub.$emit('moderators')
		},


    	/**
		 * Passes the photo to the cropModal to take care of the rest
		 *
		 * @return void
		 */
		passToCropModal (e)
		{

            this.fileUploadFormData.append('photo', e.target.files[0]);

    		axios.post('/upload-temp-avatar', this.fileUploadFormData).then((response) => {
				this.$eventHub.$emit('crop-photo-uploaded', response.data)
            });

    		this.$eventHub.$emit('crop-category-photo')
		},

    	/**
         * Whether or not user has bookmarked the submission
         *
         * @return void
         */
        setBookmarked() {
            if (Store.categoryBookmarks.indexOf(Store.category.id) != -1) {
                this.bookmarked = true;
			} else {
                this.bookmarked = false;
			}
		},

        /**
         *  Toggles the category into bookmarks
		 *
		 *  @return void
         */
    	bookmark (category) {
    		if (this.isGuest) {
        		this.mustBeLogin();
        		return;
        	}

    		this.bookmarked = !this.bookmarked

			axios.post('/bookmark-category', {
				id: Store.category.id
			}).then((response) => {
				if (Store.categoryBookmarks.indexOf(Store.category.id) != -1){
                	var index = Store.categoryBookmarks.indexOf(Store.category.id)
                	Store.categoryBookmarks.splice(index, 1)
                    this.showAppToast('取消收藏');
                	return
                }
                this.showAppToast('收藏成功');
				Store.categoryBookmarks.push(Store.category.id)
			})
    	},
    },

    computed: {
    	/**
    	 * the sort of the page
    	 *
    	 * @return mixed
    	 */
    	sort() {
    		if (this.$route.name != 'category-submissions')
    			return null;

    	    if (this.$route.query.sort == 'new')
    	    	return 'new';

    	    if (this.$route.query.sort == 'rising')
    	    	return 'rising';

    	    return 'hot';
    	},

    	date () {
    		return moment(Store.category.created_at).utc(moment().format("MMM Do")).format("MMM Do")
    	},

    	isModerator () {
    		return Store.moderatingAt.indexOf(Store.category.id) != -1
    	},

		coverBackground () {
    	    return '#3c3e44';
//        	if (Store.category.color == 'Red') {
//        		return '#9a4e4e'
//        	} else if (Store.category.color == 'Blue') {
//        		return '#5487d4'
//        	} else if (Store.category.color == 'Dark Blue') {
//        		return '#2f3b49'
//        	} else if (Store.category.color == 'Dark Green') {
//        		return '#507e75'
//        	} else if (Store.category.color == 'Bright Green') {
//        		return 'rgb(117, 148, 127)'
//        	} else if (Store.category.color == 'Purple') {
//        		return '#4d4261'
//        	} else if (Store.category.color == 'Orange') {
//        		return '#ffaf40'
//        	} else if (Store.category.color == 'Pink') {
//        		return '#ec7daa'
//        	} else { // userStore.color == 'Black'
//        		return '#333'
//        	}
        }
    }
}
</script>
