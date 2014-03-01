<?php
    echo "\n\n";
    echo "================\n";
    echo pluralize( count( $unittests ), 'unit test' ) . "\n";
    echo "================\n\n";
?>
<?php
    foreach ( $unittests as $unittest ) {
        echo "\n";
        ?>## Unit test: <?php
        echo $unittest->testName;
        ?>, with <?php
            echo pluralize( count( $unittest->tests ), 'test method' );
        ?> ##<?php
        echo "\n\n";

        foreach ( $unittest->tests as $test ) {
            echo $test->methodName . ":\n";
            if ( $test->success ) {
                ?>PASS - <?php
                echo pluralize( $test->assertCount, 'assertion' );
                echo "\n";
            }
            else {
                ?>FAIL: <?php
                echo $test->error;
                echo "\n";
                require 'views/testrun/calltrace.txt.php';
            }
        }
    }
?>
