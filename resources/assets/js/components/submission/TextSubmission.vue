<template>
	<div class="link-list-info">
		<!-- submission page -->
		<div v-if="full">
			<h1 class="submission-title">
				<i class="v-icon v-shocked go-red" aria-hidden="true" v-if="submission.nsfw"
					data-toggle="tooltip" data-placement="bottom" title="NSFW"
				></i>

				{{ submission.title }}
			</h1>

			<markdown :text="body" v-if="body && !editing"></markdown>

			<textarea class="form-control v-input-big" rows="3" id="text" placeholder="描述(可选)..." v-show="editing"
                    v-model="editedBody"
            ></textarea>

            <button type="submit" class="v-button v-button--green margin-top-1" @click="patch" v-show="editing">
                提交
            </button>
            <button type="submit" class="v-button v-button--red margin-top-1" @click="cancelEditing" v-show="editing">
                取消
            </button>
		</div>

		<!-- submission indexing pages -->
		<div v-else>
			<a @tap.stop.prevent="openReadhubPage('/c/' + submission.category_id + '/' + submission.slug)"
			class="flex-space v-ultra-bold">
				{{ submission.title }}
			</a>

			<div class="mobile-only mobile-submission-item-action">
				{{ date }}
				<router-link v-if="false" :to="'/' + '@' + submission.owner.id" class="h-underline">
					{{ '@' + submission.owner.username }}
				</router-link>
				发布于 <label class="category-label">#{{ submission.category_name }}</label>
			</div>


			<submission-footer :url="url" :comments="comments" :bookmarked="bookmarked" :submission="submission"
			@bookmark="$emit('bookmark')" @report="$emit('report')" @recommend="$emit('recommend')" @hide="$emit('hide')" @nsfw="$emit('nsfw')" @sfw="$emit('sfw')" @destroy="$emit('destroy')" @approve="$emit('approve')" @disapprove="$emit('disapprove')" @removethumbnail="$emit('removethumbnail')" :upvoted="upvoted" :downvoted="downvoted" :points="points"
			@upvote="$emit('upvote')" @downvote="$emit('downvote')"
			></submission-footer>
		</div>
	</div>
</template>

<script>
    import Markdown from '../../components/Markdown.vue';
	import SubmissionFooter from '../../components/SubmissionFooter.vue';
    import Webview from '../../mixins/Webview';


    export default {
        data() {
            return {
                editing: false,
                body: this.submission.data.text,
                editedBody: this.submission.data.text
            }
        },

        mixins: [Webview],

        props: {
        	nsfw: {},
        	submission: {},
        	url: {},
        	comments: {},
        	bookmarked: {},
        	upvoted: {},
        	downvoted: {},
        	points: {},
            full: {
                type: Boolean,
                default: false,
            },
        },

        components: {
            Markdown,
            SubmissionFooter
        },
        computed: {
            date () {
                return moment(this.submission.created_at).fromNow()
            }
		},

        watch: {
            'submission' () {
                this.body = this.submission.data.text;
				this.editedBody = this.submission.data.text;
            }
        },

        created() {
        	this.$eventHub.$on('edit-submission', this.editSubmission);
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
	        	this.$root.autoResize();
			})
		},

		methods: {
			/**
			 * opens the edit form
			 *
			 * @return void
			 */
			editSubmission() {
			    this.editing = !this.editing;
			},

			/**
			 * patches the TextSubmission
			 *
			 * @return void
			 */
			patch() {
				axios.post('/patch-text-submission', {
					id: this.submission.id,
					text: this.editedBody
				})
				.then((response) => {
					this.body = this.editedBody;
					this.editing = false;
				}).catch((error) => {
					this.editing = true;
				});
			},

			/**
			 * cancels editing the TextSubmission
			 *
			 * @return void
			 */
			cancelEditing() {
				this.editedBody = this.body;
			    this.editing = false;
			},
		}
    }
</script>
