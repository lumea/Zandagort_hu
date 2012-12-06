<?
include_once('config.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;

set_time_limit(0);//igy lefuthat minden, max neha egy-egy percet kesik a galaxis oraja (a lock miatt)
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');


$fantom_lebukas=true;


include('csatlak.php');


if ($fut_a_szim) {

if ($inaktiv_szerver) {//inaktiv szerveren automatikus aktivalas
	$d=date('Y-m-d H:i:s',time()-3600*24);
	mysql_query('update userek set uccso_akt="'.$d.'"');
}

echo "\n".date('Y-m-d H:i:s');
//mysql_query('do release_lock("'.$szimlock_name.'")');//ha vmiert befagy a lock; de igazabol ha beall a lock, akkor valamelyik query irgalmatlan lassan fut, vagyis a processlist-et kell lecsekkolni

define('SZIM_SEB',15);//ha atirod, akkor bolygok es bolygo_eroforras-ban is a bolygo_id_mod-ot ujra kell generalni
$mikor_indul=microtime(true);
$res=mysql_query('select get_lock("'.$szimlock_name.'",0)');
$aux=mysql_fetch_array($res);
if ($aux[0]==1) {

$er=mysql_query('select * from ido');$rendszer_idopont=mysql_fetch_array($er);$idopont=$rendszer_idopont['idopont'];$perc=$idopont%SZIM_SEB;$hanyadik_kor=floor($idopont/SZIM_SEB);
$szimlog_hossz_npc=0;$szimlog_hossz_monetaris=0;$szimlog_hossz_termeles=0;$szimlog_hossz_felderites=0;$szimlog_hossz_flottamoral=0;$szimlog_hossz_flottak=0;$szimlog_hossz_csatak=0;$szimlog_hossz=0;$szimlog_hossz_debug_elott=0;$szimlog_hossz_debug_utan=0;$szimlog_hossz_ostromok=0;$szimlog_hossz_fog=0;
$mostani_datum=date('Y-m-d H:i:s');
$mai_nap=date('Y-m-d');
$tegnap=date('Y-m-d',time()-3600*24);
$tegnap_minusz_egy_het=date('Y-m-d',time()-3600*24*7);
$ora_perc=date('H:i');

mysql_query('update ido set idopont=idopont+1');//az egesz elejen van, igy biztos, hogy barhol behal a szkript, a "hibaterheles" egyenletesen megoszlik a jatekosok kozott

mysql_query('update ido set idopont_kezd=idopont_kezd+1');
/*******************************************************************************************************/
//TP frissites
//rang frissites
//harcos karrier frissites
if (substr($ora_perc,0,2)=='00') if (substr($rendszer_idopont['uccso_tp_frissites'],0,10)<$mai_nap) {
	mysql_query('update ido set uccso_tp_frissites="'.$mostani_datum.'"');
	//
	mysql_select_db($database_mmog_nemlog);
	//0. hist_csata_lelottek (ez nem a tp-hez kell, hanem a speci nyitashoz, meg amugy se jon rosszul)
	mysql_query('insert into hist_csata_lelottek (csata_id,user_id,lelott_ember,lelott_kaloz,lelott_zanda)
select cs.id as csata_id,csft.iranyito as user_id
,round(sum(if(csfv.iranyito>0,css.sebzes,0)/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)) as lelott_ember
,round(sum(if(csfv.iranyito=0 and csfv.tulaj=0,css.sebzes,0)/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)) as lelott_kaloz
,round(sum(if(csfv.iranyito=0 and csfv.tulaj=-1,css.sebzes,0)/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)) as lelott_zanda
from hist_csatak cs
inner join hist_csata_sebzesek css on css.csata_id=cs.id
inner join hist_csata_flotta csft on csft.csata_id=cs.id and csft.flotta_id=css.tamado_flotta_id
inner join hist_csata_flotta csfv on csfv.csata_id=cs.id and csfv.flotta_id=css.vedo_flotta_id
inner join hist_csata_flotta_hajo csfhv on csfhv.csata_id=cs.id and csfhv.flotta_id=css.vedo_flotta_id and csfhv.hajo_id=css.vedo_hajo_id
inner join '.$database_mmog.'.hajok hv on hv.id=css.vedo_hajo_id
where css.tamado_hajo_id!=206 and css.vedo_hajo_id!=206
and csft.kezdo=0 and csfv.kezdo=0
and csft.iranyito>0
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by cs.id,csft.iranyito');
	//1. hist_csata_tpk
	mysql_query('insert into hist_csata_tpk (csata_id,user_id,lelott)
select cs.id,csft.iranyito,round(sum(css.sebzes/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)) as lelott
from hist_csatak cs
inner join hist_csata_sebzesek css on css.csata_id=cs.id
inner join hist_csata_flotta csft on csft.csata_id=cs.id and csft.flotta_id=css.tamado_flotta_id
inner join hist_csata_flotta csfv on csfv.csata_id=cs.id and csfv.flotta_id=css.vedo_flotta_id
inner join hist_csata_flotta_hajo csfhv on csfhv.csata_id=cs.id and csfhv.flotta_id=css.vedo_flotta_id and csfhv.hajo_id=css.vedo_hajo_id
inner join '.$database_mmog.'.hajok hv on hv.id=css.vedo_hajo_id
where css.tamado_hajo_id!=206 and css.vedo_hajo_id!=206
and csft.kezdo=0 and csfv.kezdo=0
and csft.iranyito>0 and csfv.iranyito>0
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by cs.id,csft.iranyito');
	mysql_query('update hist_csata_tpk cstp, (
select cs.id,csfv.iranyito,round(sum(css.sebzes/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)) as bukott
from hist_csatak cs
inner join hist_csata_sebzesek css on css.csata_id=cs.id
inner join hist_csata_flotta csft on csft.csata_id=cs.id and csft.flotta_id=css.tamado_flotta_id
inner join hist_csata_flotta csfv on csfv.csata_id=cs.id and csfv.flotta_id=css.vedo_flotta_id
inner join hist_csata_flotta_hajo csfhv on csfhv.csata_id=cs.id and csfhv.flotta_id=css.vedo_flotta_id and csfhv.hajo_id=css.vedo_hajo_id
inner join '.$database_mmog.'.hajok hv on hv.id=css.vedo_hajo_id
where css.tamado_hajo_id!=206 and css.vedo_hajo_id!=206
and csft.kezdo=0 and csfv.kezdo=0
and csft.iranyito>0 and csfv.iranyito>0
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by cs.id,csfv.iranyito
) t
set cstp.bukott=t.bukott
where cstp.csata_id=t.id
and cstp.user_id=t.iranyito');
	//ossz_lelott: heti atlag
	$r=mysql_query('select round(sum(css.sebzes/csfhv.serules*(csfhv.ossz_hp_elotte-csfhv.ossz_hp_utana)*hv.ar)/greatest(count(distinct date(cs.mikor)),1)) as lelott
from hist_csatak cs
inner join hist_csata_sebzesek css on css.csata_id=cs.id
inner join hist_csata_flotta csft on csft.csata_id=cs.id and csft.flotta_id=css.tamado_flotta_id
inner join hist_csata_flotta csfv on csfv.csata_id=cs.id and csfv.flotta_id=css.vedo_flotta_id
inner join hist_csata_flotta_hajo csfhv on csfhv.csata_id=cs.id and csfhv.flotta_id=css.vedo_flotta_id and csfhv.hajo_id=css.vedo_hajo_id
inner join '.$database_mmog.'.hajok hv on hv.id=css.vedo_hajo_id
where css.tamado_hajo_id!=206 and css.vedo_hajo_id!=206
and csft.kezdo=0 and csfv.kezdo=0
and csft.iranyito>0 and csfv.iranyito>0
and cs.mikor between "'.$tegnap_minusz_egy_het.' 00:00:00" and "'.$tegnap.' 23:59:59"');
	$aux=mysql_fetch_array($r);
	mysql_query('update hist_csata_tpk cstp, hist_csatak cs
set cstp.ossz_lelott='.sanitint($aux['lelott']).'
where cstp.csata_id=cs.id
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"');
	//
	mysql_query('update hist_csata_tpk cstp, (
select csf.csata_id,csf.iranyito,max(csf.tp) as maxtp
from hist_csatak cs
inner join hist_csata_flotta csf on csf.csata_id=cs.id
where csf.iranyito>0
and csf.kezdo=0
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by csf.csata_id,csf.iranyito
) t
set cstp.sajat_tp=t.maxtp
where cstp.csata_id=t.csata_id and cstp.user_id=t.iranyito');
	mysql_query('update hist_csata_tpk cstp, (
select cs.id,csft.iranyito,max(csfv.tp) as maxtp
from hist_csatak cs
inner join hist_csata_sebzesek css on css.csata_id=cs.id
inner join hist_csata_flotta csft on csft.csata_id=cs.id and csft.flotta_id=css.tamado_flotta_id
inner join hist_csata_flotta csfv on csfv.csata_id=cs.id and csfv.flotta_id=css.vedo_flotta_id
where csft.iranyito>0 and csfv.iranyito>0
and csft.kezdo=0 and csfv.kezdo=0
and css.sebzes>0
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by cs.id,csft.iranyito
) t
set cstp.ellen_tp=t.maxtp
where cstp.csata_id=t.id and cstp.user_id=t.iranyito');
	mysql_query('update hist_csata_tpk
set szerzett_tp=round(
least(greatest(lelott/if(bukott=0,1,bukott)-0.8,0),2)
*least(sqrt(least(0.01,lelott/if(ossz_lelott=0,1,ossz_lelott))),1)
*least(greatest((ellen_tp+1)/(sajat_tp+1),0.2),1)
*1000)');
	//2. hist_tp_szerzesek
	mysql_query('insert into hist_tp_szerzesek
select cstp.user_id,date(cs.mikor) as mikor,sum(cstp.szerzett_tp)
from hist_csata_tpk cstp, hist_csatak cs
where cstp.csata_id=cs.id
and cs.mikor between "'.$tegnap.' 00:00:00" and "'.$tegnap.' 23:59:59"
group by cstp.user_id');
	//3. userek.tp
	mysql_select_db($database_mmog);
	mysql_query('update userek u, '.$database_mmog_nemlog.'.hist_tp_szerzesek htpsz
set u.tp=u.tp+htpsz.szerzett_tp
where htpsz.user_id=u.id
and htpsz.mikor="'.$tegnap.'"');
	//
	//harci toplista
	mysql_query('lock tables userek u write, '.$database_mmog_nemlog.'.hist_tp_szerzesek write');
	mysql_query('update userek u set u.heti_harci_toplista=0');
	mysql_query('set @x:=0');
	mysql_query('update userek u, (
select @x:=@x+1 as sorszam,t.user_id,t.tp
from (select user_id,sum(szerzett_tp) as tp from '.$database_mmog_nemlog.'.hist_tp_szerzesek where mikor>="'.$tegnap_minusz_egy_het.' 00:00:00" group by user_id having tp>0) t
order by t.tp desc
) tt
set u.heti_harci_toplista=tt.sorszam, u.heti_tp=tt.tp
where u.id=tt.user_id');
	mysql_query('unlock tables');
	//rangok
	mysql_query('update userek set rang=4 where rang<4 and tp>=20000 and heti_harci_toplista between 1 and 10');//200 TP
	mysql_query('update userek set rang=3 where rang<3 and tp>=4000 and heti_harci_toplista between 1 and 25');//40 TP
	mysql_query('update userek set rang=2 where rang<2 and tp>=1000 and heti_harci_toplista between 1 and 100');//10 TP
	//
	//specik nyitasa
	//speci=1 (vedelmezo)
	mysql_query('update userek u,(select user_id from '.$database_mmog_nemlog.'.hist_csata_lelottek group by user_id
having sum(lelott_kaloz)/10000>=20000 and sum(lelott_kaloz)>sum(lelott_ember)) t
set u.speci_2_1=1
where u.id=t.user_id and u.karrier=2');
	//speci=2 (marsall)
	mysql_query('update userek u,(select user_id from '.$database_mmog_nemlog.'.hist_csata_lelottek group by user_id
having sum(lelott_ember)/10000>=20000 and sum(lelott_ember)>sum(lelott_kaloz)) t
set u.speci_2_2=1
where u.id=t.user_id and u.karrier=2 and u.rang>=3');
	//speci=3 (fejvadasz)
	mysql_query('update userek u,(select user_id from '.$database_mmog_nemlog.'.hist_csata_lelottek group by user_id
having sum(lelott_ember)/10000>=20000 and sum(lelott_ember)>=3*sum(lelott_kaloz)) t
set u.speci_2_3=1
where u.id=t.user_id and u.karrier=2 and u.rang>=3');
	//speci=4 (zelota)
	if ($mai_nap>=$zelota_mikortol_valaszthato) if ($mai_nap<$zelota_meddig_valaszthato) {
		mysql_query('update userek set speci_2_4=1 where karrier=2');
	}
}


//PREMIUM CSEKK
if ($idopont%60==19) {
	//epitesi lista torlese
	//mysql_query('delete q from queue_epitkezesek q, bolygok b, userek u where q.bolygo_id=b.id and b.tulaj=u.id and (u.premium=0 and u.premium_alap<=now())') or hiba(__FILE__,__LINE__,mysql_error());
	//helyett befagyasztas
	//mysql_query('update bolygok b, userek u set b.befagy_eplista=1 where b.tulaj=u.id and (u.premium=0 and u.premium_alap<=now())');
	//aki meg egyszer sem fizett elo, annak az 5 folottieket levagni (aki igen, annak semmi)
	//mysql_query('delete q from queue_epitkezesek q, bolygok b, userek u where q.bolygo_id=b.id and b.tulaj=u.id and (u.premium=0 and u.premium_alap<=now()) and q.sorszam>5 and u.premium_ertesito=0');//premium_ertesito=1, ha legalabb egyszer fizetett be konkretan penzt
	//autotranszer torlese, emelt szint
	mysql_query('delete c from cron_tabla_eroforras_transzfer c, bolygok b, userek u where c.honnan_bolygo_id=b.id and b.tulaj=u.id and (u.premium<2 and u.premium_emelt<=now())') or hiba(__FILE__,__LINE__,mysql_error());
}


if ($idopont%60==17) {//valaha_elert_max_terulet esetleges csokkentese, ennek fuggvenyeben a vedelmi szintek ujraszamitasa
	mysql_query('update userek u
left join (
select user_id as id,max(terulet) as elmult_3_heti_max
from '.$database_mmog_nemlog.'.terulet_valtozasok
where timestampdiff(day,mikor,now())<21
group by user_id
) tvt on u.id=tvt.id
left join (
select hu.id,max(hu.terulet) as elmult_3_heti_max
from '.$database_mmog_nemlog.'.hist_userek hu, '.$database_mmog_nemlog.'.hist_idopontok hi
where hu.idopont=hi.id and timestampdiff(day,hi.mikor,now())<21
group by hu.id
) hut on u.id=hut.id
set u.valaha_elert_max_terulet=greatest(u.jelenlegi_terulet,coalesce(hut.elmult_3_heti_max,0),coalesce(tvt.elmult_3_heti_max,0))');
	$er=mysql_query('select id from userek');while($aux=mysql_fetch_array($er)) frissit_user_vedelmi_szintek($aux[0]);
}

//egyseges premium uzenet mindenkinek, akinek 5 nap mulva jar le, es tobb mint egy hete kuldott ertesitot
$er=mysql_query('select id,nev,nyelv from userek where timestampdiff(day,premium_lejar_ertesito_mikor,now())>7 and premium=0 and premium_alap>now() and timestampdiff(day,now(),premium_alap)<5 order by id limit 100') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) premium_lejar_uzenet($aux[0],$aux[1],$aux[2]);







//diplomata karrierek frissitese
if (substr($ora_perc,0,2)=='06') if (substr($rendszer_idopont['uccso_diplomata_frissites'],0,10)<$mai_nap) {
	mysql_query('update ido set uccso_diplomata_frissites="'.$mostani_datum.'"');
	//bekebiro: min 2 bolygo, min 5 fos szovi tagja, min 2 48 oras mnt, ebbol min 1 top5 szovivel
	mysql_query('update userek u,(select u.id,coalesce(sz.tagletszam,0) as letszam
,count(distinct b.id) as bolygok_szama
,count(distinct dsz.id) as mntk_szama
,count(distinct dsz5.id) as mntk_szama_top5_szovivel
from userek u
inner join szovetsegek sz on sz.id=u.szovetseg
left join bolygok b on b.tulaj=u.id
left join diplomacia_statuszok ds on ds.mi=3 and ds.ki>0 and ds.kivel>ds.ki and (ds.diplo_1=u.id or ds.diplo_2=u.id) and ds.felbontasi_ido>=48
left join szovetsegek dsz on dsz.id=ds.kivel
left join szovetsegek dsz5 on dsz5.id=ds.kivel and dsz5.helyezes between 1 and 5
where u.karrier=4
group by u.id
having letszam>=5 and bolygok_szama>=2 and mntk_szama>=2 and mntk_szama_top5_szovivel>=1) t
set u.speci_4_1=1
where u.id=t.id');
	//tanacsnok: min 2 bolygo, min 4 kul szoviben tutoralt bolygo
	mysql_query('update userek u,(select u.id
,count(distinct b.id) as bolygok_szama
,count(distinct sz.id) as tutoralt_szovik_szama
from userek u
left join bolygok b on b.tulaj=u.id
left join bolygok bt on bt.kezelo=u.id
left join szovetsegek sz on sz.id=bt.tulaj_szov
where u.karrier=4
group by u.id
having bolygok_szama>=2 and tutoralt_szovik_szama>=4) t
set u.speci_4_2=1
where u.id=t.id');
}


//DIPLOMACIA
mysql_query('delete from diplomacia_statuszok where felbontas_alatt>0 and felbontas_mikor<now()');
$er=mysql_query('select * from diplomacia_leendo_statuszok where miota<now()');
while($aux=mysql_fetch_array($er)) {
	mysql_query('insert ignore into diplomacia_statuszok (ki,kivel,mi,miota,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,diplo_1,diplo_2,nyilvanos) values('.$aux['ki'].','.$aux['kivel'].','.$aux['mi'].',"'.$aux['miota'].'",'.$aux['szoveg_id'].','.$aux['kezdemenyezo'].',"'.$aux['szoveg_reszlet'].'",'.$aux['felbontasi_ido'].','.$aux['diplo_1'].','.$aux['diplo_2'].','.$aux['nyilvanos'].')');
	mysql_query('delete from diplomacia_leendo_statuszok where ki='.$aux['ki'].' and kivel='.$aux['kivel']);
}

//BEKEBIRO: potencialis es valodi kozti valtas
mysql_query('update userek u, (
select u.id,coalesce(sz.tagletszam,0) as letszam
from userek u
left join szovetsegek sz on u.szovetseg=sz.id
where u.karrier=4 and u.speci in (1,3)
) t
set u.speci=if(t.letszam<5,3,1)
where u.id=t.id');


//CRON

mysql_query('update bolygok set van_e_epites_alatti_epulet=0');
mysql_query('update bolygok b, (select distinct bolygo_id from cron_tabla where feladat=1) t
set b.van_e_epites_alatti_epulet=1
where b.id=t.bolygo_id');

$cron_er=mysql_query('select c.*,b.tulaj,u.nyelv from cron_tabla c, bolygok b, userek u where c.mikor_aktualis<="'.date('Y-m-d H:i:s').'" and c.bolygo_id=b.id and b.tulaj=u.id order by c.id') or hiba(__FILE__,__LINE__,mysql_error());
while($cron=mysql_fetch_array($cron_er)) {
	switch($cron['feladat']) {
		case FELADAT_GYAR_EPIT:
			uj_gyar_felhuzasa($cron['bolygo_id'],$cron['gyar_id'],$cron['aktiv'],$cron['darab']);
			//
			//if ($cron['gyar_id']==78) achievement_uzenet($cron['tulaj'],ACHIEVEMENT_ELSO_VAROS,$cron['nyelv']);
			if ($cron['gyar_id']==87) achievement_uzenet($cron['tulaj'],ACHIEVEMENT_ELSO_HIRSZERZO,$cron['nyelv']);
			//if (($cron['gyar_id']>=79 && $cron['gyar_id']<=86) || $cron['gyar_id']==90 || $cron['gyar_id']==91) achievement_uzenet($cron['tulaj'],ACHIEVEMENT_ELSO_KUTATO,$cron['nyelv']);
			if ($cron['gyar_id']>=59 && $cron['gyar_id']<=76) achievement_uzenet($cron['tulaj'],ACHIEVEMENT_ELSO_URHAJOGYAR,$cron['nyelv']);
			if ($cron['gyar_id']==89) achievement_uzenet($cron['tulaj'],ACHIEVEMENT_ELSO_TELEPORT,$cron['nyelv']);
			//
		break;
		case FELADAT_GYAR_LEROMBOL:
			if ($cron['indulo_allapot']>0) {//uj rendszer
				if ($cron['indulo_allapot']==1) {//nullarol epites
					$szazalek=round(100-$cron['szazalek']/2);
				}
				if ($cron['indulo_allapot']==2) {//keszrol rombolas
					$szazalek=50;
				}
				mysql_query('update gyar_epitesi_koltseg gyek,gyarak gy,bolygo_eroforras be set be.db=be.db+'.$cron['darab'].'*gyek.db*'.$szazalek.'/100 where gyek.tipus=gy.tipus and gy.id='.$cron['gyar_id'].' and gyek.szint=gy.szint and gyek.eroforras_id=be.eroforras_id and be.bolygo_id='.$cron['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
			}
			//a tenyleges rombolas mar a parancs kiadasakor megtortenik, most csak lejar
		break;
	}
	mysql_query('delete from cron_tabla where id='.$cron['id']) or hiba(__FILE__,__LINE__,mysql_error());
	bolygo_terulet_frissites($cron['bolygo_id']);
}

mysql_query('update bolygok set maradt_epites_alatti_epulet=0');
mysql_query('update bolygok b, (select distinct bolygo_id from cron_tabla where feladat=1) t
set b.maradt_epites_alatti_epulet=1
where b.id=t.bolygo_id');


//NPC
//KALOZOK
//ZANDAGORT
/*
$er_fl=mysql_query('select * from flottak where tulaj=-1 and statusz in ('.STATUSZ_ALLOMAS.','.STATUSZ_ALL.')');
while($flotta=mysql_fetch_array($er_fl)) {
	switch($flotta['zanda_statusz']) {
		case 5://jatekos bolygok ellen
			$er=mysql_query('select id from bolygok where letezik=1 and tulaj='.$flotta['zanda_cel_tulaj_szov'].' and pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2)<=pow(20000,2) order by rand() limit 1');
			//$er=mysql_query('select id from bolygok where letezik=1 and tulaj='.$flotta['zanda_cel_tulaj_szov'].' order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit 1');
			$aux=mysql_fetch_array($er);
			if ($aux) {
				mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_BOLYGORA.',cel_bolygo='.$aux[0].' where id='.$flotta['id']);
			} else {
				$er=mysql_query('select id from bolygok where letezik=1 order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit '.mt_rand(0,5).',1');
				$aux=mysql_fetch_array($er);
				if ($aux) {
					mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_BOLYGORA.',cel_bolygo='.$aux[0].' where id='.$flotta['id']);
				}
			}
		break;
		case 6://npc bolygok ellen
			$er=mysql_query('select id from bolygok where letezik=1 and tulaj=0 order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit '.mt_rand(0,5).',1');
			$aux=mysql_fetch_array($er);
			if ($aux) {
				mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_BOLYGORA.',cel_bolygo='.$aux[0].' where id='.$flotta['id']);
			}
		break;
		case 7://npc flottak ellen
			//$er=mysql_query('select id from flottak where tulaj=0 order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit '.mt_rand(0,5).',1');
			$er=mysql_query('select id from flottak where tulaj=0 order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit 1');
			$aux=mysql_fetch_array($er);
			if ($aux) {
				mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_FLOTTARA.',cel_flotta='.$aux[0].' where id='.$flotta['id']);
			}
		break;
		case 60://origo kozeli npc bolygok ellen
			//$er=mysql_query('select id from bolygok where letezik=1 and tulaj=0 and pow(x,2)+pow(y,2)<pow(10000,2) order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit '.mt_rand(0,5).',1');
			$er=mysql_query('select id from bolygok where letezik=1 and tulaj=0 and pow(x,2)+pow(y,2)<pow(5000,2) order by pow('.$flotta['x'].'-x,2)+pow('.$flotta['y'].'-y,2) limit '.mt_rand(0,5).',1');
			$aux=mysql_fetch_array($er);
			if ($aux) {
				mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_BOLYGORA.',cel_bolygo='.$aux[0].' where id='.$flotta['id']);
			} else {
				mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_MEGY_XY.',cel_x=-200,cel_y='.mt_rand(-200,200).',zanda_statusz=0 where id='.$flotta['id']);
			}
		break;
	}
}

$er_fl=mysql_query('select id from flottak where tulaj=-1 and egyenertek=0 and sebesseg=1000');//veletlenul megmaradt ures flottak
while($flotta=mysql_fetch_array($er_fl)) flotta_torles($flotta['id']);
*/


mysql_query('update ido set idopont_npc=idopont_npc+1');$szimlog_hossz_npc=round(1000*(microtime(true)-$mikor_indul));



//epito karrierek frissitese
if (substr($ora_perc,0,2)=='18') if (substr($rendszer_idopont['uccso_epito_frissites'],0,10)<$mai_nap) {
	mysql_query('update ido set uccso_epito_frissites="'.$mostani_datum.'"');
	//mernok: beepitett bolygo
	mysql_query('update userek u,(select distinct tulaj from bolygok where terulet_beepitett>=terulet and tulaj>0) t
set u.speci_1_1=1
where u.id=t.tulaj and u.karrier=1');
	//kereskedo, speki: napi eladott osszforgalom > 2mrd SHY
	$x=date('Y-m-d H:i:s',time()-3600*24);
	mysql_query('update userek u,(
select elado,sum(mennyiseg*arfolyam) as forg
from '.$database_mmog_nemlog.'.tozsdei_kotesek
where mikor>="'.$x.'" and elado>0
group by elado
having forg>2000000000
) t
set u.speci_1_2=1,u.speci_1_3=1
where u.id=t.elado and u.karrier=1');
}



//regio frissites
if (substr($ora_perc,0,2)=='19') if (substr($rendszer_idopont['uccso_regio_frissites'],0,10)<$mai_nap) {
	mysql_query('update ido set uccso_regio_frissites="'.$mostani_datum.'"');
	//tobbsegi regio
	mysql_query('update userek u, (
select tulaj
,left(group_concat(lpad(regio,2,"0") order by ipar desc),2)+0 as reg
from (select tulaj,regio,sum(iparmeret) as ipar from bolygok where tulaj>0 group by tulaj,regio) t
group by tulaj
) tt
set u.tobbsegi_regio=tt.reg
where u.id=tt.tulaj');
	//nem kerekedoknek ez az aktualis (es a valasztott is, h specializaciokor ne legyen gond)
	mysql_query('update userek
set aktualis_regio=tobbsegi_regio,aktualis_regio2=tobbsegi_regio,valasztott_regio=tobbsegi_regio,valasztott_regio2=tobbsegi_regio
where karrier!=1 or speci!=2');
}




//GALAKTIKUS KOZPONTI BANK
//regio szuzesseg
mysql_query('update regiok r,(
select r.id,count(b.id) as jatekos_bolygo
from regiok r
left join bolygok b on b.regio=r.id and b.tulaj>0
group by r.id
) t
set r.szuz=if(t.jatekos_bolygo>0,0,1)
where r.id=t.id');
//nem szuz regiok atarazasa
mysql_query('update tozsdei_arfolyamok tarf,(
select tk.regio,tk.termek_id,2*sum(if(vevo=0,0,tk.mennyiseg))/sum(tk.mennyiseg)-1+'.$inflacio.' as delta_ar
from '.$database_mmog_nemlog.'.tozsdei_kotesek tk
where tk.mikor>timestampadd(minute,-1440,now())
group by tk.termek_id
) t,regiok r
set tarf.ppm_arfolyam=greatest(round(tarf.ppm_arfolyam+66.19/1000000*tarf.ppm_arfolyam*t.delta_ar),1000000)
where tarf.termek_id=t.termek_id and tarf.regio=t.regio and tarf.regio=r.id and r.szuz=0');
//szuz regiok arazasa
mysql_query('update tozsdei_arfolyamok tarf, (
select tarf.termek_id,round(avg(tarf.ppm_arfolyam)) as ppm_arfolyam
from tozsdei_arfolyamok tarf, regiok r
where tarf.regio=r.id and r.szuz=0
group by tarf.termek_id
) t, regiok r
set tarf.ppm_arfolyam=t.ppm_arfolyam
where tarf.termek_id=t.termek_id and tarf.regio=r.id and r.szuz=1');
//ppm_arfolyam->arfolyam
mysql_query('update tozsdei_arfolyamok set arfolyam=greatest(round(ppm_arfolyam/1000000),1)');


//minden nap este 8-kor
if (substr($ora_perc,0,2)=='20') if (substr($rendszer_idopont['uccso_veteli_limit_frissites'],0,10)<$mai_nap) {
	//tozsdei veteli limit
	mysql_query('update ido set uccso_veteli_limit_frissites="'.$mostani_datum.'"');
	$effektiv_jatekosszam=mysql2num('select round(pow(sum(pontszam_exp_atlag),2)/sum(pow(pontszam_exp_atlag,2))) from userek where szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and id not in ('.implode(',',$specko_userek_listaja).')');if ($effektiv_jatekosszam==0) $effektiv_jatekosszam=1;
	mysql_query('update user_veteli_limit uvl,(select ht.id,round(avg(brutto_termeles)*96*7/4.8/'.$effektiv_jatekosszam.') as mai_varhato_ossztermeles_egy_fore
from '.$database_mmog_nemlog.'.hist_termelesek ht, '.$database_mmog_nemlog.'.hist_idopontok hi
where ht.idopont=hi.id and timestampdiff(day,hi.mikor,now())<7
group by ht.id) t,userek u
set uvl.maximum=round(if(u.karrier=1,if(u.speci=3,1.5,if(u.speci=2,1.4,1.1)),1)*t.mai_varhato_ossztermeles_egy_fore),
uvl.felhasznalt=0
where uvl.termek_id=t.id
and uvl.user_id=u.id');
	//penzatutalasi limit
	mysql_query('update userek set penz_adhato_max=round(pontszam_exp_atlag/10), penz_adott=0');
/*	$wau=mysql2num('select akt_7_nap from '.$database_mmog_nemlog.'.akt_stat order by id desc limit 1');if ($wau==0) $wau=1;
	$penzfogadasi_limit=mysql2num('select round(sum(x)/7) from (
select ht.id,avg(brutto_termeles)*96*7/4.8/'.$wau.'*0.1*tarf.arfolyam as x
from '.$database_mmog_nemlog.'.hist_termelesek ht, '.$database_mmog_nemlog.'.hist_idopontok hi, (select termek_id,avg(arfolyam) as arfolyam from tozsdei_arfolyamok group by termek_id) tarf
where ht.idopont=hi.id and timestampdiff(day,hi.mikor,now())<7 and tarf.termek_id=ht.id
group by ht.id) tt');
	mysql_query('update userek
set penz_adhato_max=round(pontszam_exp_atlag/10),
penz_adott=0,
penz_kaphato_max='.$penzfogadasi_limit.',
penz_kapott=0');*/
}



mysql_query('update ido set idopont_monetaris=idopont_monetaris+1');$szimlog_hossz_monetaris=round(1000*(microtime(true)-$mikor_indul));



//BOLYGO MORAL
mysql_query('
update bolygok
set moral=least(moral+if(vedelmi_bonusz<800,1+floor(vedelmi_bonusz/200),10),100)
where bolygo_id_mod='.$perc);

//EMBEREK
mysql_query('
update bolygo_ember be,bolygo_eroforras bkaja,bolygo_eroforras blakohely,bolygok b
set
be.pop=if(
	be.pop<1000,
	1000,
	if(
		least(bkaja.db,blakohely.db)>be.pop+10,
		round(be.pop+(least(bkaja.db,blakohely.db)-be.pop)/500*b.moral),
		if(
			least(bkaja.db,blakohely.db)<be.pop-10,
			round(be.pop+(least(bkaja.db,blakohely.db)-be.pop)*(0.15-b.moral/1000)),
			least(bkaja.db,blakohely.db)
		)
	)
)
where be.bolygo_id=bkaja.bolygo_id and bkaja.eroforras_id='.KAJA_ID.'
and be.bolygo_id=blakohely.bolygo_id and blakohely.eroforras_id='.LAKOHELY_ID.'
and be.bolygo_id=b.id
and b.bolygo_id_mod='.$perc.'
and b.tulaj!=0
');//npc nem
mysql_query('
update bolygo_ember be,bolygo_eroforras bkaja,bolygok b
set bkaja.db=if(bkaja.db>be.pop,bkaja.db-be.pop,0)
where be.bolygo_id=bkaja.bolygo_id and bkaja.eroforras_id='.KAJA_ID.'
and bkaja.bolygo_id_mod='.$perc.'
and be.bolygo_id=b.id and b.tulaj!=0
');//npc nem
mysql_query('
update bolygo_eroforras be,bolygok b set be.db=0
where be.bolygo_id_mod='.$perc.' and be.eroforras_id='.LAKOHELY_ID.'
and be.bolygo_id=b.id and b.tulaj!=0
');//npc nem
mysql_query('
update userek u,(
select u.id,u.nev,t.onep,max(bl.bolygolimit) as boli from userek u,(
select u.id,coalesce(sum(be.pop),0) as onep
from userek u
left join bolygok b on b.tulaj=u.id
left join bolygo_ember be on be.bolygo_id=b.id
group by u.id
) t, bolygolimitek bl
where u.id=t.id and bl.nepesseg<=t.onep
group by u.id
order by t.onep
) tt
set u.ossz_nepesseg=tt.onep, u.bolygo_limit=tt.boli
where u.id=tt.id
');
//TECH-SZINT
mysql_query('
update user_kutatasi_szint uksz,(
	select u.id,sum(be.pop) as nep
	from bolygo_ember be, bolygok b, userek u
	where be.bolygo_id=b.id and b.tulaj=u.id
	group by u.id
) t, userek u
set uksz.szint=greatest(uksz.szint,if(t.nep>=500000,6,if(t.nep>=340000,5,if(t.nep>=190000,4,if(t.nep>=140000,3,if(t.nep>=90000,2,if(t.nep>=45000,1,0)))))))
,u.techszint=greatest(u.techszint,if(t.nep>=500000,6,if(t.nep>=340000,5,if(t.nep>=190000,4,if(t.nep>=140000,3,if(t.nep>=90000,2,if(t.nep>=45000,1,0)))))))
where uksz.user_id=t.id and uksz.kf_id=1 and t.id=u.id');
$er=mysql_query('select id,nev,techszint,nyelv from userek where techszint!=techszint_ertesites');
while($aux=mysql_fetch_array($er)) techszint_uzenet($aux[0],$aux[1],$aux[2],'',$aux[3]);
//OSSZEOMLOTT
if (!$inaktiv_szerver) if ($vegjatek==0) {
	$er=mysql_query('select id,nev,email,nyelv from userek where ossz_nepesseg<15000 and osszeomlott=0 and timestampdiff(minute,mikortol,now())>120 and jelenlegi_terulet>0');//csak ha van bolygoja!!!
	while($aux=mysql_fetch_array($er)) osszeomlott_uzenet($aux[0],$aux[1],$aux[2],$aux[3]);
	mysql_query('update userek set osszeomlott=0 where ossz_nepesseg>25000');
}

//FEJLETLEN KEZBE KERULT BOLYGOK
$er=mysql_query('select b.id,bgy.gyar_id,coalesce(min(if(uksz.szint>=gyksz.szint,1,0))) from bolygo_gyar bgy, gyar_kutatasi_szint gyksz, user_kutatasi_szint uksz, bolygok b
where bgy.bolygo_id=b.id and bgy.aktiv_db>0 and gyksz.gyar_id=bgy.gyar_id and gyksz.kf_id=uksz.kf_id and uksz.user_id=b.tulaj
group by b.id,bgy.gyar_id
having coalesce(min(if(uksz.szint>=gyksz.szint,1,0)))=0');
while($aux=mysql_fetch_array($er)) {
	mysql_query('update bolygo_gyar set aktiv_db=0 where bolygo_id='.$aux[0].' and gyar_id='.$aux[1]);
	bgye_frissites($aux[0]);
}

//MUNKAERO
mysql_query('
update bolygo_eroforras berr,bolygo_ember be
set berr.db=round(be.pop/2)
where berr.bolygo_id=be.bolygo_id and berr.eroforras_id='.MUNKAERO_ID.'
and berr.bolygo_id_mod='.$perc.'
');

//TERMELES
mysql_query('update bolygo_eroforras set delta_db=0 where bolygo_id_mod='.$perc);

mysql_query('
update bolygo_eroforras be,(
	select be.bolygo_id,be.eroforras_id,round(sum(if(
		(gye.gyar_id=84 and gye.eroforras_id=60 and b.osztaly=3) or
		(gye.gyar_id=85 and gye.eroforras_id=61 and b.osztaly=2) or
		(gye.gyar_id=90 and gye.eroforras_id=63 and b.osztaly=1) or
		(gye.gyar_id=86 and gye.eroforras_id=62 and b.osztaly=5)
		,2*gye.io,gye.io
	)*bgy_eff.effektiv_db)) as delta
	from (
		select bgye.bolygo_id,bgye.gyar_id,min(if(bgye.io>=0,bgye.aktiv_db,if(bgye.aktiv_db*bgye.io+be.db/1000000000*bgye.reszarany>=0,bgye.aktiv_db,-be.db/1000000000*bgye.reszarany/bgye.io))) as effektiv_db
		from bolygo_gyar_eroforras bgye,bolygo_eroforras be
		where bgye.bolygo_id=be.bolygo_id and bgye.eroforras_id=be.eroforras_id and be.bolygo_id_mod='.$perc.'
		group by bgye.bolygo_id,bgye.gyar_id
	) bgy_eff,bolygo_eroforras be,gyar_eroforras gye,bolygok b
	where be.eroforras_id=gye.eroforras_id and be.bolygo_id=bgy_eff.bolygo_id and bgy_eff.gyar_id=gye.gyar_id
	and be.bolygo_id=b.id and b.tulaj!=0
	group by be.bolygo_id,be.eroforras_id
) deltatabla
set be.db=be.db+deltatabla.delta,
be.delta_db=deltatabla.delta
where be.bolygo_id=deltatabla.bolygo_id and be.eroforras_id=deltatabla.eroforras_id
');//npc nem
//AUTOMATIKUS FELTARAS
//nyers ko, nyers homok
mysql_query('
update bolygo_eroforras be,bolygok b
set db=if(
(be.eroforras_id=60 and b.osztaly=3) or
(be.eroforras_id=61 and b.osztaly=2)
,db+200,db+100)
where be.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and be.eroforras_id in (60,61) and b.tulaj!=0');//npc nem
//titanerc
mysql_query('
update bolygo_eroforras be,bolygok b
set db=if(b.osztaly=5,db+1000,db+500)
where be.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and be.eroforras_id=62 and b.tulaj!=0');//npc nem
//uranerc
mysql_query('
update bolygo_eroforras be,bolygok b
set db=if(b.osztaly=1,db+20,db+10)
where be.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and be.eroforras_id=63 and b.tulaj!=0');//npc nem
//MUNKAERO
mysql_query('
update bolygo_eroforras berr,bolygo_ember be
set berr.db=round(be.pop/2)
where berr.bolygo_id=be.bolygo_id and berr.eroforras_id='.MUNKAERO_ID.'
and berr.bolygo_id_mod='.$perc.'
');
//KEPZETT MUNKAERO
mysql_query('
update bolygo_eroforras be1, bolygo_eroforras be2, bolygok b
set be1.db=be2.delta_db,be2.db=0
where be1.bolygo_id_mod='.$perc.' and be1.eroforras_id='.KEPZETT_MUNKAERO_ID.'
and be2.bolygo_id=be1.bolygo_id and be2.eroforras_id='.KEPZETT_MUNKAHELY_ID.'
and be1.bolygo_id=b.id and b.tulaj!=0
');//npc nem
//UGYNOKOK -------------------> a 10 az valojaban KAPACITAS/DELTA_DB (vagyis hany kor alatt telik meg)
//ugynok karrier: be.delta_db=2*be.delta_db
mysql_query('update bolygok b, userek u, bolygo_eroforras be
set be.delta_db=2*be.delta_db
where b.bolygo_id_mod='.$perc.' and b.tulaj=u.id and u.karrier=3
and be.bolygo_id_mod='.$perc.' and be.bolygo_id=b.id and be.eroforras_id=76');
//kapacitasok osszegzese
mysql_query('update userek u, (
	select u.id,sum(10*be.delta_db) as kapacitas
	from userek u, bolygo_eroforras be, bolygok b
	where be.bolygo_id=b.id and b.tulaj=u.id and be.eroforras_id=76
	group by u.id
) ugynoktabla
set u.ugynok_kapacitas=ugynoktabla.kapacitas
where u.id=ugynoktabla.id');
//ugynokok osszegzese
mysql_query('update userek u
left join (select tulaj,sum(darab) as fo from ugynokcsoportok group by tulaj) ugynoktabla on u.id=ugynoktabla.tulaj
set u.ugynokok_szama=coalesce(ugynoktabla.fo,0)');
//uj ugynokcsoportok
mysql_query('
insert into ugynokcsoportok (tulaj,tulaj_szov,darab,bolygo_id)
select b.tulaj,b.tulaj_szov,round(be.delta_db*t.termeles_szazalek),b.id
from bolygok b, bolygo_eroforras be, (
select b.tulaj
,if(u.ugynok_kapacitas>u.ugynokok_szama
,if(u.ugynokok_szama+sum(be.delta_db)>u.ugynok_kapacitas
,(u.ugynok_kapacitas-u.ugynokok_szama)/sum(be.delta_db)
,1)
,0) as termeles_szazalek
from bolygo_eroforras be, bolygok b, userek u
where be.bolygo_id_mod='.$perc.' and be.eroforras_id=76
and be.bolygo_id=b.id and b.tulaj=u.id and b.tulaj!=0 and b.bolygo_id_mod='.$perc.'
group by b.tulaj
) t
where be.bolygo_id_mod='.$perc.' and be.eroforras_id=76
and be.bolygo_id=b.id and b.tulaj!=0 and b.tulaj=t.tulaj and b.bolygo_id_mod='.$perc.'
and round(be.delta_db*t.termeles_szazalek)>0
');
//ugynokok ujraosszegzese
mysql_query('update userek u
left join (select tulaj,sum(darab) as fo from ugynokcsoportok group by tulaj) ugynoktabla on u.id=ugynoktabla.tulaj
set u.ugynokok_szama=coalesce(ugynoktabla.fo,0)');
//TELEPORTTOLTES -------------------> a 100 az valojaban KAPACITAS/DELTA_DB (vagyis hany kor alatt telik meg)
mysql_query('
update bolygo_eroforras be, bolygok b
set be.db=least(be.db,100*be.delta_db)
where be.bolygo_id_mod='.$perc.' and be.eroforras_id=78
and be.bolygo_id=b.id and b.tulaj!=0
');//npc nem
//KOCSMAK, MATROZMORAL
mysql_query('
update bolygo_eroforras be, bolygok b
set be.db=be.delta_db
where be.bolygo_id_mod='.$perc.' and be.eroforras_id=75
and be.bolygo_id=b.id and b.tulaj!=0
');//npc nem
//K+F
mysql_query('
update userek u,(
	select b.tulaj,sum(be.db) as termeles
	from bolygok b, bolygo_eroforras be
	where be.eroforras_id=150 and be.bolygo_id=b.id and be.bolygo_id_mod='.$perc.'
	group by b.tulaj
) deltatabla
set u.kp=u.kp+deltatabla.termeles,u.megoszthato_kp=u.megoszthato_kp+deltatabla.termeles
where u.id=deltatabla.tulaj
');
mysql_query('update bolygo_eroforras set db=0 where eroforras_id=150 and bolygo_id_mod='.$perc);




//AUTO TRANSZFER
//erthetetlen okbol bekerult, kulonbozo tulajdonosnal levo bolygok kozti szallitasok torlese
mysql_query('delete cron_tabla_eroforras_transzfer from cron_tabla_eroforras_transzfer, bolygok b1, bolygok b2
where b1.id=cron_tabla_eroforras_transzfer.honnan_bolygo_id and b2.id=cron_tabla_eroforras_transzfer.hova_bolygo_id and b1.tulaj!=b2.tulaj');
//npc bolygokat is kivesszuk, ha netan vannak
mysql_query('delete cron_tabla_eroforras_transzfer from cron_tabla_eroforras_transzfer, bolygok b where b.id=cron_tabla_eroforras_transzfer.honnan_bolygo_id and b.tulaj=0');
mysql_query('delete cron_tabla_eroforras_transzfer from cron_tabla_eroforras_transzfer, bolygok b where b.id=cron_tabla_eroforras_transzfer.hova_bolygo_id and b.tulaj=0');
//autotranszfer
$er=mysql_query('
select c.honnan_bolygo_id,c.eroforras_id,be.db as keszlet,
c.darab,c.hova_bolygo_id,e.savszel_igeny,
b.tulaj,b2.tulaj,b.tulaj_szov,b2.tulaj_szov,b.uccso_emberi_tulaj,b.uccso_emberi_tulaj_szov
from
cron_tabla_eroforras_transzfer c, bolygok b, bolygo_eroforras be, eroforrasok e, userek u, bolygok b2
where c.honnan_bolygo_id=b.id and b.bolygo_id_mod='.$perc.'
and c.hova_bolygo_id=b2.id
and c.honnan_bolygo_id=be.bolygo_id and c.eroforras_id=be.eroforras_id
and c.eroforras_id=e.id
and b.tulaj=u.id and (u.premium=2 or u.premium_emelt>now())
order by c.honnan_bolygo_id
') or hiba(__FILE__,__LINE__,mysql_error());
$bolygo_id=0;
while($aux=mysql_fetch_array($er)) {
	if ($bolygo_id!=$aux[0]) {
		$toltes=mysql2num('select db from bolygo_eroforras where bolygo_id='.$aux[0].' and eroforras_id=78');
	}
	$bolygo_id=$aux[0];
	$ef_id=$aux[1];
	$mennyiseg=$aux[3];
	if ($aux[2]<$mennyiseg) $mennyiseg=$aux[2];
	if ($aux[5]*$toltes<$mennyiseg) $mennyiseg=$aux[5]*$toltes;
	if ($mennyiseg>0) {
		mysql_query('update bolygo_eroforras set db=if(db-'.$mennyiseg.'<0,0,db-'.$mennyiseg.') where bolygo_id='.$aux[0].' and eroforras_id='.$ef_id) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update bolygo_eroforras set db=db+'.$mennyiseg.' where bolygo_id='.$aux[4].' and eroforras_id='.$ef_id) or hiba(__FILE__,__LINE__,mysql_error());
		//toltes
		$delta_toltes=ceil($mennyiseg/$aux[5]);
		mysql_query('update bolygo_eroforras set db=if(db-'.$delta_toltes.'<0,0,db-'.$delta_toltes.') where bolygo_id='.$aux[0].' and eroforras_id=78') or hiba(__FILE__,__LINE__,mysql_error());
		$toltes-=$delta_toltes;if ($toltes<0) $toltes=0;
		insert_into_transzfer_log($aux[10],$aux[11],$aux[6],$aux[8],$aux[0],$aux[7],$aux[9],$aux[4],$ef_id,$mennyiseg,1);
	}
}
//AUTO TOZSDE
$datum=date('Y-m-d H:i:s');
$er=mysql_query('
select c.honnan_bolygo_id,c.eroforras_id,be.db as keszlet
,c.darab,0 as nulla1,e.savszel_igeny
,b.tulaj,0 as nulla2,b.tulaj_szov,0 as nulla3,b.uccso_emberi_tulaj,b.uccso_emberi_tulaj_szov
,u.megoszthato_kp
,if(c.regio_slot=2,u.aktualis_regio2,u.aktualis_regio) as regio
from cron_tabla_eroforras_transzfer c
inner join bolygok b on c.honnan_bolygo_id=b.id
inner join bolygo_eroforras be on c.honnan_bolygo_id=be.bolygo_id and c.eroforras_id=be.eroforras_id
inner join eroforrasok e on c.eroforras_id=e.id
inner join userek u on b.tulaj=u.id
where b.bolygo_id_mod='.$perc.'
and c.hova_bolygo_id=0
and (u.premium=2 or u.premium_emelt>now())
order by c.honnan_bolygo_id
') or hiba(__FILE__,__LINE__,mysql_error());
$bolygo_id=0;
while($aux=mysql_fetch_array($er)) {
	if ($bolygo_id!=$aux['honnan_bolygo_id']) {
		$toltes=mysql2num('select db from bolygo_eroforras where bolygo_id='.$aux[0].' and eroforras_id=78');
	}
	$bolygo_id=$aux['honnan_bolygo_id'];
	$ef_id=$aux[1];
	$mennyiseg=$aux[3];
	if ($ef_id<150) {//nyersi
		if ($aux['keszlet']<$mennyiseg) $mennyiseg=$aux['keszlet'];//keszlet
	} else {//KP
		if ($aux['megoszthato_kp']<$mennyiseg) $mennyiseg=$aux['megoszthato_kp'];//megoszthato
	}
	if ($aux[5]*$toltes<$mennyiseg) $mennyiseg=$aux[5]*$toltes;
	if ($mennyiseg>0) {
		$arfolyam=mysql2num('select arfolyam from tozsdei_arfolyamok where termek_id='.$ef_id.' and regio='.$aux['regio']);
		mysql_query('update userek set vagyon=vagyon+'.($mennyiseg*$arfolyam).' where id='.$aux[6]);
		if ($ef_id<150) {//nyersi
			mysql_query('update bolygo_eroforras set db=if(db-'.$mennyiseg.'>0,db-'.$mennyiseg.',0) where bolygo_id='.$bolygo_id.' and eroforras_id='.$ef_id);
		} else {//KP
			mysql_query('update userek set megoszthato_kp=if(megoszthato_kp-'.$mennyiseg.'>0,megoszthato_kp-'.$mennyiseg.',0) where id='.$aux[6]);
		}
		//toltes
		$delta_toltes=ceil($mennyiseg/$aux[5]);
		mysql_query('update bolygo_eroforras set db=if(db-'.$delta_toltes.'<0,0,db-'.$delta_toltes.') where bolygo_id='.$bolygo_id.' and eroforras_id=78');
		$toltes-=$delta_toltes;if ($toltes<0) $toltes=0;
		//
		mysql_select_db($database_mmog_nemlog);
		mysql_query('insert into tozsdei_kotesek (vevo,vevo_tulaj_szov,elado,elado_tulaj_szov,regio,termek_id,mennyiseg,arfolyam,mikor,vevo_bolygo_id,elado_bolygo_id) values(0,0,'.$aux[6].','.$aux[8].','.$aux['regio'].','.$ef_id.','.$mennyiseg.','.$arfolyam.',"'.$datum.'",0,'.$bolygo_id.')');
		mysql_select_db($database_mmog);
	}
}






//SZAPORODAS
mysql_query('
update bolygo_eroforras be,(
	select egyik.bolygo_id,egyik.eroforras_id,
	if(b.terulet>0,sum(masik.db*ff.coef/if(masik.eroforras_id>0,b.terulet/100000,1))*egyik.db/1000000-if(egyik.db<1000,1000-egyik.db,0),-egyik.db) as delta
	from bolygo_eroforras egyik,bolygo_eroforras masik,faj_faj ff,bolygok b
	where egyik.eroforras_id=ff.faj_id and masik.eroforras_id=ff.masik_faj_id and egyik.bolygo_id=masik.bolygo_id
	and b.id=egyik.bolygo_id
	and b.bolygo_id_mod='.$perc.'
	and b.tulaj!=0
	group by egyik.bolygo_id,egyik.eroforras_id
) deltatabla
set be.db=if(floor(be.db+deltatabla.delta)>0,floor(be.db+deltatabla.delta),0)
where be.bolygo_id=deltatabla.bolygo_id and be.eroforras_id=deltatabla.eroforras_id
');//npc nem




//QUEUE EPITKEZESEK

mysql_query('update bolygok set van_e_eplistaban_epulet=0');
mysql_query('update bolygok b, (select distinct bolygo_id from queue_epitkezesek) t
set b.van_e_eplistaban_epulet=1
where b.id=t.bolygo_id');

$er=mysql_query('select distinct q.bolygo_id,u.karrier,u.speci from queue_epitkezesek q, bolygok b, userek u where q.bolygo_id=b.id and b.tulaj=u.id and b.befagy_eplista=0');
while($aux=mysql_fetch_array($er)) {
	$er2=mysql_query('select q.*,gy.tipus from queue_epitkezesek q, gyarak gy where q.bolygo_id='.$aux[0].' and q.gyar_id=gy.id order by sorszam limit 1');
	$parancs=mysql_fetch_array($er2);
	if ($parancs['bolygo_id']>0) {//ha a kulso select ota toroltek egy epitkezest
		$er2=mysql_query('
select coalesce(min(if(gyek.db>0,mar.maradek_keszlet/gyek.db,999999)),0)
from gyar_epitesi_koltseg gyek,(
select be.eroforras_id as id,be.db+coalesce(t.fogy,0) as maradek_keszlet from
bolygo_eroforras be
left join (
select gye.eroforras_id,sum(if(gye.io<0,gye.io,0)*bgy_eff.effektiv_db) as fogy from
(select bgye.bolygo_id,bgye.gyar_id,min(if(bgye.io>=0,bgye.aktiv_db,if(bgye.aktiv_db*bgye.io+be.db/1000000000*bgye.reszarany>=0,bgye.aktiv_db,-be.db/1000000000*bgye.reszarany/bgye.io))) as effektiv_db
from bolygo_gyar_eroforras bgye,bolygo_eroforras be
where bgye.bolygo_id=be.bolygo_id and bgye.eroforras_id=be.eroforras_id and be.bolygo_id='.$parancs['bolygo_id'].'
group by bgye.gyar_id) bgy_eff,gyar_eroforras gye where bgy_eff.gyar_id=gye.gyar_id
group by gye.eroforras_id
) t on be.eroforras_id=t.eroforras_id
where be.bolygo_id='.$parancs['bolygo_id'].'
) mar
where mar.id=gyek.eroforras_id and gyek.tipus='.$parancs['tipus'].'	
		') or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		$hanyat=floor($aux2[0]);if ($hanyat>$parancs['darab']) $hanyat=$parancs['darab'];
		//teruleti korlatozas
		$bolygo=mysql2row('select * from bolygok where id='.$aux[0]);
		$gyartipus=mysql2row('select * from gyartipusok where id='.$parancs['tipus']);
		//ha (terulet_beepitett+db*gyar_terulet)/terraformaltsag*10000 > terulet, akkor csak részteljesítés:
		//db = floor((terulet/10000*terraformaltsag-terulet_beepitett)/gyar_terulet)
		if (($bolygo['terulet_beepitett']+$hanyat*$gyartipus['terulet'])/$bolygo['terraformaltsag']*10000>$bolygo['terulet']) {
			$hanyat=floor(($bolygo['terulet']/10000*$bolygo['terraformaltsag']-$bolygo['terulet_beepitett'])/$gyartipus['terulet']);
		}
		//
		if ($hanyat>=1) {
			mysql_query('update gyar_epitesi_koltseg gyek,bolygo_eroforras be set be.db=if(be.db>'.$hanyat.'*gyek.db,be.db-'.$hanyat.'*gyek.db,0) where gyek.tipus='.$parancs['tipus'].' and gyek.szint=1 and gyek.eroforras_id=be.eroforras_id and be.bolygo_id='.$parancs['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
			$er4=mysql_query('select * from gyar_epitesi_ido where tipus='.$parancs['tipus'].' and szint=1') or hiba(__FILE__,__LINE__,mysql_error());
			$aux4=mysql_fetch_array($er4);
			if ($aux['karrier']==1 and $aux['speci']==1) if (in_array($aux4['tipus'],$mernok_8_oras_gyarai)) $aux4['ido']=480;
			mysql_query('insert into cron_tabla (mikor_aktualis,feladat,bolygo_id,gyar_id,aktiv,darab,indulo_allapot) values("'.date('Y-m-d H:i:s',time()+60*$aux4['ido']).'",'.FELADAT_GYAR_EPIT.','.$parancs['bolygo_id'].','.$parancs['gyar_id'].','.$parancs['aktiv'].','.$hanyat.',1)')or hiba(__FILE__,__LINE__,mysql_error());
			if ($hanyat<$parancs['darab']) mysql_query('update queue_epitkezesek set darab=darab-'.$hanyat.' where id='.$parancs['id']) or hiba(__FILE__,__LINE__,mysql_error());
			else {
				mysql_query('delete from queue_epitkezesek where id='.$parancs['id']) or hiba(__FILE__,__LINE__,mysql_error());
				mysql_query('update queue_epitkezesek set sorszam=sorszam-1 where bolygo_id='.$parancs['bolygo_id'].' and sorszam>'.$parancs['sorszam']) or hiba(__FILE__,__LINE__,mysql_error());
			}
			bolygo_terulet_frissites($aux[0]);
		}
	}
}

mysql_query('update bolygok set maradt_eplistaban_epulet=0');
mysql_query('update bolygok b, (select distinct bolygo_id from queue_epitkezesek) t
set b.maradt_eplistaban_epulet=1
where b.id=t.bolygo_id');



mysql_query('update ido set idopont_termeles=idopont_termeles+1');$szimlog_hossz_termeles=round(1000*(microtime(true)-$mikor_indul));


//ugynok karrierek frissitese
if (substr($ora_perc,0,2)=='12') if (substr($rendszer_idopont['uccso_ugynok_frissites'],0,10)<$mai_nap) {
	mysql_query('update ido set uccso_ugynok_frissites="'.$mostani_datum.'"');
	//kem, szabotor: 10 hk
	mysql_query('update userek u,(select u.id,coalesce(sum(bgy.db),0) as hk
from userek u
inner join bolygok b on b.tulaj=u.id
inner join bolygo_gyar bgy on bgy.bolygo_id=b.id and bgy.gyar_id=87
where u.karrier=3
group by u.id
having hk>=10) t
set u.speci_3_1=1,u.speci_3_2=1
where u.id=t.id');
}

//ugynokok mozgatasa
mysql_query('update ugynokcsoportok ucs, bolygok b
set ucs.bolygo_id=if(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2)<=40000,b.id,0)
,ucs.cel_bolygo_id=if(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2)<=40000,0,b.id)
,ucs.x=if(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2)<=40000,b.x,round(ucs.x+(b.x-ucs.x)/sqrt(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2))*200))
,ucs.y=if(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2)<=40000,b.y,round(ucs.y+(b.y-ucs.y)/sqrt(pow(ucs.x-b.x,2)+pow(ucs.y-b.y,2))*200))
where ucs.cel_bolygo_id=b.id');
//40000=200^2=ugynoksebesseg^2 (felparsecben)

//idegen bolygon nem lehet elharitani (1,2)
mysql_query('update ugynokcsoportok ucs, bolygok b
set ucs.statusz=0
where ucs.bolygo_id=b.id and ucs.tulaj_szov!=b.tulaj_szov and ucs.statusz in (1,2)');
//sajat bolygon nem lehet kemkedni,szabotalni (3,4)
mysql_query('update ugynokcsoportok ucs, bolygok b
set ucs.statusz=0
where ucs.bolygo_id=b.id and ucs.tulaj_szov=b.tulaj_szov and ucs.statusz in (3,4)');

//koltopenz (shy_most) kiszamitasa es levonasa
mysql_query('lock tables ugynokcsoportok ucs write, ugynokcsoportok ucsr read, bolygok b write, bolygok br read, userek u write, userek ur read');
mysql_query('update ugynokcsoportok ucs set ucs.shy_most=0');
mysql_query('update ugynokcsoportok ucs, bolygok b, userek u, (
select ur.id,sum(ucsr.shy_per_akcio) as teljes_penzigeny
from ugynokcsoportok ucsr, bolygok br, userek ur
where ucsr.bolygo_id=br.id and br.bolygo_id_mod='.$perc.' and ucsr.statusz!=0 and ucsr.tulaj=ur.id
group by ur.id
) t
set ucs.shy_most=if(u.vagyon>=t.teljes_penzigeny,ucs.shy_per_akcio,round(ucs.shy_per_akcio/t.teljes_penzigeny*u.vagyon))
* case ucs.statusz
	when 1 then if(u.karrier=3 and u.speci=3,2,1)
	when 2 then if(u.karrier=3 and u.speci=3,2,1)
	when 3 then if(u.karrier=3 and u.speci=1,2,1)
	when 4 then if(u.karrier=3 and u.speci=2,2,1)
	else 1
end
where ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz!=0 and ucs.tulaj=u.id and u.id=t.id');
mysql_query('update userek u, (select ucsr.tulaj,sum(ucsr.shy_most) as teljes_koltes from ugynokcsoportok ucsr where ucsr.statusz in (3,4) group by ucsr.tulaj) t
set u.vagyon=if(u.vagyon>t.teljes_koltes,u.vagyon-t.teljes_koltes,0)
where u.id=t.tulaj');
mysql_query('unlock tables');


//aktiv elharitok
$r=mysql_query('select b.id,ucs.tulaj,coalesce(sum(ucs.darab),0),coalesce(sum(ucs.shy_most),0),b.nev,b.tulaj_szov,b.x,b.y
from ugynokcsoportok ucs, bolygok b
where b.letezik=1 and b.tulaj!=0 and ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz=2 group by b.id,ucs.tulaj');
$elozo_bolygo_id=0;
while($bolygo_tulaj=mysql_fetch_array($r)) if ($bolygo_tulaj[2]>0) {
	$aktiv_elharitok_szama=$bolygo_tulaj[2];
	$aktiv_elharitok_penze=$bolygo_tulaj[3];
	$aktiv_elharitok_penz_per_fo=$aktiv_elharitok_penze/$aktiv_elharitok_szama;
	if ($bolygo_tulaj[0]!=$elozo_bolygo_id) {
		$elozo_bolygo_id=$bolygo_tulaj[0];
		$aux=mysql2row('select coalesce(sum(if(ucs.statusz=0,ucs.darab*2,ucs.darab)),0),coalesce(sum(ucs.shy_most),0) from ugynokcsoportok ucs, bolygok b where ucs.bolygo_id=b.id and ucs.tulaj_szov!=b.tulaj_szov and b.id='.$bolygo_tulaj[0]);
		$ellenseges_ugynokok_szama=$aux[0];
		$ellenseges_ugynokok_penze=$aux[1];
	}
	if ($ellenseges_ugynokok_szama>0) {
		//aktiv elharito tenylegesen csak itt kolti el a penzet
		$levonando_penz=min($aktiv_elharitok_penze,$ellenseges_ugynokok_penze);
		mysql_query('update userek set vagyon=if(vagyon>'.$levonando_penz.',vagyon-'.$levonando_penz.',0) where id='.$bolygo_tulaj['tulaj']);
		//
		$likvidalasi_arany=$aktiv_elharitok_szama/$ellenseges_ugynokok_szama*2;//aktiv elharito hatekony
		if ($likvidalasi_arany>1) $likvidalasi_arany=1;
		mysql_query('update ugynokcsoportok set likvidalt=0 where bolygo_id='.$bolygo_tulaj[0]);
		mysql_query('update ugynokcsoportok ucs, bolygok b set ucs.likvidalt=if('.$likvidalasi_arany.'*if(ucs.statusz=0,0.5,1)*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab>=1,round('.$likvidalasi_arany.'*if(ucs.statusz=0,0.5,1)*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab),if(rand()<'.$likvidalasi_arany.'*if(ucs.statusz=0,0.5,1)*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab,1,0)) where ucs.bolygo_id=b.id and ucs.tulaj_szov!=b.tulaj_szov and b.id='.$bolygo_tulaj[0]);
		$r2=mysql_query('select ucs.*,u.nev as tulaj_nev from ugynokcsoportok ucs, userek u where ucs.likvidalt>0 and ucs.bolygo_id='.$bolygo_tulaj[0].' and ucs.tulaj=u.id');
		while($ucs=mysql_fetch_array($r2)) {
			//kopes
			if ($ucs['shy_most']>0) $kopesi_valoszinuseg=100-100/(1+$aktiv_elharitok_penze/$ucs['shy_most']);else $kopesi_valoszinuseg=100;
			if (mt_rand(0,99)<$kopesi_valoszinuseg) {
				$reszletes_info=' '.($ucs['statusz']==0?'Alvóügynökök':($ucs['statusz']==3?'Kémek':'Szabotőrök')).' voltak, akiket '.$ucs['tulaj_nev'].' küldött.';
				$reszletes_info_en=' They were '.($ucs['statusz']==0?'sleeper agents':($ucs['statusz']==3?'spies':'saboteurs')).' sent by '.$ucs['tulaj_nev'].'.';
			} else {
				$reszletes_info='';
				$reszletes_info_en='';
			}
			//jelentes az elharitonak
			rendszeruzenet($bolygo_tulaj['tulaj']
				,'Sikeres elhárítás',$bolygo_tulaj['nev'].' bolygódon elhárítottál '.$ucs['likvidalt'].' ügynököt.'.$reszletes_info
				,'Successful counterintelligence action','You have countered '.$ucs['likvidalt'].' agents on your planet '.$bolygo_tulaj['nev'].'.'.$reszletes_info_en
			);
			//jelentes a tamadonak
			rendszeruzenet($ucs['tulaj']
				,'Ellenséges elhárítás',$bolygo_tulaj['nev'].' bolygón '.$ucs['likvidalt'].' ügynöködet likvidálták.'
				,'Hostile counterintelligence action',$ucs['likvidalt'].' of your agents have been liquidated on planet '.$bolygo_tulaj['nev'].'.'
			);
		}
		mysql_query('update ugynokcsoportok set darab=if(darab>likvidalt,darab-likvidalt,0) where likvidalt>0 and bolygo_id='.$bolygo_tulaj[0]);
	}
	//kozelgo ellenseges ugynokok (10kpc-nel kozelebb, 100 perc uton belul, 6-7 kor mulva ernek ide)
	$kozelgo_ucs=mysql2row('select ucs.tulaj,u.nev as tulaj_nev,round(ucs.x/1000)*1000 as xx,round(ucs.y/1000)*1000 as yy,sum(ucs.darab) as ossz_darab from ugynokcsoportok ucs, userek u where ucs.cel_bolygo_id='.$bolygo_tulaj[0].' and ucs.tulaj_szov!='.$bolygo_tulaj['tulaj_szov'].' and ucs.tulaj=u.id and pow('.$bolygo_tulaj['x'].'-ucs.x,2)+pow('.$bolygo_tulaj['y'].'-ucs.y,2)<pow(20000,2) group by ucs.tulaj,xx,yy order by rand() limit 1');
	if ($kozelgo_ucs) if ($kozelgo_ucs['ossz_darab']>0) {
		$lebukasi_valoszinuseg=100-100/(1+$aktiv_elharitok_szama/$kozelgo_ucs['ossz_darab']/10);//1:1 arany eseten r=0.1 vagyis p=100-100/1.1 = 9,1% vagyis  6-7 kor alatt kb 50% a lebukas
		if (mt_rand(0,99)<$lebukasi_valoszinuseg) {
			rendszeruzenet($bolygo_tulaj['tulaj']
				,'Közelgő veszély',$bolygo_tulaj['nev'].' bolygódra ellenséges ügynökök közelednek. Megbízójuk: '.$kozelgo_ucs['tulaj_nev'].', létszámuk: '.$kozelgo_ucs['ossz_darab'].', várható érkezési idejük: kb '.ceil(sqrt(pow($kozelgo_ucs['xx']-$bolygo_tulaj['x'],2)+pow($kozelgo_ucs['yy']-$bolygo_tulaj['y'],2))/200).' perc.'
				,'Incoming agents','Hostile agents are approaching your planet '.$bolygo_tulaj['nev'].'. Commissioned by: '.$kozelgo_ucs['tulaj_nev'].', number: '.$kozelgo_ucs['ossz_darab'].', expected time of arrival: cca '.ceil(sqrt(pow($kozelgo_ucs['xx']-$bolygo_tulaj['x'],2)+pow($kozelgo_ucs['yy']-$bolygo_tulaj['y'],2))/200).' minutes.'
			);
		}
	}
}
mysql_query('delete from ugynokcsoportok where darab=0');



//passziv elharitok
$r=mysql_query('select b.id,ucs.tulaj,coalesce(sum(ucs.darab),0),coalesce(sum(ucs.shy_most),0),b.nev
from ugynokcsoportok ucs, bolygok b
where b.letezik=1 and b.tulaj!=0 and ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz=1 group by b.id,ucs.tulaj');
$elozo_bolygo_id=0;
while($bolygo_tulaj=mysql_fetch_array($r)) if ($bolygo_tulaj[2]>0) {
	$aktiv_elharitok_szama=$bolygo_tulaj[2];
	$aktiv_elharitok_penze=$bolygo_tulaj[3];
	$aktiv_elharitok_penz_per_fo=$aktiv_elharitok_penze/$aktiv_elharitok_szama*4;//passziv elharito olcson mukodik
	if ($bolygo_tulaj[0]!=$elozo_bolygo_id) {
		$elozo_bolygo_id=$bolygo_tulaj[0];
		$aux=mysql2row('select coalesce(sum(ucs.darab),0),coalesce(sum(ucs.shy_most),0) from ugynokcsoportok ucs, bolygok b where ucs.statusz!=0 and ucs.bolygo_id=b.id and ucs.tulaj_szov!=b.tulaj_szov and b.id='.$bolygo_tulaj[0]);
		$ellenseges_ugynokok_szama=$aux[0];//alvougynokok nem szamitanak bele
		$ellenseges_ugynokok_penze=$aux[1];
	}
	if ($ellenseges_ugynokok_szama>0) {
		//elharito tenylegesen csak itt kolti el a penzet
		$levonando_penz=min($aktiv_elharitok_penze,$ellenseges_ugynokok_penze);
		mysql_query('update userek set vagyon=if(vagyon>'.$levonando_penz.',vagyon-'.$levonando_penz.',0) where id='.$bolygo_tulaj['tulaj']);
		//
		$likvidalasi_arany=$aktiv_elharitok_szama/$ellenseges_ugynokok_szama;
		if ($likvidalasi_arany>1) $likvidalasi_arany=1;
		mysql_query('update ugynokcsoportok set likvidalt=0 where bolygo_id='.$bolygo_tulaj[0]);
		mysql_query('update ugynokcsoportok ucs, bolygok b set ucs.likvidalt=if('.$likvidalasi_arany.'*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab>=1,round('.$likvidalasi_arany.'*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab),if(rand()<'.$likvidalasi_arany.'*least(if(ucs.shy_most>0,'.$aktiv_elharitok_penz_per_fo.'/ucs.shy_most*ucs.darab,1),1)*ucs.darab,1,0)) where ucs.statusz!=0 and ucs.bolygo_id=b.id and ucs.tulaj_szov!=b.tulaj_szov and b.id='.$bolygo_tulaj[0]);//alvougynokok nem
		$r2=mysql_query('select * from ugynokcsoportok where likvidalt>0 and bolygo_id='.$bolygo_tulaj[0]);
		while($ucs=mysql_fetch_array($r2)) {
			//jelentes az elharitonak
			rendszeruzenet($bolygo_tulaj['tulaj']
				,'Sikeres elhárítás',$bolygo_tulaj['nev'].' bolygódon sikeresen elhárítottál.'
				,'Successful counterintelligence action','You have countered some agents on your planet '.$bolygo_tulaj['nev'].'.'
			);
			//jelentes a tamadonak
			rendszeruzenet($ucs['tulaj']
				,'Ellenséges elhárítás',$bolygo_tulaj['nev'].' bolygón '.$ucs['likvidalt'].' ügynöködet likvidálták.'
				,'Hostile counterintelligence action',$ucs['likvidalt'].' of your agents have been liquidated on planet '.$bolygo_tulaj['nev'].'.'
			);
		}
		mysql_query('update ugynokcsoportok set darab=if(darab>likvidalt,darab-likvidalt,0) where likvidalt>0 and bolygo_id='.$bolygo_tulaj[0]);
	}
}
mysql_query('delete from ugynokcsoportok where darab=0');


$most=date('Y-m-d H:i:s');
//kemek
$kemriportok=array();
$r=mysql_query('select b.id as bolygo_id,ucs.tulaj,ucs.feladat_domen,ucs.feladat_id,coalesce(sum(ucs.darab),0) as ossz_darab,coalesce(sum(ucs.shy_most),0) as ossz_shy_most,b.nev as bolygo_nev, ucs.tulaj_szov
from ugynokcsoportok ucs, bolygok b
where ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz=3 group by b.id,ucs.tulaj,ucs.feladat_domen,ucs.feladat_id');
while($ucs=mysql_fetch_array($r)) if ($ucs['ossz_darab']>0) if ($ucs['ossz_shy_most']>0) {
	$shy_per_fo=$ucs['ossz_shy_most']/$ucs['ossz_darab'];
	switch($ucs['feladat_domen']) {
		case 1://gyar
			$gyartipus=mysql2row('select * from gyartipusok where id='.$ucs['feladat_id']);
			$gyarak_szama=mysql2row('select sum(bgy.db),sum(bgy.aktiv_db) from bolygo_gyar bgy, gyarak gy where bgy.bolygo_id='.$ucs['bolygo_id'].' and bgy.gyar_id=gy.id and gy.tipus='.$ucs['feladat_id']);
			//ar kiszamitasa
			//$ar=$gyartipus['pontertek']/1000/10;//1 pont = 1000 shy
			//$ar=1;
			$ar=$gyartipus['kemkedes_ara'];
			//
			$valseg=$shy_per_fo/$ar;if ($valseg>1) $valseg=1;
			$n=$valseg*$ucs['ossz_darab'];
			if ($n<1) {
				if (mt_rand(0,99)<100*$n) $n=1;else $n=0;
			}
			if ($n>0) {
				$n=round($n);
				if (!$gyarak_szama) {//nincs
					$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],0,0,1);
				} else {
					if ($gyarak_szama[0]>$n) {//n+
						$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],$n,0,0);
					} else {//n
						$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],$gyarak_szama[0],$gyarak_szama[1],1);
					}
				}
			}
		break;
		case 2://eroforras
			$eroforras=mysql2row('select * from eroforrasok where id='.$ucs['feladat_id']);
			$eroforras_mennyisege=mysql2row('select db from bolygo_eroforras where bolygo_id='.$ucs['bolygo_id'].' and eroforras_id='.$ucs['feladat_id']);
			//ar kiszamitasa
			//$ar=$eroforras['pontertek']/1000/10;//1 pont = 1000 shy
			//$ar=1;
			$ar=$eroforras['kemkedes_ara']/100;//eroforrasoknal fillerben van tarolva az ar
			//
			$n=$ucs['ossz_shy_most']/$ar;
			if ($n>0) {
				$n=round($n);
				if (!$eroforras_mennyisege) {//nincs
					$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],0,0,1);
				} else {
					if ($eroforras_mennyisege[0]>$n) {//n+
						$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],$n,0,0);
					} else {//n
						$kemriportok[]=array($ucs['tulaj'],$ucs['tulaj_szov'],$ucs['bolygo_id'],0,$ucs['feladat_domen'],$ucs['feladat_id'],$eroforras_mennyisege[0],0,1);
					}
				}
			}
		break;
	}
}
if (count($kemriportok)>0) {
	mysql_select_db($database_mmog_nemlog);
	foreach($kemriportok as $kemriport) {
		mysql_query('insert into kemriportok (tulaj,tulaj_szov,bolygo_id,user_id,mikor,feladat_domen,feladat_id,darab,aktiv_darab,pontos) values('.$kemriport[0].','.$kemriport[1].','.$kemriport[2].','.$kemriport[3].',"'.$most.'",'.$kemriport[4].','.$kemriport[5].','.$kemriport[6].','.$kemriport[7].','.$kemriport[8].')');
	}
	mysql_select_db($database_mmog);
}



$egy_nap_mulva=date('Y-m-d H:i:s',time()+3600*24);
$egy_hettel_ezelott=date('Y-m-d H:i:s',time()-3600*24*7);
//szabotorok
$r=mysql_query('select b.id as bolygo_id,ucs.tulaj,ucs.feladat_domen,ucs.feladat_id,coalesce(sum(ucs.darab),0) as ossz_darab,coalesce(sum(ucs.shy_most),0) as ossz_shy_most,b.nev as bolygo_nev,b.tulaj as bolygo_tulaj,b.vedelmi_bonusz,u.uccso_szabotazs_mikor,if(ut.karrier=3 and ut.speci=3,1,0) as fantom_tamado,ut.helyezes
from ugynokcsoportok ucs, bolygok b, userek u, userek ut
where ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz=4 and b.tulaj=u.id and ucs.tulaj=ut.id
group by b.id,ucs.tulaj,ucs.feladat_domen,ucs.feladat_id');
while($ucs=mysql_fetch_array($r)) if ($ucs['ossz_darab']>0) if ($ucs['ossz_shy_most']>0) /*if ($ucs['vedelmi_bonusz']<800)*/ {
	$shy_per_fo=$ucs['ossz_shy_most']/$ucs['ossz_darab'];
	switch($ucs['feladat_domen']) {
		case 1://gyar inaktivalas 1 napra
			$gyartipus=mysql2row('select * from gyartipusok where id='.$ucs['feladat_id']);
			//ar kiszamitasa
			//$ar=$gyartipus['pontertek']/1000/10;//1 pont = 1000 shy
			$ar=$gyartipus['szabotazs_ara'];
			//
			$valseg=$shy_per_fo/$ar;if ($valseg>1) $valseg=1;
			$n=$valseg*$ucs['ossz_darab'];
			if ($n<1) {
				if (mt_rand(0,99)<100*$n) $n=1;else $n=0;
			}
			if ($n>0) {
				$n=round($n);
				//vedelmi bonusz alapjan korlat (figyelembe veve a user.uccso_szabotazs_mikor-t is)
				$letezo=mysql2num('select coalesce(sum(bgy.db),0) from bolygo_gyar bgy, gyarak gy where bgy.bolygo_id='.$ucs['bolygo_id'].' and bgy.gyar_id=gy.id and gy.tipus='.$gyartipus['id']);
				$epulo=mysql2num('select coalesce(sum(ct.darab),0) from cron_tabla ct, gyarak gy where ct.bolygo_id='.$ucs['bolygo_id'].' and ct.gyar_id=gy.id and gy.tipus='.$gyartipus['id']);
				if ($ucs['uccso_szabotazs_mikor']>$egy_hettel_ezelott) $max_n=$letezo+$epulo;
				else {
					if ($ucs['vedelmi_bonusz']<200) $max_n=$letezo+$epulo;
					elseif ($ucs['vedelmi_bonusz']<400) $max_n=round(0.80*($letezo+$epulo));
					elseif ($ucs['vedelmi_bonusz']<600) $max_n=round(0.60*($letezo+$epulo));
					elseif ($ucs['vedelmi_bonusz']<800) $max_n=round(0.40*($letezo+$epulo));
					else $max_n=0;
				}
				if ($n>$max_n) $n=$max_n;
				if ($n>0) {
					//fantom tamado egy bolygoja lebukik
					$forras_info='';$forras_info_en='';
					if ($fantom_lebukas) if ($ucs['fantom_tamado']) {
						$random_bolygo=mysql2row('select * from bolygok where tulaj='.$ucs['tulaj'].' order by rand() limit 1');
						if ($random_bolygo) list($forras_info,$forras_info_en)=fantom_bolygo_uzenet($random_bolygo,$ucs);
					}
					//n gyarat inaktivalni 24 orara
					mysql_query('insert into bolygo_gyartipus_szabotazs (bolygo_id,tipus,db,meddig) values('.$ucs['bolygo_id'].','.$gyartipus['id'].','.$n.',"'.$egy_nap_mulva.'")');
					//ertesitesek
					rendszeruzenet($ucs['tulaj']
						,'Sikeres szabotázs',$ucs['bolygo_nev'].' bolygón sikeresen szabotáltál '.$n.' '.$gyartipus['nev'].'-t.'
						,'Successful sabotage','You have sabotaged '.$n.' '.$gyartipus['nev_en'].' on planet '.$ucs['bolygo_nev'].'.'
					);
					rendszeruzenet($ucs['bolygo_tulaj']
						,'Ellenséges szabotázs',$ucs['bolygo_nev'].' bolygódon szabotáltak '.$n.' '.$gyartipus['nev'].'-t.'.$forras_info
						,'Hostile sabotage',$n.' '.$gyartipus['nev_en'].' have been sabotaged on your planet '.$ucs['bolygo_nev'].'.'.$forras_info_en
					);
					//uccso_szabotazs_mikor frissitese
					mysql_query('update userek set uccso_szabotazs_mikor="'.$most.'" where id='.$ucs['tulaj']);
				}
			}
		break;
		case 2://gyar robbantas
		break;
	}
}
//szabotalt gyarakat inaktivalni
$r=mysql_query('select bgytsz.bolygo_id,bgytsz.tipus,max(bgytsz.db) as szabotalt,sum(bgy.db) as osszesen,sum(bgy.aktiv_db) as aktiv
,min(gy.id) as min_gyar_id
,max(gy.id) as max_gyar_id
from bolygo_gyar bgy, gyarak gy, bolygo_gyartipus_szabotazs bgytsz
where bgy.bolygo_id=bgytsz.bolygo_id
and bgy.gyar_id=gy.id
and gy.tipus=bgytsz.tipus
group by bgytsz.bolygo_id,bgytsz.tipus
having osszesen<szabotalt+aktiv');
while($bgy=mysql_fetch_array($r)) {
	$inaktivalni_kell=$bgy['szabotalt']+$bgy['aktiv']-$bgy['osszesen'];
	if ($bgy['min_gyar_id']!=$bgy['max_gyar_id']) {
		$r2=mysql_query('select bgy.* from bolygo_gyar bgy, gyarak gy where bgy.bolygo_id='.$bgy['bolygo_id'].' and bgy.gyar_id=gy.id and gy.tipus='.$bgy['tipus'].' order by bgy.gyar_id');
		while(($inaktivalni_kell>0) and ($aux=mysql_fetch_array($r2))) {
			$most_inaktivalni_kell=$inaktivalni_kell;
			if ($most_inaktivalni_kell>$aux['aktiv_db']) $most_inaktivalni_kell=$aux['aktiv_db'];
			mysql_query('update bolygo_gyar set aktiv_db=if(aktiv_db>'.$most_inaktivalni_kell.',aktiv_db-'.$most_inaktivalni_kell.',0) where bolygo_id='.$bgy['bolygo_id'].' and gyar_id='.$aux['gyar_id']);
			$inaktivalni_kell-=$most_inaktivalni_kell;
		}
	} else {
		mysql_query('update bolygo_gyar set aktiv_db=if(aktiv_db>'.$inaktivalni_kell.',aktiv_db-'.$inaktivalni_kell.',0) where bolygo_id='.$bgy['bolygo_id'].' and gyar_id='.$bgy['max_gyar_id']);
	}
	bgye_frissites($bgy['bolygo_id']);
}
//lejart szabotazsokat torolni
mysql_query('delete from bolygo_gyartipus_szabotazs where meddig<now()');


//hanyszor-- es ha vege, statusz=0 meg a tobbit is kinullazni
mysql_query('update ugynokcsoportok ucs, bolygok b
set ucs.statusz=if(ucs.hanyszor=1,0,ucs.statusz)
,ucs.hanyszor=if(ucs.hanyszor>0,ucs.hanyszor-1,0)
,ucs.shy_per_akcio=if(ucs.hanyszor=1,0,ucs.shy_per_akcio)
,ucs.feladat_domen=if(ucs.hanyszor=1,0,ucs.feladat_domen)
,ucs.feladat_id=if(ucs.hanyszor=1,0,ucs.feladat_id)
where ucs.bolygo_id=b.id and b.bolygo_id_mod='.$perc.' and ucs.statusz!=0');

//ugynokok ujraosszegzese
mysql_query('update userek u, (
select tulaj,sum(darab) as fo from ugynokcsoportok group by tulaj
) ugynoktabla
set u.ugynokok_szama=ugynoktabla.fo
where u.id=ugynoktabla.tulaj');

mysql_query('update ido set idopont_felderites=idopont_felderites+1');$szimlog_hossz_felderites=round(1000*(microtime(true)-$mikor_indul));




/******************************************************** FLOTTAK ELEJE ******************************************************************/

//tech 5 alatti flottak nem tamadhatnak jatekos bolygot
mysql_query('update flottak f, userek u, bolygok b
set f.statusz='.STATUSZ_ALL.',f.cel_bolygo=0
where f.tulaj=u.id
and f.cel_bolygo=b.id
and f.statusz in (7,8,9,10)
and b.tulaj>0
and u.techszint<5');


//define('STATUSZ_ALLOMAS',1);
mysql_query('
update flottak f
inner join bolygok b on f.bolygo=b.id
left join diplomacia_statuszok dsz on dsz.ki=b.tulaj_szov and dsz.kivel=f.tulaj_szov
set f.statusz='.STATUSZ_ALL.'
where f.statusz='.STATUSZ_ALLOMAS.'
and b.tulaj_szov!=f.tulaj_szov
and coalesce(dsz.mi,0)!='.DIPLO_TESTVER.'
') or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('
update flottak f, bolygok b
set f.x=b.x, f.y=b.y
where f.bolygo=b.id and f.statusz='.STATUSZ_ALLOMAS.'
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_ALL',2);
mysql_query('
update flottak f
inner join bolygok b on f.x=b.x and f.y=b.y
left join diplomacia_statuszok dsz on dsz.ki=b.tulaj_szov and dsz.kivel=f.tulaj_szov
set f.statusz='.STATUSZ_ALLOMAS.', f.bolygo=b.id
where f.statusz='.STATUSZ_ALL.'
and (b.tulaj_szov=f.tulaj_szov or coalesce(dsz.mi,0)='.DIPLO_TESTVER.')
') or hiba(__FILE__,__LINE__,mysql_error());


//tech 5 alatti flottak csak sajat bolygo felett allomasozhatnak (kulonben a csatatiltas miatt vedeni tudnanak mas bolygokat)
mysql_query('update flottak f, userek u, bolygok b
set f.statusz='.STATUSZ_ALL.',f.bolygo=0
where f.tulaj=u.id
and f.bolygo=b.id
and f.statusz='.STATUSZ_ALLOMAS.'
and b.tulaj!=f.tulaj
and u.techszint<5');



//define('STATUSZ_PATROL_1',3);
mysql_query('
update flottak
set
statusz=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),'.STATUSZ_PATROL_2.',statusz),
x=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),cel_x,round(x+(cel_x-x)/sqrt(pow(cel_x-x,2)+pow(cel_y-y,2))*sebesseg)),
y=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),cel_y,round(y+(cel_y-y)/sqrt(pow(cel_x-x,2)+pow(cel_y-y,2))*sebesseg))
where statusz='.STATUSZ_PATROL_1.' and elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());
//define('STATUSZ_PATROL_2',4);
mysql_query('
update flottak
set
statusz=if(pow(bazis_x-x,2)+pow(bazis_y-y,2)<=pow(sebesseg,2),'.STATUSZ_PATROL_1.',statusz),
x=if(pow(bazis_x-x,2)+pow(bazis_y-y,2)<=pow(sebesseg,2),bazis_x,round(x+(bazis_x-x)/sqrt(pow(bazis_x-x,2)+pow(bazis_y-y,2))*sebesseg)),
y=if(pow(bazis_x-x,2)+pow(bazis_y-y,2)<=pow(sebesseg,2),bazis_y,round(y+(bazis_y-y)/sqrt(pow(bazis_x-x,2)+pow(bazis_y-y,2))*sebesseg))
where statusz='.STATUSZ_PATROL_2.' and elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_MEGY_XY',5);
mysql_query('
update flottak
set
statusz=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),'.STATUSZ_ALL.',statusz),
x=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),cel_x,round(x+(cel_x-x)/sqrt(pow(cel_x-x,2)+pow(cel_y-y,2))*sebesseg)),
y=if(pow(cel_x-x,2)+pow(cel_y-y,2)<=pow(sebesseg,2),cel_y,round(y+(cel_y-y)/sqrt(pow(cel_x-x,2)+pow(cel_y-y,2))*sebesseg))
where statusz='.STATUSZ_MEGY_XY.' and elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_VISSZA',11);
mysql_query('update flottak set statusz='.STATUSZ_ALL.' where bazis_bolygo=0 and statusz='.STATUSZ_VISSZA) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('
update flottak f,bolygok b
set
f.statusz=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_ALLOMAS.',f.statusz),
f.bolygo=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),f.bazis_bolygo,0),
f.x=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.x,round(f.x+(b.x-f.x)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg)),
f.y=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.y,round(f.y+(b.y-f.y)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg))
where f.bazis_bolygo=b.id and f.statusz='.STATUSZ_VISSZA.' and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_MEGY_BOLYGO',6);
//ha idegen bolygo ellen nem tamadsz, hanem mesz, akkor atrak megy_xy-ra
mysql_query('
update flottak f
inner join bolygok b on f.cel_bolygo=b.id
left join diplomacia_statuszok dsz on dsz.ki=b.tulaj_szov and dsz.kivel=f.tulaj_szov
set
f.statusz='.STATUSZ_MEGY_XY.',
f.cel_x=b.x,
f.cel_y=b.y
where f.statusz='.STATUSZ_MEGY_BOLYGO.'
and b.tulaj_szov!=f.tulaj_szov
and coalesce(dsz.mi,0)!='.DIPLO_TESTVER.'
') or hiba(__FILE__,__LINE__,mysql_error());

//ha sajat vagy szovitars vagy testverszovi bolygora megy, akkor allomasozik a celba eresnel
mysql_query('
update flottak f
inner join bolygok b on f.cel_bolygo=b.id
left join diplomacia_statuszok dsz on dsz.ki=b.tulaj_szov and dsz.kivel=f.tulaj_szov
set
f.statusz=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_ALLOMAS.',f.statusz),
f.bolygo=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),f.cel_bolygo,0),
f.x=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.x,round(f.x+(b.x-f.x)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg)),
f.y=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.y,round(f.y+(b.y-f.y)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg))
where f.statusz='.STATUSZ_MEGY_BOLYGO.'
and (b.tulaj_szov=f.tulaj_szov or coalesce(dsz.mi,0)='.DIPLO_TESTVER.')
and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_TAMAD_BOLYGORA',7);
mysql_query('
update flottak f,bolygok b
set
f.statusz=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_TAMAD_BOLYGOT.',f.statusz),
f.x=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.x,round(f.x+(b.x-f.x)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg)),
f.y=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.y,round(f.y+(b.y-f.y)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg))
where f.cel_bolygo=b.id and f.statusz='.STATUSZ_TAMAD_BOLYGORA.' and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_RAID_BOLYGORA',9);
mysql_query('
update flottak f,bolygok b
set
f.statusz=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_RAID_BOLYGOT.',f.statusz),
f.x=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.x,round(f.x+(b.x-f.x)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg)),
f.y=if(pow(b.x-f.x,2)+pow(b.y-f.y,2)<=pow(f.sebesseg,2),b.y,round(f.y+(b.y-f.y)/sqrt(pow(b.x-f.x,2)+pow(b.y-f.y,2))*f.sebesseg))
where f.cel_bolygo=b.id and f.statusz='.STATUSZ_RAID_BOLYGORA.' and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//cel_flotta kolcsonosseg
mysql_query('update flottak f1, flottak f2
set
f1.x=round((f1.sebesseg*f2.x+f2.sebesseg*f1.x)/(f1.sebesseg+f2.sebesseg))
,f1.y=round((f1.sebesseg*f2.y+f2.sebesseg*f1.y)/(f1.sebesseg+f2.sebesseg))
,f2.x=round((f1.sebesseg*f2.x+f2.sebesseg*f1.x)/(f1.sebesseg+f2.sebesseg))
,f2.y=round((f1.sebesseg*f2.y+f2.sebesseg*f1.y)/(f1.sebesseg+f2.sebesseg))
where f1.cel_flotta=f2.id and f2.cel_flotta=f1.id
and f1.id!=f2.id
and f1.statusz in (12,13,14) and f2.statusz in (12,13,14)
and pow(f2.x-f1.x,2)+pow(f2.y-f1.y,2)<=pow(f1.sebesseg+f2.sebesseg,2)');

//define('STATUSZ_MEGY_FLOTTAHOZ',12);
mysql_query('
update flottak f,flottak f2
set
f.statusz=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_ALL.',f.statusz),
f.x=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.x,round(f.x+(f2.x-f.x)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg)),
f.y=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.y,round(f.y+(f2.y-f.y)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg))
where f.cel_flotta=f2.id and f.statusz='.STATUSZ_MEGY_FLOTTAHOZ.' and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_TAMAD_FLOTTARA',13);
mysql_query('
update flottak f,flottak f2
set
f.statusz=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),'.STATUSZ_TAMAD_FLOTTAT.',f.statusz),
f.x=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.x,round(f.x+(f2.x-f.x)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg)),
f.y=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.y,round(f.y+(f2.y-f.y)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg))
where f.cel_flotta=f2.id and f.statusz='.STATUSZ_TAMAD_FLOTTARA.' and f.elkerules=0
') or hiba(__FILE__,__LINE__,mysql_error());

//define('STATUSZ_TAMAD_BOLYGOT',8);
//define('STATUSZ_RAID_BOLYGOT',10);

//define('STATUSZ_TAMAD_FLOTTAT',14);
mysql_query('
update flottak f,flottak f2
set
f.statusz=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f.statusz,'.STATUSZ_TAMAD_FLOTTARA.'),
f.x=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.x,round(f.x+(f2.x-f.x)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg)),
f.y=if(pow(f2.x-f.x,2)+pow(f2.y-f.y,2)<=pow(f.sebesseg,2),f2.y,round(f.y+(f2.y-f.y)/sqrt(pow(f2.x-f.x,2)+pow(f2.y-f.y,2))*f.sebesseg))
where f.cel_flotta=f2.id and f.statusz='.STATUSZ_TAMAD_FLOTTAT.'
') or hiba(__FILE__,__LINE__,mysql_error());



//sajat, szovi, testverszovi es mnt elleni tamadasok leallitasa
mysql_query('update flottak f
inner join bolygok b on f.cel_bolygo=b.id and f.statusz in ('.STATUSZ_TAMAD_BOLYGORA.','.STATUSZ_TAMAD_BOLYGOT.','.STATUSZ_RAID_BOLYGORA.','.STATUSZ_RAID_BOLYGOT.')
left join diplomacia_statuszok dsz on f.tulaj_szov=dsz.ki and b.tulaj_szov=dsz.kivel
set f.statusz='.STATUSZ_MEGY_BOLYGO.'
where f.tulaj_szov=b.tulaj_szov or coalesce(dsz.mi,0) in ('.DIPLO_TESTVER.','.DIPLO_MNT.')');
mysql_query('update flottak f
inner join flottak f2 on f.cel_flotta=f2.id and f.statusz in ('.STATUSZ_TAMAD_FLOTTARA.','.STATUSZ_TAMAD_FLOTTAT.')
left join diplomacia_statuszok dsz on f.tulaj_szov=dsz.ki and f2.tulaj_szov=dsz.kivel
set f.statusz='.STATUSZ_MEGY_FLOTTAHOZ.'
where f.tulaj_szov=f2.tulaj_szov or coalesce(dsz.mi,0) in ('.DIPLO_TESTVER.','.DIPLO_MNT.')');



//feregjaratok
mysql_query('update feregjaratok fj, flottak f
set f.x=round(fj.cel_x+200*rand()-100),f.y=round(fj.cel_y+200*rand()-100)
where fj.forras_x=f.x and fj.forras_y=f.y and f.statusz='.STATUSZ_ALL);




//csak paros koordinatak lehetnek!!!
mysql_query('update flottak set x=round(x/2)*2, y=round(y/2)*2');


//hexak meghatarozasa, utana annak fuggvenyeben moralvaltozas (lasd lentebb)
//left join, hogy a nagy hexakoron kivuli flottaknak 0 legyen a hexa_voronoi_bolygo_id-ja (es ne megmaradjon a korabbi)
mysql_query('update flottak f
inner join (select id,x,y,
@hx:=floor(x/217),
@hy:=floor(y/125),
@hatarparitas:=(abs(@hx%2)+abs(@hy%2))%2,
@xmar:=x-217*@hx,
@ymar:=y-125*@hy,
@m:=if(@hatarparitas=0,-1,1)*217/125,
@N:=if(@ymar-125/2>@m*(@xmar-217/2),1,0),
@hx+if(@hatarparitas=1,1-@N,@N) as hexa_x,
if(@hy%2=0,round(@hy/2)+@N,round((@hy+1)/2)) as hexa_y
from flottak) t on f.id=t.id
left join hexak h on t.hexa_x=h.x and t.hexa_y=h.y
set f.hexa_x=t.hexa_x, f.hexa_y=t.hexa_y, f.hexa_voronoi_bolygo_id=coalesce(h.voronoi_bolygo_id,0)');

//sajat_teruleten,idegen_teruleten
mysql_query('update flottak f
left join bolygok b on b.id=f.hexa_voronoi_bolygo_id
set f.sajat_teruleten=if(b.tulaj is not null and f.tulaj=b.tulaj,1,0)
,f.idegen_teruleten=if(b.tulaj is not null,if(f.tulaj!=b.tulaj and b.tulaj>0,1,0),1)');


mysql_query('update ido set idopont_flottak=idopont_flottak+1');$szimlog_hossz_flottak=round(1000*(microtime(true)-$mikor_indul));
/******************************************************** FLOTTAK VEGE ******************************************************************/


/******************************************************** FLOTTAMORAL ELEJE ******************************************************************/
//kocsmaszazalek kiszamitasa: 0..100 kozott
mysql_query('
update bolygok b,(
	select b.id,if(be.db>0,if(sum(fh.ossz_hp*h.ar)>10000*be.db,round(be.db/sum(fh.ossz_hp*h.ar)*1000000),100),0) as kocsmasz
	from flotta_hajo fh, hajok h, flottak f, bolygok b, bolygo_eroforras be
	where fh.flotta_id=f.id and fh.hajo_id=h.id and f.bolygo=b.id and f.bolygo=be.bolygo_id and be.eroforras_id=75 and f.statusz='.STATUSZ_ALLOMAS.' and fh.hajo_id!='.HAJO_TIPUS_SZONDA.'
	group by f.bolygo
) t
set b.kocsmaszazalek=t.kocsmasz
where b.id=t.id
');

//100%-os kocsmaszazaleknal percenkent +1%, vagyis kb masfel ora alatt lehet fullosra tolteni egy flottat (10-szer gyorsabban toltodik, mint fogy)
mysql_query('
update flotta_hajo fh,(
select fh.flotta_id, fh.hajo_id, b.kocsmaszazalek
from flotta_hajo fh, flottak f, bolygok b
where fh.flotta_id=f.id and f.bolygo=b.id and f.statusz='.STATUSZ_ALLOMAS.' and fh.hajo_id!='.HAJO_TIPUS_SZONDA.'
group by fh.flotta_id, fh.hajo_id
) t
set fh.moral=least(fh.moral+t.kocsmaszazalek,10000)
where fh.flotta_id=t.flotta_id and fh.hajo_id=t.hajo_id
');

//percenkent -0,1%, kiveve allomasozas, szonda, fekete (nem piros) terulet (sajat, szovi, testver, mnt, npc), npc, Zanda, kalózok, Zharg'al, olyan hexa, amihez nincs bolygo (inner join)
//es kiveve, ha a hexa tulaja fantom (mert ekkor nem is piros)
//ha a flottanak jar a fejvadasz bonusz (f.fejvadasz_bonusz=1), akkor csak haboru eseten van csokkenes
//ha a bolygo bekebiroe, akkor a moralcsokkenes 3-szoros
mysql_query('
update flotta_hajo fh
inner join flottak f on fh.flotta_id=f.id
inner join bolygok b on b.id=f.hexa_voronoi_bolygo_id
left join userek u on u.id=b.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel=b.tulaj_szov
set fh.moral=greatest(fh.moral-if(coalesce(u.karrier,0)=4 and coalesce(u.speci,0)=1,30,10),0)
where f.statusz!='.STATUSZ_ALLOMAS.'
and fh.hajo_id!='.HAJO_TIPUS_SZONDA.'
and b.tulaj_szov!=f.tulaj_szov
and coalesce(dsz.mi,0)!='.DIPLO_TESTVER.'
and coalesce(dsz.mi,0)!='.DIPLO_MNT.'
and b.tulaj!=0
and b.letezik=1
and (coalesce(u.karrier,0)!=3 or coalesce(u.speci,0)!=3)
and (f.fejvadasz_bonusz=0 or coalesce(dsz.mi,0)='.DIPLO_HADI.')
and f.tulaj!=0 and f.tulaj!=-1 and f.tulaj_szov!='.KALOZOK_HADA_SZOV_ID.' and f.tulaj!='.ZHARG_AL_TANITVANYAI_USER_ID.'
');

//barmi van, 0 es 100% kozott maradjon
mysql_query('update flotta_hajo set moral=greatest(least(moral,10000),0)');



//csak szondas es nulla moralos flottak ne tamadjanak/portyazzanak (egyszerubb itt atirni, mint az ostromnal, ahol tobbszor is le vannak valogatva az erintett flottak
mysql_query('update flottak f,(select f.id,coalesce(round(sum(fh.ossz_hp*fh.moral)/sum(fh.ossz_hp)),0) as moral_szonda_nelkul
from flottak f, flotta_hajo fh, hajok h
where f.id=fh.flotta_id and fh.hajo_id=h.id
and h.id!='.HAJO_TIPUS_SZONDA.'
and h.id!='.HAJO_TIPUS_KOORDI.'
and h.id!='.HAJO_TIPUS_OHS.'
group by f.id) t
set f.moral_szonda_nelkul=t.moral_szonda_nelkul
where f.id=t.id');
mysql_query('update flottak f
set f.statusz='.STATUSZ_ALL.'
where (f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.') and f.moral_szonda_nelkul=0');


mysql_query('update ido set idopont_flottamoral=idopont_flottamoral+1');$szimlog_hossz_flottamoral=round(1000*(microtime(true)-$mikor_indul));
/******************************************************** FLOTTAMORAL VEGE ******************************************************************/



/******************************************************** CSATAK ELEJE ******************************************************************/

//0. flotta tp
mysql_query('update flottak f, userek u set f.tp=u.tp where f.uccso_parancs_by=u.id');
mysql_query('update flottak f, userek u set f.tp=greatest(f.tp,u.tp) where f.tulaj=u.id');
mysql_query('update flottak f, resz_flotta_aux rfa, userek u set f.tp=greatest(f.tp,u.tp) where f.id=rfa.flotta_id and rfa.user_id=u.id');

//1. csatak
mysql_query('delete from csatak');
mysql_query('insert into csatak (x,y,zanda)
select x,y,if(sum(tulaj=-1)>0,1,0)
from flottak
group by x,y
having count(distinct tulaj_szov)>1');

//2. csata_flotta
mysql_query('truncate csata_flotta');
mysql_query('insert into csata_flotta (csata_id,flotta_id,tulaj,tulaj_szov,tulaj_nev,tulaj_szov_nev,nev,kozos,iranyito,iranyito_nev,tp,iranyito_karrier,iranyito_speci,iranyito_rang,kezdo)
select cs.id,f.id,f.tulaj,f.tulaj_szov,if(f.tulaj=-1,"Zandagort",coalesce(u.nev,"")),coalesce(sz.nev,""),f.nev,f.kozos,f.uccso_parancs_by,coalesce(ui.nev,""),f.tp,ui.karrier,ui.speci,ui.rang,u.techszint<5
from csatak cs
inner join flottak f on cs.x=f.x and cs.y=f.y
left join userek u on f.tulaj=u.id
left join szovetsegek sz on f.tulaj_szov=sz.id
left join userek ui on f.uccso_parancs_by=ui.id');

//2,5. TE/VE-frissites, egyenertek-szamitas
$er=mysql_query('select flotta_id from csata_flotta');
while($aux=mysql_fetch_array($er)) {
	flotta_minden_frissites($aux[0]);
}
mysql_query('update csata_flotta csf, flottak f
set csf.egyenertek_elotte=f.egyenertek
where csf.flotta_id=f.id');
mysql_query('update csatak cs,(select csata_id,sum(egyenertek_elotte) as ossz from csata_flotta group by csata_id) t
set cs.resztvett_egyenertek=t.ossz
where cs.id=t.csata_id');

//3. csata_flottamatrix
mysql_query('truncate csata_flottamatrix');
//kozvetlen tamadasok
mysql_query('insert into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csf1.csata_id,f1.id,f2.id
from csata_flotta csf1
inner join csata_flotta csf2 on csf2.csata_id=csf1.csata_id
inner join flottak f1 on f1.id=csf1.flotta_id
inner join flottak f2 on f2.id=csf2.flotta_id
left join diplomacia_statuszok dsz on dsz.ki=f1.tulaj_szov and dsz.kivel=f2.tulaj_szov
where
greatest(
if(f1.statusz='.STATUSZ_TAMAD_FLOTTAT.' and f1.cel_flotta=f2.id,1,if(f2.statusz='.STATUSZ_TAMAD_FLOTTAT.' and f2.cel_flotta=f1.id,1,0)),
if(f1.statusz in ('.STATUSZ_TAMAD_BOLYGOT.','.STATUSZ_RAID_BOLYGOT.') and f2.statusz='.STATUSZ_ALLOMAS.' and f1.cel_bolygo=f2.bolygo,1,if(f2.statusz in ('.STATUSZ_TAMAD_BOLYGOT.','.STATUSZ_RAID_BOLYGOT.') and f1.statusz='.STATUSZ_ALLOMAS.' and f2.cel_bolygo=f1.bolygo,1,0)),
if(coalesce(dsz.mi='.DIPLO_HADI.',0),1,0)
)=1');
//elso koros tarsbepakolas
mysql_query('insert ignore into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csfm.csata_id,csfm.masik_flotta_id,fu.id
from csata_flottamatrix csfm
inner join flottak fr on fr.id=csfm.egyik_flotta_id
inner join flottak frm on frm.id=csfm.masik_flotta_id
inner join csata_flotta csfu on csfu.csata_id=csfm.csata_id
inner join flottak fu on fu.id=csfu.flotta_id and fu.id!=csfm.egyik_flotta_id and fu.id!=csfm.masik_flotta_id
left join diplomacia_statuszok dsz on dsz.ki=fr.tulaj_szov and dsz.kivel=fu.tulaj_szov
left join diplomacia_statuszok dsz2 on dsz2.ki=frm.tulaj_szov and dsz2.kivel=fu.tulaj_szov
where (fr.tulaj_szov=fu.tulaj_szov or coalesce(dsz.mi,0)='.DIPLO_TESTVER.') and (coalesce(dsz2.mi,0)!='.DIPLO_MNT.')');
//ez kell, hogy a masodik koros bepakolas lefedjen minden masodrendu esetet
mysql_query('insert ignore into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csata_id,masik_flotta_id,egyik_flotta_id
from csata_flottamatrix');
//masodik koros tarsbepakolas (elvileg vegtelen kor kene)
mysql_query('insert ignore into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csfm.csata_id,csfm.masik_flotta_id,fu.id
from csata_flottamatrix csfm
inner join flottak fr on fr.id=csfm.egyik_flotta_id
inner join flottak frm on frm.id=csfm.masik_flotta_id
inner join csata_flotta csfu on csfu.csata_id=csfm.csata_id
inner join flottak fu on fu.id=csfu.flotta_id and fu.id!=csfm.egyik_flotta_id and fu.id!=csfm.masik_flotta_id
left join diplomacia_statuszok dsz on dsz.ki=fr.tulaj_szov and dsz.kivel=fu.tulaj_szov
left join diplomacia_statuszok dsz2 on dsz2.ki=frm.tulaj_szov and dsz2.kivel=fu.tulaj_szov
where (fr.tulaj_szov=fu.tulaj_szov or coalesce(dsz.mi,0)='.DIPLO_TESTVER.') and (coalesce(dsz2.mi,0)!='.DIPLO_MNT.')');
//hogy szimmetrikus (redundans) matrix legyen
mysql_query('insert ignore into csata_flottamatrix (csata_id,egyik_flotta_id,masik_flotta_id)
select csata_id,masik_flotta_id,egyik_flotta_id
from csata_flottamatrix');


//tech 5 alattiak csak npc-vel csatazhatnak (kiveve mas jatekos teruleten)
mysql_query('delete csfm from
csata_flottamatrix csfm, csata_flotta csf1, csata_flotta csf2, userek u1, userek u2, flottak f1, flottak f2
where csf1.csata_id=csfm.csata_id and csf1.flotta_id=csfm.egyik_flotta_id
and csf2.csata_id=csfm.csata_id and csf2.flotta_id=csfm.masik_flotta_id
and u1.id=csf1.tulaj
and u2.id=csf2.tulaj
and f1.id=csf1.flotta_id
and f2.id=csf2.flotta_id
and u1.id=f1.tulaj
and u2.id=f2.tulaj
and if(u1.techszint<5 and f1.sajat_teruleten=1,1,0)+if(u2.techszint<5 and f2.sajat_teruleten=1,1,0)>0');


//ures csatakat kidobalni (ahol egy pozin eltero tulaj_szov van, de nincs koztuk osszecsapas
$er=mysql_query('select cs.id
from csatak cs
left join csata_flottamatrix csfm on cs.id=csfm.csata_id
group by cs.id
having count(csfm.egyik_flotta_id)=0');
while($aux=mysql_fetch_array($er)) {
	mysql_query('delete from csatak where id='.$aux[0]);
	mysql_query('delete from csata_flotta where csata_id='.$aux[0]);
}

$er_van_e_csata=mysql_query('select count(1) from csatak');
$aux_van_e_csata=mysql_fetch_array($er_van_e_csata);
if ($aux_van_e_csata[0]>0) {


mysql_query('update flotta_hajo set effektiv_moral=moral');//alapertelmezes
if ($vegjatek==1) {//effektiv_moral = 100%, ha Zanda is ott van
	$er=mysql_query('select csf.flotta_id from csatak cs, csata_flotta csf where cs.id=csf.csata_id and cs.zanda=1');
	while($aux=mysql_fetch_array($er)) {
		mysql_query('update flotta_hajo set effektiv_moral=10000 where flotta_id='.$aux[0]);
	}
} elseif ($vegjatek==2) {//effektiv_moral = 100%, mindig
	mysql_query('update flotta_hajo set effektiv_moral=10000');
}


//4. csata_flotta_hajo
mysql_query('truncate csata_flotta_hajo');
mysql_query('insert into csata_flotta_hajo (csata_id,flotta_id,hajo_id,ossz_hp_elotte)
select csf.csata_id,csf.flotta_id,fh.hajo_id,fh.ossz_hp
from csata_flotta csf, flotta_hajo fh
where csf.flotta_id=fh.flotta_id');

//5. normalo_osszeg
mysql_query('update csata_flotta_hajo tamado_csfh,(
select tamado_csfh.csata_id,tamado_csfh.flotta_id,tamado_csfh.hajo_id,sum(vedo_fh.ossz_hp*vedo_fh.valodi_hp*hh.coef*hh.coef) as uj_normalo_osszeg
from csata_flotta_hajo tamado_csfh, flotta_hajo tamado_fh, csata_flotta_hajo vedo_csfh, flotta_hajo vedo_fh, hajo_hajo hh, csata_flottamatrix csfm
where csfm.csata_id=tamado_csfh.csata_id and csfm.csata_id=vedo_csfh.csata_id
and csfm.egyik_flotta_id=tamado_csfh.flotta_id and csfm.egyik_flotta_id=tamado_fh.flotta_id
and csfm.masik_flotta_id=vedo_csfh.flotta_id and csfm.masik_flotta_id=vedo_fh.flotta_id
and tamado_csfh.hajo_id=tamado_fh.hajo_id
and vedo_csfh.hajo_id=vedo_fh.hajo_id
and tamado_fh.hajo_id=hh.hajo_id and vedo_fh.hajo_id=hh.masik_hajo_id and hh.masik_hajo_id>0
group by tamado_csfh.csata_id,tamado_csfh.flotta_id,tamado_csfh.hajo_id
) t
set tamado_csfh.normalo_osszeg=t.uj_normalo_osszeg
where tamado_csfh.csata_id=t.csata_id and tamado_csfh.flotta_id=t.flotta_id and tamado_csfh.hajo_id=t.hajo_id');

//6. sebzesek (azert kell az if(tamado_fh.ossz_hp>0,greatest(tamado_fh.ossz_hp,3)/100,0), hogy ne legyen vegtelen csata)

//csata_sebzesek
mysql_query('truncate csata_sebzesek');
mysql_query('insert into csata_sebzesek
select vedo_csfh.csata_id
,tamado_csfh.flotta_id as tamado_flotta_id,tamado_csfh.hajo_id as tamado_hajo_id
,vedo_csfh.flotta_id as vedo_flotta_id,vedo_csfh.hajo_id as vedo_hajo_id
,round(sum(if(
tamado_csfh.normalo_osszeg=0
,0

,(
100
+ 10*least(tamado_fh.koordi_arany,10) + if(tamado_fh.hajo_id='.HAJO_TIPUS_POLLUX.',2.5*least(tamado_fh.castor_arany,20),0)
+ case tamado_csf.iranyito_rang
	when 2 then 10
	when 3 then 30
	when 4 then 50
	else 0
end
+ if(tamado_csf.iranyito_karrier=2,10,0)
+ case tamado_csf.iranyito_speci
	when 2 then 50
	when 4 then if(vedo_csf.tulaj=-1,70,0)
	else 0
end
)/100

* greatest(
100
- 5*least(vedo_fh.ohs_arany,10) - if(vedo_fh.hajo_id='.HAJO_TIPUS_CASTOR.',2.5*least(vedo_fh.pollux_arany,20),0)
- if(vedo_csf.iranyito_karrier=2,3,0)
- case vedo_csf.iranyito_speci
	when 1 then 7
	when 2 then 2
	when 4 then if(tamado_csf.tulaj=-1,7,0)
	else 0
end
,0)/100

* sqrt(tamado_fh.moral/10000)
* if(tamado_fh.ossz_hp>0,greatest(tamado_fh.ossz_hp,3)/100,0)
/tamado_csfh.normalo_osszeg*(vedo_fh.ossz_hp*vedo_fh.valodi_hp*hh.coef*hh.coef) *
hh.coef/10 * tamado_fh.tamado_ero / vedo_fh.valodi_hp * 100 * if(tamado_fh.hajo_id='.HAJO_TIPUS_AKNA.',if(rand()<0.1,1,0),1)
))) as sebzes
from csata_flotta tamado_csf, csata_flotta_hajo tamado_csfh, flotta_hajo tamado_fh, csata_flotta vedo_csf, csata_flotta_hajo vedo_csfh, flotta_hajo vedo_fh, hajo_hajo hh, csata_flottamatrix csfm
where csfm.csata_id=tamado_csfh.csata_id and csfm.csata_id=tamado_csf.csata_id
and csfm.csata_id=vedo_csfh.csata_id and csfm.csata_id=vedo_csf.csata_id
and csfm.egyik_flotta_id=tamado_csfh.flotta_id and csfm.egyik_flotta_id=tamado_csf.flotta_id and csfm.egyik_flotta_id=tamado_fh.flotta_id
and csfm.masik_flotta_id=vedo_csfh.flotta_id and csfm.masik_flotta_id=vedo_csf.flotta_id and csfm.masik_flotta_id=vedo_fh.flotta_id
and tamado_csfh.hajo_id=tamado_fh.hajo_id
and vedo_csfh.hajo_id=vedo_fh.hajo_id
and tamado_fh.hajo_id=hh.hajo_id and vedo_fh.hajo_id=hh.masik_hajo_id and hh.masik_hajo_id>0
group by vedo_csfh.csata_id
,tamado_csfh.flotta_id,tamado_csfh.hajo_id
,vedo_csfh.flotta_id,vedo_csfh.hajo_id');

//tenyleges sebzesek
mysql_query('update csata_flotta_hajo vedo_csfh,(
select csata_id,vedo_flotta_id as flotta_id,vedo_hajo_id as hajo_id,sum(sebzes) as uj_serules
from csata_sebzesek
group by csata_id,vedo_flotta_id,vedo_hajo_id) t
set vedo_csfh.serules=t.uj_serules
where vedo_csfh.csata_id=t.csata_id and vedo_csfh.flotta_id=t.flotta_id and vedo_csfh.hajo_id=t.hajo_id');

mysql_query('update flotta_hajo fh, csata_flotta_hajo csfh
set fh.ossz_hp=if(csfh.serules<0,fh.ossz_hp,if(csfh.serules>fh.ossz_hp,0,fh.ossz_hp-csfh.serules))
where fh.flotta_id=csfh.flotta_id and fh.hajo_id=csfh.hajo_id');
mysql_query('update csata_flotta_hajo csfh, flotta_hajo fh
set csfh.ossz_hp_utana=fh.ossz_hp
where fh.flotta_id=csfh.flotta_id and fh.hajo_id=csfh.hajo_id');


//csata_user tablat meg azelott tolteni, h torlodnenek a kilott flottak resztulajai
mysql_query('insert ignore into csata_user (csata_id,user_id,olvasott)
select csata_id,tulaj,0 from csata_flotta where tulaj!=0');
mysql_query('insert ignore into csata_user (csata_id,user_id,olvasott)
select csata_id,iranyito,0 from csata_flotta where iranyito!=0');
mysql_query('insert ignore into csata_user (csata_id,user_id,olvasott)
select distinct csf.csata_id,rfh.user_id,0 from csata_flotta csf, resz_flotta_hajo rfh where csf.flotta_id=rfh.flotta_id');


//7. TE/VE-frissites, egyenertek-szamitas
$er=mysql_query('select flotta_id from csata_flotta');
while($aux=mysql_fetch_array($er)) {
	flotta_minden_frissites($aux[0]);
	flotta_reszflotta_frissites($aux[0]);
}
mysql_query('update csata_flotta csf, flottak f
set csf.egyenertek_utana=f.egyenertek
where csf.flotta_id=f.id');
mysql_query('update csatak cs,(select csata_id,sum(egyenertek_elotte)-sum(egyenertek_utana) as ossz from csata_flotta group by csata_id) t
set cs.megsemmisult_egyenertek=t.ossz
where cs.id=t.csata_id');


//9. historizalas, ebbol lesznek a csatajelentesek
mysql_select_db($database_mmog_nemlog);
mysql_query('insert into hist_csatak select * from '.$database_mmog.'.csatak');
mysql_query('insert into hist_csata_flotta (csata_id,flotta_id,tulaj,tulaj_szov,egyenertek_elotte,egyenertek_utana,tulaj_nev,tulaj_szov_nev,nev,kozos,iranyito,iranyito_nev,tp,iranyito_karrier,iranyito_speci,iranyito_rang,kezdo)
select csata_id,flotta_id,tulaj,tulaj_szov,egyenertek_elotte,egyenertek_utana,tulaj_nev,tulaj_szov_nev,nev,kozos,iranyito,iranyito_nev,tp,iranyito_karrier,iranyito_speci,iranyito_rang,kezdo from '.$database_mmog.'.csata_flotta');
mysql_query('insert into hist_csata_flottamatrix select * from '.$database_mmog.'.csata_flottamatrix');
mysql_query('insert into hist_csata_flotta_hajo select csata_id,flotta_id,hajo_id,serules,ossz_hp_elotte,ossz_hp_utana from '.$database_mmog.'.csata_flotta_hajo where hajo_id>0');
mysql_query('insert into hist_csata_sebzesek select * from '.$database_mmog.'.csata_sebzesek where sebzes>0');//eleg csak a tenyleges sebzeseket historizalni
mysql_select_db($database_mmog);

//10. meghalt flottakat felszamolni
$er=mysql_query('select flotta_id from csata_flotta where egyenertek_utana=0');
while($aux=mysql_fetch_array($er)) flotta_torles($aux[0]);



if (false) {
	//11. Zanda flottak egy resze dezertal
	$er=mysql_query('select csata_id,flotta_id from csata_flotta where egyenertek_utana>0 and tulaj=-1');
	while($aux=mysql_fetch_array($er)) if (mt_rand(1,100)<=10) {
		$er2=mysql_query('select tulaj,tulaj_szov from csata_flotta where egyenertek_utana>0 and tulaj>0 and csata_id='.$aux['csata_id'].' order by rand() limit 1');
		$aux2=mysql_fetch_array($er2);
		if ($aux2) {
			$info_hu='';$info_en='';
			if (false) {//dezertalo flottaban levo hajokrol informacio!!!
				$idegen_hajo_id=(int)mysql2num('select hajo_id from flotta_hajo where flotta_id='.$aux['flotta_id'].' and hajo_id>218 and ossz_hp>0 order by rand() limit 1');
				if ($idegen_hajo_id>0) {
					$idegen_hajo_adatok=mysql2row('select * from hajok where id='.$idegen_hajo_id);
					$anev_hu='a'.($idegen_hajo_id==222?'z':'').' '.$idegen_hajo_adatok['nev'];
					$melyik_info_legyen=mt_rand(1,7);
					$info_hu=' Mérnökeidnek pedig sikerült kiderítenie, hogy <b>';
					$info_en=' And your engineers discovered that <b>';
					switch($melyik_info_legyen) {
						case 1:
							$info_hu.=$anev_hu.' támadóereje '.$idegen_hajo_adatok['tamado_ero'].'</b>.';
							$info_en.='the attack value of '.$idegen_hajo_adatok['nev_en'].' is '.$idegen_hajo_adatok['tamado_ero'].'</b>.';
						break;
						case 2:
							$info_hu.=$anev_hu.' HP-ja '.$idegen_hajo_adatok['valodi_hp'].'</b>.';
							$info_en.='the HP of '.$idegen_hajo_adatok['nev_en'].' is '.$idegen_hajo_adatok['valodi_hp'].'</b>.';
						break;
						case 3:
							$info_hu.=$anev_hu.' sebessége '.round($idegen_hajo_adatok['sebesseg']/2).'</b>.';
							$info_en.='the speed of '.$idegen_hajo_adatok['nev_en'].' is '.round($idegen_hajo_adatok['sebesseg']).'</b>.';
						break;
						case 4:
							$info_hu.=$anev_hu.' látótávolsága '.$idegen_hajo_adatok['latotav'].'</b>.';
							$info_en.='the vision of '.$idegen_hajo_adatok['nev_en'].' is '.$idegen_hajo_adatok['latotav'].'</b>.';
						break;
						case 5:
							$info_hu.=$anev_hu.' rejtőzése '.$idegen_hajo_adatok['rejtozes'].'</b>.';
							$info_en.='the stealth of '.$idegen_hajo_adatok['nev_en'].' is '.$idegen_hajo_adatok['rejtozes'].'</b>.';
						break;
						case 6:
							switch($idegen_hajo_id) {
								case 219:$mi_hu='cirkálók';$mi_en='cruisers';break;
								case 220:$mi_hu='';$mi_en='';break;
								case 221:$mi_hu='vadászok';$mi_en='fighters';break;
								case 222:$mi_hu='csatahajók';$mi_en='battleships';break;
								case 223:$mi_hu='rombolók';$mi_en='destroyers';break;
								case 224:$mi_hu='interceptorok';$mi_en='interceptors';break;
							}
							if ($mi_hu=='') {
								$info_hu='';$info_en='';
							} else {
								$info_hu.=$anev_hu.' '.$mi_hu.' ellen jó</b>.';
								$info_en.='the '.$idegen_hajo_adatok['nev_en'].' is good against '.$mi_en.'</b>.';
							}
						break;
						case 7:
							switch($idegen_hajo_id) {
								case 219:$mi_hu='csatahajók';$mi_en='battleships';break;
								case 220:$mi_hu='';$mi_en='';break;
								case 221:$mi_hu='cirkálók';$mi_en='cruisers';break;
								case 222:$mi_hu='rombolók';$mi_en='destroyers';break;
								case 223:$mi_hu='interceptorok';$mi_en='interceptors';break;
								case 224:$mi_hu='vadászok';$mi_en='fighters';break;
							}
							if ($mi_hu=='') {
								$info_hu='';$info_en='';
							} else {
								$info_hu.=$anev_hu.' '.$mi_hu.' ellen rossz</b>.';
								$info_en.='the '.$idegen_hajo_adatok['nev_en'].' is bad against '.$mi_en.'</b>.';
							}
						break;
					}
				}
			}
			//
			mysql_query('update flottak set tulaj='.$aux2['tulaj'].', tulaj_szov='.$aux2['tulaj_szov'].' where id='.$aux['flotta_id']);
			mysql_query('update flottak set statusz='.STATUSZ_MEGY_FLOTTAHOZ.' where cel_flotta='.$aux['flotta_id'].' and (statusz='.STATUSZ_TAMAD_FLOTTARA.' or statusz='.STATUSZ_TAMAD_FLOTTAT.')');
			rendszeruzenet_html($aux2['tulaj']
				,'Dezertáló idegen flotta'
				,'Zandagort egyik flottája megadta magát és átállt hozzád. Idegen hajókat továbbra sem tudsz gyártani, de az így megszerzetteket szabadon használhatod.'.$info_hu
				,'Deserting alien fleet'
				,'One of the fleets of Zandagort has surrendered and defected to you. You still cannot produce alien ships, but you may freely use those you acquired now.'.$info_en);
		}
	}
}



}//voltak-e egyaltalan csatak


mysql_query('update ido set idopont_csatak=idopont_csatak+1');$szimlog_hossz_csatak=round(1000*(microtime(true)-$mikor_indul));
/******************************************************** CSATAK VEGE ******************************************************************/


/******************************************************** OSTROMOK ELEJE ******************************************************************/

//bolygok ponterteke
mysql_query('update bolygok set aux_pontertek=0');
mysql_query('update bolygok b,(select b.id,sum(be.db*e.pontertek) as pontertek,sum(if(e.szallithato=1,be.db/e.savszel_igeny,0)) as ttertek,sum(if(e.szallithato=1,be.db*e.pontertek,0)) as uj_keszlet_pontertek
from bolygok b, bolygo_eroforras be, eroforrasok e
where b.id=be.bolygo_id and be.eroforras_id=e.id and e.pontertek>0
group by b.id) t
set b.aux_pontertek=b.aux_pontertek+round(coalesce(t.pontertek,0)),
b.keszlet_ttertek=round(coalesce(t.ttertek,0)),
b.keszlet_pontertek=round(coalesce(t.uj_keszlet_pontertek,0))
where t.id=b.id');
mysql_query('update bolygok b,(select b.id,sum(bgy.db*gyt.pontertek) as pontertek
from bolygok b, bolygo_gyar bgy, gyarak gy, gyartipusok gyt
where b.id=bgy.bolygo_id and bgy.gyar_id=gy.id and gy.tipus=gyt.id
group by b.id) t
set b.aux_pontertek=b.aux_pontertek+round(coalesce(t.pontertek,0))
where t.id=b.id');
mysql_query('update bolygok b, eroforrasok e
set b.aux_pontertek=b.aux_pontertek+b.raforditott_kornyezet_kp*e.pontertek
where e.id=150');
mysql_query('update bolygok set pontertek=aux_pontertek');

//bolygok iparmerete
mysql_query('update bolygok set aux_iparmeret=0');
mysql_query('update bolygok b,(select b.id,sum(bgy.db*gyt.pontertek) as pontertek
from bolygok b, bolygo_gyar bgy, gyarak gy, gyartipusok gyt
where b.id=bgy.bolygo_id and bgy.gyar_id=gy.id and gy.tipus=gyt.id
group by b.id) t
set b.aux_iparmeret=b.keszlet_pontertek+round(coalesce(t.pontertek,0))
where t.id=b.id');
mysql_query('update bolygok b, eroforrasok e
set b.aux_iparmeret=b.aux_iparmeret+b.raforditott_kornyezet_kp*e.pontertek
where e.id=150');
mysql_query('update bolygok set iparmeret=aux_iparmeret');

//flottak ponterteke
mysql_query('update flottak f,(
select f.id,sum(fh.ossz_hp*e.pontertek) as pontertek
from flottak f, flotta_hajo fh, eroforrasok e
where f.id=fh.flotta_id and fh.hajo_id=e.id
group by f.id) t
set f.pontertek=round(coalesce(t.pontertek,0))
where f.id=t.id');


$ostromok_listaja=array();//befejezetlen, a bekebiro elleni aggresszio detektalasahoz lett volna jo, asszem...
//tipus
//flotta_id,flotta_tulaj,flotta_tulaj_szov
//bolygo_id,bolygo_tulaj,bolygo_tulaj_szov
//0 annihilacio
//1 fantom utes
//2 npc utes
//3 fosztas
//4 szuperfosztas
//5 foglalas


$datum=date('Y-m-d H:i:s');
//Zandagort ostroma
$er_cel_bolygo=mysql_query('
select b.*
from bolygok b, flottak f
where b.x=f.x and b.y=f.y
group by b.id
having sum(if(f.statusz='.STATUSZ_TAMAD_BOLYGOT.' and f.cel_bolygo=b.id and f.tulaj=-1,1,0))>0
and sum(if(f.statusz='.STATUSZ_ALLOMAS.' and f.bolygo=b.id,1,0))=0');
while($cel_bolygo=mysql_fetch_array($er_cel_bolygo)) {//van tamado _idegen_ flotta, nincs vedo
	$r=mysql_query('select f.* from flottak f where f.x='.$cel_bolygo['x'].' and f.y='.$cel_bolygo['y'].' and f.cel_bolygo='.$cel_bolygo['id'].' and f.statusz='.STATUSZ_TAMAD_BOLYGOT.' and f.tulaj=-1');
	while($foszto_flotta=mysql_fetch_array($r)) $ostromok_listaja[]=array(0,$foszto_flotta['id'],$foszto_flotta['tulaj'],$foszto_flotta['tulaj_szov'],$cel_bolygo['id'],$cel_bolygo['tulaj'],$cel_bolygo['tulaj_szov']);
	//
	//valami porfelhő ikon maradjon a térképen egy ideig
	//ertesites
	$poz=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y']);
	$poz_en=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y'],'en');
	if ($cel_bolygo['tulaj']>0) rendszeruzenet($cel_bolygo['tulaj']
	,'Bolygó annihiláció','Zandagort megsemmisítette '.$cel_bolygo['nev'].' ('.$poz.') bolygódat.'
	,'Annihilation of planet','Zandagort has destroyed your planet '.$cel_bolygo['nev'].' ('.$poz_en.').'
	);
	//ide jovo flottak megallitasa (beleertve a Zanda flottat is
	mysql_query('update flottak set statusz='.STATUSZ_ALL.',cel_bolygo=0 where cel_bolygo='.$cel_bolygo['id']);
	//ugynokok megsemmisitese
	mysql_query('delete from ugynokcsoportok where bolygo_id='.$cel_bolygo['id']);
	//barmilyen ugynok tevekenyseg torlese
	mysql_query('update ugynokcsoportok set cel_bolygo_id=0 where cel_bolygo_id='.$cel_bolygo['id']);
	//szallitasok torlese
	mysql_query('delete from cron_tabla_eroforras_transzfer where honnan_bolygo_id='.$cel_bolygo['id']);
	mysql_query('delete from cron_tabla_eroforras_transzfer where hova_bolygo_id='.$cel_bolygo['id']);
	//epitkezesek torlese
	mysql_query('delete from cron_tabla where bolygo_id='.$cel_bolygo['id']);
	//bolygo annihilalasa
	mysql_query('update bolygok set letezik=0,tulaj_szov=-6,tulaj=-1 where id='.$cel_bolygo['id']);
	//mindenfele valtozasok regisztralasa
	bolygo_tulaj_valtozas($cel_bolygo['id'],$cel_bolygo['tulaj'],-1,$cel_bolygo['tulaj_szov'],-6);
}

//fantomok 1000-es vedelmi_bonuszu bolygoja elleni tamadasok
//mivel ezek nincsenek benne a sima tamadasi listaban
$er_cel_bolygo=mysql_query('
select b.*,u.nev as tulaj_nev
from bolygok b, flottak f, userek u
where b.x=f.x and b.y=f.y and b.tulaj=u.id
and b.moratorium_mikor_jar_le<=now()
and b.vedelmi_bonusz=1000
and coalesce(u.karrier,0)=3 and coalesce(u.speci,0)=3
group by b.id
having sum(if((f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.') and f.cel_bolygo=b.id,1,0))>0
and sum(if(f.statusz='.STATUSZ_ALLOMAS.' and f.bolygo=b.id,1,0))=0');
while($cel_bolygo=mysql_fetch_array($er_cel_bolygo)) {
	$poz=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y']);
	$poz_en=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y'],'en');
	//uzenet a tamado(k)nak es a bolygo tulajanak
	$er=mysql_query('select f.*,u.nev as tulaj_nev,u2.nev as iranyito_nev from flottak f
inner join userek u on u.id=f.tulaj
left join userek u2 on u2.id=f.uccso_parancs_by
where f.x='.$cel_bolygo['x'].' and f.y='.$cel_bolygo['y'].' and f.cel_bolygo='.$cel_bolygo['id'].' and (f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.') and f.tulaj>0
order by f.tulaj,f.id');
	//$elozo_tulaj=0;
	while($foszto_flotta=mysql_fetch_array($er)) {
		$ostromok_listaja[]=array(1,$foszto_flotta['id'],$foszto_flotta['tulaj'],$foszto_flotta['tulaj_szov'],$cel_bolygo['id'],$cel_bolygo['tulaj'],$cel_bolygo['tulaj_szov']);
		//if ($foszto_flotta['tulaj']!=$elozo_tulaj) {
			//$elozo_tulaj=$foszto_flotta['tulaj'];
			rendszeruzenet($foszto_flotta['tulaj']
				,$lang['hu']['kisphpk']['Fantom bolygó'],strtr($lang['hu']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) bolygón. Mint kiderült, ez YYY bolygója.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$cel_bolygo['tulaj_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
				,$lang['en']['kisphpk']['Fantom bolygó'],strtr($lang['en']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) bolygón. Mint kiderült, ez YYY bolygója.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$cel_bolygo['tulaj_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
			);
			rendszeruzenet($cel_bolygo['tulaj']
				,$lang['hu']['kisphpk']['Lebuktál'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával megütötte ZZZ (POZ) bolygódat, így megtudta, hogy ez nem NPC bolygó, hanem a tied.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
				,$lang['en']['kisphpk']['Lebuktál'],strtr($lang['en']['kisphpk']['XXX YYY flottájával megütötte ZZZ (POZ) bolygódat, így megtudta, hogy ez nem NPC bolygó, hanem a tied.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
			);
			if ($foszto_flotta['uccso_parancs_by']>0) if ($foszto_flotta['uccso_parancs_by']!=$foszto_flotta['tulaj']) {
				rendszeruzenet($foszto_flotta['uccso_parancs_by']
					,$lang['hu']['kisphpk']['Fantom bolygó'],strtr($lang['hu']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) bolygón. Mint kiderült, ez YYY bolygója.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$cel_bolygo['tulaj_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
					,$lang['en']['kisphpk']['Fantom bolygó'],strtr($lang['en']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) bolygón. Mint kiderült, ez YYY bolygója.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$cel_bolygo['tulaj_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
				);
				rendszeruzenet($cel_bolygo['tulaj']
					,$lang['hu']['kisphpk']['Lebuktál'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával megütötte ZZZ (POZ) bolygódat, így megtudta, hogy ez nem NPC bolygó, hanem a tied.'],array('XXX'=>$foszto_flotta['iranyito_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
					,$lang['en']['kisphpk']['Lebuktál'],strtr($lang['en']['kisphpk']['XXX YYY flottájával megütötte ZZZ (POZ) bolygódat, így megtudta, hogy ez nem NPC bolygó, hanem a tied.'],array('XXX'=>$foszto_flotta['iranyito_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
				);
			}
		//}
	}
	//moratorium beallitasa, hogy ne folyamatosan kuldje az uzeneteket
	mysql_query('update bolygok set moratorium_mikor_jar_le="'.date('Y-m-d H:i:s',time()+MORATORIUM_HOSSZA).'" where id='.$cel_bolygo['id']);
}

//emberek ostroma
$er_cel_bolygo=mysql_query('
select b.*
from bolygok b, flottak f
where b.x=f.x and b.y=f.y
and b.moratorium_mikor_jar_le<=now() and b.vedelmi_bonusz<1000
group by b.id
having sum(if((f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.') and f.cel_bolygo=b.id,1,0))>0
and sum(if(f.statusz='.STATUSZ_ALLOMAS.' and f.bolygo=b.id,1,0))=0');
while($cel_bolygo=mysql_fetch_array($er_cel_bolygo)) {//nincs moratorium, 1000-nel kisebb a vedelmi bonusz, van tamado, nincs vedo
	$poz=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y']);
	$poz_en=terkep_koordinatak($cel_bolygo['x'],$cel_bolygo['y'],'en');
	//moralvaltozas
	$moral_csokkenes=20;//legyen fixen 20, ahogy regen
	mysql_query('update bolygok set moral=greatest(moral-'.$moral_csokkenes.',0) where id='.$cel_bolygo['id']);
	if ($moral_csokkenes<$cel_bolygo['moral']) {//fosztas
		if ($cel_bolygo['tulaj']>0) {//jatekos bolygo -> fosztas
/********************************************************************************************************************************************/
			//egy-ket alap cucc
			$vedo_jatekos=mysql_fetch_array(mysql_query('select * from userek where id='.$cel_bolygo['tulaj']));
			$aux=mysql_fetch_array(mysql_query('select sum(egyenertek),sum(pontertek) from flottak where x='.$cel_bolygo['x'].' and y='.$cel_bolygo['y'].' and cel_bolygo='.$cel_bolygo['id'].' and (statusz='.STATUSZ_TAMAD_BOLYGOT.' or statusz='.STATUSZ_RAID_BOLYGOT.')'));
			$teljes_tamado_egyenertek=$aux[0];$teljes_tamado_pontertek=$aux[1];
			//zsakmany es veszteseg szazalekok
			$b=$cel_bolygo['vedelmi_bonusz']/1000;
			$maximalis_fosztas_szazalek=$maximalis_fosztas_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)];
			//SHY-fosztas
			$tulaj_vagyona=mysql2num('select vagyon from userek where id='.$cel_bolygo['tulaj']);
			$bolygora_eso_penz=round($cel_bolygo['pontertek']/mysql2num('select sum(pontertek) from bolygok where tulaj='.$cel_bolygo['tulaj'])*$tulaj_vagyona);
			if ($bolygora_eso_penz>0) $penz_fosztas_szazalek=$teljes_tamado_pontertek/$bolygora_eso_penz; else $penz_fosztas_szazalek=0;//a pontertek SHY-ban merve, ezert osszevethetok
			if ($penz_fosztas_szazalek>$maximalis_fosztas_szazalek) $penz_fosztas_szazalek=$maximalis_fosztas_szazalek;
			$penz_zsakmany_szazalek=(1-$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)])*$penz_fosztas_szazalek;
			$penz_veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)]*$penz_fosztas_szazalek;
			//
			if ($cel_bolygo['keszlet_pontertek']>0) $fosztas_szazalek=$teljes_tamado_pontertek/$cel_bolygo['keszlet_pontertek'];else $fosztas_szazalek=0;
			if ($fosztas_szazalek>$maximalis_fosztas_szazalek) $fosztas_szazalek=$maximalis_fosztas_szazalek;
			$zsakmany_szazalek=(1-$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)])*$fosztas_szazalek;
			$veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)]*$fosztas_szazalek;
			//1. mindenfele keszleteket osszeszamolni (nyersi, penz, ajanlatok)
			unset($keszletek);
			$r=mysql_query('select e.id
,vedo.db,coalesce(sum(a.mennyiseg),0) as ajanlott_db,vedo.db+coalesce(sum(a.mennyiseg),0) as ossz_db
,e.mertekegyseg,e.nev,e.mertekegyseg_en,e.nev_en
from bolygo_eroforras vedo
inner join eroforrasok e on e.id=vedo.eroforras_id
left join szabadpiaci_ajanlatok a on a.termek_id=e.id and a.user_id='.$cel_bolygo['tulaj'].' and a.bolygo_id='.$cel_bolygo['id'].' and a.vetel=0
where vedo.bolygo_id='.$cel_bolygo['id'].' and e.tipus='.EROFORRAS_TIPUS_EROFORRAS.' and e.szallithato=1
group by e.id');
			while($aux=mysql_fetch_array($r)) $keszletek[$aux[0]]=$aux;
			$vagyon=$vedo_jatekos['vagyon'];
			$eladasi_ajanlatok=sanitint(mysql2num('select sum(mennyiseg*arfolyam) from szabadpiaci_ajanlatok where bolygo_id='.$cel_bolygo['id'].' and user_id='.$cel_bolygo['tulaj'].' and vetel=1'));
			$keszletek[0]=array(
				'id'=>0
/*				,'db'=>$vagyon
				,'ajanlott_db'=>$eladasi_ajanlatok
				,'ossz_db'=>$vagyon+$eladasi_ajanlatok*/
				,'db'=>$bolygora_eso_penz
				,'ajanlott_db'=>$eladasi_ajanlatok
				,'ossz_db'=>$bolygora_eso_penz+$eladasi_ajanlatok
				,'mertekegyseg'=>'SHY'
				,'nev'=>'pénz'
				,'mertekegyseg_en'=>'SHY'
				,'nev_en'=>'money'
			);
			//foszto flottakon vegig
			$er_foszto=mysql_query('select f.*,u.nev as tulaj_nev,b.nev as bazis_nev,u.nyelv as tulaj_nyelv,u2.nev as iranyito_nev,u2.nyelv as iranyito_nyelv,if(u.karrier=3 and u.speci=3,1,0) as fantom_tamado,u.helyezes
from flottak f
inner join userek u on u.id=f.tulaj
inner join bolygok b on b.id=f.bazis_bolygo
left join userek u2 on u2.id=f.uccso_parancs_by
where f.x='.$cel_bolygo['x'].' and f.y='.$cel_bolygo['y'].' and f.cel_bolygo='.$cel_bolygo['id'].' and (f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.')
order by f.tulaj,f.id');
			if (mysql_num_rows($er_foszto)>0) {
				//2. zsakmanyok, vesztesegek, ertesitesek, transzferek
				while($foszto_flotta=mysql_fetch_array($er_foszto)) {
					//fantom tamado egy bolygoja lebukik
					$forras_info='';$forras_info_en='';
					if ($fantom_lebukas) if ($foszto_flotta['fantom_tamado']) {
						$random_bolygo=mysql2row('select * from bolygok where tulaj='.$foszto_flotta['tulaj'].' order by rand() limit 1');
						if ($random_bolygo) list($forras_info,$forras_info_en)=fantom_bolygo_uzenet($random_bolygo,$foszto_flotta);
					}
					//reszflottak
					flotta_reszflotta_frissites($foszto_flotta['id']);
					$vannak_e_reszflottai_a_fosztonak=sanitint(mysql2num('select coalesce(count(distinct user_id),0) from resz_flotta_hajo where flotta_id='.$foszto_flotta['id']));
					if ($vannak_e_reszflottai_a_fosztonak>1) {//foszto flotta resztulajdonosokkal
						$iranyito_kapott_e=false;
						//reszeken vegigmenni
						$er2=mysql_query('select user_id,coalesce(sum(rfh.hp/100*h.ar),0) as egyenertek from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$foszto_flotta['id'].' group by user_id');
						while($resz_flotta=mysql_fetch_array($er2)) {
							//bazis = jobb hijan az elso bolygo
							$bazis_bolygo=mysql2row('select * from bolygok where tulaj='.$resz_flotta['user_id'].' order by uccso_foglalas_mikor limit 1');
							//zsakmany
							if ($teljes_tamado_pontertek>0 && $foszto_flotta['egyenertek']>0) {
								$zsakmany_mennyisege_szazalekban=$resz_flotta['egyenertek']/$foszto_flotta['egyenertek']*$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
								$penz_zsakmany_mennyisege_szazalekban=$resz_flotta['egyenertek']/$foszto_flotta['egyenertek']*$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
							} else {
								$zsakmany_mennyisege_szazalekban=0;
								$penz_zsakmany_mennyisege_szazalekban=0;
							}
							//resztulaj
							$zsakmany_hu='';$zsakmany_en='';
							foreach($keszletek as $ef_id=>$keszlet) {
								if ($ef_id>0) {
									$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
										$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
										$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
										mysql_query('update bolygo_eroforras set db=db+'.$x.' where bolygo_id='.$bazis_bolygo['id'].' and eroforras_id='.$ef_id);
									}
								} else {
									$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
										$zsakmany_hu.=$x.' SHY';
										$zsakmany_en.=$x.' SHY';
										mysql_query('update userek set vagyon=vagyon+'.$x.' where id='.$resz_flotta['user_id']);
									}
								}
							}
							//
							if ($zsakmany_hu!='') {
								$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$bazis_bolygo['nev'],'YYY'=>$zsakmany_hu));
								$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$bazis_bolygo['nev'],'YYY'=>$zsakmany_en));
							} else {
								$zsakmany_szoveg='';
								$zsakmany_szoveg_en='';
							}
							if ($resz_flotta['user_id']==$foszto_flotta['uccso_parancs_by']) {
								$iranyito_kapott_e=true;
								rendszeruzenet($resz_flotta['user_id']
									,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
									,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
								);
							} else {
								rendszeruzenet($resz_flotta['user_id']
									,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal (WWW irányításával) kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
									,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal (WWW irányításával) kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
								);
							}
							//
						}
						//zsakmany es veszteseg osszesitve
						//zsakmany
						if ($teljes_tamado_pontertek>0) {
							$zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
							$veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$veszteseg_szazalek;
							$penz_zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
							$penz_veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_veszteseg_szazalek;
						} else {
							$zsakmany_mennyisege_szazalekban=0;
							$veszteseg_mennyisege_szazalekban=0;
							$penz_zsakmany_mennyisege_szazalekban=0;
							$penz_veszteseg_mennyisege_szazalekban=0;
						}
						//
						$zsakmany_hu='';$zsakmany_en='';
						$veszteseg_hu='';$veszteseg_en='';
						foreach($keszletek as $ef_id=>$keszlet) {
							if ($ef_id>0) {
								$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
								if ($x>0) {
									if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
									$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
									$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
								}
								//
								$y=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
								if ($y>0) {
									if ($veszteseg_hu!='') {$veszteseg_hu.=', ';$veszteseg_en.=', ';}
									$veszteseg_hu.=$y.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
									$veszteseg_en.=$y.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
								}
								$y1=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['db']);
								if ($y1>0) mysql_query('update bolygo_eroforras set db=if(db>'.$y1.',db-'.$y1.',0) where bolygo_id='.$cel_bolygo['id'].' and eroforras_id='.$ef_id);
							} else {
								$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
								if ($x>0) {
									if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
									$zsakmany_hu.=$x.' SHY';
									$zsakmany_en.=$x.' SHY';
								}
								$y=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
								if ($y>0) {
									if ($veszteseg_hu!='') {$veszteseg_hu.=' és ';$veszteseg_en.=' and ';}
									$veszteseg_hu.=$y.' SHY';
									$veszteseg_en.=$y.' SHY';
								}
								$y1=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['db']);
								if ($y1>0) mysql_query('update userek set vagyon=if(vagyon>'.$y1.',vagyon-'.$y1.',0) where id='.$cel_bolygo['tulaj']);
							}
						}
						//ajanlatok
						mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=1');
						mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=0');
						//
						if ($zsakmany_hu!='') {
							$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_hu));
							$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_en));
						} else {
							$zsakmany_szoveg='';
							$zsakmany_szoveg_en='';
						}
						if ($veszteseg_hu!='') {
							$veszteseg_szoveg=strtr($lang['hu']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_hu));
							$veszteseg_szoveg_en=strtr($lang['en']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_en));
						} else {
							$veszteseg_szoveg='';
							$veszteseg_szoveg_en='';
						}
						//iranyito ertesitese
						if (!$iranyito_kapott_e)
						rendszeruzenet($foszto_flotta['uccso_parancs_by']
							,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
							,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
						);
						//bolygo_tulaj ertesitese
						if ($foszto_flotta['tulaj']==$foszto_flotta['uccso_parancs_by']) {
							rendszeruzenet($cel_bolygo['tulaj']
								,$lang['hu']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
								,$lang['en']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
							);
						} else {
							rendszeruzenet($cel_bolygo['tulaj']
								,$lang['hu']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával (WWW irányításával) kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
								,$lang['en']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával (WWW irányításával) kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
							);
						}
					} else {//foszto flotta egy tulajdonossal
						//zsakmany
						if ($teljes_tamado_pontertek>0) {
							$zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
							$veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$veszteseg_szazalek;
							$penz_zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
							$penz_veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_veszteseg_szazalek;
						} else {
							$zsakmany_mennyisege_szazalekban=0;
							$veszteseg_mennyisege_szazalekban=0;
							$penz_zsakmany_mennyisege_szazalekban=0;
							$penz_veszteseg_mennyisege_szazalekban=0;
						}
						//
						$zsakmany_hu='';$zsakmany_en='';
						$veszteseg_hu='';$veszteseg_en='';
						foreach($keszletek as $ef_id=>$keszlet) {
							if ($ef_id>0) {
								$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
								if ($x>0) {
									if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
									$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
									$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
									mysql_query('update bolygo_eroforras set db=db+'.$x.' where bolygo_id='.$foszto_flotta['bazis_bolygo'].' and eroforras_id='.$ef_id);
								}
								//
								$y=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
								if ($y>0) {
									if ($veszteseg_hu!='') {$veszteseg_hu.=', ';$veszteseg_en.=', ';}
									$veszteseg_hu.=$y.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
									$veszteseg_en.=$y.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
								}
								$y1=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['db']);
								if ($y1>0) mysql_query('update bolygo_eroforras set db=if(db>'.$y1.',db-'.$y1.',0) where bolygo_id='.$cel_bolygo['id'].' and eroforras_id='.$ef_id);
							} else {
								$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
								if ($x>0) {
									if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
									$zsakmany_hu.=$x.' SHY';
									$zsakmany_en.=$x.' SHY';
									mysql_query('update userek set vagyon=vagyon+'.$x.' where id='.$foszto_flotta['tulaj']);
								}
								$y=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
								if ($y>0) {
									if ($veszteseg_hu!='') {$veszteseg_hu.=' és ';$veszteseg_en.=' and ';}
									$veszteseg_hu.=$y.' SHY';
									$veszteseg_en.=$y.' SHY';
								}
								$y1=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['db']);
								if ($y1>0) mysql_query('update userek set vagyon=if(vagyon>'.$y1.',vagyon-'.$y1.',0) where id='.$cel_bolygo['tulaj']);
							}
						}
						//ajanlatok
						mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=1');
						mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=0');
						//
						if ($zsakmany_hu!='') {
							$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$foszto_flotta['bazis_nev'],'YYY'=>$zsakmany_hu));
							$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$foszto_flotta['bazis_nev'],'YYY'=>$zsakmany_en));
							$zsakmany_szoveg_iranyito=strtr($lang['hu']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_hu));
							$zsakmany_szoveg_iranyito_en=strtr($lang['en']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_en));
						} else {
							$zsakmany_szoveg='';
							$zsakmany_szoveg_en='';
							$zsakmany_szoveg_iranyito='';
							$zsakmany_szoveg_iranyito_en='';
						}
						if ($veszteseg_hu!='') {
							$veszteseg_szoveg=strtr($lang['hu']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_hu));
							$veszteseg_szoveg_en=strtr($lang['en']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_en));
						} else {
							$veszteseg_szoveg='';
							$veszteseg_szoveg_en='';
						}
						//
						if ($foszto_flotta['tulaj']==$foszto_flotta['uccso_parancs_by']) {
							//tulaj = iranyito ertesitese
							rendszeruzenet($foszto_flotta['tulaj']
								,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
								,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
							);
							//bolygo_tulaj ertesitese
							rendszeruzenet($cel_bolygo['tulaj']
								,$lang['hu']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
								,$lang['en']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
							);
						} else {
							//tulaj ertesitese
							rendszeruzenet($foszto_flotta['tulaj']
								,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal (WWW irányításával) kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
								,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal (WWW irányításával) kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
							);
							//iranyito ertesitese
							rendszeruzenet($foszto_flotta['uccso_parancs_by']
								,$lang['hu']['kisphpk']['Fosztogatás'],strtr($lang['hu']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg_iranyito
								,$lang['en']['kisphpk']['Fosztogatás'],strtr($lang['en']['kisphpk']['XXX flottáddal kifosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_iranyito_en
							);
							//bolygo_tulaj ertesitese
							rendszeruzenet($cel_bolygo['tulaj']
								,$lang['hu']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával (WWW irányításával) kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
								,$lang['en']['kisphpk']['Ellenséges fosztogatás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával (WWW irányításával) kifosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
							);
						}
					}
				}
			}
/********************************************************************************************************************************************/
		} else {//npc bolygo -> csak moralcsokkenes
			//ertesites, hogy utottel az npc-n
			$er=mysql_query('select * from flottak where x='.$cel_bolygo['x'].' and y='.$cel_bolygo['y'].' and cel_bolygo='.$cel_bolygo['id'].' and (statusz='.STATUSZ_TAMAD_BOLYGOT.' or statusz='.STATUSZ_RAID_BOLYGOT.') and tulaj>0 order by tulaj,id');
			$elozo_tulaj=0;while($foszto_flotta=mysql_fetch_array($er)) {
				if ($foszto_flotta['tulaj']!=$elozo_tulaj) {
					$elozo_tulaj=$foszto_flotta['tulaj'];
					rendszeruzenet($foszto_flotta['tulaj']
					,$lang['hu']['kisphpk']['NPC-ütés'],strtr($lang['hu']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) npc bolygón. Amint a morálja nullára csökken, tied lesz a bolygó.'],array('XXX'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
					,$lang['en']['kisphpk']['NPC-ütés'],strtr($lang['en']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) npc bolygón. Amint a morálja nullára csökken, tied lesz a bolygó.'],array('XXX'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
					);
				}
			}
		}
		//moratorium beallitasa
		mysql_query('update bolygok set moratorium_mikor_jar_le="'.date('Y-m-d H:i:s',time()+MORATORIUM_HOSSZA).'" where id='.$cel_bolygo['id']) or hiba(__FILE__,__LINE__,mysql_error());
	} elseif ($cel_bolygo['vedelmi_bonusz']<800) {//foglalas (elvileg nem kellene ez a csekkolas, de a MORATORIUM_HOSSZA-kavaras miatt megis) vagy szuperfosztas (elvileg lehetne 800 felett is, de a moral miatt valojaban nem lehet)
		//az elso olyan tamadot megkeresni, akinek belefer a bolygolimitjebe!!! es nem RAID-el, hanem TAMAD
		$foglalo_flotta=null;
		$er=mysql_query('select f.*,u.bolygo_limit,u.nyelv as tulaj_nyelv,if(u.karrier=3 and u.speci=3,1,0) as fantom_tamado,u.helyezes from flottak f, userek u where f.x='.$cel_bolygo['x'].' and f.y='.$cel_bolygo['y'].' and f.cel_bolygo='.$cel_bolygo['id'].' and f.statusz='.STATUSZ_TAMAD_BOLYGOT.' and f.tulaj=u.id order by f.tulaj,f.id');
		while($foglalo_flotta_jelolt=mysql_fetch_array($er)) {
			$aux9=mysql_fetch_array(mysql_query('select count(1) from bolygok where tulaj='.$foglalo_flotta_jelolt['tulaj']));
			if ($foglalo_flotta_jelolt['bolygo_limit']>$aux9[0]) {
				$foglalo_flotta=$foglalo_flotta_jelolt;
				break;
			}
		}
		//utolso bolygo vedelem
		$nem_utolso_bolygo=true;
		if ($cel_bolygo['tulaj']>0) {
			$megtamadott_bolygoinak_szama=mysql2num('select count(1) from bolygok where letezik=1 and tulaj='.$cel_bolygo['tulaj']);
			if ($megtamadott_bolygoinak_szama==1) $nem_utolso_bolygo=false;
		}
		if ((!is_null($foglalo_flotta)) && $nem_utolso_bolygo) {//van olyan tamado, akinek belefer a bolygolimitjebe, es nem utolso bolygo
			//fantom tamado egy bolygoja lebukik
			$forras_info='';$forras_info_en='';
			if ($fantom_lebukas) if ($foglalo_flotta['fantom_tamado']) {
				$random_bolygo=mysql2row('select * from bolygok where tulaj='.$foglalo_flotta['tulaj'].' order by rand() limit 1');
				if ($random_bolygo) list($forras_info,$forras_info_en)=fantom_bolygo_uzenet($random_bolygo,$foglalo_flotta);
			}
			$tamado_jatekos=mysql_fetch_array(mysql_query('select nev from userek where id='.$foglalo_flotta['tulaj']));
			$veszteseg_szoveg='';
			$veszteseg_szoveg_en='';
			$veszteseg_szazalek=0;
			if ($cel_bolygo['tulaj']>0) {//jatekos bolygo -> veszteseg a bolygonak!!!
				$veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)];
				mysql_query('update bolygo_eroforras be, eroforrasok e set be.db=0 where be.bolygo_id='.$cel_bolygo['id'].' and be.eroforras_id=e.id and e.tipus=3');//urhajok elvesznek
				mysql_query('update bolygo_eroforras be, eroforrasok e set be.db=round(be.db*'.(1-$veszteseg_szazalek).') where be.bolygo_id='.$cel_bolygo['id'].' and be.eroforras_id=e.id and e.raktarozhato=1');
				//mysql_query('update bolygo_eroforras be, eroforrasok e set be.db=round(be.db*'.(1-$veszteseg_szazalek).') where be.bolygo_id='.$cel_bolygo['id'].' and be.eroforras_id=e.id and (e.raktarozhato=1 or e.tipus=3)');
				mysql_query('update bolygo_gyar bgy set bgy.db=round(bgy.db*'.(1-$veszteseg_szazalek).') where bgy.bolygo_id='.$cel_bolygo['id']);
				mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$cel_bolygo['id']);
				//bontasi/epitesi listat is leosztani!!! -> trukkozes ellen
				mysql_query('update cron_tabla set darab=floor(darab*'.(1-$veszteseg_szazalek).') where bolygo_id='.$cel_bolygo['id']);
				mysql_query('delete from cron_tabla where darab=0 and bolygo_id='.$cel_bolygo['id']);//a 0 epiteseket torolni, mert felrevezeto
				bgye_frissites($cel_bolygo['id']);
				$veszteseg_szoveg=strtr($lang['hu']['kisphpk'][' A foglaláskor elveszett a bolygó XXX%-a.'],array('XXX'=>round(100*$veszteseg_szazalek)));
				$veszteseg_szoveg_en=strtr($lang['en']['kisphpk'][' A foglaláskor elveszett a bolygó XXX%-a.'],array('XXX'=>round(100*$veszteseg_szazalek)));
			}
			if ($cel_bolygo['tulaj']>0) {
				$vedo_jatekos=mysql_fetch_array(mysql_query('select nev from userek where id='.$cel_bolygo['tulaj']));
				rendszeruzenet($foglalo_flotta['tulaj']
				,$lang['hu']['kisphpk']['Foglalás'],strtr($lang['hu']['kisphpk']['XXX flottáddal elfoglaltad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foglalo_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg
				,$lang['en']['kisphpk']['Foglalás'],strtr($lang['en']['kisphpk']['XXX flottáddal elfoglaltad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foglalo_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en
				);
				rendszeruzenet($cel_bolygo['tulaj']
				,$lang['hu']['kisphpk']['Ellenséges foglalás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával elfoglalta ZZZ (POZ) bolygódat.'],array('XXX'=>$tamado_jatekos['nev'],'YYY'=>$foglalo_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$forras_info
				,$lang['en']['kisphpk']['Ellenséges foglalás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával elfoglalta ZZZ (POZ) bolygódat.'],array('XXX'=>$tamado_jatekos['nev'],'YYY'=>$foglalo_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$forras_info_en
				);
			} else {
				rendszeruzenet($foglalo_flotta['tulaj']
				,$lang['hu']['kisphpk']['Foglalás'],strtr($lang['hu']['kisphpk']['XXX flottáddal elfoglaltad ZZZ (POZ) npc bolygót.'],array('XXX'=>$foglalo_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
				,$lang['en']['kisphpk']['Foglalás'],strtr($lang['en']['kisphpk']['XXX flottáddal elfoglaltad ZZZ (POZ) npc bolygót.'],array('XXX'=>$foglalo_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
				);
			}
			//log
			insert_into_bolygo_transzfer_log($cel_bolygo['id'],$cel_bolygo['uccso_emberi_tulaj'],$cel_bolygo['uccso_emberi_tulaj_szov'],$cel_bolygo['tulaj'],$cel_bolygo['tulaj_szov'],$foglalo_flotta['tulaj'],$foglalo_flotta['tulaj_szov'],1,$cel_bolygo['pontertek'],round((1-$veszteseg_szazalek)*$cel_bolygo['pontertek']),round($veszteseg_szazalek*$cel_bolygo['pontertek']));
			//bolygo tulajt,anyabolygot,moralt atirni
			$aux_anya[0]=$foglalo_flotta['bazis_bolygo'];//a támadó flotta bázisa
			if ($aux_anya[0]==0) {//ha nincs, akkor a támadó egy bolygója
				$aux_anya=mysql_fetch_array(mysql_query('select id from bolygok where tulaj='.$foglalo_flotta['tulaj'].' order by nev limit 1'));
			}
			mysql_query('update bolygok set tulaj='.$foglalo_flotta['tulaj'].',uccso_emberi_tulaj='.$foglalo_flotta['tulaj'].',tulaj_szov='.$foglalo_flotta['tulaj_szov'].',uccso_emberi_tulaj_szov='.$foglalo_flotta['tulaj_szov'].',kezelo=0,fobolygo=0,uccso_foglalas_mikor="'.$datum.'",moral=least(moral+'.BOLYGO_MORAL_NOVELES_FOGLALASKOR.',100),anyabolygo='.((int)$aux_anya[0]).' where id='.$cel_bolygo['id']) or hiba(__FILE__,__LINE__,mysql_error());
			//a tamado flottat ide kotni
			mysql_query('update flottak set bolygo='.$cel_bolygo['id'].',statusz='.STATUSZ_ALLOMAS.' where id='.$foglalo_flotta['id']) or hiba(__FILE__,__LINE__,mysql_error());
			//bolygo_tulaj_valtozas
			bolygo_tulaj_valtozas($cel_bolygo['id'],$cel_bolygo['tulaj'],$foglalo_flotta['tulaj'],$cel_bolygo['tulaj_szov'],$foglalo_flotta['tulaj_szov']);
			//ha easter egg, akkor felturbozni (bolygo_reset_oriasira fuggveny jelenleg (s8) nincs is)
			//if ($cel_bolygo['uccso_emberi_tulaj']==0) if (in_array($cel_bolygo['id'],array(8007,12733,6487,1310,9670,3555,3970))) bolygo_reset_oriasira($cel_bolygo['id']);
		} else {//szuperfosztas
			/********************************************************************************/
			if ($cel_bolygo['tulaj']>0) {//jatekos bolygo -> szuperfosztas
/********************************************************************************************************************************************/
				//egy-ket alap cucc
				$vedo_jatekos=mysql_fetch_array(mysql_query('select * from userek where id='.$cel_bolygo['tulaj']));
				$aux=mysql_fetch_array(mysql_query('select sum(egyenertek),sum(pontertek) from flottak where x='.$cel_bolygo['x'].' and y='.$cel_bolygo['y'].' and cel_bolygo='.$cel_bolygo['id'].' and (statusz='.STATUSZ_TAMAD_BOLYGOT.' or statusz='.STATUSZ_RAID_BOLYGOT.')'));
				$teljes_tamado_egyenertek=$aux[0];$teljes_tamado_pontertek=$aux[1];
				//zsakmany es veszteseg szazalekok
				$b=$cel_bolygo['vedelmi_bonusz']/1000;
				$maximalis_fosztas_szazalek=$maximalis_fosztas_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)];
				//SHY-fosztas
				$tulaj_vagyona=mysql2num('select vagyon from userek where id='.$cel_bolygo['tulaj']);
				$bolygora_eso_penz=round($cel_bolygo['pontertek']/mysql2num('select sum(pontertek) from bolygok where tulaj='.$cel_bolygo['tulaj'])*$tulaj_vagyona);
				if ($bolygora_eso_penz>0) $penz_fosztas_szazalek=10*$teljes_tamado_pontertek/$bolygora_eso_penz; else $penz_fosztas_szazalek=0;//a pontertek SHY-ban merve, ezert osszevethetok, szuperfosztas -> a flotta ertekenek 10-szerese szamit
				if ($penz_fosztas_szazalek>$maximalis_fosztas_szazalek) $penz_fosztas_szazalek=$maximalis_fosztas_szazalek;
				$penz_zsakmany_szazalek=(1-$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)])*$penz_fosztas_szazalek;
				$penz_veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)]*$penz_fosztas_szazalek;
				//
				if ($cel_bolygo['keszlet_pontertek']>0) $fosztas_szazalek=10*$teljes_tamado_pontertek/$cel_bolygo['keszlet_pontertek'];else $fosztas_szazalek=0;//szuperfosztas -> a flotta ertekenek 10-szerese szamit
				if ($fosztas_szazalek>$maximalis_fosztas_szazalek) $fosztas_szazalek=$maximalis_fosztas_szazalek;
				$zsakmany_szazalek=(1-$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)])*$fosztas_szazalek;
				$veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)]*$fosztas_szazalek;
				//epuletek lebontasa
				$er=mysql_query('select gyar_id,db,aktiv_db,round('.$fosztas_szazalek.'*db) as lebont_db from bolygo_gyar where bolygo_id='.$cel_bolygo['id']);
				while($gyar=mysql_fetch_array($er)) {
					//megadott szamu epulet elbontasa
					if ($gyar['db']>$gyar['lebont_db']) {
						mysql_query('update bolygo_gyar set db=if(db>'.$gyar['lebont_db'].',db-'.$gyar['lebont_db'].',0) where bolygo_id='.$cel_bolygo['id'].' and gyar_id='.$gyar['gyar_id']);
						mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$cel_bolygo['id'].' and gyar_id='.$gyar['gyar_id']);
					} else {
						mysql_query('delete from bolygo_gyar where bolygo_id='.$cel_bolygo['id'].' and gyar_id='.$gyar['gyar_id']);
						mysql_query('delete from bolygo_gyar_eroforras where bolygo_id='.$cel_bolygo['id'].' and gyar_id='.$gyar['gyar_id']);
					}
					//nyersik (epitoanyag fele) visszaadasa
					mysql_query('update gyar_epitesi_koltseg gyek,gyarak gy,bolygo_eroforras be set be.db=be.db+'.$gyar['lebont_db'].'*gyek.db/2 where gyek.tipus=gy.tipus and gy.id='.$gyar['gyar_id'].' and gyek.szint=gy.szint and gyek.eroforras_id=be.eroforras_id and be.bolygo_id='.$cel_bolygo['id']);
				}
				bgye_frissites($cel_bolygo['id']);
				//1. mindenfele keszleteket osszeszamolni (nyersi, penz, ajanlatok)
				unset($keszletek);
				$r=mysql_query('select e.id
,vedo.db,coalesce(sum(a.mennyiseg),0) as ajanlott_db,vedo.db+coalesce(sum(a.mennyiseg),0) as ossz_db
,e.mertekegyseg,e.nev,e.mertekegyseg_en,e.nev_en
from bolygo_eroforras vedo
inner join eroforrasok e on e.id=vedo.eroforras_id
left join szabadpiaci_ajanlatok a on a.termek_id=e.id and a.user_id='.$cel_bolygo['tulaj'].' and a.bolygo_id='.$cel_bolygo['id'].' and a.vetel=0
where vedo.bolygo_id='.$cel_bolygo['id'].' and e.tipus='.EROFORRAS_TIPUS_EROFORRAS.' and e.szallithato=1
group by e.id');
				while($aux=mysql_fetch_array($r)) $keszletek[$aux[0]]=$aux;
				$vagyon=$vedo_jatekos['vagyon'];
				$eladasi_ajanlatok=sanitint(mysql2num('select sum(mennyiseg*arfolyam) from szabadpiaci_ajanlatok where bolygo_id='.$cel_bolygo['id'].' and user_id='.$cel_bolygo['tulaj'].' and vetel=1'));
				$keszletek[0]=array(
					'id'=>0
					,'db'=>$vagyon
					,'ajanlott_db'=>$eladasi_ajanlatok
					,'ossz_db'=>$vagyon+$eladasi_ajanlatok
					,'mertekegyseg'=>'SHY'
					,'nev'=>'pénz'
					,'mertekegyseg_en'=>'SHY'
					,'nev_en'=>'money'
				);
				//foszto flottakon vegig
				$er_foszto=mysql_query('select f.*,u.nev as tulaj_nev,b.nev as bazis_nev,u.nyelv as tulaj_nyelv,u2.nev as iranyito_nev,u2.nyelv as iranyito_nyelv,if(u.karrier=3 and u.speci=3,1,0) as fantom_tamado,u.helyezes
from flottak f
inner join userek u on u.id=f.tulaj
inner join bolygok b on b.id=f.bazis_bolygo
left join userek u2 on u2.id=f.uccso_parancs_by
where f.x='.$cel_bolygo['x'].' and f.y='.$cel_bolygo['y'].' and f.cel_bolygo='.$cel_bolygo['id'].' and (f.statusz='.STATUSZ_TAMAD_BOLYGOT.' or f.statusz='.STATUSZ_RAID_BOLYGOT.')
order by f.tulaj,f.id');
				if (mysql_num_rows($er_foszto)>0) {
					//2. zsakmanyok, vesztesegek, ertesitesek, transzferek
					while($foszto_flotta=mysql_fetch_array($er_foszto)) {
						//fantom tamado egy bolygoja lebukik
						$forras_info='';$forras_info_en='';
						if ($fantom_lebukas) if ($foszto_flotta['fantom_tamado']) {
							$random_bolygo=mysql2row('select * from bolygok where tulaj='.$foszto_flotta['tulaj'].' order by rand() limit 1');
							if ($random_bolygo) list($forras_info,$forras_info_en)=fantom_bolygo_uzenet($random_bolygo,$foszto_flotta);
						}
						//reszflottak
						flotta_reszflotta_frissites($foszto_flotta['id']);
						$vannak_e_reszflottai_a_fosztonak=sanitint(mysql2num('select coalesce(count(distinct user_id),0) from resz_flotta_hajo where flotta_id='.$foszto_flotta['id']));
						if ($vannak_e_reszflottai_a_fosztonak>1) {//foszto flotta resztulajdonosokkal
							$iranyito_kapott_e=false;
							//reszeken vegigmenni
							$er2=mysql_query('select user_id,coalesce(sum(rfh.hp/100*h.ar),0) as egyenertek from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$foszto_flotta['id'].' group by user_id');
							while($resz_flotta=mysql_fetch_array($er2)) {
								//bazis = jobb hijan az elso bolygo
								$bazis_bolygo=mysql2row('select * from bolygok where tulaj='.$resz_flotta['user_id'].' order by uccso_foglalas_mikor limit 1');
								//zsakmany
								if ($teljes_tamado_pontertek>0 && $foszto_flotta['egyenertek']>0) {
									$zsakmany_mennyisege_szazalekban=$resz_flotta['egyenertek']/$foszto_flotta['egyenertek']*$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
									$penz_zsakmany_mennyisege_szazalekban=$resz_flotta['egyenertek']/$foszto_flotta['egyenertek']*$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
								} else {
									$zsakmany_mennyisege_szazalekban=0;
									$penz_zsakmany_mennyisege_szazalekban=0;
								}
								//resztulaj
								$zsakmany_hu='';$zsakmany_en='';
								foreach($keszletek as $ef_id=>$keszlet) {
									if ($ef_id>0) {
										$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
										if ($x>0) {
											if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
											$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
											$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
											mysql_query('update bolygo_eroforras set db=db+'.$x.' where bolygo_id='.$bazis_bolygo['id'].' and eroforras_id='.$ef_id);
										}
									} else {
										$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
										if ($x>0) {
											if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
											$zsakmany_hu.=$x.' SHY';
											$zsakmany_en.=$x.' SHY';
											mysql_query('update userek set vagyon=vagyon+'.$x.' where id='.$resz_flotta['user_id']);
										}
									}
								}
								//
								if ($zsakmany_hu!='') {
									$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$bazis_bolygo['nev'],'YYY'=>$zsakmany_hu));
									$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$bazis_bolygo['nev'],'YYY'=>$zsakmany_en));
								} else {
									$zsakmany_szoveg='';
									$zsakmany_szoveg_en='';
								}
								if ($resz_flotta['user_id']==$foszto_flotta['uccso_parancs_by']) {
									$iranyito_kapott_e=true;
									rendszeruzenet($resz_flotta['user_id']
										,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
										,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
									);
								} else {
									rendszeruzenet($resz_flotta['user_id']
										,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal (WWW irányításával) szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
										,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal (WWW irányításával) szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
									);
								}
								//
							}
							//zsakmany es veszteseg osszesitve
							//zsakmany
							if ($teljes_tamado_pontertek>0) {
								$zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
								$veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$veszteseg_szazalek;
								$penz_zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
								$penz_veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_veszteseg_szazalek;
							} else {
								$zsakmany_mennyisege_szazalekban=0;
								$veszteseg_mennyisege_szazalekban=0;
								$penz_zsakmany_mennyisege_szazalekban=0;
								$penz_veszteseg_mennyisege_szazalekban=0;
							}
							//
							$zsakmany_hu='';$zsakmany_en='';
							$veszteseg_hu='';$veszteseg_en='';
							foreach($keszletek as $ef_id=>$keszlet) {
								if ($ef_id>0) {
									$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
										$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
										$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
									}
									//
									$y=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
									if ($y>0) {
										if ($veszteseg_hu!='') {$veszteseg_hu.=', ';$veszteseg_en.=', ';}
										$veszteseg_hu.=$y.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
										$veszteseg_en.=$y.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
									}
									$y1=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['db']);
									if ($y1>0) mysql_query('update bolygo_eroforras set db=if(db>'.$y1.',db-'.$y1.',0) where bolygo_id='.$cel_bolygo['id'].' and eroforras_id='.$ef_id);
								} else {
									$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
										$zsakmany_hu.=$x.' SHY';
										$zsakmany_en.=$x.' SHY';
									}
									$y=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
									if ($y>0) {
										if ($veszteseg_hu!='') {$veszteseg_hu.=' és ';$veszteseg_en.=' and ';}
										$veszteseg_hu.=$y.' SHY';
										$veszteseg_en.=$y.' SHY';
									}
									$y1=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['db']);
									if ($y1>0) mysql_query('update userek set vagyon=if(vagyon>'.$y1.',vagyon-'.$y1.',0) where id='.$cel_bolygo['tulaj']);
								}
							}
							//ajanlatok
							mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=1');
							mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=0');
							//
							if ($zsakmany_hu!='') {
								$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_hu));
								$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_en));
							} else {
								$zsakmany_szoveg='';
								$zsakmany_szoveg_en='';
							}
							if ($veszteseg_hu!='') {
								$veszteseg_szoveg=strtr($lang['hu']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_hu));
								$veszteseg_szoveg_en=strtr($lang['en']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_en));
							} else {
								$veszteseg_szoveg='';
								$veszteseg_szoveg_en='';
							}
							//iranyito ertesitese
							if (!$iranyito_kapott_e)
							rendszeruzenet($foszto_flotta['uccso_parancs_by']
								,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
								,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
							);
							//bolygo_tulaj ertesitese
							if ($foszto_flotta['tulaj']==$foszto_flotta['uccso_parancs_by']) {
								rendszeruzenet($cel_bolygo['tulaj']
									,$lang['hu']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
									,$lang['en']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
								);
							} else {
								rendszeruzenet($cel_bolygo['tulaj']
									,$lang['hu']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával (WWW irányításával) szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
									,$lang['en']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával (WWW irányításával) szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
								);
							}
						} else {//foszto flotta egy tulajdonossal
							//zsakmany
							if ($teljes_tamado_pontertek>0) {
								$zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$zsakmany_szazalek;
								$veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$veszteseg_szazalek;
								$penz_zsakmany_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_zsakmany_szazalek;
								$penz_veszteseg_mennyisege_szazalekban=$foszto_flotta['pontertek']/$teljes_tamado_pontertek*$penz_veszteseg_szazalek;
							} else {
								$zsakmany_mennyisege_szazalekban=0;
								$veszteseg_mennyisege_szazalekban=0;
								$penz_zsakmany_mennyisege_szazalekban=0;
								$penz_veszteseg_mennyisege_szazalekban=0;
							}
							//
							$zsakmany_hu='';$zsakmany_en='';
							$veszteseg_hu='';$veszteseg_en='';
							foreach($keszletek as $ef_id=>$keszlet) {
								if ($ef_id>0) {
									$x=round($zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=', ';$zsakmany_en.=', ';}
										$zsakmany_hu.=$x.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
										$zsakmany_en.=$x.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
										mysql_query('update bolygo_eroforras set db=db+'.$x.' where bolygo_id='.$foszto_flotta['bazis_bolygo'].' and eroforras_id='.$ef_id);
									}
									//
									$y=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
									if ($y>0) {
										if ($veszteseg_hu!='') {$veszteseg_hu.=', ';$veszteseg_en.=', ';}
										$veszteseg_hu.=$y.' '.$keszlet['mertekegyseg'].' '.$keszlet['nev'];
										$veszteseg_en.=$y.' '.$keszlet['mertekegyseg_en'].' of '.$keszlet['nev_en'];
									}
									$y1=round(($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban)*$keszlet['db']);
									if ($y1>0) mysql_query('update bolygo_eroforras set db=if(db>'.$y1.',db-'.$y1.',0) where bolygo_id='.$cel_bolygo['id'].' and eroforras_id='.$ef_id);
								} else {
									$x=round($penz_zsakmany_mennyisege_szazalekban*$keszlet['ossz_db']);
									if ($x>0) {
										if ($zsakmany_hu!='') {$zsakmany_hu.=' és ';$zsakmany_en.=' and ';}
										$zsakmany_hu.=$x.' SHY';
										$zsakmany_en.=$x.' SHY';
										mysql_query('update userek set vagyon=vagyon+'.$x.' where id='.$foszto_flotta['tulaj']);
									}
									$y=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['ossz_db']);
									if ($y>0) {
										if ($veszteseg_hu!='') {$veszteseg_hu.=' és ';$veszteseg_en.=' and ';}
										$veszteseg_hu.=$y.' SHY';
										$veszteseg_en.=$y.' SHY';
									}
									$y1=round(($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban)*$keszlet['db']);
									if ($y1>0) mysql_query('update userek set vagyon=if(vagyon>'.$y1.',vagyon-'.$y1.',0) where id='.$cel_bolygo['tulaj']);
								}
							}
							//ajanlatok
							mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($zsakmany_mennyisege_szazalekban+$veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=1');
							mysql_query('update szabadpiaci_ajanlatok set mennyiseg=greatest(mennyiseg-round('.($penz_zsakmany_mennyisege_szazalekban+$penz_veszteseg_mennyisege_szazalekban).'*mennyiseg),0) where user_id='.$cel_bolygo['tulaj'].' and bolygo_id='.$cel_bolygo['id'].' and vetel=0');
							//
							if ($zsakmany_hu!='') {
								$zsakmany_szoveg=strtr($lang['hu']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$foszto_flotta['bazis_nev'],'YYY'=>$zsakmany_hu));
								$zsakmany_szoveg_en=strtr($lang['en']['kisphpk'][' A zsákmány (YYY) a flotta bázisára (XXX) került.'],array('XXX'=>$foszto_flotta['bazis_nev'],'YYY'=>$zsakmany_en));
								$zsakmany_szoveg_iranyito=strtr($lang['hu']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_hu));
								$zsakmany_szoveg_iranyito_en=strtr($lang['en']['kisphpk'][' A zsákmány: YYY.'],array('YYY'=>$zsakmany_en));
							} else {
								$zsakmany_szoveg='';
								$zsakmany_szoveg_en='';
								$zsakmany_szoveg_iranyito='';
								$zsakmany_szoveg_iranyito_en='';
							}
							if ($veszteseg_hu!='') {
								$veszteseg_szoveg=strtr($lang['hu']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_hu));
								$veszteseg_szoveg_en=strtr($lang['en']['kisphpk'][' A veszteséged: YYY.'],array('YYY'=>$veszteseg_en));
							} else {
								$veszteseg_szoveg='';
								$veszteseg_szoveg_en='';
							}
							//
							if ($foszto_flotta['tulaj']==$foszto_flotta['uccso_parancs_by']) {
								//tulaj = iranyito ertesitese
								rendszeruzenet($foszto_flotta['tulaj']
									,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
									,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
								);
								//bolygo_tulaj ertesitese
								rendszeruzenet($cel_bolygo['tulaj']
									,$lang['hu']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
									,$lang['en']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
								);
							} else {
								//tulaj ertesitese
								rendszeruzenet($foszto_flotta['tulaj']
									,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal (WWW irányításával) szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg
									,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal (WWW irányításával) szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_en
								);
								//iranyito ertesitese
								rendszeruzenet($foszto_flotta['uccso_parancs_by']
									,$lang['hu']['kisphpk']['Szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$zsakmany_szoveg_iranyito
									,$lang['en']['kisphpk']['Szuperfosztás'],strtr($lang['en']['kisphpk']['XXX flottáddal szuperfosztottad YYY ZZZ (POZ) bolygóját.'],array('XXX'=>$foszto_flotta['nev'],'YYY'=>$vedo_jatekos['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$zsakmany_szoveg_iranyito_en
								);
								//bolygo_tulaj ertesitese
								rendszeruzenet($cel_bolygo['tulaj']
									,$lang['hu']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['hu']['kisphpk']['XXX YYY flottájával (WWW irányításával) szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz)).$veszteseg_szoveg.$forras_info
									,$lang['en']['kisphpk']['Ellenséges szuperfosztás'],strtr($lang['en']['kisphpk']['XXX YYY flottájával (WWW irányításával) szuperfosztotta ZZZ (POZ) bolygódat.'],array('XXX'=>$foszto_flotta['tulaj_nev'],'YYY'=>$foszto_flotta['nev'],'WWW'=>$foszto_flotta['iranyito_nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en)).$veszteseg_szoveg_en.$forras_info_en
								);
							}
						}
					}
				}
/********************************************************************************************************************************************/
			} else {//npc bolygo -> csak moralcsokkenes
				//ertesites, hogy utottel az npc-n
				$er=mysql_query('select * from flottak where x='.$cel_bolygo['x'].' and y='.$cel_bolygo['y'].' and cel_bolygo='.$cel_bolygo['id'].' and (statusz='.STATUSZ_TAMAD_BOLYGOT.' or statusz='.STATUSZ_RAID_BOLYGOT.') and tulaj>0 order by tulaj,id');
				$elozo_tulaj=0;while($foszto_flotta=mysql_fetch_array($er)) {
					if ($foszto_flotta['tulaj']!=$elozo_tulaj) {
						$elozo_tulaj=$foszto_flotta['tulaj'];
						rendszeruzenet($foszto_flotta['tulaj']
						,$lang['hu']['kisphpk']['NPC-ütés'],strtr($lang['hu']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) npc bolygón. De csak akkor lehet a tied, ha belefér a bolygólimitedbe.'],array('XXX'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz))
						,$lang['en']['kisphpk']['NPC-ütés'],strtr($lang['en']['kisphpk']['XXX flottáddal ütöttél egyet ZZZ (POZ) npc bolygón. De csak akkor lehet a tied, ha belefér a bolygólimitedbe.'],array('XXX'=>$foszto_flotta['nev'],'ZZZ'=>$cel_bolygo['nev'],'POZ'=>$poz_en))
						);
					}
				}
			}
			/********************************************************************************/
		}
		mysql_query('update bolygok set moratorium_mikor_jar_le="'.date('Y-m-d H:i:s',time()+MORATORIUM_HOSSZA).'" where id='.$cel_bolygo['id']) or hiba(__FILE__,__LINE__,mysql_error());
	}
}



if (count($ostromok_listaja)>0) {
	$most=date('Y-m-d H:i:s');
	mysql_select_db($database_mmog_nemlog);
	foreach($ostromok_listaja as $ostrom) {
		mysql_query('insert into ostromok (tipus,flotta_id,flotta_tulaj,flotta_tulaj_szov,bolygo_id,bolygo_tulaj,bolygo_tulaj_szov,mikor) values('.$ostrom[0].','.$ostrom[1].','.$ostrom[2].','.$ostrom[3].','.$ostrom[4].','.$ostrom[5].','.$ostrom[6].',"'.$most.'")');
	}
	mysql_select_db($database_mmog);
}



mysql_query('update ido set idopont_ostromok=idopont_ostromok+1');$szimlog_hossz_ostromok=round(1000*(microtime(true)-$mikor_indul));
/******************************************************** OSTROMOK VEGE ******************************************************************/


/************************************** FOG OF WAR ELEJE *****************************************************************/
//flotta_atrendezes.php-ban es flotta_kivonasa.php-ban is megfeleloen igazitani!

if ($fog_of_war) {

mysql_query('update bolygok b, bolygo_eroforras be
set b.latotav=if(be.db>0,500,0)
where be.eroforras_id=75 and be.bolygo_id=b.id');

mysql_query('lock tables lat_user_flotta write, lat_szov_flotta write, hexa_flotta write, hexa_flotta hf read, flottak f read, bolygok bu read, flottak fu read, hexa_bolygo hb read, hexa_kor hk read, resz_flotta_aux write, resz_flotta_hajo read, resz_flotta_aux rfa read');

mysql_query('delete from resz_flotta_aux');
mysql_query('insert ignore into resz_flotta_aux (flotta_id,user_id)
select distinct flotta_id,user_id from resz_flotta_hajo');

mysql_query('delete from hexa_flotta');//a lock miatt nem lehet truncate!!!
mysql_query('insert into hexa_flotta (x,y,id)
select f.hexa_x+hk.x,f.hexa_y+hk.y,f.id
from flottak f, hexa_kor hk
where tulaj!=0
and f.latotav>=hk.r');

mysql_query('delete from lat_user_flotta');//a lock miatt nem lehet truncate!!!

//bolygo tulaj lat flottat
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select bu.tulaj,f.id,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1)
from flottak f, bolygok bu, hexa_bolygo hb
where bu.id=hb.id and bu.tulaj!=0
and f.hexa_x=hb.x and f.hexa_y=hb.y
and sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))/2<=bu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1))');

//flotta tulaj lat flottat (nem kell tulaj>0-ra szurni, mert hexa_flotta-t csak a tulaj>0-k kapnak (ld fentebb))
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select fu.tulaj,f.id,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1)
from flottak f, flottak fu, hexa_flotta hf
where fu.id=hf.id
and f.hexa_x=hf.x and f.hexa_y=hf.y
and sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))/2<=fu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1))');

//reszflotta tulaj lat flottat
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select rfa.user_id,f.id,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1)
from flottak f, flottak fu, resz_flotta_aux rfa, hexa_flotta hf
where fu.id=hf.id
and f.hexa_x=hf.x and f.hexa_y=hf.y
and sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))/2<=fu.latotav-f.rejtozes
and rfa.flotta_id=fu.id
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1))');

//sajat flottak
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select f.tulaj,f.id,2 from flottak f where f.tulaj!=0
on duplicate key update lathatosag=2');

mysql_query('delete from lat_szov_flotta');//a lock miatt nem lehet truncate!!!

//bolygo szovije lat flottat
mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select bu.tulaj_szov,f.id,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1)
from flottak f, bolygok bu, hexa_bolygo hb
where bu.id=hb.id and bu.tulaj_szov!=0
and f.hexa_x=hb.x and f.hexa_y=hb.y
and sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))/2<=bu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1))');

//flotta szovije lat flottat
mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select fu.tulaj_szov,f.id,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1)
from flottak f, flottak fu, hexa_flotta hf
where fu.id=hf.id and fu.tulaj_szov!=0
and f.hexa_x=hf.x and f.hexa_y=hf.y
and sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))/2<=fu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1))');

//sajat flottak
mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select f.tulaj_szov,f.id,2 from flottak f where f.tulaj_szov!=0
on duplicate key update lathatosag=2');

mysql_query('unlock tables');

}

mysql_query('update ido set idopont_fog=idopont_fog+1');$szimlog_hossz_fog=round(1000*(microtime(true)-$mikor_indul));
/************************************** FOG OF WAR VEGE *****************************************************************/



/************************************** TUT_LEVEL ES TECH_SZINT ELEJE *****************************************************************/

//2: fa<50k es ko<50k -> 3. level
$r=mysql_query('select u.id,u.nev,u.nyelv,b.id as bolygo_id,b.x as bolygo_x,b.y as bolygo_y
from userek u
inner join bolygok b on b.tulaj=u.id
inner join bolygo_eroforras be1 on be1.bolygo_id=b.id and be1.eroforras_id=64
inner join bolygo_eroforras be2 on be2.bolygo_id=b.id and be2.eroforras_id=65
where u.tut_level=2
group by u.id
having coalesce(sum(be1.db),0)<50000 and coalesce(sum(be1.db),0)<50000');
while($aux=mysql_fetch_array($r)) {
	tut_level($aux[0],3,array($aux[1],$aux[2]));
	//kalozok
	mysql_query('update userek set tut_kaloz=1 where id='.$aux[0]);
	if ($aux[2]=='hu') $kaloz_nev='Kalóz';else $kaloz_nev='Pirate';
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']-80,$aux['bolygo_y']+20,$aux['bolygo_id'],$kaloz_nev.'-1',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']+60,$aux['bolygo_y']-40,$aux['bolygo_id'],$kaloz_nev.'-2',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']-10,$aux['bolygo_y']-50,$aux['bolygo_id'],$kaloz_nev.'-3',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']+10,$aux['bolygo_y']+70,$aux['bolygo_id'],$kaloz_nev.'-4',array(206=>1,204=>1));
}

//ha valaki mashogy epitkezett, az is kapjon kalozokat
$r=mysql_query('select u.id,u.nev,u.nyelv,b.id as bolygo_id,b.x as bolygo_x,b.y as bolygo_y
from userek u
inner join bolygok b on b.tulaj=u.id
where u.tut_level>3 and u.tut_kaloz=0 and u.tut_kalozjutalom=0
group by u.id');
while($aux=mysql_fetch_array($r)) {
	tut_level($aux[0],3,array($aux[1],$aux[2]));
	//kalozok
	mysql_query('update userek set tut_kaloz=1 where id='.$aux[0]);
	if ($aux[2]=='hu') $kaloz_nev='Kalóz';else $kaloz_nev='Pirate';
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']-80,$aux['bolygo_y']+20,$aux['bolygo_id'],$kaloz_nev.'-1',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']+60,$aux['bolygo_y']-40,$aux['bolygo_id'],$kaloz_nev.'-2',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']-10,$aux['bolygo_y']-50,$aux['bolygo_id'],$kaloz_nev.'-3',array(206=>1));
	uj_kaloz_jutalom_flottat_felrak($aux['bolygo_x']+10,$aux['bolygo_y']+70,$aux['bolygo_id'],$kaloz_nev.'-4',array(206=>1,204=>1));
}

//kalozok lelovese
$r=mysql_query('select u.id,u.nev,u.nyelv,b.id as bolygo_id
from userek u
inner join bolygok b on b.tulaj=u.id
left join flottak f on f.kaloz_bolygo_id=b.id and f.tulaj=0
where u.tut_kaloz=1 and u.tut_kalozjutalom=0
group by u.id
having count(f.id)=0');
while($aux=mysql_fetch_array($r)) {
	if ($aux[2]=='hu') {
		$uzi="Kedves ".$aux[1]."!\n\n";
		$uzi.="Gratulálunk! Minden kalóztól megszabadultál a környéken. Veszteségeid pótlására a jutalom 1000 félvezető.";
		$uzi.="\n\n\nZandagort és népe";
		$uzi.="\n\n".$zanda_ingame_msg_ps['hu'];
		rendszeruzenet_a_kozponti_szolgaltatohaztol($aux[0],'Jutalom',$uzi,'hu');
	} else {
		$uzi="Dear ".$aux[1]."!\n\n";
		$uzi.="Congratulations! You got rid of all pirates around. To compensate for your losses you get 1000 units of chip as bounty.";
		$uzi.="\n\n\nZandagort and his people";
		$uzi.="\n\n".$zanda_ingame_msg_ps['en'];
		rendszeruzenet_a_kozponti_szolgaltatohaztol($aux[0],'Bounty',$uzi,'en');
	}
	mysql_query('update bolygo_eroforras set db=db+1000 where bolygo_id='.$aux['bolygo_id'].' and eroforras_id=73');
	mysql_query('update userek set tut_kalozjutalom=1,tut_kaloz=0 where id='.$aux[0]);
}

//4: 15 perc eltelt a tut_uccso_level ota -> 5. level
$r=mysql_query('select id,nev,nyelv from userek where tut_level=4 and timestampdiff(minute,tut_uccso_level,now())>15');
while($aux=mysql_fetch_array($r)) tut_level($aux[0],5,array($aux[1],$aux[2]));

//6: titanmu elkezd epulni -> 7. level
$r=mysql_query('select u.id,u.nev,u.nyelv
from userek u
inner join bolygok b on b.tulaj=u.id
inner join cron_tabla ct on ct.bolygo_id=b.id and ct.gyar_id=52
where u.tut_level=6
group by u.id');
while($aux=mysql_fetch_array($r)) tut_level($aux[0],7,array($aux[1],$aux[2]));

//8: 4 uveggyar -> 9. level
$r=mysql_query('select u.id,u.nev,u.nyelv
from userek u
inner join bolygok b on b.tulaj=u.id
inner join bolygo_gyar bgy on bgy.bolygo_id=b.id and bgy.gyar_id=54
where u.tut_level=8
group by u.id
having coalesce(sum(bgy.db),0)>=4');
while($aux=mysql_fetch_array($r)) tut_level($aux[0],9,array($aux[1],$aux[2]));

//10: 4500 muanyag -> 11. level
$r=mysql_query('select u.id,u.nev,u.nyelv
from userek u
inner join bolygok b on b.tulaj=u.id
inner join bolygo_eroforras be on be.bolygo_id=b.id and be.eroforras_id=71
where u.tut_level=10
group by u.id
having coalesce(sum(be.db),0)>=4500');
while($aux=mysql_fetch_array($r)) tut_level($aux[0],11,array($aux[1],$aux[2]));

//11: felvezgyar -> 12. level
$r=mysql_query('select u.id,u.nev,u.nyelv
from userek u
inner join bolygok b on b.tulaj=u.id
inner join bolygo_gyar bgy on bgy.bolygo_id=b.id and bgy.gyar_id=55
where u.tut_level=11
group by u.id');
while($aux=mysql_fetch_array($r)) tut_level($aux[0],12,array($aux[1],$aux[2]));

/************************************** TUT_LEVEL ES TECH_SZINT VEGE *****************************************************************/




//automatikus torlesek
//inaktivak torlese (14 napnal regebb ota nem csinalt semmit)
$er=mysql_query('select id from userek where timestampdiff(day,uccso_akt,now())>14 order by id limit 1');$aux=mysql_fetch_array($er);if ($aux) del_ures_user($aux[0]);
if ($admin_nyaral==0) {
	//7 napnal regebb ota inaktiv es nincs aktivalva a regisztracioja
	$er=mysql_query('select id from userek where aktivalo_kulcs!="" and timestampdiff(day,uccso_akt,now())>7 order by id limit 1');$aux=mysql_fetch_array($er);if ($aux) del_ures_user($aux[0]);
}


//torlendo userek (akik tech 4 utan toroltek magukat)
$er=mysql_query('select user_id from torlendo_userek where mikor<now()');
while($aux=mysql_fetch_array($er)) {
	del_ures_user($aux[0]);
	mysql_query('delete from torlendo_userek where user_id='.$aux[0]);
}


//eplista es epites alatti email ertesitesek
$er=mysql_query('select b.nev as bolygo_nev,b.van_e_eplistaban_epulet,b.maradt_eplistaban_epulet,b.van_e_epites_alatti_epulet,b.maradt_epites_alatti_epulet
,u.nev as user_nev,u.email,u.nyelv
,ub.email_noti_eplista,ub.email_noti_epites_alatt
from bolygok b, userek u, user_beallitasok ub where b.tulaj>0 and b.tulaj=u.id and u.id=ub.user_id and (b.van_e_eplistaban_epulet=1 and b.maradt_eplistaban_epulet=0 or b.van_e_epites_alatti_epulet=1 and b.maradt_epites_alatti_epulet=0 and b.maradt_eplistaban_epulet=0)');
while($aux=mysql_fetch_array($er)) {
	if ($aux['van_e_eplistaban_epulet']==1) if ($aux['maradt_eplistaban_epulet']==0) if ($aux['email_noti_eplista']==1) {//eplista kifogyott
		if ($aux['nyelv']=='hu') {
			zandamail('hu',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['user_nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - '.$aux['bolygo_nev'].' bolygódon kifogyott az építési lista',
				'html'	=>	"<p>Kedves {$aux['user_nev']}!</p>
<p>{$aux['bolygo_nev']} bolygódon kifogyott az építési lista, vagyis elkezdődött az utolsó listában lévő gyár építése is. Nem tudjuk, mit a terveid, de talán itt az ideje feltölteni a listát, ha tovább akarod fejleszteni a bolygót.</p>
<p>Ha nem szeretnél a továbbiakban ehhez hasonló értesítéseket kapni, azt a PROFIL menüben tudod beállítani.</p>
",
				'plain'	=>	"Kedves {$aux['user_nev']}!

{$aux['bolygo_nev']} bolygódon kifogyott az építési lista, vagyis elkezdődött az utolsó listában lévő gyár építése is. Nem tudjuk, mit a terveid, de talán itt az ideje feltölteni a listát, ha tovább akarod fejleszteni a bolygót.
Ha nem szeretnél a továbbiakban ehhez hasonló értesítéseket kapni, azt a PROFIL menüben tudod beállítani.
"
			));
		} else {
			zandamail('en',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['user_nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - The construction queue got empty on planet '.$aux['bolygo_nev'],
				'html'	=>	"<p>Dear {$aux['user_nev']}!</p>
<p>The construction queue got empty on planet {$aux['bolygo_nev']}. We don't know your plans, but it might be the right time to fill up the queue, so your planet can develop further.</p>
<p>If you don't want notifications like this, you can change your settings in the PROFILE menu.</p>
",
				'plain'	=>	"Dear {$aux['user_nev']}!

The construction queue got empty on planet {$aux['bolygo_nev']}. We don't know your plans, but it might be the right time to fill up the queue, so your planet can develop further.
If you don't want notifications like this, you can change your settings in the PROFILE menu.
"
			));
		}
	}
	if ($aux['van_e_epites_alatti_epulet']==1) if ($aux['maradt_epites_alatti_epulet']==0) if ($aux['maradt_eplistaban_epulet']==0) if ($aux['email_noti_epites_alatt']==1) {//epites alatti kifogyott
		if ($aux['nyelv']=='hu') {
			zandamail('hu',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['user_nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - '.$aux['bolygo_nev'].' bolygódon megépült minden',
				'html'	=>	"<p>Kedves {$aux['user_nev']}!</p>
<p>{$aux['bolygo_nev']} bolygódon megépült minden. Nem tudjuk, mit a terveid, de talán itt az ideje belekezdeni pár új építkezésbe, ha tovább akarod fejleszteni a bolygót.</p>
<p>Ha nem szeretnél a továbbiakban ehhez hasonló értesítéseket kapni, azt a PROFIL menüben tudod beállítani.</p>
",
				'plain'	=>	"Kedves {$aux['user_nev']}!

{$aux['bolygo_nev']} bolygódon megépült minden. Nem tudjuk, mit a terveid, de talán itt az ideje belekezdeni pár új építkezésbe, ha tovább akarod fejleszteni a bolygót.
Ha nem szeretnél a továbbiakban ehhez hasonló értesítéseket kapni, azt a PROFIL menüben tudod beállítani.
"
			));
		} else {
			zandamail('en',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['user_nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - Everything has been built on planet '.$aux['bolygo_nev'],
				'html'	=>	"<p>Dear {$aux['user_nev']}!</p>
<p>Everything has been built on planet {$aux['bolygo_nev']}. We don't know your plans, but it might be the right time to start building some more factories, so your planet can develop further.</p>
<p>If you don't want notifications like this, you can change your settings in the PROFILE menu.</p>
",
				'plain'	=>	"Dear {$aux['user_nev']}!

Everything has been built on planet {$aux['bolygo_nev']}. We don't know your plans, but it might be the right time to start building some more factories, so your planet can develop further.
If you don't want notifications like this, you can change your settings in the PROFILE menu.
"
			));
		}
	}
}




//email ertesites
if (!$inaktiv_szerver) if ($vegjatek==0) {
	//7 nap
	$er=mysql_query('select id,nev,email,nyelv from userek where timestampdiff(day,uccso_akt,now())>7 and inaktivitasi_ertesito<7 order by uccso_akt desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) {
		if ($aux['nyelv']=='hu') {
			zandamail('hu',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - Merre vagy?',
				'html'	=>	"<p>Kedves {$aux['nev']}!</p><p>Azért kapod ezt a levelet, mert több, mint egy hete nem jelentkeztél be a játékba. Természetesen ez szíved joga, de ne feledd, hogy 14 nap inaktivitás után törlődik az accountod. Vagyis, ha szeretnél tovább játszani, érdemes bejelentkezned a <a href=\"".$zanda_game_url['hu']."\">".$zanda_game_url['hu']."</a> címen. További szép napot!</p>",
				'plain'	=>	"Kedves {$aux['nev']}!\n\nAzért kapod ezt a levelet, mert több, mint egy hete nem jelentkeztél be a játékba. Természetesen ez szíved joga, de ne feledd, hogy 14 nap inaktivitás után törlődik az accountod. Vagyis, ha szeretnél tovább játszani, érdemes bejelentkezned a ".$zanda_game_url['hu']." címen. További szép napot!\n"
			));
		} else {
			zandamail('en',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - Where are you?',
				'html'	=>	"<p>Dear {$aux['nev']}!</p><p>You're getting this mail because you haven't been playing for more than a week now. It's your choice of course, but don't forget that your account will be deleted after 14 days of inactivity. So if you want to continue playing sign in at <a href=\"".$zanda_game_url['en']."\">".$zanda_game_url['en']."</a>. Have a nice day!</p>",
				'plain'	=>	"Dear {$aux['nev']}!\n\nYou're getting this mail because you haven't been playing for more than a week now. It's your choice of course, but don't forget that your account will be deleted after 14 days of inactivity. So if you want to continue playing sign in at ".$zanda_game_url['en']." . Have a nice day!\n"
			));
		}
		mysql_query('update userek set inaktivitasi_ertesito=7 where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
	}
	//3 nap
	$er=mysql_query('select id,nev,email,nyelv from userek where timestampdiff(day,uccso_akt,now())>3 and inaktivitasi_ertesito<3 order by uccso_akt desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) {
		if ($aux['nyelv']=='hu') {
			zandamail('hu',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - Merre vagy?',
				'html'	=>	"<p>Kedves {$aux['nev']}!</p><p>Azért kapod ezt a levelet, mert több, mint 3 napja nem jelentkeztél be a játékba. Természetesen ez szíved joga, de ne feledd, hogy 14 nap inaktivitás után törlődik az accountod. Vagyis, ha szeretnél tovább játszani, érdemes bejelentkezned a <a href=\"".$zanda_game_url['hu']."\">".$zanda_game_url['hu']."</a> címen. További szép napot!</p>",
				'plain'	=>	"Kedves {$aux['nev']}!\n\nAzért kapod ezt a levelet, mert több, mint 3 napja nem jelentkeztél be a játékba. Természetesen ez szíved joga, de ne feledd, hogy 14 nap inaktivitás után törlődik az accountod. Vagyis, ha szeretnél tovább játszani, érdemes bejelentkezned a ".$zanda_game_url['hu']." címen. További szép napot!\n"
			));
		} else {
			zandamail('en',array(
				'email'	=>	$aux['email'],
				'name'	=>	$aux['nev'],
				'subject'	=>	'Zandagort '.$szerver_prefix.' - Where are you?',
				'html'	=>	"<p>Dear {$aux['nev']}!</p><p>You're getting this mail because you haven't been playing for more than 3 days now. It's your choice of course, but don't forget that your account will be deleted after 14 days of inactivity. So if you want to continue playing sign in at <a href=\"".$zanda_game_url['en']."\">".$zanda_game_url['en']."</a>. Have a nice day!</p>",
				'plain'	=>	"Dear {$aux['nev']}!\n\nYou're getting this mail because you haven't been playing for more than 3 days now. It's your choice of course, but don't forget that your account will be deleted after 14 days of inactivity. So if you want to continue playing sign in at ".$zanda_game_url['en']." . Have a nice day!\n"
			));
		}
		mysql_query('update userek set inaktivitasi_ertesito=3 where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
	}
}


/************************************** STATISZTIKAK ELEJE *****************************************************************/
if ($idopont%60==27) {
$huszonnegy_oraja=date('Y-m-d H:i:s',time()-3600*24);
//admin, sock puppetek es egyebek aktivalasa
foreach($specko_userek_listaja as $id) {
	mysql_query('update userek set uccso_akt="'.$huszonnegy_oraja.'" where id='.$id);
}
foreach($specko_szovetsegek_listaja as $id) {
	mysql_query('update userek set uccso_akt="'.$huszonnegy_oraja.'" where szovetseg='.$id);
}
//hataridoig kitiltottak aktivalasa
mysql_query('update userek set uccso_akt="'.$huszonnegy_oraja.'" where kitiltva_meddig>now()');



//regi cset hozzaszolasok torlese
mysql_query('delete from cset_hozzaszolasok where szov_id>-1000 and mikor<"'.date('Y-m-d H:i:s',time()-3600).'"');
mysql_query('delete from cset_hozzaszolasok where szov_id<=-1000 and mikor<"'.date('Y-m-d H:i:s',time()-3600*24).'"');


//pontszam frissites (uj, reszflottas)
//a nullpontot ujraszamolni, ha valtozik a kezdokeszlet (pl anyahajo, varos)
mysql_query('
update userek u, (select u.id,coalesce(bpont,0)+coalesce(fpont,0)-coalesce(levonando.pontertek,0)+coalesce(hozzaadando.pontertek,0) as pont
from userek u
left join (select u.id,coalesce(sum(b.pontertek),0) as bpont from userek u left join bolygok b on b.tulaj=u.id group by u.id) b on b.id=u.id
left join (select u.id,coalesce(sum(f.pontertek),0) as fpont from userek u left join flottak f on f.tulaj=u.id group by u.id) f on f.id=u.id
left join (select f.tulaj,round(sum(rfh.hp/rfh.ossz_hp*fh.ossz_hp*e.pontertek)) as pontertek
from resz_flotta_hajo rfh, flottak f, flotta_hajo fh, eroforrasok e
where rfh.flotta_id=f.id and rfh.flotta_id=fh.flotta_id
and rfh.hajo_id=fh.hajo_id and rfh.hajo_id=e.id
group by f.tulaj) levonando on levonando.tulaj=u.id
left join (select rfh.user_id,round(sum(rfh.hp/rfh.ossz_hp*fh.ossz_hp*e.pontertek)) as pontertek
from resz_flotta_hajo rfh, flottak f, flotta_hajo fh, eroforrasok e
where rfh.flotta_id=f.id and rfh.flotta_id=fh.flotta_id
and rfh.hajo_id=fh.hajo_id and rfh.hajo_id=e.id
group by rfh.user_id) hozzaadando on hozzaadando.user_id=u.id
group by u.id) t
set u.pontszam=round(greatest(u.vagyon+t.pont-2894180000,0)/1000)
where u.id=t.id
');



// 3 napos -> 3x24 oras
// alfa = 2/(N+1) = 2/(3x24+1) = 0.02739726 = 0.027
// 1-alfa = 0.972602739 = 0.973
mysql_query('update userek set pontszam_exp_atlag=round(0.027*pontszam+0.973*pontszam_exp_atlag)');


//szovetseg helyezesek
mysql_query('update szovetsegek set helyezes=0');
$r=mysql_query('select sz.id from szovetsegek sz, userek u where u.szovetseg=sz.id and sz.id not in ('.implode(',',$specko_szovetsegek_listaja).') and u.id not in ('.implode(',',$specko_userek_listaja).') group by sz.id order by sum(u.pontszam_exp_atlag) desc');
$n=0;while($aux=mysql_fetch_array($r)) {
	$n++;
	mysql_query('update szovetsegek set helyezes='.$n.' where id='.$aux[0]);
}


//akt_stat
$er=mysql_query('select count(1) from userek') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$user_szam=$aux[0];
$akt_stat_hosszak=array(1,5,10,15,60,1440,4320,10080);
for($i=0;$i<count($akt_stat_hosszak);$i++) {
	$er=mysql_query('select count(1) from (select id,nev,pontszam,coalesce(timestampdiff(minute,uccso_akt,now()),coalesce(1440-timestampdiff(minute,now(),session_ervenyesseg),coalesce(timestampdiff(minute,uccso_login,now()),timestampdiff(minute,mikortol,now())))) as utoljara from userek) t where utoljara<='.$akt_stat_hosszak[$i]) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);$akt_stat_db[$i]=$aux[0];
}
mysql_select_db($database_mmog_nemlog);
mysql_query('insert into akt_stat (mikor,akt_1_perc,akt_5_perc,akt_10_perc,akt_15_perc,akt_1_ora,akt_24_ora,akt_3_nap,akt_7_nap,ossz) values("'.date('Y-m-d H:i:s').'",'.implode(',',$akt_stat_db).','.$user_szam.')') or hiba(__FILE__,__LINE__,mysql_error());
mysql_select_db($database_mmog);
}
if ($idopont%60==27) {
	if ($vegjatek==1) {
		hist_snapshot($idopont%360==327);//a vegso csata alatt orankenti mentes, leszamitva a hist_termelesek-et, ami csak 6 orankent
	} else {
		if ($idopont%360==327) hist_snapshot();//6 orankent mentes az adattarhazba
	}
}
/************************************** STATISZTIKAK VEGE *****************************************************************/
if ($idopont%1440==35) {
	mysql_select_db($database_mmog_nemlog);
	mysql_query('update multi_matrix set pont=round(0.9*pont),minusz_pont=round(0.9*minusz_pont)');//naponta egyszer csokkenteni a multipontokat
	mysql_query('delete from loginek where timestampdiff(minute,mikor,now())>1440');//24 oranal regebbi loginek torlese, h gyorsabb legyen a tabla
	mysql_select_db($database_mmog);
}

/************************************** TOZSDEI GYERTYAK ELEJE *****************************************************************/
if ($idopont%60==15) {
//tozsdei gyertyak
$er=mysql_query('select id from eroforrasok where tozsdezheto order by id');
while($aux=mysql_fetch_array($er)) $termek_idk[]=$aux[0];
$er=mysql_query('select id from regiok order by id');
while($aux=mysql_fetch_array($er)) $regiok[]=$aux[0];

mysql_select_db($database_mmog_nemlog);
foreach($regiok as $regio) {
	$tozsdei_kotesek_tabla='tozsdei_kotesek';
	//napi
	if (date('H')=='00') {
		$datumtol=date('Y-m-d',time()-3600).' 00:00:00';
		$datumig=date('Y-m-d',time()-3600).' 23:59:59';
		for($termek_sorszam=0;$termek_sorszam<count($termek_idk);$termek_sorszam++) {
			$termek=$termek_idk[$termek_sorszam];
			$er=mysql_query('select count(1),sum(mennyiseg),min(arfolyam),max(arfolyam) from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'"');$aux=mysql_fetch_array($er);
			$kotesszam=$aux[0];$otszazalek=round($kotesszam*0.05);
			if ($kotesszam>0) {
				$forgalom=$aux[1];
				$min_ar=$aux[2];
				$max_ar=$aux[3];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor limit 1');$aux=mysql_fetch_array($er);
				$nyito_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor desc limit 1');$aux=mysql_fetch_array($er);
				$zaro_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
				$min5_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam desc limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
				$max5_ar=$aux[0];
				mysql_query("insert into tozsdei_gyertyak (regio,termek_id,felbontas,mikor,nyito_ar,zaro_ar,min_ar,max_ar,min5_ar,max5_ar,forgalom) values($regio,$termek,3,\"$datumtol\",$nyito_ar,$zaro_ar,$min_ar,$max_ar,$min5_ar,$max5_ar,$forgalom)");
			}
		}
	}
	//harmadnapi
	if (date('H')=='00' || date('H')=='08' || date('H')=='16') {
		$datumtol=date('Y-m-d H',time()-8*3600).':00:00';
		$datumig=date('Y-m-d H',time()-3600).':59:59';
		for($termek_sorszam=0;$termek_sorszam<count($termek_idk);$termek_sorszam++) {
			$termek=$termek_idk[$termek_sorszam];
			$er=mysql_query('select count(1),sum(mennyiseg),min(arfolyam),max(arfolyam) from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'"');$aux=mysql_fetch_array($er);
			$kotesszam=$aux[0];$otszazalek=round($kotesszam*0.05);
			if ($kotesszam>0) {
				$forgalom=$aux[1];
				$min_ar=$aux[2];
				$max_ar=$aux[3];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor limit 1');$aux=mysql_fetch_array($er);
				$nyito_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor desc limit 1');$aux=mysql_fetch_array($er);
				$zaro_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
				$min5_ar=$aux[0];
				$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam desc limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
				$max5_ar=$aux[0];
				mysql_query("insert into tozsdei_gyertyak (regio,termek_id,felbontas,mikor,nyito_ar,zaro_ar,min_ar,max_ar,min5_ar,max5_ar,forgalom) values($regio,$termek,2,\"$datumtol\",$nyito_ar,$zaro_ar,$min_ar,$max_ar,$min5_ar,$max5_ar,$forgalom)");
			}
		}
	}
	//orankenti
	$datumtol=date('Y-m-d H',time()-3600).':00:00';
	$datumig=date('Y-m-d H',time()-3600).':59:59';
	for($termek_sorszam=0;$termek_sorszam<count($termek_idk);$termek_sorszam++) {
		$termek=$termek_idk[$termek_sorszam];
		$er=mysql_query('select count(1),sum(mennyiseg),min(arfolyam),max(arfolyam) from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'"');$aux=mysql_fetch_array($er);
		$kotesszam=$aux[0];$otszazalek=round($kotesszam*0.05);
		if ($kotesszam>0) {
			$forgalom=$aux[1];
			$min_ar=$aux[2];
			$max_ar=$aux[3];
			$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor limit 1');$aux=mysql_fetch_array($er);
			$nyito_ar=$aux[0];
			$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by mikor desc limit 1');$aux=mysql_fetch_array($er);
			$zaro_ar=$aux[0];
			$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
			$min5_ar=$aux[0];
			$er=mysql_query('select arfolyam from '.$tozsdei_kotesek_tabla.' where regio='.$regio.' and termek_id='.$termek.' and mikor>="'.$datumtol.'" and mikor<="'.$datumig.'" order by arfolyam desc limit '.$otszazalek.',1');$aux=mysql_fetch_array($er);
			$max5_ar=$aux[0];
			mysql_query("insert into tozsdei_gyertyak (regio,termek_id,felbontas,mikor,nyito_ar,zaro_ar,min_ar,max_ar,min5_ar,max5_ar,forgalom) values($regio,$termek,1,\"$datumtol\",$nyito_ar,$zaro_ar,$min_ar,$max_ar,$min5_ar,$max5_ar,$forgalom)");
		}
	}
}
mysql_select_db($database_mmog);
}
/************************************** TOZSDEI GYERTYAK VEGE *****************************************************************/



/************************************** BADGE-EK ELEJE *****************************************************************/
//nepesseg (1-6)
$case_str=get_badgek_case_str(array(1,2,3,4,5,6));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select u.id as user_id
,if(u.ossz_nepesseg<1000000,1
,if(u.ossz_nepesseg<10000000,2
,if(u.ossz_nepesseg<100000000,3
,if(u.ossz_nepesseg<1000000000,4
,if(u.ossz_nepesseg<10000000000,5,6
))))) as badge_id
from userek u
where u.ossz_nepesseg>=100000

) t');

//varosok (13-18)
$case_str=get_badgek_case_str(array(13,14,15,16,17,18));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select u.id as user_id
,if(sum(bgy.aktiv_db)<100,13
,if(sum(bgy.aktiv_db)<1000,14
,if(sum(bgy.aktiv_db)<10000,15
,if(sum(bgy.aktiv_db)<100000,16
,if(sum(bgy.aktiv_db)<1000000,17,18
))))) as badge_id
from userek u, bolygok b, bolygo_gyar bgy
where u.id=b.tulaj and b.id=bgy.bolygo_id and bgy.gyar_id=78 and b.tulaj>0
group by u.id
having sum(bgy.aktiv_db)>=10

) t');

//bolygok (19-23)
$case_str=get_badgek_case_str(array(19,20,21,22,23));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<20,19
,if(count(1)<30,20
,if(count(1)<40,21
,if(count(1)<50,22,23
)))) as badge_id
from bolygok b
where b.tulaj>0
group by b.tulaj
having count(1)>=10

) t');

//A-bolygok
$case_str=get_badgek_case_str(array(24,25,26));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<5,24
,if(count(1)<10,25,26
)) as badge_id
from bolygok b
where b.tulaj>0 and b.osztaly=1
group by b.tulaj
having count(1)>=3

) t');
//B-bolygok
$case_str=get_badgek_case_str(array(27,28,29));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<5,27
,if(count(1)<10,28,29
)) as badge_id
from bolygok b
where b.tulaj>0 and b.osztaly=2
group by b.tulaj
having count(1)>=3

) t');
//C-bolygok
$case_str=get_badgek_case_str(array(30,31,32));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<5,30
,if(count(1)<10,31,32
)) as badge_id
from bolygok b
where b.tulaj>0 and b.osztaly=3
group by b.tulaj
having count(1)>=3

) t');
//D-bolygok
$case_str=get_badgek_case_str(array(33,34,35));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<5,33
,if(count(1)<10,34,35
)) as badge_id
from bolygok b
where b.tulaj>0 and b.osztaly=4
group by b.tulaj
having count(1)>=3

) t');
//E-bolygok
$case_str=get_badgek_case_str(array(36,37,38));
mysql_query('insert ignore into user_badge (user_id,badge_id,szin)
select user_id,badge_id,case badge_id '.$case_str.' end
from (

select b.tulaj as user_id
,if(count(1)<5,36
,if(count(1)<10,37,38
)) as badge_id
from bolygok b
where b.tulaj>0 and b.osztaly=5
group by b.tulaj
having count(1)>=3

) t');


//tul sok aranyat es ezustot bronzositani
$r=mysql_query('select badge_id,sum(szin=1),sum(szin=2) from user_badge group by badge_id having sum(szin=1)>10 or sum(szin=2)>50');
while($aux=mysql_fetch_array($r)) {
	if ($aux[1]>10) mysql_query('update user_badge set szin=3 where badge_id='.$aux[0].' and szin=1');
	elseif ($aux[2]>50) mysql_query('update user_badge set szin=3 where badge_id='.$aux[0].' and szin=2');
}

//ertesites es publikalas
//1-es badge (POP-100k) ne legyen, mert a TL-ek mellett folosleges
$r=mysql_query('select ub.*,us.badge_pub,b.cim,b.alcim,b.leiras_hu,b.leiras_en
from user_badge ub, user_beallitasok us, badgek b
where ub.user_id=us.user_id and ub.bejelentett=0 and ub.badge_id!=1 and ub.badge_id=b.id');
while($aux=mysql_fetch_array($r)) {
	if ($aux['szin']==1) {$szin_hu='arany';$szin_en='gold';}
	elseif ($aux['szin']==2) {$szin_hu='ezüst';$szin_en='silver';}
	else {$szin_hu='bronz';$szin_en='bronze';}
	rendszeruzenet_html($aux['user_id']
		,'Új '.$szin_hu.' plecsnit kaptál: '.$aux['leiras_hu'],'<a href="#" onclick="return user_katt('.$aux['user_id'].')"><div title="'.$aux['leiras_hu'].'" style="position:relative;display:inline-block;width:64px;height:64px;background:transparent url(img/ikonok/zanda_badge_'.$aux['szin'].'.png)"><div style="text-align:center;font-size:14pt;font-weight:bold;color:rgb(42,43,45);margin-top:20px">'.$aux['cim'].'</div><div style="text-align:center;font-size:8pt;font-weight:bold;color:rgb(42,43,45);margin-top:0px">'.$aux['alcim'].'</div></div></a>'
		,'You got a new '.$szin_en.' badge: '.$aux['leiras_en'],'<a href="#" onclick="return user_katt('.$aux['user_id'].')"><div title="'.$aux['leiras_en'].'" style="position:relative;display:inline-block;width:64px;height:64px;background:transparent url(img/ikonok/zanda_badge_'.$aux['szin'].'.png)"><div style="text-align:center;font-size:14pt;font-weight:bold;color:rgb(42,43,45);margin-top:20px">'.$aux['cim'].'</div><div style="text-align:center;font-size:8pt;font-weight:bold;color:rgb(42,43,45);margin-top:0px">'.$aux['alcim'].'</div></div></a>'
	);
	mysql_query('update user_badge set bejelentett=1, publikus='.$aux['badge_pub'].' where user_id='.$aux['user_id'].' and badge_id='.$aux['badge_id']);
}

/************************************** BADGE-EK VEGE *****************************************************************/



/*******************************************************************************************************/
$szimlog_hossz=round(1000*(microtime(true)-$mikor_indul));
mysql_query('do release_lock("'.$szimlock_name.'")');

mysql_select_db($database_mmog_nemlog);
mysql_query("insert into szim_log (idopont,hossz_npc,hossz_monetaris,hossz_termeles,hossz_felderites,hossz_flottamoral,hossz_flottak,hossz_csatak,hossz,hossz_debug_elott,hossz_debug_utan,hossz_ostromok,hossz_fog) values($idopont,$szimlog_hossz_npc,$szimlog_hossz_monetaris,$szimlog_hossz_termeles,$szimlog_hossz_felderites,$szimlog_hossz_flottamoral,$szimlog_hossz_flottak,$szimlog_hossz_csatak,$szimlog_hossz,$szimlog_hossz_debug_elott,$szimlog_hossz_debug_utan,$szimlog_hossz_ostromok,$szimlog_hossz_fog)") or hiba(__FILE__,__LINE__,mysql_error());
mysql_select_db($database_mmog);

$lock_rendben=1;
} else $lock_rendben=0;//sikeres lock vege
$mikor_vegzodik=microtime(true);




echo ' '.round(1000*($mikor_vegzodik-$mikor_indul)).($lock_rendben?'':' LOCK');
insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));
}
mysql_close($mysql_csatlakozas);
?>