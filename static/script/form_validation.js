$( document ).ready( function() { 
    $( '#register-form' ).submit(function() {
        var $username = $( '#username' ).val();  
        var $password = $( '#password' ).val();  
        var $passwordRepeat = $( '#password_repeat' ).val();  
        var $email = $( '#email' ).val();  
        if ( $username == '' ) {
            createError( '#register-form', 'Please type a username.' );
            return false;
        }
        if ( !$username.match( /^[a-zA-Z0-9_.]{3,16}$/ ) ) {
            createError( '#register-form', 'Usernames can only have numbers, letters, "." and "_"' );
            return false;
        }
        $.get(
            'user/view',
            {
                username: $username 
            },
            function( responseText ) {
                    createError( '#register-form', 'Username already exists' );
                    return false;
            },
            "html"
        );
        if ( $password == '' ) {
            createError( '#register-form', 'Please type a password.' );
            return false;
        }
        if ( $password.length < 7 ) {
            createError( '#register-form', 'Password should be at least 7 characters long' );
            return false;
        }
        if ( $passwordRepeat != $password ) {
            createError( '#register-form', 'Please repeat correctly your password' );
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
