<?php
    include 'views/header.php';
?>
<p>Step 1</p>
<p><a href="">Download the libraries</a></p>
<p>Step 2</p>
<p>Read the basic <a href="">rules</a></p>
<p>Step 3</p>
<p>Code your bot</p>
<p>Step 4</p>
<p>Register to try it out</p>
<?php
    if ( isset( $empty_user ) ) {
        ?><p>Please fill the username form.</p><?php
    }
    else if ( isset( $empty_pass ) ) {
        ?><p>Please fill the password form.</p><?php
    }
    else if ( isset( $empty_mail ) ) {
        ?><p>Please fill the email form.</p><?php
    }
    else if ( isset( $empty_pass_repeat ) ) {
        ?><p>Please fill the Password Repeat form</p><?php
    }
?>

<form action="index.php?resource=user&amp;method=create" method="post">
    <label for="username">Username</label>
    <?php
        if ( isset( $user_used ) ) {
            ?><p>Username already exists</p><?php
            $val = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'username' ] ) ) {
            $val = $_SESSION[ 'create_post' ][ 'username' ];
            unset( $_SESSION[ 'create_post' ][ 'username' ] );
        }
        else {
            $val = "";
        }
    ?>
    <p><input type="text" id="username" name="username" value="<?php
        echo htmlspecialchars( $val );
    ?>"/></p>
    <label for="password">Password</label>
    <?php
        if ( isset( $small_pass ) ) {
            ?><p>Password should be at least 7 characters long</p><?php
        }
        if ( isset( $not_matched ) ) {
            ?><p>Passwords do not match</p><?php
        }
    ?>
    <p><input type="password" id="password" name="password" /></p>
    <label for="password_repeat">Repeat</label>
    <p><input type="password" id="password_repeat" name="password_repeat" /></p>
    <label for="email">Email</label>
    <?php
        if ( isset( $mail_used ) ) {
            ?><p>Mail is already used</p><?php
            $val = "";
        }
        else if ( isset( $mail_notvalid ) ) {
            ?><p>This is not a valid email</p><?php
            $val = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'email' ] ) ) {
            $val = $_SESSION[ 'create_post' ][ 'email' ];
            unset( $_SESSION[ 'create_post' ][ 'email' ] );
        }
        else {
            $val = "";
        }
    ?>
    <p><input type="text" id="email" name="email" value="<?php
        echo htmlspecialchars( $val );
    ?>"/></p>
    <select>
        <option value="select">Select Country</option>
        <?php
            include 'db/country/countries_array.php';
            $countries = getCountries();
            foreach ( $countries as $country ) {
                ?><option value="<?php
                    echo $country;
                ?>"><?php
                    echo $country;
                ?></option><?php
            }
        ?>
    </select> 
    <p><input type="checkbox" /> I agree on the <a href="">Terms of Usage</a></p>
    <p><input type="submit" value="Register" /></p>
</form>

<?php
    include 'views/footer.php';
?>
