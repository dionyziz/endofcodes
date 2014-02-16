<?php
    include_once 'migrate.php';

 	Migration::createTable(
		'data', 
		[
			'country' => 'text COLLATE utf8_unicode_ci NOT NULL',
			'shortname' => 'text COLLATE utf8_unicode_ci NOT NULL'
		]
	);  
?>
