<template>
    <section class="container margin-top-1 col-7">
		<section class="box-typical comments" id="comments-section" v-if="comments.length">
	    	<div class="box-typical-inner ui threaded comments">
	    		<div v-for="c in comments" class="v-comment-not-full">
			        <comment :list="c" :comments-order="'created_at'"></comment>
			    </div>
	    	</div>
	    </section>

        <no-content v-if="nothingFound" :text="'您尚未收到过回复'"></no-content>

        <loading v-if="loading"></loading>

	    <no-more-items :text="'无更多内容'" v-if="NoMoreItems && !nothingFound"></no-more-items>
    </section>
</template>

<script>
    import Loading from '../components/Loading.vue';
    import Comment from '../components/Comment.vue';
    import NoContent from '../components/NoContent.vue';
    import NoMoreItems from '../components/NoMoreItems.vue';
    import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        components: {
            Loading,
            Comment,
            NoContent,
            NoMoreItems
        },

        data () {
            return {
            	NoMoreItems: false,
	            page: 0,
                comments: [],
                loading: true,
                nothingFound: false
            }
        },

        created () {
            this.$eventHub.$on('scrolled-to-bottom', this.loadMore);
            this.getComments();
            //this.setPageTitle('@' + this.$route.params.username);
        },

       	watch: {
	    	'$route': function () {
	    		this.clearContent();
	    		this.getComments();
	    	}
	    },


        methods: {
            loadMore() {
				if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems) {
					this.getComments();
				}
			},

        	clearContent() {
               	this.comments = []
                this.loading = true
                this.nothingFound = false
        	},

        	/**
        	 * Fetches user's comments
        	 *
        	 * @return void
        	 */
        	getComments() {
                this.loading = true
				this.page ++

				if (preload.comments && this.$route.name == 'user-comments' && this.page == 1) {
	        		this.comments = preload.comments.data;

					if (!this.comments.length) {
						this.nothingFound = true
					}

					if (preload.comments.next_page_url == null) {
						this.NoMoreItems = true
					}

					this.loading = false;

					// clear the preload
					delete preload.comments;

					return;
	        	}

        		axios.get('/user-comments', {
        			params: {
        				page: this.page,
	    				username: this.$route.params.username
        			}
	    		}).then((response) => {
                    this.comments = [...this.comments, ...response.data.data]

                    if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

	                if( this.comments.length < 1 ) {
	                	this.nothingFound = true
	                }

                    this.loading = false
	            });
        	}
        }
    }
</script>
