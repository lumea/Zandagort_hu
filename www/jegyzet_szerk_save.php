<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];

if ($_REQUEST['id']==0) {//uj jegyzet
	if (strlen(trim($_REQUEST['szoveg']))) {
		$q_er=mysql_query('select max(sorszam) from jegyzetek where tulaj='.$uid);
		$aux=mysql_fetch_array($q_er);
		mysql_query('insert into jegyzetek (tulaj,szoveg,sorszam) values('.$uid.',"'.sanitstr_html($_REQUEST['szoveg']).'",'.($aux[0]+1).')');
	}
} else {
	$q_er=mysql_query('select * from jegyzetek where id='.$_REQUEST['id'].' and tulaj='.$uid);
	$q=mysql_fetch_array($q_er);
	if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen jegyzet.']);
	if (strlen(trim($_REQUEST['szoveg']))) {
		mysql_query('update jegyzetek set szoveg="'.sanitstr_html($_REQUEST['szoveg']).'" where id='.$_REQUEST['id']);
	} else {
		mysql_query('delete from jegyzetek where id='.$_REQUEST['id']);
	}
}

kilep();
?>