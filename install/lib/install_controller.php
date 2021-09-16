<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

require_once __DIR__ . '/check_directory.php';

function wi_create_htaccess():string
{
	return <<<___END___
AllowOverride None
Require all granted
<FilesMatch "^[_.]|~$|#$">
	Require all denied
</FilesMatch>

___END___;
}


function wi_create_pccontroller( string $prefix ): string
{
	return <<<___END___
<?php
/**
 * PC Controller for application.
 */

require_once WGCONF_DIR_FRAMEWORK_CONTROLLER . '/WGFPCController.php';

class {$prefix}PCController extends WGFPCController
{
}

___END___;
}

function wi_create_xmlcontroller( string $prefix ): string
{
	return <<<___END___
<?php
/**
 * XML Controller for application.
 */

require_once WGCONF_DIR_FRAMEWORK_CONTROLLER . '/WGFXMLController.php';

class {$prefix}XMLController extends WGFXMLController
{
}

___END___;
}

function wi_create_jsoncontroller( string $prefix ): string
{
	return <<<___END___
<?php
/**
 * JSON Controller for application.
 */

require_once WGCONF_DIR_FRAMEWORK_CONTROLLER . '/WGFJSONController.php';

class {$prefix}JSONController extends WGFJSONController
{
}

___END___;
}

function wi_create_HTE(): string
{
	return <<<___END___
<?php
/**
 * HTE htmltemplate encoder
 */
class HTE extends HtmlTemplateEncoder
{
}

___END___;
}

function wi_install_create_controller( $prefix )
{
	$dirInfo = wi_install_dir_info();

	$files = [
		[ $dirInfo['pub'] . ".htaccess", wi_create_htaccess() ],
		[ $dirInfo['inc'] . "/{$prefix}PCController.php", wi_create_pccontroller( $prefix ) ],
		[ $dirInfo['inc'] . "/{$prefix}XMLController.php", wi_create_xmlcontroller( $prefix ) ],
		[ $dirInfo['inc'] . "/{$prefix}JSONController.php", wi_create_jsoncontroller( $prefix ) ],
		[ $dirInfo['inc'] . '/HTE.php', wi_create_HTE() ]
	];

	foreach ( $files as $file )
	{
		clearstatcache();
		if ( ! file_exists( $file[0] ) )
		{
			wi_echo( ECHO_NORMAL, "Creating: %s", $file[0]);
			file_put_contents( $file[0], $file[1] );
		}
		else
		{
			wi_echo( ECHO_NORMAL, "Skipped to create: %s (already exists)", $file[0]);
		}
		clearstatcache();
	}
}
