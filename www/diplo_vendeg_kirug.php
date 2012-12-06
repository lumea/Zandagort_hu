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

$kirugas_jelolt=mysql2row('select * from userek where id='.sanitint($_REQUEST['kit']));
if (!$kirugas_jelolt) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$adataim['szovetseg'].' and user_id='.$kirugas_jelolt['id']);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen vendég a szövetségedben.']);

mysql_query('delete from szovetseg_vendegek where szov_id='.$adataim['szovetseg'].' and user_id='.$kirugas_jelolt['id']);
?>