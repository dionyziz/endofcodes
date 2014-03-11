function logout() {
    $( '#logout' ).click( function() {
        $( '#logout-form' ).submit();
    });
}

$( document ).ready( logout );

