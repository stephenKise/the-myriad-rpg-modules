<?php
//*******************************************************************************
//I, Stephen Kise, am not responsible for any security attacks made to your site.
//*******************************************************************************

function password_getmoduleinfo()
{
	$info = array(
		"name" => "Forgotten Password",
		"author" => "`b`&Stephen Kise`b",
		"download" => "nope", //You cannot stop me, I have done this forever. I will disregard every time someone dipshit over at dragonprime mentions that 'nope' is not needed - because you cannot take away my nostalgia.
		"version" => "0.1b",
		"category" => "Account",
		"allowanonymous" => true, //never understood why this was not allow_anonymous
		"settings" => array(
			"tokens" => "Reset tokens (ignore this),viewonly|")
		);
	return $info;
}

function password_install()
{
	module_addhook('index');
	return TRUE;
}

function password_uninstall()
{
	return TRUE;
}

function password_dohook($hook,$args)
{
	global $session;
	switch($hook)
	{
		case 'index':
		  	if ($session['message'] == "`4Error, your login was incorrect.`0")
				$session['message'] .= "`b`n`2Forget your password? Click <a href='runmodule.php?module=password&op=forgot'>`@here</a>`2!`b`n";
			blocknav("create.php?op=forgot");
			//We are blocking this because it required the account's login. We provide info to access the account's login name to be accessed.
			//There was another module that searched for a player's email, but that brought back multiple responses based on multiple alts.
			//This is a replacement for the core feature, and the other module by SexyCook, it covers both features with just an emailaddress search.
			//Also, this is meant to reduce the amount of links provided on the homepage, which should actually only be provided if the account is locked out.
			//Hince the $session['message'] 'hack'.
		break;
	}
	return $args;
}

function password_run()
{
	$op = httpget('op');
	page_header('Forgotten Password');
	switch($op)
	{
		case 'forgot':
			addnav('Login','home.php');
			output("`c`b`QForgotten Password`b`c`n`2We are very sorry that you are experiencing issues to access our server! Please fill out your email you last associated the account with. We will then provide you information on how to recover the account via that email address.`n");
			rawoutput("<form action='runmodule.php?module=password&op=email' method='POST'>");
			rawoutput("<input type='email' name='email' placeholder='stephen@example.com'>");
			rawoutput("<input type='submit' value='Send'>");
			rawoutput("</form>");
		break;
		case 'email';
			global $_SERVER;
			$post = httpallpost();
			$token = md5(time().$post['email']);
			set_module_setting('tokens',get_module_setting('tokens').",$token");
			addnav('Login','home.php');
			$message = "Here are the list of accounts linked with this email: <br>";
			$sql = db_query("SELECT login, lastip FROM accounts WHERE emailaddress = '{$post['email']}'");
			while ($row = db_fetch_assoc($sql))
			{

				$message .= ucfirst($row['login'])." (IP: {$row['lastip']}) <<a href='{$_SERVER['HTTP_ORIGIN']}/runmodule.php?module=password&op=reset&token=$token&login={$row['login']}'>reset</a>><br>";
			}
			$headers  = "MIME-Version: 1.0 \r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
			$headers .= "Mailed-by: {$_SERVER['HTTP_HOST']} \r\n Signed-by: {$_SERVER['HTTP_HOST']} \r\n";
			$headers .= "From: Xythen's Auto Emailer <noreply@{$_SERVER['HTTP_HOST']}> \r\n";
			if (mail($post['email'],"Forgotten Password!",$message,$headers))
				output("`@You got mail!`n`2Be sure to check your spam folder for a message from us, `^Xythen's Auto Emailer`2. Read over the email and follow the links to reset your account's password.");
			else
				output("`\$Error, we could not send the email to that address.");
		break;
		case 'reset':
				addnav('Login','home.php');
				output("`@Set your new password for this account:`n");
				rawoutput("<form action='runmodule.php?module=password&op=changepassword' method='post'>");
				rawoutput("<input type='hidden' name='token' value='".httpget('token')."'>");
				rawoutput("<input type='hidden' name='login' value='".httpget('login')."'>");
				rawoutput("<input type='password' name='password' placeholder='New password'>");
				rawoutput("<input type='submit' value='Save'>");
				rawoutput("</form>");
		break;
		case 'changepassword':
	   		global $session;
			$post = httpallpost();
			$token = $post['token'];
			$password = md5(md5($post['password']));
			if (strstr(get_module_setting('tokens'),$token))
			{ //verification
				$tokens = str_replace(get_module_setting('tokens'),",$token",'');
				set_module_setting('tokens',$tokens);
				$sql = db_query("UPDATE accounts SET password = '$password' WHERE login = '{$post['login']}'");
				$session['message'] = "`^Your password has been successfully changed. Please continue to login!`0";
				require_once('lib/redirect.php');
				redirect('home.php');
			}
		break;
	}
	page_footer();
}
?>