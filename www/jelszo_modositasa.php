<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();
if ($adataim['epp_most_helyettes_id']!=0) kilep($lang[$lang_lang]['kisphpk']['Nem változtathatod meg annak a jelszavát, akit helyettesítesz.']);//a helyettesem ne tudjon, csak en magamnak

$jelszo_hash=hash('whirlpool',$_REQUEST['regi'].$adataim['jelszo_so'].$rendszer_so);
if ($jelszo_hash!=$adataim['jelszo_hash']) kilep($lang[$lang_lang]['kisphpk']['Hibás a régi jelszó.']);

if ($_REQUEST['uj1']!=$_REQUEST['uj2']) kilep($lang[$lang_lang]['kisphpk']['Nem egyezik meg a kétszer begépelt új jelszó.']);

$jelszo=$_REQUEST['uj1'];
$jelszo_so=randomgen(32);
$jelszo_hash=hash('whirlpool',$jelszo.$jelszo_so.$rendszer_so);
$kozos_jelszo_hash=hash('whirlpool',$jelszo.$rendszer_so);
$session_so=randomgen(32);
$token=randomgen(32);
$ttt=time()+$suti_hossz;
$datum=date('Y-m-d H:i:s',$ttt);
mysql_query('update userek set kozos_jelszo_hash="'.$kozos_jelszo_hash.'", jelszo_so="'.$jelszo_so.'", jelszo_hash="'.$jelszo_hash.'", session_so="'.$session_so.'",token="'.$token.'",session_ervenyesseg="'.$datum.'",uccso_login="'.date('Y-m-d H:i:s').'",uccso_login_ip="'.gethostbyaddr($_SERVER['REMOTE_ADDR']).' ('.$_SERVER['REMOTE_ADDR'].')" where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
setcookie('uid',$session_so.$uid,$ttt,'/');
?>