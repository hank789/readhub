<template>
    <div class="photo-viewer-wrapper user-select">
        <div class="photo-viewer-header">
            <div class="flex-display">
                <div class="info desktop-only">
                    <h3>
                        {{ str_limit(list.title, 40) }}
                    </h3>

                    <small class="go-gray">
                        Submitted by
                        <router-link :to="'/' + '@' + list.owner.id">@{{ list.owner.username }}</router-link>
                        to
                        <router-link :to="'/c/' + list.category_name">#{{ list.category_name }}</router-link>
                        -
                        <router-link :to="'/c/' + list.category_name + '/' + list.slug">
                            {{ date }}
                        </router-link>
                    </small>
                </div>

                <div class="voting-wrapper">
                    <a class="fa-stack" @click="$emit('bookmark')"
    					data-toggle="tooltip" data-placement="bottom" title="Bookmark">
    					<i class="v-icon h-yellow" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
    				</a>

                    <a class="fa-stack align-right" @click="$emit('upvote')"
                        data-toggle="tooltip" data-placement="bottom" title="Upvote">
                        <i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
                    </a>

                    <div class="detail">
                        {{ points }} Points
                    </div>

                    <a class="fa-stack align-right" @click="$emit('downvote')"
                        data-toggle="tooltip" data-placement="bottom" title="Downvote">
                        <i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
                    </a>
                </div>
            </div>

            <div>
                <i class="v-icon pointer v-cancel margin-right-1" aria-hidden="true" @click="$emit('close')"
                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)"
                ></i>
            </div>
        </div>

        <div class="photo-wrapper" @click="$emit('close')">
            <video loop controls autoplay @click.stop.prevent="" :poster="list.data.thumbnail_path">
                <source :src="list.data.mp4_path" type="video/mp4">
            </video>
        </div>
    </div>
</template>

<script>
    import Helpers from '../mixins/Helpers'

    export default {
        mixins: [Helpers],

        props: [
            'points', 'upvoted', 'downvoted', 'bookmarked', 'list'
        ],

        computed: {
            date () {
                return moment(this.list.created_at).fromNow()
            },
        },

        created () {
            window.addEventListener('keyup', this.keyup)
        },

        mounted: function() {
            this.$nextTick(function() {
                this.$root.loadSemanticTooltip()
            })
        },

        methods: {
            prevent(e) {
                e.preventDefault()
            },

            /**
             * Catches the event fired for the pressed key, and runs the neccessary methods.
             *
             * @param keyup event
             * @return void
             */
            keyup(event){
                // esc
                if (event.keyCode == 27) {
                    this.$emit('close')
                }
            },

        }
    };
</script>
