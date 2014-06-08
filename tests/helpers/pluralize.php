<?php
    class PluralizeTest extends UnitTest {
        public function testPluralize() {
            $this->assertEquals( '1 unit', pluralize( 1, 'unit' ), '1 should not be pluralized' );
            $this->assertEquals( '2 units', pluralize( 2, 'unit' ), '2 should be pluralized' );
            $this->assertEquals( '0 units', pluralize( 0, 'unit' ), '0 should be pluralized' );
            $this->assertEquals( '5 babies', pluralize( 5, 'baby' ), 'Nouns ending in -y must be pluralized as -ies' );
        }
    }

    return new PluralizeTest();
?>
