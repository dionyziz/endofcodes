<?php
    require 'views/header.php';

    if ( !empty( $error ) ) {
        ?><h2>Database Error: <?php
            echo htmlspecialchars( $error );
        ?></h2><?php
            if ( !empty( $dbSaid ) ) {
                ?><p class="alert alert-danger">MySQL said: <?php
                    echo htmlspecialchars( $dbSaid );
                ?></p><?php
            }
        ?><div id="dbconfig">
        <p>EndOfCodes is unable to run because you have not configured your database. We will now create the file config-local.php for you. Please enter your database credentials below: </p><?php
    }
    $form = new Form( 'dbconfig', 'create' );
    $form->output( function( $self ) use ( $oldConfig ) {
        $self->createLabel( 'user', 'Database User' );
        $self->createInput( 'text', 'user', '', $oldConfig[ 'db' ][ 'user' ], [ 'class' => 'form-control' ] );

        $self->createLabel( 'user', 'Database Password' );
        $self->createInput( 'text', 'pass', '', $oldConfig[ 'db' ][ 'pass' ], [ 'class' => 'form-control' ] );

        $self->createLabel( 'user', 'Database Name' );
        $self->createInput( 'text', 'dbname', '', $oldConfig[ 'db' ][ 'dbname' ], [ 'class' => 'form-control' ] );

        $self->createSubmit( 'Create Configuration', [ 'class' => 'btn btn-primary' ] );
    } );
?></div>
<?php
    require 'views/footer/view.php';
?>
