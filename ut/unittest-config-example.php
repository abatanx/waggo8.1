<?php
/**
 * waggo8.1 configuration
 */
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '80';

/**
 * waggo8.1 configuration
 */
const WG_DEBUG           = false;
const WG_SQLDEBUG        = false;
const WG_SESSIONDEBUG    = false;
const WG_CONTROLLERDEBUG = false;
const WG_MODELDEBUG      = false;
const WG_JSNOCACHE       = false;
define( 'WG_INSTALLDIR', realpath( __DIR__ ) );

const WG_LOGDIR  = WG_INSTALLDIR . "/logs";
const WG_LOGNAME = 'waggo.unit-test.log'; // Edited by install.php at 2021/09/14 12:53:51
const WG_LOGFILE = WG_LOGDIR . '/' . WG_LOGNAME;
const WG_LOGTYPE = 0;
define( 'WG_ENCODING', mb_internal_encoding() );

define( 'WGCONF_DIR_ROOT', WG_INSTALLDIR );
define( 'WGCONF_DIR_WAGGO', realpath( WG_INSTALLDIR . '/../../sys/waggo8.1' ) );
define( 'WGCONF_DIR_PUB', WG_INSTALLDIR . '/pub' );
define( 'WGCONF_DIR_SYS', WG_INSTALLDIR . '/sys' );
define( 'WGCONF_DIR_TPL', WG_INSTALLDIR . '/tpl' );
define( 'WGCONF_CANVASCACHE', WG_INSTALLDIR . '/temporary' );
define( 'WGCONF_DIR_UP', WG_INSTALLDIR . '/upload' );
define( 'WGCONF_DIR_RES', WG_INSTALLDIR . '/resources' );
define( 'WGCONF_DIR_EXTENSIONS', WG_INSTALLDIR . '/extensions' );

const WGCONF_DIR_FRAMEWORK            = WGCONF_DIR_WAGGO . '/framework';
const WGCONF_DIR_FRAMEWORK_MODEL      = WGCONF_DIR_FRAMEWORK . '/m';
const WGCONF_DIR_FRAMEWORK_VIEW8      = WGCONF_DIR_FRAMEWORK . '/v8';
const WGCONF_DIR_FRAMEWORK_CONTROLLER = WGCONF_DIR_FRAMEWORK . '/c';
const WGCONF_DIR_FRAMEWORK_EXT        = WGCONF_DIR_FRAMEWORK . '/exts';
const WGCONF_DIR_FRAMEWORK_GAUNTLET   = WGCONF_DIR_FRAMEWORK . '/gauntlet';

const WGCONF_PEAR  = '/usr/local/share/pear';
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
const WGCONF_DBMS_PASSWD = 'password';
const WGCONF_DBMS_CA     = '';

define( 'WGCONF_URLBASE', 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] );

const WGCONF_GOOGLEMAPS_X = 139.767073;
const WGCONF_GOOGLEMAPS_Y = 35.681304;
const WGCONF_PHPCLI       = '/usr/local/bin/php';
const WGCONF_CONVERT      = '/usr/local/bin/convert';
const WGCONF_FFMPEG       = '/usr/local/bin/ffmpeg';

const WGCONF_HASHKEY          = 'HASHHASHHASHHASHHASHHASHHASHHASH';
const WGCONF_PASSWORD_HASHKEY = 'PWHASHPWHASHPWHASHPWHASHPWHASHPW';

global $WGCONF_AUTOLOAD;
$WGCONF_AUTOLOAD = [
	WGCONF_DIR_FRAMEWORK_VIEW8,
	WGCONF_DIR_FRAMEWORK_GAUNTLET,
	WGCONF_DIR_FRAMEWORK_MODEL,
	WGCONF_DIR_FRAMEWORK_EXT,
	WGCONF_DIR_SYS . '/include'
];
