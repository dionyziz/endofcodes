var request = require( 'supertest' );
var assert = require( 'assert' );
var app = require( '../lib/bot.js' );
var agent = request.agent( app );

function getAndCheckData( res, check ) {
    res.setEncoding( 'utf8' );

    var data = "";

    res.on( 'data', function( d ) {
        data += d;
    } );

    res.on( 'end', function() {
        check( data );
    } );
}

describe( 'bot request', function() {
    it( 'should return the bot credentials', function( done ) {
        agent
            .get( '/bot' )
            .expect( function( res ) {
                var expData = '{"botname":"sample_botname","version":"0.1.0","username":"sample_username"}';

                assert.strictEqual( expData, res.text );
            } )
            .end( done );
    } );
} );

describe( 'game request', function() {
    it( 'should return an empty object', function( done ) {
        agent
            .get( '/game' )
            .expect( function( res ) {
                var expData = '{}';

                assert.strictEqual( expData, res.text );
            } )
            .end( done );
    } );
} );

describe( 'round request', function() {
    function requestAndCheckResponse( map, validDirections, validActions, myid, numCreatures, done ) {
        //console.log( JSON.parse( JSON.stringify( map ) ) );
        agent
            .post( '/round' )
            .send( 'map=' + JSON.stringify( map ) +'&myid=1&W=10&H=10&round=1&gameid=1' )
            .expect( function( res ) {
                var intent = JSON.parse( res.text ).intent;

                assert.ok( intent );
                assert.strictEqual( intent.length, numCreatures );
                for ( var i = 0, len = intent.length; i < len; ++i ) {
                    assert.notEqual( validActions.indexOf( intent[ i ].action ), -1 );
                    if ( intent[ i ].action == 'NONE' ) {
                        assert.strictEqual( intent[ i ].direction, 'NONE' );
                    }
                    else {
                        assert.notEqual( validDirections.indexOf( intent[ i ].direction ), -1 );
                    }
                }
            } )
            .end( done );
    }

    it( "should return intents for only the user's creatures", function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 1,
                y: 1,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 2,
                x: 1,
                y: 2,
                hp: 100
            },
            {
                creatureid: 3,
                userid: 3,
                x: 1,
                y: 3,
                hp: 100
            }
        ];
        
        agent
            .post( '/round' )
            .send( 'map=' + JSON.stringify( map ) +'&myid=1&W=10&H=10&round=1&gameid=1' )
            .expect( function( res ) {
                var intent = JSON.parse( res.text ).intent;

                assert.ok( intent );
                assert.strictEqual( intent.length, 1 );
                assert.strictEqual( intent[ 0 ].creatureid, 1 );
            } )
            .end( done );
    } );

    it( 'should return an attack action when a creature is near it', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 1,
                y: 1,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 2,
                x: 1,
                y: 2,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'NORTH' ], [ 'ATTACK' ], 1, 1, done );
    } );

    it( 'should return an attack action when 2 creatures are near it', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 1,
                y: 1,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 2,
                x: 1,
                y: 2,
                hp: 100
            },
            {
                creatureid: 3,
                userid: 3,
                x: 2,
                y: 1,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'NORTH', 'EAST' ], [ 'ATTACK' ], 1, 1, done );
    } );

    it( 'should move or do nothing when there are no enemies around', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 1,
                y: 1,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'NORTH', 'EAST', 'SOUTH', 'WEST' ], [ 'MOVE', 'NONE' ], 1, 1, done );
    } );
    
    it( 'should not move to an invalid location', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 0,
                y: 0,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'NORTH', 'EAST' ], [ 'MOVE', 'NONE' ], 1, 1, function() {} );

        map = [
            {
                creatureid: 1,
                userid: 1,
                x: 9,
                y: 9,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'SOUTH', 'WEST' ], [ 'MOVE', 'NONE' ], 1, 1, done );
    } );

    it( 'should move all of the creatures', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 5,
                y: 5,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 1,
                x: 7,
                y: 7,
                hp: 100
            }
        ];

        requestAndCheckResponse( map, [ 'NORTH', 'EAST', 'SOUTH', 'WEST' ], [ 'MOVE', 'NONE' ], 1, 2, done );
    } );

    it( 'should not move dead creatures', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 5,
                y: 5,
                hp: 0
            }
        ];

        requestAndCheckResponse( map, [], [], 1, 0, done );
    } );

    it( 'should not move to an occupied location', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 5,
                y: 5,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 1,
                x: 5,
                y: 6,
                hp: 100
            }
        ];

        agent
            .post( '/round' )
            .send( 'map=' + JSON.stringify( map ) +'&myid=1&W=10&H=10&round=1&gameid=1' )
            .expect( function( res ) {
                var intent = JSON.parse( res.text ).intent;
                var validActions = [ 'MOVE', 'NONE' ];

                assert.ok( intent );
                assert.strictEqual( intent.length, 2 );
                assert.notEqual( validActions.indexOf( intent[ 0 ].action ), -1 );
                if ( intent[ 0 ].action == 'MOVE' ) {
                    assert.notEqual( [ 'EAST', 'SOUTH', 'WEST' ].indexOf( intent[ 0 ].direction ), -1 );
                }
                assert.notEqual( validActions.indexOf( intent[ 1 ].action ), -1 );
                if ( intent[ 1 ].action == 'MOVE' ) {
                    assert.notEqual( [ 'NORTH', 'EAST', 'WEST' ].indexOf( intent[ 1 ].direction ), -1 );
                }
            } )
            .end( done );
    } );

    it( 'should attack the same enemy with two creatures', function( done ) {
        var map = [
            {
                creatureid: 1,
                userid: 1,
                x: 4,
                y: 5,
                hp: 100
            },
            {
                creatureid: 2,
                userid: 1,
                x: 5,
                y: 6,
                hp: 100
            },
            {
                creatureid: 3,
                userid: 2,
                x: 5,
                y: 5,
                hp: 100
            }
        ];

        agent
            .post( '/round' )
            .send( 'map=' + JSON.stringify( map ) +'&myid=1&W=10&H=10&round=1&gameid=1' )
            .expect( function( res ) {
                var intent = JSON.parse( res.text ).intent;

                assert.ok( intent );
                assert.strictEqual( intent.length, 2 );
                assert.strictEqual( intent[ 0 ].action, 'ATTACK' );
                assert.strictEqual( intent[ 0 ].direction, 'EAST' );
                assert.strictEqual( intent[ 1 ].action, 'ATTACK' );
                assert.strictEqual( intent[ 1 ].direction, 'SOUTH' );
            } )
            .end( done );
    } );
} );
