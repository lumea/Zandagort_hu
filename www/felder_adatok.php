<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"ugynokszam":<?
echo $adataim['ugynokok_szama'];
?>,"ugynokkapacitas":<?
echo $adataim['ugynok_kapacitas'];
?>,"eloszlas":<?
if ($adataim['ugynokok_szama']>0) {
echo mysql2jsonassoc('select concat("s",statusz),sum(darab),round(sum(darab)/'.$adataim['ugynokok_szama'].'*100) from ugynokcsoportok where tulaj='.$uid.' group by statusz');
} else {
echo '[]';
}
?>,"vagyon":<?
echo $adataim['vagyon'];
?>,"fogyasztas":<?
echo mysql2num('select coalesce(sum(shy_per_akcio),0) from ugynokcsoportok where tulaj='.$uid.' and statusz!=0 and bolygo_id!=0');//csak azok fogyasztanak, amik mar bolygon vannak
?>,"csoportok":<?
echo mysql2jsonmultiassoc('select ucs.bolygo_id
,ucs.id,ucs.darab,ucs.statusz
,ucs.bolygo_id,coalesce(b.kulso_nev,"") as bolygo_nev,ucs.x,ucs.y
,ucs.cel_bolygo_id,coalesce(cb.kulso_nev,"") as cel_bolygo_nev,coalesce(cb.osztaly,0)
,coalesce(ceil(sqrt(pow(cb.x-ucs.x,2)+pow(cb.y-ucs.y,2))/200),0) as perc
,b.osztaly
,ucs.hanyszor,ucs.shy_per_akcio
,ucs.feladat_domen,ucs.feladat_id
from ugynokcsoportok ucs
left join bolygok b on b.id=ucs.bolygo_id
left join bolygok cb on cb.id=ucs.cel_bolygo_id
where ucs.tulaj='.$uid.'
order by if(bolygo_id=0,1,0),if(b.tulaj='.$uid.',1,2),if(b.tulaj_szov='.$adataim['tulaj_szov'].',1,2),b.id,ucs.statusz,ucs.id');
?>}*/
<?

?>
<? mysql_close($mysql_csatlakozas);?>