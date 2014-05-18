var UserView = {
    uploading: false,
    IMAGE_HEIGHT: 168,
    IMAGE_WIDTH: 168,
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
        var imgWidth = $image.width();
        var imgHeight = $image.height();

        if ( imgWidth > imgHeight ) {
            $image.height( UserView.IMAGE_HEIGHT );
            $image.css( 'top', 0 );
            $image.css( 'left', -Math.floor( ( $image.width() - UserView.IMAGE_WIDTH ) / 2 ) );
        }
        else {
            $image.width( UserView.IMAGE_WIDTH );
            $image.css( 'left', 0 );
            $image.css( 'top', -Math.floor( ( $image.height() - UserView.IMAGE_HEIGHT ) / 2 ) );
        }
    },
    finishUploadAnimation: function() {
        $( ".uploading" ).hide();
        $( ".uploading" ).removeClass( 'animate' );
        UserView.uploading = false;
    },
    startUploadAnimation: function() {
        $( ".upload-link" ).hide();
        $( ".uploading" ).show();
        $( ".uploading" ).addClass( 'animate' );
        UserView.uploading = true;
    },
    ready: function() {
        $( '.avatar img' ).load( function() {
            UserView.fixImageSize( $( '.avatar img' ) );
        } );
        $( ".avatar" ).mouseover( function() {
            if ( $( ".profile-header" ).attr( 'data-sameUser' ) == 'no' || UserView.uploading ) {
                return;
            }
            $( ".upload-link" ).show();
        } );
        $( ".avatar" ).mouseout( function() {
            if ( UserView.uploading ) {
                return;
            }
            $( ".upload-link" ).hide();
        } );
        $( ".upload-link" ).click( function() {
            $( "#image" ).trigger( 'click' );
            return false;
        } );
        $( document ).on( "click", "#unfollow", function() {
            var $form = $( "#unfollow-form" );
            var followedid = $( "[name='followedid']", $form ).val();
            var token = $( "[name='token']", $form ).val();
            var formData = new FormData();

            formData.append( "followedid", followedid );
            formData.append( "token", token );

            $.ajax( {
                url: "follow/delete",
                type: "POST",
                data: formData,
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                complete: function() {
                    $( "a#unfollow" ).replaceWith( 
                        "<a href='#' id='follow'><button class='btn btn-primary follow'>Follow</button></a>"
                    );
                    $( "form#unfollow-form" ).replaceWith(
                        "<form id='follow-form' action='follow/create' method='post' > \
                            <input type='hidden' name='token' value='" + token + "' /> \
                            <input type='hidden' name='followedid' value='" + followedid + "'  /> \
                        </form>"
                    );
                }
            } );
            return false;
        } );
        $( document ).on( "click", "#follow", function() {
            var $form = $( "#follow-form" );
            var followedid = $( "[name='followedid']", $form ).val();
            var token = $( "[name='token']", $form ).val();
            var formData = new FormData();

            formData.append( "followedid", followedid );
            formData.append( "token", token );

            $.ajax( {
                url: "follow/create",
                type: "POST",
                data: formData,
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                complete: function() {
                    $( "a#follow" ).replaceWith( 
                        "<a href='#' id='unfollow'><button class='btn btn-primary follow'>Unfollow</button></a>"
                    );
                    $( "form#follow-form" ).replaceWith(
                        "<form id='unfollow-form' action='follow/delete' method='post' > \
                            <input type='hidden' name='token' value='" + token + "' /> \
                            <input type='hidden' name='followedid' value='" + followedid + "'  /> \
                        </form>"
                    );
                }
            } );
            return false;
        } );
        $( "#image" ).change( function() {
            var image = document.getElementById( "image" ).files[ 0 ];
            var token = $( "input[type=hidden]" ).val();
            var formData = new FormData();

            UserView.removeImageError();

            if ( !image ) {
                return false;
            }

            formData.append( "image", image );
            formData.append( "token", token );

            UserView.startUploadAnimation();

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

                    UserView.finishUploadAnimation();

                    reader.onloadend = function ( e ) {
                        UserView.showUploadedImage( targetPath );
                    }
                    reader.readAsDataURL( image );
                },
                error: function( jqXHR, textStatus, errorThrown ) {
                    UserView.finishUploadAnimation();

                    UserView.createImageError();
                }
            } );

            return false;
        } );
    }
}
$( document ).ready( UserView.ready );
