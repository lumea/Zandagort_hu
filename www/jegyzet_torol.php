<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];

$q_er=mysql_query('select * from jegyzetek where id='.$_REQUEST['id'].' and tulaj='.$uid);
$q=mysql_fetch_array($q_er);
if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen jegyzet.']);

mysql_query('delete from jegyzetek where id='.$q['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update jegyzetek set sorszam=sorszam-1 where tulaj='.$uid.' and sorszam>'.$q['sorszam']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>