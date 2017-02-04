<?PHP
require_once(__DIR__ . '/utils_php/auth.php');
if($_SERVER['REQUEST_METHOD'] === 'GET') {
  $me = who_is_logged_in();
  if($me === false) {
?>
<!-- PUT LOGIN PAGE HERE -->
LOGIN_PAGE
<form action="login.php" method="post">
  <input type="hidden" name="action" value="login"></input>
  <input type="input" name="username"></input>
  <input type="input" name="password"></input>
  <input type="submit" name="Login"></input>
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
