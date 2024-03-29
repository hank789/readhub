<template>
    <section class="no-border" :class="isReply ? '' : 'full-comment-form'">
        <div class="content">
        	<div class="ui reply form flex-display">
                <textarea type="text" v-model="message" :id="'comment-form-' + parent" class="v-comment-form"
                          placeholder="添加回复..." autocomplete="off" rows="1" name="comment"
                          v-on:keydown.enter="submit($event)" v-focus="focused" @focus="focused = true"
                          @keydown="whisperTyping" @keyup="whisperFinishedTyping"
                ></textarea>

                <span v-if="false" class="send-button comment-emoji-button">
                    <i class="v-icon v-smile h-yellow" aria-hidden="true" v-if="!loading" @click="toggleEmojiPicker"></i>

                    <emoji-picker v-if="emojiPicker" @emoji="emoji" v-on-clickaway="closeEmojiPicker"></emoji-picker>
                </span>

                <button class="send-button" v-bind:class="{ 'go-green': showSubmit }" @click="submit($event)">
                    <i class="v-icon v-send" aria-hidden="true" v-if="!loading"></i>
		        	<moon-loader :loading="loading" :size="'25px'" :color="'#555'"></moon-loader>
                </button>
            </div>

            <div class="flex-space user-select comment-form-guide-wrapper" v-if="!isReply">
                <typing></typing>
	            <a v-if="false" class="comment-form-guide" @click="$eventHub.$emit('markdown-guide')">
	            	格式规范
	            </a>

            </div>
        </div>
    </section>
</template>

<script>
	import MoonLoader from '../components/MoonLoader.vue';
	import EmojiPicker from '../components/EmojiPicker.vue';
	import Typing from '../components/Typing.vue';
    import { mixin as clickaway } from 'vue-clickaway';
	import { focus } from 'vue-focus';
	import Helpers from '../mixins/Helpers';
	import 'jquery.caret';
	import 'at.js';

    export default {

		directives: { focus },

    	components: {
		    MoonLoader,
            EmojiPicker,
            Typing
        },

        props: ['parent', 'submission', 'editing', 'before', 'id'],

        mixins: [clickaway, Helpers],

        data: function () {
            return {
            	Store,
                focused: null,
                emojiPicker: false,
            	loading: false,
                message: '',
                temp: '',
                mentioning: false,
                EchoChannelAddress: 'submission.' + this.$route.params.slug,
                isTyping: false
            }
        },

        created() {
            this.setFocused();
            this.setEditing();
            this.subscribeToEcho();
        },

        computed: {
        	isReply() {
        		return this.parent != 0
        	},

        	showSubmit () {
        		return this.loading == false && this.message.trim() && (this.message.trim().lastIndexOf('@') + 1) !== this.message.trim().length;
            }
        },

		mounted: function () {
            this.atWho();

			this.$nextTick(function () {
        		this.$root.autoResize();
			});
		},

        methods: {
		    /**
             * Subscribes to the Echo channel. Prepares comment form for whispering "typing".
             *
             * @return void
             */
		    subscribeToEcho() {
                if (this.isGuest) return;

                Echo.private(this.EchoChannelAddress);
            },

		    /**
             * Broadcast "typing".
             *
             * @return void
             */
            whisperTyping() {
                if (this.isGuest) return;

                if (this.isTyping) return;

                if (this.editing) return;

                Echo.private(this.EchoChannelAddress).whisper('typing', {
                    username: auth.username
                });

                this.isTyping = true;
            },

            /**
             * Broadcast "finished-typing".
             *
             * @return void
             */
            whisperFinishedTyping: _.debounce(function () {
                if (this.isGuest) return;

                Echo.private(this.EchoChannelAddress).whisper('finished-typing', {
                    username: auth.username
                });

                this.isTyping = false;
            }, 600),

            /**
             * Loads the at.js stuff
             *
             * @return void
             */
            atWho() {
                $('#comment-form-' + this.parent).atwho({
                    at: "@",
                    delay: 300,
                    searchKey: "username",
                    insertTpl: "@${username}",
                    displayTpl: "<li><img src='${avatar}' height='20' width='20' />@${username}</li>",
                    callbacks: {
                        remoteFilter: function (query, callback) {
                            axios.get('/search-mentionables', {
                                params: {
                                    searched: query
                                }
                            }).then((response) => {
                                callback(response.data);
                            });
                        }
                    }
                });
            },

            setEditing() {
                if (this.editing) {
                    this.message = this.before;
                }
            },

        	emoji(shortname){
        		this.message = this.message + shortname + " "
        	},

            toggleEmojiPicker() {
                this.emojiPicker = ! this.emojiPicker;
            },

            closeEmojiPicker() {
                this.emojiPicker = false
            },

            setFocused(){
        	    // 防止挡住回复按钮
                this.$eventHub.$emit('scrolled-a-bit');
                if(this.parent == 0){
                    this.focused = false
                    return

                }

                this.focused = true;
            },


        	submit(event) {
                // ignore shift + enter
        		if(event.shiftKey) return;

        		// ignore if the mention suggestion box is open
                if ($("#atwho-ground-comment-form-" + this.parent + " .atwho-view").is(':visible')) return;

        		event.preventDefault();

        		var msg = $('#comment-form-' + this.parent).val();
        		if(!this.message.trim()) return;
                this.message = msg;

        		if ((this.message.trim().lastIndexOf('@') + 1) === this.message.trim().length) return;

                this.closeEmojiPicker();

            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.temp = this.message;
                this.focused = false;
        		this.message = '';

        		$('#comment-form-' + this.parent).css('height', 49);

        		this.loading = true;

                // edit
                if (this.editing) {
        		    if (this.temp == this.before) {
                        this.message = this.temp;
        		        this.loading = false;
                        this.$emit('patched-comment', this.temp)
        		        return;
                    }

                    axios.post('/edit-comment', {
                        comment_id: this.id,
                        body: this.temp
                    }).then((response) => {
                        this.loading = false;

                        this.$emit('patched-comment', this.temp);
                    }).catch((error) => {
        		        this.message = this.temp;

        		        this.loading = false;
                    });

                    return;
                }

                // new comment
        		axios.post( '/comment', {
                    parent_id: this.parent,
                    submission_id: this.submission,
                    body: this.temp,
                } ).then((response) => {
                	Store.commentUpVotes.push(response.data.id);

                    this.$eventHub.$emit('newComment', response.data);

        			this.loading = false;
                }).catch((error) => {
                    this.message = this.temp;

                    this.loading = false;
                });
        	},
        },

    }
</script>


<style>
    [data-name="null"] {
        display: none;
    }
</style>