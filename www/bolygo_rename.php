<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$_REQUEST['nev']=trim(megengedhetove_tesz($_REQUEST['nev']));
if ($_REQUEST['nev']=='') $_REQUEST['nev']='B'.$_REQUEST['bolygo_id'];

if ($adataim['karrier']==3 && $adataim['speci']==3) {//fantom
	mysql_query('update bolygok set nev="'.mysql_real_escape_string($_REQUEST['nev']).'" where id='.$_REQUEST['bolygo_id']);
} else {//mindenki mas
	mysql_query('update bolygok set nev="'.mysql_real_escape_string($_REQUEST['nev']).'",kulso_nev="'.mysql_real_escape_string($_REQUEST['nev']).'" where id='.$_REQUEST['bolygo_id']);
}

kilep();
?>