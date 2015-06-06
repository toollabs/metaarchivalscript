<?php

/*   ---------------------------------------------

Author : Quentinv57

Licence : GNU General Public License v3
                        (see http://www.gnu.org/licenses/)

Date of creation : 2011-03
Last modified : 14 November 2014

Meta Archival Script
        -> archive steward requests on the following pages :
                * [[Talk:Spam blacklist]]
                * [[Steward requests/Checkuser]]
                * [[Steward requests/Global]]
                * [[Steward requests/Global permissions]]
                * [[Steward requests/Bot status]]
                * [[Steward requests/Permissions]]
                        > temp sysop requests are transfered to [[Steward requests/Permissions/Approved temporary]] now
                        > the requests on [[Steward requests/Permissions/Approved temporary]], once the right is removed, are archived as well
                * [[Steward requests/SUL requests]] (removed, outdated)
                * [[Meta:Changing username]] (removed, outdated)
                * [[Steward requests/Username changes]]
                * [[Steward requests/Miscellaneous]]

---------------------------------------------


Here are the settings
(you can set a value of $nbdays to 0 to skip the page)

*/

$settings_match = array (       'status_template'       => '#\{\{status\|((not )?done|cannot|withdrawn|local)\}\}#i',
                                                        'status_param'          => '#(\| *status *= *(<!--.*-->)?((not )?done|cannot|withdrawn|local)|\{\{status\|((not )?done|cannot|withdrawn|loca$
                                                        // 20111009 : replace '#\| *status *= *(<!--.*-->)?(not )?done#iU' by '#(\| *status *= *(<!--.*-->)?(not )?done|\{\{status\|(not )?done\}\})$
                                                        'sb_templates'          => '#\{\{(added|declined|cannot|removed|(not )?done|closing)\}\}#i',
                                                        'sc_templates'          => '#\| *status *= *(<!--.*-->)?(not )?done|cannot|withdrawn|local#iU',
                                                        'tempsysop'                     => '#\{\{TempSysop\|([0-9]+)\|([0-9]{4,})\|([0-9]+)\|([0-9]+)(\||\}\})#i',
                                                        'st_templates'          => '#\{\{status\|(added|declined|removed|(not )?done|closing|cannot)\}\}#i',
                                                        'removetemp'            => '#\'\'\'removed\'\'\'|\'\'\'extended\'\'\'#i'        );

$settings_archives = array (
        'Talk:Spam blacklist' => array( 'subpage' => 'Talk:Spam blacklist/Archives/',
                                                                        'nbdays' => 3,
                                                                        'subsections' => array('Proposed additions','Proposed removals'),
                                                                        'match' => $settings_match['sb_templates']),

        'Steward requests/Checkuser' => array(  'subpage' => 'Steward requests/Checkuser/',
                                                                                        'nbdays' => 4, // was 7 before
                                                                                        'subsections' => array('Requests'),
                                                                                        'match' => $settings_match['status_param']), // #\{\{status\|(not )?done\}\}#

        'Steward requests/Global' => array(     'subpage' => 'Steward requests/Global/',
                                                                                'nbdays' => 2, // previously 3
                                                                                'subsections' => array('Requests for global (un)block','Requests for global (un)lock and (un)hiding'),
                                                                                'match' => $settings_match['status_template']),

        'Steward requests/Global permissions' => array( 'subpage' => 'Steward requests/Global permissions/',
                                                                                                        'nbdays' => 2,
                                                                                                        'subsections' => array('Requests for global IP block exemption','Requests for global rename permissions','Requests for other global permissions'),
                                                                                                        'match' => $settings_match['status_param']),

        'Steward requests/Bot status' => array( 'subpage' => 'Steward requests/Bot status/',
                                                                                        'nbdays' => 2, // previously 3
                                                                                        'subsections' => array('Global bot status requests','Removal of global bot status','Bot status requests','Removal of bot status'),
                                                                                        'match' => $settings_match['status_param']),

        'Steward requests/Permissions' => array(        'subpage' => 'Steward requests/Permissions/',
                                                                                                'nbdays' => 2,
                                                                                                'subsections' => array('Administrator access','Bureaucrat access','CheckUser access','Oversight access','Removal of access','Miscellaneous requests'),
                                                                                                'match' => $settings_match['status_param'],
                                                                                                'lvlsect' => 3),

        'Steward requests/Miscellaneous' => array(        'subpage' => 'Steward requests/Miscellaneous/',
                                                                                                'nbdays' => 2,
                                                                                                'subsections' => array('Manual requests'),
                                                                                                'match' => $settings_match['st_templates']),

        'Steward requests/Username changes' => array(   'subpage' => 'Steward requests/Username changes/',
                                                                                                        'nbdays' => 2, // previously 3
                                                                                                        'subsections' => array('Simple rename requests','Requests involving usurps or other complications'),
                                                                                                        'match' => $settings_match['status_param'])
        );

