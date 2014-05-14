<?php
    require_once 'models/game.php';
    require_once 'models/round.php';

    class GraderSerializer {
        public static function gameRequestParams( Game $game ) {
            assert( $game->id > 0 );
            $gameid = $game->id;
            $W = $game->width;
            $H = $game->height;
            $M = $game->creaturesPerPlayer;
            $MAX_HP = $game->maxHp;
            $players = GraderSerializer::serializeUserList( $game->users );
            return compact( 'gameid', 'W', 'H', 'M', 'MAX_HP', 'players' );
        }
        public static function roundRequestParams( Round $roundObject, User $user, Game $game ) {
            $round = $roundObject->id;
            $map = GraderSerializer::serializeCreatureList( $roundObject->creatures );
            $W = $game->width;
            $H = $game->height;
            $gameid = $game->id;
            $myid = $user->id;

            return compact( 'round', 'map', 'myid', 'gameid', 'H', 'W' );
        }
        public static function serializeRoundList( Array $rounds ) {
            $ret = [];
            foreach ( $rounds as $round ) {
                $ret[ $round->id ] = GraderSerializer::flattenCreatures( $round->creatures );
            }
            return json_encode( $ret );
        }
        public static function serializeUserList( Array $users ) {
            $flattenedUsers = array_map( [ 'GraderSerializer', 'flattenUser' ], array_values( $users ) );

            return json_encode( $flattenedUsers );
        }
        public static function serializeCreatureList( Array $creatures ) {
            $flattenedCreatures = GraderSerializer::flattenCreatures( $creatures );

            return json_encode( $flattenedCreatures );
        }
        public static function flattenCreatures( Array $creatures ) {
            return array_map( [ 'GraderSerializer', 'flattenCreature' ], array_values( $creatures ) );
        }
        public static function flattenUser( User $user ) {
            $username = $user->username;
            $userid = $user->id;

            return compact( 'username', 'userid' );
        }
        public static function flattenCreature( Creature $creature ) {
            $hp = $creature->hp;
            $x = $creature->locationx;
            $y = $creature->locationy;
            $userid = $creature->user->id;
            $creatureid = $creature->id;

            return compact( 'creatureid', 'userid', 'x', 'y', 'hp' );
        }
    }
?>
