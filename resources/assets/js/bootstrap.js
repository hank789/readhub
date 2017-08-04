window.moment = require('moment-timezone');
window.moment.tz.setDefault("Asia/Shanghai");
window.moment.locale('zh-cn');
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


axios.interceptors.response.use(function (response) {
	return response;
}, function (error) {
	if (error.response.status === 401) location.reload();

	return Promise.reject(error);
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import Echo from "laravel-echo";

if (Laravel.env == 'local') {
	window.Echo = new Echo({
	    broadcaster: 'pusher',
	    key: Laravel.pusherKey,
	    cluster: Laravel.pusherCluster
	});
} else {
	window.Echo = new Echo({
	    broadcaster: 'socket.io',
	    host: Laravel.echo_address,
	    auth:
	    {
	        headers:
	        {
	            'Authorization': 'Bearer ' + 'nb35mdq2ca9928qgl4sgjf3imil5811sn41qsmcaph0p3h6sa5ht8hoktdeg'
	        }
	    }
	});
}

// The rest of (non-NPM) packages
require('./libs/transition');
require('./libs/dropdown');
require('./libs/popup');
require('./libs/tooltip');
require('./libs/form');
require('./libs/checkbox');
require('./libs/Jcrop');


window.emojione = require('./libs/emojione.min');

//检查错误信息插件
import Raven from 'raven-js';
import RavenVue from 'raven-js/plugins/vue';

var sentry_url = 'https://6cd9e4811e7f4ade86ff3d4a18b28e19@sentry.io/167478';
if (Laravel.env === 'production') {
    sentry_url = 'https://0b85668d118943ffa71c7b6c34f0a6bf@sentry.io/175782';
}
Raven
    .config(sentry_url)
    .addPlugin(RavenVue, Vue)
    .install();