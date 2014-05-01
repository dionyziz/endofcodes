$( document ).ready( function() {
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
    function findGameAndRoundId( href ) {
        var hrefArray = href.split( "?" )[ 1 ].split( "&" );

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
    }
    function fixUserList( hasCreatures ) {
        var $nodes = $( '.playerList li' );
        for ( var i = 0; i < $nodes.length; ++i ) {
            var $node = $nodes.eq( i );
            var array = $node.html().split( '</span>' );
            var name = array[ 1 ].trim();
            var userid = $node.attr( 'data-id' );
            if ( hasCreatures[ userid ] && name.indexOf( "<del>" ) != -1 ) {
                name = name.substring( 5, name.indexOf( "</del>" ) );
            }
            else if ( !hasCreatures[ userid ] && name.indexOf( "<del>" ) ) {
                name = "<del>" + name + "</del>";
            }
            $node.html( array[ 0 ] + "</span>" + name );
        }
    }
    function getMap( href, roundAddition ) {
        $.getJSON( href, function( creatures ) {
            var maxHp = $( '.creature' ).attr( 'data-maxHp' );
            var maxRounds = $( '.gamemeta h2' ).attr( 'data-rounds' );
            var nextHref = $( '.next a' ).attr( 'href' );
            var previousHref = $( '.previous a' ).attr( 'href' );
            var gameInfo = findGameAndRoundId( href );
            var prefix = "game/view?gameid=" + gameInfo.gameid + "&roundid=";
            var roundValue;
            var hasCreatures = [];

            history.pushState( {}, "", href );

            if ( ( roundValue = findGameAndRoundId( nextHref ).roundid + roundAddition ) >= maxRounds ) {
                $( '.next' ).hide();
            }
            else {
                $( '.next' ).show();
            }
            $( '.next a' ).attr( 'href', prefix + roundValue );
            if ( ( roundValue = findGameAndRoundId( previousHref ).roundid + roundAddition ) < 0 ) {
                $( '.previous' ).hide();
            }
            else {
                $( '.previous' ).show();
            }
            $( '.previous a' ).attr( 'href', prefix + roundValue ); 
            $( '.round' ).text( 'Round ' + findGameAndRoundId( href ).roundid );

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
            fixUserList( hasCreatures );
        } );
    }
    $( '.next a' ).click( function() {
        getMap( this.href, 1 );
        return false;
    } );
    $( '.previous a' ).click( function() {
        getMap( this.href, -1 );
        return false;
    } );
} );
