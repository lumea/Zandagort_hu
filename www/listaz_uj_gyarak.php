<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$iparag_nevek=array('','Erőművek','Élelmiszeripar','Kitermelés','Feldolgozó ipar','Települések','Speciális épületek','Hadiipar');

$_REQUEST['bid']=(int)$_REQUEST['bid'];
$res=mysql_query('select * from bolygok where id='.$_REQUEST['bid']) or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($res);

if ($bolygo['id']) if ($bolygo['tulaj']==$uid) {
?>
/*{"oke":1,"premium":<?=premium_szint();?>,"osztaly":<?=$bolygo['osztaly'];?>,"lista":[<?

$jj=0;$iparag=0;
$er_gy=mysql_query('
select gy.*,gyt.iparag,gyt.nev'.$lang__lang.' as gyar_tipus_nev,gyt.uzemmod_szam_'.$bolygo['osztaly'].' as uzemmod_szam,e.tipus as eroforras_tipus,e.bolygo_osztaly,e.bolygo_osztaly as eroforras_bo,l.kep
from gyartipusok gyt,gyarak gy,eroforrasok e,leirasok l
where gyt.id=gy.tipus and gy.szint=1 and gy.uzemmod=e.id and l.domen=1 and l.id=gyt.id
order by gyt.iparag_sorszam,gyt.id,gy.uzemmod') or hiba(__FILE__,__LINE__,mysql_error());
while($gyar=mysql_fetch_array($er_gy)) {
	$mehet=0;
	$lehet=1;
	if ($gyar['eroforras_tipus']==EROFORRAS_TIPUS_FAJ) {
		if ($gyar['eroforras_bo']&pow(2,$bolygo['osztaly']-1)) $lehet=1;else $lehet=0;
	}
	$er2=mysql_query('
select sum(if(uksz.szint>=gyksz.szint,0,1))
from gyar_kutatasi_szint gyksz, user_kutatasi_szint uksz
where gyksz.gyar_id='.$gyar['id'].' and gyksz.kf_id=uksz.kf_id and uksz.user_id='.$uid.'
') or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($er2);
	if ($aux2===false) $mehet=$lehet;
	elseif ($aux2[0]==0) $mehet=$lehet;
	switch($gyar['tipus']) {
		case 1:if ($bolygo['osztaly']!=2) $mehet=0;//nap
		break;
		case 5:if ($bolygo['osztaly']!=1 && $bolygo['osztaly']!=4) $mehet=0;//viz
		break;
		case 6:if ($bolygo['osztaly']<3) $mehet=0;//hullam
		break;
		case 7:if ($bolygo['osztaly']<3 || $bolygo['hold']==0) $mehet=0;//arapaly
		break;
		case 8:if ($bolygo['osztaly']!=4) $mehet=0;//ozmozis
		break;
		case 9:if ($bolygo['osztaly']!=5) $mehet=0;//geoterm
		break;
		case 11:if ($bolygo['osztaly']==5) $mehet=0;//bioetanol
		break;
	}
	if ($mehet) {
		if ($iparag==0 || $iparag!=$gyar['iparag']) {
			$ii=0;$iparag=$gyar['iparag'];
			$jj++;if ($jj>1) echo ']},';
			echo '{"iparag":"'.$lang[$lang_lang]['iparagak'][$iparag_nevek[$iparag]].'","gyarak":[';
		}
		$ii++;if ($ii>1) echo ',';
		if ($gyar['uzemmod_szam']>1) $x=$gyar['uzemmod'];else $x=0;
		echo '['.$gyar['tipus'].','.$gyar['id'].',"'.$gyar['gyar_tipus_nev'].'",'.$x.',"'.$gyar['kep'].'"]';
	}
}
if ($jj>0) echo ']}';

?>]}*/
<?
} else {
?>
/*{"oke":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>