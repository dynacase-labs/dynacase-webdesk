<?php
/*
 * @author Anakeen
 * @package WEBDESK
*/

include_once "WHAT/Lib.Http.php";
include_once "FDL/Lib.Dir.php";
include_once "WEBDESK/svcrss.php";

function freedomrss(Action & $action)
{
    
    $dbaccess = $action->dbaccess;
    header('Content-type: text/xml; charset=utf-8');
    
    $rssid = GetHttpVars("rssid", -1);
    
    if ($rssid == - 1) $rsslink = "";
    else {
        $doc = new_doc($dbaccess, $rssid);
        $rsslink = urlencode($doc->getRssLink());
    }
    $max = (GetHttpVars("max", 10) > 0 ? GetHttpVars("max", 10) : 100);
    $fcard = GetHttpVars("fcard", 0);
    
    setHttpVar("rss", $rsslink);
    setHttpVar("max", $max);
    setHttpVar("vfull", $fcard);
    setHttpVar("dlg", 0);
    
    $action->lay = new Layout($action->GetLayoutFile("svcrss.xml") , $action);
    svcrss($action);
}
