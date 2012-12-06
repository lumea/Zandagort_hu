<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['gyar_id']=(int)$_REQUEST['gyar_id'];
$_REQUEST['a']=(int)$_REQUEST['a'];
$darab=(int)$_REQUEST['db'];if ($darab<0) $darab=-$darab;//ha veletlenul beut egy minusz jelet
if ($darab<=0) kilep();

$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$_REQUEST['bolygo_id'].' and gyar_id='.$_REQUEST['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen épület.']);
if ($darab>$aux['db']) $darab=$aux['db'];
if ($darab==0) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen épület.']);

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$er=mysql_query('select * from gyarak where id='.$_REQUEST['gyar_id']) or hiba(__FILE__,__LINE__,mysql_error());
$gyar=mysql_fetch_array($er);
$er4=mysql_query('select * from gyar_epitesi_ido where tipus='.$gyar['tipus'].' and szint='.$gyar['szint']);
$aux4=mysql_fetch_array($er4);
if ($adataim['karrier']==1 && $adataim['speci']==1) if (in_array($aux4['tipus'],$mernok_8_oras_gyarai)) $aux4['ido']=480;

$teljes_ipar=mysql2num('select sum(bgy.db*gyt.pontertek) as pontertek
from bolygo_gyar bgy, gyarak gy, gyartipusok gyt
where bgy.bolygo_id='.$_REQUEST['bolygo_id'].' and bgy.gyar_id=gy.id and gy.tipus=gyt.id');

$ez_a_bontas=mysql2num('select pontertek from gyartipusok where id='.$gyar['tipus'])*$darab;

if ($teljes_ipar>0) {
	$moralcsokkenes=ceil($ez_a_bontas/$teljes_ipar*100*2);
	mysql_query("update bolygok set moral=if(moral>$moralcsokkenes,moral-$moralcsokkenes,0) where id=".$_REQUEST['bolygo_id']);
}


//keszrol lerombolas!!!
regi_gyar_lerombolasa_lassan($_REQUEST['bolygo_id'],$_REQUEST['gyar_id'],$_REQUEST['a'],$darab,6*$aux4['ido']);//6*aux=10%*60*aux (perc->mp konverzio)

bolygo_terulet_frissites($_REQUEST['bolygo_id']);

kilep();
?>