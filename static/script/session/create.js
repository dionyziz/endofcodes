$( document ).ready( function() { 
    $( '#login-form' ).submit( function() {
        $.getScript( 'static/script/error.js' );
        var $form = $( "#login-form" );
        var username = $( "[name='username']", $form ).val();
        var password = $( "[name='password']", $form ).val();
        var token = $( "[name='token']", $form ).val();

        if ( username == '' ) {
            createError( '#login-form', 'Please type a username' );
            return false;
        }
        if ( password == '' ) {
            createError( '#login-form', 'Please type a password' );
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
                    createError( '#login-form', "Username doesn't exist" );
                    $( "[name='username']", $form ).focus();
                },
                401: function() { 
                    createError( '#login-form', 'Password is incorrect' );
                    $( "[name='password']", $form ).focus();
                },
                200: function() {
                    window.location.replace( 'dashboard' );
                }
            }
        } ); 
        return false;
    } );
} );
