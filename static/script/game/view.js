var GameView = {
    roundCount: 0,
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
    fixUserList: function( creatures ) {
        var hasCreatures = [];
        for ( var i = 0; i < creatures.length; ++i ) {
            creature = creatures[ i ];
            if ( creature.hp > 0 ) {
                hasCreatures[ creature.userid ] = true;
            }
        }

        $( '.playerList li' ).each( function( index, value ) {
            var $node = $( this );
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
    createCreature: function( creatureInfo, color ) {
        var $creature = $( '<div class="' + color + ' creature"></div>' );
        for ( var attribute in creatureInfo ) {
            var value = creatureInfo[ attribute ];
            $creature.attr( 'data-' + attribute, value );
        }
        $creature.css( {
            left: creatureInfo.x * 20 + 'px',
            top: creatureInfo.y * 20 + 'px'
        } );
        return $creature;
    },
    processCreatures: function( creatures ) {
        var maxHp = $( '.creature' ).attr( 'data-maxHp' );

        $( '.creature' ).remove();
        for ( var i = 0; i < creatures.length; ++i ) {
            var creature = creatures[ i ];
            if ( creature.hp > 0 ) {
                var $user = $( '.playerList li[data-id=' + creature.userid + ']' );
                var username = $user.text();
                var color = $user.find( 'span.bubble' ).attr( 'data-color' );
                creatureInfo = {
                    creatureid: creature.creatureid,
                    username: username,
                    x: creature.x,
                    y: creature.y,
                    hp: creature.hp,
                    maxHp: maxHp
                };
                var $creature = GameView.createCreature( creatureInfo, color );
                $( '.gameboard' ).prepend( $creature );
            }
        }
    },
    getMap: function() {
        var href = this.href;
        $.getJSON( href, function( creatures ) {
            var gameInfo = GameView.findGameAndRoundId( href );
            var gameid = gameInfo.gameid;
            var roundid = gameInfo.roundid;

            history.pushState( {}, "", href );

            $( '.round' ).text( 'Round ' + roundid );

            $( '.next' ).toggle( roundid + 1 < GameView.roundCount );
            $( '.previous' ).toggle( roundid - 1 >= 0 );
            GameView.fixUrls( gameid, roundid );

            GameView.processCreatures( creatures );

            GameView.fixUserList( creatures );
        } );
        return false;
    },
    ready: function() {
        GameView.roundCount = $( '.gamemeta h2' ).attr( 'data-rounds' );
        $( document ).on( "mouseover", ".creature", function() {
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
            else { // bubble doesn't fit on the screen
                positioning = -$infobubble.height() - ARROW_HEIGHT;
            }
            $infobubble.css( 'top', $this.position().top + positioning );
            $infobubble.css( 'left', $this.position().left - $infobubble.width() + ARROW_WIDTH );
        } );
        $( document ).on( "mouseout", ".creature", function() {
            var $infobubble = $( '.infobubble' );
            $infobubble.removeClass( 'reversed' );
            $infobubble.hide();
        } );
        $( '.next a' ).click( GameView.getMap );
        $( '.previous a' ).click( GameView.getMap );
    }
}
$( document ).ready( GameView.ready );
