;(function(exports) {

	exports.Nav = function(comic) {
		this.modal = null;
		this.comic = comic;
	};

	exports.Nav.prototype = {

		render: function(id) {
			$(id).html(templates.nav({logged_in_user: Exports.user_id}));
			this.bindButtons();
		},

		bindButtons: function() {
			$('.nav-list__item--about').click(this.showAboutModal.bind(this));
			$('.nav-list__item--style').click(this.showStyleModal.bind(this));
			$('.nav-list__item--login').click(this.showLoginModal.bind(this));
			$('.nav-list__item--legal').click(this.showLegalModal.bind(this));
			$('.nav-list__item--logout').click(
				this.logout.bind(this)
			);
		},

		login: function(username, password) {
			$.post(
				'./api/',
				{
					object: 'user',
					action: 'login',
					username: username,
					password: password
				},
				function(data){
					if (data.status == 'success') {
						Exports.user_id = data.data.pk_user_id;
						Exports.username = data.data.username;
						this.modal.close();
						this.render('.nav');
						this.comic.render(false);
					} else {
						$('#login_error').text(data.data);
					}
				}.bind(this),
				'json'
			);
		},

		register: function(username, password, email) {
			$.post(
				'./api/',
				{
					object: 'user',
					action: 'register',
					username: username,
					password: password,
					email: email
				},
				function(data){
					if (data.status == 'success') {
						Exports.user_id = data.data;
						this.modal.close();
						this.render('.nav');
						this.comic.render(false);
					} else {
						$('#register_error').text(data.data);
					}
				}.bind(this),
				'json'
			);
		},

		logout: function() {
			$.post(
				'./api/',
				{
					object: 'user',
					action: 'logout',
				},
				function(data){
					if (data.status == 'success') {
						Exports.user_id = false;
						this.render('.nav');
						this.comic.render(false);
					} else {
						alert(data.data);
					}
				}.bind(this),
				'json'
			);
		},

		showAboutModal: function() {
			this.modal = new Modal();
			this.modal.render(templates.about());
		},

		showLegalModal: function() {
			this.modal = new Modal();
			this.modal.render(templates.legal());
		},

		showLoginModal: function() {
			this.modal = new Modal();
			this.modal.render(templates.login());

			$('#login').submit(function(evt){
				evt.preventDefault();
				this.login($('#login_username').val(), $('#login_password').val());
				return false;
			}.bind(this));

			$('#register').submit(function(evt){
				evt.preventDefault();
				this.register(
					$('#register_username').val(),
					$('#register_password').val(),
					$('#register_email').val()
				);
				return false;
			}.bind(this));

			// (function(d, s, id) {
			// 	var js, fjs = d.getElementsByTagName(s)[0];
			// 	if (d.getElementById(id)) return;
			// 	js = d.createElement(s); js.id = id;
			// 	js.src = "//connect.facebook.net/en_US/sdk.js";
			// 	fjs.parentNode.insertBefore(js, fjs);
			// }(document, 'script', 'facebook-jssdk'));
			// fbAsyncInit();
		},

		showStyleModal: function() {
			this.modal = new Modal();
			this.modal.render(templates.style());
		}
	};

})(window);
