<template>
<section id="reported-items">
    <h1 class="dotted-title">
		<span>
			被举报的文章
		</span>
	</h1>

    <p>
        用户举报的文章都显示在这里。您需要审核这些被举报的文章。
    </p>

    <div class="tabs is-fullwidth">
        <ul>
            <router-link tag="li" active-class="is-active" :to="{ path: '' }" exact>
                <a>
					未处理
				</a>
            </router-link>

            <router-link tag="li" active-class="is-active" :to="{ path: '?type=solved' }" exact>
                <a>
					已处理
				</a>
            </router-link>
        </ul>
    </div>

    <loading v-if="loading"></loading>

    <div class="no-more-to-load user-select" v-if="nothingFound">
        <h3 v-text="'未有被举报的文章'"></h3>
    </div>

    <reported-submission v-for="item in items" :list="item" :key="item.id" v-if="item.submission"
    @disapprove-submission="disapproveSubmission" @approve-submission="approveSubmission"></reported-submission>
</section>
</template>

<script>
import Loading from '../components/Loading.vue'
import ReportedSubmission from '../components/ReportedSubmission.vue'

export default {
    components: {
        Loading,
        ReportedSubmission
    },

    mixins: [],

    data: function() {
        return {
            NoMoreItems: false,
            loading: true,
            nothingFound: false,
            items: [],
            page: 0,
            Store
        }
    },


    computed: {
        type() {
            if (this.$route.query.type == 'solved') {
                return 'solved'
            }

            if (this.$route.query.type == 'deleted') {
                return 'deleted'
            }

            return 'unsolved'
        }
    },


    created: function() {
        this.getItems()
        this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
    },

    watch: {
        'type': function () {
            this.clearContent()
            this.getItems()
        }
    },

    mounted() {
        //
    },

    methods: {
        disapproveSubmission(submission_id){
            axios.post('/disapprove-submission', { submission_id }).then((response) => {
                this.items = this.items.filter(function (item) {
				  	return item.submission.id != submission_id
				})

                if (!this.items.length) {
                    this.nothingFound = true
                }
            })
        },

        approveSubmission(submission_id){
            axios.post('/approve-submission', { submission_id }).then((response) => {
                this.items = this.items.filter(function (item) {
				  	return item.submission.id != submission_id
				})

                if (!this.items.length) {
                    this.nothingFound = true
                }
            })
        },

        loadMore() {
            if (Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems) {
                this.getItems()
            }
        },

        /**
         * Resets all the basic data
         *
         * @return void
         */
        clearContent() {
            this.nothingFound = false
            this.items = []
            this.loading = true
            this.page = 0
        },

        getItems() {
            this.page++;
            this.loading = true


            axios.post('/reported-submissions', {
                type: this.type,
                category: Store.category.name,
                page: this.page
            }).then((response) => {
                this.items = [...this.items, ...response.data.data]

                if (!this.items.length) {
                    this.nothingFound = true
                }

                if (response.data.next_page_url == null) {
                    this.NoMoreItems = true
                }

                this.loading = false
            })

        }
    },

    beforeRouteEnter(to, from, next){
        if (Store.category.id == to.params.name) {
            // loaded
            if (Store.moderatingAt.indexOf(Store.category.id) != -1) {
                next()
            }
        } else {
            // not loaded but let's continue (the server-side is still protecting us!)
            next()
        }
    },
};
</script>

<style>
    #reported-items .fond {
        padding-top: 7%;
    }
</style>
