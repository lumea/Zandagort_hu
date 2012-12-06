<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$bolygo=mysql2row('select * from bolygok where id='.$_REQUEST['id'].' and letezik=1');
if (!$bolygo) {echo '{"letezik":0}';exit;}

$szereped='tulaj';if ($bolygo['kezelo']==$uid) $szereped='kezelo';

$bolygo_tulaj=mysql2row('select * from userek where id='.$bolygo['tulaj']);
if ($bolygo['tulaj']!=$uid) {
	if ($bolygo_tulaj['karrier']==3 && $bolygo_tulaj['speci']==3) {//fantom
		$bolygo_tulaj['nev']='-';
		$bolygo['tulaj']=0;
		$bolygo['tulaj_szov']=0;
		$bolygo['szovetseg']=0;
		$bolygo['nev']=$bolygo['kulso_nev'];
		$bolygo['alapbol_regisztralhato']=0;
		$bolygo['vedelmi_bonusz']=0;
	}
}

?>{"letezik":1,"id":<?=$bolygo['id'];?>,"nev":<?=json_encode($bolygo['nev']);




?>,"te":<?=$uid?>,"tied":<?=($bolygo[$szereped]==$uid)?1:0;
?>,"tulaj_nev":<?
if ($bolygo_tulaj) {
	echo json_encode($bolygo_tulaj['nev']);
} else echo '"-"';
?>,"tulaj_id":<?=$bolygo['tulaj'];
?>,"tulaj_szov":<?=$bolygo['tulaj_szov'];
?>,"szovetseg_nev":<?
if ($bolygo['tulaj_szov']>0) {
	$szov=mysql2row('select nev from szovetsegek where id='.$bolygo['tulaj_szov']);
	echo json_encode($szov['nev']);
} else echo '"-"';


?>,"premium":<?
echo premium_szint();
?>,"x":<?
echo $bolygo['x'];
?>,"y":<?
echo $bolygo['y'];
?>,"hexa_x":<?
echo $bolygo['hexa_x'];
?>,"hexa_y":<?
echo $bolygo['hexa_y'];
?>,"bolygokepmeret":<?
echo round($bolygo['terulet']/2000000);

?>,"regio":<?
$aux2=mysql2row('select nev from regiok where id='.$bolygo['regio']);
if ($aux2[0]) echo json_encode($aux2[0]);else echo '"-"';
?>,"osztaly":<?
echo $bolygo['osztaly'];
?>,"terulet":<?
echo round($bolygo['terulet']/1000000);
?>,"terulet_foglalt":<?
echo $bolygo['terulet_beepitett'];
?>,"terulet_foglalt_effektiv":<?
echo $bolygo['terulet_beepitett_effektiv'];
?>,"kornyezeti_fejlettseg":<?
if ($bolygo['terraformaltsag']>0) echo round(10000/$bolygo['terraformaltsag']*10000);else echo 10000;
?>,"hold":<?
echo $bolygo['hold'];
?>,"alapbol_regisztralhato":<?
echo (int)$bolygo['alapbol_regisztralhato'];
?>,"random_regisztralhato":<?
echo (int)$bolygo['random_regisztralhato'];



?>,"vedelmi_bonusz":<?
echo $bolygo['vedelmi_bonusz'];
?>,"foszthato":<?
if ($bolygo['tulaj']>0) {
	if ($bolygo['vedelmi_bonusz']<1000) echo '1';
	else echo '0';
} else echo '-1';
?>,"moratorium":<?
$morat=round((strtotime($bolygo['moratorium_mikor_jar_le'])-time())/60);
if ($morat<=0) echo '0';else echo $morat;
?>,"szabot":<?
if ($bolygo_tulaj['uccso_szabotazs_mikor']>date('Y-m-d H:i:s',time()-3600*24*7)) echo '"'.date('Y-m-d H:i:s',strtotime($bolygo_tulaj['uccso_szabotazs_mikor'])+3600*24*7).'"';
else echo '"-"';


?>,"koltozheto":<?
$koltozheto=1;
if ($bolygo['tulaj']!=0) $koltozheto=0;
if ($bolygo['alapbol_regisztralhato']!=1) $koltozheto=0;
if ($adataim['techszint']>3) $koltozheto=0;

if ($koltozheto) {
	$bolygoim_szama=mysql2num('select count(1) from bolygok where tulaj='.$uid);
	if ($bolygoim_szama!=1) $koltozheto=0;
}
if ($koltozheto) {
	$sajat_bolygo=mysql2row('select * from bolygok where tulaj='.$uid);
	if ($bolygo['osztaly']!=$sajat_bolygo['osztaly']) $koltozheto=0;
	if ($bolygo['terulet']!=$sajat_bolygo['terulet']) $koltozheto=0;
	if ($bolygo['hold']!=$sajat_bolygo['hold']) $koltozheto=0;
}
echo $koltozheto;



if ($bolygo[$szereped]==$uid) {
/******************************************** SAJAT BOLYGO ********************************************************/
?>,"gyarak":<?
echo mysql2jsonassoc_v2('select gyt.id,coalesce(sum(bgy.db),0)
from bolygo_gyar bgy, gyarak gy, gyartipusok gyt
where bgy.bolygo_id='.$bolygo['id'].' and bgy.gyar_id=gy.id and gyt.id=gy.tipus
group by gyt.id');

?>,"eroforrasok":<?
echo mysql2jsonassoc_v2('select e.id,be.db,be.delta_db
from bolygo_eroforras be, eroforrasok e
where be.bolygo_id='.$bolygo['id'].' and be.eroforras_id=e.id and e.tipus not in (1,3)');


} else {
/******************************************** IDEGEN BOLYGO ********************************************************/


}

?>}