<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

const WG_NAME      = "waggo";
const WG_VERSION   = "8.00";
const WG_COPYRIGHT = "Copyright (C) 2013-2021 CIEL, K.K., project waggo.";

function wgdie( $msg )
{
	if ( ! defined( 'WG_CLI' ) )
	{
		include __DIR__ . '/api/boot/wgdie.php';
		die();
	}
	else
	{
		die( "{$msg}\n" );
	}
}

if ( defined( 'WG_UNITTEST' ) )
{
	$configFile = __DIR__ . '/ut/unittest-config.php';
}
else
{
	if ( isset( $_SERVER['WAGGO_FORCE_CONFIG'] ) && strlen( $_SERVER['WAGGO_FORCE_CONFIG'] ) > 0 )
	{
		$configFile = __DIR__ . "/../config/waggo.{$_SERVER['WAGGO_FORCE_CONFIG']}.php";
	}
	else
	{
		$configPort = $_SERVER["SERVER_PORT"] != 80 ? ".${_SERVER['SERVER_PORT']}" : "";
		$configFile = __DIR__ . "/../config/waggo.{$_SERVER['SERVER_NAME']}{$configPort}.php";
	}
}

if ( ! file_exists( $configFile ) )
{
	wgdie( "'{$configFile}' doesn't exist.\n" );
}
else
{
	require_once $configFile;
}

require_once __DIR__ . '/api/core/lib.php';

$remote_adr = wg_get_remote_adr();
wg_log_write( WGLOG_INFO, "++ " . WG_NAME . " " . WG_VERSION );
wg_log_write( WGLOG_INFO, "** PHP version     = [" . phpversion() . "]" );
wg_log_write( WGLOG_INFO, "** Server          = [" . php_uname( "a" ) . "]" );

@wg_log_write( WGLOG_INFO, "** REQUEST_URI     = [{$_SERVER['REQUEST_URI']}]" );
@wg_log_write( WGLOG_INFO, "** REQUEST_METHOD  = [{$_SERVER['REQUEST_METHOD']}] {$_SERVER['SERVER_PROTOCOL']}" );
@wg_log_write( WGLOG_INFO, "** HTTP_USER_AGENT = [{$_SERVER['HTTP_USER_AGENT']}]" );
@wg_log_write( WGLOG_INFO, "** REMOTE          = [{$remote_adr}:{$_SERVER['REMOTE_PORT']}]" );

if ( isset( $argv ) && is_array( $argv ) )
{
	@wg_log_write( WGLOG_INFO, "** ARGV            = " . implode( " ", $argv ) );
}

// Save for original request URI
if ( isset( $_SERVER['REQUEST_URI'] ) )
{
	$_SERVER['X_ORIGINAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
}

wg_log_write( WGLOG_INFO, "[[ Loaded   framework config : {$configFile}) ]]" );

require_once __DIR__ . '/api/http/http.php';

foreach ( $_GET as $k => $v )
{
	wg_log_write( WGLOG_INFO, "## GET  %-10s = %s", "[$k]", var_export( $v, true ) );
}
foreach ( $_POST as $k => $v )
{
	wg_log_write( WGLOG_INFO, "## POST %-10s = %s", "[$k]", var_export( $v, true ) );
}

require_once __DIR__ . '/api/core/exception.php';
require_once __DIR__ . '/api/core/autoload.php';
require_once __DIR__ . '/api/core/quotemeta.php';
require_once __DIR__ . '/api/core/safe.php';
require_once __DIR__ . '/api/core/check.php';
require_once __DIR__ . '/api/core/secure.php';
require_once __DIR__ . '/api/core/shell.php';
require_once __DIR__ . '/api/core/crypt.php';
require_once __DIR__ . '/api/resources/id.php';
require_once __DIR__ . '/api/user/users.php';
require_once __DIR__ . '/api/session/session.php';
require_once __DIR__ . '/api/html/wiki.php';
require_once __DIR__ . '/api/html/canvas.php';
require_once __DIR__ . '/api/html/color.php';
require_once __DIR__ . '/api/mail/mail.php';
require_once __DIR__ . '/api/dbms/interface.php';
require_once __DIR__ . '/api/datetime/datetime.php';

wg_log_write( WGLOG_INFO, "[[ Loaded   framework APIs ]]" );

if ( ! defined( 'WG_UNITTEST' ) )
{
	require_once __DIR__ . '/../config.php';
	wg_log_write( WGLOG_INFO, "[[ Loaded application APIs ]]" );
}
