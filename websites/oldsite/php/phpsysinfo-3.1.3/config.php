<?php
if (!defined('PSI_CONFIG_FILE')) {
    /**
     * phpSysInfo version
     */
    define('PSI_VERSION','3.1.3');
    /**
     * phpSysInfo configuration
     */
    define('PSI_CONFIG_FILE', APP_ROOT.'/phpsysinfo.ini');

    /* default error handler */
    if (function_exists('errorHandlerPsi')) {
        restore_error_handler();
    }

    /* fatal errors only */
    $old_err_rep = error_reporting();
    error_reporting(E_ERROR);

    /* get git revision */
    if (file_exists(APP_ROOT.'/.git/HEAD')) {
        $contents = @file_get_contents(APP_ROOT.'/.git/HEAD');
        if ($contents && preg_match("/^ref:\s+(.*)\/([^\/\s]*)/m", $contents, $matches)) {
            $contents = @file_get_contents(APP_ROOT.'/.git/'.$matches[1]."/".$matches[2]);
            if ($contents && preg_match("/^([^\s]*)/m", $contents, $revision)) {
                define('PSI_VERSION_STRING', PSI_VERSION ."-".$matches[2]."-".substr($revision[1],0,7));
            } else {
                define('PSI_VERSION_STRING', PSI_VERSION ."-".$matches[2]);
            }
        }
    }
    /* get svn revision */
    if (!defined('PSI_VERSION_STRING') && file_exists(APP_ROOT.'/.svn/entries')) {
        $contents = @file_get_contents(APP_ROOT.'/.svn/entries');
        if ($contents && preg_match("/dir\n(.+)/", $contents, $matches)) {
            define('PSI_VERSION_STRING', PSI_VERSION."-r".$matches[1]);
        } else {
            define('PSI_VERSION_STRING', PSI_VERSION);
        }
    }
    if (!defined('PSI_VERSION_STRING')) {
        define('PSI_VERSION_STRING', PSI_VERSION);
    }

    /* get Linux code page */
    if (PHP_OS == 'Linux') {
        if (file_exists('/etc/sysconfig/i18n')) {
            $contents = @file_get_contents('/etc/sysconfig/i18n');
        } elseif (file_exists('/etc/default/locale')) {
            $contents = @file_get_contents('/etc/default/locale');
        } elseif (file_exists('/etc/locale.conf')) {
            $contents = @file_get_contents('/etc/locale.conf');
        } elseif (file_exists('/etc/sysconfig/language')) {
            $contents = @file_get_contents('/etc/sysconfig/language');
        } else {
            $contents = false;
            if (file_exists('/system/build.prop')) { //Android
                define('PSI_OS', 'Android');
                define('PSI_SYSTEM_CODEPAGE', 'UTF-8');
                if (!function_exists("proc_open")) { //proc_open function test by execyting 'pwd' command
                    define('PSI_MODE_POPEN', true); //use popen() function - no stderr error handling
                } else {
                    $out = '';
                    $err = '';
                    $pipes = array();
                    $descriptorspec = array(0=>array("pipe", "r"), 1=>array("pipe", "w"), 2=>array("pipe", "w"));
                    $process = proc_open("pwd 2>/dev/null ", $descriptorspec, $pipes);
                    if (!is_resource($process)) {
                        define('PSI_MODE_POPEN', true);
                    } else {
                        $w = null;
                        $e = null;
                        $read = array($pipes[1],$pipes[2]);
                        while (!(feof($pipes[1])&& feof($pipes[2])) && ($n = stream_select($read, $w, $e, 5)) !== false && $n > 0) {
                            $out .= fread($pipes[1], 4096);
                            $err .= fread($pipes[2], 4096);
                        }
                        if (is_null($out) || (trim($out) == "") || (substr(trim($out),0 ,1) != "/")) {
                            define('PSI_MODE_POPEN', true);
                        }
                        fclose($pipes[0]);
                        fclose($pipes[1]);
                        fclose($pipes[2]);
                        // It is important that you close any pipes before calling
                        // proc_close in order to avoid a deadlock
                        proc_close($process);
                    }
                }
            }
        }
        if ($contents && ( preg_match('/^(LANG="?[^"\n]*"?)/m', $contents, $matches)
           || preg_match('/^RC_(LANG="?[^"\n]*"?)/m', $contents, $matches))) {
            if (@exec($matches[1].' locale -k LC_CTYPE 2>/dev/null', $lines)) {
                foreach ($lines as $line) {
                    if (preg_match('/^charmap="?([^"]*)/', $line, $matches2)) {
                        define('PSI_SYSTEM_CODEPAGE', $matches2[1]);
                        break;
                    }
                }
            }
            if (@exec($matches[1].' locale 2>/dev/null', $lines)) {
                foreach ($lines as $line) {
                    if (preg_match('/^LC_MESSAGES="?([^\."@]*)/', $line, $matches2)) {
                        $lang = "";
                        if (is_readable(APP_ROOT.'/data/languages.ini') && ($langdata = @parse_ini_file(APP_ROOT.'/data/languages.ini', true))) {
                            if (isset($langdata['Linux']['_'.$matches2[1]])) {
                                $lang = $langdata['Linux']['_'.$matches2[1]];
                            }
                        }
                        if ($lang == "") {
                            $lang = 'Unknown';
                        }
                        define('PSI_SYSTEM_SYSLANG', $lang.' ('.$matches2[1].')');
                        break;
                    }
                }
            }

        }
    } elseif (PHP_OS == 'Haiku') {
            if (@exec('locale -m 2>/dev/null', $lines)) {
                foreach ($lines as $line) {
                    if (preg_match('/^"?([^\."]*)\.?([^"]*)/', $line, $matches2)) {

                        if ( isset($matches2[2]) && !is_null($matches2[2]) && (trim($matches2[2]) != "") ) {
                            define('PSI_SYSTEM_CODEPAGE', $matches2[2]);
                        }

                        $lang = "";
                        if (is_readable(APP_ROOT.'/data/languages.ini') && ($langdata = @parse_ini_file(APP_ROOT.'/data/languages.ini', true))) {
                            if (isset($langdata['Linux']['_'.$matches2[1]])) {
                                $lang = $langdata['Linux']['_'.$matches2[1]];
                            }
                        }
                        if ($lang == "") {
                            $lang = 'Unknown';
                        }
                        define('PSI_SYSTEM_SYSLANG', $lang.' ('.$matches2[1].')');
                        break;
                    }
                }
            }
    }

    if (!defined('PSI_SYSTEM_SYSLANG')) {
        define('PSI_SYSTEM_SYSLANG', null);
    }
    if (!defined('PSI_SYSTEM_CODEPAGE')) {
        define('PSI_SYSTEM_CODEPAGE', null);
    }

    /* restore error level */
    error_reporting($old_err_rep);

    /* restore error handler */
    if (function_exists('errorHandlerPsi')) {
        set_error_handler('errorHandlerPsi');
    }

    define('ARRAY_EXP', '/^return array \([^;]*\);$/'); //array expression search

    if (!is_readable(PSI_CONFIG_FILE) || !($config = @parse_ini_file(PSI_CONFIG_FILE, true))) {
        $tpl = new Template("/templates/html/error_config.html");
        echo $tpl->fetch();
        die();
    } else {
        foreach ($config as $name=>$group) {
            if (strtoupper($name)=="MAIN") {
                $name_prefix='PSI_';
            } else {
                $name_prefix='PSI_PLUGIN_'.strtoupper($name).'_';
            }
            foreach ($group as $param=>$value) {
                if ($value==="") {
                    define($name_prefix.strtoupper($param), false);
                } elseif ($value==1) {
                    define($name_prefix.strtoupper($param), true);
                } else {
                    if (strstr($value, ',')) {
                        define($name_prefix.strtoupper($param), 'return '.var_export(preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY),1).';');
                    } else {
                        define($name_prefix.strtoupper($param), $value);
                    }
                }
            }
        }
    }
}
