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
if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_2']) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívási jogod, így törölni sem tudsz meghívót.']);

$_REQUEST['kit']=(int)$_REQUEST['kit'];

mysql_query('delete from szovetseg_meghivok where kit='.$_REQUEST['kit'].' and hova='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>