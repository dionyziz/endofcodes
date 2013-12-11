<?php
    include 'views/header.php';
?>

<form action="index.php?resource=user&amp;method=update" method="post">
    <p>Change email</p>
    <label for="email">Email</label>
    <?php
        if ( isset( $mail_notvalid ) ) {
            ?><p class="error">Email is not valid</p><?php
        }
        if ( isset( $mail_used ) ) {
            ?><p class="error">Email is already in use.</p><?php
        }
    ?>
    <p><input type="text" name="email" id="email" /></p>
    <p>Change password</p>
    <label for="password">Old password</label>
    <?php
        if ( isset( $old_pass_wrong ) ) {
            ?><p class="error">Old password is incorrect</p><?php
        }
    ?>
    <p><input type="password" name="password" id="password" /></p>
    <label for="password_new">New password</label>
    <?php
        if ( isset( $new_pass_not_matched ) ) {
            ?><p class="error">Passwords do not match</p><?php
        }
        else if ( isset( $pass_small ) ) {
            ?><p class="error">Your password should be at least 7 characters long</p><?php
        }
    ?>
    <p><input type="password" name="password_new" id="password_new" /></p>
    <label for="password_repeat">Repeat</label>
    <p><input type="password" name="password_repeat" id="password_repeat" /></p>
    <p>Change country</p>
    <p><select name="countryname">
        <option>Select Country</option>
        <?php
            foreach ( $countries as $country ) {
                ?><option value="<?php
                    echo $country[ 'name' ];
                ?>"><?php
                    echo $country[ 'name' ];
                ?></option><?php
            }
        ?>
    </select></p> 
    <p><input type="submit" value="Save settings" /></p>
</form>

<form action="index.php?resource=image&amp;method=create" method="POST" enctype="multipart/form-data"> 
    <label for="image">Upload an avatar</label>
    <?php
        if ( isset( $image_invalid ) ) {
            ?><p class="error">This isn't an image</p><?php
        }
    ?>
    <p><input type="file" name="image" id="image" /></p>
    <input type="submit" value="Upload" />
</form>

<form action="index.php?resource=user&amp;method=delete" method="post">
    <input type="submit" value="Delete your account" />
</form>

<?php
    include 'views/footer.php';
?>
