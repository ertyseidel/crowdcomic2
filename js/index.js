
$(document).ready(function() {
	var comic = new ComicPage(Exports.comic_id, {
		first: '.comic-switcher-button-first',
		prev: '.comic-switcher-button-prev',
		next: '.comic-switcher-button-next',
		last: '.comic-switcher-button-last'
	});

	window.onpopstate = function(evt) {
		comic.comicNum = evt.state.comicId || 0;
		comic.getComic(true);
	};

	var nav = new Nav(comic);
	nav.render('.nav');

	var comicLoadCallback = function() {
		$('.new-post-login').click(function(evt) {
			nav.showLoginModal();
			$('.modal').css({
				top: $(window).scrollTop()
			});
		});
	}

	comic.getComic(false, comicLoadCallback);

});
