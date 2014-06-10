<?php
    class CodeFormatTest extends UnitTest {
        public function testNoRequireOnceForViews() {
            $requireOnceInViews = shell_exec( "find . -iname '*.php'|xargs grep -n 'require_once'|grep -v codeformat|grep views" );
            $this->assertEquals( 0, strlen( $requireOnceInViews ), "Warning: 'require_once' used for including view files. Use 'require' instead.\n" . $requireOnceInViews );
        }
    }

    return new CodeFormatTest();
?>
