<?php
    function a( $koko ) {
        b( 'lalalala' );
    }

    function b( $lala ) {
        c( 'malamlamala' );
    }

    function c( $malakia ) {
        throw new Exception( 'kot' );
    }
    
    try {
        a( 'kokokoko' );
    }
    catch ( Exception $e ) {
        echo "Our ship is sinking! ";
        echo htmlspecialchars( $e->getMessage() );
        echo "<br />";

        $trace = $e->getTrace();
        ?><table>
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Line</th>
                    <th>Function</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $trace as $call ) {
                    ?><tr><?php
                        ?><td><?php
                            echo htmlspecialchars( $call[ 'file' ] );
                        ?></td>
                        <td><?php
                            echo htmlspecialchars( $call[ 'line' ] );
                        ?></td>
                        <td><?php
                            echo htmlspecialchars( $call[ 'function' ] );
                        ?></td>
                    </tr><?php
                }
                ?>
            </tbody>
        </table><?php
    }
?>
