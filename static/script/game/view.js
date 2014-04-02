$( document ).ready( function() {
    $( '.creature' ).mouseover( function () {
        $( '.infobubble' ).show();
        var id = this.getAttribute( 'data-creatureid' );
        var username = this.getAttribute( 'data-username' );
        var x = this.getAttribute( 'data-x' );
        var y = this.getAttribute( 'data-y' );
        var hp = this.getAttribute( 'data-hp' );
        var maxHp = this.getAttribute( 'data-maxHp' );
        $( '.player' ).text( username );
        $( '.creatureid' ).text( 'Creature ' + id );
        $( '.location' ).text( x + ', ' + y );
        $( '.damage' ).css( 'width', Math.floor( 100 * ( maxHp - hp ) / maxHp ) + '%' );
        $( '.infobubble' ).css( 'top', $( this ).position().top - $( '.infobubble' ).height() - 14 );
        $( '.infobubble' ).css( 'left', $( this ).position().left - $( '.infobubble' ).width() + 30 );
    } );
    $( '.creature' ).mouseout( function() {
        $( '.infobubble' ).hide();
    } );
} );
