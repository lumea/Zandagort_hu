<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

ob_start('ob_gzhandler');//gzip if browser supports

insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'start');

$radarjog=$jogaim[10];
$nagyradarjog=$jogaim[11];

$_REQUEST['minx']=(int)$_REQUEST['minx'];
$_REQUEST['maxx']=(int)$_REQUEST['maxx'];if ($_REQUEST['maxx']-$_REQUEST['minx']>11456) $_REQUEST['maxx']=11456+$_REQUEST['minx'];
$_REQUEST['miny']=(int)$_REQUEST['miny'];
$_REQUEST['maxy']=(int)$_REQUEST['maxy'];if ($_REQUEST['maxy']-$_REQUEST['miny']>11456) $_REQUEST['maxy']=11456+$_REQUEST['miny'];
$_REQUEST['rminx']=(int)$_REQUEST['rminx'];
$_REQUEST['rmaxx']=(int)$_REQUEST['rmaxx'];if ($_REQUEST['rmaxx']-$_REQUEST['rminx']>11456) $_REQUEST['rmaxx']=11456+$_REQUEST['rminx'];
$_REQUEST['rminy']=(int)$_REQUEST['rminy'];
$_REQUEST['rmaxy']=(int)$_REQUEST['rmaxy'];if ($_REQUEST['rmaxy']-$_REQUEST['rminy']>11456) $_REQUEST['rmaxy']=11456+$_REQUEST['rminy'];

$flotta_hely_feltetel='(f.x>='.$_REQUEST['minx'].' and f.x<='.$_REQUEST['maxx'].' and f.y>='.$_REQUEST['miny'].' and f.y<='.$_REQUEST['maxy'].') or (f.tulaj='.$uid.')';

$hely_feltetel='((b.x>='.$_REQUEST['minx'].' and b.x<='.$_REQUEST['maxx'].' and b.y>='.$_REQUEST['miny'].' and b.y<='.$_REQUEST['maxy'].')';
$hely_feltetel.=' and not (b.x>='.$_REQUEST['rminx'].' and b.x<='.$_REQUEST['rmaxx'].' and b.y>='.$_REQUEST['rminy'].' and b.y<='.$_REQUEST['rmaxy'].'))';

//h.x*round(125*sqrt(3))
//h.y*125*2-if(h.x%2=0,0,125)
//$hexa_hely_feltetel='((h.x*round(125*sqrt(3))>='.$_REQUEST['minx'].' and h.x*round(125*sqrt(3))<='.$_REQUEST['maxx'].' and h.y*125*2-if(h.x%2=0,0,125)>='.$_REQUEST['miny'].' and h.y*125*2-if(h.x%2=0,0,125)<='.$_REQUEST['maxy'].')';
//$hexa_hely_feltetel.=' and not (h.x*round(125*sqrt(3))>='.$_REQUEST['rminx'].' and h.x*round(125*sqrt(3))<='.$_REQUEST['rmaxx'].' and h.y*125*2-if(h.x%2=0,0,125)>='.$_REQUEST['rminy'].' and h.y*125*2-if(h.x%2=0,0,125)<='.$_REQUEST['rmaxy'].'))';

$hexa_hely_feltetel='h.x between '.$_REQUEST['minx'].'/round(125*sqrt(3)) and '.$_REQUEST['maxx'].'/round(125*sqrt(3))';
//$hexa_hely_feltetel.=' and h.y between '.$_REQUEST['miny'].'/250 and ('.$_REQUEST['maxy'].'+125)/250';//a meresek alapjan jobb ezt a "folosleges" indexelheto feltetelt kihagyni
$hexa_hely_feltetel.=' and h.y*125*2-if(h.x%2=0,0,125)>='.$_REQUEST['miny'].' and h.y*125*2-if(h.x%2=0,0,125)<='.$_REQUEST['maxy'];
//$hexa_hely_feltetel.=' and not (h.x between '.$_REQUEST['rminx'].'/round(125*sqrt(3)) and '.$_REQUEST['rmaxx'].'/round(125*sqrt(3))';
//$hexa_hely_feltetel.=' and h.y*125*2-if(h.x%2=0,0,125)>='.$_REQUEST['rminy'].' and h.y*125*2-if(h.x%2=0,0,125)<='.$_REQUEST['rmaxy'].')';

insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'init');

?>{"diplok":<?
echo '{';$i=0;
$er=mysql_query('select mi,group_concat(kivel) from diplomacia_statuszok where ki='.$adataim['tulaj_szov'].' group by mi');
while($aux=mysql_fetch_array($er)) {
$i++;if ($i>1) echo ',';
echo '"'.$aux[0].'":['.$aux[1].']';
}
echo '}';

insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'diplok');

?>,"hexak":<?
echo mysql2jsonlabeledmatrix_v2('select 0,h.x*round(125*sqrt(3)),h.y*125*2-if(h.x%2=0,0,125),h.bolygo_id,h.szomszed_bolygo_id,h.voronoi_bolygo_id
,if(b.tulaj_szov!='.$adataim['tulaj_szov'].' and (coalesce(dsz.mi,0)='.DIPLO_HADI.' or dsz.mi is null),1,0)
,b.tulaj_szov
,if(b.tulaj='.$uid.',1,if(b.tulaj_szov='.$adataim['tulaj_szov'].',2,if(coalesce(dsz.mi,0)='.DIPLO_TESTVER.',3,if(coalesce(dsz.mi,0)='.DIPLO_HADI.',4,0))))
,h.x,h.y
,coalesce(b.kulso_nev,"")
from hexak h
inner join bolygok b on h.voronoi_bolygo_id=b.id
left join userek u on u.id=b.tulaj
left join diplomacia_statuszok dsz on dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=b.tulaj_szov
where b.tulaj!=0
and b.letezik=1
and if(u.id!='.$uid.' and coalesce(u.karrier,0)=3 and coalesce(u.speci,0)=3,0,b.tulaj)!=0
and coalesce(dsz.mi,0)!='.DIPLO_MNT.'
and '.$hexa_hely_feltetel,array('sorszam','x','y','bolygo','szomszed','voronoi','szovi','tulaj_szov','szin','hexa_x','hexa_y','voronoi_nev'));
//szovi = atmeneti mezo, kesobb ki lehet venni
//szin = ez az uj

insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'hexak');

?>,"bolygok":<?
echo mysql2jsonmatrix_v2('select b.id,b.x,b.y,b.kulso_nev as nev,b.terulet,b.osztaly,b.hold,b.vedelmi_bonusz,b.tulaj,b.tulaj_szov
,if(b.tulaj='.$uid.',1,if(b.tulaj_szov='.$adataim['tulaj_szov'].',2,if(coalesce(dsz.mi,0)='.DIPLO_TESTVER.',3,0)))
,if(b.tulaj='.$uid.',1,0),b.tulaj,if(b.tulaj_szov='.$adataim['szovetseg'].',1,0),tulaj_szov
from bolygok b
left join diplomacia_statuszok dsz on dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=b.tulaj_szov
where b.letezik=1 and '.$hely_feltetel);

insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'bolygok');



?>,"flottak":<?

$fog_of_war=1;
if ($uid==1) $fog_of_war=0;

