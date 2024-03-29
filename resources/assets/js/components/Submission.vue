<template>
	<transition name="fade">
		<div class="submission-item submission-wrapper" v-show="!hidden" :id="'submission' + list.id">
			<!-- side-voting -->
			<div class="side-voting desktop-only">
				<a class="fa-stack align-right" @click="voteUp"
					data-toggle="tooltip" data-placement="top" title="Upvote">
					<i class="v-icon v-up-fat side-vote-icon" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
				</a>

				<div class="user-select vote-number">
					{{ points }}
				</div>

				<a class="fa-stack align-right" @click="voteDown"
					data-toggle="tooltip" data-placement="bottom" title="Downvote">
					<i class="v-icon v-down-fat side-vote-icon" :class="downvoted ? 'go-red' : 'go-gray'"></i>
				</a>
			</div>

			<article class="flex1" v-bind:class="'box-typical profile-post ' + list.type">
				<!-- content -->
				<div class="profile-post-content">
					<text-submission v-if="list.type == 'text'" :submission="list" :nsfw="nsfw" :full="full" @bookmark="bookmark"
					:url="'/c/' + list.category_id + '/' + list.slug" :comments="list.comments_number" :bookmarked="bookmarked"
					@report="report" @recommend="recommend" @hide="hide" @nsfw="markAsNSFW" @sfw="markAsSFW" @destroy="destroy" @approve="approve" @disapprove="disapprove" @removethumbnail="removeThumbnail" :upvoted="upvoted" :downvoted="downvoted" @upvote="voteUp" @downvote="voteDown" :points="points"
					></text-submission>

					<img-submission v-if="list.type == 'img'" :submission="list" :nsfw="nsfw" :full="full"
						@zoom="showPhotoViewer" @bookmark="bookmark"
						:url="'/c/' + list.category_id + '/' + list.slug" :comments="list.comments_number" :bookmarked="bookmarked"
						@report="report" @recommend="recommend" @hide="hide" @nsfw="markAsNSFW" @sfw="markAsSFW" @destroy="destroy" @approve="approve" @disapprove="disapprove" @removethumbnail="removeThumbnail" :upvoted="upvoted" :downvoted="downvoted" @upvote="voteUp" @downvote="voteDown" :points="points"
					></img-submission>

					<gif-submission v-if="list.type == 'gif'" :submission="list" :nsfw="nsfw" :full="full"
						@play-gif="showGifPlayer" @bookmark="bookmark"
						:url="'/c/' + list.category_id + '/' + list.slug" :comments="list.comments_number" :bookmarked="bookmarked"
						@report="report" @recommend="recommend" @hide="hide" @nsfw="markAsNSFW" @sfw="markAsSFW" @destroy="destroy" @approve="approve" @disapprove="disapprove" @removethumbnail="removeThumbnail" :upvoted="upvoted" :downvoted="downvoted" @upvote="voteUp" @downvote="voteDown" :points="points"
					></gif-submission>

					<link-submission v-if="list.type == 'link'" :submission="list" :nsfw="nsfw" :full="full"
						@embed="showEmbed" @bookmark="bookmark"
						:url="'/c/' + list.category_id + '/' + list.slug" :comments="list.comments_number" :bookmarked="bookmarked"
						@report="report" @recommend="recommend" @hide="hide" @nsfw="markAsNSFW" @sfw="markAsSFW" @destroy="destroy" @approve="approve" @disapprove="disapprove" @removethumbnail="removeThumbnail" :upvoted="upvoted" :downvoted="downvoted" @upvote="voteUp" @downvote="voteDown" :points="points"
					></link-submission>
				</div>

				<!-- full page modals -->
				<photo-viewer v-if="photoViewer" :bookmarked="bookmarked" :points="points" @close="closeViwer"
				:list="list" :photoindex="photoViewerIndex"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></photo-viewer>

				<embed-viewer v-if="embedViewer" :bookmarked="bookmarked" :points="points" @close="closeEmbed"
				:list="list"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></embed-viewer>

				<gif-player v-if="gifPlayer" :bookmarked="bookmarked" :points="points" @close="closeGifPlayer"
				:list="list"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></gif-player>
			</article>
		</div>
	</transition>
