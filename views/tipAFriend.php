<?php

function contact(){
	$out = html("section") ->id( "contactForm");
	$formData = new Request( "post" );
	$formSubmitted = $formData ->get("submitted");
	if ($formSubmitted) {
		
		sendEmail($formData);
		$out ->after( html("h2", "Your e-mail was sent - thank you!"));
	}
	
	$out ->append( showContactForm() );
	return $out;
}

function showContactForm() {
	return'
		<form method="post" action="index.php?page=tipAFriend">
			<h2>Send us an e-mail</h2>
			<ol>
				<li>Your friends e-mail here</li>
				<li><input type="text" name="e-mail" /></li>
				<li>Subject:</li>
				<li><input type="text" name="subject" /></li>
				<li>Message: </li>
				<li><textarea name="friendsemail" cols="15" rows="5"></textarea></li>
				<li><input type="submit" name="submitted" value="Send off to the moon!" /></li>
			</ol>
		</form>';
}

function sendEmail( Request $formData){
	$from = $formData ->get("e-mail");
	$subject = $formData ->get("subject");
	$text = $formData ->get("friendsemail");
	$to = "$formData ->get("friendsemail");
	$mailer = new Mailer ();
	$mailer -> replyTo($from)->to($to)->subject($subject)->message($text);
	$mailer->send();
}