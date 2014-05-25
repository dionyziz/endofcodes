<?php
    if ( !isset( $_SESSION[ 'user' ] ) || !$_SESSION[ 'user' ]->isDeveloper() ) {
        // show this information to developers only
        return;
    }
?>
<div class="dev"><?php
    /*
        $_SESSION[ 'debug' ] is:

        * not set if debugging is not configured
        * set to false if debugging is configured as disabled
        * set to true if debugging is configured as enabled
        * only developers can configure their debugging flag
    */
    if ( empty( $_SESSION[ 'debug' ] ) ) {
        // debugging is disabled; display link to enable it
        ?><a href='' title='Show profiling information' class='enable-profiling'>π</a><?php
        $form = new Form( 'profiling', 'update' );
        $form->id = 'profiling-form';
        $form->output( function( $self ) {
            $self->createInput( 'hidden', 'enable', '', 'true' );
        } );
    }
    else {
        // profiling is enabled; allow developer to view it
        ?>Δt = <a href='' class='profiling-link'><?php
            echo round( 1000 * $this->getPageGenerationTime(), 2 );
        ?>ms</a>
        
        <div class='debug-window modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h2 class='modal-title'>End of Codes Developer Console</h2>
                </div>
                <div class='debug-contents'>
                    <table class='table table-condensed'>
                        <tr><td colspan='3'>Page rendered in Δt = <?php
                            echo round( 1000 * $this->getPageGenerationTime(), 2 );
                        ?>ms (<a href='' class='disable-profiling'>disable profiling</a>)<?php
                        $form = new Form( 'profiling', 'update' );
                        $form->id = 'profiling-form';
                        $form->output( function( $self ) {
                            $self->createInput( 'hidden', 'enable', '', 'false' );
                        } );
                        ?></td></tr>
                        <tr><td colspan='3'><?php
                            global $debugger;

                            $numQueries = $debugger->getTotalQueriesExecuted();
                            
                            ?><strong><?php
                            echo pluralize( $numQueries, 'query' );
                            ?> executed.</strong> <?php
                            if ( $numQueries > 50 ) {
                                ?><span class="label label-danger">Too many</span><?php
                            }
                        ?></td></tr><?php
                            foreach ( $debugger->queryGroups as $queryGroup ) {
                                ?><tr<?php
                                if ( count( $queryGroup->queries ) > 10 ) {
                                    ?> class='danger'<?php
                                }
                                ?>><td class='num'><span class='badge'><?php
                                echo count( $queryGroup->queries );
                                ?></span></td><td><?php
                                echo htmlspecialchars( $queryGroup->queryLiteral );
                                ?></td></tr><?php
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div><?php
    }
?></div>
