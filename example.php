<?php

/**
 * DirectAdmin API example
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'directadminapi.class.php';
$directadminapi = new DirectAdminAPI();

$directadminapi->host = 'mydomain.com';
$directadminapi->user = 'mydomainuser';
$directadminapi->pass = 'mydomainpass';

if ($directadminapi->login()) {

	$directadminapi->domain = 'mydomain.com';

	if ($add = $directadminapi->domain_pointer_add('newdomain.com'))
		echo 'domain pointer added successfully.';
	else
		echo 'error adding domain pointer.';

} else
	echo 'Incorrect login information';

?>