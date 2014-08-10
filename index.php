<?php
	$templates = ['comic', 'nav'];
	$partials = ['comic_switcher', 'post', 'new_post'];
	$scripts = ['ComicPage', 'Nav', 'fblogin', 'index'];
	$styles = ['comic', 'comic-switcher', 'comic-post', 'nav'];
	include('header.php');
?>
	<div class="comic"></div>
<?php
	include('footer.php');
?>
