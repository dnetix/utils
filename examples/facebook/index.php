<?php require_once '../bootstrap.php';

    // This API requires a session this its not necessary using a framework
    session_start();

    // Check the bootstrap to see how this function works
    $facebook_login_url = facebook()->getLoginUrl();
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Dnetix\Social\FacebookHandler</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

</head>
<body>
    <h1>Facebook Connection Plugin</h1>

    <div class="container">
        <div class="well">Note that this example will not work unless this script its executed in a public server that facebook can redirect to, and you provide true facebook application data.</div>

        <p>This will send the user to the facebook platform and then will be redirected to the entry_point defined by the login_callback_url</p>
        <a href="<?= $facebook_login_url ?>">Accede con Facebook</a>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
