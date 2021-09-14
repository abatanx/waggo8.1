<?php
/**
 * waggo8
 * @copyright 2013-2020 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/check_directory.php';
require_once __DIR__ . '/install_controller.php';
require_once __DIR__ . '/detect.php';

function wi_waggo_conf( string $scheme, string $port ): string
{
	if ( $port === '80' || $port === '443' )
	{
		$urlBase = <<<PHP
define( 'WGCONF_URLBASE', '$scheme://' . \$_SERVER['SERVER_NAME'] );
PHP;
	}
	else
	{
		$urlBase = <<<PHP
define( 'WGCONF_URLBASE', '$scheme://' . \$_SERVER['SERVER_NAME'] . ':' . \$_SERVER['SERVER_PORT'] );
PHP;
	}

	return <<<___END___
<?php
/**
 * waggo8 configuration
 */
const WG_DEBUG							=	false ;
const WG_SQLDEBUG						=	false ;
const WG_SESSIONDEBUG					=	false ;
const WG_CONTROLLERDEBUG				=	false ;
const WG_MODELDEBUG						=	false ;
const WG_JSNOCACHE						=	false ;
const WG_INSTALLDIR						=	'' ;
const WG_LOGDIR							=	WG_INSTALLDIR . "/logs" ;
const WG_LOGNAME						=	'' ;
const WG_LOGFILE						=	WG_LOGDIR . '/' . WG_LOGNAME ;
const WG_LOGTYPE						=	0 ;
define( 'WG_ENCODING'					,	mb_internal_encoding() );

const WGCONF_DIR_ROOT					=	WG_INSTALLDIR ;
const WGCONF_DIR_WAGGO					=	WG_INSTALLDIR . '/sys/waggo8' ;
const WGCONF_DIR_PUB					=	WG_INSTALLDIR . '/pub';
const WGCONF_DIR_SYS					=	WG_INSTALLDIR . '/sys';
const WGCONF_DIR_TPL					=	WG_INSTALLDIR . '/tpl';
const WGCONF_CANVASCACHE				=	WG_INSTALLDIR . '/temporary';
const WGCONF_DIR_UP						=	WG_INSTALLDIR . '/upload';
const WGCONF_DIR_RES					=	WG_INSTALLDIR . '/resources';
const WGCONF_DIR_EXTENSIONS				=	WG_INSTALLDIR . '/extensions';

const WGCONF_DIR_FRAMEWORK				=	WGCONF_DIR_WAGGO . '/framework';
const WGCONF_DIR_FRAMEWORK_MODEL		=	WGCONF_DIR_FRAMEWORK . '/m';
const WGCONF_DIR_FRAMEWORK_VIEW8		=	WGCONF_DIR_FRAMEWORK . '/v8';
const WGCONF_DIR_FRAMEWORK_CONTROLLER 	=	WGCONF_DIR_FRAMEWORK . '/c';
const WGCONF_DIR_FRAMEWORK_EXT			=	WGCONF_DIR_FRAMEWORK . '/exts';
const WGCONF_DIR_FRAMEWORK_GAUNTLET		=	WGCONF_DIR_FRAMEWORK . '/gauntlet';

const WGCONF_PEAR						=	'/usr/local/lib/php' ;
const WGCONF_UP_PX						=	640 ;

const WGCONF_SMTP_HOST					=	'localhost' ;
const WGCONF_SMTP_PORT					=	25 ;
const WGCONF_SMTP_AUTH					=	false ;
const WGCONF_SMTP_AUTH_USERNAME			=	'' ;
const WGCONF_SMTP_AUTH_PASSWORD			=	'' ;
const WGCONF_SMTP_LOCALHOST				=	'localhost' ;

const WGCONF_SMTP_TEST					=	false ;
const WGCONF_SMTP_TEST_RCPTTO			=	'root@localhost' ;

const WGCONF_EMAIL						=	'root@localhost' ;
const WGCONF_ERRMAIL					=	WGCONF_EMAIL ;
const WGCONF_REPORTMAIL					=	WGCONF_EMAIL ;

const WGCONF_SESSION_GCTIME				=	60 * 30;

const WGCONF_DBMS_TYPE					=	'pgsql' ;
const WGCONF_DBMS_HOST					=	'localhost' ;
const WGCONF_DBMS_PORT					=	5432 ;
const WGCONF_DBMS_DB					=	'' ;
const WGCONF_DBMS_USER					=	'' ;
const WGCONF_DBMS_PASSWD				=	'' ;
const WGCONF_DBMS_CA					=	'';

{$urlBase}

const WGCONF_GOOGLEMAPS_X				=	139.767073 ;
const WGCONF_GOOGLEMAPS_Y				=	35.681304 ;
const WGCONF_PHPCLI						=	'/usr/local/bin/php' ;
const WGCONF_CONVERT					=	'/usr/local/bin/convert' ;
const WGCONF_FFMPEG						=	'/usr/local/bin/ffmpeg' ;

const WGCONF_HASHKEY					=	'' ;
const WGCONF_PASSWORD_HASHKEY			=	'' ;

