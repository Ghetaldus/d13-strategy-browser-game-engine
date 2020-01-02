<?php

// ========================================================================================
//
// API.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Author......................: BlackScorp
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// Just a very simple wrapper before switching to a more sophisticated soution for handling
// dependencies. This class wraps 3rd party plugin code. If one of the plugins changes, only
// the corresponding api function must be updated here.
// ========================================================================================

class d13_api

{
	
	// ----------------------------------------------------------------------------------------
	// email
	// @ This is basically a wrapper for the PHPMailer plugin
	// Copied straight from the PHPMailer setup documentation.
	// ----------------------------------------------------------------------------------------

	public static
	
	function email($fromEmail, $fromName, $to, $subject, $body)
	{
		require (CONST_INCLUDE_PATH . 'plugins/phpmailer/PHPMailerAutoload.php');

		$results_messages = array();
		$mail = new PHPMailer(true);
		$mail->CharSet = 'utf-8';

		try {
			if (!PHPMailer::validateAddress($to)) {
				throw new Exception("Email address " . $to . " is invalid -- aborting!");
			}

			$mail->isSMTP();
			$mail->SMTPDebug = 0;
			$mail->Host = "localhost";
			$mail->Port = "25";
			$mail->SMTPSecure = "none";
			$mail->SMTPAuth = false;
			$mail->addReplyTo($fromEmail, $fromName);
			$mail->setFrom($fromEmail, $fromName);
			$mail->addAddress($to);
			$mail->Subject = $subject;
			$mail->WordWrap = 78;
			$mail->msgHTML($body, dirname(__FILE__) , true);
			try {
				$mail->send();
			}
			
		}

		if ($mail->IsError()) {
			return 'emailNotSent';
		}
		else {
			return 'emailSent';
		}
	}

}

// =====================================================================================EOF

?>