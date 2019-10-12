<?php
/**
 * Redirect to /contact.php
 */
session_start();
require('config.php');

$query_string = ( ! empty($_SERVER['QUERY_STRING'])) ? '?'. $_SERVER['QUERY_STRING'] : '';

header("HTTP/1.1 301 Moved Permanently");
header("Location: ". _URL ."/contact.php". $query_string);

exit();