<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();

if ($_REQUEST['id_lista']=='') {//osszes
	mysql_query('delete c from levelek as l use index (`tulaj_idx`,`felado_idx`), cimzettek as c use index for join (`primary`) where l.tulaj='.$uid.' and l.felado=0 and c.level_id=l.id') or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('delete from levelek where tulaj='.$uid.' and felado=0') or hiba(__FILE__,__LINE__,mysql_error());
} else {//kivalasztottak
	$lista=explode(',',$_REQUEST['id_lista']);
	if (is_array($lista)) {
		for($i=0;$i<count($lista);$i++) {
			$er=mysql_query('select tulaj,felado from levelek where id='.$lista[$i]) or hiba(__FILE__,__LINE__,mysql_error());
			$aux=mysql_fetch_array($er);
			if ($aux['tulaj']==$uid && $aux['felado']==0) {
				mysql_query('delete from levelek where id='.$lista[$i]) or hiba(__FILE__,__LINE__,mysql_error());
				mysql_query('delete from cimzettek where level_id='.$lista[$i]) or hiba(__FILE__,__LINE__,mysql_error());
			}
		}
	}
}

kilep();
?>