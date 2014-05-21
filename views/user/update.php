<?php
    require 'views/header.php';
?>

<div class="text-center"><?php
    if( isset( $valid_bot ) ) {
        ?><p>Your bot is set up to play the next game</p><?php
    }
    ?><p><a href='bot/update'>Configure Bot.</a></p><?php
    $form = new Form( 'user', 'update' );
    $form->output( function( $self ) use( $email_invalid, $email_used, $password_wrong,
                                          $password_new_not_matched, $password_new_small, $countries, $user ) {
        global $config;

        ?><p>Change email</p><?php
        $self->createLabel( 'email', 'Email' );
        if ( isset( $email_invalid ) ) {
            $self->createError( 'Email is not valid' );
        }
        if ( isset( $email_used ) ) {
            $self->createError( 'Email is already in use.' );
        }
        $self->createInput( 'text', 'email', 'email', htmlspecialchars( $user->email ) );
        ?><p>Change password</p><?php
        $self->createLabel( 'password', 'Old password' );
        if ( isset( $password_wrong ) ) {
            $self->createError( 'Old password is incorrect' );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'password_new', 'New password' );
        if ( isset( $password_new_not_matched ) ) {
            $self->createError( 'Passwords do not match' );
        }
        else if ( isset( $password_new_small ) ) {
            $self->createError( 'Your password should be at least 7 characters long' );
        }
        $self->createInput( 'password', 'password_new', 'password_new' );
        $self->createLabel( 'password_repeat', 'Repeat' );
        $self->createInput( 'password', 'password_repeat', 'password_repeat' );
        ?><p>Change country</p><?php
        $countries_select_array[] = 'Select Country';
        foreach ( $countries as $key => $country ) {
            $countries_select_array[ $country->shortname ] = $country->name;
        }
        $self->createSelect( $countries_select_array, 'countryShortname' );
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
        $self->createLabel( 'name', 'Name' );
        $self->createInput( 'text', 'name' );
        $self->createLabel( 'surname', 'Surname' );
        $self->createInput( 'text', 'surname' );
        $self->createLabel( 'website', 'Website' );
        $self->createInput( 'text', 'website' );
        $self->createLabel( 'github', 'Github' );
        $self->createInput( 'text', 'github' );
        $self->createSubmit( 'Save settings' );
    } );

    $form = new Form( 'user', 'delete' );
    $form->output( function( $self ) {
        $self->createSubmit( 'Delete your account' );
    } );
?></div>

<?php
    require 'views/footer/view.php';
?>
