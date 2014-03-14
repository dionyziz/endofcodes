<?php
    require 'views/header.php';
?>
<?php
        foreach ( $migrations as $migration ) {
            $list[] = [
                'content' => $migration,
                'value' => $migration
            ];
        }
        $form = new Form( 'migration', 'create' );
        $form->output( function( $self ) use( $list ) {

            $self->createSelect( 'name', '', $list );
            $self->createSelect( 'env', 'env', [
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
?>
<?php
    require 'views/footer.php';
?>
