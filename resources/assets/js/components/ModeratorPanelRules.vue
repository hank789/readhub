<template>
    <section id="moderator-panel-rules">
        <h1 class="dotted-title">
			<span>
				规则
			</span>
		</h1>

        <p>
             文章内容条款规则详见：<a href="/tos">条款</a>。每个频道最多5条规则。
        </p>

        <div class="form-group" v-if="type == 'edit' || (type == 'create' && items.length < 5)">
            <label for="title" class="form-label">规则:</label>

            <textarea class="form-control" rows="3" v-model="title" id="title" placeholder="规则(支持markdown语法)..."></textarea>

            <small class="text-muted go-red" v-for="e in errors.title">{{ e }}</small>
        </div>

        <div class="form-group" v-if="type == 'create' && items.length < 5">
            <button type="button" class="v-button v-button--green" :disabled="!title" @click="createRule">创建</button>
        </div>

        <div class="form-group" v-if="type == 'edit'">
            <button type="button" class="v-button v-button--primary" :disabled="!title" @click="patch">编辑</button>
        </div>


        <h1 class="dotted-title" v-if="items.length">
			<span>
				所有规则
			</span>
		</h1>

        <rule v-for="rule in items" :list="rule" :key="rule.id" @delete-rule="destroy" @edit-rule="editRule"></rule>
    </section>
</template>

<script>
    import Rule from '../components/Rule.vue'

    export default {
        components: { Rule },

        data: function () {
            return {
                errors: [],
                title: null,
                id: null,
                category_id: null,
                items: [],
                type: 'create',
            }
        },


        created () {
            this.getItems()
        },


        methods: {
            createRule(){
                axios.post('/create-rule', {
                    title: this.title,
                    category_name: Store.category.name
                }).then((response) => {
                    this.items.unshift(response.data)
                    this.clear()
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                })
            },

            getItems(){
                axios.get('/rules', {
                    params: {
                    	name: Store.category.name
                    }
                }).then((response) => {
                    this.items = response.data
                })
            },

            destroy(rule_id, category_id){
                axios.post('/destroy-rule', {
                    rule_id,
                    category_id
                }).then((response) => {
                    this.items = this.items.filter(function (item) {
                      	return item.id != rule_id
                    })
                })
            },

            editRule(rule){
                this.title = rule.title
                this.id = rule.id
                this.category_id = rule.category_id

                this.type = 'edit'

                this.$nextTick(function() {
                    this.$root.autoResize()
                })
            },

            patch(){
                axios.post('/patch-rule', {
                    title: this.title,
                    category_id: this.category_id,
                    rule_id: this.id,
                }).then((response) => {
                    let id = this.id

		            function findObject(ob) {
		                return ob.id === id
		            }

		            this.items.find(findObject).title = this.title

                    this.clear()
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                });
            },

            clear(){
                this.title = null,
                this.id = null
                this.category_id = null
                this.type = 'create'
                this.errors = []
            }
        },

        // only administrators can access this route
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
