<?php

/* envoi du mail formatte dans les pages du site. Php inspire de http://a-pellegrini.developpez.com/tutoriels/php/mail/ */

	if (isset($_GET['debug']))
	{
		$debug = $_GET['debug'];
		echo "debug activ&eacute; = $debug<br \>";
	}
	else
	{
		$debug = 0;
	}

	if ($debug >= 1) echo "debut<br \>";

	/* Si le formulaire est envoyé alors on fait les traitements */
	if (isset($_POST['envoye']))
	{
		if ($debug >= 1) echo "formulaire post&eacute;<br \>";

		/* Récupération des valeurs des champs du formulaire */
		if (get_magic_quotes_gpc())
		{
			$name		= stripslashes(trim($_POST['name']));
			$from		= stripslashes(trim($_POST['email']));
			$subject	= stripslashes(trim($_POST['email'])); /* stripslashes(trim($_POST['sujet'])); */
			$message	= stripslashes(trim($_POST['message']));
			if ($debug >= 1) echo "get_magic_quotes_gpc ok, $name, $from, $subject, $message<br \>";
		}
		else
		{
			$name		= trim($_POST['name']);
			$from		= trim($_POST['email']);
			$subject	= trim($_POST['email']); /* trim($_POST['sujet']); */
			$message	= trim($_POST['message']);
			if ($debug >= 1) echo "get_magic_quotes_gpc ko, $name, $from, $subject, $message<br \>";
		}
	}	

	/* Expression régulière permettant de vérifier si le 
	*     * format d'une adresse e-mail est correct */
	$regex_mail = '/^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$/i';
	
	/* Expression régulière permettant de vérifier qu'aucun 
	*     * en-tête n'est inséré dans nos champs */
	$regex_head = '/[\n\r]/';
	
	/* Expression régulière permettant de vérifier si le 
	*     * format d'un nom est correct */
	$regex_name = '/^[a-zA-Z ]+$/';
	
	/* Si le formulaire n'est pas posté de notre site on renvoie 
	*     * vers la page d'accueil */
	if (!preg_match('/janvd.fr/',$_SERVER['HTTP_REFERER']))
	{
		if ($debug >= 1) echo $_SERVER['HTTP_HOST'];
		header('Location: http://janvd.fr/');
	}
	elseif (empty($name) || empty($from) || empty($message))
	{
		$alert = 'Tous les champs doivent &ecirc;tre renseign&eacute;s';
		if ($debug >= 1) echo "vient de janvd.fr<br \> mais $alert<br \>";
	}
	/* On vérifie que le format de l'e-mail est correct */
	elseif (!preg_match($regex_mail, $from))
	{
		$alert = 'L\'adresse '.$from.' n\'est pas valide';
		if ($debug >= 1) echo "vient de janvd.fr<br \> mais $alert<br \>";
	}
	/* On vérifie qu'il n'y a aucun header dans les champs */
	elseif (preg_match($regex_head, $from) || preg_match($regex_head, $name))
	{
		$alert = 'En-t&ecirc;tes interdites dans les champs du formulaire';
		if ($debug >= 1) echo "vient de janvd.fr<br \> mais $alert<br \>";
	}
	/* On vérifie que le format du nom est correct */
	elseif (!preg_match($regex_name, $name))
	{
		$alert = 'Le nom '.$name.' n\'est pas valide';
		if ($debug >= 1) echo "vient de janvd.fr<br \> mais $alert<br \>";
	}
	/* Si aucun problème et aucun cookie créé, on construit le message et on envoie l'e-mail */
	elseif (!isset($_COOKIE['sent']))
	{
		/* Destinataire (votre adresse e-mail) */
		$to = 'jan@janvd.fr';
		
		/* Construction du message */
		$msg  = 'Bonjour,'."\r\n\r\n";
		$msg .= 'Ce mail a été envoyé depuis janvd.fr par '.$name."\r\n\r\n";
		$msg .= 'Le message :'."\r\n";
		$msg .= '***************************'."\r\n";
		$msg .= $message."\r\n";
		$msg .= '***************************'."\r\n";
		
		/* En-têtes de l'e-mail */
		$headers = 'From: '.$name.' <'.$from.'>'."\r\n\r\n";
		
		if (mail($to, $subject, $msg, $headers))
		{
			if ($debug >= 1) echo "Email envoy&eacute;<br \>";
		
			/* On créé un cookie de courte durée (ici 120 secondes) pour éviter de 
			*             * renvoyer un mail en rafraichissant la page */
			setcookie("sent", "1", time() + 120);
		
			/* On détruit la variable $_POST */
			unset($_POST);
		}
		else
		{
			$alert = 'Erreur d\'envoi de l\'e-mail';
			if ($debug >= 1) echo "$alert<br \>";
		}
		
	}
	/* Cas où le cookie est créé et que la page est rafraichie, on détruit la variable $_POST */
	else
	{
		if ($debug >= 1) echo "!! Page rafra&icirc;e<br \n>";
		unset($_POST);
		header('Location: http://janvd.fr/');
	}

	$backpage =  'Location: '.$_SERVER['HTTP_REFERER'];
	if ($debug >= 1) echo "backpage = $backpage<br \n>";

	if (!empty($alert)) echo '<p style="color:red">'.$alert.'</p>';

	if ($debug == 0) header($backpage);
?>
