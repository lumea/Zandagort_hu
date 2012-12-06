<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

if (!$ismert) kilep();

?>
/*{"nev":<?=json_encode($adataim['nev']);
?>,"mail":<?=json_encode($adataim['email']);
?>,"uid":<?
echo $uid;
?>,"reg":<?
$mikortol=explode('-',$adataim['mikortol']);$mainap=getdate();
echo (int)((mktime(0,0,0,$mainap['mon'],$mainap['mday'],$mainap['year'],0)-mktime(0,0,0,$mikortol[1],$mikortol[2],$mikortol[0],0))/3600/24);
?>,"premium":<?
echo premium_szint();
?>,"pontszam":<?
echo $adataim['pontszam_exp_atlag'];
?>,"helyezes":<?
$er=mysql_query('select count(1) from userek where szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and id not in ('.implode(',',$specko_userek_listaja).') and (karrier!=3 or speci!=3) and pontszam_exp_atlag>'.$adataim['pontszam_exp_atlag']);
$aux=mysql_fetch_array($er);
echo $aux[0]+1;

$kohorszod=(int)mysql2num('select floor(timestampdiff(day,"'.$szerver_indulasa.'",mikortol)/7)+1 from userek where id='.$uid);
?>,"kohorsz":<?
echo $kohorszod;

?>,"kohorsz_helyezes":<?
$er=mysql_query('select count(1) from userek where szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and id not in ('.implode(',',$specko_userek_listaja).') and floor(timestampdiff(day,"'.$szerver_indulasa.'",mikortol)/7)+1='.$kohorszod.' and (karrier!=3 or speci!=3) and pontszam_exp_atlag>'.$adataim['pontszam_exp_atlag']);
$aux=mysql_fetch_array($er);
echo $aux[0]+1;

?>,"max_terulet":<?
echo $adataim['valaha_elert_max_terulet'];
?>,"abszolut_vedett":<?
echo $adataim['abszolut_vedett_terulet'];
?>,"reszben_vedett":<?
echo $adataim['reszben_vedett_terulet']-$adataim['abszolut_vedett_terulet'];



?>,"vagyon":<?
echo $adataim['vagyon'];
?>,"bolygoszam":<?
echo mysql2num('select count(1) from bolygok where tulaj='.$uid.' and letezik=1');
?>,"bolygolimit":<?
echo $adataim['bolygo_limit'];
?>,"limitnoveles":<?
echo mysql2num('select nepesseg from bolygolimitek where bolygolimit='.($adataim['bolygo_limit']+1));
?>,"nepesseg":<?
echo mysql2num('select coalesce(sum(pop),0) from bolygo_ember be,bolygok b where b.tulaj='.$uid.' and b.id=be.bolygo_id and b.letezik=1');
?>,"flottaszam":<?
echo mysql2num('select count(1) from flottak where tulaj='.$uid);
?>,"flottalimit":<?
if ($vegjatek==0) $flottalimit=max($adataim['min_flotta_limit'],3*$adataim['bolygo_limit']);else $flottalimit=max($adataim['min_flotta_limit'],10*$adataim['bolygo_limit']);
echo $flottalimit;
?>,"ossz_ertek":<?
$egyenertek_sima=mysql2num('select coalesce(round(sum(fh.ossz_hp*h.ar)/100),0) from flotta_hajo fh, flottak f, hajok h where fh.flotta_id=f.id and f.tulaj='.$uid.' and fh.hajo_id=h.id');
$egyenertek_resz_minusz=mysql2num('select coalesce(round(sum(fh.ossz_hp*h.ar)/100),0) from flotta_hajo fh, (select ff.*
from flottak ff, resz_flotta_hajo rfh
where ff.id=rfh.flotta_id and rfh.user_id='.$uid.'
group by ff.id) f, hajok h where fh.flotta_id=f.id and f.tulaj='.$uid.' and fh.hajo_id=h.id');
$egyenertek_resz_plusz=mysql2num('select coalesce(round(sum(rfh.hp*h.ar)/100),0) from resz_flotta_hajo rfh, hajok h where rfh.user_id='.$uid.' and rfh.hajo_id=h.id');

//echo $egyenertek_sima;
echo $egyenertek_sima-$egyenertek_resz_minusz+$egyenertek_resz_plusz;

?>,"ossz_legyartott_ertek":<?
echo mysql2num('select coalesce(round(sum(be.db*h.ar)/100),0) from bolygo_eroforras be, bolygok b, hajok h where be.bolygo_id=b.id and b.tulaj='.$uid.' and be.eroforras_id=h.id and b.letezik=1');


$lelott=mysql2row('select round(sum(lelott_ember)/100),round(sum(lelott_kaloz)/100),round(sum(lelott_zanda)/100),round(sum(lelott_ember+lelott_kaloz+lelott_zanda)/100) from '.$database_mmog_nemlog.'.hist_csata_lelottek where user_id='.$uid);
?>,"lelott_ember":<?
echo sanitint($lelott[0]);
?>,"lelott_kaloz":<?
echo sanitint($lelott[1]);
?>,"lelott_zanda":<?
echo sanitint($lelott[2]);
?>,"lelott_minden":<?
echo sanitint($lelott[3]);



?>,"napi_tozsdei_eladas":<?
$x=date('Y-m-d H:i:s',time()-3600*24);
echo sanitint(mysql2num('select sum(mennyiseg*arfolyam) from '.$database_mmog_nemlog.'.tozsdei_kotesek where mikor>="'.$x.'" and elado='.$uid));



?>,"bolygok":<?
if (premium_szint()==2) {
echo mysql2jsonmatrix('select b.id,b.nev,b.osztaly,b.x,b.y,b.kezelo,uk.nev,bem.pop,coalesce(legyartott.egyen,0),coalesce(allomasozo.egyen,0),round(b.terulet/1000000),vedelmi_bonusz,foglalasi_sorszam
from bolygok b
inner join bolygo_ember bem on bem.bolygo_id=b.id
left join userek uk on uk.id=b.kezelo
left join (
select b.id,round(sum(be.db*h.ar)/100) as egyen
from bolygok b, bolygo_eroforras be, hajok h
where be.bolygo_id=b.id and be.eroforras_id=h.id and b.tulaj='.$uid.' and b.letezik=1
group by b.id
) legyartott on legyartott.id=b.id
left join (
select b.id,round(sum(fh.ossz_hp*h.ar)/100) as egyen
from bolygok b, flottak f, flotta_hajo fh, hajok h
where f.bolygo=b.id and f.statusz=1 and fh.flotta_id=f.id and fh.hajo_id=h.id and b.tulaj='.$uid.' and b.letezik=1
group by b.id
) allomasozo on allomasozo.id=b.id
where b.tulaj='.$uid.' and b.letezik=1
group by b.id
order by b.nev,b.id');
} else {
echo mysql2jsonmatrix('select b.id,b.nev,b.osztaly,b.x,b.y,b.kezelo,uk.nev
from bolygok b left join userek uk on uk.id=b.kezelo
where b.tulaj='.$uid.' and b.letezik=1 order by b.nev,b.id');
}



if (premium_szint()==2) {
/*
?>,"teljesflottaertek":<?
echo mysql2num('select coalesce(round(sum(fh.ossz_hp*h.ar)/100),0) from flottak f, flotta_hajo fh, hajok h, bolygok b where f.bolygo=b.id and b.tulaj='.$uid.' and b.letezik=1 and f.statusz=1 and fh.flotta_id=f.id and fh.hajo_id=h.id');
*/

