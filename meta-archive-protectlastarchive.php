<?php

/*   ---------------------------------------------

Author : Quentinv57

Licence : GNU General Public License v3
			(see http://www.gnu.org/licenses/)
			
Date of creation : 2011-04
Last modified : 2011-08-29

Meta Archival Script - protect last archives
			
---------------------------------------------  */

function proteger ($arg1)
# fct intermédiaire pour rendre le code plus lisible
{
	global $wpadm;
	$reason = 'archive';
	
	echo "Protecting [[$arg1]]";
	$wpadm->protect ($arg1 , $reason) ;
	echo "...\n";
	sleep(30);
}



/* done 2011-04-23 
On suppose que ce script est executé au début du mois suivant (ex: le 1-7:5?)
*/

$prefix = '/home/quentinv57/script_cabot/newww/';

# Inclusion des fichiers de configuration
include $prefix. 'config/scripts.conf.php';
include $prefix. 'config/users.conf.php';
include $prefix. 'config/wikis.conf.php';

include $prefix. 'class/http.class.php';
include $prefix. 'class/wikiapi.class.php';
include $prefix. 'class/wikiadmin.class.php';

define('WIKIURL', 'meta.wikimedia.org');
define('WIKIUSERNAME', 'Quentinv57');
define('WIKIUSERPASSWD', "");

$wpapi = new wikipediaapi(WIKIURL);
$wpadm = new wikipediaadmin(WIKIURL);

# Connexion à Wikipédia
$wpapi->login ( WIKIUSERNAME, WIKIUSERPASSWD ) ;



$array_feed = array(
	'Talk:Spam blacklist/Archives/',
	'Steward requests/Checkuser/',
	'Steward requests/Global/',
	'Steward requests/Global permissions/',
	'Steward requests/Bot status/',
	'Steward requests/Permissions/',
	'Steward requests/SUL requests/'
);

if (date('m')!=1)	$sfx = date('Y') . '-' . str_pad((date('m')-1), 2, 0, STR_PAD_LEFT) ;
else				$sfx = (date('Y')-1) . '-' . '12';

foreach($array_feed as $pn)
{
	$pn .= $sfx ;

	proteger($pn);
}




	
?>