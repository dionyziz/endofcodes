<?php
    include 'views/header.php';
?>

<form action="index.php?resource=user&method=update" method="POST">
    <label for="password">New password</label>
    <input type="password" name="password" id="password" />
    <input type="submit" value="Change password" />
</form>

<?php
    include 'views/footer.php';
?>
