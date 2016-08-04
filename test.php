<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'directadminapi.class.php';
$directadminapi = new DirectAdminAPI();

$directadminapi->host = 'radhost.net';
$directadminapi->user = 'radhost';
$directadminapi->pass = 'radhost123456';

if ($directadminapi->login()) {

	$directadminapi->domain = 'radhost.net';

	/*if ($create = $directadminapi->file_delete('/domains/radhost.net/public_html/delete-me.txt'))
		var_dump($directadminapi->response_text);
	else
		echo 'error creating file.';*/

	$directadminapi->dir_make('/domains/radhost.net/public_html', 'zzz');

} else
	echo 'Incorrect login information';

?>