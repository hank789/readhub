<template>
<div>
	<category-header v-if="loaded && !auth.isMobileDevice"></category-header>

	<category-header-mobile v-if="loaded && auth.isMobileDevice"></category-header-mobile>

	<div class="col-full">
		<nsfw-warning v-if="submission.nsfw == 1 && !auth.nsfw"
			:text="'This submission contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<div v-if="submission.nsfw == 0 || auth.nsfw">
			<loading v-if="loadingSubmission"></loading>

			<full-submission v-if="!loadingSubmission" :list="submission" :full="true"></full-submission>

		    <section class="box-typical comments" id="comments-section" v-if="!loadingSubmission">
		        <header class="box-typical-header-sm bordered user-select flex-space">
		            <div>
		            	<span v-text="uniqueList.length"></span>
		            	条回复: <span class="go-gray go-small" v-if="!isGuest && false">({{ onlineUsersCount }} 在线用户)</span>

		            </div>
		            <div v-if="false" class="head-sort-icon" v-show="comments.length > 1">
		                <i class="v-icon v-like pointer" aria-hidden="true"
		                   data-toggle="tooltip" data-placement="bottom" title="Hottest"
		                   @click="newSort('hot')"
		                   :class="{ 'go-primary': sort == 'hot' }"></i>
		                <i class="v-icon v-clock pointer" aria-hidden="true"
		                   data-toggle="tooltip" data-placement="bottom" title="Newest"
		                   @click="newSort('new')"
		                   :class="{ 'go-primary': sort == 'new' }"></i>
		            </div>
		        </header>

		        <div class="box-typical-inner ui threaded comments" v-if="submission.id != 0">
		            <comment-form :submission="submission.id" :parent="0"></comment-form>

		            <loading v-if="loadingComments && page < 2"></loading>

					<comment :list="c" :comments-order="commentsOrder" v-for="c in uniqueList" :key="c.id" :full="true"></comment>
		        </div>
		    </section>

		    <button class="v-button v-button--block" v-if="moreComments" @click="loadMoreComments">
	        	加载更多回复
	    	</button>
		</div>


	</div>


	<div id="shareWrapper" class="shareWrapper mui-popover mui-popover-action mui-popover-bottom" style="display: none">
		<div class="title">分享到</div>
		<div class="more">
			<div class="single" id="wechatShareBtn"
				 @click="shareToHaoyou()"
			>
				<img src="/imgs/wechat_2x.png" />
			</div>
			<div class="single" id="wechatShareBtn2" @click="shareToPengyouQuan()">
				<img src="/imgs/pengyouquan.png" />
			</div>
		</div>
	</div>

	<div id="shareShowWrapper" class="mui-popover mui-popover-action mui-popover-top" @click="toggleShareNav()" style="display: none">
		<svg class="icon icon-inwehub" aria-hidden="true">
			<use xlink:href="#icon-dianzheli"></use>
		</svg>
	</div>

</div>
</template>

