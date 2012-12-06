<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']);
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_8']) kilep($lang[$lang_lang]['kisphpk']['Nincs témaszerkesztési jogod.']);

$_REQUEST['tema_id']=(int)$_REQUEST['tema_id'];

$er=mysql_query('select * from szov_forum_temak where id='.$_REQUEST['tema_id']);
$tema=mysql_fetch_array($er);
if (!$tema || $tema['szov_id']!=$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen téma.']);
if ($tema['belso'] && ($jogaim[1]==0)) kilep();

$_REQUEST['tema']=sanitstr($_REQUEST['tema']);if ($_REQUEST['tema']=='') $_REQUEST['tema']=$tema['cim'];
$_REQUEST['tema_belso']=(int)$_REQUEST['tema_belso'];if ($jogaim[1]==0) $_REQUEST['tema_belso']=0;
$_REQUEST['tema_vendeg']=(int)$_REQUEST['tema_vendeg'];if ($jogaim[6]==0) $_REQUEST['tema_vendeg']=0;


mysql_query('update szov_forum_temak set cim="'.$_REQUEST['tema'].'",belso='.$_REQUEST['tema_belso'].',vendeg='.$_REQUEST['tema_vendeg'].' where id='.$tema['id']);


kilep('***'.$tema['id']);
?>