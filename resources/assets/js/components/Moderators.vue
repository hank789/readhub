<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <h2 class="align-center">
                    管理人员
                </h2>

                <loading v-show="loading"></loading>

                <div class="small-modal-user" v-for="user in list">
                    <span>
                        <img :src="user.avatar" :alt="user.username">
                    </span>

                    <span>
                        {{ user.username }}
                    </span>
                </div>

                <button type="button" class="v-button v-button--green v-button--block"
                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)"
                    @click="close">
                    关闭
                </button>
            </div>
        </div>
    </div>
</template>

<style>
    .small-modal-user {
        /*display: flex;
        justify-content: space-between;
        align-items: center;*/
    }

    .small-modal-user img {
        width: 4em;
        height: auto;
        margin: 1em;
        border-radius: 50%;
        border: 1px solid #635d5d;
    }
</style>

<script>
import Loading from '../components/Loading.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
	props: ['sidebar'],

    mixins: [ clickaway ],

    components: {
        Loading
    },

    data () {
        return {
            list: [],
            loading: true,
        }
    },

    created: function () {
        this.getModerators();
    },

    methods: {
        getModerators() {
            axios.get( '/category-moderators', {
                params: {
                	name: Store.category.name
                }
            }).then((response) => {
                this.list = response.data;
                this.loading = false
            });
        },

    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close () {
    		this.$eventHub.$emit('close')
    	},
    },

}

</script>
