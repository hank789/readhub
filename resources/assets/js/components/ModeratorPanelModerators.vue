<template>
    <section>
        <h1 class="dotted-title">
			<span>
				添加新管理员
			</span>
		</h1>

        <div class="form-group">
            <multiselect :value="username" :options="users" @input="updateSelected"
            @search-change="getUsers" :placeholder="'按用户名搜索...'" :loading="loading"
            ></multiselect>
        </div>

        <div class="form-group">
            <multiselect :value="role" :options="roles" @input="updateRole"
                :placeholder="'选择角色...'"
            ></multiselect>
        </div>

        <div class="form-group">
            <button type="button" class="v-button v-button--green" :disabled="!role || !username" @click="addModerator">添加</button>
        </div>


        <h1 class="dotted-title">
			<span>
				管理员列表
			</span>
		</h1>

        <moderator v-for="(mod, index) in mods" :list="mod" :key="mod.id"
        @delete-moderator="mods.splice(index, 1)"></moderator>
    </section>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    import Moderator from '../components/Moderator.vue'

    export default {
        components: { Moderator, Multiselect },

        mixins: [],

        data: function () {
            return {
                username: null,
                users: [],
                loading: false,
                roles: ['administrator', 'moderator'],
                role: 'moderator',
                mods: [],
            }
        },

        props: {
            //
        },

        computed: {
            //
        },

        created () {
            this.getMods()
        },

        mounted () {
            //
        },

        methods: {
            getMods(){
                axios.post('/moderators', {
                    category_name: Store.category.name
                }).then((response) => {
                    this.mods = response.data
                })
            },


            getUsers: _.debounce(function (query) {
                if (!query) return

                this.loading = true

                axios.get('/users', {
                	params: {
	                    username: query,
            			category: Store.category.name
                	}
                }).then((response) => {
                    this.users = response.data
                    this.loading = false
                })
            }, 600),


            updateSelected (newSelected) {
                this.username = newSelected
            },

            updateRole (newSelected) {
                this.role = newSelected
            },

            addModerator(){
                axios.post('/add-moderator', {
                    category_name: Store.category.name,
                    username: this.username,
                    role: this.role
                }).then((response) => {
                    this.username = null
                    this.role = 'moderator'

                    this.getMods()
                })
            }
        },

        beforeRouteEnter(to, from, next){
            if (Store.category.id == to.params.name) {
                // loaded
                if (Store.administratorAt.indexOf(Store.category.id) != -1) {
                    next()
                }
            } else {
                // not loaded but let's continue (the server-side is still protecting us!)
                next()
            }
        },
    };
</script>
