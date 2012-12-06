<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);
if ($adataim['tisztseg']!=-1) kilep($lang[$lang_lang]['kisphpk']['Nem te vagy az alapító.']);

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tisztség a szövetségben.']);

mysql_query('delete from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update userek set tisztseg=0 where szovetseg='.$adataim['szovetseg'].' and tisztseg='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>