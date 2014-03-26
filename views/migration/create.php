<?php
    require 'views/header.php';

    foreach ( $migrations as $migration ) {
        $list[] = [
            'content' => $migration,
            'value' => $migration
        ];
    }
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $list ) {

        $self->createSelect( 'name', $list );
        $self->createSelect( 'env', [
            [
                'content' => 'development',
                'value' => 'development'
            ],
            [
                'content' => 'test',
                'value' => 'test'
            ]
        ] );
        $self->createSubmit( 'Run migration' );
    } );
    ?><h2>Last Migrations:</h2><?php
    if( isset( $last ) ) {
        ?><table class="table">
            <tbody>
                <?php
                    foreach ( $last as $key => $migration ) {
                        if ( empty( $migration ) ) {
                            $migration = 'unknown';
                        }
                        ?><tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo $migration; ?></td>
                        </tr><?php
                    }
            ?></tbody>
        </table><?php
    }
    else {
        ?><p>You have not created any logs yet.</p><?php
    }
    ?><h2>All Migrations <small>in case of new database</small></h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) {
        $self->createSelect( 'env', [
            [
                'content' => 'development',
                'value' => 'development'
            ],
            [
                'content' => 'test',
                'value' => 'test'
            ]
        ] );
        $self->createInput( 'hidden', 'all', 'all', true );
        $self->createSubmit( "Run all migrations" );
    } );
    ?><h2>Pending migrations</h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) {
        $self->createSelect( 'env', [
            [
                'content' => 'development',
                'value' => 'development'
            ],
            [
                'content' => 'test',
                'value' => 'test'
            ]
        ] );
        $self->createSubmit( "Run pending migrations" );
    } );
    foreach ( $pending as $env => $migs ) {
        ?><h3><?php echo $env; ?></h3><?php
        ?><p>Total: <?php echo count( $migrations ); ?> Pending: <?php echo count( $migs ); ?></p>
        <ol><?php
            foreach ( $migs as $mig ) {
                ?><li><?php echo $mig; ?></li><?php
            }
        ?></ol><?php
    }

    require 'views/footer.php';
?>
