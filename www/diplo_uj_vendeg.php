<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']>0) {
	if ($jogaim[6]==0) kilep($lang[$lang_lang]['kisphpk']['Nincs vendéghívó jogod.']);
	$ki_vagy_id=$adataim['szovetseg'];
} else kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$_REQUEST['kit']=sanitstr($_REQUEST['kit']);
if (strlen($_REQUEST['kit'])==0) kilep($lang[$lang_lang]['kisphpk']['Írd be, hogy melyik tanácsnokot szeretnéd meghívni vendégként.']);
$vendeg_jelolt=mysql2row('select * from userek where nev="'.$_REQUEST['kit'].'"');
if (!$vendeg_jelolt) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);
if ($vendeg_jelolt['szovetseg']==$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Szövitársat nem lehet meghívni vendégnek.']);
if ($vendeg_jelolt['karrier']!=4 or $vendeg_jelolt['speci']!=2) kilep($lang[$lang_lang]['kisphpk']['Ez a játékos nem tanácsnok.']);

$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$adataim['szovetseg'].' and user_id='.$vendeg_jelolt['id']);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ennek a játékosnak már van vendég státusza a szövetségedben.']);

mysql_query('insert into szovetseg_vendegek (szov_id,user_id,ki_hivta) values('.$adataim['szovetseg'].','.$vendeg_jelolt['id'].','.$uid.')');
?>