$( document ).ready( function() { 
    $( '#register-form' ).submit( function() {
        var username = $( '#username' ).val();  
        var password = $( '#password' ).val();  
        var passwordRepeat = $( '#password_repeat' ).val();  
        var email = $( '#email' ).val();  

        if ( username == '' ) {
            createFormError( '#register-form', 'username', 'Please type a username' );
            return false;
        }
        if ( !username.match( /^[a-zA-Z0-9._]+$/ ) ) {
            createFormError( '#register-form', 'username', 'Usernames can only have numbers, letters, "." and "_"' );
            return false;
        }
        $.get(
            'user/view',
            {
                username: username 
            },
            function( responseText ) {
                createFormError( '#register-form', 'username', 'Username already exists' );
            },
            "html"
        );
        if ( password == '' ) {
            createFormError( '#register-form', 'password', 'Please type a password' );
            return false;
        }
        if ( password.length < 7 ) {
            createFormError( '#register-form', 'password', 'Password should be at least 7 characters long' );
            return false;
        }
        if ( passwordRepeat != password ) {
            createFormError( '#register-form', 'password_repeat', 'Passwords do not match' );
            return false;
        }
        if ( email == '' ) {
            createFormError( '#register-form', 'email', 'Please type an email' );
            return false;
        }
        if ( !email.match( /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/ ) ) {
            createFormError( '#register-form', 'email', 'This is not a valid email' );
            return false;
        }
    } ); 
} ); 
