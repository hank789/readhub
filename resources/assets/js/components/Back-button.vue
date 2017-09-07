<template>
    <a class="back" @click="toggleBack" v-show="!noback">
        <svg class="icon-inwehub" aria-hidden="true">
            <use xlink:href="#icon-fanhui"></use>
        </svg>
    </a>
</template>

<script>

    import {plusReady} from '../libs/plus';

	export default {
        data () {
            return {
                noback:false
            }
        },
		mounted () {

		},
        created () {
		    if (this.$route.query.noback) {
                this.noback = true;
            }
        },
	    methods: {
	    	/**
	    	 * Toggles the sidebar
	    	 *
	    	 * @return void
	    	 */
            toggleBack() {
                var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?


                if (isPlusReady){
                    var currentPath = this.$route.path;

                    var from = this.$route.query.from;
                    if (from === 'webview') {
                        console.log('匹配到 webview ');
                        mui.back();
                    }

                    if (currentPath === '/h5') {
                        plusReady(() => {
                            var ws = plus.webview.currentWebview();
                            if (ws) {
                                //var parentWs = ws.parent();
                                //ws.parent().hide();
                            }
                        });
                    } else {
                        this.$router.push('/h5');
                    }

                } else {
                    if (this.$route.query.from !== undefined && this.$route.query.from === 'h5') {
                        window.history.go(-1);
                    }

                    this.$router.push('/h5');
                }
	    	}
	    }
	}
</script>
