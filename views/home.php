<?php
    include 'views/header.php';
?>
<div class="jumbotron">
    <h1 class="text-center">Programmers get ready...</h1>
    <p class="text-center" id="desc">End of Codes is a programming game. The project’s goal is to make a game targetting programmers in which each player has to use code to program a strategy for their bot to try and eliminate other players. The game aims to be a competitive programming platform in which programmers can compete for good rankings.</p>
</div>
<h2 id="ratings-title"><a href='game/view?gameid=<?php
    echo $game->id;
?>'>Last game</a> ratings</h2>
<?php
    if ( $game->ended !== false ) {
        ?><table class="table table-striped">
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
                    if ( isset( $ratings ) ) {
                        foreach ( $ratings as $rating => $users ) {
                            if ( $rating > 10 ) {
                                break;
                            }
                            foreach ( $users as $user ) {
                                if ( !isset( $user->country->name ) ) {
                                    $countryName = "unknown";
                                }
                                else {
                                    $countryName = htmlspecialchars( $user->country->name );
                                }
                                ?><tr>
                                    <td><?php
                                        echo $rating;
                                    ?></td>
                                    <td>
                                        <a href="user/view?username=<?php
                                            echo htmlspecialchars( $user->username );
                                        ?>"><?php
                                            echo htmlspecialchars( $user->username );
                                        ?></a>
                                    </td>
                                    <td><?php
                                        echo $countryName;
                                    ?></td>
                                    <td><?php
                                        echo 'Coming soon';
                                    ?></td>
                                </tr><?php
                            }
                        }
                    }
                ?>
            </tbody>
        </table><?php
    }
    else {
        ?><p>Game is still in progress</p><?php
    }
    include 'views/footer.php';
?>