$settings_archives_tempsysop = array (  'page' => 'Steward requests/Permissions',
                                                                                'suffix' => 'Approved temporary',
                                                                                'subsections' => array('Administrator access'),
                                                                                'match' => $settings_match['tempsysop'] ); // temp sysop rights archival

$settings_archives_approvedtemp = array (       'page' => 'Steward requests/Permissions/Approved temporary',
                                                                                        'arc_subpage' => 'Steward requests/Permissions/',
                                                                                        'arc_subsection' => 'Temporary permissions (expired and rejected requests only)',
                                                                                        'nbdays' => 0,
                                                                                        'match' => $settings_match['removetemp'] ); // archive requests on [[Steward requests/Permissions/Approved temporary]]
define ('DONTARCHIVESECT','{{User:SteinsplitterBot/DoNotArchiveSect}}');


define('WIKIURL', 'meta.wikimedia.org');
#define('WIKIURL', 'test.wikipedia.org');
define('WIKIUSERNAME', 'SteinsplitterBot');
define('WIKIUSERPASSWD', '');

#exit("Maintenance de bot : le script doit être testé au moins une fois manuellement pour vérifier les derniers changements\n");

/* ************************************************************************
****************************** Fonctions **********************************
************************************************************************ */

function preg_quote_magic ($foo)
# Retire tous les meta caractères (avec le #)
{
        return str_replace('#','\#',preg_quote($foo));
}

function get_content_by_section ($texte, $lvlsect)
{
        $result = array();

        $motif = '';
        for ($i=1; $i<=$lvlsect; $i++)
                $motif .= "=";
        $motif = $motif . " *([^=^ ][^=^\n]*[^=^ ]) *" . $motif . "[^=]" ;

        // si la sous-section n'apparait pas, on renvoie le texte
        if (!preg_match('#'.$motif.'#', $texte))
                return array('error'=>'NOTFOUND', 'content'=>$texte);

        // liste des sections
        preg_match_all ("#".$motif."#U", $texte, $listesections);
        $listesections = $listesections[1];

        // sinon : intro
        preg_match ("#^(.*)".preg_quote_magic($listesections[0])."#sU", $texte, $matches);
        $result[0]['wikititle'] = '';
        $result[0]['title'] = '';
        $result[0]['content'] = $matches[1];


        $i=1;
        foreach ($listesections as $key => $value) {
                // sections (1 à n-1)
                if ($i<count($listesections))
                {
                        $motif_1 = str_replace("[^=^ ][^=^\n]*[^=^ ]", preg_quote_magic($listesections[($i-1)]), $motif);
                        $motif_2 = str_replace("[^=^ ][^=^\n]*[^=^ ]", preg_quote_magic($listesections[$i]), $motif);

                        preg_match ("#(".$motif_1.")(.*)".$motif_2."#sU", $texte, $matches);

                        $result[$i]['wikititle'] = $matches[1];
                        $result[$i]['title'] = $matches[2];
                        $result[$i]['content'] = $matches[3];

                        $i++;
                }
                // section n
                else
                {
                        $motif_2 = str_replace("[^=^ ][^=^\n]*[^=^ ]", preg_quote_magic($listesections[($i-1)]), $motif);
                        preg_match ("#(".$motif_2.")(.*)$#sU", $texte, $matches);
                        $result[$i]['wikititle'] = $matches[1];
                        $result[$i]['title'] = $matches[2];
                        $result[$i]['content'] = $matches[3];
                }
        }

        return $result;
}

function NumberMonth ($foo)
{
        switch ($foo)
        {
                case 'January':         return 1;
                case 'February':        return 2;
                case 'March':           return 3;
                case 'April':           return 4;
                case 'May':             return 5;
                case 'June':            return 6;
                case 'July':            return 7;
                case 'August':          return 8;
                case 'September':       return 9;
                case 'October':         return 10;
                case 'November':        return 11;
                case 'December':        return 12;
                default: return 0;
        }
}

