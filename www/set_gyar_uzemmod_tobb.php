<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['gyar_id']=(int)$_REQUEST['gyar_id'];
$_REQUEST['u']=(int)$_REQUEST['u'];
$darab=(int)$_REQUEST['db'];

$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$_REQUEST['bolygo_id'].' and gyar_id='.$_REQUEST['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$bgy_honnan=mysql_fetch_array($er);
if (!$bgy_honnan) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen gyár.']);

if ($darab>$bgy_honnan['db']) $darab=$bgy_honnan['db'];
if ($darab==0) kilep();

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$er=mysql_query('select * from gyarak where id='.$_REQUEST['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$honnan=mysql_fetch_array($er);

$er=mysql_query('select * from gyarak where tipus='.$honnan['tipus'].' and szint='.$honnan['szint'].' and uzemmod='.$_REQUEST['u']) or hiba(__FILE__,__LINE__,mysql_error());
$hova=mysql_fetch_array($er);

if (!elerheto_ez_a_gyar($aux2['osztaly'],$aux2['hold'],$hova['id'],$uid)) kilep($lang[$lang_lang]['kisphpk']['Ez az üzemmód nem elérhető.']);

if ($bgy_honnan['aktiv_db']>0) {//aktiv+inaktiv atuzemelese
	if ($darab>$bgy_honnan['aktiv_db']) $darab_aktiv=$bgy_honnan['aktiv_db'];else $darab_aktiv=$darab;
	gyar_uj_uzemmodba_allitasa($_REQUEST['bolygo_id'],$honnan['id'],$hova['id'],1,$darab_aktiv);
	if ($darab>$darab_aktiv) {
		gyar_uj_uzemmodba_allitasa($_REQUEST['bolygo_id'],$honnan['id'],$hova['id'],0,$darab-$darab_aktiv);
	}
} elseif ($bgy_honnan['db']>0) {//inaktiv atuzemelese
	gyar_uj_uzemmodba_allitasa($_REQUEST['bolygo_id'],$honnan['id'],$hova['id'],0,$darab);
}

kilep();
?>