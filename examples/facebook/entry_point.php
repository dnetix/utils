<?php require_once '../bootstrap.php';

    $fb = facebook();
    $graphUser = $fb->getMe();

    // Analize the attributes of the graphUser to obtain more information

    if($graphUser){
        // First check if already created the user in the database with the external ID
        $externalId = $graphUser->getId();

        $name = $graphUser->getName();
        // If it hasnt been created or in the database, obtain the values from the graphUser
        $email = $graphUser->getEmail();
        // Obtain the accessToken that will last 2 months
        $accessToken = $fb->getLongLivedAccessToken();
        // The accessToken provided will expire in two months, take that in account
        $expiresOn = date('Y-m-j H:i:s', strtotime('+2 month'));
        $profileImageUrl = $graphUser->getPicture()->getUrl();

        print_r($name);
        print_r($email);
        print_r($accessToken);
        print_r($expiresOn);
        echo '<img src="' . $profileImageUrl . '" alt="' . $name . '" />';

    }else{
        // A user has not been obtained so... handle that
    }

// Now to use it later, check the API of the Facebook/Facebook class but initialize with the accessToken Provided
