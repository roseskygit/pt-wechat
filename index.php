<?php
	require_once('./config.php');
	require_once('./class/conn.class.php');
	require_once('./class/wechat.class.php');
	include ('./include/function.php');
	header('Content-type:text/html;charset=utf-8');
	const ALREADY_BINDED = '你已经绑定过了';
	const HELP = '支持的命令: 状态 , 查询@关键字 , 最新 , 热门';
	$user_ptid = -1;
	$operation = 'NULL';
	$text = '';
	$keyword = '';
	$user_openid = '';
	$con = new conn();
	$weObj = new Wechat( $wechat_options );
	$weObj->valid();
	$msg = $weObj->getRev()->getRevData();//获取用户发送的消息
	$user_openid = $weObj->getRev()->getRevFrom();//获取用户openid
	$user_openid = $con->mres($user_openid);
	$msg = trim($msg['Content']);
	switch ($msg) {
		case '绑定':
			$operation = BIND;
			break;
		case '状态':
			$operation = STATUS;
			break;
		case '热门':
			$operation = HOT;
			break;
		case '最新':
			$operation = NEWLY;
			break;
		default:
			$arr = explode( '@' , $msg );
			if( $arr[0] =='查询') {
				$keyword = $con->mres( $arr[1] );
				$operation = SEARCH;
			}
			break;
	}
	if( bind_check()) {
		if ($operation == BIND){
			$text = ALREADY_BINDED;
		}else if ($operation == HOT){
			$text = search( NULL, HOT );
		}else if ($operation == NEWLY){
			$text = search( NULL, NEWLY );
		}else if ($operation == STATUS){
			$text = user_status() ;
		}else if ($operation == SEARCH){
			$text = search( $keyword , NULL);
		}else {
			$text = HELP;
		}
	}else {
		$text = user_bind();
	}
	//echo $text;
	 $weObj->text($text);
	 $weObj->reply();

