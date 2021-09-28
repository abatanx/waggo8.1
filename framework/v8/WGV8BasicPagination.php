<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGV8BasicPaginationState
{
	const STATE_INVISIBLE = 0, STATE_DISABLE = 1, STATE_ENABLE = 2, STATE_ACTIVE = 3;

	private int $state;
	private string $number;
	private string $caption;
	private string $js;

	public function __construct()
	{
		$this->state   = self::STATE_INVISIBLE;
		$this->number  = '';
		$this->caption = '';
		$this->js      = '';
	}

	public function setNumber( $number ): self
	{
		$this->number = $number;

		return $this;
	}

	public function setCaption( $caption ): self
	{
		$this->caption = $caption;

		return $this;
	}

	public function isVisible(): bool
	{
		return $this->state !== self::STATE_INVISIBLE;
	}

	public function setInvisible(): self
	{
		$this->state = self::STATE_INVISIBLE;

		return $this;
	}

	public function setDisable(): self
	{
		$this->state = self::STATE_DISABLE;

		return $this;
	}

	public function setEnable(): self
	{
		$this->state = self::STATE_ENABLE;

		return $this;
	}

	public function setActive(): self
	{
		$this->state = self::STATE_ACTIVE;

		return $this;
	}

	public function setJS( $js ): self
	{
		$this->js = $js;

		return $this;
	}

	private function innerCaption(): string
	{
		$q = [];
		if ( $this->number != "" )
		{
			$q[] = $this->number;
		}
		if ( $this->caption != "" )
		{
			$q[] = $this->caption;
		}

		return implode( " ", $q );
	}

	public function makeLI(): string
	{
		switch ( $this->state )
		{
			case self::STATE_INVISIBLE:
				return '';
			case self::STATE_DISABLE:
				return sprintf( '<li class="disable"><a href="javascript:void(0)">%s</a>', $this->innerCaption() );
			case self::STATE_ENABLE:
				return sprintf( '<li><a href="javascript:void(0)" onclick="%s">%s</a></li>', $this->js, $this->innerCaption() );
			case self::STATE_ACTIVE:
				return sprintf( '<li class="active"><a href="javascript:void(0)" onclick="%s">%s</a></li>', $this->js, $this->innerCaption() );
			default:
				return "";
		}
	}

	public function makeButton(): string
	{
		switch ( $this->state )
		{
			case self::STATE_INVISIBLE:
				return '';
			case self::STATE_DISABLE:
				return sprintf( '<button type="button" class="btn btn-default disabled">%s</button>', $this->innerCaption() );
			case self::STATE_ENABLE:
				return sprintf( '<button type="button" class="btn btn-default" onclick="%s">%s</button>', $this->js, $this->innerCaption() );
			case self::STATE_ACTIVE:
				return sprintf( '<button type="button" class="btn btn-primary" onclick="%s">%s</button>', $this->js, $this->innerCaption() );
			default:
				return "";
		}
	}
}

class WGV8BasicPagination extends WGV8Object
{
	protected int
		$limit = 0,
		$count = 0,
		$total = 0,
		$page = 0,
		$length = 3;

	protected array $limitList;

	protected string $pageKey, $limitKey;

	public function __construct( $limit, $pagekey = 'wgpp', $limitkey = 'wgpl' )
	{
		parent::__construct();
		if ( ! is_numeric( $limit ) )
		{
			die( "WGV8BasicPagination, Invalid limit parameter, '$limit'.\n" );
		}

		foreach ( $this->pagingLineNums() as $n )
		{
			$this->limitList[ $n ] = sprintf( "%d件ずつ表示", $n );
		}

		$this->pageKey  = $pagekey;
		$this->limitKey = $limitkey;

		$this->page  = ( ! wg_inchk_int( $this->page, @$_GET[ $this->pageKey ], 1 ) ) ? 1 : $this->page;
		$this->limit = ( ! wg_inchk_int( $this->limit, @$_GET[ $this->limitKey ], 1 ) ) ? $limit : $this->limit;

		if ( ! in_array( $this->limit, array_keys( $this->limitList ) ) )
		{
			$this->limit = $this->pagingLineNums()[0];
		}
	}

	public function pagingLineNums(): array
	{
		return [ 10, 50, 100, 500 ];
	}

	public function js( int $page, array $options = [] ): string
	{
		$url = wg_remake_uri( [ $this->pageKey => $page, $this->limitKey => $this->limit ] );
		$opts = json_encode($options, JSON_FORCE_OBJECT);

		return "WG8.get('#'+$(this).closest('.wg-form').attr('id'),WG8.remakeURI('$url',$opts));";
	}

	public function setPagerLength( $len ): self
	{
		$this->length = $len;

		return $this;
	}

	public function offset(): int
	{
		return ( $this->page - 1 ) * $this->limit;
	}

	public function limit(): int
	{
		return $this->limit;
	}

	public function setTotal( $total ): self
	{
		$this->total = $total;

		// ページ数チェック
		$mp = (int) ( ( $this->total - 1 ) / $this->limit ) + 1;
		if ( $this->page < 1 )
		{
			$this->page = 1;
		}
		if ( $this->page > $mp )
		{
			$this->page = $mp;
		}

		return $this;
	}

	public function getPage(): int
	{
		return $this->page;
	}

	public function isFirstPage(): bool
	{
		return ( $this->page <= 1 );
	}

	public function isLastPage(): bool
	{
		$mp = (int) ( ( $this->total - 1 ) / $this->limit ) + 1;

		return ( $this->page >= $mp );
	}

