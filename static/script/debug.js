var Debug = {
    init: function() {
        $( '.enable-profiling' ).click( function() {
            return toggleProfiling.bind( this )( true );
        } );
        $( '.disable-profiling' ).click( function() {
            return toggleProfiling.bind( this )( false );
        } );

        function toggleProfiling( enable ) {
            var text;
            var token = $( "#profiling-form input[name=token]" ).val();

            if ( enable ) {
                $( this ).remove();
                text = 'Measuring...';
            }
            else {
                text = 'Disabling...';
            }
            $( '.dev' ).append( '<span class="measure">' + text + '</span>' );

            $.post( 'debugging/update', {
                token: token,
                enable: enable? 1: 0
            }, function() {
                window.location.reload();
            } );

            return false;
        };

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
