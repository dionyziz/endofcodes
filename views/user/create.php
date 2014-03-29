<?php
    require 'views/header.php';
?>
<div id="steps">
    <div div="step1" class="steps">
        <p>Step 1</p>
        <p><a href="">Download the libraries</a></p>
    </div>
    <div id="step2" class="steps">
        <p>Step 2</p>
        <p><a href="">Read the basic rules</a></p>
    </div>
    <div id="step3" class="steps">
        <p>Step 3</p>
        <p>Code your bot</p>
    </div>
    <div id="step4" class="steps">
        <p>Step 4</p>
        <p>Register to try it out</p>
    </div>
</div>

<?php
    $form = new Form( 'user', 'create' );
    $form->id = 'register-form';
    $form->output( function( $self ) use( $usernameEmpty, $usernameInvalid, $passwordEmpty,
            $emailEmpty, $usernameUsed, $passwordSmall,
            $passwordNotMatched, $emailUsed, $emailInvalid, $countries, $location ) {
        global $config;

        if ( isset( $usernameEmpty ) ) {
            $self->createError( 'Please type a username.' );
        }
        if ( isset( $usernameInvalid ) ) {
            $self->createError( 'Usernames can only have numbers, letters, "." and "_"' );
        }
        if ( isset( $passwordEmpty ) ) {
            $self->createError( 'Please type a password.' );
        }
        if ( isset( $emailEmpty ) ) {
            $self->createError( 'Please type an email.' );
        }
        $self->createLabel( 'username', 'Username' );
        if ( isset( $usernameUsed ) ) {
            $self->createError( 'Username already exists' );
            $usernameValue = "";
        }
        else if ( isset( $_SESSION[ 'createPost' ][ 'username' ] ) ) {
            $usernameValue = $_SESSION[ 'createPost' ][ 'username' ];
            unset( $_SESSION[ 'createPost' ][ 'username' ] );
        }
        else {
            $usernameValue = "";
        }
        $self->createInput( 'text', 'username', 'username', $usernameValue );
        $self->createLabel( 'password', 'Password' );
        if ( isset( $passwordSmall ) ) {
            $self->createError( 'Password should be at least 7 characters long' );
        }
        if ( isset( $passwordNotMatched ) ) {
            $self->createError( 'Passwords do not match' );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'passwordRepeat', 'Repeat' );
        $self->createInput( 'password', 'PasswordRepeat', 'passwordRepeat' );
        $self->createLabel( 'email', 'Email' );
        if ( isset( $emailInvalid ) ) {
            $self->createError( 'This is not a valid email' );
        }
        if ( isset( $emailUsed ) ) {
            $self->createError( 'Email is already used' );
            $emailValue = "";
        }
        else if ( isset( $_SESSION[ 'createPost' ][ 'email' ] ) ) {
            $emailValue = $_SESSION[ 'createPost' ][ 'email' ];
            unset( $_SESSION[ 'createPost' ][ 'email' ] );
        }
        else {
            $emailValue = "";
        }
        $self->createInput( 'text', 'email', 'email', $emailValue );
        $self->createLabel( 'dob', 'Date of birth' );
        $daysSelectArray = [ [ 'content' => 'Select Day' ] ];
        for ( $i = 1; $i <= 31; ++$i ) {
            $daysSelectArray[] = [ 'value' => $i, 'content' => $i ];
        }
        $self->createSelect( $daysSelectArray, 'day' );
        $monthsSelectArray = [ [ 'content' => 'Select Month' ] ];
        for ( $i = 1; $i <= 12; ++$i ) {
            $monthsSelectArray[] = [
                'value' => $i,
                'content' => date( 'M', mktime( 0, 0, 0, $i, 1, 2000 ) )
            ];
        }
        $self->createSelect( $monthsSelectArray, 'month' );
        $yearsSelectArray = [ [ 'content' => 'Select Year' ] ];
        $currentYear = date( 'Y' );
        for ( $i = $currentYear - $config[ 'age' ][ 'min' ]; $i >= $currentYear - $config[ 'age' ][ 'max' ]; --$i ) {
            $yearsSelectArray[] = [ 'value' => $i, 'content' => $i ];
        }
        $self->createSelect( $yearsSelectArray, 'year' );
        $countriesSelectArray = [ [ 'content' => 'Select Country' ] ];
        foreach ( $countries as $key => $country ) {
            $countriesSelectArray[] = [ 'value' => $key + 1, 'content' => $country->name ];
        }
        $self->createSelect( $countriesSelectArray, 'countryid', $location );
        $self->createInput( 'submit', '', '', 'Register' );
    } );

    require 'views/footer.php';
?>
