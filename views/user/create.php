<?php
    include 'views/header.php';
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
    $form->token = $token;
    $form->output( function() use( $username_empty, $username_invalid, $password_empty,
            $email_empty, $username_used, $password_small,
            $password_not_matched, $email_used, $email_invalid, $countries ) {
        if ( isset( $username_empty ) ) { 
            Form::produceError( 'Please type a username.' );
        }
        if ( isset( $username_invalid ) ) { 
            Form::produceError( 'Usernames can only have numbers, letters, "." and "_"' );
        }
        if ( isset( $password_empty ) ) {
            Form::produceError( 'Please type a password.' );
        }
        if ( isset( $email_empty ) ) {
            Form::produceError( 'Please type an email.' );
        }
        Form::createLabel( 'username', 'Username' );
        if ( isset( $username_used ) ) {
            Form::produceError( 'Username already exists' );
            $username_value = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'username' ] ) ) {
            $username_value = $_SESSION[ 'create_post' ][ 'username' ];
            unset( $_SESSION[ 'create_post' ][ 'username' ] );
        }
        else {
            $username_value = "";
        }
        Form::createInput( 'text', 'username', 'username', htmlspecialchars( $username_value ) );
        Form::createLabel( 'password', 'Password' );
        if ( isset( $password_small ) ) {
            Form::produceError( 'Password should be at least 7 characters long' );
        }
        if ( isset( $password_not_matched ) ) {
            Form::produceError( 'Passwords do not match' );
        }
        Form::createInput( 'password', 'password', 'password' );
        Form::createLabel( 'password_repeat', 'Repeat' );
        Form::createInput( 'password', 'password_repeat', 'password_repeat' );
        Form::createLabel( 'email', 'Email' );
        if ( isset( $email_invalid ) ) {
            Form::produceError( 'This is not a valid email' );
        }
        if ( isset( $email_used ) ) { 
            Form::produceError( 'Email is already used' );
            $email_value = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'email' ] ) ) {
            $email_value = $_SESSION[ 'create_post' ][ 'email' ];
            unset( $_SESSION[ 'create_post' ][ 'email' ] );
        }
        else {
            $email_value = "";
        }
        Form::createInput( 'text', 'email', 'email', htmlspecialchars( $email_value ) );
        Form::createLabel( 'dob', 'Date of birth' );
        $days_select_array = array();
        $days_select_array[] = array( 'content' => 'Select Day' );
        for ( $i = 1; $i <= 31; ++$i ) {
            $days_select_array[] = array( 'value' => $i, 'content' => $i );
        }
        Form::createSelect( 'day', 'dob', $days_select_array );
        $months_select_array = array();
        $months_select_array[] = array( 'content' => 'Select Month' );
        for ( $i = 1; $i <= 12; ++$i ) {
            $months_select_array[] = array( 
                'value' => $i, 
                'content' => date( 'M', mktime( 0, 0, 0, $i, 1, 2000 ) ) 
            );
        }
        Form::createSelect( 'month', '', $months_select_array );
        $years_select_array = array();
        $years_select_array[] = array( 'content' => 'Select Year' );
        $current_year = date( 'Y' );
        for ( $i = $current_year - 8; $i >= $current_year - 100; --$i ) {
            $years_select_array[] = array( 'value' => $i, 'content' => $i );
        }
        Form::createSelect( 'year', '', $years_select_array );
        $countries_select_array = array();
        $countries_select_array[] = array( 'content' => 'Select Country' );
        foreach ( $countries as $key => $country ) {
            $countries_select_array[] = array( 'value' => $key + 1, 'content' => $country[ 'name' ] );
        }
        Form::createSelect( 'countryid', '', $countries_select_array );
        Form::createInput( 'submit', '', '', 'Register' ); 
    } );

    include 'views/footer.php';
?>
