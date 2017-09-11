<template>
  <div>
    <header class="mui-bar mui-bar-nav">
      <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
      <h1 class="mui-title">{{ title }}</h1>

      <a class="mui-icon mui-pull-right" @tap.stop.prevent="share()">
        <svg class="icon icon-inwehub" aria-hidden="true">
          <use xlink:href="#icon-fenxiang"></use>
        </svg>
      </a>
    </header>

    <div id="shareWrapper" class="shareWrapper mui-popover mui-popover-action mui-popover-bottom">
      <div class="title">分享到</div>
      <div class="more">
        <div class="single" id="wechatShareBtn" @tap.stop.prevent="shareToHaoyou()">
          <img src="/imgs/wechat_2x.png" />
        </div>
        <div class="single" id="wechatShareBtn2" @tap.stop.prevent="shareToPengyouQuan()">
          <img src="/imgs/pengyouquan.png" />
        </div>
      </div>
    </div>

    <div id="shareShowWrapper" class="mui-popover mui-popover-action mui-popover-top" @tap.stop.prevent="toggleShareNav()">
      <svg class="icon" aria-hidden="true">
        <use xlink:href="#icon-dianzheli"></use>
      </svg>
    </div>

  </div>
</template>


<script>
  export default {
    data: () => ({
      title: '',
    }),
    mounted() {
        this.initWebview();
    },
    created () {

    },
    methods: {
      initWebview() {
          this.title =  this.$route.query.title;
          console.log('shareTitle' + this.title);
          if (mui.os.plus) {
              mui.plusReady(() => {
                  var currentWebview = plus.webview.currentWebview();

                  var data = {
                      title: currentWebview.title,
                      link: currentWebview.link,
                      content: currentWebview.content,
                      imageUrl: currentWebview.imageUrl,
                      thumbUrl: currentWebview.thumbUrl,
                  };

                  console.log(data);

                  Share.bindShare(
                      this,
                      data,
                      this.successCallback,
                      this.failCallback
                  );
              });
          } else {
              var data = {
                  title: 'test',
                  link: 'test',
                  content: 'test',
                  imageUrl: 'test',
                  thumbUrl: 'test',
              };

              window.Share.bindShare(
                  this,
                  data,
                  this.successCallback,
                  this.failCallback
              );
          }
          var shareWrapper = document.getElementById('shareWrapper');
          document.body.appendChild(shareWrapper);

          var shareShowWrapper = document.getElementById('shareShowWrapper');
          document.body.appendChild(shareShowWrapper);
      },
      toggleShareNav() {
          mui('#shareShowWrapper').popover('toggle');
      },
      shareToHaoyou(){
          this.sendHaoyou();
          if (mui.os.plus) {
            mui('#shareWrapper').popover('toggle');
          } else {
            mui('#shareWrapper').popover('toggle');
            mui('#shareShowWrapper').popover('toggle');
          }
        this.hide();
      },
      shareToPengyouQuan(){
          this.sendPengYouQuan();
          if (mui.os.plus) {
            mui('#shareWrapper').popover('toggle');
          } else {
            mui('#shareWrapper').popover('toggle');
            mui('#shareShowWrapper').popover('toggle');
          }
        this.hide();
      },
      successCallback(){
        mui.toast('分享成功');

      },
      failCallback(error){
        console.error(JSON.stringify(error));
        mui.toast('分享失败');
      },
      share(){
        if (mui.os.plus) {
          mui.plusReady(function () {
            var currentWebview = plus.webview.currentWebview();
            currentWebview.setStyle({
              height: '100%',
              opacity: 0.97,
            });
          });
        }

        setTimeout(() => {
          mui('#shareWrapper').popover('toggle');
          mui("body").on('tap','.mui-backdrop', () => {
               this.hide();
          })
        }, 150);

      },
      hide(){
        if (mui.os.plus) {
          mui.plusReady(function () {
            var currentWebview = plus.webview.currentWebview();
            currentWebview.setStyle({
              height: '44px',
              opacity: 1,
            });
          });
        }
      },
    },
    computed: {},
    watch: {
        '$route' () {
            this.initWebview();
        }
    },
  }

</script>