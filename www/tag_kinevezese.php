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

if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_4']) kilep($lang[$lang_lang]['kisphpk']['Nincs kinevezési jogod.']);


$_REQUEST['kit']=sanitstr($_REQUEST['kit']);
$_REQUEST['hova']=(int)$_REQUEST['hova'];if ($_REQUEST['hova']<0) $_REQUEST['hova']=0;

if ($_REQUEST['hova']>0) {
	$er=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$_REQUEST['hova']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tisztség a szövetségben.']);
}

mysql_query('update userek set tisztseg='.$_REQUEST['hova'].' where szovetseg='.$adataim['szovetseg'].' and tisztseg>=0 and nev="'.$_REQUEST['kit'].'"') or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>