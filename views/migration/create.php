<?php
    require 'views/header.php';

    $allMigrations = array_combine( $allMigrations, $allMigrations );
    $environments = array_combine( $environments, $environments );

    ?><h2>Last Migrations Run</h2><?php
    if ( !empty( $lastMigrationRun ) ) {
        ?><table class="table">
            <tbody>
                <?php
                    foreach ( $lastMigrationRun as $environment => $migration ) {
                        ?><tr>
                            <td><?php
                                echo $environment;
                            ?></td>
                            <td><?php
                                echo $migration;
                        ?></tr><?php
                    }
            ?></tbody>
        </table><?php
    }
    else {
        ?><p>You have not created any logs yet.</p><?php
    }

    ?><h2>Run a migration:</h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $allMigrations, $environments ) {
        $self->createSelect( $allMigrations, 'name' );
        $self->createSelect( $environments, 'environment' );
        $self->createSubmit( 'Run migration' );
    } );

    ?><h2>Pending Migrations</h2><?php
    foreach ( $pending as $environment => $migrations ) {
        ?><h3><?php
            echo $environment;
        ?></h3>
        <p>Total: <?php
            echo count( $allMigrations );
        ?> Pending: <?php
            echo count( $migrations );
        ?></p>
        <ol><?php
            foreach ( $migrations as $migration ) {
                ?><li><?php
                    echo $migration;
                ?></li><?php
            }
        ?></ol><?php
    }

    ?><h2>Run all pending migrations:</h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $environments ) {
        $self->createSelect( $environments, 'environment' );
        $self->createSubmit( "Run pending migrations" );
    } );

    require 'views/footer/view.php';
?>
