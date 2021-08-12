<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

function install_dirinfo()
{
	$dirinfo =
		[	'installer'		=>	realpath( __DIR__ .'/../install.php'),
				'install'		=>	realpath( __DIR__ .'/..'),
				'waggo'			=>	realpath( __DIR__ .'/../..'),
				'sys'			=>	realpath( __DIR__ .'/../../..'),
				'inc'			=>	realpath( __DIR__ .'/../../..').'/include',
				'application'	=>	realpath( __DIR__ .'/../../../..'),
				'pub'			=>	realpath( __DIR__ .'/../../../..').'/pub',
				'tpl'			=>	realpath( __DIR__ .'/../../../..').'/tpl',
				'upload'		=>	realpath( __DIR__ .'/../../../..').'/upload',
				'config'		=>	realpath( __DIR__ .'/../../..').'/config',
				'resources'		=>	realpath( __DIR__ .'/../../../..').'/resources',
				'temporary'		=>	realpath( __DIR__ .'/../../../..').'/temporary',
				'logs'			=>	realpath( __DIR__ .'/../../../..').'/logs',
				'inittpl'		=>	realpath( __DIR__ .'/../..').'/initdata/tpl'
		]
	;

	$dirinfo["appname"] = basename($dirinfo["application"]);
	return $dirinfo;
}

function install_dircheck()
{
	$dirinfo = install_dirinfo();

	echo <<<___END___

+--<application>                                -> {$dirinfo['application']}
     | ({$dirinfo['appname']})
     |
     +--pub              (@ 公開ディレクトリ)   -> {$dirinfo['pub']}
     |   |
     |   +--resources    (>)
     |   +--wgcss        (>)
     |   +--wgjs         (>)
     |   +--examples     (>)
     |   +--tests        (>)
     |
     +--sys                                     -> {$dirinfo['sys']}
     |   |
     |   +--include      (@)                    -> {$dirinfo['inc']}
     |   |
     |   +--waggo8                              -> {$dirinfo['waggo']}
     |   |    |
     |   |    +--install (このディレクトリ)     -> {$dirinfo['install']}
     |   |        |
     |   |        +install.php                  -> {$dirinfo['installer']}
     |   |
     |   +--config       (@)                    -> {$dirinfo['config']}
     |
     +---tpl             (@)                    -> {$dirinfo['tpl']}
     |
     +---upload          (@)                    -> {$dirinfo['upload']}
     |
     +---resources       (@)                    -> {$dirinfo['resources']}
     |
     +---temporary       (@)                    -> {$dirinfo['temporary']}
     |
     +---logs            (@)                    -> {$dirinfo['logs']}

  <application>: ディレクトリ名については、任意の名前で構いません。
              @: 新規作成(更新)します。
              >: シンボリックリンクを新規作成(更新)します。

___END___;

	$has_error = false;

	if(!preg_match('/\/sys$/',$dirinfo["sys"]))
	{
		echo "\n\n";
		echo "[ERROR] waggo8.00.tar.gz は、<application>/sys を作成し、その中で展開してください。\n";
		echo "        % mkdir hogehoge\n";
		echo "        % cd hogehoge\n";
		echo "        % mkdir sys\n";
		echo "        % cd sys\n";
		echo "        % tar xvfz ~/Downloads/waggo8.00.tar.gz\n";
		$has_error = true;
	}

	if(!preg_match('/\/sys\/waggo8$/',$dirinfo["waggo"]))
	{
		echo "\n\n";
		echo "[ERROR] waggo8.00.tar.gz は、<application>/sys/waggo8 配下に install が作成されるよう展開してください。\n";
		echo "        % tar xvfz ~/Downloads/waggo8.00.tar.gz\n";
		echo "        % mv waggo8.00 waggo8\n";
		$has_error = true;
	}

	if( $has_error ) return false;

	echo "\n\n";
	return q("以上のディレクトリ構成で、セットアップを続行してもよろしいですか? (Yes/No) -> ",["Yes","No"]) === "Yes";
}

function install_mkdir()
{
	$dirinfo = install_dirinfo();
	$keys = [
		"config"		=>	0777,
		"pub"			=>	0755,
		"inc"			=>	0755,
		"tpl"			=>	0755,
		"upload"		=>	0777,
		"resources"		=>	0777,
		"temporary"		=>	0777,
		"logs"			=>	0777
	];

	$symlinks = array(
		array(	$dirinfo["pub"]."/examples"		,	"../sys/waggo8/www/examples"	),
		array(	$dirinfo["pub"]."/tests"		,	"../sys/waggo8/www/tests"		),
		array(	$dirinfo["pub"]."/wg"			,	"../sys/waggo8/www/wg"			),
		array(	$dirinfo["pub"]."/wgjs"			,	"../sys/waggo8/www/wgjs"		),
		array(	$dirinfo["pub"]."/wgcss"		,	"../sys/waggo8/www/wgcss"		),
		array(	$dirinfo["pub"]."/resources"	,	"../resources"					)
	);

	foreach($keys as $key=>$permission)
	{
		$dir = $dirinfo[$key];
		echo sprintf("-> ディレクトリ %-50s の状態を確認しています。\n",$dir);

		clearstatcache();
		if(!is_dir($dirinfo[$key]))
		{
			@mkdir($dirinfo[$key]);
			if(!is_dir($dirinfo[$key]))
			{
				echo "【エラー】ディレクトリの作成に失敗しました。\n";
				return false;
			}
		}

		if(@chmod($dirinfo[$key], $permission)===false)
		{
			echo "【エラー】パーミッションの変更に失敗しました。\n";
			return false;
		}
	}

	foreach($symlinks as $symlink)
	{
		echo sprintf("-> シンボリックリンク %-50s を確認しています。\n",$symlink[0]);

		$dst = $symlink[0];
		$src = $symlink[1];
		@symlink($src,$dst);
	}

	return true;

}
