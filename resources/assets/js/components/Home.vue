<template>

	<div class="home-wrapper"  :class="{'home-wrapper-h5':Store.is_h5}">

		<div class="categoryMenu flex-space" v-if="$route.path === '/h5'">
			<div class="item" @tap.stop.prevent="categoryMenuClick(1)">
				<svg class="icon icon-inwehub" aria-hidden="true">
					<use xlink:href="#icon-wendashequ"></use>
				</svg>
				问答社区
            </div>
			<div class="item" @tap.stop.prevent="categoryMenuClick(2)">
				<svg class="icon icon-inwehub" aria-hidden="true">
					<use xlink:href="#icon-chengchangye-baominghuodong"></use>
				</svg>
				活动报名
            </div>
			<div class="item" @tap.stop.prevent="categoryMenuClick(3)">
				<svg class="icon icon-inwehub" aria-hidden="true">
					<use xlink:href="#icon-xiangmujiyu"></use>
				</svg>
				项目机遇
            </div>
			<div class="item" @tap.stop.prevent="categoryMenuClick(4)">
				<svg class="icon icon-inwehub" aria-hidden="true">
					<use xlink:href="#icon-fujinqiye"></use>
				</svg>
				附近企业
            </div>
			<div class="item" @tap.stop.prevent="categoryMenuClick(5)">
				<svg class="icon icon-inwehub" aria-hidden="true">
					<use xlink:href="#icon-gengduozhuanjia"></use>
				</svg>
				更多专家
            </div>
		</div>

		<div class="listBanner" id="listBanner" v-if="$route.path === '/h5'">
			<swiper :options="swiperOption">
				<swiper-slide>
					<img src="/imgs/newguwen@2x.png"/>
				</swiper-slide>
				<swiper-slide>
					<img src="/imgs/neirongjingxuan@2x.png"/>
				</swiper-slide>
				<swiper-slide>
					<img src="/imgs/jiyuhuodong.png"/>
				</swiper-slide>
			</swiper>
		</div>

		<div class="menu-inwehub" id="menu-inwehub" v-if="$route.path === '/h5'">
			<div class="left">
				<router-link tag="div" :to="{ path: '/h5' }" class="menu-item" :class="{ 'active': sort == 'hot' }">
					热门
                </router-link>
				<router-link tag="div" :to="{ path: '/h5?sort=new' }" class="menu-item" :class="{ 'active': sort == 'new' }">
					最新
                </router-link>
			</div>
			<div class="right">
				<div class="menu-item ui dropdown top pointing">
					<svg class="icon-inwehub" aria-hidden="true">
						<use xlink:href="#icon-wode1"></use>
					</svg>
					<div class="menu">
						<div @click="goLink('/bookmarks/submissions')" class="item">
							我的收藏
                        </div>
						<div @click="goLink('/' + '@' + auth.id)" class="item">
							我的发布
                        </div>
					</div>
				</div>

				<div class="menu-item">
					<svg class="icon-inwehub" aria-hidden="true" @click="refresh">
						<use xlink:href="#icon-shuaxin"></use>
					</svg>
				</div>

<router-link tag="div" :to="{ path: '/submit' }" class="menu-item">
				<svg class="icon-inwehub modify" aria-hidden="true">
					<use xlink:href="#icon-xiugai"></use>
				</svg>
			</router-link>
			</div>
		</div>

		<home-submissions></home-submissions>
		<inwehubDialog ref="inwehubDialog"></inwehubDialog>
	</div>
</template>

