import AdminPanelSuggestedCategories from './components/AdminPanelSuggestedCategories.vue';
import BookmarkedCategories from './components/BookmarkedCategories.vue';
import SubmissionRedirector from './components/SubmissionRedirector.vue';
import CategorySubmissions from './components/CategorySubmissions.vue';
import BookmarkedComments from './components/BookmarkedComments.vue';
import AdminPanelCategory from './components/AdminPanelCategory.vue';
import BookmarkedUsers from './components/BookmarkedUsers.vue';
import UserSubmissions from './components/UserSubmissions.vue';
import ModeratorPanel from './components/ModeratorPanel.vue';
import AdminPanelUser from './components/AdminPanelUser.vue';
import AdminPanelHelp from './components/AdminPanelHelp.vue';
import FindCategories from './components/FindCategories.vue';
import SubmissionPage from './components/SubmissionPage.vue';
import SubmissionPageWebview from './components/SubmissionPageWebview.vue';
import UserComments from './components/UserComments.vue';
import NewCategory from './components/NewCategory.vue';
import AdminPanel from './components/AdminPanel.vue';
import Bookmarks from './components/Bookmarks.vue';
import NotFound from './components/NotFound.vue';
import Settings from './components/Settings.vue';
import Help from './components/Help.vue';
import Home from './components/Home.vue';
import HomeBak from './components/Home.bak.vue';
import Share from './components/Share.vue';


import Submit from './components/Submit.vue';
import Category from './components/Category.vue';
import UserPage from './components/UserPage.vue';
import AdminPanelUsers from './components/AdminPanelUsers.vue';
import CategorySettings from './components/CategorySettings.vue';
import AdminPanelBanUser from './components/AdminPanelBanUser.vue';
import AdminPanelComments from './components/AdminPanelComments.vue';
import ModeratorPanelRules from './components/ModeratorPanelRules.vue';
import UserSettingsEditFeed from './components/UserSettingsEditFeed.vue';
import AdminPanelSubmissions from './components/AdminPanelSubmissions.vue';
import DeletedSubmissionPage from './components/DeletedSubmissionPage.vue';
import BookmarkedSubmissions from './components/BookmarkedSubmissions.vue';
import ModeratorPanelBanUsers from './components/ModeratorPanelBanUsers.vue';
import UserUpvotedSubmissions from './components/UserUpvotedSubmissions.vue';
import UserSettingsEditProfile from './components/UserSettingsEditProfile.vue';
import UserSettingsEditAccount from './components/UserSettingsEditAccount.vue';
import UserDownvotedSubmissions from './components/UserDownvotedSubmissions.vue';
import ModeratorPanelModerators from './components/ModeratorPanelModerators.vue';
import ModeratorPanelBlockDomains from './components/ModeratorPanelBlockDomains.vue';
import AdminPanelReportedComments from './components/AdminPanelReportedComments.vue';
import AdminPanelReportedSubmissions from './components/AdminPanelReportedSubmissions.vue';
import ModeratorPanelReportedComments from './components/ModeratorPanelReportedComments.vue';
import UserSettingsEditEmailAndPassword from './components/UserSettingsEditEmailAndPassword.vue';
import ModeratorPanelReportedSubmissions from './components/ModeratorPanelReportedSubmissions.vue';
import DetailH5 from './components/DetailH5.vue';
import SubscribedCategories from './components/SubscribedCategories.vue';

