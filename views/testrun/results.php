<?php
    include 'views/header.php';
?>

<div class='breadcrumbs'>
    <a href='testrun/create'>&laquo; all tests</a>
</div>

<strong><?php
    echo pluralize( count( $unittests ), 'unit test' );
?></strong>

<?php
    foreach ( $unittests as $unittest ) {
        ?><h2>Unit test <?php
        echo htmlspecialchars( $unittest->testName );
        ?></h2>

        <strong><?php
            echo pluralize( count( $unittest->tests ), 'test method' );
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
                                include 'views/testrun/calltrace.php';
                            }
                            ?></td>
                        </tr><?php
                    }
                ?>
            </table>
        </div><?php
    }
    include 'views/footer.php';
?>
