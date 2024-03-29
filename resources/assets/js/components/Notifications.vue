<template>
    <div class="v-modal">
        <div class="v-close" @click="close"
        data-toggle="tooltip" data-placement="bottom" title="Close (esc)">
            <i class="v-icon block-before v-cancel" aria-hidden="true"></i>
        </div>

        <div class="v-modal-title">
            <h1 class="title">
                通知
            </h1>
        </div>

        <div class="container background-white">
	    	<div class="v-push-9"></div>

            <div class="col-7">
                <div class="user-select v-nth-box" v-if=" !Store.notifications || ! Store.notifications.length">
                    <i class="v-icon v-bell icon-block-big" aria-hidden="true"></i>
                    <h3>
                    	无未读通知
                    </h3>
                </div>

                <ul class="v-contact-list user-select">
                	<notification v-for="n in uniqueList" :notification="n" :key="n.id"></notification>
                </ul>

                <div class="align-center">
                    <button type="button" class="user-select v-button v-button--green margin-top-bottom-3"
                    @click="loadReadNotifications" v-show="loadMoreButton">
                        显示之前的通知
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Notification from '../components/Notification.vue';
    import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

    	components: { Notification },

        data: function () {
            return {
                page: 1,
                loadMoreButton: false,
                nightMode: false
            }
        },

        created: function() {
            this.getNotifications();
        	this.listen();
        },


        computed: {
            /**
             * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
             * timestamps) we need a different approch to make sure the list is always unique.
             * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
             *
             * @return array
             */
            uniqueList() {
                let unique = [];
                let temp = [];

                if(Store.notifications) {
                    Store.notifications.forEach(function(element, index, self) {
                        if (temp.indexOf(element.id) === -1) {
                            unique.push(element);
                            temp.push(element.id);
                        }
                    })
                }

                return unique;
            }
        },


        methods: {
        	/**
        	 * Fires the 'close' event which causes all the modals to be closed.
        	 *
        	 * @return void
        	 */
        	close() {
        		this.$eventHub.$emit('close')
        	},

            /**
             * Loads all the unread notifications of the Auth user.
             *
             * @return void
             */
            getNotifications() {
                axios.get('/notifications').then((response) => {
                    if (response.data.length > 0) {
                        Store.notifications = response.data;
                    }

                    this.loadMoreButton = true;
                })
            },

            /**
             * loads read notifications of the Auth user.
             *
             * @return void
             */
            loadReadNotifications() {
                this.loadMoreButton = false

                axios.post('/all-notifications', { page: this.page }).then((response) => {
                    Store.notifications.push(...response.data.data);

                    this.page ++;

                    if(response.data.next_page_url) {
                        this.loadMoreButton = true;
                    }
                })
            },

            /**
             * listen for broadcasted notifications
             *
             * @return void
             */
            listen() {
                Echo.private('App.User.' + auth.id)
                .notification((n) => {
                	// lable it
                	n.broadcasted = true;

                    Store.notifications.unshift(n)

                    // give user the new recieved access (so a refresh won't be needed)
                    if (n.type == 'App\\Notifications\\BecameModerator') {
                        if (n.data.role == "moderator") {
                            Store.moderatorAt.push(n.data.category.id)
                        } else if (n.data.role == "administrator") {
                            Store.administratorAt.push(n.data.category.id)
                        }

                        Store.moderatingAt.push(n.data.category.id)
                        Store.moderatingCategories.push(n.data.category)
                    }

                    // Sending web notifications to user's OS (only if browser tab is not active)
                    if(document.hidden == true) {
                        let body = n.data.body
                        let link = n.data.url
                        let avatar = n.data.avatar

                        let title = 'Now Notification'

                        if(n.type == 'App\\Notifications\\CommentReplied'){
                            title = 'New Reply'
                        } else if (n.type == 'App\\Notifications\\SubmissionReplied') {
                            title = 'New Comment'
                        } else if (n.type == 'App\\Notifications\\BecameModerator') {
                            title = 'Now Moderating'
                        } else if (n.type == 'App\\Notifications\\CommentReported') {
                            title = 'New Report'
                        } else if (n.type == 'App\\Notifications\\SubmissionReported') {
                            title = 'New Report'
                        }

                        const data = {
                            title: title,
                            body: body,
                            url: link,
                            icon: avatar
                        }

        				this.$eventHub.$emit('push-notification', data)
                    }
                })
            },
        },
    }
</script>
