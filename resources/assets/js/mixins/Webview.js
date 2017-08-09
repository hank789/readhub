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
                        backgroundColor: '#f7f7f7', //导航栏背景色
                        titleText: title, //导航栏标题
                        titleColor: '#000000', //文字颜色
                        type: 'transparent', //透明渐变样式
                        autoBackButton: false, //自动绘制返回箭头
                        buttons: [
                            {
                                color: '#5e5e5e',
                                'float': 'left',
                                fontSize: '27px',
                                text: '\u1438',
                                onclick: webviewBackButton
                            },
                            {
                                color: '#5e5e5e',
                                'float': 'left',
                                fontSize: '27px',
                                text: '\u2606',
                                onclick: ()=> {
                                    console.log('bookmark');
                                    this.$emit('bookmark');
                                    plus.nativeUI.toast('收藏成功！');
                                }
                            },
                            {
                                color: '#5e5e5e',
                                'float': 'right',
                                fontSize: '27px',
                                text: '\u25B2',
                                onclick: ()=>{
                                    console.log('upvote');
                                    this.$emit('upvote');
                                    plus.nativeUI.toast('已赞！');
                                }
                            },
                            {
                                color: '#5e5e5e',
                                'float': 'right',
                                fontSize: '27px',
                                text: '\u25BC',
                                onclick: ()=> {
                                    console.log('downvote');
                                    this.$emit('downvote');
                                    plus.nativeUI.toast('已踩！');
                                }
                            },
                            ],
                        splitLine: { //底部分割线
                            color: '#cccccc'
                        }
                    },
                    bounce:'vertical'});

                embed.show();
            } else {
                window.open(url);
            }
    	}

    }
};
