<?php
    include 'views/header.php';
?>

<p><img src="<?php
            echo $user->image->target_path;
        ?>" alt="Profile Picture" width="100" height="100" /></p>

<p><img src="<?php
            echo $user->country->flag;
        ?>" alt="<?php
            echo $user->country->name;
        ?>" width="100" height="100" /></p>

<p><?php
    echo htmlspecialchars( $user->username );
?></p>

<p><?php
    echo htmlspecialchars( $user->email );
?></p>

<p>Region, Country: <?php
    echo htmlspecialchars( $user->country->name );
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
    if ( isset( $_SESSION[ 'user' ]->id ) && $_SESSION[ 'user' ]->id == $user->id ) {
        ?><p><a href="index.php?resource=user&amp;method=update">Edit Settings</a></p><?php
    }
    include 'views/footer.php';
?>
