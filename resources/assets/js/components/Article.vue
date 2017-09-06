<template>
    <div>

    </div>
</template>


<script>
    import Webview from '../mixins/Webview';
    export default {
        mixins: [Webview],
        data: () => ({
            loading:1
        }),
        created () {

        },
        methods: {

        },
        watch: {

        },
        mounted(){
            var self = this;

            mui.plusReady(() => {
                var ws = plus.webview.currentWebview();

                var jumpToComment = () => {
                    console.log('准备跳转3:'+ws.article_comment_url);
                    var footerPathUrl =  window.location.protocol + '//' + window.location.host +  ws.article_comment_url;

                    var webview = mui.openWindow({
                        url: footerPathUrl + '?from=webview',
                        id: 'read_comment_link_son_' + ws.id,
                        preload: false,//一定要为false
                        show: {
                            autoShow: true,
                            aniShow: 'pop-in'
                        },
                        styles: {
                            popGesture: 'hide'
                        },
                        extras:{preload: false},
                        waiting: {
                            autoShow: false
                        }
                    });
                };

                var shareContent = '来自「 频道」，这里有特别的评论，点击去看看或者参与互动？';
                this.openWebviewSubmission(
                    ws.article_url,
                    ws.article_title,
                    ws.id,
                    ws.article_comment_url,
                    shareContent,
                    ws.article_img_url,
                    ws.article_img_url + '?x-oss-process=image/resize,h_100,w_100',
                    jumpToComment
                );
            });
        }
    }

</script>