?>,"flottak":<?
echo mysql2jsonmatrix('select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,0,""
,f.statusz,b.id,b.nev,b.osztaly

,if(f.statusz in (12,13,14),f2.id,null)
,if(f.statusz in (12,13,14),f2.nev,null)

,f.bazis_x,f.bazis_y,f.cel_x,f.cel_y
,f.egyenertek,if(f.statusz in (6,7,9,11),ceil(sqrt(pow(f.x-b.x,2)+pow(f.y-b.y,2))/f.sebesseg)
,if(f.statusz in (12,13),ceil(sqrt(pow(f.x-f2.x,2)+pow(f.y-f2.y,2))/f.sebesseg),if(f.statusz='.STATUSZ_MEGY_XY.',ceil(sqrt(pow(f.x-f.cel_x,2)+pow(f.y-f.cel_y,2))/f.sebesseg),-1)))
,1 as sajat,round(f.moral_szonda_nelkul/10) as mor,1 as diplo
,f.kozos,tul.id,tul.nev
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,1000)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj='.$uid.' and f.kozos=0
group by f.id) f
inner join userek tul on tul.id=f.tulaj

left join bolygok b on b.id=
case
when f.statusz=1 then f.bolygo
when f.statusz in (6,7,8,9,10) then f.cel_bolygo
when f.statusz=11 then f.bazis_bolygo
else 0
end

