<ul class="nav-list">
	<li class="button nav-list__item nav-list__item--about">About</li>
	<li class="button nav-list__item nav-list__item--legal">Legal</li>
	{{#if logged_in_user}}
		<li class="button nav-list__item nav-list__item--logout">Logout</li>
	{{else}}
		<li class="button nav-list__item nav-list__item--login">Login</li>
	{{/if}}
	<li class="button nav-list__item nav-list__item--style">Beta</li>
</ul>
