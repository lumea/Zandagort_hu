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

if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_2']) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívási jogod.']);

$_REQUEST['kit']=(int)$_REQUEST['kit'];
$er=mysql_query('select * from userek where id='.$_REQUEST['kit']) or hiba(__FILE__,__LINE__,mysql_error());
$meghivott=mysql_fetch_array($er);
if (!$meghivott) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

$er=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$szovetseg_neve=$aux['nev'];

mysql_query('delete from szovetseg_meghivas_kerelmek where ki='.$_REQUEST['kit'].' and hova='.$adataim['szovetseg']);

rendszeruzenet($meghivott['id']
,'Belépési kérelem elutasítva: '.$szovetseg_neve
,'A(z) '.$szovetseg_neve.' szövetség elutasította belépési kérelmedet.'
,'Entry application rejected: '.$szovetseg_neve
,'The alliance '.$szovetseg_neve.' has rejected your entry application.');

kilep();
?>