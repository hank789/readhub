export default {
    data: function () {
        return {

        }
    },

    methods: {

    	openWebviewUrl(url, title='')
    	{
            var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?

    		if (isPlusReady){
                function webviewBackButton(){
                    var ws = plus.webview.getWebviewById(url);
                    if (ws) {
                        ws.close();
                    }
                }

                console.log(plus.webview.currentWebview().id);

                var embed=plus.webview.create(url, url,{popGesture: 'hide',
                    top:'0px',
                    bottom:'0px',
                    position:'dock',
                    dock:'bottom',
                    backButtonAutoControl: 'hide',
                    titleNView: {
                        backgroundColor: '#3c3e44', //导航栏背景色
                        titleText: title, //导航栏标题
                        titleColor: '#fff', //文字颜色
                        type: 'transparent', //透明渐变样式
                        titleSize:'18px',
                        autoBackButton: true, //自动绘制返回箭头
                        // buttons: [
                        //     {
                        //         color: '#5e5e5e',
                        //         'float': 'left',
                        //         fontSize: '27px',
                        //         text: '\u1438',
                        //         onclick: webviewBackButton
                        //     },
                        //     {
                        //         color: '#5e5e5e',
                        //         'float': 'left',
                        //         fontSize: '27px',
                        //         text: '\u2606',
                        //         onclick: ()=> {
                        //             console.log('bookmark');
                        //             this.$emit('bookmark');
                        //             plus.nativeUI.toast('收藏成功');
                        //         }
                        //     },
                        //     {
                        //         color: '#5e5e5e',
                        //         'float': 'right',
                        //         fontSize: '27px',
                        //         text: '\u25B2',
                        //         onclick: ()=>{
                        //             console.log('upvote');
                        //             this.$emit('upvote');
                        //             plus.nativeUI.toast('已赞');
                        //         }
                        //     },
                        //     {
                        //         color: '#5e5e5e',
                        //         'float': 'right',
                        //         fontSize: '27px',
                        //         text: '\u25BC',
                        //         onclick: ()=> {
                        //             console.log('downvote');
                        //             this.$emit('downvote');
                        //             plus.nativeUI.toast('已踩');
                        //         }
                        //     },
                        //     ],
                        splitLine: { //底部分割线
                            color: '#3c3e44'
                        }
                    },
                    bounce:'vertical'});


                plus.key.addEventListener("backbutton",() =>{
                    webviewBackButton();
                });


                embed.show();
            } else {
                window.open(url);
            }
    	},

        hideWebviewFooter(){
            var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?
            if (isPlusReady){
                var currentPath = this.$route.path;
                var ws = plus.webview.currentWebview();
                if (currentPath === '/h5') {
                    if (ws) {
                        ws.setStyle({
                                popGesture: 'hide',
                                top: '0px',
                                dock: 'top',
                                bottom: '75px',
                                bounce:'none'
                        });
                    }
                } else {
                    if (ws) {
                        ws.setStyle({
                            popGesture: 'hide',
                            top: '0px',
                            dock: 'top',
                            bottom: '0px',
                            bounce:'none'
                        }
                    );
                    }
                }
            }
        }

    }
};