const routes = [
    // app上的路由begin
    {
        name: 'h5',
        path: '/h5',
        component: Home,
        meta: { title: '首页' }
    },
    // app上的路由end

    { // readhub
        name:'detailH5',
        path: '/readhub/detail',
        meta: { title: '文章详情页' },
        component: DetailH5
    },

    {
        name: 'home',
        path: '/',
        meta: { title: '首页' },
        component: HomeBak
    },

    { path: '/help', component: Help, meta: { title: 'Help Center' } },

    { path: '/channel', component: NewCategory, meta: { title: 'New #Channel' } },
    { path: '/subscribed-channels', component: SubscribedCategories, meta: { title: 'My Subscriptions' } },
    { path: '/submit', component: Submit, meta: { title: 'Submit' } },
    { path: '/big-daddy', redirect: '/big-daddy/reports/submissions' },
    {
        path: '/big-daddy',
        component: AdminPanel,
        children: [
            { path: 'help', component: AdminPanelHelp, name: 'admin-panel-help' },
            {
                path: '/users',
                component: AdminPanelUsers,
                name: 'admin-panel-users',
                children: [
                    { path: 'all', component: AdminPanelUser, name: 'admin-panel-all-users' },
                    { path: 'ban', component: AdminPanelBanUser, name: 'admin-panel-ban-users' },
                ]
            },
            { path: 'channels', component: AdminPanelCategory, name: 'admin-panel-categories' },
            { path: 'submissions', component: AdminPanelSubmissions, name: 'admin-panel-submissions' },
            { path: 'comments', component: AdminPanelComments, name: 'admin-panel-comments' },
            { path: 'reports/submissions', component: AdminPanelReportedSubmissions, name: 'admin-panel-reported-submissions' },
            { path: 'reports/comments', component: AdminPanelReportedComments, name: 'admin-panel-reported-comments' },
            { path: 'suggested-categories', component: AdminPanelSuggestedCategories, name: 'admin-panel-suggested-categories' },
        ]
    },

    { path: '/@:username/settings', redirect: '/@:username/settings/account' },
    {
        path: '/@:username',
        component: UserPage,
        children: [
            { path: '', component: UserSubmissions, name: 'user-submissions', meta: { title: '我的发布' }},
            {
                 path: 'settings',
                 component: Settings,
                 name: 'user-settings',
                 children: [
                     { path: 'account', component: UserSettingsEditAccount, name: 'user-settings-account' },
                     { path: 'profile', component: UserSettingsEditProfile, name: 'user-settings-profile' },
                     { path: 'feed', component: UserSettingsEditFeed, name: 'user-settings-feed' },
                     { path: 'email-and-password', component: UserSettingsEditEmailAndPassword, name: 'user-settings-email-and-password' },
                 ]
             },
            { path: 'comments', component: UserComments, name: 'user-comments',meta: { title: '我的回复' } },
            { path: 'upvoted-submissions', component: UserUpvotedSubmissions, name: 'user-upvoted-submissions',meta: { title: '我的点赞' } },
            { path: 'downvoted-submissions', component: UserDownvotedSubmissions, name: 'user-downvoted-submissions',meta: { title: '我的踩' } },
        ]
    },

    { path: '/c/:name/mod/reports', redirect: '/c/:name/mod/reports/submissions' },
    { path: '/c/:name/mod', redirect: '/c/:name/mod/reports/submissions' },
    {
        path: '/c/:name',
        component: Category,
        children: [
        	{ path: '', component: CategorySubmissions, name: 'category-submissions' },
            {
                path: 'mod',
                component: ModeratorPanel,
                children: [
                    {
                        path: 'reports/submissions',
                        name: 'moderator-panel-reported-submissions',
                        component: ModeratorPanelReportedSubmissions,
                        meta: { title: 'Reports | Submissions' },
                    },
                    {
                        path: 'reports/comments',
                        name: 'moderator-panel-reported-comments',
                        component: ModeratorPanelReportedComments,
                        meta: { title: 'Reports | Comments' },
                    },
                    { path: 'ban-users', name: 'moderator-panel-ban-users', component: ModeratorPanelBanUsers, meta: { title: 'Ban Users | Moderator Panel' } },
                    { path: 'block-domains', name: 'moderator-panel-block-domains', component: ModeratorPanelBlockDomains, meta: { title: 'Block Domains | Moderator Panel' } },
                    { path: 'rules', name: 'moderator-panel-rules', component: ModeratorPanelRules, meta: { title: 'Rules | Moderator Panel' } },
                    { path: 'manage-moderators', name: 'moderator-panel-manage-moderators', component: ModeratorPanelModerators, meta: { title: 'Manage Moderators | Moderator Panel' } },
                    { path: 'settings', name: 'category-settings', component: CategorySettings, meta: { title: 'Settings | Moderator Panel' } },
                ]
            },
        ]
    },



    { path: '/deleted-submission', component: DeletedSubmissionPage },
    { path: '/submission/:id', component: SubmissionRedirector },
    { path: '/share', component: Share },
    { path: '/find-channels', component: FindCategories, name: 'find-categories', meta: { title: 'Find Channels' } },
    { path: '/404', component: NotFound, name: 'not-found', meta: { title: 'Not Found' } },
    { path: '/c/:name/:slug', component: SubmissionPage, name: 'submission-page', meta: { title: '文章详情-评论页' } },
    { path: '/c/:name/:slug/webview', component: SubmissionPageWebview, name: 'submission-page-webview', meta: { title: '文章详情' } },

    { path: '/bookmarks', redirect: '/bookmarks/submissions' },
    {
        name: 'bookmarks',
        path: '/bookmarks',
        component: Bookmarks,
        children: [
            { path: 'submissions', component: BookmarkedSubmissions, name: 'bookmarked-submissions', meta: { title: 'Submissions | Bookmarks' } },
            { path: 'comments', component: BookmarkedComments, name: 'bookmarked-comments', meta: { title: 'Comments | Bookmarks' } },
            { path: 'channels', component: BookmarkedCategories, name: 'bookmarked-categories', meta: { title: 'Channels | Bookmarks' } },
            { path: 'users', component: BookmarkedUsers, name: 'bookmarked-users', meta: { title: 'Users | Bookmarks' } },
        ]
    },
    { path: '/home', redirect: '/' },
    { path: '*', component: NotFound, meta: { title: 'Not Found' } },
]

