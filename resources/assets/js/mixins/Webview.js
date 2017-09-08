import {plusReady} from '../libs/plus';

export default {
    data: function () {
        return {

        }
    },

    methods: {

        openWebviewByUrl(id, url, autoShow=true, aniShow='pop-in', popGesture='hide', reload = false) {
            mui.plusReady(function(){
                var webview = mui.openWindow({
                    url: url,
                    id: id,
                    preload: false,//一定要为false
                    show: {
                        autoShow: autoShow,
                        aniShow: aniShow
                    },
                    styles: {
                        popGesture: popGesture
                    },
                    extras:{preload: false},
                    waiting: {
                        autoShow: false
                    }
                });
                console.log("openWindow:"+webview.getURL());
            });

        },
    	openWebviewSubmission(url, title='', id, pathUrl, shareContent, shareImg, shareThumbUrl, commentJumpCallback)
    	{
            var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?

            if (mixpanel && mixpanel.track) {
                mixpanel.track(
                    'readhub:read-page-detail',
                    {
                        "app": "readhub","user_device": getUserAppDevice(), 'page': url, 'page_title': title
                    }
                );
            };

    		if (isPlusReady){

                mui.plusReady(() => {
                    var ws = plus.webview.currentWebview();
                    ws.addEventListener('show',createEmbed(ws),false);
                    function createEmbed(ws) {

                        var readhubUrl = window.location.protocol + '//' + window.location.host;

                        //绘制标题栏
                        var titleUrl = readhubUrl + '/share?title=' + encodeURIComponent(title);
                        console.log('webview-title:' + titleUrl);
                        var shareTitle = 'InweHub发现 | ' + title;
                        var shareId = 'webview_readhub_share_' + id;
                        var sharePathUrl = readhubUrl + '/h5?redirect_url=' + pathUrl;
                        var shareView = plus.webview.create(titleUrl, shareId, {
                            cachemode:'noCache',
                            popGesture: 'hide',
                            top:'0px',
                            right:'0px',
                            width:'100%',
                            height:'44px',
                            dock:'top',
                            position:'dock',
                            backButtonAutoControl: 'hide',
                            bounce:'none', //不允许滑动
                            scrollIndicator:'none', //不显示滚动条
                        }, {
                            title: shareTitle,
                            link: sharePathUrl,
                            content: shareContent,
                            imageUrl:shareImg,
                            thumbUrl:shareThumbUrl
                        });

                        ws.append(shareView);


                        //绘制body
                        var bodyTop = '0px';
                        var bodyBottom = '0px';
                        if (mui.os.android) {
                            bodyTop = '44px';
                            bodyBottom = '44px';
                        }
                        console.log('webview-body:' + url);
                        var webview = plus.webview.create(url, ws.id, {
                            popGesture: 'hide',
                            top:bodyTop,
                            bottom:bodyBottom,
                            position:'absolute',
                            backButtonAutoControl: 'hide',
                            statusbar:{background:'#3c3e44'},
                            bounce:'vertical'});
                        ws.append(webview);

                        //绘制底部菜单
                        var footerPathUrl = readhubUrl + pathUrl;
                        var toolUrl = footerPathUrl + '/webview';
                        console.log('webview-footer:' + toolUrl);
                        var toolUrlId = 'toolUrl_readhub_detail_son_' + id;
                        var embed =plus.webview.create(toolUrl, toolUrlId, {
                            popGesture: 'hide',
                            bottom:'0px',
                            height:'44px',
                            dock:'bottom',
                            position:'dock',
                            backButtonAutoControl: 'hide',
                            bounce:'none', //不允许滑动
                            scrollIndicator:'none', //不显示滚动条
                        });

                        //绘制底部链接
                        var view = new plus.nativeObj.View('test', {bottom:'0px',left:'0',height:'44px',width:'60%'});

                        view.draw([
                            {tag:'rect',id:'rect',rectStyles:{color:'rgba(0,0,0,0)'},position:{bottom:'0px',left:'0px',width:'100%',height:'44px'}},
                        ]);
                        view.addEventListener('click', commentJumpCallback, false);
                        embed.append(view);
                        ws.append(embed);
                    }

                });
            } else {
                window.open(url);
            }
    	},
        parentOpenUrl(url){
            var isPlusReady = navigator.userAgent.match(/Html5Plus/i); //TODO 5\+Browser?

            if (mixpanel && mixpanel.track) {
                mixpanel.track(
                    'readhub:readhub_to_inwehub',
                    {
                        'app': 'readhub',
                        "user_device": getUserAppDevice(),
                        'page': url
                    }
                );
            }
            if (isPlusReady){
                plusReady(() => {
                    var webview = plus.webview.getWebviewById(plus.runtime.appid);
                    console.log('rootWebviewid:' + webview.id);
                    webview.loadURL('/public/index.html#' + url);
                });
            } else {
                window.top.location.href = window.Laravel.inwehub_url + '/#' + url;
            }
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
                                popGesture: 'none',
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