if ($fog_of_war) {

if ($nagyradarjog) {//nagy radarjog (ez automatice sima radarjog is)
echo mysql2jsonlabeledmatrix_v2('
select f.id,f.x,f.y,f.nev,if(f.tulaj='.$uid.',1,0),f.tulaj,if(f.kezelo='.$uid.' or f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1,1,0),f.kezelo,if(f.tulaj_szov='.$adataim['szovetseg'].',1,0),f.tulaj_szov,if(lt.lathatosag=2,round(sum(fh.ossz_hp/100*h.ar)),"?"),f.rejtozes
from flottak f, flotta_hajo fh, hajok h, (
select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].'
union all
select fid,lathatosag from lat_szov_flotta lszf, diplomacia_statuszok dsz where dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=lszf.szid and dsz.mi='.DIPLO_TESTVER.') t
group by fid
) lt
where f.id=lt.fid
and fh.flotta_id=f.id and fh.hajo_id=h.id and f.x>='.$_REQUEST['minx'].' and f.x<='.$_REQUEST['maxx'].' and f.y>='.$_REQUEST['miny'].' and f.y<='.$_REQUEST['maxy'].'
group by f.id',array('id','x','y','nev','tied','tulaj','kezeled','kezelo','szovi','tulaj_szov','egyenertek','rejtozes'));
} elseif ($radarjog) {//radarjog
echo mysql2jsonlabeledmatrix_v2('
select f.id,f.x,f.y,f.nev,if(f.tulaj='.$uid.',1,0),f.tulaj,if(f.kezelo='.$uid.' or f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1,1,0),f.kezelo,if(f.tulaj_szov='.$adataim['szovetseg'].',1,0),f.tulaj_szov,if(lt.lathatosag=2,round(sum(fh.ossz_hp/100*h.ar)),"?"),f.rejtozes
from flottak f, flotta_hajo fh, hajok h, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].') t
group by fid) lt
where f.id=lt.fid
and fh.flotta_id=f.id and fh.hajo_id=h.id and f.x>='.$_REQUEST['minx'].' and f.x<='.$_REQUEST['maxx'].' and f.y>='.$_REQUEST['miny'].' and f.y<='.$_REQUEST['maxy'].'
group by f.id',array('id','x','y','nev','tied','tulaj','kezeled','kezelo','szovi','tulaj_szov','egyenertek','rejtozes'));
} else {//nincs radarjog
echo mysql2jsonlabeledmatrix_v2('
select f.id,f.x,f.y,f.nev,if(f.tulaj='.$uid.',1,0),f.tulaj,if(f.kezelo='.$uid.' or f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1,1,0),f.kezelo,if(f.tulaj_szov='.$adataim['szovetseg'].',1,0),f.tulaj_szov,if(lt.lathatosag=2,round(sum(fh.ossz_hp/100*h.ar)),"?"),f.rejtozes
from flottak f, flotta_hajo fh, hajok h, lat_user_flotta lt
where f.id=lt.fid and lt.uid='.$uid.'
and fh.flotta_id=f.id and fh.hajo_id=h.id and f.x>='.$_REQUEST['minx'].' and f.x<='.$_REQUEST['maxx'].' and f.y>='.$_REQUEST['miny'].' and f.y<='.$_REQUEST['maxy'].'
group by f.id',array('id','x','y','nev','tied','tulaj','kezeled','kezelo','szovi','tulaj_szov','egyenertek','rejtozes'));
}

} else {//nincs fog of war
echo mysql2jsonlabeledmatrix_v2('
select f.id,f.x,f.y,f.nev,if(f.tulaj='.$uid.',1,0),f.tulaj,if(f.kezelo='.$uid.' or f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1,1,0),f.kezelo,if(f.tulaj_szov='.$adataim['szovetseg'].',1,0),f.tulaj_szov,round(sum(fh.ossz_hp/100*h.ar)),f.rejtozes
from flottak f, flotta_hajo fh, hajok h
where fh.flotta_id=f.id and fh.hajo_id=h.id and f.x>='.$_REQUEST['minx'].' and f.x<='.$_REQUEST['maxx'].' and f.y>='.$_REQUEST['miny'].' and f.y<='.$_REQUEST['maxy'].'
group by f.id',array('id','x','y','nev','tied','tulaj','kezeled','kezelo','szovi','tulaj_szov','egyenertek','rejtozes'));
}



insert_into_php_debug_log_resz(round(1000*(microtime(true)-$szkript_mikor_indul)),'flottak');

?>}