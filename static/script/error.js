function createFormError( form, inputName, description, focus ) {
    focus = focus || true;
    $( '.alert' ).remove();
    $( "<div class='alert alert-danger'>" + description + "</div>" ).insertBefore( form + ' input[name=' + inputName + ']' );
    $( '[name=' + inputName + ']', form ).focus().select();
}
