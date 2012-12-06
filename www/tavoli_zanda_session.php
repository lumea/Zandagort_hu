<?
function tavoli_zanda_session() {
	return array(0,'');
}

/*
function zanda_sanitstr($x) {
	if (get_magic_quotes_gpc()) $x=stripslashes($x);
	return mysql_real_escape_string(trim(strip_tags($x)));
}
function tavoli_zanda_session() {
	$tavoli_zandanet_csatlak=mysql_connect('HOST','USER','PASSWORD');//ZandaNet szerver
	mysql_select_db('mmog',$tavoli_zandanet_csatlak);
	mysql_query('set names "utf8"',$tavoli_zandanet_csatlak);
	//
	$ip=$_SERVER['REMOTE_ADDR'];
	$ua=$_SERVER['HTTP_USER_AGENT'];
	$ref=$_SERVER['HTTP_REFERER'];
	$access_now=date('Y-m-d H:i:s');
	$access_later=date('Y-m-d H:i:s',time()+1800);
	//
	$res=mysql_query('select * from sessions where ip="'.zanda_sanitstr($ip).'" and ua="'.zanda_sanitstr($ua).'" and `last`>="'.$access_now.'" order by id desc limit 1',$tavoli_zandanet_csatlak);
	$aux=mysql_fetch_array($res);
	if ($aux) {
		$ref=$aux['ref'];
		$session_id=$aux['id'];
		mysql_query('update sessions set `last`="'.$access_later.'" where id='.$session_id,$tavoli_zandanet_csatlak);
	} else {
		mysql_query('insert into sessions (ip,ua,ref,`last`) values("'.zanda_sanitstr($ip).'","'.zanda_sanitstr($ua).'","'.zanda_sanitstr($ref).'","'.$access_later.'")',$tavoli_zandanet_csatlak);
		$res=mysql_query('select last_insert_id() from sessions',$tavoli_zandanet_csatlak);
		$aux=mysql_fetch_array($res);
		$session_id=$aux[0];
	}
	//insert into pageviews
	mysql_query('insert into pageviews (session_id,url) values('.$session_id.',"'.zanda_sanitstr('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']).'")',$tavoli_zandanet_csatlak);
	//
	mysql_close($tavoli_zandanet_csatlak);
	return array($session_id,$ref);
}
*/
?>