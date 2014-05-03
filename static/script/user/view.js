$( document ).ready( function() {
    function showUploadedImage( source ) {
        $( "#userImage" ).attr( "src", source );
    }
    $( "#image-form" ).submit( function() {
        var image = document.getElementById( "image" ).files[ 0 ];
        var token = $( "input[type=hidden]" ).val();
        var formdata = new FormData();
        var reader = new FileReader();

        $( "#imageSubmit" ).hide();
        $( "#uploading" ).show();

        reader.onloadend = function ( e ) {
            showUploadedImage( e.target.result );
        }
        reader.readAsDataURL( image );

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
                $( "#imageSubmit" ).show();
                $( "#uploading" ).hide();
            },
            error: function( res ) {
                console.log( res );
                $( "#imageSubmit" ).show();
                $( "#uploading" ).hide();
            }
        } );

        return false;
    } );
} );
