<?php
/*
 * @author Anakeen
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');

function gsvc(Action & $action)
{
    
    $svcname = strtolower(GetHttpVars("sname", ""));
    $svcact = strtolower(GetHttpVars("sact", ""));
    
    if ($svcname == "") {
        $action->lay->set("msg", "No service given!");
        return null;
    }
    if ($svcact != '' && !preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $svcact)) {
        $action->lay->set("msg", "Malformed action name!");
        return null;
    }
    if (strpos($svcname, '/') !== false) {
        $action->lay->set("msg", "Malformed service name!");
        return null;
    }
    
    $actfile = $svcname . ($svcact == "" ? "" : "_") . $svcact . ".php";
    if (!file_exists("WEBDESK" . DIRECTORY_SEPARATOR . $actfile)) {
        $action->lay->set("msg", "Service action file not found.");
        return null;
    }
    $actfunc = $svcname . ($svcact == "" ? "" : "_") . $svcact;
    if (!gsvc_func_check("WEBDESK" . DIRECTORY_SEPARATOR . $actfile, $actfunc)) {
        $action->lay->set("msg", "Function not found in action file.");
        return null;
    }
    $actlay = $svcname . ($svcact == "" ? "" : "_") . $svcact . ".xml";
    
    $lfile = $action->GetLayoutFile($actlay);
    if ($lfile != "") $action->lay = new Layout($lfile, $action);
    
    $hasfunc = false;
    include_once ($actfile);
    if (function_exists($actfunc)) {
        $hasfunc = true;
        $ret = call_user_func($actfunc, $action);
        return $ret;
    }
    
    if ($lfile == "" && !$hasfunc) {
        $action->lay->set("msg", "no layout, no action !");
    }
    return null;
}
/**
 * Basic security check by parsing the tokens to see if the target function
 * is defined in the file.
 *
 * Known issues:
 * - Class's methods are treated as plain functions (false positive).
 *
 * @param string $file File to examine
 * @param string $func Target function name
 * @return bool bool(true) if the target function is defined in the file, bool(false) if not.
 */
function gsvc_func_check($file, $func)
{
    /* Strip whitespace tokens */
    $tokens = array_values(array_filter(token_get_all(file_get_contents($file)) , function ($tok)
    {
        return !(is_int($tok[0]) && token_name($tok[0]) == 'T_WHITESPACE');
    }));
    /* Search for "function" keyword followed by the target function name */
    for ($i = 0; $i < count($tokens); $i++) {
        if (is_int($tokens[$i][0]) && (token_name($tokens[$i][0]) == 'T_FUNCTION') && (($i + 1) < count($tokens)) && is_array($tokens[$i + 1]) && (token_name($tokens[$i + 1][0]) == 'T_STRING') && (strtolower($tokens[$i + 1][1]) == strtolower($func))) {
            return true;
        }
    }
    return false;
}
