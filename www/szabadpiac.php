<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];

$res=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id'].' and letezik=1 and tulaj='.$uid);
$bolygo=mysql_fetch_array($res);
if (!$bolygo) kilep();
?>
/*{"vagyon":<?
echo $adataim['vagyon'];
?>,"premium":<?
echo premium_szint();
?>,"speki":<?
if ($adataim['karrier']==1 && $adataim['speci']==3) echo '1';else echo '0';
?>,"piacok":<?
echo mysql2jsonmatrix('select ta.termek_id,min(ta.arfolyam),min(sza.arfolyam),2*max(ta.arfolyam) from tozsdei_arfolyamok ta, szabadpiaci_arfolyamok sza where ta.termek_id=sza.termek_id group by ta.termek_id');
?>,"keszletek":<?
echo mysql2jsonassoc('
select e.id,be.db,-1 from eroforrasok e, bolygo_eroforras be
where be.bolygo_id='.$_REQUEST['bolygo_id'].' and be.eroforras_id=e.id and e.szallithato=1
union all
select 150,megoszthato_kp,kp from userek where id='.$uid.'
');
?>,"legjobb_ajanlatok":<?
echo mysql2jsonassoc('
select ta.termek_id,max(if(ta.vetel=1,ta.arfolyam,0)) as veteli_ar,sum(if(ta.vetel=1,ta.mennyiseg,0)) as veteli_mennyiseg
,max(if(ta.vetel=0,ta.arfolyam,0)) as eladasi_ar,sum(if(ta.vetel=0,ta.mennyiseg,0)) as eladasi_mennyiseg
from szabadpiaci_ajanlatok ta,(
select termek_id,max(if(vetel=1,arfolyam,null)) as veteli,min(if(vetel=0,arfolyam,null)) as eladasi from szabadpiaci_ajanlatok group by termek_id
) arak
where arak.termek_id=ta.termek_id
and (
(ta.vetel=1 and ta.arfolyam=arak.veteli) or
(ta.vetel=0 and ta.arfolyam=arak.eladasi)
)
group by ta.termek_id');
?>,"sajat_ajanlatok":<?
echo mysql2jsonmultiassoc('select termek_id,id,vetel,arfolyam,mennyiseg from szabadpiaci_ajanlatok where user_id='.$uid.' and bolygo_id='.$_REQUEST['bolygo_id'].' order by termek_id,arfolyam');
//bolygo_idx???


?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>