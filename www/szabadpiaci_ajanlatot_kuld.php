<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($_REQUEST['vetel']!=1) if ($adataim['karrier']!=1 || $adataim['speci']!=3) kilep();

$_REQUEST['termek_id']=(int)$_REQUEST['termek_id'];
$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$mennyiseg=sanitint($_REQUEST['mennyiseg']);
$arfolyam=sanitint($_REQUEST['arfolyam']);
$_REQUEST['vetel']=(int)$_REQUEST['vetel'];

$er=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']);
$bolygo=mysql_fetch_array($er);
if ($bolygo['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Ez nem a te bolygód.']);

$er=mysql_query('select tozsdezheto from eroforrasok where id='.$_REQUEST['termek_id']);
$aux=mysql_fetch_array($er);
if ($aux[0]==0) kilep($lang[$lang_lang]['kisphpk']['Ezzel az erőforrással nem lehet kereskedni.']);

//arkorlatozas
if ($arfolyam<=0) kilep();
$regios_arak=mysql2row('select min(arfolyam),max(arfolyam) from tozsdei_arfolyamok where termek_id='.$_REQUEST['termek_id']);
if ($arfolyam<$regios_arak[0]) kilep($lang[$lang_lang]['kisphpk']['A minimális ár a legalacsonyabb régiós ár.']);
if ($arfolyam>2*$regios_arak[1]) kilep($lang[$lang_lang]['kisphpk']['A maximális ár a legmagasabb régiós ár duplája.']);


$datum=date('Y-m-d H:i:s');

if ($_REQUEST['vetel']==1) {/************************** VETEL ***********************************/

if ($mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Csak pozitív mennyiséget tudsz venni.']);

//LOCK ELEJE
mysql_query('lock tables userek write, szabadpiaci_ajanlatok write');
	$vagyon=mysql2num('select vagyon from userek where id='.$uid);
	if ($vagyon<$mennyiseg*$arfolyam) $mennyiseg=floor($vagyon/$arfolyam);
	if ($mennyiseg>0) {
		mysql_query('insert into szabadpiaci_ajanlatok (termek_id,user_id,bolygo_id,mennyiseg,arfolyam,vetel,mikor) values('.$_REQUEST['termek_id'].','.$uid.','.$bolygo['id'].','.$mennyiseg.','.$arfolyam.',1,"'.$datum.'")');
		mysql_query('update userek set vagyon=if(vagyon>'.($mennyiseg*$arfolyam).',vagyon-'.($mennyiseg*$arfolyam).',0) where id='.$uid);
	}
mysql_query('unlock tables');
//LOCK VEGE

if ($mennyiseg>0) {
	szabadpiac_tisztit($_REQUEST['termek_id']);
}

} else {/************************** ELADAS ***********************************/

if ($mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Csak pozitív mennyiséget tudsz eladni.']);

$eladasi_hiba='';
//LOCK ELEJE

mysql_query('lock tables eroforrasok read, bolygo_eroforras write, userek write, szabadpiaci_ajanlatok write');
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
			mysql_query('insert into szabadpiaci_ajanlatok (termek_id,user_id,bolygo_id,mennyiseg,arfolyam,vetel,mikor) values('.$_REQUEST['termek_id'].','.$uid.','.$bolygo['id'].','.$mennyiseg.','.$arfolyam.',0,"'.$datum.'")');
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
	szabadpiac_tisztit($_REQUEST['termek_id']);
}

}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>