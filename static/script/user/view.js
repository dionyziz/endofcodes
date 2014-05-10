var UserView = {
    showUploadedImage: function( source ) {
        $( ".avatar img" ).remove();
        $image = $( '<img src="' + source + '" alt="Profile Picture" />' );
        $image.load( function() {
            UserView.fixImageSize( $image );
        } );
        $( ".avatar" ).append( $image );
    },
    createImageError: function() {
        $( '.text-center' ).prepend( "<div class='alert alert-danger'>This isn't an image</div>" )
    },
    removeImageError: function() {
        $( '.text-center .alert.alert-danger' ).remove();
    },
    fixImageSize: function( $image ) {
        var height, width;
        var imgWidth = $image.width();
        var imgHeight = $image.height();
        console.log( 'Width: ' + imgWidth + ' height: ' + imgHeight );

        height = width = 168;

        if ( imgWidth > imgHeight ) {
            $image.height( height );
            $image.css( 'top', 0 );
            $image.css( 'left', -Math.floor( ( $image.width() - width ) / 2 ) );
        }
        else {
            $image.width( width );
            $image.css( 'left', 0 );
            $image.css( 'top', -Math.floor( ( $image.height() - height ) / 2 ) );
        }
    },
    ready: function() {
        var $image = $( '.avatar img' );

        UserView.fixImageSize( $image );

        $( ".avatar" ).mouseover( function() {
            if ( $( ".profile-header" ).attr( 'data-sameUser' ) == 'yes' ) {
                $( "#upload-link" ).show();
            }
        } );
        $( ".avatar" ).mouseout( function() {
            $( "#upload-link" ).hide();
        } );
        $( "#upload-link" ).click( function() {
            $( "#image" ).trigger( 'click' );
            return false;
        } );
        $( "#image" ).change( function() {
            var image = document.getElementById( "image" ).files[ 0 ];
            var token = $( "input[type=hidden]" ).val();
            var formData = new FormData();

            UserView.removeImageError();

            if ( !image ) {
                UserView.createImageError();
                return false;
            }

            formData.append( "image", image );
            formData.append( "token", token );

            $.ajax( {
                url: "image/create",
                type: "POST",
                data: formData,
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function( targetPath ) {
                    var reader = new FileReader();

                    reader.onloadend = function ( e ) {
                        UserView.showUploadedImage( targetPath );
                    }
                    reader.readAsDataURL( image );
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    UserView.createImageError();
                    $( "#imageSubmit" ).show();
                    $( "#uploading" ).hide();
                }
            } );

            return false;
        } );
    }
}
$( document ).ready( UserView.ready );
