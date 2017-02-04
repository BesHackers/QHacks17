<?PHP
require_once(__DIR__ . '/utils_php/auth.php');
if($_SERVER['REQUEST_METHOD'] === 'GET') {
  $me = who_is_logged_in();
  if($me === false) {
?>
<!-- PUT LOGIN PAGE HERE -->
LOGIN_PAGE
<form action="login.php" method="post">
 <!--div id="AvatarHolder">
									<img id="Avatar" src="Avatar.png" alt="Avatar" class="avatar">
								</div-->
								
								<img id="Avatar" src="img/Avatar.png" alt="Avatar" class="avatar">
								<div id="info_holder">
									</br>
									</br>
									<input  style="display:none;" name="submitType" value="login">
									<label>Username</label>
									<input class="formInput" type="text" placeholder="Enter Username" name="usr" required>
									</br>
									</br>
									<label>Password</label>
									<input class="formInput" type="password" placeholder="Enter Password" name="pass" required>
									</br>
									</br>
									<input type="checkbox" checked="checked"> Remember me
									<input type="submit" name="Login">
								</div>
</form>
<?PHP
  } else {
?>
<!-- PUT USER INFO PAGE HERE -->
USER_INFO
<?PHP
  }
} elseif($_SERVER['REQUEST_METHOD'] === "POST") {
  if($_POST['action'] === 'logout') {
    // Someone is logging out, log out.
    logout();
    header('Location: .');
    exit();
  } elseif($_POST['action'] === 'login') {
    if(!empty($_POST['username']) && !empty($_POST['password'])) {
      // Someone is logging in, log in.
      $status = login();
      if($status === true) {
        header('Location: .');
      } else {
        exit($status);
      }
    } else {
      exit("Please enter a username and password");
    }
  }
} else {
  http_response_code(501);
  exit("Invalid request method.");
}
