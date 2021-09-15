<?php

require_once __DIR__ . '/../waggo_test.php';

class TestInchkInt extends TestPCController
{
	public ?int $a1, $a2;
	public int $b1 = 0, $b2 = 0;

	public function create()
	{
		$results   = [];
		$results[] = [ wg_inchk_int( $this->a1, @$_GET['a1'] ), $this->a1 ];
		$results[] = [ wg_inchk_int( $this->a2, @$_GET['a2'] ), $this->a2 ];
		$results[] = [ wg_inchk_int( $this->b1, @$_GET['b1'] ), $this->b1 ];
		$results[] = [ wg_inchk_int( $this->b2, @$_GET['b2'] ), $this->b2 ];
		$results[] = [ wg_inchk_int( $c1, @$_GET['c1'] ), $c1 ];
		$results[] = [ wg_inchk_int( $c2, @$_GET['c2'] ), $c2 ];

		foreach( $results as $result )
		{
			printf('[%s][%s]', $result[0] ? 't' : 'f', $result[1]);
		}
	}
}

TestInchkInt::START();
