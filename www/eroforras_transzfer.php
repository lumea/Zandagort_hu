<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['forras_id']=(int)$_REQUEST['forras_id'];
$_REQUEST['cel_id']=(int)$_REQUEST['cel_id'];

$ossz_mennyiseg=0;
$eroforrasok=explode(',',$_REQUEST['mennyisegek']);
for($i=0;$i<count($eroforrasok)-1;$i++) {
	$eroforras=explode(':',$eroforrasok[$i]);
	$ossz_mennyiseg+=sanitint($eroforras[1]);
}
if ($ossz_mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Válassz ki legalább egy adag erőforrást.']);

$er=mysql_query('select * from bolygok where id='.$_REQUEST['forras_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$forras_bolygo=$aux;
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);
$forras_orig_tulaj=$aux['uccso_emberi_tulaj'];$forras_orig_tulaj_szov=$aux['uccso_emberi_tulaj_szov'];

if ($_REQUEST['cel_id']>0) {
	$er=mysql_query('select * from bolygok where id='.$_REQUEST['cel_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$_REQUEST['cel_nev']=sanitstr($_REQUEST['cel_nev']);
	$er=mysql_query('select * from bolygok where letezik=1 and concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.$_REQUEST['cel_nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
}
$aux=mysql_fetch_array($er);$_REQUEST['cel_id']=$aux['id'];
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj']!=$forras_bolygo['tulaj']) kilep($lang[$lang_lang]['kisphpk']['Csak saját bolygóra tudsz küldeni.']);
$cel_uid=$aux['tulaj'];$cel_tulaj_szov=$aux['tulaj_szov'];


if ($_REQUEST['forras_id']==$_REQUEST['cel_id']) kilep($lang[$lang_lang]['kisphpk']['Csak két különböző bolygó között tudsz szállítani.']);


$datum=date('Y-m-d H:i:s');

$eroforrasok=explode(',',$_REQUEST['mennyisegek']);
$atment=0;$nem_ment_at=0;


//LOCK ELEJE
mysql_query('lock tables '.$database_mmog_nemlog.'.transzfer_log write, bolygo_eroforras write, bolygo_eroforras be read, eroforrasok e read');
$toltes=mysql2num('select db from bolygo_eroforras where bolygo_id='.$_REQUEST['forras_id'].' and eroforras_id=78');
for($i=0;$i<count($eroforrasok)-1;$i++) {
	$eroforras=explode(':',$eroforrasok[$i]);
	$ef_id=(int)$eroforras[0];
	$mennyiseg=sanitint($eroforras[1]);
	$er=mysql_query('select be.db,e.savszel_igeny from bolygo_eroforras be,eroforrasok e where be.bolygo_id='.$_REQUEST['forras_id'].' and be.eroforras_id='.$ef_id.' and e.id='.$ef_id.' and e.szallithato=1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]<$mennyiseg) $mennyiseg=$aux[0];
	$nem_ment_at+=$mennyiseg;
	if ($aux[1]*$toltes<$mennyiseg) $mennyiseg=$aux[1]*$toltes;
	$atment+=$mennyiseg;
	$nem_ment_at-=$mennyiseg;
	if ($mennyiseg>0) {
		mysql_query('update bolygo_eroforras set db=if(db-'.$mennyiseg.'<0,0,db-'.$mennyiseg.') where bolygo_id='.$_REQUEST['forras_id'].' and eroforras_id='.$ef_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update bolygo_eroforras set db=db+'.$mennyiseg.' where bolygo_id='.$_REQUEST['cel_id'].' and eroforras_id='.$ef_id) or hiba(__FILE__,__LINE__,mysql_error());
		//toltes
		$delta_toltes=ceil($mennyiseg/$aux[1]);
		mysql_query('update bolygo_eroforras set db=if(db-'.$delta_toltes.'<0,0,db-'.$delta_toltes.') where bolygo_id='.$_REQUEST['forras_id'].' and eroforras_id=78') or hiba(__FILE__,__LINE__,mysql_error());
		$toltes-=$delta_toltes;
		insert_into_transzfer_log($forras_orig_tulaj,$forras_orig_tulaj_szov,$forras_bolygo['tulaj'],$forras_bolygo['tulaj_szov'],$_REQUEST['forras_id'],$cel_uid,$cel_tulaj_szov,$_REQUEST['cel_id'],$ef_id,$mennyiseg,0);
	}
}
mysql_query('unlock tables');
//LOCK VEGE


if ($nem_ment_at>0) {
	if ($atment>0) kilep('***'.$lang[$lang_lang]['kisphpk']['A megbízás csak '].round($atment/($atment+$nem_ment_at)*100).$lang[$lang_lang]['kisphpk']['%-ban tudott teljesülni.']);
	else kilep('***'.$lang[$lang_lang]['kisphpk']['A megbízás nem tudott teljesülni.']);
}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>