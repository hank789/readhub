import KeyboardShortcutsGuide from './components/KeyboardShortcutsGuide.vue';
import ReportSubmission from './components/ReportSubmission.vue';
import ReportTableItem from './components/ReportTableItem.vue';
import CategoryAvatar from './components/CategoryAvatar.vue';
import ReportComment from './components/ReportComment.vue';
import Notifications from './components/Notifications.vue';
import MarkdownGuide from './components/MarkdownGuide.vue';
import Subscribe from './components/Subscribe-button.vue';
import VuiMenuButton from './components/Menu-button.vue';
import VuiBackButton from './components/Back-button.vue';
import GuestSidebar from './components/GuestSidebar.vue';
import SearchModal from './components/SearchModal.vue';
import ScrollButton from './components/ScrollButton.vue';
import WebNotification from './mixins/WebNotification';
import AvatarEdit from './components/AvatarEdit.vue';
import Moderators from './components/Moderators.vue';
import LoginModal from './components/LoginModal.vue';
import CropModal from './components/CropModal.vue';
import Dashboard from './components/Dashboard.vue';
import NotFound from './components/NotFound.vue';
import Messages from './components/Messages.vue';
import LocalStorage from './mixins/LocalStorage';
import StoreStorage from './mixins/StoreStorage';
import Sidebar from './components/Sidebar.vue';
import Rules from './components/Rules.vue';
import Helpers from './mixins/Helpers';
import autosize from 'autosize';
import router from './routes';
import Webview from './mixins/Webview';
import { swiper, swiperSlide } from 'vue-awesome-swiper';



/**
 * This is our event bus, which is used for event dispatching. The base is that we create an empty
 * Vue instance. First we fire the event by: "this.$eventHub.$emit('eventName', 'data')"
 * and later we listen for it by: "this.$eventHub.$on('eventName', this.newComment)"
 *
 *
 * (which is defined in the created() function of the vue componentr (or root instance), after catching the
 * event, passes the data to the defined funciton. In this example case it's newComment() but notice that
 * it doesn't require to be actually written as argumans! ) Happy eventing in your awesome components.
 */
Vue.prototype.$eventHub = new Vue();


/**
 * A great wrapper for using LocalStorage which we take advantage of a looot!
 */
import VueLocalStorage from 'vue-ls';
const localStorageConfig = {
    namespace: auth.username + auth.id + '__voten__'
};
Vue.use(VueLocalStorage, localStorageConfig);

import infiniteScroll from 'vue-infinite-scroll'
Vue.use(infiniteScroll);

/**
 * The very serious and important vue instance!!! This is what gives power to voten's
 * front-end. Try to love it, maintain it, appriciate it and maybe even more! This
 * also plays a role in switching states and maintaining the Store.
 */
