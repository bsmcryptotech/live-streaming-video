<?php
/**
 * Redirect to /edit-profile.php
 */
session_start();
require('config.php');

$query_string = ( ! empty($_SERVER['QUERY_STRING'])) ? '?'. $_SERVER['QUERY_STRING'] : '';

header("HTTP/1.1 301 Moved Permanently");
header("Location: ". _URL ."/edit-profile.php". $query_string);

exit();