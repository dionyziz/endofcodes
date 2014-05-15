<?php
    require 'views/header.php';
?>
<h2>Database Error: <?php
    echo $error;
?></h2><p class="bg-danger">MySQL said: <?php
    echo $dbSaid;
?></p>
<p>EndOfCodes is unable to run because you have not configured your database.</p>
<p>We will now create the file config-local.php for you. Please enter your database credentials below: </p>
<?php
    $form = new Form( 'dbconfig', 'create' );
    $form->output( function( $self ) use ( &$configLocal ) {
        $self->createLabel( 'user', 'Database User' );
        $self->createInput( 'text', 'user', '', issetShield( $configLocal[ 'user' ] ), [ 'class' => 'form-control input-lg' ] );

        $self->createLabel( 'user', 'Database Password' );
        $self->createInput( 'text', 'pass', '', issetShield( $configLocal[ 'pass' ] ), [ 'class' => 'form-control input-lg' ] );

        $self->createLabel( 'user', 'Database Name' );
        $self->createInput( 'text', 'dbname', '', issetShield( $configLocal[ 'dbname' ] ), [ 'class' => 'form-control input-lg' ] );  

        $self->createSubmit( 'Create Configuration', [ 'class' => 'btn btn-primary' ] ); 
    } );

    require 'views/footer.php';
?>
