<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
if ($_REQUEST['id']==0) $res=mysql_query('select *,timestampdiff(day,alapitas,now()) as alapitva from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
else $res=mysql_query('select *,timestampdiff(day,alapitas,now()) as alapitva from szovetsegek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($res);

if (!$res || $szovetseg['id']==0) {//maganyos farkas sajat adatlapjat
?>
/*{"letezik":0,"nev":"<?=addslashes($adataim['nev'])?>","meghivoid":<?
echo mysql2jsonmatrix('select szm.ki,u.nev,szm.hova,sz.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivok szm, szovetsegek sz, userek u where szm.kit='.$uid.' and szm.hova=sz.id and szm.ki=u.id');
?>,"meghivo_kerelmeid":<?
echo mysql2jsonmatrix('select 0,"",szm.hova,sz.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivas_kerelmek szm, szovetsegek sz where szm.ki='.$uid.' and szm.hova=sz.id');

$bolygoid_szama=mysql2num('select count(1) from bolygok where tulaj='.$uid);
?>,"nincs_bolygod":<?
if ($bolygoid_szama>0) echo '0';else echo '1';
?>,"szovetsegek":<?
//kozeli szoviket, ha van bolygod
if ($bolygoid_szama>0) {
echo mysql2jsonmatrix('select sz.id,sz.nev,u.id,u.nev,timestampdiff(day,sz.alapitas,now()),motto,tagletszam,alapito_elnevezese,if(length(sz.minicimer_ext)>0,concat("p",sz.id,sz.minicimer_crc,".",sz.minicimer_ext),"") as minicimer_fajlnev, round(sqrt(min(pow(bsz.x-bu.x,2)+pow(bsz.y-bu.y,2)))/2) as tavolsag, sz.zart
from szovetsegek sz, userek u, bolygok bsz, bolygok bu
where sz.alapito=u.id
and bsz.tulaj_szov=sz.id and bsz.tulaj!='.$uid.' and bu.tulaj='.$uid.'
and bsz.letezik=1 and bu.letezik=1
group by sz.id
order by tavolsag, sz.nev');
} else {
echo mysql2jsonmatrix('select sz.id,sz.nev,u.id,u.nev,timestampdiff(day,sz.alapitas,now()),motto,tagletszam,alapito_elnevezese,if(length(sz.minicimer_ext)>0,concat("p",sz.id,sz.minicimer_crc,".",sz.minicimer_ext),"") as minicimer_fajlnev, -1, sz.zart
from szovetsegek sz, userek u
where sz.alapito=u.id
order by sz.nev');
}
?>,"statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki=-'.$uid.'
order by s.mi,s.kivel,s.miota');
?>,"leendo_statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_leendo_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki=-'.$uid.'
order by s.mi,s.kivel,s.miota');
?>,"ajanlatok":<?
echo mysql2jsonmatrix('
select a.id as ajanlat_id,a.kinek,if(a.kinek>0,szo.id,u.id),if(a.kinek>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,1,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.kinek=-u.id
left join szovetsegek szo on a.kinek=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.ki=-'.$uid.'
union all
select a.id as ajanlat_id,a.ki,if(a.ki>0,szo.id,u.id),if(a.ki>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,0,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.ki=-u.id
left join szovetsegek szo on a.ki=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.kinek=-'.$uid.'
order by ajanlat_mikor desc, ajanlat_id desc
');

?>,"vendegsegek":<?
echo mysql2jsonmatrix('select sz.id,sz.nev,szv.mikortol,um.id,um.nev
from szovetseg_vendegek szv
inner join szovetsegek sz on sz.id=szv.szov_id
left join userek um on um.id=szv.ki_hivta
where szv.user_id='.$uid.'
order by sz.nev,sz.id');

?>}*/
<?
kilep();
}

?>
/*{"letezik":1,"id":<?=$szovetseg['id'];?>,"tag_vagy":<?
if ($adataim['szovetseg']==$szovetseg['id']) echo 1;else echo 0;
?>,"alapito_vagy":<?
if ($szovetseg['alapito']==$uid) echo 1;else echo 0;
?>,"nev":"<?
echo addslashes($szovetseg['nev']);
?>","rovid_nev":"<?
echo addslashes($szovetseg['rovid_nev']);
?>","motto":"<?
echo addslashes($szovetseg['motto']);
?>","kepfajl_random":"<?
echo addslashes($szovetseg['kepfajl_random']);
?>","minicimer_fajlnev":"<?
if (strlen($szovetseg['minicimer_ext'])) {
	echo 'p'.$szovetseg['id'].$szovetseg['minicimer_crc'].'.'.$szovetseg['minicimer_ext'];
} else echo '';
?>","cimer_fajlnev":"<?
if (szov_premium_szint($szovetseg['id'])>0 && strlen($szovetseg['cimer_ext'])) {
	echo 'p'.$szovetseg['id'].$szovetseg['cimer_crc'].'.'.$szovetseg['cimer_ext'];
} else echo '';
?>","alapitva":<?
echo $szovetseg['alapitva'];//hany napja
?>,"alapito":<?
echo $szovetseg['alapito'];
?>,"alapito_neve":"<?
$res2=mysql_query('select nev from userek where id='.$szovetseg['alapito']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2[0]) echo addslashes($aux2[0]);else echo '-';
?>","alapito_elnevezese":"<?
echo $szovetseg['alapito_elnevezese'];
?>","tagletszam":<?
echo $szovetseg['tagletszam'];
?>,"zart":<?
echo $szovetseg['zart'];
?>,"meghivo_kerelmed_ide":<?
echo mysql2num('select count(1) from szovetseg_meghivas_kerelmek where ki='.$uid.' and hova='.$szovetseg['id']);
?>,"premium":<?
echo premium_szint();
?>,"meghivoid":<?
echo mysql2jsonmatrix('select szm.ki,u.nev,szm.hova,sz.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivok szm, szovetsegek sz, userek u where szm.kit='.$uid.' and szm.hova=sz.id and szm.ki=u.id');
?>,"meghivo_kerelmeid":<?
echo mysql2jsonmatrix('select 0,"",szm.hova,sz.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivas_kerelmek szm, szovetsegek sz where szm.ki='.$uid.' and szm.hova=sz.id');
?>,"udvozlet":<?
$res2=mysql_query('select udvozlet,szabalyzat from szovetseg_szabalyzatok where id='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
echo json_encode(($aux2[0]));
if ($adataim['szovetseg']==$szovetseg['id']) {//tagoknak
/*****************************************************************************************/
?>,"szabalyzat":<?
echo json_encode(($aux2[1]));

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$szovetseg['id'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
?>,"diplomata_jogod":<?
echo $jogaim[7];
?>,"vendeg_jogod":<?
echo $jogaim[6];
?>,"jogaid":[<?
$jog_str='';
for($jj=1;$jj<=$jogok_szama;$jj++) {
	if ($szovetseg['alapito']==$uid || $tiszt_jog['jog_'.$jj]) echo 1;else echo 0;
	if ($jj<$jogok_szama) echo ',';
	$jog_str.=',jog_'.$jj;
}
?>],"tisztseged":"<?
if ($aux2) {
	echo addslashes($aux2['nev']);
} else {
	if ($adataim['tisztseg']==-1) echo $szovetseg['alapito_elnevezese'];
	else echo ($lang_lang=='hu')?'Tag':'Ordinary member';
}
?>","tisztsegek":<?
echo mysql2jsonmatrix('select id,nev'.$jog_str.' from szovetseg_tisztsegek where szov_id='.$szovetseg['id'].' order by nev');

if ($adataim['tisztseg']==-1 || $tiszt_jog['jog_2']) {//meghivo embereknek
?>,"maganyos_farkasok":<?
echo mysql2jsonmatrix('select round(sqrt(min(pow(bsz.x-bu.x,2)+pow(bsz.y-bu.y,2)))/2) as tavolsag, u.id, u.nev, u.nyelv
from bolygok bsz, bolygok bu, userek u
where bsz.tulaj_szov='.$szovetseg['id'].' and bu.tulaj_szov<0 and bu.tulaj=u.id
and bsz.letezik=1 and bu.letezik=1
and u.karrier!=3 and u.speci!=3
group by u.id
having tavolsag<=10000
order by tavolsag
limit 50');
}

if ($adataim['tisztseg']==-1 || $tiszt_jog['jog_5']) {
?>,"kozos_flottak":<?
echo mysql2jsonmatrix('select f.id as flotta_id,f.nev as flotta_nev,f.x,f.y,f.kezelo,uk.nev
,f.statusz,b.id,b.kulso_nev,b.osztaly

,if(f.statusz in (12,13,14),f2.id,null)
,if(f.statusz in (12,13,14),f2.nev,null)

,f.bazis_x,f.bazis_y,f.cel_x,f.cel_y
,round(sum(fh.ossz_hp*h.ar/100)),if(f.statusz in (6,7,9,11),round(sqrt(pow(f.x-b.x,2)+pow(f.y-b.y,2))/f.sebesseg)
,if(f.statusz in (12,13),round(sqrt(pow(f.x-f2.x,2)+pow(f.y-f2.y,2))/f.sebesseg),if(f.statusz='.STATUSZ_MEGY_XY.',round(sqrt(pow(f.x-f.cel_x,2)+pow(f.y-f.cel_y,2))/f.sebesseg),-1)))
,if(f.tulaj='.$uid.',1,0) as sajat,round(sum(fh.ossz_hp*fh.moral/10)/sum(fh.ossz_hp)) as mor,if(f.tulaj='.$uid.',1,if(f.tulaj_szov='.$szovetseg['id'].',2,3+coalesce(dsz.mi,0))) as diplo
,f.kozos,tul.id,tul.nev
from flottak f
inner join flotta_hajo fh on fh.flotta_id=f.id
inner join hajok h on fh.hajo_id=h.id
inner join userek tul on tul.id=f.tulaj
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'
left join userek uk on uk.id=f.kezelo

left join bolygok b on b.id=
case
when f.statusz=1 then f.bolygo
when f.statusz in (6,7,8,9,10) then f.cel_bolygo
when f.statusz=11 then f.bazis_bolygo
else 0
end

left join flottak f2 on f2.id=f.cel_flotta
where f.kozos=1 and f.tulaj_szov='.$szovetseg['id'].'
group by f.id
order by flotta_nev,flotta_id
');
}

?>,"meghivok":<?
echo mysql2jsonmatrix('select szm.ki,u.nev,szm.kit,u2.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivok szm, userek u, userek u2 where szm.hova='.$szovetseg['id'].' and szm.kit=u2.id and szm.ki=u.id order by u2.nev,u2.id');
?>,"meghivo_kerelmek":<?
echo mysql2jsonmatrix('select 0,"",szm.ki,u.nev,timestampdiff(day,szm.mikor,now()) from szovetseg_meghivas_kerelmek szm, userek u where szm.hova='.$szovetseg['id'].' and szm.ki=u.id order by u.nev,u.id');

?>,"tagok":<?
echo mysql2jsonmatrix('select u.id,u.nev,timestampdiff(day,u.szov_belepes,now()),u.tisztseg,szt.nev
,if(u.id=1,1440,timestampdiff(minute,u.uccso_akt,now()))
,u.pontszam_exp_atlag
,count(b.id),if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),""),u.nyelv
,coalesce(szt.jog_7,0)
,u.karrier,u.speci
from userek u
left join szovetseg_tisztsegek szt on u.tisztseg=szt.id and szt.szov_id='.$szovetseg['id'].'
left join bolygok b on b.tulaj=u.id
where u.szovetseg='.$szovetseg['id'].'
group by u.id
order by if(u.tisztseg=0,100,0), szt.nev, u.nev');

?>,"vendegek":<?
echo mysql2jsonmatrix('select u.id,u.nev,szv.mikortol,um.id,um.nev
from szovetseg_vendegek szv
inner join userek u on u.id=szv.user_id
left join userek um on um.id=szv.ki_hivta
where szv.szov_id='.$szovetseg['id'].'
order by u.nev,u.id');

?>,"statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki='.$szovetseg['id'].'
order by s.mi,s.kivel,s.miota');
?>,"leendo_statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_leendo_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki='.$szovetseg['id'].'
order by s.mi,s.kivel,s.miota');
?>,"ajanlatok":<?
echo mysql2jsonmatrix('
select a.id as ajanlat_id,a.kinek,if(a.kinek>0,szo.id,u.id),if(a.kinek>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,1,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.kinek=-u.id
left join szovetsegek szo on a.kinek=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.ki='.$szovetseg['id'].'
union all
select a.id as ajanlat_id,a.ki,if(a.ki>0,szo.id,u.id),if(a.ki>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,0,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.ki=-u.id
left join szovetsegek szo on a.ki=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.kinek='.$szovetseg['id'].'
order by ajanlat_mikor desc, ajanlat_id desc
');
/*****************************************************************************************/
} else {//kulsosoknek
/*****************************************************************************************/
//nyilvanos diplok:

//vendeg vagy-e
$vendeg_vagy=0;
$vendeg=mysql2row('select * from szovetseg_vendegek where szov_id='.$szovetseg['id'].' and user_id='.$uid);
if ($vendeg) $vendeg_vagy=1;

if ($vendeg_vagy) {
?>,"vendegek":<?
echo mysql2jsonmatrix('select u.id,u.nev,szv.mikortol,um.id,um.nev
from szovetseg_vendegek szv
inner join userek u on u.id=szv.user_id
left join userek um on um.id=szv.ki_hivta
where szv.szov_id='.$szovetseg['id'].'
order by u.nev,u.id');
}

?>,"statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki='.$szovetseg['id'].' and (s.nyilvanos=1 or '.$vendeg_vagy.'=1)
order by s.mi,s.kivel,s.miota');
?>,"leendo_statuszok":<?
echo mysql2jsonmatrix('select s.kivel,if(s.kivel>0,sz.id,u.id),if(s.kivel>0,sz.nev,u.nev),s.mi,s.miota,s.szoveg_id,if(s.kezdemenyezo=s.ki,1,0),s.szoveg_reszlet,s.felbontasi_ido,s.felbontas_alatt,s.felbontas_mikor
,u_kezd_1.id,u_kezd_1.nev,u_kezd_2.id,u_kezd_2.nev,s.nyilvanos
from diplomacia_leendo_statuszok s
left join userek u on s.kivel=-u.id
left join szovetsegek sz on s.kivel=sz.id
left join userek u_kezd_1 on s.diplo_1=u_kezd_1.id
left join userek u_kezd_2 on s.diplo_2=u_kezd_2.id
where s.ki='.$szovetseg['id'].' and (s.nyilvanos=1 or '.$vendeg_vagy.'=1)
order by s.mi,s.kivel,s.miota');
?>,"ajanlatok":<?
echo mysql2jsonmatrix('
select a.id as ajanlat_id,a.kinek,if(a.kinek>0,szo.id,u.id),if(a.kinek>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,1,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.kinek=-u.id
left join szovetsegek szo on a.kinek=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.ki='.$szovetseg['id'].' and a.nyilvanos=1
union all
select a.id as ajanlat_id,a.ki,if(a.ki>0,szo.id,u.id),if(a.ki>0,szo.nev,u.nev),a.mit,a.mikor as ajanlat_mikor,a.szoveg_id,a.szoveg_reszlet,0,a.felbontasi_ido
,u_kezd.id,u_kezd.nev,a.nyilvanos
from diplomacia_ajanlatok a
left join userek u on a.ki=-u.id
left join szovetsegek szo on a.ki=szo.id
left join userek u_kezd on a.diplo=u_kezd.id
where a.kinek='.$szovetseg['id'].' and (a.nyilvanos=1 or '.$vendeg_vagy.'=1)
order by ajanlat_mikor desc, ajanlat_id desc
');

?>,"tagok":<?
echo mysql2jsonmatrix('select u.id,u.nev,timestampdiff(day,u.szov_belepes,now()),u.tisztseg,szt.nev
,if('.$vendeg_vagy.'=1,if(u.id=1,1440,timestampdiff(minute,u.uccso_akt,now())),"?")
,if('.$vendeg_vagy.'=1,u.pontszam_exp_atlag,"?")
,count(b.id),if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),""),u.nyelv
,coalesce(szt.jog_7,0)
,u.karrier,u.speci
from userek u
left join szovetseg_tisztsegek szt on u.tisztseg=szt.id and szt.szov_id='.$szovetseg['id'].'
left join bolygok b on b.tulaj=u.id
where u.szovetseg='.$szovetseg['id'].'
group by u.id
order by if(u.tisztseg=0,100,0), szt.nev, u.nev');

/*****************************************************************************************/
}
?>,"nincs_bolygod":0<?
?>,"szovetsegek":<?
echo mysql2jsonmatrix('select sz.id,sz.nev,u.id,u.nev,timestampdiff(day,sz.alapitas,now()),motto,tagletszam,alapito_elnevezese,if(length(sz.minicimer_ext)>0,concat("p",sz.id,sz.minicimer_crc,".",sz.minicimer_ext),"") as minicimer_fajlnev
from szovetsegek sz, userek u
where sz.alapito=u.id
order by sz.nev');
?>,"vendegsegek":<?
echo mysql2jsonmatrix('select sz.id,sz.nev,szv.mikortol,um.id,um.nev
from szovetseg_vendegek szv
inner join szovetsegek sz on sz.id=szv.szov_id
left join userek um on um.id=szv.ki_hivta
where szv.user_id='.$uid.'
order by sz.nev,sz.id');

?>}*/
<?

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>