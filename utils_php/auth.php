<?PHP
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/db.php');

/* Returns the user that is currently logged in */
function who_is_logged_in() {
  global $db;

  if(session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  $stmt = $db->prepare('SELECT * FROM users WHERE session_id=? LIMIT 1');
  $stmt->execute([session_id()]);
  return $stmt->fetch();
}

/* Returns true iff the account has admin access */
function is_admin($me) {
  return $me !== false && $me['status'] === '3';
}

/* Returns true iff the account is locked */
function is_locked($me) {
  return $me !== false && $me['status'] === '1';
}

/* Logs in as a user */
function login() {
  global $db;

  $username = $_POST['username'];
  $password = $_POST['password'];

  if(who_is_logged_in() !== null) {
      logout();
  }

  $stmt = $db->prepare('SELECT password_hash, status FROM users WHERE username=? LIMIT 1');
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if($user === false) {
      return 'Could not find user ' . htmlspecialchars($username);
  }

  if(password_verify($password, $user['password_hash']) === false) {
      return 'Incorrect password';
  }

  if($user['status'] === 1) {
      return 'Your account has been locked. Please contact the site owner to request access.';
  }

  if(password_needs_rehash($user['password_hash'], config\password_algorithm, config\password_options)) {
      $new_hash = password_hash($password, config\password_algorithm, config\password_options);
      $db->prepare('UPDATE users SET password_hash=? WHERE username=?')->execute([$new_hash, $user['username']]);
  }

  if(session_status() === PHP_SESSION_NONE)
  {
    session_start();
  }
  $db->prepare('UPDATE users SET session_id=? WHERE username=?')->execute([session_id(), $user['username']]);

  return true;
}

/* NULLS the currently logged in user's session_id */
function logout() {
    global $db;

    if(session_status() === PHP_SESSION_NONE)
    {
      session_start();
    }
    $db->prepare('UPDATE users SET session_id=NULL WHERE session_id=?')->execute([session_id()]);
}
//
// /* Generates a reset link for the given user */
// function generate_reset_for($id) {
// 	global $db;
//
// 	$db->beginTransaction();
//
// 	$reset_link = bin2hex(openssl_random_pseudo_bytes(128));
// 	$stmt = $db->prepare('UPDATE users SET reset_link=? WHERE username=?');
// 	$stmt->execute([$reset_link, $id]);
//
// 	$db->commit();
//
// 	return $reset_link;
// }
//
// /* Sends the reset link to the user it is for. Please ensure that a link headers
//    already been generated for the user. */
// function send_reset($reset_link) {
// 	global $db;
//
// 	$stmt = $db->prepare('SELECT email FROM users WHERE reset_link=?');
// 	$stmt->execute([$reset_link]);
// 	$email = $stmt->fetchColumn();
//
// 	$headers = 'Sender: ' . config\mail_from . "\r\n";
//   $headers = 'From: ' . config\mail_from . "\r\n";
// 	$body = 'Here is your password reset link: ' . config\full_web_address . '/login/reset.php?link=' . $reset_link;
// 	return mail($email, '[ShiftParts] Password Reset', $body, $headers);
// }

/* Checks if a password is valid */
function verify_password_requirements($pass) {
  // $valid = !empty($pass) &&
  //    preg_match('/^.{8,}$/', $pass) && // At least 8 characters
  //    preg_match('/[0-9]/', $pass) === 1 && // 1 number
  //    preg_match('/[^a-zA-Z 0-9]/', $pass) === 1; // 1 special character
  //
  // if($valid) {
  // 	$common = file(__DIR__ . '/common_passes.hidden'); // Load the 10000 most common passwords
  // 	$result = array_search($pass, $common, true);
  // 	if($result !== false) { // This will not match anything because the rules exclude all top 10000 passwords, but I'm leaving it in for later
  //     return 'Your password is the #' . ($result + 1) . ' most commonly used password';
  //   }
  //   return true;
  // } else {
  //   return 'Your password must contain at least 8 characters, 1 number and 1 special character';
  // }
  return true;
}

/* Changes a users password. $old_password must be the old password or true
  ONLY for a password reset. User is the target user. */
function change_password($new_password, $old_password, $user = null) {
    global $db;

    if($user === null) {
      $user = who_is_logged_in()['username'];
    }

    // Verify the old password
    if($old_password !== true) {
      $stmt = $db->prepare('SELECT password_hash FROM users WHERE session_id=?');
      $stmt->execute([session_id()]);
      $old_hash = $stmt->fetchColumn();
      if(password_verify($old_password, $old_hash) === false) {
          return 'Incorrect current password, your password has not changed.';
      }
    }

    // Verify the new password
    $result = verify_password_requirements($new_password);
    if($result !== true) {
      return $result;
    }

    // Insert the new password
    $new_hash = password_hash($new_password, config\password_algorithm, config\password_options);
    $stmt = $db->prepare('UPDATE users SET password_hash=?, reset_link=NULL WHERE username=?');
    $stmt->execute([$new_hash, $user]);
    if($stmt->rowCount() === 0) {
      var_dump([$new_hash, $user]);
      return 'Couldn\'t find user with given username.';
    }

    return true;
}

?>
