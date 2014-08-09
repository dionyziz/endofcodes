var express = require( 'express' );
var http = require( 'http' );
var assert = require( 'assert' );
var bodyParser = require( 'body-parser' );
var multer = require( 'multer' );

var app = express();

app.use( bodyParser.urlencoded( {
    extended: true
} ) );

app.use( multer( {
    dest: './uploads/'
} ) );

app.listen( 8000 );

app.get( '/bot', function( req, res ) {
    res.json( {
        botname: 'sample_botname',
        version: '0.1.0',
        username: 'sample_username'
    } );
} );

app.post( '/game', function( req, res ) {
    res.json( {} );
} );

app.post( '/round', function( req, res ) {
    function randomDirection( array ) {
        var directions = [ 'NORTH', 'EAST', 'SOUTH', 'WEST' ];
        return directions[ array[ Math.floor( Math.random() * array.length ) ] ];
    }

    var round = req.body.round;
    var map = JSON.parse( req.body.map );
    var gameid = req.body.gameid;
    var myid = req.body.myid;
    var W = req.body.W;
    var H = req.body.H;
    var intent = [];

    for ( var i = 0, len = map.length; i < len; ++i ) {
        var creature = map[ i ];
        if ( creature.userid == myid && creature.hp > 0 ) {
            var x = creature.x, y = creature.y, hp = creature.hp;
            var attacks = [], moves = [];
            var offsets = [
                [ 0, 1 ],
                [ 1, 0 ],
                [ 0, -1 ],
                [ -1, 0 ]
            ];

            for ( var j = 0; j < offsets.length; ++j ) {
                var newX = parseInt( x, 10 ) + offsets[ j ][ 0 ], newY = parseInt( y, 10 ) + offsets[ j ][ 1 ];

                if ( newX >= 0 && newX < W && newY >= 0 && newY < H ) {
                    var creatureFound = false;

                    for ( var z = 0; z < len; ++z ) {
                        if ( map[ z ].x == newX && map[ z ].y == newY ) {
                            creatureFound = true;

                            if ( map[ z ].userid != myid && map[ z ].hp > 0 ) {
                                attacks.push( j );
                            }
                        }
                    }

                    if ( !creatureFound ) {
                        moves.push( j );
                    }
                }
            }

            if ( attacks.length ) {
                intent.push( {
                    'creatureid': creature.creatureid,
                    'action': 'ATTACK',
                    'direction': randomDirection( attacks )
                } );
            }
            else {
                if ( !moves.length || Math.random() < 0.5 ) {
                    intent.push( {
                        'creatureid': creature.creatureid,
                        'action': 'NONE',
                        'direction': 'NONE'
                    } );
                }
                else {
                    intent.push( {
                        'creatureid': creature.creatureid,
                        'action': 'MOVE',
                        'direction': randomDirection( moves )
                    } );
                }
            }
        }
    }

    res.json( {
        'intent': intent
    } );
} );

module.exports = app;
