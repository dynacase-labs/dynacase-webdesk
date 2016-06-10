<?php
/*
 * @author Anakeen
 * @package WEBDESK
*/

function freedomrss_edit(Action & $action)
{
    
    $dbaccess = $action->dbaccess;
    
    include_once ("WHAT/Lib.Http.php");
    include_once ("FDL/Lib.Dir.php");
    
    $action->lay->eSet("rsstitle", "");
    $rssid = GetHttpVars("rssid", -1);
    if ($rssid > 0) {
        $doc = new_Doc($dbaccess, $rssid);
        if ($doc->isAffected()) $action->lay->eSet("rsstitle", $doc->getTitle());
    }
}
