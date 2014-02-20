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
        public static function roundRequestParams( Round $roundObject ) {
            $round = $roundObject->id;
            $map = GraderSerializer::serializeCreatureList( $roundObject->creatures );

            return compact( 'round', 'map' );
        }
        public static function serializeUserList( $users ) {
            $flattenedUsers = array_map( [ 'GraderSerializer', 'flattenUser' ], array_values( $users ) );

            return json_encode( $flattenedUsers );
        }
        public static function serializeCreatureList( $creatures ) {
            $flattenedCreatures = array_map( [ 'GraderSerializer', 'flattenCreature' ], array_values( $creatures ) );

            return json_encode( $flattenedCreatures );
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
