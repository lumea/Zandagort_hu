<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

mysql_query('lock tables ugynokcsoportok write');
$_REQUEST['hova']=sanitint($_REQUEST['hova']);
if ($_REQUEST['hova']>0) $hova=mysql2row('select * from ugynokcsoportok where tulaj='.$uid.' and id='.$_REQUEST['hova']);
$ucsk=explode(',',$_REQUEST['mennyisegek']);
for($i=0;$i<count($ucsk);$i++) {
	$ucs=explode(':',$ucsk[$i]);
	$honnan_id=(int)$ucs[0];
	$mennyit=sanitint($ucs[1]);
	if ($mennyit>0) {
		$honnan=mysql2row('select * from ugynokcsoportok where tulaj='.$uid.' and id='.$honnan_id);
		if ($mennyit>$honnan['darab']) $mennyit=$honnan['darab'];
		if ($mennyit>0) {
			if (!$hova) {//uj csop letrehozasa
				mysql_query('insert into ugynokcsoportok (tulaj,tulaj_szov,bolygo_id) values('.$honnan['tulaj'].','.$honnan['tulaj_szov'].','.$honnan['bolygo_id'].')');
				$aux=mysql2row('select last_insert_id() from ugynokcsoportok');
				$hova=mysql2row('select * from ugynokcsoportok where tulaj='.$uid.' and id='.$aux[0]);
			}
			if ($hova['tulaj']==$honnan['tulaj']) if ($hova['bolygo_id']==$honnan['bolygo_id']) {//tenyleges mozgatas
				mysql_query('update ugynokcsoportok set darab=darab-'.$mennyit.' where id='.$honnan['id']);
				mysql_query('update ugynokcsoportok set darab=darab+'.$mennyit.' where id='.$hova['id']);
			}
		}
	}
}
mysql_query('delete from ugynokcsoportok where darab=0');
mysql_query('unlock tables');


kilep();
?>