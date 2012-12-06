<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];
$_REQUEST['hova']=(int)$_REQUEST['hova'];

$q_er=mysql_query('select * from user_tagek where id='.$_REQUEST['id'].' and tulaj='.$uid);
$q=mysql_fetch_array($q_er);
if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tulajdonság.']);


if ($_REQUEST['hova']>0) {
	$er2=mysql_query('select * from user_tagek where tulaj='.$uid.' and sorszam>'.$q['sorszam'].' order by sorszam limit 1') or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er2=mysql_query('select * from user_tagek where tulaj='.$uid.' and sorszam<'.$q['sorszam'].' order by sorszam desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
}
$aux2=mysql_fetch_array($er2);
if ($aux2) {
	mysql_query('update user_tagek set sorszam='.$q['sorszam'].' where id='.$aux2['id']);
	mysql_query('update user_tagek set sorszam='.$aux2['sorszam'].' where id='.$q['id']);
}


kilep();
?>