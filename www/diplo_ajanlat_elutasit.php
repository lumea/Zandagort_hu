<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']>0) {
	$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($res2);
	if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
	if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_7']) kilep($lang[$lang_lang]['kisphpk']['Nincs diplomáciai jogod.']);
	$ki_vagy_id=$adataim['szovetseg'];
} else $ki_vagy_id=-$uid;

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from diplomacia_ajanlatok where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$ajanlat=mysql_fetch_array($er);
if (!$ajanlat) kilep();
if ($ajanlat['kinek']!=$ki_vagy_id) kilep($lang[$lang_lang]['kisphpk']['Ezt az ajánlatot másnak küldték.']);

if ($ki_vagy_id>0) {
	$er=mysql_query('select nev from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);$nev=$aux[0];
} else $nev=$adataim['nev'];

if ($ajanlat['ki']>0) diplouzenet($ajanlat['ki'],'Ajánlat elutasítása',"$nev elutasította szövetséged ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatát.",'Offer rejected',"$nev rejected the offer of ".$diplo_ajanlatok_en[$ajanlat['mit']]." of your alliance.");
else diplouzenet($ajanlat['ki'],'Ajánlat elutasítása',"$nev elutasította ".$diplo_ajanlatok[$ajanlat['mit']]." ajánlatodat.",'Offer rejected',"$nev rejected your offer of ".$diplo_ajanlatok_en[$ajanlat['mit']].".");
mysql_query('delete from diplomacia_ajanlatok where id='.$ajanlat['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_szovegek where id='.$ajanlat['szoveg_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>