function FooMonth ($nb)
{
        switch ($nb)
        {
                case 1:                         return 'January';
                case 2:                         return 'February';
                case 3:                         return 'March';
                case 4:                         return 'April';
                case 5:                         return 'May';
                case 6:                         return 'June';
                case 7:                         return 'July';
                case 8:                         return 'August';
                case 9:                         return 'September';
                case 10:                        return 'October';
                case 11:                        return 'November';
                case 12:                        return 'December';
                default: return 0;
        }
}

function get_last_date ($text)
{ // retourne la dernière date trouvée dans le texte
        $date = preg_match_all("#([1-9]|[1-2][0-9]|3[01]) (January|February|March|April|May|June|July|August|September|October|November|December) ([0-9]{4}) \(UTC\)#i", $text, $matches);
        $lastdate = array();

        foreach ($matches[0] as $key => $value) {
                $d = intval($matches[1][$key]);
                $m = NumberMonth($matches[2][$key]);
                $y = intval($matches[3][$key]);

                if ($lastdate==array()) {
                        $lastdate = array('d'=>$d, 'm'=>$m, 'y'=>$y);
                } else {
                        if ($y>$lastdate['y'] OR ($y==$lastdate['y'] AND $m>$lastdate['m']) OR ($y==$lastdate['y'] AND $m==$lastdate['m'] AND $d>$lastdate['d']))
                                $lastdate = array('d'=>$d, 'm'=>$m, 'y'=>$y);
                }
        }

        return $lastdate;
}

function get_expiring_date ($sectiontext)
{ // extensions temp_sysop
        preg_match ("#([1-9]|[1-2][0-9]|3[01]) (January|February|March|April|May|June|July|August|September|October|November|December) ([0-9]{4})#i", $sectiontext, $matches);

        $d = intval($matches[1]);
        $m = NumberMonth($matches[2]);
        $y = intval($matches[3]);

        $lastdate = array('d'=>$d, 'm'=>$m, 'y'=>$y);

        return $lastdate;
}

function is_last_x_days ($date, $nbdays)
{ // TRUE si la date $date est plus vieille qu'il y a $nbdays jours
        $time = mktime (0,0,1, $date['m'], $date['d'], $date['y']);
        $time += $nbdays * 3600 * 24;

        if ($time<=time())      return TRUE;
        else                            return FALSE;
}

function lastmonth ()
{
        if (date('m')!=1)       return date('Y') . '-' . str_pad((date('m')-1), 2, 0, STR_PAD_LEFT) ;
        else                            return (date('Y')-1) . '-' . '12';
}

function numtoken ($length)
{
   $result = '';

   for ($i=0; $i<=$length; $i++) {
      $result .= rand(0, 9);
   }

   return $result;
}

function zerofill ($num, $length)
{
        $result = $num;

        for ($i=0; $i<($length-strlen($num)); $i++)
                $result = '0'.$result;

        return $result;
}

/* ************************************************************************
************************ Fonction principale  *****************************
************************************************************************ */

/*      $contentpagename est le nom de la page principale
        $archivepagename est le préfixe du nom de la page d'archive (avec /)
        $subsections est un tableau contenant les titres des sections de niveau 2 à archiver
        $nbdaysexec est le nombre de jours sans réponse min. après lesquels on archive le sujet
        $matchrgx (facultatif) est une régex qui doit être trouvée dans la sous-section pour qu'elle soit archivée
        <<$notmatchrgx (facultatif) est une régex qui doit être trouvée dans la sous-section pour qu'elle soit archivée>>
        $lvlsect (facultatif) est le niveau des sections données dans $subsections
*/

