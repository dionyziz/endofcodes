<ul>
    <?php
        foreach ( $unittest->tests as $test ) {
            ?><li><?php
            echo htmlspecialchars( $test->methodName );
            ?>: <?php
            if ( $test->success ) {
                ?><span class='pass'>PASS</span><?php
            }
            else {
                ?><span class='fail'>FAIL:</span> <?php
                echo htmlspecialchars( $test->error );

                ?><table class='calltrace'>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Line</th>
                            <th>Function</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( $test->calltrace as $call ) {
                            ?><tr><?php
                                ?><td><?php
                                    if ( isset( $call[ 'file' ] ) ) {
                                        echo htmlspecialchars( $call[ 'file' ] );
                                    }
                                ?></td>
                                <td><?php
                                    if ( isset( $call[ 'line' ] ) ) {
                                        echo htmlspecialchars( $call[ 'line' ] );
                                    }
                                ?></td>
                                <td><?php
                                    echo htmlspecialchars( $call[ 'function' ] );
                                    ?>(<?php
                                    $outputargs = array();
                                    foreach ( $call[ 'args' ] as $arg ) {
                                        if ( is_scalar( $arg ) ) {
                                            $outputargs[] = $arg;
                                        }
                                        else if ( is_array( $arg ) ) {
                                            $outputargs[] = '[array]';
                                        }
                                        else if ( is_object( $arg ) ) {
                                            $outputargs[] = '[object]';
                                        }
                                        else {
                                            $outputargs[] = '[unknown]';
                                        }
                                    }
                                    echo implode( ', ', array_map( 'htmlspecialchars', $outputargs ) );
                                    ?>)<?php
                                ?></td>
                            </tr><?php
                        }
                        ?>
                    </tbody>
                </table><?php
            }
            ?></li><?php
        }
    ?>
</ul>
