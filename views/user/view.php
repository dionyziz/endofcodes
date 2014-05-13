<?php
    require 'views/header.php';
?>
<div class="text-center">
    <div class="profile-header">
        <div class="avatar">
            <img src="<?php
                echo $user->image->target_path;
            ?>" alt="Profile Picture" />
        </div>
        <div class="upper-header">
            <ul>
                <li class="name"><?php
                    echo $user->username;
                ?></li>
                <?php
                    if ( isset( $user->country->name ) ) {
                        ?><li class="country">
                            <img src="<?php
                                echo $user->country->flag;
                            ?>" alt="<?php
                                echo htmlspecialchars( $user->country->name );
                            ?>" />
                            <?php
                                echo htmlspecialchars( $user->country->name );
                            ?>
                        </li><?php
                    }
                ?>
            </ul>
        </div>
        <div class="lower-header">
            <?php
                if ( isset( $_SESSION[ 'user' ] ) && !$sameUser ) {
                    if ( $followExists ) {
                        ?><a href="#" id="unfollow"><button class="btn btn-primary follow">Unfollow</button></a><?php
                        $formId = 'unfollow-form';
                        $followMethod = 'delete';
                    }
                    else {
                        ?><a href="#" id="follow"><button class="btn btn-primary follow">Follow</button></a><?php
                        $formId = 'follow-form';
                        $followMethod = 'create';
                    }
                    $form = new Form( 'follow', $followMethod );
                    $form->id = $formId;
                    $form->output( function( $self ) use( $user ) {
                        $self->createInput( 'hidden', 'followedid', '', $user->id );
                    } );
                }
            ?>
            <!--
            <p class='when'>This week:</p>
            <ul>
                <li>23rd in <?php
                    echo $user->country->name;
                ?></li>
                <li>152nd worldwide</li>
            </ul>
            -->
        </div>
    </div>
    <div class="profile-body"><?php
        if ( isset( $_SESSION[ 'user' ] ) && $sameUser && $user->boturl != '' ) {
            ?><p class='bot-status bg-success'><img src="http://endofcodes.com/static/images/check.png" alt="check" /> Your bot is working correctly</p><?php
        }
        else if ( $sameUser ) {
            ?><p>You don't have a bot. <a href="bot/update">Add one.</a></p><?php
        }
    ?>

        <ul class='contact'>
            <li><a href=""><img src="http://www.defaulticon.com/v1/assets/icons/png/16x16/mail.png" alt="mail" /> <?php
                echo $user->email;
            ?></a></li>
            <li><a href=""><img src="http://endofcodes.com/static/images/github-logo.png" /></a></li>
            <li><a href=""><img src="http://endofcodes.com/static/images/facebook-logo.jpeg" /></a></li>
        </ul>
    </div>
</div>
<?php
    require 'views/footer.php';
?>
