<?php
    require 'views/header.php';
?>
<h1 class='text-center' id='title'>Settings<h1>       
<?php
    $form = new Form( 'user', 'update' );
    $form->id = 'user-update-form';
    $form->attributes = [
        'class' => 'form-horizontal'
    ];
    $form->output( function( $self ) use( $email_invalid, $email_used, $password_wrong,
                                          $password_new_not_matched, $password_new_small, $countries, $user ) {
        global $config;

    ?><div class="form-group"><?php
        $self->createLabel( 'username', 'Username', [ 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10"><?php
        $self->createInput( 'text', 'username', 'username', '', [
            'class' => 'form-control',
            'disabled' => 'true',
            'placeholder' => htmlspecialchars( $user->username ) 
        ] );
        ?></div>
    </div>
    <!--
    <div class="form-group"><?php
      //  $self->createLabel( 'name', 'Name', [ 'class' => 'col-sm-2 control-label' ] );
      //  ?><div class="col-sm-10" id='name-input'><?php
      //      $self->createInput( 'text', 'name', 'name', '', [
      //          'class' => 'form-control',
      //          'placeholder' => 'Name'
      //      ] ); 
      //      $self->createInput( 'text', 'surname', 'surname', '', [
      //          'class' => 'form-control',
      //          'placeholder' => 'Surname'
      //      ] ); 
      ?></div>
    </div>
    -->
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
            $self->createSelect( $countriesSelectArray, 'countryShortname', $user->country->name, 'country-input', [ 'class' => 'form-control' ] );
        ?></div>
    </div>
    <div class="form-group"><?php
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
                'value' => htmlspecialchars( $user->email ) 
            ] ); 
        ?></div>
    </div>
    <!-- 
        <div class="form-group"><?php
          //  $self->createLabel( 'website', 'Website', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div class="col-sm-10"><?php
          //      $self->createInput( 'text', 'website', 'website', '', [
          //          'class' => 'form-control',
          //          'placeholder' => htmlspecialchars( $user->website );
          //      ] ); 
            ?></div>
        </div>
        <div class="form-group"><?php
          //  $self->createLabel( 'github', 'Github link', [ 'class' => 'col-sm-2 control-label' ] );
            ?><div class="col-sm-10"><?php
          //      $self->createInput( 'text', 'github', 'Github', '', [
          //          'class' => 'form-control',
          //          'placeholder' => htmlspecialchars( $user->github );
          //      ] ); 
            ?></div>
        </div>
    -->
    <div class="form-group"><?php
        $self->createLabel( 'password', 'Password', [ 'id' => 'pswd-label', 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10" id='password-input'><?php
            ?><button id='pswd-change' class="btn btn-default">Change password</button><?php
            if ( isset( $password_wrong ) ) {
                $self->createError( 'Old password is incorrect' );
            }
            $self->createInput( 'password', 'password', '', '', [
                'class' => 'form-control',
                'placeholder' => 'Old password'
            ] ); 
            if ( isset( $password_new_not_matched ) ) {
                $self->createError( 'Passwords do not match' );
            }
            $self->createInput( 'password', 'password_new', '', '', [
                'class' => 'form-control',
                'placeholder' => 'Password'
            ] ); 
            if ( isset( $password_new_small ) ) {
                $self->createError( 'Your password should be at least 7 characters long' );
            }
            $self->createInput( 'password', 'password_repeat', '', '', [
                'class' => 'form-control',
                'placeholder' => 'Repeat password'
            ] ); 
        ?></div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10"><?php
            $self->createSubmit( 'Update', [ 'class' => 'btn btn-primary'] );
        ?></div>
    </div><?php
} ); ?>
<div class='text-center'>
    <a id="delete-account">Delete your account</a>
</div>

<aside id="default-popup" class="avgrund-popup text-center"><?php
    $form = new Form( 'user', 'delete' );
    $form->id = 'user-delete-form';
    $form->output( function( $self ) {} );
    ?><h2 class='text-danger'>Do you want to delete your account?</h2>
    <p class='delete-description'>
        After deleting your End Of Codes account you will not be able to participate to the games.
    </p>
    <p>
    <button class='btn btn-default' id='close-modal'>Cancel</button>
    <button class='btn btn-danger' id='delete-button'>Delete</button>
    </p>
</aside>
<?php
    require 'views/footer/view.php';
?>