function archiveprocess ($contentpagename, $archivepagename, $subsections, $nbdaysexec, $matchregx=NULL, $lvlsect=2)
{
        global $wpapi;

        echo "Working on [[$contentpagename]]...\n";

        $contentpage = $wpapi->getpage($contentpagename);
        $difflen = strlen($contentpage);
        $archivepage = array();
        $archivedrequests = array('total'=>0);

        // Pour monter jusqu'au niveau de section $lvlsect, s'il est strict. supérieur à deux, on prend seulement le contenu de la première section de titre niveau 2, etc.
        for ($i=2; $i<=$lvlsect; $i++) {
                if ($i==2) $sub = get_content_by_section ($contentpage, 2);
                else $sub = get_content_by_section ($sub[1]['content'], $i);
        }
        unset($sub[0]);

        foreach ($subsections as $nsub => $title)
        {
                $id = NULL;
                foreach ($sub as $key2=>$temp) {
                        if ($sub[$key2]['title']==$title) $id = $key2;
                }

                if ($id != NULL)
                {
                        echo "-> ".$sub[$id]['title']."\n";

                        $token = numtoken(100);
                        $motif = "== *([^=^ ][^=^\n]*[^=^ ]) *==( *\n|$)"; // + "^\n", + " *" (2 lines), changing place of "\n" on $actumotif - " *\n" replaced by "( *\n|$)"
                        $actumotif = "\n== *(" .preg_quote_magic($sub[$id]['title']). ") *== *";

                        foreach ($archivepage as $key => $value)
                        {
                                ## INIT ARCHIVE PAGE WITH TOKEN - Begin
                                if (preg_match("#".$actumotif.".*(".$motif.")#sU", $archivepage[$key], $matches))
                                        $archivepage[$key] = str_replace ($matches[2], $token.$matches[2], $archivepage[$key]);
                                else // le problème d'archivage *venait* d'ici
                                {
                                        if (!preg_match("#".$actumotif."#",$archivepage[$key])) exit('Script Aborted : Error in ARCHIVE PAGE WITH TOKEN (1) - unable to find "'.$actumotif.'" in "'.$key.'"');
                                        else $archivepage[$key] .= $token;
                                }

                                if (!preg_match("#\n".$token."#",$archivepage[$key]))
                                        $archivepage[$key] = str_replace($token, "\n".$token, $archivepage[$key]);
                                ## INIT ARCHIVE PAGE WITH TOKEN - End
                        }

                        $sect1 = get_content_by_section ($sub[$id]['content'], ($lvlsect+1));
                        unset($sect1[0]);

                        foreach ($sect1 as $key => $value)
                        {
                                if ((empty($matchregx) OR preg_match($matchregx, $sect1[$key]['content'])) AND !preg_match('#'.preg_quote_magic(DONTARCHIVESECT).'#i', $sect1[$key]['content']))
                                { // 20111011 - Adding a condition that will prevent requests containing the DONTARCHIVESECT template to be archived
                                        $lastdate = get_last_date($sect1[$key]['content']);

                                        if (!empty($lastdate) AND is_last_x_days($lastdate,$nbdaysexec))
                                        {
                                                $lastdate_formated = $lastdate['y'].'-'.str_pad($lastdate['m'], 2, 0, STR_PAD_LEFT);

                                                if (empty($archivepage[$lastdate_formated]))
                                                { // si on a pas encore eu besoin de cette page d'archive, on l'initialise
                                                        $archivepage[$lastdate_formated] = $wpapi->getpage($archivepagename.$lastdate_formated);
                                                        $archivedrequests[$lastdate_formated] = 0;

                                                        ## INIT ARCHIVE PAGE WITH TOKEN - Begin
                                                        if (preg_match("#".$actumotif.".*(".$motif.")#sU", $archivepage[$lastdate_formated], $matches))
                                                                $archivepage[$lastdate_formated] = str_replace ($matches[2], $token.$matches[2], $archivepage[$lastdate_formated]);
                                                        else // le problème d'archivage *venait* d'ici
                                                        {
                                                                if (!preg_match("#".$actumotif."#",$archivepage[$lastdate_formated])) {
                                                                        // var_dump($archivepage[$lastdate_formated]);
                                                                        exit('Script Aborted : Error in ARCHIVE PAGE WITH TOKEN (2) - unable to find '.$actumotif.' in '.$lastdate_formated);
                                                                } else {
                                                                        $archivepage[$lastdate_formated] .= $token;
                                                                        //echo "Attention ! (<2>)\n";
                                                                }
                                                        }

                                                        if (!preg_match("#\n".$token."#",$archivepage[$lastdate_formated]))
                                                                $archivepage[$lastdate_formated] = str_replace($token, "\n".$token, $archivepage[$lastdate_formated]);
                                                        if (!preg_match("#".$token."\n#",$archivepage[$lastdate_formated]))
                                                                $archivepage[$lastdate_formated] = str_replace($token, $token."\n", $archivepage[$lastdate_formated]);
                                                        ## INIT ARCHIVE PAGE WITH TOKEN - End
                                                }

                                                // on archive la requête $key
                                                $contentpage = str_replace($sect1[$key]['wikititle'].$sect1[$key]['content'], '', $contentpage);
                                                $archivepage[$lastdate_formated] = str_replace($token, $sect1[$key]['wikititle'].$sect1[$key]['content']."\n".$token, $archivepage[$lastdate_formated]);
                                                $archivedrequests['total']++;
                                                $archivedrequests[$lastdate_formated]++;
                                                # Test (Attention certaines requetes sont supprimées mais non archivées !)
                                                echo $lastdate_formated."<->".$sect1[$key]['title']."\n";
                                        }
                                }
                        }

                        foreach ($archivepage as $key => $value)
                                $archivepage[$key] = str_replace($token, '', $archivepage[$key]);
                }
                else echo "Warning : section $title not found.\n";
        }

        // Retrait des doubles espaces dans la page d'archive
        foreach ($archivepage as $key => $value)
                $archivepage[$key] = preg_replace("#\n\n\n#", "\n\n", $archivepage[$key]);

        // Substitution de la page
        $difflen -= strlen($contentpage); // diff de longueur (en bytes) de la page
        if ( $archivedrequests['total']>2 || ($difflen>4000) )
        { // on n'archive que si on a au moins deux requêtes (ou si la page perd plus de 5000 bytes)
                $archivesummary = ($archivedrequests['total']>1) ? $archivedrequests['total'] . " requests archived" : "1 request archived";
                $wpapi->edit($contentpagename, $contentpage, $archivesummary, TRUE);

                foreach ($archivepage as $key => $value) {
                        sleep(5);
                        $archivesummary = ($archivedrequests[$key]>1) ? $archivedrequests[$key] . " requests archived" : "1 request archived";
                        $wpapi->edit($archivepagename.$key, $value, $archivesummary, FALSE);
                }

                echo $archivedrequests['total'] . " request(s) archived with success !\n\n";
                sleep(20);
        }
        else echo "No edit(".$archivedrequests['total']." request(s) - $difflen bytes)\n\n";
}