<script>
	import HomeSubmissions from '../components/HomeSubmissions.vue';
	import Announcement from '../components/Announcement.vue';
	import Helpers from '../mixins/Helpers';
	import LocalStorage from '../mixins/LocalStorage';
    import { swiper, swiperSlide } from 'vue-awesome-swiper';
    import Webview from '../mixins/Webview';
    import inwehubDialog from '../components/Dialog.vue';

    export default {
    	mixins: [Helpers, LocalStorage, Webview],

	    components: {
	        HomeSubmissions,
	        Announcement,
            swiper,
            swiperSlide,
            inwehubDialog
	    },
        data(){
            return {
                swiperOption: {
                    slidesPerView: 3,
                    spaceBetween: 10
                }
            }
        },

        created() {
            this.swiperOption = {
                slidesPerView: 'auto',
                spaceBetween: 10,
                onTap:(swiper) => {
                    this.categoryMenuClick(swiper.clickedIndex + 6);
                }
            };

            this.setPageTitle('Inwehub - 阅读', true);
            this.askNotificationPermission();
        },

		mounted () {

			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
	        	this.$root.loadSemanticDropdown();
			})
		},

        computed: {
        	filter() {
        	    return Store.feedFilter;
        	},

        	/**
    	 	 * the sort of the page
	    	 *
	    	 * @return mixed
	    	 */
	    	sort() {
	    	    if (this.$route.query.sort == 'new')
	    	    	return 'new';

	    	    if (this.$route.query.sort == 'rising')
	    	    	return 'rising';

	    	    return 'hot';
	    	},
        },

        methods: {
            openNewUrl(submission){

                var pathUrl = '/c/' + submission.category_id + '/' + submission.slug;
                var isPlusReady = navigator.userAgent.match(/Html5Plus/i);
                if (isPlusReady) {

                    if(/http/.test(submission.data.url)) {

                        var avatarUrl = null;
                        if (/^http/.test(submission.owner.avatar)) {
                            avatarUrl = submission.owner.avatar;
                        } else {
                            avatarUrl = window.location.protocol + '//' + window.location.host +  this.submission.owner.avatar;
                        }

                        var data = {
                            article_id: submission.id,
                            article_url: submission.data.url,
                            article_title: submission.title,
                            article_category_name: submission.category_name,
                            article_comment_url: pathUrl,
                            article_img_url:avatarUrl,
                        };

                        this.putLS('readhub_article_son_data', data);

                        console.log('传给article的参数:' + JSON.stringify(data));

                        var webview = mui.openWindow({
                            url: window.location.protocol + '//' + window.location.host + '/article/0',
                            id: 'readhub_article_son',
                            preload: false, //一定要为false
                            createNew: false,
                            show: {
                                autoShow: false,
                                aniShow: 'pop-in'
                            },
                            styles: {
                                popGesture: 'hide'
                            },
                            waiting: {
                                autoShow: false
                            },
                            extras: {
                                article_id: submission.id,
                                article_url: submission.data.url,
                                article_title: submission.title,
                                article_category_name: submission.category_name,
                                article_comment_url: pathUrl,
                                article_img_url:avatarUrl,
                                preload: true
                            }
                        });

                        mui.fire(webview,'go_to_readhub_page',{
                            url: '/article/'+submission.id
                        });
                        setTimeout( () => {
                            webview.show();
                        },100);
                    } else {
                        this.openWebviewSubmission(submission.data.url,submission.title);
                    }
                } else {
                    this.openWebviewSubmission(submission.data.url,submission.title);
                }
            },
            categoryMenuClick(index){
                var callback = (response) => {
                    var warningAlert = () => {
                        var level = response.data.current_level;
                        this.$refs.inwehubDialog.getHtml('test', {level:level}, (html) => {
                            window.alertSimple(html, '查看等级详情', (num) =>{
                                if (num.index == 0) {
                                    this.parentOpenUrl('/my/Growth');
                                }

                            }, true);
                        });
					};

                    switch(index) {
                        case 2:
                        case 8:
                            if (response.data.is_valid) {
                                this.parentOpenUrl('/home/ActiveList');
							} else {
                                warningAlert();
							}

                            break;
                        case 3:
                            if (response.data.is_valid) {
                                this.parentOpenUrl('/home/OpportunityList');
                            } else {
                                warningAlert();
                            }
                            break;
                        case 1:
                        case 4:
                        case 5:
                        case 7:
                            warningAlert();
                            break;
                        case 6:
                            var submition = {
                                id:'139',
                                title:'test',
                                category_id:'3',
                                category_name:'河南test',
                                slug:'5',
                                data:{
                                    url:'http://mp.weixin.qq.com/s/g7AwX8SZbGfdWWDg05e2aA',
                                },
                                owner:{
                                    avatar:'https://intervapp-test.oss-cn-zhangjiakou.aliyuncs.com/media/257/medialibrarySfyBBi',
                                }
                            };
							this.openNewUrl(submition);
                            break;

                    }

                };
                axios.get(this.authUrl('check-user-level'), {
                    params: {
                        permission_type: index
                    }
                }).then((response) => {
                    callback(response);
                    console.log(response);
                })
            },
    	    goLink(url) {
    	        setTimeout(() => {
                    this.$router.push(url);
				}, 100);
			},
            getUnreadNotifications(){
                this.$eventHub.$emit('getUnreadNotifications')
			},
            changeRoute: function(newRoute) {
                this.$eventHub.$emit('new-route', newRoute)
            },
        	/**
        	 * changes the filter for home feed
        	 *
        	 * @return void
        	 */
        	changeFilter(filter) {
        		if (Store.feedFilter == filter) return;

        	    Store.feedFilter = filter;

        	    this.putLS('feed-filter', filter);

        	    this.refresh();
        	},

        	/**
        	 * fires the refresh event
        	 *
        	 * @return void
        	 */
        	refresh() {
        	    this.$eventHub.$emit('refresh-home');
        	},

        	/**
        	 * In case the user has just joined to the Voten community let's ask them for the awesome Desktop notifications permission.
        	 *
        	 * @return void
        	 */
        	askNotificationPermission() {
                 if (this.$route.query.newbie == 1) {
                     if ('Notification' in window) {
                         Notification.requestPermission()
                     } else {
                         console.log('Your browser does not support desktop notifications. ')
                     }
                 }
        	}
        },
    }
</script>
