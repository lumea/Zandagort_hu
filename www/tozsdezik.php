<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['termek_id']=(int)$_REQUEST['termek_id'];
$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$mennyiseg=sanitint($_REQUEST['mennyiseg']);
$_REQUEST['vetel']=(int)$_REQUEST['vetel'];
$regio=(int)$_REQUEST['regio'];

//csak sajat regioban lehet
if ($adataim['karrier']==1 && $adataim['speci']==2) {//kereskedo
	if ($regio!=$adataim['aktualis_regio']) if ($regio!=$adataim['aktualis_regio2']) kilep();
} else {//nem kereskedo
	if ($regio!=$adataim['aktualis_regio']) kilep();
}

$er=mysql_query('select tulaj from bolygok where id='.$_REQUEST['bolygo_id']);
$bolygo=mysql_fetch_array($er);
if ($bolygo['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Ez nem a te bolygód.']);

$er=mysql_query('select tozsdezheto from eroforrasok where id='.$_REQUEST['termek_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux[0]==0) kilep($lang[$lang_lang]['kisphpk']['Ezzel az erőforrással nem lehet kereskedni.']);

//arkorlatozas
$er_arf=mysql_query('select arfolyam from tozsdei_arfolyamok where termek_id='.$_REQUEST['termek_id'].' and regio='.$regio);
$aux_arf=mysql_fetch_array($er_arf);
$arfolyam=$aux_arf[0];

$datum=date('Y-m-d H:i:s');

if ($_REQUEST['vetel']==1) {/************************** VETEL ***********************************/

if ($mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Csak pozitív mennyiséget tudsz venni.']);

$veteli_hiba='';
//LOCK ELEJE
mysql_query('lock tables bolygo_eroforras write, userek write, user_veteli_limit write');
	//mennyisegi korlat
	$napi_limit=mysql2row('select maximum,felhasznalt from user_veteli_limit where user_id='.$uid.' and termek_id='.$_REQUEST['termek_id']);
	if ($napi_limit[1]+$mennyiseg>$napi_limit[0]) $mennyiseg=$napi_limit[0]-$napi_limit[1];
	if ($mennyiseg<=0) $veteli_hiba='Felhasználtad a napi limitedet.';
	if ($veteli_hiba=='') {
		$er=mysql_query('select vagyon from userek where id='.$uid);
		$aux=mysql_fetch_array($er);
		if ($aux[0]<$mennyiseg*$arfolyam) $mennyiseg=floor($aux[0]/$arfolyam);
		if ($mennyiseg>0) {
			if ($_REQUEST['termek_id']<150) {//nyersi
				mysql_query('update bolygo_eroforras set db=db+'.$mennyiseg.' where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id='.$_REQUEST['termek_id']);
			} else {//KP
				mysql_query('update userek set kp=kp+'.$mennyiseg.' where id='.$uid);
			}
			mysql_query('update userek set vagyon=if(vagyon>'.($mennyiseg*$arfolyam).',vagyon-'.($mennyiseg*$arfolyam).',0) where id='.$uid);
		}
		mysql_query('update user_veteli_limit set felhasznalt=least(felhasznalt+'.$mennyiseg.',maximum) where user_id='.$uid.' and termek_id='.$_REQUEST['termek_id']);
	}
mysql_query('unlock tables');
//LOCK VEGE
if (strlen($veteli_hiba)) kilep($lang[$lang_lang]['kisphpk'][$veteli_hiba]);


if ($mennyiseg>0) {
	$vevo_bolygo_id=$_REQUEST['bolygo_id'];
	$elado_bolygo_id=0;
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into tozsdei_kotesek (vevo,vevo_tulaj_szov,elado,elado_tulaj_szov,regio,termek_id,mennyiseg,arfolyam,mikor,vevo_bolygo_id,elado_bolygo_id) values('.$uid.','.$adataim['tulaj_szov'].',0,0,'.$regio.','.$_REQUEST['termek_id'].','.$mennyiseg.','.$arfolyam.',"'.$datum.'",'.$vevo_bolygo_id.','.$elado_bolygo_id.')');
	mysql_select_db($database_mmog);
}

} else {/************************** ELADAS ***********************************/

if ($mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Csak pozitív mennyiséget tudsz eladni.']);

$eladasi_hiba='';
//LOCK ELEJE
mysql_query('lock tables eroforrasok read, bolygo_eroforras write, userek write');//hogy ne lehessen ugyanazt a cuccot tobbszor eladni, vagy ugyanazt a TT-t tobbszor felhasznalni
	//van-e eleg eladnivalo
	if ($_REQUEST['termek_id']<150) {//nyersi
		$er=mysql_query('select db from bolygo_eroforras where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id='.$_REQUEST['termek_id']);
	} else {//KP
		$er=mysql_query('select megoszthato_kp from userek where id='.$uid);
	}
	$aux=mysql_fetch_array($er);
	if ($mennyiseg>$aux[0]) $mennyiseg=$aux[0];
	if ($mennyiseg<=0) $eladasi_hiba='Nincs semmi eladnivalód.';
	else {
		//van-e eleg toltes
		$toltes=mysql2num('select db from bolygo_eroforras where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id=78');
		$savszel_igeny=mysql2num('select savszel_igeny from eroforrasok where id='.$_REQUEST['termek_id']);
		if ($savszel_igeny*$toltes<$mennyiseg) $mennyiseg=$savszel_igeny*$toltes;
		$delta_toltes=ceil($mennyiseg/$savszel_igeny);
		if ($mennyiseg<=0) $eladasi_hiba='Nincs elég teleporttöltésed.';
		else {
			mysql_query('update userek set vagyon=vagyon+'.($mennyiseg*$arfolyam).' where id='.$uid);
			if ($_REQUEST['termek_id']<150) {//nyersi
				mysql_query('update bolygo_eroforras set db=if(db-'.$mennyiseg.'>0,db-'.$mennyiseg.',0) where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id='.$_REQUEST['termek_id']);
			} else {//KP
				mysql_query('update userek set megoszthato_kp=if(megoszthato_kp-'.$mennyiseg.'>0,megoszthato_kp-'.$mennyiseg.',0) where id='.$uid);
			}
			mysql_query('update bolygo_eroforras set db=if(db-'.$delta_toltes.'>0,db-'.$delta_toltes.',0) where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id=78');
		}
	}
mysql_query('unlock tables');
//LOCK VEGE
if (strlen($eladasi_hiba)) kilep($lang[$lang_lang]['kisphpk'][$eladasi_hiba]);
if ($mennyiseg>0) {
	$vevo_bolygo_id=0;
	$elado_bolygo_id=$_REQUEST['bolygo_id'];
	mysql_select_db($database_mmog_nemlog);
	mysql_query('insert into tozsdei_kotesek (vevo,vevo_tulaj_szov,elado,elado_tulaj_szov,regio,termek_id,mennyiseg,arfolyam,mikor,vevo_bolygo_id,elado_bolygo_id) values(0,0,'.$uid.','.$adataim['tulaj_szov'].','.$regio.','.$_REQUEST['termek_id'].','.$mennyiseg.','.$arfolyam.',"'.$datum.'",'.$vevo_bolygo_id.','.$elado_bolygo_id.')');
	mysql_select_db($database_mmog);
}

}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>