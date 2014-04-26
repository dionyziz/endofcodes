$( document ).ready( function() { 
    $( '#register-form' ).submit(function() {
        var $username = $( '#username' ).val();  
        var $password = $( '#password' ).val();  
        var $email = $( '#email' ).val();  
        if ( $username == '' ) {
            //createError( '#register-form', 'Please type a username.' );
            //return false
            window.location.href = "user/create?username_empty=yes";
        }
        if ( !$username.match( /[^a-zA-Z0-9._]/ ) ) {
            createError( '#register-form', 'Usernames can only have numbers, letters, "." and "_"' );
            return false;
        }
        if ( $password == '' ) {
            createError( '#register-form', 'Please type a password.' );
            return false;
        }
        if ( $email == '' ) {
            createError( '#register-form', 'Please type an email.' );
            return false;
        }
    }); 

    function createError( $form, $description ) {
        if ( $('.alert').length != 0 ) {
            $( '.alert' ).remove();
        }
        $( $form ).prepend( "<div class='alert alert-danger'>" + $description + "</div>" );
    }
});











