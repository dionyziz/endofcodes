$( document ).ready( function() {
    $( '.creature' ).mouseover( function () {
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
            positioning = $this.height() + 14;
            $infobubble.addClass( 'reversed' );
        }
        else {
            positioning = -$infobubble.height() - 14;
        }
        $infobubble.css( 'top', $this.position().top + positioning );
        $infobubble.css( 'left', $this.position().left - $infobubble.width() + 30 );
    } );
    $( '.creature' ).mouseout( function() {
        var $infobubble = $( '.infobubble' );
        if ( $infobubble.hasClass( 'reversed' ) ) {
            $infobubble.removeClass( 'reversed' );
        }
        $infobubble.hide();
    } );
} );
