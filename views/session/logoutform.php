<form action="index.php?resource=session&amp;method=delete" method="post">
    <input type="hidden" name="token" value="<?php
        echo $token;
    ?>" />
    <input type="submit" value="Logout" />
</form>
