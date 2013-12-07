<?php
    include_once 'views/header.php';
?>

<form action="index.php?resource=user&amp;method=update" method="post">
    <label for="password">Old password</label>
    <?php
        if ( isset( $wrong_pass ) ) {
            ?><p class="error">Old password is incorrect</p><?php
        }
    ?>
    <p><input type="password" name="password" id="password" /></p>
    <label for="email">Email</label>
    <?php
        if ( isset( $mail_notvalid ) ) {
            ?><p class="error">Mail is not valid</p><?php
        }
        if ( isset( $mail_used ) ) {
            ?><p class="error">Mail is used</p><?php
        }
    ?>
    <p><input type="text" name="email" id="email" /></p>
    <label for="password_new">New password</label>
    <?php
        if ( isset( $not_matched ) ) {
            ?><p class="error">Passwords do not match</p><?php
        }
        else if ( isset( $small_pass ) ) {
            ?><p class="error">Your password should be at least 7 characters long</p><?php
        }
    ?>
    <p><input type="password" name="password_new" id="password_new" /></p>
    <label for="password_repeat">Repeat</label>
    <p><input type="password" name="password_repeat" id="password_repeat" /></p>
    <p><select name="country">
        <option>Select Country</option>
        <?php
            include_once 'db/country/countries_array.php';
            $countries = getCountries();
            foreach ( $countries as $country ) {
                ?><option value="<?php
                    echo $country;
                ?>"><?php
                    echo $country;
                ?></option><?php
            }
        ?>
    </select></p> 
    <p><input type="submit" value="Save settings" /></p>
</form>

<?php
    include_once 'views/footer.php';
?>
