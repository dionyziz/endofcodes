function createError( form, description ) {
    $( '.alert' ).remove();
    $( form ).prepend( "<div class='alert alert-danger'>" + description + "</div>" );
}
