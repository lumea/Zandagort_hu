<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['gyar_id']=(int)$_REQUEST['gyar_id'];
$_REQUEST['db']=(int)$_REQUEST['db'];if ($_REQUEST['db']<1) $_REQUEST['db']=1;
$aktiv_e=1;if (isset($_REQUEST['a'])) if ($_REQUEST['a']==0) $aktiv_e=0;

$er=mysql_query('select * from gyarak where id='.$_REQUEST['gyar_id']);
$gyar=mysql_fetch_array($er);
if (!$gyar) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen épülettípus.']);

$er=mysql_query('select * from gyartipusok where id='.$gyar['tipus']);
$gyartipus=mysql_fetch_array($er);

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']);
$bolygo=mysql_fetch_array($er2);
if ($bolygo['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

if (!elerheto_ez_a_gyar($bolygo['osztaly'],$bolygo['hold'],$gyar['id'],$uid)) kilep($lang[$lang_lang]['kisphpk']['Ez az épület nem elérhető.']);


$van_epitenivalo=true;
$hirtelen=false;
//LOCK ELEJE
mysql_query('lock tables cron_tabla write, queue_epitkezesek write, gyar_epitesi_koltseg gyek write, bolygo_eroforras be write, eroforrasok e read, gyar_epitesi_ido read');

$er3=mysql_query('select group_concat('.$_REQUEST['db'].'*gyek.db-be.db," ",e.nev'.$lang__lang.' order by e.id separator ", ")
from gyar_epitesi_koltseg gyek, bolygo_eroforras be, eroforrasok e
where gyek.tipus='.$gyar['tipus'].' and gyek.szint='.$gyar['szint'].' and gyek.eroforras_id=be.eroforras_id and be.bolygo_id='.$_REQUEST['bolygo_id'].' and e.id=be.eroforras_id
and '.$_REQUEST['db'].'*gyek.db>be.db');
$aux3=mysql_fetch_array($er3);
if (strlen($aux3[0])>0) {//nem tud mindent megepiteni
	$er4=mysql_query('select coalesce(min(floor(be.db/gyek.db)),0) from gyar_epitesi_koltseg gyek, bolygo_eroforras be where gyek.tipus='.$gyar['tipus'].' and gyek.szint='.$gyar['szint'].' and gyek.eroforras_id=be.eroforras_id and gyek.db>0 and be.bolygo_id='.$_REQUEST['bolygo_id']);
	$aux4=mysql_fetch_array($er4);
	if ($aux4[0]<$_REQUEST['db']) {//queue
		//
		if (premium_szint()==0) {
			$hany_elem_van=mysql2num('select count(1) from queue_epitkezesek where bolygo_id='.$_REQUEST['bolygo_id']);
			if ($hany_elem_van>=5) {//nem fer mar a listara
				if ($aux4[0]>0) kilep_and_unlock($lang[$lang_lang]['kisphpk']['Csak '].$aux4[0].$lang[$lang_lang]['kisphpk']['-t tudnál most építeni. Az összeshez hiányzik: '].$aux3[0]);
				else kilep_and_unlock($lang[$lang_lang]['kisphpk']['Az építéshez hiányzik: '].$aux3[0]);
			}
		}
		//
		$er5=mysql_query('select max(sorszam) from queue_epitkezesek where bolygo_id='.$_REQUEST['bolygo_id']);
		$aux5=mysql_fetch_array($er5);
		mysql_query('insert into queue_epitkezesek (bolygo_id,gyar_id,aktiv,darab,sorszam) values('.$_REQUEST['bolygo_id'].','.$_REQUEST['gyar_id'].','.$aktiv_e.','.($_REQUEST['db']-$aux4[0]).','.($aux5[0]+1).')');
		$_REQUEST['db']=$aux4[0];
		//if ($_REQUEST['db']==0) kilep_and_unlock();
		if ($_REQUEST['db']==0) $van_epitenivalo=false;//tut_level-ek miatt fontos, h vegigmenjen akkor is, ha nincs mar mit epiteni
	}
}

if ($van_epitenivalo) {
	//van-e eleg hely, mire van eleg hely
	//ha (terulet_beepitett+db*gyar_terulet)/terraformaltsag*10000 > terulet, akkor csak részteljesítés:
	//db = floor((terulet/10000*terraformaltsag-terulet_beepitett)/gyar_terulet)
	if (($bolygo['terulet_beepitett']+$_REQUEST['db']*$gyartipus['terulet'])/$bolygo['terraformaltsag']*10000>$bolygo['terulet']) {
		$_REQUEST['db']=floor(($bolygo['terulet']/10000*$bolygo['terraformaltsag']-$bolygo['terulet_beepitett'])/$gyartipus['terulet']);
		if ($_REQUEST['db']<=0) kilep_and_unlock($lang[$lang_lang]['kisphpk']['Nincs elég szabad terület a bolygódon. Végezz építési fejlesztést!']);
	}

	mysql_query('update gyar_epitesi_koltseg gyek,bolygo_eroforras be set be.db=if(be.db>'.$_REQUEST['db'].'*gyek.db,be.db-'.$_REQUEST['db'].'*gyek.db,0) where gyek.tipus='.$gyar['tipus'].' and gyek.szint='.$gyar['szint'].' and gyek.eroforras_id=be.eroforras_id and be.bolygo_id='.$_REQUEST['bolygo_id']);

	$er4=mysql_query('select * from gyar_epitesi_ido where tipus='.$gyar['tipus'].' and szint='.$gyar['szint']);
	$aux4=mysql_fetch_array($er4);
	if ($adataim['karrier']==1 && $adataim['speci']==1) if (in_array($aux4['tipus'],$mernok_8_oras_gyarai)) $aux4['ido']=480;

	if ($adataim['tut_fa']==0) if ($gyartipus['id']==18) if ($_REQUEST['db']==1) $hirtelen=true;
	if ($adataim['tut_ko']==0) if ($gyartipus['id']==19) if ($_REQUEST['db']==1) $hirtelen=true;

	if (!$hirtelen) {
		mysql_query('insert into cron_tabla (mikor_aktualis,feladat,bolygo_id,gyar_id,aktiv,darab,indulo_allapot) values("'.date('Y-m-d H:i:s',time()+60*$aux4['ido']).'",'.FELADAT_GYAR_EPIT.','.$_REQUEST['bolygo_id'].','.$_REQUEST['gyar_id'].','.$aktiv_e.','.$_REQUEST['db'].',1)');
	}
}

mysql_query('unlock tables');
//LOCK VEGE


if ($hirtelen) {
	if ($gyartipus['id']==18) mysql_query('update userek set tut_fa=1 where id='.$uid);
	if ($gyartipus['id']==19) mysql_query('update userek set tut_ko=1 where id='.$uid);
	uj_gyar_felhuzasa($_REQUEST['bolygo_id'],$_REQUEST['gyar_id'],$aktiv_e,1);
	if ($adataim['tut_level']==1) {
		if ($adataim['tut_ko']==1) if ($gyartipus['id']==18) tut_level($uid,2);
		if ($adataim['tut_fa']==1) if ($gyartipus['id']==19) tut_level($uid,2);
	}
}

bolygo_terulet_frissites($_REQUEST['bolygo_id']);

// barmi valtozas van, ne feledd a szim.php-ban a queue-olt epiteseknel is megbuheralni!!!

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>