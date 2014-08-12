<?php
    require 'views/header.php';
?>

<h1 class='text-center' id='title'>Register<h1>
<?php
    $form = new Form( 'user', 'create' );
    $form->attributes = [
        'class' => 'form-horizontal'
    ];
    $form->id = 'register-form';
    $form->output( function( $self ) use( $username_empty, $username_invalid, $password_empty,
            $email_empty, $username_used, $password_small,
            $password_not_matched, $email_used, $email_invalid, $countries, $location ) {
        global $config;

        ?><div class="form-group"><?php
            if ( isset( $username_empty ) ) {
                $self->createError( 'Please type a username.' );
            }
            if ( isset( $username_invalid ) ) {
                $self->createError( 'Usernames can only have numbers, letters, "." and "_"' );
            }
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
            $self->createLabel( 'username', 'Username', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div class="col-sm-10"><?php
            $self->createInput( 'text', 'username', 'username', '', [
                'class' => 'form-control',
                'placeholder' => 'Username'
            ] );
            ?></div>
        </div>
        <div class="form-group"><?php
            $self->createLabel( 'password', 'Password', [ 'id' => 'pswd-label', 'class' => 'col-sm-2 control-label' ] );
            if ( isset( $password_empty ) ) {
                $self->createError( 'Please type a password.' );
            }
            if ( isset( $password_small ) ) {
                $self->createError( 'Password should be at least 7 characters long' );
            }
            if ( isset( $password_not_matched ) ) {
                $self->createError( 'Passwords do not match' );
            }
            ?><div class="col-sm-10" id='password-input'><?php
                $self->createInput( 'password', 'password', 'password', '', [
                    'class' => 'form-control',
                    'placeholder' => 'Password'
                ] );
                $self->createInput( 'password', 'password_repeat', 'password_repeat', '', [
                    'class' => 'form-control',
                    'placeholder' => 'Repeat password'
                ] );
            ?></div>
        </div>
        <div class="form-group"><?php
            if ( isset( $email_empty ) ) {
                $self->createError( 'Please type an email.' );
            }
            if ( isset( $email_invalid ) ) {
                $self->createError( 'Email is not valid' );
            }
            if ( isset( $email_used ) ) {
                $self->createError( 'Email is already in use' );
            }
            $self->createLabel( 'email', 'Email', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div class="col-sm-10"><?php
                $self->createInput( 'text', 'email', 'email', '', [
                    'class' => 'form-control',
                    'placeholder' => 'Email'
                ] );
            ?></div>
        </div>
        <div class="form-group form-inline"><?php
            $self->createLabel( 'day', 'Birthday', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div id='dob' class="row"><?php
                $days = createSelectPrepare( range( 1, 31 ), 'day' );
                $self->createSelect( $days, 'day', '', '', [ 'class' => 'form-control dob-input'] );
                $months = createSelectPrepare( range( 1, 12 ), 'month' );
                $self->createSelect( $months, 'month', '', '', [ 'class' => 'form-control dob-input'] );
                $current_year = date( 'Y' );
                $years = createSelectPrepare (
                    range( $current_year - $config[ 'age' ][ 'min' ], $current_year - $config[ 'age' ][ 'max' ] ),
                    'year'
                );
                $self->createSelect( $years, 'year', '', '', [ 'class' => 'form-control dob-input'] );
            ?></div>
        </div>
        <div class="form-group"><?php
            $self->createLabel( 'countryShortname', 'Country', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div class="col-sm-10"><?php
                $countriesSelectArray[] = 'Select Country';
                foreach ( $countries as $key => $country ) {
                    $countriesSelectArray[ $country->shortname ] = $country->name;
                }
                $self->createSelect( $countriesSelectArray, 'countryShortname', '', 'country-input', [ 'class' => 'form-control' ] );
            ?></div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10"><?php
                $self->createSubmit( 'Register', [ 'class' => 'btn btn-primary'] );
            ?></div>
        </div><?php
    } ); ?>

<?php
    require 'views/footer/view.php';
?>
