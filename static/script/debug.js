var Debug = {
    init: function() {
        $( '.enable-profiling' ).click( function() {
            var token = $( ".profiling-form input[type=hidden]" ).val();

            $( '.enable-profiling' ).remove();
            $( '.dev' ).append( '<span class="measure">Measuring...</span>' );
            $.post( 'profiling/update', {
                enable: true
            }, function() {
                alert( 'Profiling is now enabled' );
            } );
            return false;
        } );
    }
};

$( document ).ready( Debug.init );