import VueRouter from 'vue-router'
Vue.use(VueRouter);

var router = new VueRouter({
    mode: 'history',
    routes,
    scrollBehavior (to, from, savedPosition) {
        return {x: 0, y:0}
    }
})


/**
 * Fills the <title> tag with the right value before navigating to the
 * new route.First it checks the title in the meta, if it exists it
 * sets it to that, otherwise sets it to the default (voten).
 */
router.beforeEach((to, from, next) => {
    if (to.meta.title) {
        document.title = to.meta.title
    } else {
    	if (
	    		to.name != "submission-page" &&
	    		to.name != "category" &&
	    		to.name != "home" &&
	    		to.name != "user-submissions" &&
	    		to.name != "user-comments"
    		) {
    		document.title = 'Readhub'
    	}
    }

    next()
})


/**
 * Since Google Analytics's default tracking code doesn't play nice with
 * single-page-applications, we're gonna use this one. What it does
 * is simple running after after navigating to the new routes.
 */
router.afterEach((to, from) => {
    console.log(to.meta.title);
	if (Laravel.env == 'production') {
		(function(i, s, o, g, r, a, m) {
	       i['GoogleAnalyticsObject'] = r;
	       i[r] = i[r] || function() {
	           (i[r].q = i[r].q || []).push(arguments)
	       }, i[r].l = 1 * new Date();
	       a = s.createElement(o),
	           m = s.getElementsByTagName(o)[0];
	       a.async = 1;
	       a.src = g;
	       m.parentNode.insertBefore(a, m)
	   })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

	   ga('create', 'UA-97476315-1', 'auto');

	   ga('set', 'page', to.fullPath);
	   ga('send', 'pageview');

        if (mixpanel && mixpanel.track) {
            var mixpanel_event = 'readhub:';
            if (to.name) {
                mixpanel_event += to.name;
            } else {
                mixpanel_event += to.fullPath;
            }
            mixpanel.track(
                mixpanel_event,
               {"app": "readhub","user_device": getUserAppDevice(), "page": to.fullPath, "page_name": to.name, "page_title": to.meta.title, "referrer_page": from.fullPath}
           );
        }
	}
})

export default router;
