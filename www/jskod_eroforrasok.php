<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
var kemkedheto_eroforrasok_neve=new Array();
<?
$r=mysql_query('select id,nev'.$lang__lang.' from eroforrasok where tipus=2 order by id');
while ($aux=mysql_fetch_array($r)) echo "kemkedheto_eroforrasok_neve[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var szallithato_eroforrasok=new Array();
<?
$r=mysql_query('select id,nev'.$lang__lang.' from eroforrasok where szallithato=1');
while ($aux=mysql_fetch_array($r)) echo "szallithato_eroforrasok.push([".$aux[0].",'".htmlspecialchars($aux[1],ENT_QUOTES)."']);";
?>
var eroforrasok_neve=new Array();
<?
$r=mysql_query('select id,nev'.$lang__lang.' from eroforrasok') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "eroforrasok_neve[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var eroforrasok_mertekegysege=new Array();
<?
$r=mysql_query('select id,mertekegyseg'.$lang__lang.' from eroforrasok') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "eroforrasok_mertekegysege[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var eroforrasok_savszele=new Array();
<?
$r=mysql_query('select id,savszel_igeny from eroforrasok') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "eroforrasok_savszele[".$aux[0]."]=".((int)$aux[1]).";";
?>
var eroforrasok_fajlneve=new Array();
<?
$r=mysql_query('select id,kep from leirasok where domen=2') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "eroforrasok_fajlneve[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var epuletek_fajlneve=new Array();
<?
$r=mysql_query('select id,kep from leirasok where domen=1') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "epuletek_fajlneve[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var epuletek_neve=new Array();
<?
$r=mysql_query('select id,nev'.$lang__lang.' from gyartipusok') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) echo "epuletek_neve[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
?>
var epuletek_gyartasi_koltsege=new Array();
var epuletek_gyartasi_ideje=new Array();
var epuletek_gyartasi_koltsege_tomb=new Array();
<?
$r=mysql_query('
select gyt.id,
group_concat(if(gyek.db>=1000,if(round(gyek.db/1000)*1000=gyek.db,concat(round(gyek.db/1000),"k"),concat(round(gyek.db/1000,1),"k")),gyek.db)," ",e.nev'.$lang__lang.' order by e.id separator ", "),
round(avg(gyei.ido)),
group_concat(concat(e.id,":",gyek.db) order by e.id)
from gyartipusok gyt, gyar_epitesi_koltseg gyek, gyar_epitesi_ido gyei, eroforrasok e
where gyt.id=gyek.tipus and gyek.szint=1 and gyek.eroforras_id=e.id
and gyei.tipus=gyt.id and gyei.szint=1
group by gyt.id
') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) {
	if ($lang_lang=='hu') {
		echo "epuletek_gyartasi_koltsege[".$aux[0]."]='".htmlspecialchars(strtr($aux[1],'.',','),ENT_QUOTES)."';";
	} else {
		echo "epuletek_gyartasi_koltsege[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
	}
	if ($adataim['karrier']==1 && $adataim['speci']==1) if (in_array($aux[0],$mernok_8_oras_gyarai)) $aux[2]=480;
	echo "epuletek_gyartasi_ideje[".$aux[0]."]=".((int)$aux[2]).";";
	echo "epuletek_gyartasi_koltsege_tomb[".$aux[0]."]={".$aux[3]."};";
}
?>
var gyarak_inputja=new Array();
var gyarak_outputja=new Array();
<?
$r=mysql_query('
select gye.gyar_id,
group_concat(if(gye.io<0,concat(if(-gye.io>=1000,if(round(gye.io/1000)*1000=gye.io,concat(round(-gye.io/1000),"k"),concat(if(round(gye.io/100)*100=gye.io,round(-gye.io/1000,1),round(-gye.io/1000,2)),"k")),-gye.io)," ",e.nev'.$lang__lang.'),null) order by e.id separator ", "),
group_concat(if(gye.io>0,concat(if(gye.io>=1000,if(round(gye.io/1000)*1000=gye.io,concat(round(gye.io/1000),"k"),concat(if(round(gye.io/100)*100=gye.io,round(gye.io/1000,1),round(gye.io/1000,2)),"k")),if(e.tipus=3,round(gye.io/100,2),gye.io))," ",e.nev'.$lang__lang.'),null) order by e.id separator ", ")
from gyar_eroforras gye, eroforrasok e
where gye.eroforras_id=e.id and e.tipus<>'.EROFORRAS_TIPUS_REJTETT.'
group by gye.gyar_id
') or hiba(__FILE__,__LINE__,mysql_error());
while ($aux=mysql_fetch_array($r)) {
	if ($lang_lang=='hu') {
		echo "gyarak_inputja[".$aux[0]."]='".htmlspecialchars(strtr($aux[1],'.',','),ENT_QUOTES)."';";
		echo "gyarak_outputja[".$aux[0]."]='".htmlspecialchars(strtr($aux[2],'.',','),ENT_QUOTES)."';";
	} else {
		echo "gyarak_inputja[".$aux[0]."]='".htmlspecialchars($aux[1],ENT_QUOTES)."';";
		echo "gyarak_outputja[".$aux[0]."]='".htmlspecialchars($aux[2],ENT_QUOTES)."';";
	}
}
?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>