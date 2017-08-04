<template>
    <button type="button" class="btn-nth" aria-haspopup="true"
            data-toggle="tooltip" data-placement="bottom" aria-expanded="false" @click="toggleBack">
        <i class="v-icon v-previous" aria-hidden="true"></i>
    </button>
</template>

<script>
	export default {
		mounted () {

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
                    var ws = plus.webview.currentWebview();
                    if (ws) {
                        var parentWs = ws.parent();
                        ws.parent().hide();
                    }
                } else {
                    this.$router.go(-1);
                }
	    	}
	    }
	}
</script>