left join flottak f2 on f2.id=f.cel_flotta
where f.tulaj='.$uid.' and f.kozos=0
group by f.id

order by sajat desc,flotta_nev,flotta_id
');

?>,"kozos_flottak":<?
echo mysql2jsonmatrix('select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,0,""
,f.statusz,b.id,b.nev,b.osztaly

,if(f.statusz in (12,13,14),f2.id,null)
,if(f.statusz in (12,13,14),f2.nev,null)

,f.bazis_x,f.bazis_y,f.cel_x,f.cel_y
,f.egyenertek,if(f.statusz in (6,7,9,11),ceil(sqrt(pow(f.x-b.x,2)+pow(f.y-b.y,2))/f.sebesseg)
,if(f.statusz in (12,13),ceil(sqrt(pow(f.x-f2.x,2)+pow(f.y-f2.y,2))/f.sebesseg),if(f.statusz='.STATUSZ_MEGY_XY.',ceil(sqrt(pow(f.x-f.cel_x,2)+pow(f.y-f.cel_y,2))/f.sebesseg),-1)))
,1 as sajat,round(f.moral_szonda_nelkul/10) as mor,1 as diplo
,f.kozos,tul.id,tul.nev
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,1000)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj='.$uid.' and f.kozos=1
group by f.id) f
inner join userek tul on tul.id=f.tulaj

left join bolygok b on b.id=
case
when f.statusz=1 then f.bolygo
when f.statusz in (6,7,8,9,10) then f.cel_bolygo
when f.statusz=11 then f.bazis_bolygo
else 0
end

left join flottak f2 on f2.id=f.cel_flotta
where f.tulaj='.$uid.' and f.kozos=1
group by f.id

union

select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,0,""
,f.statusz,b.id,b.nev,b.osztaly

,if(f.statusz in (12,13,14),f2.id,null)
,if(f.statusz in (12,13,14),f2.nev,null)

,f.bazis_x,f.bazis_y,f.cel_x,f.cel_y
,f.egyenertek,if(f.statusz in (6,7,9,11),ceil(sqrt(pow(f.x-b.x,2)+pow(f.y-b.y,2))/f.sebesseg)
,if(f.statusz in (12,13),ceil(sqrt(pow(f.x-f2.x,2)+pow(f.y-f2.y,2))/f.sebesseg),if(f.statusz='.STATUSZ_MEGY_XY.',ceil(sqrt(pow(f.x-f.cel_x,2)+pow(f.y-f.cel_y,2))/f.sebesseg),-1)))
,0 as sajat,round(f.moral_szonda_nelkul/10) as mor,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo
,f.kozos,tul.id,tul.nev
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,-1)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1
group by f.id) f
inner join userek tul on tul.id=f.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'

left join bolygok b on b.id=
case
when f.statusz=1 then f.bolygo
when f.statusz in (6,7,8,9,10) then f.cel_bolygo
when f.statusz=11 then f.bazis_bolygo
else 0
end

