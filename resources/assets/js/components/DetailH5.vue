<template>
  <div>
    <iframe v-show="show" id="show-iframe"  @load="loaded" frameborder=0 name="showHere" scrolling=auto></iframe>

    <iframe id="readhub-footer" class="footer" height="44px" width="100%"></iframe>

    <div class="toComment" @tap.stop.prevent="toDetail()"></div>
  </div>
</template>


<script>

  export default {
    data: () => ({
      loading:1,
      url: '',
      footerUrl:'',
      show: false
    }),
    created () {

    },
    methods: {
      loaded(){

      },
      toDetail(){
          var url = this.pathUrl.replace('/webview', '?from=h5');
          window.location.href=url;
      }
    },
    computed: {

    },
    watch: {

    },
    mounted(){
      this.url = this.$route.query.url;
      this.pathUrl = this.$route.query.pathUrl;

      var oIframe = document.getElementById('show-iframe');
      oIframe.src = this.url;
      const deviceWidth = document.documentElement.clientWidth;
      const deviceHeight = document.documentElement.clientHeight - 44;
      oIframe.style.width = deviceWidth + 'px';
      oIframe.style.height = deviceHeight + 'px';
      this.show = true;

      this.pathUrl = this.$route.query.pathUrl;
      var oIframeFooter = document.getElementById('readhub-footer');
      oIframeFooter.src = this.pathUrl;
    }
  }

</script>
<style>
  .footer{
    position: fixed;
    bottom:14px;
    z-index:99;
    border: 0;
    background: #fff;
  }
  .toComment{
     width:30%;
     height:44px;
     position: fixed;
     z-index:100;
     bottom:14px;
    left:50%;
    margin-left:-15%;
  }
</style>
