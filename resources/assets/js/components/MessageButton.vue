<template>
    <button class="v-button v-button--green" @click="sendMessage">
        私信
    </button>
</template>


<script>
    export default {
        props: ['id'],

        data: function () {
            return {
                subscribed: false,
                contact: [],
            }
        },


        created: function () {
        	this.getUser()
        },

        methods: {
        	/**
        	 * Fetches the required user's info for starting a conversation with him/her
        	 *
        	 * @return void
        	 */
        	getUser() {
        		axios.get('/contact-info', {
	                params: {
	                	user_id: this.id
	                }
	            }).then((response) => {
	            	this.contact = response.data;
	            });
        	},

        	/**
        	 * Fires the send message event and sends the contact
        	 *
        	 * @return void
        	 */
        	sendMessage() {
        		this.$eventHub.$emit('start-conversation', this.contact)
        	}
        }
    }
</script>