const app = new Vue({
    router,

    mixins: [Helpers, Webview, LocalStorage, StoreStorage, WebNotification],

    components: {
    	KeyboardShortcutsGuide,
        ReportSubmission,
        ReportTableItem,
        CategoryAvatar,
    	MarkdownGuide,
        ReportComment,
        Notifications,
        VuiMenuButton,
        VuiBackButton,
        ScrollButton,
        GuestSidebar,
        SearchModal,
        LoginModal,
        AvatarEdit,
        Moderators,
        CropModal,
        Subscribe,
        Dashboard,
        NotFound,
        Messages,
        Sidebar,
        Rules,
        swiper,
        swiperSlide
    },

    data: {
        showSwipper:false,
        modalRouter: '',
        reportCategory: '',
        reportSubmissionId: '',
        reportCommentId: '',
        sidebar: true,
        sortFilter: 'hot',
        pageTitle: document.title,
        scrolledBusy: false,
        swiperOption: {
            slidesPerView: 3,
            spaceBetween: 10
        }
    },

    computed: {
        title(){
            if (/@/.test(this.$route.path)) {
                return '我的发布';
            }

            switch(this.$route.name) {
                case 'bookmarked-submissions':
                    return '我的收藏';
                    break;
                case 'user-submissions':
                    return '发布';
                    break;
                case 'submission-page':
                    return '评论';
                    break;
                default:
                    return '发现';
            }

        },
        smallModal() {
            return this.modalRouter != '';
        },

        unreadNotifications() {
            return Store.notifications.filter(function(item) {
                return item.read_at == null;
            }).length;
        },

        sort() {
            if (this.$route.query.sort == 'new')
                return 'new';

            if (this.$route.query.sort == 'rising')
                return 'rising';

            return 'hot';
        },

        unreadMessages() {
            return Store.contacts.filter(function(item) {
                return item.last_message.owner.id != auth.id && item.last_message.read_at == null;
            }).length;
        },
    },


    watch: {
        '$route' () {
            this.closeModals();
            this.$eventHub.$emit('scrolled-a-bit');

            if (auth.isMobileDevice) {
            	this.sidebar = false;
            }

            if (this.$route.query.sidebar == 1) {
                this.sidebar = true;
            }
            this.hideWebviewFooter();
        },
    },


    created: function() {
        this.swiperOption = {
            slidesPerView: 3,
            spaceBetween: 10,
            loop:true,
            onTap:(swiper) => {
                this.categoryMenuClick(swiper.clickedIndex + 6);
            }
        };

        window.addEventListener('keydown', this.keydown);
        this.hideWebviewFooter();
        this.fillBasicStore();

        this.setSidebar();

        // Let's hear it for the events, shall we?
        this.$eventHub.$on('start-conversation', this.startConversation);
        this.$eventHub.$on('report-submission', this.reportSubmission);
        this.$eventHub.$on('toggle-sidebar', this.toggleSidebar);
        this.$eventHub.$on('new-route', this.newRoute);
        this.$eventHub.$on('close', this.closeModals);
        this.$eventHub.$on('new-modal', this.newModal);
        this.$eventHub.$on('rules', this.categoryRules);
        this.$eventHub.$on('login-modal', this.loginModal);
        this.$eventHub.$on('category-sort', this.categorySort);
        this.$eventHub.$on('report-comment', this.reportComment);
        this.$eventHub.$on('moderators', this.categoryModerators);
        this.$eventHub.$on('markdown-guide', this.openMarkdownGuide);
        this.$eventHub.$on('crop-user-photo', this.cropUserModal);
        this.$eventHub.$on('push-notification', this.pushNotification)
        this.$eventHub.$on('crop-category-photo', this.cropCategoryModal);
        this.$eventHub.$on('mark-notifications-read', this.markAllNotificationsAsRead);

        this.$eventHub.$on('refreshBasicStore', this.fillBasicStore);
        this.$eventHub.$on('updateTitle', this.updateTitle);
        this.$eventHub.$on('getUnreadNotifications', this.getUnreadNotifications);


        if (this.$route.query.search) {
            this.changeRoute('search');
        }

    },

    mounted() {
        this.showSwipper = true;
        this.$nextTick(function() {
            this.loadCheckBox();
            this.loadSemanticTooltip();
            this.loadSemanticDropdown();
        });
        //监听自定义事件，前往页面
        document.addEventListener('go_to_readhub_page', (event) => {
            var url = event.detail.url;
            this.$router.push(url);
        });

    },

    methods: {
        categoryMenuClick(index){
            var callback = (response) => {
                switch(index) {
                    case 2:
                    case 8:
                        this.parentOpenUrl('/home/ActiveList');
                        break;
                    case 3:
                        this.parentOpenUrl('/home/OpportunityList');
                        break;
                    case 1:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        mui.alert('debug');
                        break;

                }

            };
            axios.get(this.authUrl('check-user-level'), {
                params: {
                    permission_type: index
                }
            }).then((response) => {
                callback(response);
                console.log(response);
            })
        },
        refresh() {
            this.$eventHub.$emit('refresh-home');
        },
        goLink(url) {
            setTimeout(() => {
                this.$router.push(url);
            }, 100);
        },
        share(){
            setTimeout(() => {
                mui('#shareWrapper').popover('toggle');
            }, 150);
        },
        getUnreadNotifications(){
            return this.getUnreadNotifications;
        },
        openMarkdownGuide() {
            this.changeModalRoute('markdown-guide')
        },

        /**
         * Catches the notification event and passes it in case it should.
         *
         * @param {Object} data
         * @return void
         */
        pushNotification(data) {
            this.webNotification(data.title, data.body, data.url, data.icon);
        },

        /**
         * Catches the scroll event and fires the neccessary ones for componenets. (Such as Inifinite Scrolling)
         *
         * @return void
         */
        scrolled(event) {
        	this.$eventHub.$emit('scrolled');

            let box = event.target;

            if ((box.scrollHeight - box.scrollTop) < (box.clientHeight + 100)) {
                this.$eventHub.$emit('scrolled-to-bottom');
            }

            if (box.scrollTop < 1500) {
                this.$eventHub.$emit('scrolled-a-bit');
            } else {
                this.$eventHub.$emit('scrolled-a-lot');
            }
            
        },
        scrolledToBottom() {
            this.scrolledBusy = true;
            this.$eventHub.$emit('scrolled-to-bottom');
            var appContent = document.querySelector('#v-content');
            if (appContent.scrollTop < 700) {
                this.$eventHub.$emit('scrolled-a-bit');
            } else {
                this.$eventHub.$emit('scrolled-a-lot');
            }
            this.scrolledBusy = false;
        },

        /**
         * Fetches the info about the user which we need later
         *
         * @return void
         * @param {String} username
         */
        getUserStore() {
        	// if landed on the user page as guest
        	if (preload.user) {
        		this.submissions = preload.user;

				Store.user = preload.user

                if (Store.user.id == auth.id) {
                	auth.stats = Store.user.stats
                }

				// clear the preload
				delete preload.user;

				return;
        	}

            axios.get('/get-user-store', {
            	params: {
            		username: this.$route.params.username
            	}
            }).then((response) => {
                Store.user = response.data

                if (Store.user.id == auth.id) {
                	auth.stats = Store.user.stats
                    auth.avatar = Store.user.avatar
                    auth.username = Store.user.username
                }
            }).catch((error) => {
                if (error.response.status === 404) {
                    this.$router.push('/404')
                }
            });
        },

        /**
         * navigates to home route. aaaand bit more in case the current route IS "home"
         *
         * @return void
         */
        homeRoute() {
            this.closeModals();

            if (this.$route.name === 'home') {
            	this.$eventHub.$emit('refresh-home');
            }
        },

        /**
         * Fetches the info about the category which we need later
         *
         * @return void
         * @param string name
         */
        getCategoryStore(name) {
        	// if landed on a submission page
        	if (preload.category && preload.category.id == this.$route.params.name) {
        		Store.category = preload.category;
        		delete preload.category;
        		return;
        	}

            axios.get('/get-category-store', {
            	params: {
            		name: name
            	}
            }).then((response) => {
                Store.category = response.data

                // update the category in the user's subscriptions (avatar might have changed)
                let category_id = Store.category.id
                function findObject(ob) {
	                return ob.id === category_id
	            }
	            let i = Store.subscribedCategories.findIndex(findObject)

	            if (i != -1 && Store.subscribedCategories[i].avatar != response.data.avatar) {
	            	Store.subscribedCategories[i].avatar = response.data.avatar
	            	this.putLS('subscribedCategories', Store.subscribedCategories)
	            }

	            // update the category in the user's moderating (avatar might have changed)
	            i = Store.moderatingCategories.findIndex(findObject)

	            if ( i != -1 && Store.moderatingCategories[i].avatar != response.data.avatar) {
	            	Store.moderatingCategories[i].avatar = response.data.avatar
	            	this.putLS('moderatingCategories', Store.moderatingCategories)
	            }
            }).catch((error) => {
                if (error.response.status === 404) {
                    this.$router.push('/404')
                }
            });
        },

        /**
         * Runned at the page load, sets the default valie for this.sidebar
         *
         * @return Boolean
         */
        setSidebar() {
    		if (this.$route.query.sidebar == 0) {
   				this.sidebar = false;
   				return;
   			}

            if (this.$route.query.sidebar == 1) {
   				this.sidebar = true;
   				return;
   			}

   			if (auth.isMobileDevice) {
   				this.sidebar = false;
   				return;
   			}

   			if (this.isSetLS('sidebar')) {
   				this.sidebar = this.getLS('sidebar');
   				return;
   			}
    	},

    	/**
         * Hide/Show the sidebar
         *
         * @return void
         */
        toggleSidebar () {
        	this.sidebar = !this.sidebar
        	this.putLS('sidebar', this.sidebar)
        },

        /**
         * Loads Semantic UI's dropdown components. Sending an ID would make this a lot faster
         *
         * @return void
         */
        loadSemanticDropdown (targetID = 'someID') {
        	if (targetID != 'someID') {
        		$('#' + targetID +' .ui.dropdown').dropdown({ duration: 50 });

        		return;
        	}

            $('.ui.dropdown').dropdown({ duration: 50 });
        },

        /**
         * Loads Semantic UI's Tooltip component
         *
         * @return void
         */
        loadSemanticTooltip() {
            $('[data-toggle="tooltip"]').tooltip({ trigger: "hover", delay: { show: 500, hide: 100 } })
        },

        /**
         * Loads Semantic UI's Tooltip component
         *
         * @return void
         */
        loadSemanticPopup() {
            $('.s-popup').popup({ inline: true });
        },

        /**
         * Loads the Semantic UI's CheckBox component
         *
         * @return void
         */
        loadCheckBox() {
            $('.ui.checkbox').checkbox()
        },

        /**
         * Auto resizes the text area. To make it work we need to call it by :
         * "this.$root.autoResize()"in the ready(){} method of the component
         *
         * @return void
         */
        autoResize() {
            autosize(document.querySelectorAll('textarea'));
        },

        /**
         * Opens the messages component and starts the conversation with the sent user.
         *
         * @return void
         */
        startConversation(contact) {
            this.changeRoute('messages')
            this.$eventHub.$emit('conversation', contact)
        },

        /**
         * Opens the comment-reporting modal
         *
         * @return void
         */
        reportComment(id, category) {
            this.closeModals()
            this.reportCommentId = id
            this.reportCategory = category
            this.modalRouter = 'report-comment'
        },

        /**
         * Opens that submission-reporting modal
         *
         * @return void
         */
        reportSubmission(id, category) {
            this.closeModals()
            this.reportSubmissionId = id
            this.reportCategory = category
            this.modalRouter = 'report-submission'
        },

        /**
         * Opens that user avatar crop modal
         *
         * @return void
         */
        cropUserModal() {
            this.closeModals()
            this.modalRouter = 'crop-user'
        },

        /**
         * Opens that category avatar crop modal
         *
         * @return void
         */
        cropCategoryModal() {
            this.closeModals()
            this.modalRouter = 'crop-category'
        },

        /**
         * Switches the modalRouter
         *
         * @return void
         */
        newModal(route) { this.modalRouter = route },

        /**
         * Switches the to the dispatched route (without any checking)
         *
         * @return void
         */
        newRoute(route) { Store.contentRouter = route },

        /**
         * Sets the default sort type in a category
         *
         * @return void
         */
        categorySort(sort) { this.sortFilter = sort },

        /**
         * Updates the <title> by adding the number of notifications and messages
         *
         * @return void
         */
        updatePageTitle() {
            // disable for now
            return;

            let total = this.unreadMessages + this.unreadNotifications;

            if (total > 0) {
                document.title = '(' + total + ') ' + this.pageTitle;
                return;
            }

            document.title = this.pageTitle;
        },
        updateTitle(title){
          this.title = title;
        },

        /**
         * Switches the contentRouter.
         *
         * @param  string
         * @return void
         */
        changeRoute(newRoute) {
            Store.contentRouter = newRoute;

            if (newRoute === 'notifications') {
                this.seenAllNotifications();
            }

            this.updatePageTitle();
        },

        /**
         * Marks all user notifications as read
         *
         * @return void
         */
        markAllNotificationsAsRead() {
            axios.post('/mark-notifications-read');

            Store.notifications.forEach(function(element, index) {
                if (!element.read_at) {
                    element.read_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
                }
            });
        },

        seenAllNotifications() {
            this.markAllNotificationsAsRead();
            Vue.ls.set('event', 'mark-notifications-read', 60 * 60 * 1000);
        },

        /**
         * Switches the route
         *
         * @param {String} newRoute
         * @return void
         */
        changeModalRoute(newRoute) {
            this.closeModals()
            this.modalRouter = newRoute
        },

        // Used for keyup.esc
        closeModals() {
            Store.contentRouter = 'content'
            this.modalRouter = ''
        },

        // Displays a smallModal containing category rules
        categoryRules() {
            this.modalRouter = 'rules'
        },

        // Displays the login modal
        loginModal() {
            // var isWeixin = this.isWechat();
            // if (isWeixin) {
            //     // var pathInfo = window.location.pathname;
            //     // window.Laravel.inwehub_api_url = 'https://api.ywhub.com';
            //     // var url = window.Laravel.inwehub_api_url + '/wechat/oauth?redirect=/discover?redirect_url=' + pathInfo;
            //     console.log('准备跳转' + window.Laravel.deep_mlink);
            //     window.location.href=window.Laravel.deep_mlink;
            // } else {
            //     this.modalRouter = 'login';
            // }

            console.log('准备跳转' + window.Laravel.deep_mlink);
            if (window.Laravel.deep_mlink) {
                window.location.href=window.Laravel.deep_mlink;
            }
        },

        // Displays a smallModal containing category moderators
        categoryModerators() {
            this.modalRouter = 'moderators'
        },

        // Tells the component (that contains submissions) to change the sort method.
        sortBy(sort, event) {
            if (this.sortFilter == sort) {
                return
            }

            event.preventDefault()
            this.sortFilter = sort
            this.$eventHub.$emit('sort-by', sort)
        },

        isWechat(){
            var wechat = navigator.userAgent.match(/(MicroMessenger)\/([\d\.]+)/i);
            if (wechat) {
                return true;
            }
            return false;
        },

        /**
         * Catches the event fired for the pressed key, and runs the neccessary methods.
         *
         * @param {keydown} event
         * @return void
         */
        keydown(event){
            // esc
            if (event.keyCode == 27) {
                this.closeModals()
            }

            // all shortcuts after this one need to be prevented if user is typing
            if (this.isTyping(event)) return

            // alt + s == event.altKey && event.keyCode == 83
        	if (event.altKey && event.keyCode == 83) { // alt + s
        		this.$router.push('/submit')
        		return
        	}

        	if (event.altKey && event.keyCode == 67) { // alt + c
        		this.$router.push('/channel')
        		return
        	}

        	if(event.shiftKey && event.keyCode == 191){ // shift + /
                this.changeModalRoute('keyboard-shortcuts-guide');
                return;
            }

        	switch(event.keyCode) {
                case 83: // "s"
			        this.toggleSidebar()
			        break
			    case 78: // "n"
			    	if (this.isGuest) break;

			        this.changeRoute('notifications')
			        break
		        case 77: // "m"
		        	if (this.isGuest) break;

			        this.changeRoute('messages')
			        break
    	        case 191: // "/"
    	        	event.preventDefault()
			        this.changeRoute('search')
			        break
    	        case 66: // "b"
    	        	if (this.isGuest) break;

			        this.$router.push('/bookmarks')
			        break
    	        case 72: // "h"
			        this.$router.push('/')
			        break
    	        case 80: // "p"
                    if (this.isGuest) break

			        this.$router.push('/@' + this.auth.id)
			        break
    	        case 82: // "r"
                    if (this.$route.name === 'home'){
                        this.$eventHub.$emit('refresh-home')
                    }else if(this.$route.name === 'category-submissions'){
                        this.$eventHub.$emit('refresh-category-submissions')
                    }

			        break
			    default:
			        return
			}
        },
    },
}).$mount('#voten-app');
