<?php
    include 'views/header.php';
?>

<h1><?php
        echo htmlspecialchars( $credentials[ 'username' ] );
?></h1>

<p>Contact: <?php
    echo htmlspecialchars( $credentials[ 'email' ] );
?></p>

<?php
    $target_path = 'Avatars/' . $credentials[ 'username' ];
    $extentions = array( '.jpg', '.jpeg', '.png' );
    for ( $i = 0; $i < 3; ++$i ) {
        if ( file_exists( $target_path . $extentions[ $i ] ) ) {
            $found = true;
            $target_path = $target_path . $extentions[ $i ];
            break;
        }
    }
    if ( isset( $found ) ) {
        ?><p><img src="<?php
            echo $target_path;
        ?>" alt="Profile Picture" width="100" height="100" /></p><?php
    }
?>

<?php
    if ( $_SESSION[ 'user' ][ 'userid' ] === $credentials[ 'userid' ] ) {
        ?><form action="index.php?resource=image&method=create" method="POST" enctype="multipart/form-data">
            <label for="image">Upload an avatar</label>
            <?php
                if ( isset( $notvalid ) ) {
                    ?><p>This isn't an image</p><?php
                }
            ?>
            <p><input type="file" name="image" id="image" /></p>
            <input type="submit" value="Upload" />
        </form>
        <p>Want to <a href="index.php?resource=user&method=update">change password</a>?</p>
        <form action="index.php?resource=user&method=delete" method="POST">
            <input type="submit" value="Delete your account" />
        </form><?php
    }
?>

<?php
    include 'views/footer.php';
?>
