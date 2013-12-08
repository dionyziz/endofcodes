<?php
    include_once 'views/header.php';
?>
<p><img src="<?php
            echo $target_path;
        ?>" alt="Profile Picture" width="100" height="100" /></p>
<?php 
    if ( $_SESSION[ 'user' ][ 'id' ] == $user->id ) {
        ?><form action="index.php?resource=image&amp;method=create" method="POST" enctype="multipart/form-data">
            <label for="image">Upload an avatar</label>
            <?php
                if ( isset( $notvalid ) ) {
                    ?><p>This isn't an image</p><?php
                }
            ?>
            <p><input type="file" name="image" id="image" /></p>
            <input type="submit" value="Upload" />
        </form><?php
    }
?>

<p><?php
    echo htmlspecialchars( $user->username );
?></p>

<p><?php
    echo htmlspecialchars( $user->email );
?></p>

<p>Region, Country: <?php
    echo $country;
?></p>
<p>Score: *score*</p>
<p><a href="">Global</a> position: *pos*</p>
<p><a href="">Country</a> position: *pos*</p>
<p><a href="">Last match</a> position: *pos*</p>
<p><img src="static/images/facebook-logo.jpeg" alt="facebook link" width="40" height="40" /></p>
<p><img src="static/images/twitter-logo.png" alt="twitter link" width="40" height="40" /></p>
<p><img src="static/images/github-logo.png" alt="github link" width="40" height="40" /></p>
<p><img src="static/images/google+-logo.jpeg" alt="google+ link" width="40" height="40" /></p>
<p><a href="">Add friend</a></p>

<?php 
    if ( $_SESSION[ 'user' ][ 'id' ] == $user->id ) {
        ?><form action="index.php?resource=user&amp;method=delete" method="post">
            <input type="submit" value="Delete your account" />
        </form>
        <p><a href="index.php?resource=user&amp;method=update">Edit Settings</a></p><?php
    }
    include_once 'views/footer.php';
?>
