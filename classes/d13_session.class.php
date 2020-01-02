<?php

// ========================================================================================
//
// SESSION.CLASS
//
// # Author......................: Robert Hafner
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// This SessionManager starts starts the php session (regardless of which handler is set)
// and secures it by locking down the cookie, restricting the session to a specific host and
// browser, and regenerating the ID.
//
// This is an attempt to secure the basic PHP sessions a little bit more.
//
// ========================================================================================

class d13_session

{
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		$this->sessionStart(CONST_PREFIX, CONST_SESSION_LIFETIME + time() , '/', CONST_DOMAIN);
	}

	// ----------------------------------------------------------------------------------------
	// sessionStart
	// This function starts, validates and secures a session.
	//
	// @param string $name The name of the session.
	// @param int $limit Expiration date of the session cookie, 0 for session only
	// @param string $path Used to restrict where the browser sends the cookie
	// @param string $domain Used to allow subdomains access to the cookie
	// @param bool $secure If true the browser only sends the cookie over https
	// ----------------------------------------------------------------------------------------
	public static
	
	function sessionStart($name, $limit = 0, $path = '/', $domain = null, $secure = null)
	{

		// Set the cookie name

		session_name($name . '_Session');

		// Set SSL level

		$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

		// Set session cookie options

		session_set_cookie_params($limit, $path, $domain, $https, true);
		session_start();

		// Make sure the session hasn't expired, and destroy it if it has

		if (self::validateSession()) {

			// Check to see if the session is new or a hijacking attempt

			if (!self::preventHijacking()) {

				// Reset session data and regenerate id

				$_SESSION = array();
				$_SESSION['IPaddress'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
				$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
				self::regenerateSession();

				// Give a 5% chance of the session id changing on any request

			}
			elseif (rand(1, 100) <= 5) {
				self::regenerateSession();
			}
		}
		else {
			$_SESSION = array();
			session_destroy();
			session_start();
		}

		// - - - - - SETUP DUMMY USER (when not logged in)

		if (!isset($_SESSION[CONST_PREFIX . 'User']['id'])) {
			$_SESSION[CONST_PREFIX . 'User']['color'] = CONST_DEFAULT_COLOR;
			$_SESSION[CONST_PREFIX . 'User']['template'] = CONST_DEFAULT_TEMPLATE;
			$_SESSION[CONST_PREFIX . 'User']['locale'] = CONST_DEFAULT_LOCALE;
			$_SESSION[CONST_PREFIX . 'User']['node'] = -1;
		}
	}

	// ----------------------------------------------------------------------------------------
	// regenerateSession
	// This function regenerates a new ID and invalidates the old session.
	// This should be called whenever permission levels for a user change.
	// ----------------------------------------------------------------------------------------

	public static
	function regenerateSession()
	{

		// If this session is obsolete it means there already is a new id

		if (isset($_SESSION['OBSOLETE']) || (isset($_SESSION['OBSOLETE']) && $_SESSION['OBSOLETE'] == true)) return;

		// Set current session to expire in 10 seconds

		$_SESSION['OBSOLETE'] = true;
		$_SESSION['EXPIRES'] = time() + 10;

		// Create new session without destroying the old one

		session_regenerate_id(false);

		// Grab current session ID and close both sessions to allow other scripts to use them

		$newSession = session_id();
		session_write_close();

		// Set session ID to the new one, and start it back up again

		session_id($newSession);
		session_start();

		// Now we unset the obsolete and expiration values for the session we want to keep

		unset($_SESSION['OBSOLETE']);
		unset($_SESSION['EXPIRES']);
	}

	// ----------------------------------------------------------------------------------------
	// validateSession
	// This function is used to see if a session has expired or not.
	// @return bool
	// ----------------------------------------------------------------------------------------

	protected static
	function validateSession()
	{
		if (isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES'])) return false;
		if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time()) return false;
		return true;
	}

	// ----------------------------------------------------------------------------------------
	// preventHijacking
	// This function checks to make sure a session exists and is coming from the proper host.
	// On new visits and hacking attempts this function will return false.
	// @return bool
	// ----------------------------------------------------------------------------------------

	protected static
	function preventHijacking()
	{
		if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])) return false;
		if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'] && !(strpos($_SESSION['userAgent'], 'Trident') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
			return false;
		}

		$sessionIpSegment = substr($_SESSION['IPaddress'], 0, 7);
		$remoteIpHeader = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$remoteIpSegment = substr($remoteIpHeader, 0, 7);
		if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) return false;
		return true;
	}
}

// =====================================================================================EOF