<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"bolygok":<?
echo mysql2jsonmatrix('
select id,nev,osztaly,1 as tied,if(timestampdiff(minute,now(),moratorium_mikor_jar_le)>-5,if(timestampdiff(minute,uccso_foglalas_mikor,now())<70,0,1),0) as utik,moral from bolygok where tulaj='.$uid.' and letezik=1
union all
select id,nev,osztaly,0 as tied,if(timestampdiff(minute,now(),moratorium_mikor_jar_le)>-5,if(timestampdiff(minute,uccso_foglalas_mikor,now())<70,0,1),0) as utik,moral from bolygok where kezelo='.$uid.' and letezik=1
order by tied desc,nev,id');

?>,"flottak":<?
if ($user_beallitasok['kozos_flottak_listaban']>0) {
echo mysql2jsonmatrix('
select id,nev,1 as diplo,1 as tied,0,kozos from flottak where tulaj='.$uid.'
union all
(select f.id,f.nev,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo,0 as tied,0,f.kozos
from flottak f
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1)
order by tied desc,nev,id');
} else {
echo mysql2jsonmatrix('
select id,nev,1 as diplo,1 as tied,0,kozos from flottak where tulaj='.$uid.'
order by tied desc,nev,id');
}

?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>