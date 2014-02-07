<?php
    include_once 'models/grader/serializer.php';

    class SerializerTest extends UnitTestWithFixtures {
        public function testGameSerialize() {
            $game = $this->buildGame();
            $game->genesis();

            $this->assertTrue( method_exists( 'GraderSerializer', "gameRequestParams" ), 'GraderSerializer must have a "gameRequestParams" function' );

            $requestParams = GraderSerializer::gameRequestParams( $game );

            $this->assertTrue( isset( $requestParams[ 'gameid' ] ), 'gameid must exist in exported request params' );
            $this->assertEquals( $game->id, $requestParams[ 'gameid' ], 'gameid must be encoded properly to request params' );
            
            $this->assertTrue( isset( $requestParams[ 'W' ] ), 'W must exist in exported request params' );
            $this->assertEquals( $game->width, $requestParams[ 'W' ], 'W must be encoded properly to request params' );

            $this->assertTrue( isset( $requestParams[ 'H' ] ), 'H must exist in exported request params' );
            $this->assertEquals( $game->height, $requestParams[ 'H' ], 'H must be encoded properly to request params' );

            $this->assertTrue( isset( $requestParams[ 'M' ] ), 'M must exist in exported request params' );
            $this->assertEquals( $game->creaturesPerPlayer, $requestParams[ 'M' ], 'M must be encoded properly to request params' );

            $this->assertTrue( isset( $requestParams[ 'MAX_HP' ] ), 'MAX_HP must exist in exported request params' );
            $this->assertEquals( $game->maxHp, $requestParams[ 'MAX_HP' ], 'MAX_HP must be encoded properly to request params' );

            $players = json_decode( $requestParams[ 'players' ] );

            $this->assertTrue( isset( $requestParams[ 'players' ] ), 'players must exist in exported request params' );
            $this->assertTrue( is_array( $players ), 'players must be an array in exported request params' );
            $this->assertEquals( 4, count( $players ), 'players must contain correct number of users in exported request params' );

            $this->assertEquals( 1, $players[ 0 ]->userid, 'All players must exist in exported request params' );
            $this->assertEquals( 2, $players[ 1 ]->userid, 'All players must exist in exported request params' );
            $this->assertEquals( 3, $players[ 2 ]->userid, 'All players must exist in exported request params' );
            $this->assertEquals( 4, $players[ 3 ]->userid, 'All players must exist in exported request params' );
        }
        public function testRoundSerialize() {
            $round = $this->buildRound();

            $this->assertTrue( method_exists( 'GraderSerializer', "roundRequestParams" ), 'GraderSerializer must have a "roundRequestParams" function' );

            $requestParams = GraderSerializer::roundRequestParams( $round );

            $this->assertTrue( isset( $requestParams[ 'round' ] ), 'roundid must exist in exported request params' );
            $this->assertEquals( $round->id, $requestParams[ 'round' ], 'roundid must be encoded properly to request params' );

            $map = json_decode( $requestParams[ 'map' ] );
            $this->assertTrue( isset( $requestParams[ 'map' ] ), 'map must exist in exported request params' );
            $this->assertTrue( is_array( $map ), 'map must be an array in exported request params' );
            $this->assertEquals( 2, count( $map ), 'map must contain correct number of creatures in exported request params' );

            $this->assertEquals( 1, $map[ 0 ]->creatureid, 'All creatures must exist in exported request params' );
            $this->assertEquals( 2, $map[ 1 ]->creatureid, 'All creatures must exist in exported request params' );
        }
        public function testFlattenUser() {
            $user = $this->buildUser( 'vitsalis' );

            $this->assertTrue( method_exists( 'GraderSerializer', "flattenUser" ), 'GraderSerializer must have a "flattenUser" function' );

            $flattenedUser = GraderSerializer::flattenUser( $user );

            $this->assertTrue( isset( $flattenedUser[ 'username' ] ), 'username must exist in exported flattened data' ); 
            $this->assertEquals( $user->username, $flattenedUser[ 'username' ], 'username must be encoded properly to flattened data' );

            $this->assertTrue( isset( $flattenedUser[ 'userid' ] ), 'userid must exist in exported flattened data' ); 
            $this->assertEquals( $user->id, $flattenedUser[ 'userid' ], 'userid must be encoded properly to flattened data' );
        }
        public function testSerializeUserList() {
            $userList = array( $this->buildUser( 'vitsalis' ), $this->buildUser( 'dionyziz' ) );

            $this->assertTrue( method_exists( 'GraderSerializer', "serializeUserList" ), 'GraderSerializer must have a "serializeUserList" function' );

            $json = GraderSerializer::serializeUserList( $userList ); 
            $data = json_decode( $json );
            
            $this->assertTrue( is_array( $data ), 'Data returned from decoded json must be an array' );
            $this->assertEquals( count( $userList ), count( $data ), 'Data must have the same number of users as userlist has' );

            $this->assertEquals( $userList[ 0 ]->id, $data[ 0 ]->userid, 'All users must be serialized' );
            $this->assertEquals( $userList[ 1 ]->id, $data[ 1 ]->userid, 'All users must be serialized' );
        }
    }

    return new SerializerTest();
?>
