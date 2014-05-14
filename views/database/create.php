<?php
    require 'views/header.php';
?>
<h1>Database Error: <?php
	echo $error;
?></h1>
<p>EndOfCodes is unable to run because you have not configured your database.</p>
<p>We will now create the file config-local.php for you. Please enter your database credentials below: </p>
<?php
	$form = new Form( 'dbconfig', 'create' );
	?><?php
	$form->output( function( $self ) {
		$self->createInput( 'text', 'user', '', '', [ 'class' => 'form-control input-lg', 'placeholder' => 'Database User' ] );
		$self->createInput( 'text', 'pass', '', '', [ 'class' => 'form-control input-lg', 'placeholder' => 'Database Password' ] );
		$self->createInput( 'text', 'dbname', '', '', [ 'class' => 'form-control input-lg', 'placeholder' => 'Database Name' ] );	
		$self->createSubmit( 'Create Configuration', [ 'class' => 'btn btn-primary' ] ); 
		
	} );

    require 'views/footer.php';
?>
