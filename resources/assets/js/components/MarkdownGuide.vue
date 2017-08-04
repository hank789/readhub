<template>
    <div class="v-modal-small" :class="{ 'width-100': !sidebar }">
        <div class="v-modal-small-box" v-on-clickaway="close">
            <div class="flex1">
                <h2 class="align-center user-select">
                    Markdown规范
                </h2>

                <p class="user-select">
                    请使用Markdown撰写内容：
                </p>

                <table class="table">
				  	<thead class="user-select">
					    <tr>
							<th>内容</th>
							<th class="half-width">显示</th>
					    </tr>
				  	</thead>

				  	<tbody>
			  			<tr>
					      	<td>
					      		**bold**
					      	</td>

					      	<td>
					      		<b>bold</b>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		__italic__
					      	</td>

					      	<td>
					      		<i>italic</i>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		[inwehub.com](https://www.inwehub.com)
					      	</td>

					      	<td>
					      		<a href="https://inwehub.com" target="_blank">inwehub.com</a>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
                                -unordered lists <br>
                                -unordered lists <br>
                                -unordered lists
					      	</td>

					      	<td>
					      		<ul class="no-margin">
					      		    <li>unordered lists</li>
					      		    <li>unordered lists</li>
					      		    <li>unordered lists</li>
					      		</ul>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
                                1.ordered list <br>
                                2.ordered list <br>
                                3.ordered list
					      	</td>

					      	<td>
					      		<ol class="no-margin">
					      		    <li>ordered list</li>
					      		    <li>ordered list</li>
					      		    <li>ordered list</li>
					      		</ol>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      		`Inline code`
					      	</td>

					      	<td>
					      		<code>Inline code</code>
					      	</td>
					    </tr>

                        <tr>
					      	<td>
					      	<br>
					      		```if (url == "inwehub") {<br>
						        	return "欢迎"<br>
						        }```
					      	</td>

					      	<td>
<pre>if (url == "inwehub") {
    return "欢迎"
}</pre>
					      	</td>
					    </tr>
				  	</tbody>
				</table>

                <button type="button" class="v-button v-button--green v-button--block"
                    data-toggle="tooltip" data-placement="bottom" title="Close (esc)"
                    @click="close">
                    关闭
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mixin as clickaway } from 'vue-clickaway';

	export default {
		props: ['sidebar'],

        mixins: [ clickaway ],

	    methods: {
	    	close () {
	    		this.$eventHub.$emit('close')
	    	},
	    },
	}
</script>
