$( document ).ready( function() { 
    $( '#register-form' ).submit( function() {
        var username = $( '#username' ).val();  
        var password = $( '#password' ).val();  
        var passwordRepeat = $( '#password_repeat' ).val();  
        var email = $( '#email' ).val();  
        if ( username == '' ) {
            createError( '#register-form', 'Please type a username' );
            return false;
        }
        if ( !username.match( /^[a-zA-Z0-9._]+$/ ) ) {
            createError( '#register-form', 'Usernames can only have numbers, letters, "." and "_"' );
            return false;
        }
        $.get(
            'user/view',
            {
                username: username 
            },
            function( responseText ) {
                createError( '#register-form', 'Username already exists' );
            },
            "html"
        );
        if ( password == '' ) {
            createError( '#register-form', 'Please type a password' );
            return false;
        }
        if ( password.length < 7 ) {
            createError( '#register-form', 'Password should be at least 7 characters long' );
            return false;
        }
        if ( passwordRepeat != password ) {
            createError( '#register-form', 'Passwords do not match' );
            return false;
        }
        if ( email == '' ) {
            createError( '#register-form', 'Please type an email' );
            return false;
        }
        if ( !email.match( /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/ ) ) {
            createError( '#register-form', 'This is not a valid email' );
            return false;
        }
    }); 

    $( '#login-form' ).submit( function() {
        var form = $("#login-form"); // or $("form"), or any selector matching an element containing your input fields
        var username = $( "[name='username']", form ).val();
        var password = $( "[name='password']", form ).val();
        var token = $( "[name='token']", form ).val();
        if ( username == '' ) {
            createError( '#login-form', 'Please type a username' );
            return false;
        }
        if ( password == '' ) {
            createError( '#login-form', 'Please type a password' );
            return false;
        }
        $.ajax({ 
            type: "POST",
            url: "session/create",
            data: { username: username, password: password, token: token },
            dataType: "json",
            async: true,
            statusCode: { 
                404: function() {
                    createError( '#login-form', "Username doesn't exist" );
                },
                401: function() { 
                    createError( '#login-form', 'Password is incorrect' );
                },
                200: function() {
                    window.location.replace( 'dashboard' );
                }
            }
        }); 
        return false;
    });

    function createError( form, description ) {
        $( '.alert' ).remove();
        $( form ).prepend( "<div class='alert alert-danger'>" + description + "</div>" );
    }
});
