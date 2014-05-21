var Debug = {
    init: function() {
        $( '.enable-profiling' ).click( function() {
            var token = $( "#profiling-form input[name=token]" ).val();

            $( '.enable-profiling' ).remove();
            $( '.dev' ).append( '<span class="measure">Measuring...</span>' );
            $.post( 'debugging/update', {
                token: token,
                enable: true
            }, function() {
                window.location.reload();
            } );
            return false;
        } );

        $( '.profiling-link' ).click( function() {
            $( '.debug-window' ).show();
            return false;
        } );
        $( '.debug-window .close' ).click( function() {
            $( '.debug-window' ).hide();
        } );
    }
};

$( document ).ready( Debug.init );
