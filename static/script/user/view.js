$( document ).ready( function() {
    function showUploadedImage( source ) {
        $( "#userImage" ).attr( "src", source );
    }
    function createImageError() {
        $( '#image-form' ).prepend( "<div class='alert alert-danger'>This isn't an image</div>" )
    }
    function removeImageError() {
        $( '#image-form .alert.alert-danger' ).remove();
    }
    function toggleSubmit() {
        $( "#imageSubmit" ).toggle();
        $( "#uploading" ).toggle();
    }
    $( "#image-form" ).submit( function() {
        var image = document.getElementById( "image" ).files[ 0 ];
        var token = $( "input[type=hidden]" ).val();
        var formdata = new FormData();

        removeImageError();

        if ( !image ) {
            createImageError();
            return false;
        }

        toggleSubmit();

        formdata.append( "image", image );
        formdata.append( "token", token );

        $.ajax( {
            url: "image/create",
            type: "POST",
            data: formdata,
            cache: false,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function( res ) {
                var reader = new FileReader();

                reader.onloadend = function ( e ) {
                    showUploadedImage( e.target.result );
                }
                reader.readAsDataURL( image );

                toggleSubmit();
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                createImageError();
                $( "#imageSubmit" ).show();
                $( "#uploading" ).hide();
            }
        } );

        return false;
    } );
} );