<script>
	import FullSubmission from '../components/FullSubmission.vue';
	import Comment from '../components/Comment.vue';
	import CommentForm from '../components/CommentForm.vue';
	import CategoryHeader from '../components/CategoryHeader.vue';
	import CategoryHeaderMobile from '../components/CategoryHeaderMobile.vue';
	import Loading from '../components/Loading.vue';
	import NsfwWarning from '../components/NsfwWarning.vue';
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        components: {
            FullSubmission,
            Comment,
            CommentForm,
            Loading,
            CategoryHeader,
            CategoryHeaderMobile,
			NsfwWarning
        },

        data () {
            return {
            	page: 1,
            	moreComments: false,
                submission: [],
                loadingComments: true,
                loadingSubmission: true,
                comments: [],
                auth,
                sort: 'hot',
                onlineUsers: [],
                category: this.$route.params.name,
                Store,
                preload
            }
        },

        created () {
            this.getSubmission();
            this.getComments();
            this.listen();
            this.$eventHub.$on('newComment', this.newComment);
        },

	    watch: {
			'$route' () {
	            this.getSubmission();
	            this.getComments();
	            this.clearContent();
	            this.listen();
	            this.updateCategoryStore();
	            this.$eventHub.$on('newComment', this.newComment);
			}
		},

        computed: {
    	    onlineUsersCount() {
    	        return this.onlineUsers.length;
			},

			/**
			 * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
			 * timestamps) we need a different approch to make sure the list is always unique.
			 * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
			 *
			 * @return array
			 */
			uniqueList() {
				let unique = []
				let temp = []

				this.comments.forEach(function(element, index, self) {
					if (temp.indexOf(element.id) === -1) {
						unique.push(element)
						temp.push(element.id)
					}
				})

				return unique;
			},

			/**
			 * Is the category store loaded yet
			 *
			 * @return bool
			 */
        	loaded () {
                if (Store.category.id != undefined) {
                    return Store.category.id == this.$route.params.name || Store.category.name == this.$route.params.name;
                }

                return false;

	        },

            /**
             * The order that comments should be printed with
             *
             * @return string
             */
            commentsOrder() {
            	return this.sort == 'hot' ? 'rate' : 'created_at';
            },
        },
        mounted() {
			var shareWrapper = document.getElementById('shareWrapper');
            document.body.appendChild(shareWrapper);

            var shareShowWrapper = document.getElementById('shareShowWrapper');
            document.body.appendChild(shareShowWrapper);
		},
        methods: {
            share(){
                setTimeout(() => {
                    mui('#shareWrapper').popover('toggle');
                }, 150);
            },
            toggleShareNav() {
                mui('#shareShowWrapper').popover('toggle');
            },
            shareToHaoyou() {
                this.sendHaoyou();
                if (mui.os.plus) {
                    mui('#shareWrapper').popover('toggle');
                } else {
                    mui('#shareWrapper').popover('toggle');
                    mui('#shareShowWrapper').popover('toggle');
                }
            },
            shareToPengyouQuan(){
                this.sendPengYouQuan();
                if (mui.os.plus) {
                    mui('#shareWrapper').popover('toggle');
                } else {
                    mui('#shareWrapper').popover('toggle');
                    mui('#shareShowWrapper').popover('toggle');
                }

            },
            successCallback(){
                mui.toast('分享成功');

            },
            failCallback(error){
                console.log(JSON.stringify(error));
                mui.toast('分享失败');
            },

        	/**
        	 * resets all the basic data to prevent possible conflicts
        	 *
        	 * @return void
        	 */
        	clearContent () {
        		this.moreComments = false;
        		this.page = 1;
        		this.comments = [];
        	},

        	loadMoreComments () {
        		this.page ++;
                this.moreComments = false;
        		this.getComments();
        	},

        	/**
	    	 * Checks wheather or not the Store.category needs to be filled or updated, and if yes simply does it
	    	 *
	    	 * @return void
	    	 */
	    	updateCategoryStore() {
	    		if (Store.category.name == undefined || Store.category.id != this.$route.params.name) {
		    		this.$root.getCategoryStore(this.$route.params.name);
		    		this.category = this.$route.params.name;
	    		}
	    	},

	    	/**
	    	 * receives the broadcasted comment.
	    	 *
	    	 * @return void
	    	 */
            newComment(comment) {
            	if (comment.parent_id != 0 || comment.submission_id != this.submission.id) return;

				// add broadcasted (used for styling)
				if (comment.user_id != auth.id) {
					comment.broadcasted = true;
				}

				this.comments.unshift(comment);
				this.submission.comments_number ++;
            },

            /**
             * listen for broadcasted comments
             *
             * @return void
             */
            listen() {
                const channelAddress = 'submission.' + this.$route.params.slug;

                Echo.channel(channelAddress)
                    .listen('CommentCreated', event => {
                    	this.$eventHub.$emit('newComment', event.comment);
                    }).listen('CommentWasPatched', event => {
                    	this.$eventHub.$emit('patchedComment', event.comment);
                    }).listen('CommentWasDeleted', event => {
                    	this.$eventHub.$emit('deletedComment', event.comment);
                    });

                // we can't do presence channel or/and listen for private channels, if the user is a guest
                if (this.isGuest) return;

                Echo.join(channelAddress)
				    .here((users) => {
				        this.onlineUsers = users;
				    })
				    .joining((user) => {
				        this.onlineUsers.push(user);
				    })
				    .leaving((user) => {
                        let index = this.onlineUsers.indexOf(user.username);
                        this.onlineUsers.splice(index, 1);

                        // if typer loses his connection for any reason, we $emit "finished-typing" because
						// after all, we must make sure other users won't see "@user is typing" forever!
                        this.$eventHub.$emit('finished-typing', user.username);
				    });
            },

            /**
             * Get submissions
             *
             * @return void
             */
            getSubmission() {

                var bindShare = () => {
                    var avatarUrl = null;
                    if (/^http/.test(this.submission.owner.avatar)) {
                        avatarUrl = this.submission.owner.avatar;
                    } else {
                        avatarUrl = window.location.protocol + '//' + window.location.host +  this.submission.owner.avatar;
                    }
                    console.log('头像url:' + avatarUrl);
                    var data = {
                        title: 'InweHub发现 | ' + this.submission.title,
                        link: window.location.href,
                        content:  '来自「 ' + this.submission.category_name + '」，这里有特别的评论，点击去看看或者参与互动？',
                        imageUrl: avatarUrl,
                        thumbUrl: avatarUrl + '?x-oss-process=image/resize,h_100,w_100',
                    };

                    console.log('注册分享');
                    console.log(data);
                    window.Share.bindShare(
                        this,
                        data,
                        this.successCallback,
                        this.failCallback
                    );
				}

            	// if landed on a submission page
            	if (preload.submission) {
            		this.submission = preload.submission;
                    bindShare();
            		Store.category = preload.submission.category;
            		this.loadingSubmission = false;
            		delete preload.submission;
            		return;
            	}

                axios.get('/get-submission', {
            		params: {
            			slug: this.$route.params.slug
            		}
            	}).then((response) => {
					this.submission = response.data;
					this.setPageTitle(this.submission.title);

                    if(!this.loaded) {
                    	Store.category = response.data.category;
                    }

                    bindShare();

                    this.loadingSubmission = false;
				}).catch((error) => {
					if (error.response.status === 404) {
						this.$router.push('/404')
					}
				});
            },

            /**
             * get comments
             *
             * @return void
             */
            getComments () {
                this.loadingComments = true

                axios.get('/submission-comments', {
                	params: {
	                	submission_slug: this.$route.params.slug,
	                	page: this.page,
	                	sort: this.sort
                	}
                }).then((response) => {
                	this.loadingComments = false

                    this.comments.push(...response.data.data)

                    if (response.data.next_page_url != null) {
                    	this.moreComments = true
                    }
                })
            },

            newSort(sort) {
            	if (sort == this.sort) return;

            	this.clearContent();
            	this.getComments();
                this.sort = sort;
            }
        },

        /**
         * necessary actions before leaving this submission page
         *
         * @return void
         */
        beforeRouteLeave(to, from, next) {
        	Echo.leave('submission.' + from.params.slug);

			next();
		}
    }

</script>
