<?php
    class ArrayHelperTest extends UnitTest {
        public function testArray_diff_recursive() {
            $array = [
                'name' => 'bugos',
                'technologies' => [
                    'c++' => 5,
                    'python' => 3
                ]
            ];
            $updatedArray = [
                'name' => 'bugos',
                'email' => 'bugos@gmail.com',
                'technologies' => [
                    'c++' => 5,
                    'python' => 3,
                    'php' => 3
                ]
            ];
            $this->assertSame( [
                    'email' => 'bugos@gmail.com',
                    'technologies' => [
                        'php' => 3
                    ]
                ],
                array_diff_recursive( $updatedArray, $array ),
                'array_diff_recursive() must return the entries in $updatedArray that does not exist in $array.'
            );

            $slicedArray = [
                'name' => 'bugos',
                'email' => NULL,
                'technologies' => [
                    'c++' => 5,
                    'python' => 3,
                    'php' => NULL
                ]
            ];
            $this->assertSame( [
                    'name' => 'bugos',
                    'technologies' => [
                        'c++' => 5,
                        'python' => 3
                    ]
                ],
                array_diff_recursive( $slicedArray ),
                'NULL entries must be deleted.'
            );
        }
    }

    return new ArrayHelperTest();
?>
