<?php
function issetShield( &$variable ) {
	if ( !isset( $variable ) ) {
		$variable = '';	
	}
	return $variable;
}
?>