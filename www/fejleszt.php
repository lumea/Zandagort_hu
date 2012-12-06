<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$_REQUEST['tema']=(int)$_REQUEST['tema'];
$_REQUEST['kp']=(int)$_REQUEST['kp'];

if ($_REQUEST['tema']<=2) kilep();//nem fejlesztheto temak

if ($_REQUEST['kp']<=0) kilep($lang[$lang_lang]['kisphpk']['Legalább 1 KP-t fel kell használnod.']);

$r=mysql_query('select * from kutatasi_temak where id='.$_REQUEST['tema']);
$tema=mysql_fetch_array($r);
if (!$tema) kilep();

$feltetelek=mysql2num('select coalesce(sum(uksz.szint<kt.max_szint),0)
from kutatasi_feltetelek kf
inner join kutatasi_temak kt on kt.id=kf.feltetel
inner join user_kutatasi_szint uksz on uksz.kf_id=kt.id and uksz.user_id='.$uid.'
where tema='.$_REQUEST['tema']);
if ($feltetelek>0) kilep();


if ($_REQUEST['kp']>$adataim['kp']) $_REQUEST['kp']=$adataim['kp'];
if ($_REQUEST['kp']<=0) kilep($lang[$lang_lang]['kisphpk']['Nincs KP-d a fejlesztéshez.']);

$r=mysql_query('select * from user_kutatasi_szint where user_id='.$uid.' and kf_id='.$tema['id']);
$szint=mysql_fetch_array($r);

if ($szint['szint']+$_REQUEST['kp']>$tema['max_szint']) $_REQUEST['kp']=$tema['max_szint']-$szint['szint'];
if ($_REQUEST['kp']<=0) kilep();

mysql_query('update user_kutatasi_szint set szint=szint+'.$_REQUEST['kp'].' where user_id='.$uid.' and kf_id='.$tema['id']);
mysql_query('update userek set kp=if(kp>'.$_REQUEST['kp'].',kp-'.$_REQUEST['kp'].',0) where id='.$uid);

kilep();
?>