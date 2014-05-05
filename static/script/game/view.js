var GameView = {
    findGameAndRoundId: function( href ) {
        var hrefArray = href.split( "?" )[ 1 ].split( "&" );
        var attribute, gameid, roundid;

        for ( var i = 0; i < hrefArray.length; ++i ) {
            attribute = hrefArray[ i ].split( "=" );
            switch ( attribute[ 0 ] ) {
                case 'roundid':
                    roundid = parseInt( attribute[ 1 ] );
                    break;
                case 'gameid':
                    gameid = parseInt( attribute[ 1 ] );
                    break;
            }
        }
        return {
            gameid: gameid,
            roundid: roundid
        }
    },
    fixUserList: function( hasCreatures ) {
        $( '.playerList li' ).each( function( index, value ) {
            $node = $( this );
            var userid = $node.attr( 'data-id' );
            var $nameNode = $node.contents()[ 1 ];
            var $newNameNode = $( document.createTextNode( $node.text() ) );

            if ( !hasCreatures[ userid ] ) {
                $newNameNode = $( "<del>" + $newNameNode.text() + "</del>" );
            }
            $nameNode.remove();
            $node.append( $newNameNode );
        } );
    },
    makeUrl: function( gameid, roundid ) {
        return "game/view?gameid=" + gameid + "&roundid=" + roundid;
    },
    fixUrls: function( gameid, roundid ) {
        $( ".next a" ).attr( 'href', this.makeUrl( gameid, roundid + 1 ) );
        $( ".previous a" ).attr( 'href', this.makeUrl( gameid, roundid - 1 ) );
    },
    getMap: function( href ) {
        $.getJSON( href, function( creatures ) {
            var maxHp = $( '.creature' ).attr( 'data-maxHp' );
            var maxRounds = $( '.gamemeta h2' ).attr( 'data-rounds' );
            var gameInfo = GameView.findGameAndRoundId( href );
            var gameid = gameInfo.gameid;
            var roundid = gameInfo.roundid;
            var hasCreatures = new Array();

            history.pushState( {}, "", href );

            $( '.next' ).toggle( roundid + 1 < maxRounds );
            $( '.previous' ).toggle( roundid - 1 >= 0 );
            GameView.fixUrls( gameid, roundid );

            $( '.round' ).text( 'Round ' + roundid );

            $( '.creature' ).remove();
            for ( var i = 0; i < creatures.length; ++i ) {
                var creature = creatures[ i ];
                if ( creature.hp > 0 ) {
                    hasCreatures[ creature.userid ] = true;
                    var $user = $( '.playerList li[data-id=' + creature.userid + ']' );
                    var username = $user.text();
                    var color = $user.find( 'span.bubble' ).attr( 'data-color' );
                    creatureInfo = {
                        creatureid: creature.id,
                        username: username,
                        x: creature.x,
                        y: creature.y,
                        hp: creature.hp,
                        maxHp: maxHp
                    };
                    $creature = $( '<div class="' + color + ' creature"></div>' );
                    for ( var attribute in creatureInfo ) {
                        var value = creatureInfo[ attribute ];
                        $creature.attr( 'data-' + attribute, value );
                    }
                    $creature.css( {
                        left: creature.x * 20 + 'px',
                        top: creature.y * 20 + 'px'
                    } );
                }
                $( '.gameboard' ).prepend( $creature );
            }
            GameView.fixUserList( hasCreatures );
        } );
    },
    ready: function() {
        $( '.creature' ).mouseover( function () {
            var ARROW_HEIGHT = 14;
            var ARROW_WIDTH = 30;
            var id = this.getAttribute( 'data-creatureid' );
            var username = this.getAttribute( 'data-username' );
            var x = this.getAttribute( 'data-x' );
            var y = this.getAttribute( 'data-y' );
            var hp = this.getAttribute( 'data-hp' );
            var maxHp = this.getAttribute( 'data-maxHp' );
            var $this = $( this );
            var offsetTop = $this.offset().top - $( '.infobubble' ).height() - 14;
            var $infobubble = $( '.infobubble' );
            var positioning;

            $infobubble.show();
            $( '.player' ).text( username );
            $( '.creatureid' ).text( 'Creature ' + id );
            $( '.location' ).text( x + ', ' + y );
            $( '.numeric' ).text( hp + ' / ' + maxHp );
            $( '.damage' ).css( 'width', Math.floor( 100 * ( maxHp - hp ) / maxHp ) + '%' );
            if ( offsetTop < 0 ) {
                positioning = $this.height() + ARROW_HEIGHT;
                $infobubble.addClass( 'reversed' );
            }
            else {
                positioning = -$infobubble.height() - ARROW_HEIGHT;
            }
            $infobubble.css( 'top', $this.position().top + positioning );
            $infobubble.css( 'left', $this.position().left - $infobubble.width() + ARROW_WIDTH );
        } );
        $( '.creature' ).mouseout( function() {
            var $infobubble = $( '.infobubble' );
            $infobubble.removeClass( 'reversed' );
            $infobubble.hide();
        } );
        $( '.next a' ).click( function() {
            GameView.getMap( this.href );
            return false;
        } );
        $( '.previous a' ).click( function() {
            GameView.getMap( this.href );
            return false;
        } );
    }
}
$( document ).ready( GameView.ready );
