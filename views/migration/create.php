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
    ?><p>Last Migrations:</p><?php
        if( isset( $last ) ) {
            ?><table class="table">
                <tbody>
                    <tr><?php
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
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $list ) {
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
        $self->createSubmit( "Run Migrations that haven't been executed" );
    } );

    require 'views/footer.php';
?>
