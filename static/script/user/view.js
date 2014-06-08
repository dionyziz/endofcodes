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
        $( ".nav li img" ).attr( "src", source );
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
        $( document ).on( "click", ".follow", function() {
            var now, next;

            if ( this.id == 'follow' ) {
                now = { 'method': 'create', 'action': 'follow' };
                next = { 'method': 'delete', 'action': 'unfollow' };
            }
            else {
                now = { 'method': 'delete', 'action': 'unfollow' };
                next = { 'method': 'create', 'action': 'follow' };
            }

            var $form = $( "#" + now.action + "-form" );
            var followedid = $( "[name='followedid']", $form ).val();
            var token = $( "[name='token']", $form ).val();
            var formData = new FormData();

            formData.append( "followedid", followedid );
            formData.append( "token", token );

            $( '.follow button' ).fadeOut( 100, function() {
                $.ajax( {
                    url: "follow/" + now.method,
                    type: "POST",
                    data: formData,
                    cache: false,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    complete: function() {
                        var btn_text = next.action.substr( 0, 1 ).toUpperCase() + next.action.substr( 1 ); 
                        $( "a#" + now.action ).replaceWith( 
                            "<a href='#' class='follow' id='" + next.action + "'><button class='btn btn-primary'>" + btn_text + "</button></a>"
                        );
                        $( 'form#' + now.action + '-form' ).attr( 'action', 'follow/' + next.method );
                        $( 'form#' + now.action + '-form' ).attr( 'id', next.action + '-form' );
                        $( '.follow' ).fadeIn();
                    }
                } );
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
