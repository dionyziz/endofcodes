<?php
    require 'views/header.php';
?>

   <ul><?php
    $handle = opendir( 'database/migration/' );
    while ( false !== ( $entry = readdir( $handle ) ) ) {
        if ( $entry != "." && $entry != ".." ) {
            ?><li><a href="database/migration/<?php echo $entry; ?>"><?php echo $entry; ?></a></li><?php
        }
    }
    ?></ul>
    
<?php
    require 'views/footer.php';
?>
