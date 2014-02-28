<?php
    require_once 'migrate.php';

 	Migration::createTable(
		'countries', 
		[
			'country' => 'text COLLATE utf8_unicode_ci NOT NULL',
			'shortname' => 'text COLLATE utf8_unicode_ci NOT NULL'
		]
	);  
?>
