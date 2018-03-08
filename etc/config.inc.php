<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

/*
 * See https://github.com/phppgadmin/phppgadmin/blob/master/conf/config.inc.php-dist
 * for descriptions of the $conf variables
 *
 */


/* Ensure we got the environment */
$vars = array(
    'PPA_HOST',
    'PPA_HOSTS',
    'PPA_DESC',
    'PPA_DESCS',
    'PPA_PORT',
    'PPA_PORTS',
    'PPA_SSLMODE',
    'PPA_SSLMODES',
    'PPA_DEFAULTDB',
    'PPA_DEFAULTDBS',
    'PPA_DEFAULT_LANG',
    'PPA_AUTOCOMPLETE',
    'PPA_EXTRA_LOGIN_SECURITY',
    'PPA_OWNED_ONLY',
    'PPA_SHOW_COMMENTS',
    'PPA_SHOW_ADVANCED',
    'PPA_SHOW_SYSTEM',
    'PPA_MIN_PASSWORD_LENGTH',
    'PPA_LEFT_WIDTH',
    'PPA_THEME',
    'PPA_SHOW_OIDS',
    'PPA_MAX_ROWS',
    'PPA_MAX_CHARS',
    'PPA_USE_XHTML_STRICT',
    'PPA_HELP_BASE',
    'PPA_AJAX_REFRESH',
    'PPA_PLUGINS',
);

foreach ($vars as $var) {
    $env = getenv($var);
    if (!isset($_ENV[$var]) && $env !== false) {
        $_ENV[$var] = $env;
    }
}

/* Figure out hosts */

/* Fallback to default linked */
$hosts = array('db');

/* Set by environment */
if (!empty($_ENV['PPA_HOST'])) {
    $hosts      = array($_ENV['PPA_HOST']);
    $descs      = array($_ENV['PPA_DESC']);
    $ports      = array($_ENV['PPA_PORT']);
    $sslmodes   = array($_ENV['PPA_SSLMODE']);
    $defaultdbs = array($_ENV['PPA_DEFAULTDB']);
} elseif (!empty($_ENV['PPA_HOSTS'])) {
    $hosts      = explode(',', $_ENV['PPA_HOSTS']);
    $descs      = explode(',', $_ENV['PPA_DESCS']);
    if (!empty($_ENV['PPA_PORTS'])) {
        $ports      = explode(',', $_ENV['PPA_PORTS']);
    }
    if (!empty($_ENV['PPA_SSLMODES'])) {
        $sslmodes   = explode(',', $_ENV['PPA_SSLMODES']);
    }
    if (!empty($_ENV['PPA_DEFAULTDBS'])) {
        $defaultdbs = explode(',', $_ENV['PPA_DEFAULTDBS']);
    }
}

/* Server settings */
for ($i = 1; isset($hosts[$i - 1]); $i++) {
    $conf['servers'][$i]['host'] = $hosts[$i - 1];
    $conf['servers'][$i]['pg_dump_path'] = '/usr/bin/pg_dump';
    $conf['servers'][$i]['pg_dumpall_path'] = '/usr/bin/pg_dumpall';
    if (isset($descs[$i - 1])) {
        $conf['servers'][$i]['desc'] = $descs[$i - 1];
    } else {
        $conf['servers'][$i]['desc'] = $hosts[$i - 1];
    }
    if (isset($ports[$i - 1])) {
        $conf['servers'][$i]['port'] = $ports[$i - 1];
    } else {
        $conf['servers'][$i]['port'] = '5432';
    }
    if (isset($sslmodes[$i - 1])) {
        $conf['servers'][$i]['sslmode'] = $sslmode[$i - 1];
    } else {
        $conf['servers'][$i]['sslmode'] = 'allow';
    }
    if (isset($defaultdbs[$i - 1])) {
        $conf['servers'][$i]['defaultdb'] = $defaultdbs[$i - 1];
    } else {
        $conf['servers'][$i]['defaultdb'] = 'postgres';
    }
}

