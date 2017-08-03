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
                var embed=plus.webview.create(url,'embed',{popGesture: 'hide',
                    top:topoffset,
                    bottom:'0px',
                    position:'dock',
                    dock:'bottom',
                    titleNView: {
                        backgroundColor: '#f7f7f7', //导航栏背景色
                        titleText: title, //导航栏标题
                        titleColor: '#000000', //文字颜色
                        type: 'transparent', //透明渐变样式
                        autoBackButton: true, //自动绘制返回箭头
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
