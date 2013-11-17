<?php
    include 'views/header.php';
?>

<h1><?php
    echo htmlspecialchars( $credentials[ 'username' ] );
?></h1>

<p>Contact: <?php
    echo htmlspecialchars( $credentials[ 'email' ] );
?></p>

<p><img src="<?php
            echo $target_path;
        ?>" alt="Profile Picture" width="100" height="100" /></p>

<?php
    if ( $_SESSION[ 'user' ][ 'userid' ] == $credentials[ 'userid' ] ) {
        ?><form action="index.php?resource=image&amp;method=create" method="POST" enctype="multipart/form-data">
            <label for="image">Upload an avatar</label>
            <?php
                if ( isset( $notvalid ) ) {
                    ?><p>This isn't an image</p><?php
                }
            ?>
            <p><input type="file" name="image" id="image" /></p>
            <input type="submit" value="Upload" />
        </form>
        <p>Want to <a href="index.php?resource=user&amp;method=update">change password</a>?</p>
        <form action="index.php?resource=user&amp;method=delete" method="post">
            <input type="submit" value="Delete your account" />
        </form><?php
    }
?>

<p><a href="index.php">Home</a></p>

<?php
    include 'views/footer.php';
?>