global \$WGCONF_AUTOLOAD;
\$WGCONF_AUTOLOAD = [
	WGCONF_DIR_FRAMEWORK_VIEW8,
	WGCONF_DIR_FRAMEWORK_GAUNTLET,
	WGCONF_DIR_FRAMEWORK_MODEL,
	WGCONF_DIR_FRAMEWORK_EXT,
	WGCONF_DIR_SYS.'/include'
];
___END___;
}

function wi_app_config_conf(): string
{
	return <<<___END___
<?php
//
// Common include script
//


___END___;
}


function wi_apache_conf( string $domain, string $dir, string $email ): string
{
	return <<<___END___
#
#
#
<VirtualHost *:80>
    ServerAdmin {$email}
    DocumentRoot "{$dir}/pub"
    ServerName {$domain}
    ErrorLog "{$domain}.error_log"
    CustomLog "{$domain}.access_log" common
</VirtualHost>

<Directory "{$dir}/pub">
    AllowOverride None
    Require all granted
    <FilesMatch "^_|~$|#$">
        Require all denied
    </FilesMatch>
    php_value include_path ".:{$dir}/sys/waggo"
</Directory>

___END___;
}

function wi_add_single_slashes( string $str ): string
{
	$r = "";
	for ( $i = 0; $i < strlen( $str ); $i ++ )
	{
		switch ( $str[ $i ] )
		{
			case "'":
				$r .= "\\'";
				break;
			case "\\":
				$r .= "\\\\";
				break;
			default:
				$r .= $str[ $i ];
				break;
		}
	}

	return $r;
}

function wi_replace_waggo_conf( $filename, $dat )
{
	$dirInfo   = wi_install_dir_info();
	$newConfig = "";

	$editMessage = "// Edited by install.php at " . date( "Y/m/d H:i:s" );
	$lines       = file( $filename, FILE_IGNORE_NEW_LINES );
	foreach ( $lines as $line )
	{
		$checker = trim( $line );
		if ( preg_match( '/^const(\s+)([0-9A-Za-z_]+)(\s*=\s*)(.+)$/', $checker, $m ) )
		{
			list( , $prefix, $key, $equal, $val ) = $m;

			switch ( $key )
			{
				case 'WG_INSTALLDIR':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dirInfo["application"] ) );
					break;
				case 'WG_LOGNAME':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( 'waggo.' . $dat["server"]["host"] . '.log' ) );
					break;
				case 'WGCONF_DBMS_DB':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["postgresql"]["dbname"] ) );
					break;
				case 'WGCONF_DBMS_USER':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["postgresql"]["username"] ) );
					break;
				case 'WGCONF_DBMS_PASSWD':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["postgresql"]["password"] ) );
					break;
				case 'WGCONF_DBMS_HOST':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["postgresql"]["host"] ) );
					break;
				case 'WGCONF_HASHKEY':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["hash"]["general_hashkey"] ) );
					break;
				case 'WGCONF_PASSWORD_HASHKEY':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["hash"]["password_hashkey"] ) );
					break;
				case 'WGCONF_PHPCLI':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["executable"]["phpcli"] ) );
					break;
				case 'WGCONF_CONVERT':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["executable"]["convert"] ) );
					break;
				case 'WGCONF_FFMPEG':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["executable"]["ffmpeg"] ) );
					break;
				case 'WGCONF_PEAR':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, wi_add_single_slashes( $dat["pear"]["dir"] ) );
					break;
			}
		}
		$newConfig .= rtrim( $line ) . "\n";
	}
	file_put_contents( $filename, $newConfig );
}

function wi_create_hash(): string
{
	$r = "";
	for ( $i = 0; $i < 32; $i ++ )
	{
		do
		{
			$c = chr( mt_rand( 33, 126 ) );
		}
		while ( $c == '"' || $c == "'" || $c == "\\" );
		$r .= $c;
	}

	return $r;
}

