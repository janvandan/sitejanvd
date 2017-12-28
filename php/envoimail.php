<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">"
echo "<html lang=\"fr\">"
echo "<head>"
echo "<title>Test envoimail.php</title>"
echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" />"
echo "</head>"
echo "<body>"
/* Si le formulaire est envoyé alors on fait les traitements */
if (isset($_POST['envoye']))
{
    /* Récupération des valeurs des champs du formulaire */
    if (get_magic_quotes_gpc())
    {
      $name	     	= stripslashes(trim($_POST['name']));
      $from     	= stripslashes(trim($_POST['email']));
      $subject    = stripslashes(trim($_POST['email'])); /* stripslashes(trim($_POST['sujet'])); */
      $message		= stripslashes(trim($_POST['message']));
    }
    else
    {
      $name		    = trim($_POST['name']);
      $from     	= trim($_POST['email']);
      $subject		= trim($_POST['email']); /* trim($_POST['sujet']); */
      $message		= trim($_POST['message']);
    }

    /* Expression régulière permettant de vérifier si le
    * format d'une adresse e-mail est correct */
    $regex_mail = '/^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$/i';

    /* Expression régulière permettant de vérifier qu'aucun
    * en-tête n'est inséré dans nos champs */
    $regex_head = '/[\n\r]/';

    /* Si le formulaire n'est pas posté de notre site on renvoie
    * vers la page d'accueil */
    if (preg_match('/^http:\/\/janvd\.fr\/index\.html$',$_SERVER['HTTP_REFERER']))
    {
      echo "OK HTTP_REFERE = $_SERVER['HTTP_REFERER']";
      echo "name = $name";
      echo "from = $from";
      echo "subject = $subject";
      echo "message = $message";
    }
    else
    {
      echo "KO HTTP_REFERE = $_SERVER['HTTP_REFERER']";
      echo "name = $name";
      echo "from = $from";
      echo "subject = $subject";
      echo "message = $message";
    }

    /*if($_SERVER['HTTP_REFERER'] != 'http://janvd.fr/*')
    {
        header('Location: http://janvd.fr');
    }*/
} else {
  echo "notset $_POST['envoye']";
}
echo "</body>"
echo "</html>"
?>
    /* On vérifie que tous les champs sont remplis */
    elseif (empty($civilite)
           || empty($nom)
           || empty($expediteur)
           || empty($sujet)
           || empty($message))
    {
      $alert = 'Tous les champs doivent être renseignés';
    }
    /* On vérifie que le format de l'e-mail est correct */
    elseif (!preg_match($regex_mail, $expediteur))
    {
      $alert = 'L\'adresse '.$expediteur.' n\'est pas valide';
    }
    /* On vérifie qu'il n'y a aucun header dans les champs */
    elseif (preg_match($regex_head, $expediteur)
            || preg_match($regex_head, $nom)
            || preg_match($regex_head, $sujet))
    {
        $alert = 'En-têtes interdites dans les champs du formulaire';
    }
    /* Si aucun problème et aucun cookie créé, on construit le message et on envoie l'e-mail */
    elseif (!isset($_COOKIE['sent']))
    {
        /* Destinataire (votre adresse e-mail) */
        $to = 'moi@domaine.com';

        /* Construction du message */
        $msg  = 'Bonjour,'."\r\n\r\n";
        $msg .= 'Ce mail a été envoyé depuis monsite.com par '.$civilite.' '.$nom."\r\n\r\n";
        $msg .= 'Voici le message qui vous est adressé :'."\r\n";
        $msg .= '***************************'."\r\n";
        $msg .= $message."\r\n";
        $msg .= '***************************'."\r\n";

        /* En-têtes de l'e-mail */
        $headers = 'From: '.$nom.' <'.$expediteur.'>'."\r\n\r\n";

        /* Envoi de l'e-mail */
        if (mail($to, $sujet, $msg, $headers))
        {
            $alert = 'E-mail envoyé avec succès';

            /* On créé un cookie de courte durée (ici 120 secondes) pour éviter de
            * renvoyer un mail en rafraichissant la page */
            setcookie("sent", "1", time() + 120);

            /* On détruit la variable $_POST */
            unset($_POST);
        }
        else
        {
            $alert = 'Erreur d\'envoi de l\'e-mail';
        }

    }
    /* Cas où le cookie est créé et que la page est rafraichie, on détruit la variable $_POST */
    else
    {
        unset($_POST);
    }
}
?>
