{{#if is_locked}}
	<h3>This comic has been locked. Check back tomorrow for what happens next!</h3>
{{else}}
	{{#if logged_in_user}}
		{{#unless has_self_post}}
			<div>
				<button class="button comic-post-cell--new-post__button comic-post-cell--new-post__button--suggestion">
					<span class="suggestion">Suggest</span> what the protagonist should do next
				</button>
				or
				<button type="button" class="button comic-post-cell--new-post__button comic-post-cell--new-post__button--question">
					Ask the author a <span class="question">Question</span>
				</button>
			</div>
			<div class="new-post-form-container"></div>
		{{/unless}}
	{{else}}
		<h3><button class="button new-post-login">Login</button> to leave a post or upvote one!</h3>
	{{/if}}
{{/if}}