	public function count(): int
	{
		$this->count ++;

		return $this->limit * ( $this->page - 1 ) + $this->count;
	}

	public function countRevert(): int
	{
		$this->count ++;

		return $this->total - ( $this->limit * ( $this->page - 1 ) + $this->count ) + 1;
	}

	protected function firstCaption(): string
	{
		return '';
	}

	protected function lastCaption(): string
	{
		return '';
	}

	protected function allCaption(): string
	{
		return '';
	}

	public function formHtml(): string
	{
		return $this->showHtml();
	}

	public function showHtml(): string
	{
		if ( $this->total == 0 )
		{
			return '';
		}

		$max_page = (int) ( ( $this->total - 1 ) / $this->limit ) + 1;
		$tags     = [];

		// 初期化
		for ( $p = 1; $p <= $max_page; $p ++ )
		{
			$tags[ $p ] = new WGV8BasicPaginationState();
			$tags[ $p ]->setNumber( number_format( $p ) );
			$tags[ $p ]->setJS( $this->js( $p ) );
		}

		// 現在のページの前後を「表示」に変更。
		for (
			$p = $this->page - $this->length;
			$p <= $this->page + $this->length;
			$p ++
		)
		{
			if ( $p >= 1 && $p <= $max_page )
			{
				$tags[ $p ]->setEnable();
			}
		}

		// 最初のページ最後のページを、「表示」に変更
		$tags[1]->setEnable();
		$tags[ $max_page ]->setEnable();

		// 現在のページ
		if ( isset( $tags[ $this->page ] ) )
		{
			$tags[ $this->page ]->setActive();
		}

		// ページ数が複数ある場合
		if ( $max_page > 1 )
		{
			// 「最初」「最後」のキャプションをセット
			$tags[1]->setCaption( $this->firstCaption() );
			$tags[ $max_page ]->setCaption( $this->lastCaption() );
		}
		// ページ数が１ページにおさまった場合
		else
		{
			// 「全部」をセット
			$tags[1]->setCaption( $this->allCaption() );
		}

		// 前後
		$prevtags = [];
		$nexttags = [];

		$lt = '<span aria-hidden="true">&laquo;</span>';
		$gt = '<span aria-hidden="true">&raquo;</span>';

		/**
		 * Prev page
		 */
		$pt = new WGV8BasicPaginationState();
		$pt->setNumber( $lt );

		if ( $this->page > 1 )
		{
			// 前に行ける
			$pt->setEnable();
			$pt->setJS( $this->js( $this->page - 1 ) );
		}
		else
		{
			$pt->setDisable();
		}
		$prevtags[] = $pt;

		/**
		 * Next page
		 */
		$pt = new WGV8BasicPaginationState();
		$pt->setNumber( $gt );

		if ( $this->page < $max_page )
		{
			// 次へ行ける
			$pt->setEnable();
			$pt->setJS( $this->js( $this->page + 1 ) );
		}
		else
		{
			$pt->setDisable();
		}
		$nexttags[] = $pt;

		/**
		 *  Skip page
		 */
		$alltags    = [];
		$is_visible = false;

		/**
		 * @var WGV8BasicPaginationState $tag
		 */
		foreach ( array_merge( $prevtags, $nexttags, $tags ) as $tag )
		{
			if ( ! $tag->isVisible() )
			{
				if ( $is_visible )
				{
					$pt = new WGV8BasicPaginationState();
					$pt->setNumber( "..." );
					$pt->setDisable();

					$alltags[] = $pt;
				}
			}
			else
			{
				$alltags[] = $tag;
			}
			$is_visible = $tag->isVisible();
		}

		/**
		 * Rendering
		 */
		$li = [];
		foreach ( $alltags as $tag )
		{
			$li[] = $tag->makeButton();
		}
		$body = implode( "\n", $li );

		$li = [];
		foreach ( $this->limitList as $k => $c )
		{
			$active = ( (int) $k === (int) $this->limit ) ? "active" : "";
			$li[]   = sprintf(
				'<li role="presentation" class="%s"><a role="menuitem" tabindex="-1" href="javascript:void(0)" data-value="%s" data-caption="%s" onclick="%s">%s</a></li>',
				$active,
				htmlspecialchars( $k ),
				htmlspecialchars( $c ),
				$this->js( 1, $this->limitKey . ':' . $k ),
				htmlspecialchars( $c )
			);
		}
		$cap = htmlspecialchars( $this->limitList[ $this->limit ] );
		$lis = implode( "\n", $li );

		if ( $this->total > 0 )
		{
			$totalCaption = sprintf( '<span class="badge">%s件</span>', number_format( $this->total ) );
		}
		else
		{
			$totalCaption = '<span class="badge">データなし</span>';
		}

		/**
		 * view id
		 */
		$id = $this->getId();

		return <<<HTML
<nav>
	<div class="form-group">
		<div class="form-inline">
			<div id="{$id}_pagination_ul">
				<div class="btn-group">$body</div>
			</div>
			
			<div id="{$id}_pagination_dropdown" class="dropdown">
				<button class="btn btn-default" type="button" id="{$id}_toggle" data-toggle="dropdown">
					<span id="{$id}_caption">$cap</span> <span class="caret"></span>
				</button>
				<ul id="{$id}_ul" class="dropdown-menu" role="menu" aria-labelledby="{$id}_toggle">
			$lis
				</ul>
				$totalCaption
			</div>
			
		</div>
	</div>
</nav>
<style>#{$id}_pagination_ul,#{$id}_pagination_dropdown { display:inline-block; }</style>

HTML;
	}
}
