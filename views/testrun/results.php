<?php
    include 'views/header.php';
?>

<div class='breadcrumbs'>
    <a href='testrun/create'>&laquo; all tests</a>
</div>

<h2>Unit test <?php
echo htmlspecialchars( $name );
?></h2>

<strong><?php
    echo pluralize( count( $unittest->tests ), 'test' );
?></strong>

<div class='unittest'>
    <table class='results'>
        <?php
            foreach ( $unittest->tests as $test ) {
                ?><tr>
                    <td><?php
                    echo htmlspecialchars( $test->methodName );
                    ?>
                    </td><td>
                    <?php
                    if ( $test->success ) {
                        ?><span class='pass'>PASS</span> - <?php
                        echo pluralize( $test->assertCount, 'assertion' );
                    }
                    else {
                        ?><span class='fail'>FAIL:</span> <?php
                        echo htmlspecialchars( $test->error );

                        ?><table class='calltrace'>
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th class='linenr'>Line</th>
                                    <th>Function</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ( $test->calltrace as $call ) {
                                    ?><tr><?php
                                        ?><td><?php
                                            if ( isset( $call[ 'file' ] ) ) {
                                                $file = $call[ 'file' ];
                                                if ( substr( $file, 0, strlen( $config[ 'root' ] ) ) == $config[ 'root' ] ) {
                                                    $file = substr( $file, strlen( $config[ 'root' ] ) );
                                                }
                                                if ( substr( $file, -strlen( '.php' ) ) == '.php' ) {
                                                    $file = substr( $file, 0, -strlen( '.php' ) );
                                                    echo htmlspecialchars( $file );
                                                    ?><span class='extension'>.php</span><?php
                                                }
                                                else {
                                                    echo htmlspecialchars( $file );
                                                }
                                            }
                                        ?></td>
                                        <td class='linenr'><?php
                                            if ( isset( $call[ 'line' ] ) ) {
                                                echo htmlspecialchars( $call[ 'line' ] );
                                            }
                                        ?></td>
                                        <td>
                                            <span class='function'><?php
                                            echo htmlspecialchars( $call[ 'function' ] );
                                            ?></span><span class='delimiter'>(</span><?php
                                            $outputargs = [];
                                            foreach ( $call[ 'args' ] as $arg ) {
                                                ob_start();
                                                include 'views/testrun/variable.php';
                                                $outputargs[] = ob_get_clean();
                                            }
                                            if ( count( $outputargs ) ) {
                                                echo " " . implode( ', ', $outputargs ) . " ";
                                            }
                                            ?><span class='delimiter'>)</span><?php
                                        ?></td>
                                    </tr><?php
                                }
                                ?>
                            </tbody>
                        </table><?php
                    }
                    ?></td>
                </tr><?php
            }
        ?>
    </table>
</div>
<?php
    include 'views/footer.php';
?>