function wi_install(): bool
{
	$infs   = [];
	$dir    = realpath( __DIR__ . '/..' );
	$handle = opendir( $dir );
	$id     = 1;
	while ( ( $file = readdir( $handle ) ) !== false )
	{
		if ( $file === '.' || $file === '..' )
		{
			continue;
		}
		if ( preg_match( '/\.dat$/', $file ) )
		{
			$datfile        = "{$dir}/{$file}";
			$infs[ $id ++ ] = [ $file, $datfile, parse_ini_file( $datfile, true ) ];
		}
	}
	closedir( $handle );

	printf( "============== Registered installation domains  ==============\n" );
	foreach ( $infs as $id => $inf )
	{
		printf( "  [%d] ... %s:%s\n", $id, $inf[2]["server"]["host"], $inf[2]["server"]["port"], );
	}
	printf( "  [0] ... New domain\n" );
	printf( "--------------------------------------------------------------\n" );
	printf( "  [q] ... Quit\n" );
	printf( "==============================================================\n" );

	// どのドメインを利用するか。
	do
	{
		$id = wi_in( "Select -> " );
		if ( $id == 'q' )
		{
			return true;
		}

		if ( is_numeric( $id ) )
		{
			$id = (int) $id;
		}
	}
	while ( ! is_int( $id ) || ( $id != 0 && ! isset( $infs[ $id ] ) ) );

	$inf = ( $id == 0 ) ?
		[
			'server' =>
				[
					'scheme' => 'http',
					'host'   => '127.0.0.1',
					'port'   => '8080'
				],

			'app' =>
				[
					'prefix' => 'App',
					'email'  => 'root@localhost'
				],

			'executable' =>
				[
					'phpcli'  => wi_search_phpcli(),
					'convert' => wi_search_convert(),
					'ffmpeg'  => wi_search_ffmpeg()
				],

			'pear' =>
				[
					'dir' => wi_search_pear()
				],

			'postgresql' =>
				[
					'host'     => 'localhost',
					'dbname'   => 'waggo',
					'username' => 'waggo',
					'password' => ''
				],

			'hash' =>
				[
					'general_hashkey'  => wi_create_hash(),
					'password_hashkey' => wi_create_hash()
				]
		] : $infs[ $id ][2];

	// データ入力
	$settings = [
		[ 'server', 'host', 'App domain or IP', '127.0.0.1' ],
		[ 'server', 'scheme', 'App URL scheme', 'http' ],
		[ 'server', 'port', 'App port', '8080' ],
		[ 'app', 'prefix', 'Controller prefix', 'App' ],
		[ 'app', 'email', 'Mail address for app', 'root@localhost' ],
		[ 'executable', 'phpcli', 'PHP(CLI)', wi_search_phpcli() ],
		[ 'pear', 'dir', 'PEAR', wi_search_pear() ],
		[ 'executable', 'convert', 'convert(ImageMagick)', wi_search_convert() ],
		[ 'executable', 'ffmpeg', 'ffmpeg', wi_search_ffmpeg() ],
		[ 'postgresql', 'host', 'Database host', 'localhost' ],
		[ 'postgresql', 'dbname', 'Database name', 'waggo' ],
		[ 'postgresql', 'username', 'Database user-name', 'waggo' ],
		[ 'postgresql', 'password', 'Database password', 'password' ],
		[ 'hash', 'general_hashkey', 'Hash key for general purpose', 'Auto generated' ],
		[ 'hash', 'password_hashkey', 'Hash key for password', 'Auto generated' ]
	];
	foreach ( $settings as $setting )
	{
		$def = @$inf[ $setting[0] ][ $setting[1] ] ?? '';

		$inf[ $setting[0] ][ $setting[1] ] = wi_in_default(
			"* {$setting[2]}\n" .
			"          Ex:({$setting[3]})\n" .
			"   Default -> {$def}\n" .
			"           -> ", $def, true );
		echo "\n";
	}

	if ( wi_read( "OK ? (Yes/No) -> ", array( "Yes", "No" ) ) !== "Yes" )
	{
		return false;
	}

	$filename = $dir . "/install." . $inf["server"]["host"] . "." . $inf["server"]["port"] . ".dat";
	$fp = fopen( $filename, "w" ) or die( "Error: Failed to save." );
	foreach ( $inf as $k => $kv )
	{
		fprintf( $fp, "[%s]\n", $k );
		foreach ( $kv as $k => $v )
		{
			fprintf( $fp, "%s = \"%s\"\n", $k, $v );
		}
		fprintf( $fp, "\n" );
	}
	fclose( $fp );

	$dirInfo          = wi_install_dir_info();
	$waggoConfScheme  = $inf['server']['scheme'];
	$waggoConfHost    = $inf['server']['host'];
	$waggoConfRawPort = $inf['server']['port'];
	$waggoConfPort    = $waggoConfRawPort !== '80' ? '.' . $inf['server']['port'] : '';
	$waggoConfName    = $dirInfo['config'] . "/waggo.{$waggoConfHost}{$waggoConfPort}.php";
	$apacheConfName   = $dirInfo['config'] . "/apache-vhosts.{$waggoConfHost}.conf";
	$appConfName      = $dirInfo['sys'] . '/config.php';

	if ( ! file_exists( $waggoConfName ) )
	{
		file_put_contents( $waggoConfName,
			wi_waggo_conf(
				$waggoConfScheme, $waggoConfRawPort
			)
		);
	}

	file_put_contents( $apacheConfName, wi_apache_conf( $waggoConfHost, $dirInfo["application"], $inf["app"]["email"] ) );
	if ( ! file_exists( $appConfName ) )
	{
		file_put_contents( $appConfName,
			wi_app_config_conf()
		);
	}

	$tpls = array( 'abort.html', 'iroot.html', 'mail.txt', 'null.html', 'pcroot.html', 'pcroot.xml' );
	foreach ( $tpls as $name )
	{
		$src = $dirInfo['inittpl'] . "/" . $name;
		$dst = $dirInfo['tpl'] . "/" . $name;

		if ( ! file_exists( $src ) )
		{
			copy( $src, $dst );
		}
	}

	wi_replace_waggo_conf( $waggoConfName, $inf );

	wi_install_create_controller( $inf["app"]["prefix"] );

	wi_pause( "Saved." );

	return false;
}
