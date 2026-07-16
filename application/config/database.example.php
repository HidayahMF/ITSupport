<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Database Configuration Template
|--------------------------------------------------------------------------
| Copy this file to database.php and fill in your actual credentials.
| database.php is gitignored and will NOT be pushed to the repository.
*/

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'YOUR_HOST',
    'username' => 'YOUR_USERNAME',
    'password' => 'YOUR_PASSWORD',
    'database' => 'YOUR_DB',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

$db['sqlServer'] = array(
    'dsn' => '',
    'hostname' => 'sqlsrv:Server=YOUR_HOST;Database=YOUR_DB',
    'username' => 'YOUR_USERNAME',
    'password' => 'YOUR_PASSWORD',
    'database' => 'YOUR_DB',
    'dbdriver' => 'pdo',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => FALSE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
