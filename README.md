User Authentication System (PHP, SQLite)
=======================
Introduction
------------
Any website running a PHP and SQLite compatible server is able to use this PHP class to manage their own user system. It provides very basic functionality, such as creating users, logging in, setting cookies, changing passwords, etc. It does not use a MySQL database, so it's easier to install, and more portable. Click here to see a demo.

Security Measures
-----------------
The obvious downside to using a flat-file database is poor security. Ideally, you should change the file permissions on the server to prevent unauthorized access to the database file directly. All passwords are heavily salted and hashed with MD5. Since it is impossible to algorithmically reverse an MD5 hash, a large and arbitrary salt will almost guarantee an uncrackable hash.



Usage - initialization
-----------------
First, the class file, which can be found at the end of the page, needs to be included at the top of all pages. This will automatically create a new instance of userauth handled by $u. Note that the include must be called before anything is printed to the page. This is so any sessions and cookies can be properly stored in the header.

Example:
-----------------


`<?php
    /* NO HTML ABOVE THIS */
    include 'userauth.php';
?>
<html>`


Usage - creating users
-----------------
Users can be created by calling `$u->newUser(username, password);`. This will return true if the user was created successfully, or false if the username was already taken.

Example:
-----------------


`echo ($u->newUser($_POST['user'], $_POST['pass']) ? "User created." : "Username taken.";`

Usage - logging in
-----------------
You can log a user in by calling $u->login(username, password, [stayloggedin=false]). This will automatically authenticate and create the session data for the user. It will return false if authentication fails. If you set `stayloggedin=true`, it will also create cookies to keep the user logged in every time they visit. However, if you choose to use this feature, you will need to place `$u->cookielogin();` directly below your userauth.php include call.


`echo ($u->login($_POST['user'], $_POST['pass'], true)) ? "Logged in!" : "Login failed.";`

Other
-----------------
`$u->isloggedin()` will return true if there is an active session
`$u->logout();` will end the current session
`$u->updatePassword(username, currentpassword, newpassword)` will update the users password and return true if successful