$( document ).ready( function() { 
    if ( $( ".alert-danger" )[ 0 ] || $( ".error" )[ 0 ] || $( '.alert-success' )[ 0 ] ) {
        $( "#bot-set-up" ).hide();    
    }
} ); 
