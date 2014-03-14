<?php
 	Migration::createTable(
		'countries', 
		[
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'country' => 'text COLLATE utf8_unicode_ci NOT NULL',
			'shortname' => 'text COLLATE utf8_unicode_ci NOT NULL'
		],
        [
            [ 'type' => 'primary', 'field' => 'id' ]
        ]
	);  
?>
