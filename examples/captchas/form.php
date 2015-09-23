<?php require_once '../bootstrap.php';

    $recaptcha = new \Dnetix\Captchas\ReCaptcha($config_recaptcha);

?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Dnetix\Dates\DateHelper</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <?= $recaptcha->getHeadTag() ?>

</head>
<body>
<h1>Recaptcha Google</h1>

<form method="post" action="check.php">

    <?= $recaptcha->getFormTag() ?>

    <button type="submit" class="btn btn-primary btn-lg">Submit</button>

</form>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
