<?php

/*   ---------------------------------------------

Author : Quentinv57

Licence : GNU General Public License v3
			(see http://www.gnu.org/licenses/)
			
Date of creation : 2011-04
Last modified : 2011-10-13

Meta Archival Script - creates all archive pages
			
---------------------------------------------   */

function createpage ($pn, $text)
# fct intermédiaire pour rendre le code plus lisible
{
	global $wpapi;
	$reason = 'init archive';
	
	echo "Creating [[$pn]]";
	$wpapi->edit ($pn, $text, $reason) ;
	
	echo "...\n";
	sleep(30);
}

$prefix = '/home/quentinv57/commons/';

# Inclusion des fichiers de configuration
include $prefix. 'class/http.class.php';
include $prefix. 'class/wikiapi.class.php';
include $prefix. 'class/wikiadmin.class.php';

define('WIKIURL', 'meta.wikimedia.org');
define('WIKIUSERNAME', 'QuentinvBot');
define('WIKIUSERPASSWD', '');
#exit("Maintenance de bot : le script doit être testé au moins une fois manuellement pour vérifier les derniers changements\n");

$wpapi = new wikipediaapi(WIKIURL);
$wpadm = new wikipediaadmin(WIKIURL);

# Connexion à Wikipédia
$wpapi->login ( WIKIUSERNAME, WIKIUSERPASSWD ) ;



$array_feed = array(
'Talk:Spam blacklist/Archives/' => "{{Archive header}}

== Proposed additions ==
{| style=\"border:1px solid #AAA; background:#f9f9f9; width:100%; margin:0 auto 1em auto; padding:.2em; text-align:justify;\"
|style=\"width:50px;\"|[[File:Symbol comment vote.svg|50px]]
|style=\"padding-left:.2em;\"|This section is for completed requests that a website be blacklisted
|}

== Proposed removals ==
{| style=\"border:1px solid #AAA; background:#f9f9f9; width:100%; margin:0 auto 1em auto; padding:.2em; text-align:justify;\"
|style=\"width:50px;\"|[[File:Symbol comment vote.svg|50px]]
|style=\"padding-left:.2em;\"|This section is for archiving proposals that a website be ''un''listed.
|}

== Troubleshooting and problems ==
{| style=\"border:1px solid #AAA; background:#f9f9f9; width:100%; margin:0 auto 1em auto; padding:.2em; text-align:justify;\"
|style=\"width:50px;\"|[[File:Symbol comment vote.svg|50px]]
|style=\"padding-left:.2em;\"|This section is for archiving Troubleshooting and problems.
|}


== Discussion ==
{| style=\"border:1px solid #AAA; background:#f9f9f9; width:100%; margin:0 auto 1em auto; padding:.2em; text-align:justify;\"
|style=\"width:50px;\"|[[File:Symbol comment vote.svg|50px]]
|style=\"padding-left:.2em;\"|This section is for archiving Discussions.
|}
",

'Steward requests/Checkuser/' => "__NOINDEX__ 
{{archive header}} 
[[Category:Steward requests archive/Checkuser]]

== Requests ==
",

'Steward requests/Global/' => "{{archive header}}{{NOINDEX}}
[[Category:Steward requests archive/Global]]

== Requests for global (un)block ==

== Requests for global (un)lock and (un)hiding ==
",

'Steward requests/Global permissions/' => "__NOINDEX__ 
{{archive header}} 
[[Category:Steward requests archive/Global permissions]]

== Requests for global rollback permissions ==

== Requests for global sysop permissions ==

== Requests for global editinterface permissions ==

== Requests for global IP block exemption ==
",

'Steward requests/Bot status/' => "__NOINDEX__ 
{{archive-header}} 
[[Category:Steward requests archive/Bot status]]

== Global bot status requests ==

== Removal of global bot status ==

== Bot status requests ==

== Removal of bot status ==
",

'Steward requests/Permissions/' => "__NOINDEX__ 
{{archive header}} 
[[Category:Steward requests archive/Permissions]]

== Administrator access ==

== Bureaucrat access ==

== CheckUser access ==

== Oversight access ==

== Removal of access ==

== Temporary permissions (expired and rejected requests only) ==

== Miscellaneous requests ==
",

'Steward requests/SUL requests/' => "__NOINDEX__ 
{{archive header}} 
[[Category:Steward requests archive/SUL requests]]

== Requests ==
",

'Meta:Changing username/Archives/' => "{{archive header}}
[[Category:Closed Meta-Wiki requests for changing username|Changing username]]

==Requests==
",

'Steward requests/Username changes/' => "__NOINDEX__ 
{{archive header}} 
[[Category:Steward requests archive/Username changes]]

== Requests for renaming your own account ==

== Requests for renaming an account of another person ==
"
);

if (date('m')!=12)	$sfx = date('Y') . '-' . str_pad((date('m')+1), 2, 0, STR_PAD_LEFT) ;
else				$sfx = (date('Y')+1) . '-' . '01';

foreach($array_feed as $pn => $text)
{
	$pn .= $sfx ;

	if ($wpapi->page_empty($pn))
	{		
		createpage($pn, $text);
	}
	else echo "Skipping [[$pn]] (already created)\n";
}




	
?>
