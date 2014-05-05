<?php
    require 'views/header.php';
?>
<div class="text-center">
    <p><img src="<?php
                echo $user->image->target_path;
            ?>" alt="Profile Picture" width="100" height="100" id="userImage" /></p><?php

    if ( isset( $user->country ) ) {
        ?><p><img src="<?php
                echo $user->country->flag;
            ?>" alt="<?php
                echo htmlspecialchars( $user->country->name );
            ?>" width="100" height="100" /></p><?php
    }
    ?><p><?php
        echo htmlspecialchars( $user->username );
    ?></p>

    <p><?php
        echo htmlspecialchars( $user->email );
    ?></p><?php

    if ( isset( $user->country ) ) {
        ?><p>Country: <?php
            echo htmlspecialchars( $user->country->name );
        ?></p><?php
    }

    ?><p>Score: *score*</p>
    <p><a href="">Global</a> position: *pos*</p>
    <p><a href="">Country</a> position: *pos*</p>
    <p><a href="">Last match</a> position: *pos*</p>
    <p><img src="static/images/facebook-logo.jpeg" alt="facebook link" width="40" height="40" /></p>
    <p><img src="static/images/twitter-logo.png" alt="twitter link" width="40" height="40" /></p>
    <p><img src="static/images/github-logo.png" alt="github link" width="40" height="40" /></p>
    <p><img src="static/images/google+-logo.jpeg" alt="google+ link" width="40" height="40" /></p>
    <?php
        if ( isset( $_SESSION[ 'user' ] ) ) {
            if ( $_SESSION[ 'user' ]->id != $user->id ) {
                if ( !$followExists ) {
                    $formMethod = 'create';
                    $submitValue = 'Follow';
                }
                else {
                    $formMethod = 'delete';
                    $submitValue = 'Unfollow';
                }
                $form = new Form( 'follow', $formMethod );
                $form->output( function( $self ) use( $user, $submitValue ) {
                    $self->createInput( 'hidden', 'followerid', '', $_SESSION[ 'user' ]->id );
                    $self->createInput( 'hidden', 'followedid', '', $user->id );
                    $self->createInput( 'submit', '', '', $submitValue );
                } );
            }
            else {
                ?><p id="uploading">Uploading...</p><?php
                $form = new Form( 'image', 'create' );
                $form->id = 'image-form';
                $form->output( function( $self ) use( $image_invalid ) {
                    $self->createLabel( 'image', 'Upload an avatar' );
                    if ( isset( $image_invalid ) ) {
                        $self->createError( "This isn't an image" );
                    }
                    $self->createInput( 'file', 'image', 'image' );
                    $self->createSubmit( 'Upload', [ "id" => "imageSubmit" ]  );
                } );

                ?><p><a href="user/update">Edit Settings</a></p><?php
            }
        }
    ?>
</div>
<?php
    require 'views/footer.php';
?>
