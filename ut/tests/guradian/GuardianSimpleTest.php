<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/local-common.php';

require_once __DIR__ . '/../../../framework/guardian/WGGuardian.php';

class GuardianSimpleParam extends WGGuardian
{
	#[WGGuard(post:"a")]
	public int $id;
}


class GuardianSimpleTest extends TestCase
{
	public function test_guardian_simple()
	{
		$g = new GuardianSimpleParam( null );
		$g->fromGET();
	}
}
