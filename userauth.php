<?php
	session_start();
	class userauth {
	
		//Choose a unique salt. The longer and more complex it is, the better.
		//Since the database is being stored in an unencrypted file, we need
		//strong protection on the passwords.
		
		private $salt = "@#6$%^&*THIS$%^&IS(&^A%^&LARGE^&**%^&SALT-8,/7%^&*";
		private $dbfile = "userauth.db";
		private $db = -1;
		
		public function dbinit() {
			if ($this->db == -1) 
				$this->db = sqlite_open($this->dbfile, 0666, $e) or die($e);
		}
		
		public function dbclose() {
			if ($this->db != -1) {
				sqlite_close($this->db);
				$db = -1;
			}
				
		}
		
		public function createTables() {
			$q = "CREATE TABLE users 
									(	uid INTEGER PRIMARY KEY,
										username varchar(255),
										password varchar(32),
										lastlogin int
									);";
			sqlite_exec($this->db, $q, $e) or 
			die("The database table already exists. Please remove createTables() from your code<br />".$e);
			
			die("The database and tables were created. Please remove createTables() from userauth.php now.");
		}
		
		public function login($user, $password, $stayloggedin = false) {
			$user = sqlite_escape_string($user);
			$password = md5($salt.$password);
			
			$q = "SELECT * FROM users WHERE username = '$user' AND password = '$password' LIMIT 1";
			$res = sqlite_query($this->db, $q, $e);
			if (sqlite_num_rows($res) > 0) {
				if ($stayloggedin) {
					
					$r = sqlite_fetch_array($res);
					setcookie("userid", $r['uid'], time() + 60 * 60 * 24 * 30);
					setcookie("pass", $password, time() + 60 * 60 * 24 * 30);
				}
				$_SESSION['uid'] = $r['uid'];
				$_SESSION['uname'] = $r['username'];
				$time = time();
				$id = $r['uid'];
				sqlite_exec($this->db, "UPDATE users SET lastlogin = '$time' WHERE uid = '$id'", $e);
				return true;
			}
			return false;
		}
		
		//Returns false if username is taken
		public function newUser($username, $password) {
			$user = sqlite_escape_string($username);
			$pass = md5($salt.$password);
			$time = time();
			$checkuser = sqlite_query($this->db, "SELECT uid FROM users WHERE username = '$user'", $e);
			if (sqlite_num_rows($checkuser) > 0)
				return false;
			$q = "INSERT INTO users VALUES (NULL, '$user', '$pass', '$time')";
			
			return sqlite_query($this->db, $q, $e) or die($e);
		}
		
		public function updatePassword($username, $cpass, $newpass) {
			if (!$this->login($username, $cpass)) return false;
			
			// escaping session data is probably unnecessary, but better safe than sorry
			$id = sqlite_escape_string($_SESSION['uid']); 
			$q = "UPDATE users SET password = '$newpass' WHERE uid = '$id' AND password = '$cpass'";
			return sqlite_query($this->db, $q, $e);
		}
		
		public function cookielogin() {
			if (isset($_COOKIE['userid']) && isset($_COOKIE['pass'])) {
				$id = sqlite_escape_string($_COOKIE['userid']);
				$pass = sqlite_escape_string($_COOKIE['pass']);
				$q = sqlite_query($this->db, "SELECT * FROM users WHERE uid = '$id' AND password = '$pass' LIMIT 1", $e);
				if (sqlite_num_rows($q) > 0) {
					$r = sqlite_fetch_array($q);
					$_SESSION['uid'] = $r['uid'];
					$_SESSION['uname'] = $r['username'];
				} 
				
			}
		}
		public function isloggedin() {
			return isset($_SESSION['uid']);
		}
		public function logout() {
			unset($_SESSION['uid']);
			session_destroy();
			setcookie("userid", "", time() - 60 * 60 * 24 * 30);
			setcookie("pass", "", time() - 60 * 60 * 24 * 30);
		}
		public function uname() {
			if ($this->isloggedin()) 
				return $_SESSION['uname'];
			else 
				return "NO LOGIN.";
		}
		
		
	}
	$u = new userauth();
	$u->dbinit();
	
?>