/* ************************************************************************
************************ Extension temp_sysop  ****************************
************************************************************************ */

function archiveprocess_tempsysop ($contentpagename, $archivepagename, $subsections, $nbdaysexec, $matchregx=NULL, $lvlsect=2)
{
        global $wpapi;

        echo "Working on [[$contentpagename]] (temp sysop requests) ...\n";

        $contentpage = $wpapi->getpage($contentpagename);
        $archivepage = $wpapi->getpage($archivepagename);
        $difflen = strlen($contentpage);
        $archivedrequests = 0;


        // Pour monter jusqu'au niveau de section $lvlsect, s'il est strict. supérieur à deux, on prend seulement le contenu de la première section de titre niveau 2, etc.
        for ($i=2; $i<=$lvlsect; $i++) {
                if ($i==2) $sub = get_content_by_section ($contentpage, 2);
                else $sub = get_content_by_section ($sub[1]['content'], $i);
        }
        unset($sub[0]);

        foreach ($subsections as $nsub => $title)
        {
                $id = NULL;
                foreach ($sub as $key2=>$temp) {
                        if ($sub[$key2]['title']==$title) $id = $key2;
                }

                if ($id != NULL)
                {
                        echo "-> ".$sub[$id]['title']."\n";

                        $motif = "== *([^=^ ][^=^\n]*[^=^ ]) *==( *\n|$)"; // + "^\n", + " *" (2 lines), changing place of "\n" on $actumotif - " *\n" replaced by "( *\n|$)"
                        $actumotif = "\n== *(" .preg_quote_magic($sub[$id]['title']). ") *== *";

                        // No need to archive page with tooken - especially because this page does not have the same format

                        $sect1 = get_content_by_section ($sub[$id]['content'], ($lvlsect+1));
                        unset($sect1[0]);

                        $archivesect = get_content_by_section ($archivepage, 2);
                        foreach ($archivesect as $key => $value)
                        {
                                $expiredate = get_expiring_date($archivesect[$key]['title']);
                                $archivesect[$key]['date'] = $expiredate['y'].zerofill($expiredate['m'],2).zerofill($expiredate['d'],2);
                        }
                        // Sort by date BEGIN
                        $archivedsect_date=array();
                        foreach ($archivedsect as $value2)
                                $archivedsect_date[]=$value2['date'];
                        array_multisort($archivedsect_date,SORT_NUMERIC,$archivedsect);
                        // Sort by date END

                        foreach ($sect1 as $key => $value)
                        {
                                if ((empty($matchregx) OR preg_match($matchregx, $sect1[$key]['content'], $matches)) AND !preg_match('#'.preg_quote_magic(DONTARCHIVESECT).'#i', $sect1[$key]['content']))
                                { // 20111011 - Adding a condition that will prevent requests containing the DONTARCHIVESECT template to be archived
                                        $lastdate = get_last_date($sect1[$key]['content']);

                                        if (!empty($lastdate) AND is_last_x_days($lastdate,$nbdaysexec))
                                        {
                                                // get template data :
                                                $template_data['delay'] = $matches[1];
                                                $template_data['year'] = $matches[2];
                                                $template_data['month'] = $matches[3];
                                                $template_data['day'] = $matches[4];
                                                $datefoo = $template_data['year'].$template_data['month'].$template_data['day'];

                                                $token = numtoken(100);
                                                $title = '== Expiring: '.number_format($template_data['day']).' '.FooMonth($template_data['month']).' '.$template_data['year'].' ==';
                                                $enddate = 1; // la date située juste après notre section

                                                foreach ($archivesect as $key2 => $value2)
                                                {
                                                        if ($archivesect[$key2]['date'] == $datefoo)
                                                                $enddate=0;

                                                        elseif ($archivesect[$key2]['date'] > $datefoo) {
                                                                if ($enddate) { // si la section n'existe pas encore
                                                                        $archivepage = str_replace ($archivesect[$key2]['wikititle'].$archivesect[$key2]['content'], $title."\n".$token."\n".$archivesect[$key2]['wikititle'].$archivesect[$key2]['content'], $archivepage);
                                                                } else { // si la section existe déjà, on ne la crée pas
                                                                        $archivepage = str_replace ($archivesect[$key2]['wikititle'].$archivesect[$key2]['content'], $token."\n".$archivesect[$key2]['wikititle'].$archivesect[$key2]['content'], $archivepage);
                                                                }

                                                                $enddate=0;
                                                                break;
                                                        }
                                                }
                                                if ($enddate)
                                                { // Cas où on met la requête à la fin de la liste
                                                        $archivepage .= "\n\n".$title."\n".$token;
                                                }


                                                // on archive la requête $key
                                                $contentpage = str_replace($sect1[$key]['wikititle'].$sect1[$key]['content'], '', $contentpage);
                                                $archivepage = str_replace($token, $sect1[$key]['wikititle'].$sect1[$key]['content'], $archivepage);
                                                $archivedrequests++;
                                                # Test (Attention certaines requetes sont supprimées mais non archivées !)
                                                echo "->".$sect1[$key]['title']."\n";

                                                // Add it in the array $archivesect (avoids problems if we archive two temp requests that expire the same day)
                                                $archivesect[] = array ('wikititle'=>$sect1[$key]['wikititle'], 'title'=>$sect1[$key]['title'], 'content'=>$sect1[$key]['content'], 'date'=>$datefoo);
                                                // Sort by date BEGIN
                                                $archivedsect_date=array();
                                                foreach ($archivedsect as $value2)
                                                        $archivedsect_date[]=$value2['date'];
                                                array_multisort($archivedsect_date,SORT_NUMERIC,$archivedsect);
                                                // Sort by date END
                                        }
                                }
                        }

                }
                else echo "Warning : section $title not found.\n";
        }

        // Retrait des doubles espaces dans la page d'archive
        $archivepage = preg_replace("#\n\n\n#", "\n\n", $archivepage);

        // Substitution de la page
        $difflen -= strlen($contentpage); // diff de longueur (en bytes) de la page
        if ( $archivedrequests>0 )
        { // pas de condition d'archivage pour les temp requests
                $archivesummary = ($archivedrequests>1) ? $archivedrequests . " requests moved to [[Steward requests/Permissions/Approved temporary]]" : "1 request moved to [[Steward requests/Permissions/Approved temporary]]";
                $wpapi->edit($contentpagename, $contentpage, $archivesummary, TRUE);

                sleep(5);
                $archivesummary = ($archivedrequests>1) ? $archivedrequests . " temp sysop requests archived" : "1 temp sysop request archived";
                $wpapi->edit($archivepagename, $archivepage, $archivesummary, FALSE);

                echo $archivedrequests . " request(s) archived with success !\n\n";
                sleep(20);
        }
        else echo "No edit(".$archivedrequests['total']." request(s) - $difflen bytes)\n\n";
}


