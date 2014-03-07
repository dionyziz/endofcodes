<?php
    require_once 'views/header.php';
?>
<div id="home">
    <p>Last match ratings(global)</p>
    <ol id="toplist">
        <?php
            $i = 1;
            foreach( $ratings as $rating ) {
                foreach ( $rating as $user ) {
                    ?><li><a href=""><?php echo "$i.$user->username"; ?></a></li><?php
                }
                $i++;
            }
        ?>
    </ol>
    <?php
//      $filter = [
//          [ 'value' => 'default', 'content' => 'select Ratings' ],
//          [ 'value' => 'global', 'content' => 'Global' ],
//          [ 'value' => 'country', 'content' => 'By country' ]
//      ];
//      $form = new Form( 'dashboard', 'view' );
//      $form->id = 'selectbox';
//      $form->createSelect( 'filter', '', $filter );
    ?>
</div>
<?php
    require_once 'views/footer.php';
?>
