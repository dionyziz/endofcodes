<?php
    require_once 'views/header.php';
?>
<div id="home">
    <p class='center'>Last match ratings(global)</p>
    <ol class='center' id="toplist">
        <?php
            foreach( $ratings as $rating ) {
                foreach ( $rating as $user ) {
                    ?><li><a href=""><?php echo $user->username; ?></a></li><?php
                }
            } 
        ?>
    </ol>
    <?php
//        $filter = [ 
//            [ 'value' => 'default', 'content' => 'select Ratings' ], 
//            [ 'value' => 'global', 'content' => 'Global' ], 
//            [ 'value' => 'country', 'content' => 'By country' ]
//        ];
//        $form = new Form( 'dashboard', 'view' );
//        $form->id = 'selectbox';
//        $form->createSelect( 'filter', '', $filter );
        if ( isset( $user ) ) {
            require_once 'views/session/logoutform.php';
        }
    ?>
</div>
<?php
    require_once 'views/footer.php';
?>