left join flottak f2 on f2.id=f.cel_flotta
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1
group by f.id

order by sajat desc,flotta_nev,flotta_id
');



?>,"resz_flottak":<?
echo mysql2jsonmatrix('select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,0,""
,f.statusz,b.id,b.nev,b.osztaly

,if(f.statusz in (12,13,14),f2.id,null)
,if(f.statusz in (12,13,14),f2.nev,null)

,f.bazis_x,f.bazis_y,f.cel_x,f.cel_y
,f.egyenertek,if(f.statusz in (6,7,9,11),ceil(sqrt(pow(f.x-b.x,2)+pow(f.y-b.y,2))/f.sebesseg)
,if(f.statusz in (12,13),ceil(sqrt(pow(f.x-f2.x,2)+pow(f.y-f2.y,2))/f.sebesseg),if(f.statusz='.STATUSZ_MEGY_XY.',ceil(sqrt(pow(f.x-f.cel_x,2)+pow(f.y-f.cel_y,2))/f.sebesseg),-1)))
,0 as sajat,round(f.moral_szonda_nelkul/10) as mor,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo
,f.kozos,tul.id,tul.nev
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,-1)) as reszesedes
from flottak f
inner join resz_flotta_hajo rfh on rfh.flotta_id=f.id
inner join hajok h on h.id=rfh.hajo_id
group by f.id
having sum(if(rfh.user_id='.$uid.',rfh.hp,0))>0) f
inner join userek tul on tul.id=f.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'

left join bolygok b on b.id=
case
when f.statusz=1 then f.bolygo
when f.statusz in (6,7,8,9,10) then f.cel_bolygo
when f.statusz=11 then f.bazis_bolygo
else 0
end

left join flottak f2 on f2.id=f.cel_flotta
where f.tulaj!='.$uid.' and not (f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1)
group by f.id

order by sajat desc,flotta_nev,flotta_id
');
/*
select ff.*
from flottak ff, resz_flotta_hajo rfh
where ff.id=rfh.flotta_id and rfh.user_id='.$uid.'
group by ff.id
*/


?>,"hajok":<?
echo mysql2jsonmatrix('select h.id
,ceil(sum(be2.hp)/100)
,round(sum(be2.hp*h.ar/100))
,ceil(sum(coalesce(fh2.hp,0)-coalesce(fh2_minusz.hp,0)+coalesce(fh2_plusz.hp,0))/100)
,round(sum((coalesce(fh2.hp,0)-coalesce(fh2_minusz.hp,0)+coalesce(fh2_plusz.hp,0))*h.ar/100))
,ceil(sum(be2.hp)/100)+ceil(sum(coalesce(fh2.hp,0)-coalesce(fh2_minusz.hp,0)+coalesce(fh2_plusz.hp,0))/100)
,round(sum(be2.hp*h.ar/100))+round(sum((coalesce(fh2.hp,0)-coalesce(fh2_minusz.hp,0)+coalesce(fh2_plusz.hp,0))*h.ar/100))
from hajok h
left join (select be.eroforras_id,sum(be.db) as hp from bolygo_eroforras be, bolygok b where be.bolygo_id=b.id and b.tulaj='.$uid.' and b.letezik=1 group by be.eroforras_id) be2 on be2.eroforras_id=h.id
left join (select fh.hajo_id,sum(fh.ossz_hp) as hp from flotta_hajo fh, flottak f where fh.flotta_id=f.id and f.tulaj='.$uid.' group by fh.hajo_id) fh2 on fh2.hajo_id=h.id
left join (select rfh.hajo_id,sum(rfh.hp) as hp from resz_flotta_hajo rfh, flottak f where rfh.flotta_id=f.id and f.tulaj='.$uid.' group by rfh.hajo_id) fh2_minusz on fh2_minusz.hajo_id=h.id
left join (select rfh.hajo_id,sum(rfh.hp) as hp from resz_flotta_hajo rfh, flottak f where rfh.flotta_id=f.id and rfh.user_id='.$uid.' group by rfh.hajo_id) fh2_plusz on fh2_plusz.hajo_id=h.id
group by h.id
with rollup');


} else {



?>,"flottak":<?
echo mysql2jsonmatrix('
select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,f.kozos
,1 as diplo
,tul.id,tul.nev
,1 as sajat
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,1000)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj='.$uid.' and f.kozos=0
group by f.id) f
inner join userek tul on tul.id=f.tulaj
where f.tulaj='.$uid.' and f.kozos=0
group by f.id

