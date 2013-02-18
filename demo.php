<?php include 'userauth.php';
$u->cookielogin();
if ($_GET['a'] == "logout")
	$u->logout();
if (isset($_POST['user'])) {
	$res = ($u->login($_POST['user'], $_POST['pass'], true)) ? "Logged in!" : "Login failed.";
}	

 ?>
<h1>Demo of User Authentication system</h1><br />
<?php
	echo $res."<br />";
	if ($u->isloggedin()) {
		echo "You are currently logged in as " . $u->uname() . ". Click <a href=\"?a=logout\">here</a> to log out.";
	} else {
?>
<form method="POST" action="index.php">
Username: <input name="user" /> Password: <input type="password" name="pass" /> <input type="submit" value="Login" />
</form>
<?php } ?>

<h1>Create a new user</h1><br />
<?php
	if (isset($_POST['newuser'])) {
		echo ($u->newUser($_POST['newuser'], $_POST['pass'])) ? "User created." : "Username taken.";
	}
?>
<form method="POST" action="index.php">
Username: <input name="newuser" /> Password: <input name="pass" /> <input type="submit" value="Create user" />
</form>