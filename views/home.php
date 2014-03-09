<?php
    include 'views/header.php';
?>
<div class="jumbotron">
    <h1 class="text-center">Programmers get ready...</h1>
    <p class="text-center" id="desc">End of Codes is a programming game. The project’s goal is to make a game targetting programmers in which each player has to use code to program a strategy for their bot to try and eliminate other players. The game aims to be a competitive programming platform in which programmers can compete for good rankings.</p>
</div>
<h2 id="ratings-title">Last game ratings</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Country</th>
            <th>Games won</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $i = 1;
            foreach ( $ratings as $rating ) {
                foreach ( $rating as $user ) {
                    if ( !isset( $user->country->name ) ) {
                        $country = "unknown";
                        
                    }
                    else {
                        $country = $user->country->name;
                    }
                    ?><tr>
                        <td><?php 
                            echo $i; 
                        ?></td>
                        <td><a href="index.php?resource=user&amp;method=view&amp;username=<?php 
                                echo $user->username;
                            ?>"><?php 
                                echo $user->username; 
                            ?></a>
                        </td>
                        <td><?php 
                            echo $country;
                        ?></td>
                        <td><?php 
                            echo 'Coming soon';
                        ?></td>
                    </tr><?php 
                }
                ++$i;
            }
        ?>
    </tbody>
</table>
<?php
    include 'views/footer.php';
?>