</template>

<script>
    import TextSubmission from '../components/submission/TextSubmission.vue';
    import LinkSubmission from '../components/submission/LinkSubmission.vue';
    import ImgSubmission from '../components/submission/ImgSubmission.vue';
    import GifSubmission from '../components/submission/GifSubmission.vue';
	import PhotoViewer from '../components/PhotoViewer.vue';
	import EmbedViewer from '../components/Embed.vue';
	import GifPlayer from '../components/GifPlayer.vue';
	import Helpers from '../mixins/Helpers';
    import Webview from '../mixins/Webview';


    export default {
        props: ['list', 'full'],

        mixins: [Helpers, Webview],

        components: {
            TextSubmission,
            LinkSubmission,
            ImgSubmission,
			GifSubmission,
			PhotoViewer,
			EmbedViewer,
			GifPlayer,
        },

        data () {
            return {
                bookmarked: false,
                upvoted: false,
                downvoted: false,
                hidden: false,
                reported: false,
                sendingQuickComment: false,
				quickComment: '',
				photoViewerIndex: null,
				photoViewer: false,
				embedViewer: false,
				gifPlayer: false
            }
        },

        created () {
        	this.setBookmarked();
        	this.setVoteds();
			this.$eventHub.$on('photo-viewer', this.showPhotoViewer);
			this.$eventHub.$on('scape', this.closeViwer);
        },

	    watch: {
			'$route' () {
	        	this.setBookmarked();
	        	this.setVoteds();
			},

			'Store.submissionUpVotes' () {
				this.setVoteds();
			},

			'Store.submissionDownVotes' () {
				this.setVoteds();
			},

			'Store.submissionBookmarks' () {
				this.setBookmarked();
			},
		},

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
	        	this.$root.loadSemanticDropdown('submission' + this.list.id);
			})
		},


        computed: {
			points(){
				let total = this.list.upvotes - this.list.downvotes;

				if (total < 0 ) return 0;

				return total;
			},

			/**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns() {
        		return auth.id == this.list.owner.id;
        	},

            /**
             * Whether or not user wants to see NSFW content's image
             *
             * (Hint: The base idea is that we don't display NSFW content)
             * If the user wants to see NSFW media then return false, like it's not NSFW at all
             * Otherwise return true which means the media must not be displayed.
             * (false: the media will be displayed)
             *
             * @return boolean
             */
            nsfw() {
				return this.list.nsfw && !auth.nsfwMedia;
            },

			/**
			 * The current vote type. It's being used to optimize the voing request on the server-side.
			 *
			 * @return mixed
			 */
			currentVote () {
			    if (this.upvoted) {
			    	return "upvote";
			    }

				if (this.downvoted) {
					return "downvote";
				}

				return null;
			},

            date () {
                return moment(this.list.created_at).fromNow()
            },
        },

        methods: {
			removeThumbnail(){
				this.list.data.thumbnail = null
				this.list.data.img = null

				axios.post('/remove-thumbnail', {
				    id: this.list.id
				})
			},

			/**
			 * marks the submission as NSFW (not safe for work)
			 *
			 * @return void
			 */
			markAsNSFW() {
			     axios.post('/mark-submission-nsfw', {
			         id: this.list.id
			     }).then((response) => {
			         this.list.nsfw = true
			     })
			},

			/**
			 * marks the submission as NSFW (not safe for work)
			 *
			 * @return void
			 */
			markAsSFW() {
			     axios.post('/mark-submission-sfw', {
			         id: this.list.id
			     }).then((response) => {
			         this.list.nsfw = false
			     })
			},

        	/**
             * whether or not the user has voted on submission
             *
             * @return void
             */
            setVoteds () {
                if (Store.submissionUpVotes.indexOf(this.list.id) != -1) {
                    this.upvoted = true;
                    this.downvoted = false;
                    return;
                }

                if (Store.submissionDownVotes.indexOf(this.list.id) != -1) {
                    this.downvoted = true;
                    this.upvoted = false;
                    return;
                }

                this.downvoted = false;
                this.upvoted = false;
            },

        	/**
             * Whether or not user has bookmarked the submission
             *
             * @return void
             */
            setBookmarked() {
                if (Store.submissionBookmarks.indexOf(this.list.id) != -1) {
                    this.bookmarked = true;
				} else {
                    this.bookmarked = false;
				}
			},

        	/**
             * Toggles the submission into bookmarks
			 *
			 * @return void
             */
        	bookmark (submission) {
        		if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.bookmarked = !this.bookmarked

				axios.post('/bookmark-submission', {
					id: this.list.id
				}).then((response) => {
                    if (Store.submissionBookmarks.indexOf(this.list.id) != -1) {
                        var index = Store.submissionBookmarks.indexOf(this.list.id);
                        Store.submissionBookmarks.splice(index, 1);
                        this.showAppToast('取消收藏');
                        return;
                    }
                    this.showAppToast('收藏成功');

                    Store.submissionBookmarks.push(this.list.id);
				})
        	},

            /**
             * hide(block) submission
             *
             * @return void
             */
            hide () {
                this.hidden = true;
                axios.post('/hide-submission', { submission_id: this.list.id });
            },

            /**
             * Deletes the submission. Only the owner is allowed to make such decision.
             *
             * @return void
             */
            destroy () {
                axios.post('/destroy-submission', { id: this.list.id })
                if (this.full) {
                    this.$router.push('/');
                } else {
                	this.hidden = true;
                }
            },

			/**
             * Approves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			approve(){
				axios.post('/approve-submission', {
				    submission_id: this.list.id
				}).then((response) => {
				    this.list.approved_at = moment().utc().format('YYYY-MM-DD HH:mm:ss')
				})
			},

			/**
             * Disapproves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			disapprove(){
				axios.post('/disapprove-submission', {
				    submission_id: this.list.id
				}).then((response) => {
					if (this.full) {
	                    this.$router.push('/')
	                } else {
	                	this.hidden = true
	                }
				})
			},

            /**
            *  Report submission
            *
            *  @return void
            */
            report() {
                this.reported = true;
        		this.$eventHub.$emit('report-submission', this.list.id, this.list.category_name);
            },
            /**
             * 推荐到app
             */
            recommend () {
                axios.post('/recommend-app-submission', {
                    submission_id: this.list.id
                })
            },

            /**
             *  Upvote submission
             *
             *  @return void
             */
            voteUp() {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

				let id = this.list.id

				axios.post('/upvote-submission', {
					submission_id: id,
					previous_vote: this.currentVote
				})

            	// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --

            		var index = Store.submissionUpVotes.indexOf(id);
                	Store.submissionUpVotes.splice(index, 1);

            		return
            	}

				// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --

            		var index = Store.submissionDownVotes.indexOf(id);
                	Store.submissionDownVotes.splice(index, 1);
            	}

            	// Not voted
            	this.upvoted = true
            	this.list.upvotes ++
            	Store.submissionUpVotes.push(id)
            },


            /**
             *  Downvote submission
             *
             *  @return void
             */
            voteDown () {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

				let id = this.list.id

				axios.post('/downvote-submission', {
					submission_id: id,
					previous_vote: this.currentVote
				})

            	// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --

            		var index = Store.submissionDownVotes.indexOf(id);
                	Store.submissionDownVotes.splice(index, 1);

            		return
            	}

				// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --

            		var index = Store.submissionUpVotes.indexOf(id);
                	Store.submissionUpVotes.splice(index, 1);
            	}

            	// Not voted
            	this.downvoted = true
            	this.list.downvotes ++
            	Store.submissionDownVotes.push(id)
            },

			showPhotoViewer(index = null){
				if (index !== null) {
					this.photoViewerIndex = index
				}
	            this.photoViewer = true
	        },

			showEmbed(){
				this.embedViewer = true
			},

			showGifPlayer(){
				this.gifPlayer = true
			},

			closeViwer(){
				this.photoViewer = false
			},

			closeEmbed(){
				this.embedViewer = false
			},

			closeGifPlayer(){
				this.gifPlayer = false
			}
        }
    }
</script>
