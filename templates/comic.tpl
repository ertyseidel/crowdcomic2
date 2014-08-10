<div class="comic">
	{{> comic_switcher}}
	{{#with origin_post}}
		{{>post}}
	{{/with}}
	{{#each parts}}
		{{#if image}}
			<div class="comic-part-image">
				<img src="./comics/{{image}}" alt="Comic Image" />
			</div>
		{{/if}}
		{{#if text}}
			<div class="comic-part-text">
				{{{text}}}
			</div>
		{{/if}}
	{{/each}}
	<div class="comic-text">
		{{{text}}}
	</div>
	{{> comic_switcher}}
	{{#unless is_locked}}
		{{>new_post}}
	{{/unless}}
	<div class="comic-posts">
		<!-- user post -->
		{{#each posts}}
			{{#if is_self_post}}
				{{>post}}
			{{/if}}
		{{/each}}
		<!-- other posts -->
		{{#each posts}}
			{{#unless is_self_post}}
				{{>post}}
			{{/unless}}
		{{/each}}
	</div>
</div>
