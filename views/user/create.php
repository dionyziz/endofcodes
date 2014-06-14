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
    $form->output( function( $self ) use( $username_empty, $username_invalid, $password_empty, $email_empty, 
            $username_used, $password_small, $password_not_matched, $email_used, $email_invalid, $countries, $location ) {
        global $config;

        if ( isset( $username_empty ) ) {
            $self->createError( 'Please type a username.' );
        }
        if ( isset( $username_invalid ) ) {
            $self->createError( 'Usernames can only have numbers, letters, "." and "_"' );
        }
        if ( isset( $password_empty ) ) {
            $self->createError( 'Please type a password.' );
        }
        if ( isset( $email_empty ) ) {
            $self->createError( 'Please type an email.' );
        }
        $self->createLabel( 'username', 'Username' );
        if ( isset( $username_used ) ) {
            $self->createError( 'Username already exists' );
            $username_value = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'username' ] ) ) {
            $username_value = $_SESSION[ 'create_post' ][ 'username' ];
            unset( $_SESSION[ 'create_post' ][ 'username' ] );
        }
        else {
            $username_value = "";
        }
        $self->createInput( 'text', 'username', 'username', $username_value );
        $self->createLabel( 'password', 'Password' );
        if ( isset( $password_small ) ) {
            $self->createError( 'Password should be at least 7 characters long' );
        }
        if ( isset( $password_not_matched ) ) {
            $self->createError( 'Passwords do not match' );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'password_repeat', 'Repeat' );
        $self->createInput( 'password', 'password_repeat', 'password_repeat' );
        $self->createLabel( 'email', 'Email' );
        if ( isset( $email_invalid ) ) {
            $self->createError( 'This is not a valid email' );
        }
        if ( isset( $email_used ) ) {
            $self->createError( 'Email is already used' );
            $email_value = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'email' ] ) ) {
            $email_value = $_SESSION[ 'create_post' ][ 'email' ];
            unset( $_SESSION[ 'create_post' ][ 'email' ] );
        }
        else {
            $email_value = "";
        }
        $self->createInput( 'text', 'email', 'email', $email_value );
        $self->createLabel( 'dob', 'Date of birth' );
        $days = createSelectPrepare( range( 1, 31 ), 'Select Day' );
        $self->createSelect( $days, 'day' );
        $months = createSelectPrepare( range( 1, 12 ), 'Select Month' );
        $self->createSelect( $months, 'month' );
        $current_year = date( 'Y' );
        $years = createSelectPrepare (
            range( $current_year - $config[ 'age' ][ 'min' ], $current_year - $config[ 'age' ][ 'max' ] ),
            'Select Year'
        );
        $self->createSelect( $years, 'year' );
        $countriesSelectArray[] = 'Select Country';
        foreach ( $countries as $key => $country ) {
            $countriesSelectArray[ $country->shortname ] = $country->name;
        }
        $self->createSelect( $countriesSelectArray, 'countryShortname', $location );
        $self->createInput( 'submit', '', '', 'Register' );
    } );

    require 'views/footer/view.php';
?>
