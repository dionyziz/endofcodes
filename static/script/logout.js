document.addEventListener(
    'DOMContentLoaded', function() {
        var button = document.getElementById( 'logout' );
        button.addEventListener( 
            'click', function() { 
                document.getElementById( 'logout-form' ).submit(); 
            }, false 
        );
    }
);
