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
    $form->output( function( $self ) use( $countries ) {
        global $config

   ?><div class="form-group"><?php
        $self->createLabel( 'username', 'Username', [ 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10"><?php
        $self->createInput( 'text', 'username', 'username', '', [
            'class' => 'form-control',
            'disabled' => 'true',
            'placeholder' => 'pkakelas'
        ] );
        ?></div>
    </div>
    <div class="form-group"><?php
        $self->createLabel( 'name', 'Name', [ 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10" id='name-input'><?php
            $self->createInput( 'text', 'name', 'name', '', [
                'class' => 'form-control',
                'placeholder' => 'Name'
            ] ); 
            $self->createInput( 'text', 'surname', 'surname', '', [
                'class' => 'form-control',
                'placeholder' => 'Surname'
            ] ); 
        ?></div>
    </div>
    <div class="form-group form-inline"><?php
        $self->createLabel( 'day', 'Birthday', [ 'class' => 'col-sm-2 control-label' ] );
        ?><div id='dob' class="row"><?php
            $days = createSelectPrepare( range( 1, 31 ), 'Select Day' );
            $self->createSelect( $days, 'day', '', '', [ 'class' => 'form-control dob-input'] );
            $months = createSelectPrepare( range( 1, 12 ), 'Select Month' );
            $self->createSelect( $months, 'month', '', '', [ 'class' => 'form-control dob-input'] );
            $current_year = date( 'Y' );
            $years = createSelectPrepare (
                range( $current_year - $config[ 'age' ][ 'min' ], $current_year - $config[ 'age' ][ 'max' ] ),
                'Select Year'
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
    <div class="form-group"><?php
        $self->createLabel( 'email', 'Email', [ 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10"><?php
            $self->createInput( 'text', 'email', 'email', '', [
                'class' => 'form-control',
                'value' => 'pkakelas@gmail.com'
            ] ); 
        ?></div>
    </div>
    <!-- 
        <div class="form-group">
            <label class="col-sm-2 control-label">Website</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Website">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Github</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Github">
            </div>
        </div>
    -->
    <div class="form-group"><?php
        $self->createLabel( 'email', 'Email', [ 'id' => 'pswd-label', 'class' => 'col-sm-2 control-label' ] );
        ?><div class="col-sm-10" id='password-input'>
            <button id='pswd-change' class="btn btn-default">Change password</button><?php
            $self->createInput( 'password', 'password', '', '', [
                'class' => 'form-control',
                'placeholder' => 'Old password'
            ] ); 
            $self->createInput( 'password', 'password_new', '', '', [
                'class' => 'form-control',
                'placeholder' => 'Password'
            ] ); 
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
<aside id="default-popup" class="avgrund-popup text-center">
    <h2 class='text-danger'>Are you sure you want to delete your account?</h2>
    <p>
        After the deletion of your End Of Codes account you will not be able to participate to the games.
    </p>
    <p>
    <button class='btn btn-default' id='close-modal'>Cancel</button>
    <button class='btn btn-danger' id='delete-button'>Delete</button>
    </p>
</aside>

<?php
    require 'views/footer/view.php';
?>