if (!empty($_ENV['PPA_DEFAULT_LANG'])) {
    $conf['default_lang'] = $_ENV['PPA_DEFAULT_LANG'];
} else {
    $conf['default_lang'] = 'auto';
}

if (!empty($_ENV['PPA_AUTOCOMPLETE'])) {
    $conf['autocomplete'] = $_ENV['PPA_AUTOCOMPLETE'];
} else {
    $conf['autocomplete'] = 'default on';
}

if (!empty($_ENV['PPA_EXTRA_LOGIN_SECURITY'])) {
    $conf['extra_login_security'] = $_ENV['PPA_EXTRA_LOGIN_SECURITY'];
} else {
    $conf['extra_login_security'] = true;
}

if (!empty($_ENV['PPA_OWNED_ONLY'])) {
    $conf['owned_only'] = true;
} else {
    $conf['owned_only'] = false;
}

if (!empty($_ENV['PPA_SHOW_COMMENTS'])) {
    $conf['show_comments'] = $_ENV['PPA_SHOW_COMMENTS'];
} else {
    $conf['show_comments'] = true;
}

if (!empty($_ENV['PPA_SHOW_ADVANCED'])) {
    $conf['show_advanced'] = $_ENV['PPA_SHOW_ADVANCED'];
} else {
    $conf['show_advanced'] = false;
}

if (!empty($_ENV['PPA_SHOW_SYSTEM'])) {
    $conf['show_system'] = $_ENV['PPA_SHOW_SYSTEM'];
} else {
    $conf['show_system'] = false;
}

if (!empty($_ENV['PPA_MIN_PASSWORD_LENGTH'])) {
    $conf['min_password_length'] = $_ENV['PPA_MIN_PASSWORD_LENGTH'];
} else {
    $conf['min_password_length'] = 1;
}

if (!empty($_ENV['PPA_LEFT_WIDTH'])) {
    $conf['left_width'] = $_ENV['PPA_LEFT_WIDTH'];
} else {
    $conf['left_width'] = 200;
}

if (!empty($_ENV['PPA_THEME'])) {
    $conf['theme'] = $_ENV['PPA_THEME'];
} else {
    $conf['theme'] = 'default';
}

if (!empty($_ENV['PPA_SHOW_OIDS'])) {
    $conf['show_oids'] = $_ENV['PPA_SHOW_OIDS'];
} else {
    $conf['show_oids'] = false;
}

if (!empty($_ENV['PPA_MAX_ROWS'])) {
    $conf['max_rows'] = $_ENV['PPA_MAX_ROWS'];
} else {
    $conf['max_rows'] = 30;
}

if (!empty($_ENV['PPA_MAX_CHARS'])) {
    $conf['max_chars'] = $_ENV['PPA_MAX_CHARS'];
} else {
    $conf['max_chars'] = 50;
}

if (!empty($_ENV['PPA_USE_XHTML_STRICT'])) {
    $conf['use_xhtml_strict'] = $_ENV['PPA_USE_XHTML_STRICT'];
} else {
    $conf['use_xhtml_strict'] = false;
}

if (!empty($_ENV['PPA_HELP_BASE'])) {
    $conf['help_base'] = $_ENV['PPA_HELP_BASE'];
} else {
    $conf['help_base'] = 'http://www.postgresql.org/docs/%s/interactive/';
}

if (!empty($_ENV['PPA_AJAX_REFRESH'])) {
    $conf['ajax_refresh'] = $_ENV['PPA_AJAX_REFRESH'];
} else {
    $conf['ajax_refresh'] = 3;
}

if (!empty($_ENV['PPA_PLUGINS'])) {
    $conf['plugins'] = explode(',', $_ENV['PPA_PLUGINS']);
} else {
    $conf['plugins'] = array();
}
  /*****************************************
   * Don't modify anything below this line *
   *****************************************/

  $conf['version'] = 19;

?>
