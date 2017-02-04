<?PHP namespace config;
/* MySQL server info */
const mysql_host = 'localhost';
const mysql_user = 'qhacks17';
const mysql_pass = 'changethis';
const mysql_db_name = 'qhacks17';

/* Password hashing settings (old passwords will be rehashed when the user next logs in) */
const password_algorithm = PASSWORD_DEFAULT;
const password_options = [
    'cost' => 10, // Default is 10
];
?>
