<?php
/**
 * waggo8 configuration
 */

$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '80';

const WG_DEBUG           = false; // Log for debug
const WG_SQLDEBUG        = false; // Log for SQL debug
const WG_SESSIONDEBUG    = false; // Log for Session debug
const WG_CONTROLLERDEBUG = false; // Log for Controller debug
const WG_MODELDEBUG      = false; // Log for Model debug
const WG_JSNOCACHE       = false; // Ignore cache for JS script

define( 'WG_INSTALLDIR' , realpath(__DIR__ . '/..') );

const WGCONF_DIR_ROOT                 = WG_INSTALLDIR;
const WGCONF_DIR_WAGGO                = WG_INSTALLDIR . '/sys/waggo8';
const WGCONF_DIR_PUB                  = WG_INSTALLDIR . '/pub';
const WGCONF_DIR_SYS                  = WG_INSTALLDIR . '/sys';
const WGCONF_DIR_TPL                  = WG_INSTALLDIR . '/tpl';
const WGCONF_CANVASCACHE              = WG_INSTALLDIR . '/temporary';
const WGCONF_DIR_UP                   = WG_INSTALLDIR . '/upload';
const WGCONF_DIR_RES                  = WG_INSTALLDIR . '/resources';
const WGCONF_DIR_LOG                  = WG_INSTALLDIR . "/logs";
const WGCONF_DIR_PLUGINS              = WG_INSTALLDIR . '/sys/plugins';
const WGCONF_DIR_FRAMEWORK            = WGCONF_DIR_WAGGO . '/framework';
const WGCONF_DIR_FRAMEWORK_MODEL      = WGCONF_DIR_FRAMEWORK . '/m';
const WGCONF_DIR_FRAMEWORK_VIEW8      = WGCONF_DIR_FRAMEWORK . '/v8';
const WGCONF_DIR_FRAMEWORK_CONTROLLER = WGCONF_DIR_FRAMEWORK . '/c';
const WGCONF_DIR_FRAMEWORK_EXT        = WGCONF_DIR_FRAMEWORK . '/exts';
const WGCONF_DIR_FRAMEWORK_GAUNTLET   = WGCONF_DIR_FRAMEWORK . '/gauntlet';

const WGCONF_LOGNAME = '';
const WGCONF_LOGFILE = WGCONF_DIR_LOG . '/' . WGCONF_LOGNAME;
const WGCONF_LOGTYPE = 0;

const WGCONF_PEAR  = '/usr/local/lib/php';
const WGCONF_UP_PX = 640;

const WGCONF_SMTP_HOST          = 'localhost';
const WGCONF_SMTP_PORT          = 25;
const WGCONF_SMTP_AUTH          = false;
const WGCONF_SMTP_AUTH_USERNAME = '';
const WGCONF_SMTP_AUTH_PASSWORD = '';
const WGCONF_SMTP_LOCALHOST     = 'localhost';

const WGCONF_SMTP_TEST        = false;
const WGCONF_SMTP_TEST_RCPTTO = 'root@localhost';

const WGCONF_EMAIL      = 'root@localhost';
const WGCONF_ERRMAIL    = WGCONF_EMAIL;
const WGCONF_REPORTMAIL = WGCONF_EMAIL;

const WGCONF_SESSION_GCTIME = 60 * 30;

const WGCONF_DBMS_TYPE   = 'pgsql';
const WGCONF_DBMS_HOST   = 'localhost';
const WGCONF_DBMS_PORT   = 5432;
const WGCONF_DBMS_DB     = 'waggo8test';
const WGCONF_DBMS_USER   = 'waggo';
const WGCONF_DBMS_PASSWD = 'waggo';
const WGCONF_DBMS_CA     = '';

define( 'WGCONF_URLBASE', "http://{$_SERVER['SERVER_NAME']}" );

const WGCONF_GOOGLEMAPS_X = 139.767073;
const WGCONF_GOOGLEMAPS_Y = 35.681304;
const WGCONF_PHPCLI       = '/usr/local/bin/php';
const WGCONF_CONVERT      = '/usr/local/bin/convert';
const WGCONF_FFMPEG       = '/usr/local/bin/ffmpeg';

const WGCONF_HASHKEY          = '';
const WGCONF_PASSWORD_HASHKEY = '';

global $WGCONF_AUTOLOAD;
$WGCONF_AUTOLOAD = [
	WGCONF_DIR_FRAMEWORK_VIEW8,
	WGCONF_DIR_FRAMEWORK_GAUNTLET,
	WGCONF_DIR_FRAMEWORK_MODEL,
	WGCONF_DIR_FRAMEWORK_EXT,
	WGCONF_DIR_SYS . '/include'
];
