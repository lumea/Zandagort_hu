<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['gyar_id']=(int)$_REQUEST['gyar_id'];
$_REQUEST['db']=(int)$_REQUEST['db'];if ($_REQUEST['db']<1) $_REQUEST['db']=1;//if ($_REQUEST['db']>100) $_REQUEST['db']=100;
$aktiv_e=1;if (isset($_REQUEST['a'])) if ($_REQUEST['a']==0) $aktiv_e=0;
$_REQUEST['hova']=(int)$_REQUEST['hova'];

$er=mysql_query('select * from gyarak where id='.$_REQUEST['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$gyar=mysql_fetch_array($er);
if (!$gyar) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen épülettípus.']);

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

if (!elerheto_ez_a_gyar($aux2['osztaly'],$aux2['hold'],$gyar['id'],$uid)) kilep($lang[$lang_lang]['kisphpk']['Ez az épület nem elérhető.']);

if (premium_szint()==0) {
	$hany_elem_van=mysql2num('select count(1) from queue_epitkezesek where bolygo_id='.$_REQUEST['bolygo_id']);
	//if ($hany_elem_van>=5) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);
	if ($hany_elem_van>=5) kilep($lang[$lang_lang]['kisphpk']['Ha ötnél több elemet szeretnél az építési listádba tenni, elő kell fizetned.']);
}

if ($_REQUEST['hova']==2) {
	$er5=mysql_query('select max(sorszam) from queue_epitkezesek where bolygo_id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux5=mysql_fetch_array($er5);
	mysql_query('insert into queue_epitkezesek (bolygo_id,gyar_id,aktiv,darab,sorszam) values('.$_REQUEST['bolygo_id'].','.$_REQUEST['gyar_id'].','.$aktiv_e.','.$_REQUEST['db'].','.($aux5[0]+1).')') or hiba(__FILE__,__LINE__,mysql_error());
} else {
	mysql_query('update queue_epitkezesek set sorszam=sorszam+1 where bolygo_id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('insert into queue_epitkezesek (bolygo_id,gyar_id,aktiv,darab,sorszam) values('.$_REQUEST['bolygo_id'].','.$_REQUEST['gyar_id'].','.$aktiv_e.','.$_REQUEST['db'].',1)') or hiba(__FILE__,__LINE__,mysql_error());
}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>