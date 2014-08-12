QUnit.test( 'findGameAndRoundId', function( assert ) {
    var gameid = 10, roundid = 13;
    var href = 'http://example.com/?gameid=' + gameid + '&randomthing=20&roundid=' + roundid;
    var gameInfo = GameView.findGameAndRoundId( href );

    assert.equal( gameInfo.gameid, gameid, 'Returned gameid should be the same one as the one on href' );
    assert.equal( gameInfo.roundid, roundid, 'Returned roundid should be the same one as the one on href' );
} );
QUnit.test( 'fixUrls', function( assert ) {
    var gameid = 10, roundid = 13;
    var $a1 = $( '<a>' ), $a2 = $( '<a>' );
    var $next = $( '<p>' ).addClass( 'next' ).append( $a1 );
    var $previous = $( '<p>' ).addClass( 'previous' ).append( $a2 );
    var regex1 = new RegExp( "roundid=" + ( roundid + 1 ) );
    var regex2 = new RegExp( "roundid=" + ( roundid - 1 ) );

    $( 'body' ).append( $next );
    $( 'body' ).append( $previous );
    GameView.fixUrls( gameid, roundid );

    assert.ok( regex1.test( $a1.attr( 'href' ) ), 'next URL must be created as expected' );
    assert.ok( regex2.test( $a2.attr( 'href' ) ), 'previous URL must be created as expected' );

    $next.remove();
    $previous.remove();
} );
QUnit.test( 'createCreature', function( assert ) {
    var creatureInfo = {
        creatureid: 1,
        username: 'vitsalis',
        x: 2,
        y: 2,
        hp: 10,
        userid: 10
    };
    var color = 'red';
    var $creature;

    $creature = GameView.createCreature( creatureInfo, color );

    assert.ok( $creature.is( 'div' ), 'Creature returned must be a div' );
    assert.ok( $creature.hasClass( color ), 'Creature must have class according to its color' );
    assert.ok( $creature.hasClass( 'creature' ), 'Creature must have class "creature"' );
    $.each( creatureInfo, function( key, value ) {
        assert.equal( $creature.attr( 'data-' + key ), creatureInfo[ key ], 'Data attribute ' + key + ' must have the same value as defined' );
    } );
    assert.equal( $creature.css( 'top' ), 20 * creatureInfo.x + 'px', "Top CSS should be 20 times the creature's x coordinate, in pixels" );
    assert.equal( $creature.css( 'left' ), 20 * creatureInfo.y + 'px', "left CSS should be 20 times the creature's y coordinate, in pixels" );
} );
QUnit.test( 'findUser', function( assert ) {
    var userid = 10;
    var $list = $( "<div><ul>" ).addClass( 'playerList' );
    // create 2 lis to be sure findUser finds the correct one
    $list.append( '<li></li><li></li>' );

    // start from 1 because first child is ul
    var $li1 = $list.children().eq( 1 );
    var $li2 = $list.children().eq( 2 );

    $li1.attr( 'data-id', userid );
    $li2.attr( 'data-id', userid + 1 );

    $( 'body' ).append( $list );

    assert.equal( GameView.findUser( userid ).attr( 'data-id' ), userid, 'findUser must find the correct user' );

    $list.remove();
} );
QUnit.test( 'fixUserList', function( assert ) {
    // setup
    var $list = $( "<div><ul><li><li>" ).addClass( 'playerList' );
    var $li1 = $list.children().eq( 0 ).children().eq( 0 ).text( 'user1' ).attr( 'data-id', 1 );
    var $li2 = $list.children().eq( 0 ).children().eq( 1 ).text( 'user2' ).attr( 'data-id', 2 );
    var creatures = [
        {
            hp: 10,
            userid: 1
        },
        {
            hp: 0,
            userid: 2
        }
    ];

    $li1.prepend( $( '<span>' ) );
    $li2.prepend( $( '<span>' ) );

    $( 'body' ).append( $list );

    GameView.fixUserList( creatures );

    assert.equal( $li1.text(), 'user1', 'nodeValue should not change' );
    assert.equal( $li2.text(), 'user2', 'nodeValue should not change' );
    assert.equal( $li1.children().length, 1, 'no new nodes should be created if the user has alive creatures' );
    assert.equal( $li2.children().eq( 1 ).get( 0 ).tagName, 'DEL', 'a DEL node should be created when the user has no alive creatures' );

    // destruct
    $( '.playerList' ).remove();
} );
