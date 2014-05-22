$( document ).ready( function() { 
    $( '#delete-account' ).click( function() {
        $( ".navbar, footer" ).fadeTo( "slow",0.15 );
        Avgrund.show( "#default-popup" );
    } );
    $( '#close-modal' ).click( function() {
        Avgrund.hide( "default-popup" );
        $( ".navbar, footer" ).fadeTo( "slow", 1 );
    } );
    $( '#pswd-change' ).click( function() {
        $( '#pswd-change' ).hide();
        $( "#password-input input, #pswd-label" ).slideDown( "medium" );
        return false;
    } );
} );
