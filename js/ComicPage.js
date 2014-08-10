;(function(exports) {
	exports.ComicPage = function(id, buttons){
		this.comicMax = 0;
		this.buttons = buttons;
		this.comicData = false;
	};

	exports.ComicPage.prototype = {
		getComic: function(updateHistory, callback) {
			$.get(
				'./api/',
				{
					object: 'comic',
					action: 'get',
					pk_comic_id: Exports.comic_id
				},
				function(data){
					if (data.status == 'success') {
						this.comicData = data.data;
						if (updateHistory) {
							history.pushState(
								{comicId: Exports.comic_id},
								"",
								Exports.comic_id
							);
						}
						this.render(updateHistory);
					} else {
						alert(data.data);
						Exports.comic_id = 0;
					//	this.getComic(false);
					}
					if (typeof(callback) == 'function') {
						callback.call();
					}
				}.bind(this),
				'json'
			);
		},
		render: function(shouldScroll) {
			if (!this.comicData) return;
			this.comicData.has_self_post = false;
			this.comicData.has_viewer_vote = false;
			this.comicData.logged_in_user = Exports.user_id ? true : false;
			this.comicData.username = Exports.username;
			if (this.comicData.posts) {
				this.comicData.posts.map(function(c) {
					c.logged_in_user = Exports.user_id;
					console.log(c);

					c.is_suggestion = c.type == 'suggestion';
					c.is_question = c.type == 'question';

					if(c.user.pk_user_id == Exports.user_id){
						c.is_self_post = true;
						this.comicData.has_self_post = true;
					}

					if (c.has_viewer_vote) {
						this.comicData.has_viewer_vote = true;
					}

					return c;
				}.bind(this));
			} else {
				this.comicData.posts = [];
			}

			if (!Exports.comic_id) {
				Exports.comic_id = this.comicData.pk_comic_id;
			}
			this.comicMax = this.comicData.max_id;
			if (shouldScroll) {
				setTimeout(function() {
					$('html, body').scrollTop($('.comic').offset().top);
				}, 10);
			} else {
			}
			$('.comic').html(templates.comic(this.comicData));
			this.bindButtons();
			if (this.comicData.has_self_post) {
				this.hideVotingStars();
			}
		},
		bindButtons: function() {
			if (Exports.comic_id == 1) {
				$(this.buttons.first).hide();
				$(this.buttons.prev).hide();
			} else {
				$(this.buttons.first).click(this.first.bind(this));
				$(this.buttons.prev).click(this.decrement.bind(this));
			}
			if (Exports.comic_id >= this.comicMax) {
				$(this.buttons.next).hide();
				$(this.buttons.last).hide();
			} else {
				$(this.buttons.next).click(this.increment.bind(this));
				$(this.buttons.last).click(this.last.bind(this));
			}
			this.bindVotes();
			this.bindSubmit();
		},
		increment: function() {
			++Exports.comic_id;
			if (Exports.comic_id > this.comicMax) {
				Exports.comic_id = this.comicMax;
			} else {
				this.getComic(true);
			}
		},
		decrement: function() {
			--Exports.comic_id;
			if(Exports.comic_id < 1) {
				Exports.comic_id = 1;
			} else {
				this.getComic(true);
			}
		},
		first: function() {
			Exports.comic_id = 1;
			this.getComic(true);
		},
		last: function() {
			Exports.comic_id = this.comicMax;
			this.getComic(true);
		},
		bindVotes: function() {
			$('.comic-post-cell--vote:not(.comic-post-cell--vote--winner)')
				.click(function(evt){
					this.voteOn(evt.target);
				}.bind(this));
		},
		voteOn: function(target) {
			var post_id = $(target).attr('data-post-id');
			$.post(
				'./api/',
				{
					object: 'vote',
					action: 'post',
					pk_post_id: post_id
				},
				function(data){
					if (data.status == 'success') {
						this.getComic(false);
					} else {
						alert(data.data);
					}
				}.bind(this),
				'json'
			);
		},
		bindSubmit: function() {
			var newPostContents = "";
			$('.comic-post-cell--new-post__button--suggestion')
				.click(function() {
					if (newPostContents == 'suggestion') {
						return;
					}
					newPostContents = 'suggestion';
					$('.new-post-form-container').html(
						templates.new_post_form({
							'username': Exports.username,
							'placeholder': 'Type what you think the protagonist should try to do next here!',
							'action': 'Submit Post'
						})
					);
					$('.comic-post-cell--submit__button')
						.click(function(event) {
							console.log(this);
							this.submitPost('suggestion', $('.comic-post-cell--text--new__input').val());
						}.bind(this));
				}.bind(this));
			// $('.comic-post-cell--submit__button--question')
			// 	.click(function() {
			// 		$('.comic').html(templates.comic(this.comicData));
			// 	});
		},
		submitPost: function(type, content) {
			$.post(
				'./api/',
				{
					object: type,
					action: 'post',
					text: content,
					comic_id: Exports.comic_id
				},
				function(data){
					if (data.status == 'success') {
						this.hideVotingStars();
						this.getComic(false);
					} else {
						alert(data.data);
					}
				}.bind(this),
				'json'
			);
		},
		hideVotingStars: function() {
			$('.comic-post-cell--vote:not(.comic-post-cell--vote--up)')
				.unbind('click')
				.addClass('comic-post-cell--vote--hidden');
		}
	};

})(window);
