<?php
    class BotTest extends UnitTestWithFixtures {
        public function testSendGameRequest() {
            ob_start();
            include 'bots/php/game.php';
            $response = json_decode( ob_get_clean() );

            $this->assertTrue( $response !== NULL, 'The game request response must have valid json' );

            $this->assertTrue( is_array( $response ), 'The game request response must be an array' );
            $this->assertTrue( empty( $response ), 'The game request response must be an empty array' );
        }
        public function testSendBotRequest() {
            ob_start();
            include 'bots/php/bot.php';
            $response = json_decode( ob_get_clean() );

            $this->assertTrue( $response !== NULL, 'The bot request response must have valid json' );

            $this->assertTrue( is_object( $response ), 'The game request response must be an object' );
            $this->assertEquals( 3, count( get_object_vars( $response ) ), 'The bot request response must be an object with 3 attributes' );

            $this->assertEquals( 'sample_botname', $response->botname, 'The bot request response must have the correct botname' );
            $this->assertEquals( 'sample_username', $response->username, 'The bot request response must have the correct username' );
            $this->assertEquals( '0.1.0', $response->version, 'The bot request response must have the correct version' );
        }
        protected function roundRequestAndGetResponse( $postFields ) {
            $_POST = $postFields;
            ob_start();
            include 'bots/php/round.php';
            return json_decode( ob_get_clean() );
        }
        public function testSendRoundRequestNoCreatures() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 1,
                'H' => 1
            ] );

            $this->assertTrue( $response !== NULL, 'The round request response must have valid json' );
            $this->assertTrue( isset( $response->intent ), 'The round request response must have intent set' );
            $this->assertEquals( 0, count( $response->intent ), 'When no creatures are given the user must respond with an empty array' );
        }
        public function testSendRoundRequestOtherUsersCreatures() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 2,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 2,
                        'x' => 2,
                        'y' => 3,
                        'hp' => 100
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            $this->assertTrue( isset( $response->intent ), 'The round request response must have intent set' );
            $this->assertTrue( is_array( $response->intent ), 'Intent must be an array' );
            $this->assertEquals( 0, count( $response->intent ), 'When no creatures that belong to the user are given the user must respond with an empty array' );
        }
        public function testSendRoundRequestAttackNeighbor() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 2,
                        'x' => 1,
                        'y' => 2,
                        'hp' => 100
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            $this->assertEquals( 1, count( $response->intent ), 'When a user has a creature intent must have 1 field' );
            $this->assertTrue( is_object( $response->intent[ 0 ] ), 'Each intent element must be a dictionary' );
            $this->assertEquals( 1, $response->intent[ 0 ]->creatureid, 'Response must have a valid creatureid' );
            $this->assertEquals( 'ATTACK', $response->intent[ 0 ]->action, 'Response must have a valid action' );
            $this->assertEquals( 'NORTH', $response->intent[ 0 ]->direction, 'Response must have a valid direction' );
        }
        public function testSendRoundRequestMultipleEnemyNeightbors() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 2,
                        'x' => 1,
                        'y' => 2,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 3,
                        'userid' => 2,
                        'x' => 1,
                        'y' => 0,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 4,
                        'userid' => 2,
                        'x' => 0,
                        'y' => 1,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 5,
                        'userid' => 2,
                        'x' => 2,
                        'y' => 1,
                        'hp' => 100
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );
            $this->assertEquals( 1, $response->intent[ 0 ]->creatureid, 'Response must have a valid creatureid' );
            $this->assertEquals( 'ATTACK', $response->intent[ 0 ]->action, 'Response must have a valid action' );
            $validDirections = [ 'NORTH', 'WEST', 'SOUTH', 'EAST' ];
            $directionValid = array_search( $response->intent[ 0 ]->direction, $validDirections );
            $this->assertTrue( is_int( $directionValid ), 'Response must have a valid direction' );
        }
        public function testSendRoundRequestNoNeighbors() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 100
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            if ( isset( $response->intent[ 0 ] ) ) {
                $this->assertEquals( 'MOVE', $response->intent[ 0 ]->action, 'Response must have a valid action' );
                $validDirections = [ 'NORTH', 'WEST', 'SOUTH', 'EAST' ];
                $directionValid = array_search( $response->intent[ 0 ]->direction, $validDirections );
                $this->assertTrue( is_int( $directionValid ), 'Response must have a valid direction' );
            }
        }
        public function testAttackDeadCreature() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 100
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 2,
                        'x' => 2,
                        'y' => 1,
                        'hp' => 0
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            if ( isset( $response->intent[ 0 ] ) ) {
                $this->assertTrue( $response->intent[ 0 ]->action !== 'ATTACK', 'A creature must not try and attack a dead creature' );
            }
        }
        public function testAttackWithDeadCreature() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 0
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 2,
                        'x' => 2,
                        'y' => 1,
                        'hp' => 10
                    ]
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            $this->assertTrue( empty( $response->intent ), 'The bot should not try to attack with dead creatures' );
        }
        public function testResponseEmptyWhenNotMoving() {
            $response = $this->roundRequestAndGetResponse( [
                'round' => 1,
                'map' => json_encode( [
                    [
                        'creatureid' => 1,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 1,
                        'hp' => 2
                    ],
                    [
                        'creatureid' => 2,
                        'userid' => 1,
                        'x' => 2,
                        'y' => 1,
                        'hp' => 10
                    ],
                    [
                        'creatureid' => 3,
                        'userid' => 1,
                        'x' => 0,
                        'y' => 1,
                        'hp' => 2
                    ],
                    [
                        'creatureid' => 4,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 0,
                        'hp' => 2
                    ],
                    [
                        'creatureid' => 5,
                        'userid' => 1,
                        'x' => 1,
                        'y' => 2,
                        'hp' => 2
                    ],
                ] ),
                'gameid' => 1,
                'myid' => 1,
                'W' => 100,
                'H' => 100
            ] );

            $hasIntent = false;
            foreach ( $response->intent as $creatureIntent ) {
                if ( $creatureIntent->creatureid == 1 ) {
                    $hasIntent = true;
                }
            }
            $this->assertFalse( $hasIntent, 'If a creature can not move or attack an intent must not be given' );
        }
    }
    return new BotTest();
?>
