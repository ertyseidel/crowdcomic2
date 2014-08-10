;(function(exports) {

	var timeout = null;
	var user = {
		id: 0,
		email: 0,
		name: '',
		link: '',
		oauth_token: '',
		locale: '',
		timezone: '',
		verified: false
	};

	var storeUserData = function() {
		user.email = response.email;
		user.name = response.name || response.first_name + ' ' + response.last_name;
		user.link = response.link;
		user.locale = response.locale;
		user.timezone = response.timezone;
		user.verified = response.verified;

		$.post(
			'./api/',
			{
				object: 'fb',
				action: 'login',
				user: user
			},
			function(data){
				console.log(data);
			}.bind(this),
			'json'
		);
	}

	// This is called with the results from from FB.getLoginStatus().
	var statusChangeCallback = function(response) {
		user.id = response.authResponse.userID;
		user.oauth_token = response.authResponse.accessToken;
		// The response object is returned with a status field that lets the
		// app know the current login status of the person.
		// Full docs on the response object can be found in the documentation
		// for FB.getLoginStatus().
		if (response.status === 'connected') {
			// Logged into your app and Facebook.
			FB.api('/me', function(){storeUserData(response)});
		}
	}

	// This function is called when someone finishes with the Login
	// Button.  See the onlogin handler attached to it in the sample
	// code below.
	exports.checkFBLoginState = function() {
		FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
		});
	}

	exports.fbAsyncInit = function() {
		if (typeof(FB) == 'undefined') return;
		FB.init({
			appId: '742676615770390',
			cookie : true,// enable cookies to allow the server to access
								// the session
			xfbml: true,// parse social plugins on this page
			version: 'v2.0' // use version 2.0
		});

		// Now that we've initialized the JavaScript SDK, we call
		// FB.getLoginStatus().This function gets the state of the
		// person visiting this page and can return one of three states to
		// the callback you provide.They can be:
		//
		// 1. Logged into your app ('connected')
		// 2. Logged into Facebook, but not your app ('not_authorized')
		// 3. Not logged into Facebook and can't tell if they are logged into
		//your app or not.
		//
		// These three cases are handled in the callback function.

		FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
		});

	};

})(window);
