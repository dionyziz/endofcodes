$( document ).ready( function() {
    $( '.creature' ).mouseover( function () {
        $( '.infobubble' ).show();
        var $this = $( this );
        var id = $this.attr( 'data-creatureid' );
        var username = $this.attr( 'data-username' );
        var x = $this.attr( 'data-x' );
        var y = $this.attr( 'data-y' );
        var hp = $this.attr( 'data-hp' );
        var maxHp = $this.attr( 'data-maxHp' );
        $( '.player' ).text( username );
        $( '.creatureid' ).text( 'Creature ' + id );
        $( '.location' ).text( x + ', ' + y );
        $( '.numeric' ).text( hp + ' / ' + maxHp );
        $( '.damage' ).css( 'width', Math.floor( 100 * ( maxHp - hp ) / maxHp ) + '%' );
        $( '.infobubble' ).css( 'top', $( this ).position().top - $( '.infobubble' ).height() - 14 );
        $( '.infobubble' ).css( 'left', $( this ).position().left - $( '.infobubble' ).width() + 30 );
    } );
    $( '.creature' ).mouseout( function() {
        $( '.infobubble' ).hide();
    } );
    $( '.next a' ).click( function() {
        $.getJSON( this.href, function( creatures ) {
            var maxHp = $( '.creature' ).attr( 'data-maxHp' );
            $( '.creature' ).remove();
            for ( var i = 0; i < creatures.length; ++i ) {
                var creature = creatures[ i ];
                if ( creature.hp > 0 ) {
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
        } );
        return false;
    } );
} );
