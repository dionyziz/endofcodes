$( document ).ready( function() { 
    $( '#login-form' ).submit( function() {
        var $form = $( "#login-form" );
        var username = $( "[name='username']", $form ).val();
        var password = $( "[name='password']", $form ).val();
        var token = $( "[name='token']", $form ).val();

        if ( username == '' ) {
            createFormError( '#login-form', 'username', 'Please type a username' );
            return false;
        }
        if ( password == '' ) {
            createFormError( '#login-form', 'password', 'Please type a password' );
            return false;
        }
        $.ajax( { 
            type: "POST",
            url: "session/create",
            data: { username: username, password: password, token: token },
            dataType: "json",
            async: true,
            statusCode: { 
                404: function() {
                    createFormError( '#login-form', 'username', "Username doesn't exist" );
                },
                401: function() { 
                    createFormError( '#login-form', 'password', 'Password is incorrect' );
                },
                200: function() {
                    window.location.replace( 'dashboard' );
                }
            }
        } ); 
        return false;
    } );
} );
