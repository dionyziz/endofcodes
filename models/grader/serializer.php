<?php
    include_once 'models/game.php';
    include_once 'models/round.php';

    class GraderSerializer {
        public static function gameRequestParams( $game ) {
            $gameid = $game->id;
            $W = $game->width;
            $H = $game->height;
            $M = $game->creaturesPerPlayer;
            $MAX_HP = $game->maxHp;
            $players = GraderSerializer::serializeUserList( $game->users );

            return compact( 'gameid', 'W', 'H', 'M', 'MAX_HP', 'players' );
        }
        public static function roundRequestParams( $roundObject ) {
            $round = $roundObject->id;
            $map = GraderSerializer::serializeCreatureList( $roundObject->creatures );

            return compact( 'round', 'map' );
        }
        public static function serializeUserList( $users ) {
            $flattenedUsers = array_map( array( 'GraderSerializer', 'flattenUser' ), $users );

            return json_encode( $flattenedUsers );
        }
        public static function serializeCreatureList( $creatures ) {
            $flattenedCreatures = array_map( array( 'GraderSerializer', 'flattenCreature' ), $creatures );

            return json_encode( $flattenedCreatures );
        }
        public static function flattenUser( $user ) {
            $username = $user->username;
            $userid = $user->id;

            return compact( 'username', 'userid' );
        }
        public static function flattenCreature( $creature ) {
            $hp = $creature->hp;
            $x = $creature->locationx;
            $y = $creature->locationy;
            $userid = $creature->user->id;
            $creatureid = $creature->id;

            return compact( 'creatureid', 'userid', 'x', 'y', 'hp' );
        }
    }
?>
