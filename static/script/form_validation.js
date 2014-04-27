$( document ).ready( function() { 
    $( '#register-form' ).submit(function() {
        var $username = $( '#username' ).val();  
        var $password = $( '#password' ).val();  
        var $passwordRepeat = $( '#password_repeat' ).val();  
        var $email = $( '#email' ).val();  
        $.ajax({
            url: 'jsRequests.php',
            type: 'post',
            data: { 
                'function': 'checkUsername', 
                'username': $username 
            },
            success: function(response) { 
                alert( response ); 
            }
        });
        if ( $username == '' ) {
            createError( '#register-form', 'Please type a username.' );
            return false;
            //window.location.href = "user/create?username_empty=yes";
        }
        if ( !$username.match( /^[a-zA-Z0-9_.]{3,16}$/ ) ) {
            createError( '#register-form', 'Usernames can only have numbers, letters, "." and "_"' );
            return false;
        }
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











