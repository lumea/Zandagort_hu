<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_9']) kilep($lang[$lang_lang]['kisphpk']['Nincs moderálási jogod.']);

$_REQUEST['tema_id']=(int)$_REQUEST['tema_id'];

$er=mysql_query('select * from szov_forum_temak where id='.$_REQUEST['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
$tema=mysql_fetch_array($er);
if (!$tema || $tema['szov_id']!=$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen téma.']);
if ($tema['belso'] && ($jogaim[1]==0)) kilep();

mysql_query('delete from szov_forum_kommentek where tema_id='.$_REQUEST['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from szov_forum_temak where id='.$_REQUEST['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from szov_forum_tema_olv where tema_id='.$_REQUEST['tema_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>