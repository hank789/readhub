import {plusReady} from '../libs/plus';

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
                        splitLine: { //底部分割线
                            color: '#3c3e44'
                        }
                    },
                    bounce:'vertical'});


                plus.key.addEventListener("backbutton",() =>{
                    webviewBackButton();
                });




                embed.show();
                return embed;
            } else {
                window.open(url);
            }
    	},
        parentOpenUrl(url){
            var webview = plus.webview.getWebviewById(plus.runtime.appid);
            console.log('rootWebviewid:' + webview.id);
            webview.loadURL('/public/index.html#' + url);
        },
        hideWebviewFooter(){
            var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?
            if (isPlusReady){
                var currentPath = this.$route.path;

                plusReady(() => {
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
                            console.log('currentPath:'+currentPath);
                            if (currentPath.match(/webview$/)) {
                                return false;
                            }

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
                });

            }
        },

    }
};
