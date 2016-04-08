<?php
/*
 * @author Anakeen
 * @package WEBDESK
*/

function esvcrss(Action &$action)
{
    
    if (GetHttpVars("static") == 1) $action->lay->set("static", true);
    else $action->lay->set("static", false);
}