/* ************************************************************************
************************ Extension approved_temp  ****************************
************************************************************************ */

function archiveprocess_approvedtemp ($contentpagename, $archivepagename, $archive_subsection, $nbdaysexec, $matchregx=NULL, $lvlsect=2)
{
        global $wpapi;

        echo "Working on [[$contentpagename]]...\n";

        $contentpage = $wpapi->getpage($contentpagename);
        $difflen = strlen($contentpage);
        $archivepage = array();
        $archivedrequests = array('total'=>0);

        $token = numtoken(100);
        $motif = "== *([^=^ ][^=^\n]*[^=^ ]) *==( *\n|$)"; // + "^\n", + " *" (2 lines), changing place of "\n" on $actumotif - " *\n" replaced by "( *\n|$)"
        $actumotif = "\n== *(" .preg_quote_magic($archive_subsection). ") *== *";

        $expiredates = get_content_by_section ($contentpage, 2);
        unset($expiredates[0]);

        foreach ($expiredates as $ndate => $contentdate)
        {
                $sect1 = get_content_by_section ($expiredates[$ndate]['content'], ($lvlsect+1));
                unset($expiredates[0]);

                $nbarchivedinsect1 = 0; // nb de requêtes archivées dans la section sect1

                foreach ($sect1 as $key => $value)
                {
                        if ((empty($matchregx) OR preg_match($matchregx, $sect1[$key]['content'])) AND !preg_match('#'.preg_quote_magic(DONTARCHIVESECT).'#i', $sect1[$key]['content']))
                                { // 20111011 - Adding a condition that will prevent requests containing the DONTARCHIVESECT template to be archived
                                $lastdate = get_last_date($sect1[$key]['content']);

                                if (!empty($lastdate) AND is_last_x_days($lastdate,$nbdaysexec))
                                {
                                        $lastdate_formated = $lastdate['y'].'-'.str_pad($lastdate['m'], 2, 0, STR_PAD_LEFT);

                                        if (empty($archivepage[$lastdate_formated]))
                                        { // si on a pas encore eu besoin de cette page d'archive, on l'initialise
                                                $archivepage[$lastdate_formated] = $wpapi->getpage($archivepagename.$lastdate_formated);
                                                $archivedrequests[$lastdate_formated] = 0;

                                                ## INIT ARCHIVE PAGE WITH TOKEN - Begin
                                                if (preg_match("#".$actumotif.".*(".$motif.")#sU", $archivepage[$lastdate_formated], $matches))
                                                        $archivepage[$lastdate_formated] = str_replace ($matches[2], $token.$matches[2], $archivepage[$lastdate_formated]);
                                                else // le problème d'archivage *venait* d'ici
                                                {
                                                        if (!preg_match("#".$actumotif."#",$archivepage[$lastdate_formated])) {
                                                                // var_dump($archivepage[$lastdate_formated]);
                                                                exit('Script Aborted : Error in ARCHIVE PAGE WITH TOKEN (2) - unable to find '.$actumotif.' in '.$lastdate_formated);
                                                        } else {
                                                                $archivepage[$lastdate_formated] .= $token;
                                                                //echo "Attention ! (<2>)\n";
                                                        }
                                                }

                                                if (!preg_match("#\n".$token."#",$archivepage[$lastdate_formated]))
                                                        $archivepage[$lastdate_formated] = str_replace($token, "\n".$token, $archivepage[$lastdate_formated]);
                                                if (!preg_match("#".$token."\n#",$archivepage[$lastdate_formated]))
                                                        $archivepage[$lastdate_formated] = str_replace($token, $token."\n", $archivepage[$lastdate_formated]);
                                                ## INIT ARCHIVE PAGE WITH TOKEN - End
                                        }


                                        // on archive la requête $key
                                        $nbarchivedinsect1++;
                                        $contentpage = str_replace($sect1[$key]['wikititle'].$sect1[$key]['content'], '', $contentpage);
                                                // important : retirer le titre niveau 2 (date d'expiration) si c'est la seule / la dernière requête archivée pour ce jour
                                                if ((count($sect1)-1)==$nbarchivedinsect1)
                                                        $contentpage = str_replace($expiredates[$ndate]['wikititle'], '', $contentpage);

                                        $archivepage[$lastdate_formated] = str_replace($token, $sect1[$key]['wikititle'].$sect1[$key]['content']."\n".$token, $archivepage[$lastdate_formated]);
                                        $archivedrequests['total']++;
                                        $archivedrequests[$lastdate_formated]++;
                                        # Test (Attention certaines requetes sont supprimées mais non archivées !)
                                        echo $lastdate_formated."<->".$sect1[$key]['title']."\n";
                                }
                        }
                }
        }

        // Retrait des doubles espaces dans la page d'archive
        foreach ($archivepage as $key => $value)
        {
                $archivepage[$key] = preg_replace("#(== *" .preg_quote_magic($archive_subsection). " *== *)\n\n#", "$1\n", $archivepage[$key]); // + (pour faire joli, retrait des espaces indésirables)
                $archivepage[$key] = str_replace($token, "\n", $archivepage[$key]); // + (idem)
                $archivepage[$key] = preg_replace("#\n\n\n#", "\n\n", $archivepage[$key]);
        }

        // Substitution de la page
        $difflen -= strlen($contentpage); // diff de longueur (en bytes) de la page
        if ( $archivedrequests['total']>2 || ($difflen>4000) ) // + lastdate <---------- todo !
        { // on n'archive que si on a au moins deux requêtes (ou si la page perd plus de 5000 bytes)
                $archivesummary = ($archivedrequests['total']>1) ? $archivedrequests['total'] . " requests archived" : "1 request archived";
                $wpapi->edit($contentpagename, $contentpage, $archivesummary, TRUE);

                foreach ($archivepage as $key => $value) {
                        sleep(5);
                        $archivesummary = ($archivedrequests[$key]>1) ? $archivedrequests[$key] . " requests archived" : "1 request archived";
                        $wpapi->edit($archivepagename.$key, $value, $archivesummary, FALSE);
                }

                echo $archivedrequests['total'] . " request(s) archived with success !\n\n";
                sleep(20);
        }
        else echo "No edit(".$archivedrequests['total']." request(s) - $difflen bytes)\n\n";
}