order by sajat desc,flotta_nev,flotta_id
');

?>,"kozos_flottak":<?
echo mysql2jsonmatrix('
select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,f.kozos
,1 as diplo
,tul.id,tul.nev
,1 as sajat
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,1000)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj='.$uid.' and f.kozos=1
group by f.id) f
inner join userek tul on tul.id=f.tulaj
where f.tulaj='.$uid.' and f.kozos=1
group by f.id

union

select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,f.kozos
,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo
,tul.id,tul.nev
,0 as sajat
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,-1)) as reszesedes
from flottak f
left join resz_flotta_hajo rfh on rfh.flotta_id=f.id
left join hajok h on h.id=rfh.hajo_id
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1
group by f.id) f
inner join userek tul on tul.id=f.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1
group by f.id

order by sajat desc,flotta_nev,flotta_id
');


?>,"resz_flottak":<?
echo mysql2jsonmatrix('select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,f.kozos
,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo
,tul.id,tul.nev
,0 as sajat
,f.reszesedes
from (select f.*,round(coalesce(sum(if(rfh.user_id='.$uid.',rfh.hp*h.ar,0))/sum(rfh.hp*h.ar)*1000,-1)) as reszesedes
from flottak f
inner join resz_flotta_hajo rfh on rfh.flotta_id=f.id
inner join hajok h on h.id=rfh.hajo_id
group by f.id
having sum(if(rfh.user_id='.$uid.',rfh.hp,0))>0) f
inner join userek tul on tul.id=f.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'
where f.tulaj!='.$uid.' and not (f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1)
group by f.id

order by sajat desc,flotta_nev,flotta_id
');


}




