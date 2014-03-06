<?php
    require_once 'views/header.php';
?>
<div id="home">
    <p class='center'>Last match ratings(global)</p>
    <ul class='center' id="toplist">
        <?php
            $i = 1;
            foreach( $ratings as $rating ) {
                foreach ( $rating as $user ) {
                    ?><li><a href=""><?php echo "$i.$user->username"; ?></a></li><?php
                }
                $i++;
            }
        ?>
    </ul>
    <?php
//      $filter = [
//          [ 'value' => 'default', 'content' => 'select Ratings' ],
//          [ 'value' => 'global', 'content' => 'Global' ],
//          [ 'value' => 'country', 'content' => 'By country' ]
//      ];
//      $form = new Form( 'dashboard', 'view' );
//      $form->id = 'selectbox';
//      $form->createSelect( 'filter', '', $filter );
        if ( isset( $user ) ) {
            require_once 'views/session/logoutform.php';
        }
    ?>
</div>
<?php
    require_once 'views/footer.php';
?>
