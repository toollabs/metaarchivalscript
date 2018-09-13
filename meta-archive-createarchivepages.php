<?php

/*   ---------------------------------------------

Author : Quentinv57 (2011 - 2014)
		 Steinsplitter (2014 - )

Licence : GNU General Public License v3
						(see http://www.gnu.org/licenses/)

Date of creation : 2011-04

Meta Archival Script - creates all archive pages

---------------------------------------------   */

function createpage( $pn, $text ) {
# fct intermédiaire pour rendre le code plus lisible
		global $site;
		$reason = 'init archive';

		echo "Creating [[$pn]]";
		$site->initPage( $pn )->edit( $text, $reason );

		echo "...\n";
		sleep( 5 );
}

// Dependency: https://github.com/MW-Peachy/Peachy
require '/data/project/sbot/Peachy/Peachy/Init.php';

$site = Peachy::newWiki( "meta" );
$site->set_runpage( null );

$array_feed = [
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

== Requests for global IP block exemption ==

== Requests for global rename permissions ==

== Requests for other global permissions ==
",

'Steward requests/Bot status/' => "__NOINDEX__
{{archive-header}}
[[Category:Steward requests archive/Bot status]]

== Global bot status requests ==

== Removal of global bot status ==

== Bot status requests ==

== Removal of bot status ==
",

'Steward requests/Miscellaneous/' => "__NOINDEX__
{{archive-header}}

== Manual requests ==
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

'Steward requests/Username changes/' => "__NOINDEX__
{{archive header}}
[[Category:Steward requests archive/Username changes]]

== Simple rename requests ==

== Requests involving merges, usurps or other complications ==
"
];

if ( date( 'm' ) != 12 ) {      $sfx = date( 'Y' ) . '-' . str_pad( ( date( 'm' ) + 1 ), 2, 0, STR_PAD_LEFT );
} else {                            $sfx = ( date( 'Y' ) + 1 ) . '-' . '01';
}

foreach ( $array_feed as $pn => $text ) {
		global $site;
		$pn .= $sfx;
		$esum = "";

		$es = $site->initPage( $pn );
		if ( !( $es->get_exists() ) ) {
				$site->initPage( $pn )->edit( $text, $reason );
		} else { echo "Skipping [[$pn]] (already created)\n";
		}
}

?>
