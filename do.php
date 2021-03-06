<?php
	require_once('./config.php');
	require_once('./class/conn.class.php');
	include('./include/function.php');
	$con = new conn();
	if( !mkglobal('user:password:openid') ) exit( json_encode(array('status'=>'fail','errmsg'=>'非法操作')));
	$sql = "SELECT * FROM `weixin` WHERE `openid` = '$openid'";
	$result = $con->query($sql);
	if( !empty($result) ) exit( json_encode( array( 'status'=>'fail','errmsg'=>'已绑定账户!') ) );
	$sql = "SELECT * FROM `users` WHERE `username` = '$user' ";
	$result = $con->query($sql);
	if( $result != NULL ){
		$rs = $result[0];
		if( $rs['status'] == 'pending' ) {
			echo json_encode( array('status'=>'fail','errmsg'=>'账户未激活') );
			exit;
		}
		if( $rs['passhash'] != md5( $rs['secret']  . $password . $rs['secret'] ) ){
			var_dump($rs);
			echo md5( $rs['secret']  . $password . $rs['secret'] );
			echo json_encode( array('status'=>'fail','errmsg'=>'密码错误!') );
			exit;
		}
		$sql = "INSERT INTO `weixin` (`ptid` , `openid` ) VALUES ('{$rs['id']}','$openid')";
		if( $con->query($sql) ) {
			echo json_encode(array('status'=>'success','errmsg'=>'成功!'));
		}else{ 
			echo json_encode(array('status'=>'fail','errmsg'=>'绑定失败,请联系管理员.'));
		}
	}else{
		echo json_encode(array('status'=>'fail','errmsg'=>'没有此账户'));
	}