/* ************************************************************************
************************* Corps de programme ******************************
************************************************************************ */

/* done 2011-04-23 */

$prefix = '/data/project/sbot/meta/script_cabot/';

# Inclusion des fichiers de configuration
include $prefix. 'class/http.class.php';
include $prefix. 'class/wikiapi.class.php';
include $prefix. 'class/wikiadmin.class.php';

$wpapi = new wikipediaapi(WIKIURL);
$wpadm = new wikipediaadmin(WIKIURL);

# Connexion à Wikipédia
$wpapi->login ( WIKIUSERNAME, WIKIUSERPASSWD ) ;


foreach ($settings_archives as $arcpagetitle => $arcpageset)
{
        if (!empty($arcpagetitle) && !empty($arcpageset['subpage']) && !empty($arcpageset['subsections']) && is_int($arcpageset['nbdays']))
        {
                $arcpageset['match'] = (empty($arcpageset['match'])) ? 2 : $arcpageset['match'];
                $arcpageset['lvlsect'] = (empty($arcpageset['lvlsect'])) ? 2 : $arcpageset['lvlsect'];

                if ($arcpageset['nbdays']>0)
                        if ($arcpagetitle==$settings_archives_tempsysop['page'])
                        {
                                // archives temp sysop requests first
                                archiveprocess_tempsysop ($settings_archives_tempsysop['page'], $settings_archives_tempsysop['page'].'/'.$settings_archives_tempsysop['suffix'], $settings_archives_tempsysop['subsections'], $arcpageset['nbdays'], $settings_archives_tempsysop['match'], $arcpageset['lvlsect']);
                                archiveprocess_approvedtemp ($settings_archives_approvedtemp['page'], $settings_archives_approvedtemp['arc_subpage'], $settings_archives_approvedtemp['arc_subsection'], $settings_archives_approvedtemp['nbdays'], $settings_archives_approvedtemp['match'], $arcpageset['lvlsect']);
                        }

                        archiveprocess ($arcpagetitle, $arcpageset['subpage'], $arcpageset['subsections'], $arcpageset['nbdays'], $arcpageset['match'], $arcpageset['lvlsect']);
        }
        else exit('Error : please check settings definition.');
}

?>
