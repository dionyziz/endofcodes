function logout() {
    $( '#logout' ).click( function() {
        $( '#logout-form' ).submit();
        return false;
    });
}

$( document ).ready( logout );
