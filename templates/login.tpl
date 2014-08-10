<h3>Register an Account</h3>
<form id="register">
	<table>
		<tr>
			<th>Username</th>
			<td><input id="register_username" class="input" type="text" name="username" /></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input id="register_password" class="input" type="password" name="password" /></td>
		</tr>
		<tr>
			<th>Email*</th>
			<td><input id="register_email" class="input" type="text" name="email" /></td>
		</tr>
		<tr>
			<td colspan="2"><input class="button" type="submit" value="Register!" /></td>
		</tr>
		<tr>
			<td colspan="2"><p id="register_error"></p></td>
		</tr>
	</table>
</form>

<p>* Email is optional, for password recovery purposes only.</p>

<!--
Or login with Facebook: <fb:login-button scope="public_profile,email" onlogin="checkFBLoginState();">
</fb:login-button>
<div id="fb-root"></div>
<div id="fb-status"></div>
-->
<p>Facebook login coming very soon. (You'll be able to connect an existing account to facebook.)</p>

<h3>Login</h3>
<form id="login">
	<table>
		<tr>
			<th>Username</th>
			<td><input id="login_username" class="input" type="text" name="username"></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input id="login_password" class="input" type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" class="button" value="Log in!" /></td>
		</tr>
		<tr>
			<td colspan="2"><p id="login_error"></p></td>
		</tr>
	</table>
</form>
