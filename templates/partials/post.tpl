<table class="comic-post {{#if is_self_post}}comic-post--self{{/if}}">
	<tr>
		{{#if is_origin_post}}
			<td class="comic-post-cell comic-post-cell--vote comic-post-cell--vote--winner"></td>
		{{else}}
			{{#if logged_in_user}}
				<td class="comic-post-cell comic-post-cell--vote {{#if has_viewer_vote}}comic-post-cell--vote--up{{/if}}" data-post-id="{{pk_post_id}}"> </td>
			{{else}}
				<td class="comic-post-cell comic-post-cell--placeholder">&nbsp;</td>
			{{/if}}
		{{/if}}
		<td class="comic-post-cell comic-post-cell--username">
			{{user.username}}
			{{#if is_suggestion}}
				<span class="suggestion"> suggests:</span>
			{{/if}}
			{{#if is_question}}
				<span class="question"> asks:</span>
			{{/if}}
		</td>
		<td class="comic-post-cell comic-post-cell--text">
			{{text}}
		</td>
	</tr>
</table>