?>,"kp":<?
echo $adataim['kp'];
?>,"megoszthato_kp":<?
echo $adataim['megoszthato_kp'];
?>,"kf":<?
echo mysql2jsonmatrix('
select kt.id,kt.nev'.$lang__lang.',uksz.szint,kt.max_szint
,coalesce(group_concat(if(uksz2.szint<kt2.max_szint,kt2.nev'.$lang__lang.',null) order by kt2.sorszam separator ", "),"")
,coalesce(group_concat(if(uksz2.szint<kt2.max_szint,null,kt2.nev'.$lang__lang.') order by kt2.sorszam separator ", "),"")
from user_kutatasi_szint uksz
inner join kutatasi_temak kt on uksz.kf_id=kt.id
left join kutatasi_feltetelek kf on kf.tema=kt.id
left join kutatasi_temak kt2 on kt2.id=kf.feltetel
left join user_kutatasi_szint uksz2 on uksz2.kf_id=kt2.id and uksz2.user_id='.$uid.'
where uksz.user_id='.$uid.' and kt.id>2
group by kt.id
order by kt.sorszam');
?>,"penz_adhato_max":<?
echo $adataim['penz_adhato_max'];
?>,"penz_adott":<?
echo $adataim['penz_adott'];
/*
?>,"penz_kaphato_max":<?
echo $adataim['penz_kaphato_max'];
?>,"penz_kapott":<?
echo $adataim['penz_kapott'];
*/



?>,"tech":<?
echo $adataim['techszint'];
?>,"kov_hatar":<?
$hatarok=array(45,90,140,190,340,500);
if ($adataim['techszint']<6) echo $hatarok[$adataim['techszint']];
else echo 0;
?>,"kov_gyarak":<?
if ($adataim['techszint']<6) echo mysql2jsonmatrix('select gyt.id,gyt.nev'.$lang__lang.'
from gyar_kutatasi_szint gyksz, gyarak gy, gyartipusok gyt
where gyksz.kf_id=1 and gyksz.szint='.($adataim['techszint']+1).' and gyksz.gyar_id=gy.id and gy.tipus=gyt.id
group by gyt.id
order by gyt.iparag_sorszam,gyt.id');
else echo '[]';
?>,"most_gyarak":<?
if ($adataim['techszint']<=6) echo mysql2jsonmatrix('select gyt.id,gyt.nev'.$lang__lang.'
from gyar_kutatasi_szint gyksz, gyarak gy, gyartipusok gyt
where gyksz.kf_id=1 and gyksz.szint='.($adataim['techszint']).' and gyksz.gyar_id=gy.id and gy.tipus=gyt.id
group by gyt.id
order by gyt.iparag_sorszam,gyt.id');
else echo '[]';


if (premium_szint()==2) {
?>,"transzfer_osszefoglalo":<?


echo mysql2jsonmatrix('select bid,bosztaly,bnev
,sum(t1),sum(t2),sum(t3),sum(t4),sum(t5),sum(t6)
,sum(t7),sum(t8),sum(t9),sum(t10),sum(t11),sum(t12)
,sum(t13)
from (

select honnan_bolygo_id as bid,b1.osztaly as bosztaly,b1.nev as bnev
,-sum(if(eroforras_id=56,darab,0)) as t1
,-sum(if(eroforras_id=64,darab,0)) as t2
,-sum(if(eroforras_id=65,darab,0)) as t3
,-sum(if(eroforras_id=66,darab,0)) as t4
,-sum(if(eroforras_id=67,darab,0)) as t5
,-sum(if(eroforras_id=68,darab,0)) as t6
,-sum(if(eroforras_id=69,darab,0)) as t7
,-sum(if(eroforras_id=70,darab,0)) as t8
,-sum(if(eroforras_id=71,darab,0)) as t9
,-sum(if(eroforras_id=72,darab,0)) as t10
,-sum(if(eroforras_id=73,darab,0)) as t11
,-sum(if(eroforras_id=74,darab,0)) as t12
,-sum(if(eroforras_id=150,darab,0)) as t13
from cron_tabla_eroforras_transzfer t
inner join bolygok b1 on b1.id=t.honnan_bolygo_id
left join bolygok b2 on b2.id=t.hova_bolygo_id
where b1.tulaj='.$uid.' and coalesce(b2.tulaj,'.$uid.')='.$uid.'
group by honnan_bolygo_id

union all

select hova_bolygo_id as bid,coalesce(b2.osztaly,0) as bosztaly,coalesce(b2.nev,"") as bnev
,sum(if(eroforras_id=56,darab,0)) as t1
,sum(if(eroforras_id=64,darab,0)) as t2
,sum(if(eroforras_id=65,darab,0)) as t3
,sum(if(eroforras_id=66,darab,0)) as t4
,sum(if(eroforras_id=67,darab,0)) as t5
,sum(if(eroforras_id=68,darab,0)) as t6
,sum(if(eroforras_id=69,darab,0)) as t7
,sum(if(eroforras_id=70,darab,0)) as t8
,sum(if(eroforras_id=71,darab,0)) as t9
,sum(if(eroforras_id=72,darab,0)) as t10
,sum(if(eroforras_id=73,darab,0)) as t11
,sum(if(eroforras_id=74,darab,0)) as t12
,sum(if(eroforras_id=150,darab,0)) as t13
from cron_tabla_eroforras_transzfer t
inner join bolygok b1 on b1.id=t.honnan_bolygo_id
left join bolygok b2 on b2.id=t.hova_bolygo_id
where b1.tulaj='.$uid.' and coalesce(b2.tulaj,'.$uid.')='.$uid.'
group by hova_bolygo_id

) tt
group by bid
order by if(bid>0,0,1),bnev,bid');


} else {
?>,"transzfer_osszefoglalo":""<?

}




?>}*/
<?

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>