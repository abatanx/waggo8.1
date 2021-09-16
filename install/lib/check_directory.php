<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function wi_install_dir_info(): array
{
	$dirInfo =
		[
			'installer'   => realpath( __DIR__ . '/../install.php' ),
			'install'     => realpath( __DIR__ . '/..' ),
			'waggo'       => realpath( __DIR__ . '/../..' ),
			'sys'         => realpath( __DIR__ . '/../../..' ),
			'inc'         => realpath( __DIR__ . '/../../..' ) . '/include',
			'plugins'     => realpath( __DIR__ . '/../../..' ) . '/plugins',
			'config'      => realpath( __DIR__ . '/../../..' ) . '/config',
			'application' => realpath( __DIR__ . '/../../../..' ),
			'pub'         => realpath( __DIR__ . '/../../../..' ) . '/pub',
			'tpl'         => realpath( __DIR__ . '/../../../..' ) . '/tpl',
			'upload'      => realpath( __DIR__ . '/../../../..' ) . '/upload',
			'resources'   => realpath( __DIR__ . '/../../../..' ) . '/resources',
			'temporary'   => realpath( __DIR__ . '/../../../..' ) . '/temporary',
			'extensions'  => realpath( __DIR__ . '/../../../..' ) . '/extensions',
			'logs'        => realpath( __DIR__ . '/../../../..' ) . '/logs',
			'inittpl'     => realpath( __DIR__ . '/../..' ) . '/initdata/tpl'
		];

	$dirInfo['appname'] = basename( $dirInfo['application'] );

	return $dirInfo;
}

function wi_setup_dir(): bool
{
	$dirInfo = wi_install_dir_info();

	wi_echo( ECHO_NORMAL, <<<___END___

+--<application>                                -> {$dirInfo['application']}
     | ({$dirInfo['appname']})
     |
     +--pub              (@ public-directory)   -> {$dirInfo['pub']}
     |   |
     |   +--resources    (>)
     |   +--wgcss        (>)
     |   +--wgjs         (>)
     |   +--examples     (>)
     |   +--tests        (>)
     |
     +--sys                                     -> {$dirInfo['sys']}
     |   |
     |   +--include      (@)                    -> {$dirInfo['inc']}
     |   |
     |   +--plugins      (@)                    -> {$dirInfo['plugins']}
     |   |
     |   +--waggo8                              -> {$dirInfo['waggo']}
     |   |    |
     |   |    +--install                        -> {$dirInfo['install']}
     |   |        |
     |   |        +install.php                  -> {$dirInfo['installer']}
     |   |
     |   +--config       (@)                    -> {$dirInfo['config']}
     |
     +---tpl             (@)                    -> {$dirInfo['tpl']}
     |
     +---upload          (@)                    -> {$dirInfo['upload']}
     |
     +---extensions      (@)                    -> {$dirInfo['extensions']}
     |
     +---resources       (@)                    -> {$dirInfo['resources']}
     |
     +---temporary       (@)                    -> {$dirInfo['temporary']}
     |
     +---logs            (@)                    -> {$dirInfo['logs']}

  <application>: 
              @: Create
              >: Create Symbolic Link

___END___
	);

	$hasError = false;

	if ( ! preg_match( '/\/sys$/', $dirInfo['sys'] ) )
	{
		wi_echo( ECHO_NORMAL, "Error: Place <application>/sys/waggo8 ." );
		wi_echo( ECHO_NORMAL, " % mkdir hogehoge" );
		wi_echo( ECHO_NORMAL, " % cd hogehoge" );
		wi_echo( ECHO_NORMAL, " % mkdir sys" );
		wi_echo( ECHO_NORMAL, " % cd sys" );
		wi_echo( ECHO_NORMAL, " % tar xvfz ~/Downloads/waggo8.00.tar.gz" );
		$hasError = true;
	}

	if ( ! preg_match( '/\/sys\/waggo8$/', $dirInfo['waggo'] ) )
	{
		wi_echo( ECHO_NORMAL, "Error: Place <application>/sys/waggo8 ." );
		wi_echo( ECHO_NORMAL, " % tar xvfz ~/Downloads/waggo8.00.tar.gz" );
		wi_echo( ECHO_NORMAL, " % mv waggo8.00 waggo8" );
		$hasError = true;
	}

	if ( $hasError )
	{
		return false;
	}

	wi_echo( ECHO_NORMAL, "\n\n" );

	return wi_read( 'Continue ? (Yes/No) -> ', [ 'Yes', 'No' ] ) === 'Yes';
}

function wi_setup_dir_and_permissions(): bool
{
	$dirInfo = wi_install_dir_info();

	$permissions = [
		'config'     => 0777,
		'plugins'    => 0755,
		'pub'        => 0755,
		'inc'        => 0755,
		'tpl'        => 0755,
		'upload'     => 0777,
		'resources'  => 0777,
		'temporary'  => 0777,
		'extensions' => 0755,
		'logs'       => 0777
	];

	$symlinks = array(
		[ $dirInfo['pub'] . '/examples', '../sys/waggo8/www/examples' ],
		[ $dirInfo['pub'] . '/tests', '../sys/waggo8/ut/www' ],
		[ $dirInfo['pub'] . '/wg', '../sys/waggo8/www/wg' ],
		[ $dirInfo['pub'] . '/wgjs', '../sys/waggo8/www/wgjs' ],
		[ $dirInfo['pub'] . '/wgcss', '../sys/waggo8/www/wgcss' ],
		[ $dirInfo['pub'] . '/resources', '../resources' ],
	);

	foreach ( $permissions as $key => $permission )
	{
		$dir = $dirInfo[ $key ];
		echo sprintf( "Checking directory: %s\n", $dir );

		clearstatcache();
		if ( ! is_dir( $dirInfo[ $key ] ) )
		{
			@mkdir( $dirInfo[ $key ] );
			if ( ! is_dir( $dirInfo[ $key ] ) )
			{
				echo "Error: Can't make directory.\n";

				return false;
			}
		}

		if ( @chmod( $dirInfo[ $key ], $permission ) === false )
		{
			echo "Error: Can't change permission.\n";

			return false;
		}
	}

	foreach ( $symlinks as $symlink )
	{
		$dst = $symlink[0];
		$src = $symlink[1];

		wi_echo( ECHO_NORMAL, "Checking Symbolic-link: %s -> %s", $src, $dst );

		@symlink( $src, $dst );
	}

	wi_pause( "Finished to check the directory." );

	return true;
}
