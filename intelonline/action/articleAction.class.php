<?php

/**
 * 文章控制器类
 *
 */
class articleAction extends Action {
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ( false );
	}
	function index() {
	}
	
	/**
	 * 文章详情
	 */
	function article_detail() {
		$url = $_GET ['url'];
		// $url = 'http://g.pconline.com.cn/x/323/3231765.html';
		// $url = 'http://g.pconline.com.cn/x/313/3130442.html';
		// $url = urlencode($url);
		// $url = 'http://g.pconline.com.cn/261/2615844.html';
		$p = isset ( $_GET ['p'] ) ? $_GET ['p'] : 1;
		phpQuery::newDocumentFile ( $url );
		$doms = pq ( '.body .artWrap' );
		$articleTitle = $doms->find ( '.artTitle' )->html ();
		
		if ($articleTitle == '') {
			exit ( "很抱歉，您浏览的页面暂时不能访问哦！" );
		}
		
		$artTime = $doms->find ( '.artInfo .sInfo-1' )->text ();
		$artAuthor = $doms->find ( '.artInfo .sInfo-2' )->text ();
		$artAuthor = trim ( $artAuthor );
		// $content = $doms->find('.artBody .artBody-con')->html();
		$content = $doms->find ( '.artBody' )->html ();
		$content = preg_replace ( '/href/', 'name', $content );
		// $content = preg_replace('/http:\/\/([a-zA-Z])+.pconline.com.cn(\/[0-9a-zA-Z_]+)*(.html)*/', '', $content);
		$content = str_replace ( '详情点击这里', '', $content );
		$content = preg_replace ( '/相关阅读：/', '', $content );
		$content = preg_replace ( '/style=/', 'stylehide', $content );
		
		$prevurl = $doms->find ( '.artNav-page .prev' )->attr ( 'href' );
		$nexturl = $doms->find ( '.artNav-page .next' )->attr ( 'href' );
		$pageViewStr = $doms->find ( '.artNav-page .pageViewGuidedd' )->text ();
		
		$next = $p + 1;
		$prev = $p - 1;
		if ($prevurl) {
			$pages .= '<a href="' . redirctArticle ( $prevurl ) . '&p=' . $prev . '" >上一页</a>';
		} else {
			$pages .= '<a>上一页</a>';
		}
		
		if ($nexturl) {
			$pages .= '<a href="' . redirctArticle ( $nexturl ) . '&p=' . $next . '" >下一页</a>';
		} else {
			$pages .= '<a>下一页</a>';
		}
		$showpages = false;
		if ($prevurl || $nexturl) {
			$showpages = true;
			preg_match ( '/共计([0-9]+)页/', $pageViewStr, $match );
			$total = $match [1];
			if ($p > $total) {
				$p = $total;
			}
			$pages .= ' 共' . $p . '/' . $total . '页';
		}
		
		$this->assign ( array (
				'articleTitle' => $articleTitle,
				'artTime' => $artTime,
				'artAuthor' => $artAuthor,
				'content' => $content,
				'prevurl' => $prevurl,
				'nexturl' => $nexturl,
				'pages' => $pages,
				'showpages' => $showpages 
		) );
		
		// $info = $this->smarty->fetch('tpl/article_detail.html');
		
		$this->display ( 'wap/article_detail.html' );
	}
	function pcarticle_detail() {
		$url = $_GET ['url'];
		$p = isset ( $_GET ['p'] ) ? $_GET ['p'] : 1;
		phpQuery::newDocumentFile ( $url );
		$doms = pq ( '.body .artWrap' );
		$articleTitle = $doms->find ( '.artTitle' )->html ();
		
		if ($articleTitle == '') {
			exit ( "很抱歉，您浏览的页面暂时不能访问哦！" );
		}
		
		$artTime = $doms->find ( '.artInfo .sInfo-1' )->text ();
		$artAuthor = $doms->find ( '.artInfo .sInfo-2' )->text ();
		$artAuthor = trim ( $artAuthor );
		// $content = $doms->find('.artBody .artBody-con')->html();
		$content = $doms->find ( '.artBody' )->html ();
		$content = preg_replace ( '/href/', 'name', $content );
		// $content = preg_replace('/http:\/\/([a-zA-Z])+.pconline.com.cn(\/[0-9a-zA-Z_]+)*(.html)*/', '', $content);
		$content = str_replace ( '详情点击这里', '', $content );
		$content = preg_replace ( '/相关阅读：/', '', $content );
		$content = preg_replace ( '/style=/', 'stylehide', $content );
		
		$prevurl = $doms->find ( '.artNav-page .prev' )->attr ( 'href' );
		$nexturl = $doms->find ( '.artNav-page .next' )->attr ( 'href' );
		$pageViewStr = $doms->find ( '.artNav-page .pageViewGuidedd' )->text ();
		
		$next = $p + 1;
		$prev = $p - 1;
		if ($prevurl) {
			$pages .= '<a href="' . redirctArticle ( $prevurl ) . '&p=' . $prev . '" >上一页</a>';
		} else {
			$pages .= '<a>上一页</a>';
		}
		
		if ($nexturl) {
			$pages .= '<a href="' . redirctArticle ( $nexturl ) . '&p=' . $next . '" >下一页</a>';
		} else {
			$pages .= '<a>下一页</a>';
		}
		$showpages = false;
		if ($prevurl || $nexturl) {
			$showpages = true;
			preg_match ( '/共计([0-9]+)页/', $pageViewStr, $match );
			$total = $match [1];
			if ($p > $total) {
				$p = $total;
			}
			$pages .= ' 共' . $p . '/' . $total . '页';
		}
		
		$this->assign ( array (
				'articleTitle' => $articleTitle,
				'artTime' => $artTime,
				'artAuthor' => $artAuthor,
				'content' => $content,
				'prevurl' => $prevurl,
				'nexturl' => $nexturl,
				'pages' => $pages,
				'showpages' => $showpages 
		) );
		
		// $info = $this->smarty->fetch('tpl/article_detail.html');
		
		$this->display ( 'wap/article_detail.html' );
	}
	
	/**
	 * 文章详情
	 */
	function test_detail() {
		$url = 'http://g.pconline.com.cn/x/313/3130442_4.html';
		// $url = 'http://g.pconline.com.cn/x/150/1502206.html';
		$p = isset ( $_GET ['p'] ) ? $_GET ['p'] : 1;
		phpQuery::newDocumentFile ( $url );
		$doms = pq ( '.body .artWrap' );
		$articleTitle = $doms->find ( '.artTitle' )->html ();
		
		if ($articleTitle == '') {
			exit ( "很抱歉，您浏览的页面暂时不能访问哦！" );
		}
		
		$artTime = $doms->find ( '.artInfo .sInfo-1' )->text ();
		$artAuthor = $doms->find ( '.artInfo .sInfo-2' )->text ();
		$artAuthor = trim ( $artAuthor );
		// $content = $doms->find('.artBody .artBody-con')->html();
		$content = $doms->find ( '.artBody' )->html ();
		$content = preg_replace ( '/href/', 'name', $content );
		// $content = preg_replace('/http:\/\/([a-zA-Z])+.pconline.com.cn(\/[0-9a-zA-Z_]+)*(.html)*/', '', $content);
		$content = str_replace ( '详情点击这里', '', $content );
		$content = preg_replace ( '/相关阅读：/', '', $content );
		
		$prevurl = $doms->find ( '.artNav-page .prev' )->attr ( 'href' );
		$nexturl = $doms->find ( '.artNav-page .next' )->attr ( 'href' );
		$pageViewStr = $doms->find ( '.artNav-page .pageViewGuidedd' )->text ();
		
		$next = $p + 1;
		$prev = $p - 1;
		if ($prevurl) {
			$pages .= '<a href="' . redirctArticle ( $prevurl ) . '&p=' . $prev . '" >上一页</a>';
		} else {
			$pages .= '<a>上一页</a>';
		}
		
		if ($nexturl) {
			$pages .= '<a href="' . redirctArticle ( $nexturl ) . '&p=' . $next . '" >下一页</a>';
		} else {
			$pages .= '<a>下一页</a>';
		}
		$showpages = false;
		if ($prevurl || $nexturl) {
			$showpages = true;
			preg_match ( '/共计([0-9]+)页/', $pageViewStr, $match );
			$total = $match [1];
			if ($p > $total) {
				$p = $total;
			}
			$pages .= ' 共' . $p . '/' . $total . '页';
		}
		
		$this->assign ( array (
				'articleTitle' => $articleTitle,
				'artTime' => $artTime,
				'artAuthor' => $artAuthor,
				'content' => $content,
				'prevurl' => $prevurl,
				'nexturl' => $nexturl,
				'pages' => $pages,
				'showpages' => $showpages 
		) );
		
		// $info = $this->smarty->fetch('tpl/article_detail.html');
		
		$this->display ( 'wap/article_detail_test.html' );
	}
	
	/**
	 * 图文详情页
	 */
	public function material_article() {
		$id = isset ( $_GET ['id'] ) && ! empty ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$material = new material ();
		$info = $material->getArticleById ( $id );
		$url = trim ( $info ['url'] );
		if ($url) {
			header ( "Location:$url" );
		}
		// $info['content'] = str_replace('<em>', '<span style="font-style:italic !important">', $info['content']);
		// $info['content'] = str_replace('</em>', '</span>', $info['content']);
		
		/*
		 * $info['content'] = str_replace('width:', 'swidth:', $info['content']);
		 * $info['content'] = str_replace('height:', 'sheight:', $info['content']);
		 * $info['content'] = str_replace('width=', 'swidth=', $info['content']);
		 * $info['content'] = str_replace('height=', 'sheight=', $info['content']);
		 * $info['content'] = str_replace('class="main"', '', $info['content']);
		 * $info['content'] = str_replace('line-sheight:', 'line-height:', $info['content']);
		 */
		phpQuery::newDocument ( "<shtml>" . $info ['content'] . "<shtml>" );
		pq ( "img" )->removeAttr ( "width" )->removeAttr ( "height" )->removeAttr ( "style" );
		$content = pq ( "shtml" )->html ();
		$info ['content'] = $content;
		$info ['content'] = str_replace ( 'class="main"', '', $info ['content'] );
		$info ['content'] = str_replace ( '<h2', '<div', $info ['content'] );
		$info ['content'] = str_replace ( '</h2', '</div', $info ['content'] );
		
		$this->assign ( array (
				'info' => $info,
				'id' => $id 
		) ); // (文id 为161,162,156 文章页面做分享)
		$this->display ( 'material/article.html' );
	}
	public function test_article() {
		$id = isset ( $_GET ['id'] ) && ! empty ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$material = new material ();
		$info = $material->getArticleById ( $id );
		$url = trim ( $info ['url'] );
		if ($url) {
			header ( "Location:$url" );
		}
		phpQuery::newDocument ( "<shtml>" . $info ['content'] . "<shtml>" );
		pq ( "img" )->removeAttr ( "width" )->removeAttr ( "height" )->removeAttr ( "style" );
		$content = pq ( "shtml" )->html ();
		$info ['content'] = $content;
		$info ['content'] = str_replace ( 'class="main"', '', $info ['content'] );
		$this->assign ( array (
				'info' => $info,
				'id' => $id 
		) ); // (文id 为161,162,156 文章页面做分享)
		$this->display ( 'material/article_new.html' );
	}
	
	/**
	 * 图文详情页 (IIP) add lhg
	 */
	public function material_article_IIP() {
		$id = isset ( $_GET ['id'] ) && ! empty ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		$material = new IIPmaterial ();
		$info = $material->getArticleById ( $id );
		$url = trim ( $info ['url'] );
		if ($url) {
			header ( "Location:$url" );
		}
		// $info['content'] = str_replace('<em>', '<span style="font-style:italic !important">', $info['content']);
		// $info['content'] = str_replace('</em>', '</span>', $info['content']);
		$info ['content'] = str_replace ( 'width:', 'swidth:', $info ['content'] );
		$info ['content'] = str_replace ( 'width:', 'swidth:', $info ['content'] );
		$info ['content'] = str_replace ( 'height:', 'sheight:', $info ['content'] );
		$info ['content'] = str_replace ( 'width=', 'swidth=', $info ['content'] );
		$info ['content'] = str_replace ( 'height=', 'sheight=', $info ['content'] );
		$info ['content'] = str_replace ( 'line-sheight:', 'line-height:', $info ['content'] );
		$this->assign ( array (
				'info' => $info 
		) );
		$this->display ( 'material/article.html' );
	}
}