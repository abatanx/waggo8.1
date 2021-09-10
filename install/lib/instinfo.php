<?php
/**
 * waggo8
 * @copyright 2013-2020 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/dircheck.php';
require_once __DIR__ . '/gencontroller.php';
require_once __DIR__ . '/detect.php';

function file_waggoconf()
{
	$tf = <<<___END___
<?php
/**
 * waggo8 configuration
 */
const WG_DEBUG							=	false ; // Log for debug
const WG_SQLDEBUG						=	false ; // Log for SQL debug
const WG_SESSIONDEBUG					=	false ; // Log for Session debug
const WG_CONTROLLERDEBUG				=	false ; // Log for Controller debug
const WG_MODELDEBUG						=	false ; // Log for Model debug
const WG_JSNOCACHE						=	false ; // Ignore cache for JS script
const WG_INSTALLDIR						=	'' ;
const WG_LOGDIR							=	WG_INSTALLDIR . "/logs" ;
const WG_LOGNAME						=	'' ;
const WG_LOGFILE						=	WG_LOGDIR . '/' . WG_LOGNAME ;
const WG_LOGTYPE						=	0 ;
define( 'WG_ENCODING'					,	mb_internal_encoding() );

const WGCONF_DIR_ROOT					=	WG_INSTALLDIR ;
define( 'WGCONF_DIR_WAGGO'				,	realpath( __DIR__ . '/../waggo8') );
define( 'WGCONF_DIR_PUB'				,	realpath( __DIR__ . '/../../pub') );
define( 'WGCONF_DIR_SYS'				,	realpath( __DIR__ . '/../../sys') );
define( 'WGCONF_DIR_TPL'				,	realpath( __DIR__ . '/../../tpl') );
const WGCONF_CANVASCACHE				=	WG_INSTALLDIR.'/temporary';
const WGCONF_DIR_UP						=	WG_INSTALLDIR.'/upload';
const WGCONF_DIR_RES					=	WG_INSTALLDIR.'/resources';

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
define( 'WGCONF_URLBASE'				,	"http://{\$_SERVER['SERVER_NAME']}" );

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

	return $tf;
}

function file_conf()
{
	$tf = <<<___END___
<?php
//
// Common include script
//


___END___;

	return $tf;
}


function file_apache( $domain, $dir, $email )
{
	$tf = <<<___END___
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

	return $tf;
}

function addsingleslashes( $str )
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

function replace_waggoconf( $filename, $dat )
{
	$dirInfo = install_dirinfo();
	$newConfig = "";

	$editMessage = "// Edited by install.php at " . date( "Y/m/d H:i:s" );
	$lines = file( $filename, FILE_IGNORE_NEW_LINES );
	foreach ( $lines as $line )
	{
		$checker = trim( $line );
		if ( preg_match( '/^const(\s+)([0-9A-Za-z_]+)(\s*=\s*)(.+)$/', $checker, $m ) )
		{
			list( , $prefix, $key, $equal, $val ) = $m;

			switch ( $key )
			{
				case 'WG_INSTALLDIR':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dirInfo["application"] ) );
					break;
				case 'WG_LOGNAME':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( 'waggo.' . $dat["domain"]["domain"] . '.log' ) );
					break;
				case 'WGCONF_DBMS_DB':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["postgresql"]["dbname"] ) );
					break;
				case 'WGCONF_DBMS_USER':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["postgresql"]["username"] ) );
					break;
				case 'WGCONF_DBMS_PASSWD':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["postgresql"]["password"] ) );
					break;
				case 'WGCONF_DBMS_HOST':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["postgresql"]["host"] ) );
					break;
				case 'WGCONF_HASHKEY':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["hash"]["general_hashkey"] ) );
					break;
				case 'WGCONF_PASSWORD_HASHKEY':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["hash"]["password_hashkey"] ) );
					break;
				case 'WGCONF_PHPCLI':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["executable"]["phpcli"] ) );
					break;
				case 'WGCONF_CONVERT':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["executable"]["convert"] ) );
					break;
				case 'WGCONF_FFMPEG':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["executable"]["ffmpeg"] ) );
					break;
				case 'WGCONF_PEAR':
					$line = sprintf( "const%s%s%s'%s'; {$editMessage}", $prefix, $key, $equal, addsingleslashes( $dat["pear"]["dir"] ) );
					break;
			}
		}
		$newConfig .= rtrim( $line ) . "\n";
	}
	file_put_contents( $filename, $newConfig );
}

function install_gen_hash()
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

