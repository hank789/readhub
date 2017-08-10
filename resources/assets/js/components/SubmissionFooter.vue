<template>
	<div class="index-submission-footer user-select">
		<div :class="auth.isMobileDevice ? 'flex-space' : 'display-inline'">
			<div :class="auth.isMobileDevice ? '' : 'display-inline'">
				<router-link :to="url" class="comments-icon h-green"
				data-toggle="tooltip" data-placement="top" title="Comments">

					<svg class="icon-inwehub v-icon" aria-hidden="true">
						<use xlink:href="#icon-pinglun1"></use>
					</svg>

					<span class="commentNum" v-if="comments" v-text="comments"></span>
				</router-link>

				<a @click="$emit('bookmark')"
					data-toggle="tooltip" data-placement="top" title="Bookmark">

					<svg class="icon-inwehub v-icon shoucang" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'" aria-hidden="true">
						<use xlink:href="#icon-shoucang-xianxing1"></use>
					</svg>
				</a>


			</div>

			<div class="voting-wrapper display-none mobile-only">
				<a class="fa-stack align-right" @click="$emit('upvote')"
					data-toggle="tooltip" data-placement="top" title="Upvote">

					<svg class="icon-inwehub v-icon" aria-hidden="true" :class="upvoted ? 'go-primary' : 'go-gray'">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-dianzan1"></use>
					</svg>
				</a>

				<div class="detail">
					{{ points }}
				</div>

				<a class="fa-stack align-right" @click="$emit('downvote')"
					data-toggle="tooltip" data-placement="top" title="Downvote">

					<svg class="icon-inwehub v-icon rotate-180" aria-hidden="true" :class="downvoted ? 'go-red' : 'go-gray'">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-dianzan1"></use>
					</svg>
				</a>

				<div class="ui icon top right pointing dropdown" v-if="!isGuest">
					<i class="v-icon v-more" aria-hidden="true"></i>

					<div class="menu menu-inwehub-menu">
						<button class="item" @click="$emit('report')" v-if="!owns">
							举报
						</button>

						<button class="item" @click="$emit('hide')" v-if="!owns">
							隐藏
						</button>

						<button class="item" @click="$emit('nsfw')" v-if="showNSFW">
							NSFW
						</button>

						<button class="item" @click="$emit('sfw')" v-if="showSFW">
							Family Safe
						</button>

						<button class="item" @click="$emit('destroy')" v-if="owns">
							删除
						</button>

						<button class="item" @click="$emit('approve')" v-if="showApprove">
							审核通过
						</button>

						<button class="item" @click="$emit('disapprove')" v-if="showDisapprove">
							删除
						</button>

						<button class="item" @click="$emit('removethumbnail')" v-if="showRemoveTumbnail">
							移除图片
						</button>
					</div>
				</div>
			</div>
		</div>

		<span class="desktop-only">
			 {{ date }}
			<router-link v-if="false" :to="'/' + '@' + submission.owner.username" class="h-underline desktop-only">
				{{ '@' + submission.owner.username }}
			</router-link>
			发布于 <router-link :to="'/c/' + submission.category_name" class="category-label h-underline">#{{ submission.category_name }}</router-link>
		</span>

	</div>
</template>

<script>
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        props: [
        	'url', 'comments', 'bookmarked', 'submission', 'upvoted', 'downvoted', 'points'
        ],

        data () {
            return {
                auth,
                Store
            }
        },

        computed: {
        	/**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns() {
        		return auth.id == this.submission.owner.id
        	},

        	showApprove(){
				return !this.submission.approved_at && Store.moderatingAt.indexOf(this.submission.category_id) != -1 && !this.owns
			},

			showDisapprove(){
				return !this.submission.deleted_at && Store.moderatingAt.indexOf(this.submission.category_id) != -1 && !this.owns
			},

			showNSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.submission.category_id) != -1) && !this.submission.nsfw
			},

			showSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.submission.category_id) != -1) && this.submission.nsfw
			},

			showRemoveTumbnail(){
				if (this.owns && this.submission.data.thumbnail)
					return true
				return false
			},

            date () {
                return moment(this.submission.created_at).fromNow()
            }
        },

        mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
	        	this.$root.loadSemanticDropdown()
			})
        }
    };
</script>
