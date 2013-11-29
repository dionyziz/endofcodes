<?php
    ?><p>Last match ratings(global)</p>
    <ol>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
        <li>... : 10</li>
    </ol>
    <select>
        <option value="select">Select ratings</option>
        <option value="global">Global</option>
        <option value="friends">Friends</option>
        <option value="country">By country</option>
        <option value="date">By date</option>
    </select>
    <p>Your position: 4</p>
    <p>Score: 10</p>
    <p>Congratulations! you got up by 5 positions and 8 points since your last match</p>
    <p>Check your progress <a href="">here</a></p>
    <p>Check your programs process <a href="">here</a></p>
    <?php
    if ( isset( $username ) ) {
        include 'views/session/logoutform.php';
    }
?>
