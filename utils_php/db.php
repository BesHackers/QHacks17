<?PHP
  require_once(__DIR__ . '/config.php');

  $opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ];

  $db = new PDO('mysql:host=' . config\mysql_host . ';dbname=' . config\mysql_db_name . ';charset=utf8', config\mysql_user, config\mysql_pass, $opts);
?>