function install_instinfo()
{
	// データファイル検索
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

	printf( "================== 登録済みインストール情報 ==================\n" );
	foreach ( $infs as $id => $inf )
	{
		printf( "  [%d] ... %s\n", $id, $inf[2]["domain"]["domain"] );
	}
	printf( "  [0] ... 新規ドメイン\n" );
	printf( "--------------------------------------------------------------\n" );
	printf( "  [q] ... 終了\n" );
	printf( "==============================================================\n" );

	// どのドメインを利用するか。
	do
	{
		$id = in( "どのドメインを利用してインストールを行いますか？ -> " );
		if ( $id == 'q' )
		{
			exit;
		}

		if ( is_numeric( $id ) )
		{
			$id = (int) $id;
		}
	}
	while ( ! is_int( $id ) || ( $id != 0 && ! isset( $infs[ $id ] ) ) );

	// デフォルト値生成
	$inf = ( $id == 0 ) ?
		array(
			'domain'     =>
				array(
					'domain' => '127.0.0.1'
				),
			'app'        =>
				array(
					'prefix' => 'App',
					'email'  => 'root@localhost'
				),
			'executable' =>
				array(
					'phpcli'  => detect_phpcli(),
					'convert' => detect_convert(),
					'ffmpeg'  => detect_ffmpeg()
				),
			'pear'       =>
				array(
					'dir' => detect_pear()
				),
			'postgresql' =>
				array(
					'host'     => 'localhost',
					'dbname'   => 'waggo',
					'username' => 'waggo',
					'password' => 'password'
				),
			'app'        =>
				array(
					'email' => 'root@localhost'
				),
			'hash'       =>
				array(
					'general_hashkey'  => install_gen_hash(),
					'password_hashkey' => install_gen_hash()
				)
		) : $infs[ $id ][2];

	// データ入力
	$settings = array(
		array(
			'domain',
			'domain',
			'このフレームワークで構築するサイトのドメイン名',
			'127.0.0.1'
		),
		array(
			'app',
			'prefix',
			'このフレームワークで構築するコントローラ等に付与する接頭句',
			'App'
		),
		array( 'app', 'email', '連絡先メールアドレス', 'root@localhost' ),
		array( 'executable', 'phpcli', 'PHP(CLI)', detect_phpcli() ),
		array( 'pear', 'dir', 'PEAR', detect_pear() ),
		array( 'executable', 'convert', 'convert(ImageMagick)', detect_convert() ),
		array( 'executable', 'ffmpeg', 'ffmpeg', detect_ffmpeg() ),
		array( 'postgresql', 'host', 'DB サーバアドレス', 'localhost' ),
		array( 'postgresql', 'dbname', 'DB データベース名', 'waggo' ),
		array( 'postgresql', 'username', 'DB 接続ユーザ名', 'waggo' ),
		array( 'postgresql', 'password', 'DB 接続パスワード', 'password' ),
		array( 'hash', 'general_hashkey', '汎用ハッシュキー', '通常は自動生成されています' ),
		array( 'hash', 'password_hashkey', 'パスワード用ハッシュキー', '通常は自動生成されています' )
	);
	foreach ( $settings as $setting )
	{
		$def                               = @$inf[ $setting[0] ][ $setting[1] ];
		$inf[ $setting[0] ][ $setting[1] ] = indef(
			"● {$setting[2]}\n" .
			"              例:({$setting[3]})\n" .
			"   Enter規定値 -> {$def}\n" .
			"          入力 -> ", $def, true );
		echo "\n";
	}

	// どのドメインを利用するか。
	if ( q( "設定ファイルを更新してもよいですか？ (Yes/No) -> ", array( "Yes", "No" ) ) !== "Yes" )
	{
		return;
	}

	// 設定値保存
	$filename = $dir . "/install." . $inf["domain"]["domain"] . ".dat";
	$fp = fopen( $filename, "w" ) or die( "設定ファイルの保存に失敗しました。" );
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

	// 設定ファイルの作成
	$dirinfo    = install_dirinfo();
	$domain     = $inf['domain']['domain'];
	$waggoconf  = $dirinfo['config'] . "/waggo.{$domain}.php";
	$apacheconf = $dirinfo['config'] . "/apache-vhosts.{$domain}.conf";
	$appconf    = $dirinfo['sys'] . '/config.php';

	if ( ! file_exists( $waggoconf ) )
	{
		file_put_contents( $waggoconf, file_waggoconf() );
	}
	file_put_contents( $apacheconf, file_apache( $domain, $dirinfo["application"], $inf["app"]["email"] ) );
	if ( ! file_exists( $appconf ) )
	{
		file_put_contents( $appconf, file_conf() );
	}

	// 初期テンプレートの複写
	$tpls = array( 'abort.html', 'iroot.html', 'mail.txt', 'null.html', 'pcroot.html', 'pcroot.xml' );
	foreach ( $tpls as $name )
	{
		$src = "{$dirinfo['inittpl']}/{$name}";
		$dst = "{$dirinfo['tpl']}/{$name}";

		if ( ! file_exists( $dirinfo["tpl"] . "/" . $name ) )
		{
			copy( $dirinfo["inittpl"] . "/" . $name, $dirinfo["tpl"] . "/" . $name );
		}
	}

	// 設定ファイルの更新
	replace_waggoconf( $waggoconf, $inf );

	// コントローラー作成
	install_gencontroller( $inf["app"]["prefix"] );

	//
	a( "設定ファイルを更新しました。" );
}
