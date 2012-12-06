<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();

if ($_REQUEST['id_lista']=='') {//osszes
	mysql_query('delete from csata_user where user_id='.$uid);
} else {//kivalasztottak
	$lista=explode(',',$_REQUEST['id_lista']);
	if (is_array($lista)) {
		$L='';
		for($i=0;$i<count($lista);$i++) $L.=','.((int)$lista[$i]);
		mysql_query('delete from csata_user where csata_id in ('.substr($L,1).') and user_id='.$uid);
	}
}

kilep();
?>