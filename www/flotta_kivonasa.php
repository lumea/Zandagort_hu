<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['forras_id']=(int)$_REQUEST['flotta_id'];//!!!


$er=mysql_query('select * from flottak where id='.$_REQUEST['forras_id']) or hiba(__FILE__,__LINE__,mysql_error());
$forras_flotta=mysql_fetch_array($er);$aux=$forras_flotta;
if (!$forras_flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);

flotta_reszflotta_frissites($forras_flotta['id']);

$ossz_mennyiseg=sanitint(mysql2num('select sum(hp) from resz_flotta_hajo where flotta_id='.$forras_flotta['id'].' and user_id='.$uid));
if ($ossz_mennyiseg==0) kilep();//van-e egyaltalan reszem ebben a flottaban


$flottak_szama=(int)mysql2num('select count(1) from flottak where tulaj='.$uid);
if ($vegjatek==0) $flottalimit=max($adataim['min_flotta_limit'],3*$adataim['bolygo_limit']);else $flottalimit=max($adataim['min_flotta_limit'],10*$adataim['bolygo_limit']);
if ($flottalimit<=$flottak_szama) kilep($lang[$lang_lang]['kisphpk']['A flottalimited '].$flottalimit.$lang[$lang_lang]['kisphpk'][', miközben '].$flottak_szama.$lang[$lang_lang]['kisphpk'][' flottád van, ezért nem hozhatsz létre még egyet.']);


if ($forras_flotta['bolygo']) {
	$bolygo=$forras_flotta['bolygo'];
	$statusz=STATUSZ_ALLOMAS;
} else {
	$bolygo=0;
	$statusz=STATUSZ_ALL;
}
mysql_query('insert into flottak (nev,tulaj,tulaj_szov,kezelo,kozos,bolygo,bazis_bolygo,statusz,sebesseg,x,y) values("",'.$uid.','.$adataim['tulaj_szov'].',0,'.$forras_flotta['kozos'].','.$bolygo.','.$forras_flotta['bazis_bolygo'].','.$statusz.',0,'.$forras_flotta['x'].','.$forras_flotta['y'].')') or hiba(__FILE__,__LINE__,mysql_error());
$er=mysql_query('select last_insert_id() from flottak') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$aux[0].',0,100)') or hiba(__FILE__,__LINE__,mysql_error());
$_REQUEST['cel_id']=$aux[0];
mysql_query('update flottak set nev="F'.$_REQUEST['cel_id'].'" where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
$er=mysql_query('select id from eroforrasok where tipus='.EROFORRAS_TIPUS_URHAJO) or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp,moral) values('.$_REQUEST['cel_id'].','.$aux[0].',0,0)');
}


$ossz_mennyiseg_hp=0;$eredeti_flotta_meret_hp=0;

//LOCK ELEJE
mysql_query('lock tables flotta_hajo write, flotta_hajo fh read, hajok h read, resz_flotta_hajo write, resz_flotta_hajo rfh read');
$er_rfh=mysql_query('select rfh.hajo_id,rfh.hp from resz_flotta_hajo rfh where rfh.flotta_id='.$forras_flotta['id'].' and rfh.user_id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
while($aux_rfh=mysql_fetch_array($er_rfh)) {
	$tipus=$aux_rfh[0];
	$mennyiseg_hp=$aux_rfh[1];
	$er=mysql_query('select fh.ossz_hp,ceil(fh.ossz_hp/100),fh.tapasztalat,fh.moral,h.ar from flotta_hajo fh, hajok h where fh.flotta_id='.$_REQUEST['forras_id'].' and fh.hajo_id='.$tipus.' and h.id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$eredeti_flotta_meret_hp+=$aux[0];
	if ($aux[0]<$mennyiseg_hp) $mennyiseg_hp=$aux[0];
	$ossz_mennyiseg_hp+=$mennyiseg_hp;
	if ($mennyiseg_hp>0) {
		mysql_query('update flotta_hajo set ossz_hp=if(ossz_hp-'.$mennyiseg_hp.'<0,0,ossz_hp-'.$mennyiseg_hp.') where flotta_id='.$_REQUEST['forras_id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update flotta_hajo
set
tapasztalat=if(ossz_hp>0,round((ossz_hp*tapasztalat+'.($mennyiseg_hp*$aux[2]).')/(ossz_hp+'.$mennyiseg_hp.')),'.$aux[2].'),
moral=greatest(least(if(ossz_hp>0,round((ossz_hp*moral+'.($mennyiseg_hp*$aux[3]).')/(ossz_hp+'.$mennyiseg_hp.')),'.$aux[3].'),10000),0),
ossz_hp=ossz_hp+'.$mennyiseg_hp.'
where flotta_id='.$_REQUEST['cel_id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
	}
}
mysql_query('delete from resz_flotta_hajo where flotta_id='.$forras_flotta['id'].' and user_id='.$uid);
mysql_query('unlock tables');
//LOCK VEGE


//a forras flotta elleni tamadasokat neha a cel flotta ellen iranyitani
if (2*$ossz_mennyiseg_hp>$eredeti_flotta_meret_hp) {
	mysql_query('update flottak set cel_flotta='.$_REQUEST['cel_id'].' where cel_flotta='.$_REQUEST['forras_id']);
}

flotta_minden_frissites($forras_flotta['id']);
flotta_reszflotta_frissites($forras_flotta['id']);
$er=mysql_query('select sum(ossz_hp) from flotta_hajo where flotta_id='.$forras_flotta['id'].' and hajo_id>0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux[0]==0) flotta_torles($forras_flotta['id']);


$hany_reszbol_all=mysql2num('select count(distinct user_id) from resz_flotta_hajo where flotta_id='.$forras_flotta['id']);
if ($hany_reszbol_all<2) mysql_query('delete from resz_flotta_hajo where flotta_id='.$forras_flotta['id']);



mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['cel_id']);
flotta_minden_frissites($_REQUEST['cel_id']);
if ($ossz_mennyiseg_hp<=0) {
	flotta_torles($_REQUEST['cel_id']);
	kilep();
}



//fog of war-t generalni (ehhez kell az uj flotta rejtozese (ld flotta_minden_frissites fentebb) es hexa koordinatai (ld lentebb)
//hexa_x,hexa_y-t beallitani
mysql_query('update flottak f,(select id,x,y,
@hx:=floor(x/217),
@hy:=floor(y/125),
@hatarparitas:=(abs(@hx%2)+abs(@hy%2))%2,
@xmar:=x-217*@hx,
@ymar:=y-125*@hy,
@m:=if(@hatarparitas=0,-1,1)*217/125,
@N:=if(@ymar-125/2>@m*(@xmar-217/2),1,0),
@hx+if(@hatarparitas=1,1-@N,@N) as hexa_x,
if(@hy%2=0,round(@hy/2)+@N,round((@hy+1)/2)) as hexa_y
from flottak where id='.$_REQUEST['cel_id'].') t
set f.hexa_x=t.hexa_x, f.hexa_y=t.hexa_y
where f.id='.$_REQUEST['cel_id']);
//
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select bu.tulaj,f.id,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1)
from flottak f, bolygok bu, hexa_bolygo hb
where bu.id=hb.id and bu.tulaj!=0 and f.id='.$_REQUEST['cel_id'].'
and f.hexa_x=hb.x and f.hexa_y=hb.y
and sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))/2<=bu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1))');
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select fu.tulaj,f.id,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1)
from flottak f, flottak fu, hexa_flotta hf
where fu.id=hf.id and f.id='.$_REQUEST['cel_id'].'
and f.hexa_x=hf.x and f.hexa_y=hf.y
and sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))/2<=fu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1))');
//onmaga
mysql_query('insert into lat_user_flotta (uid,fid,lathatosag)
select f.tulaj,f.id,2 from flottak f where f.id='.$_REQUEST['cel_id'].'
on duplicate key update lathatosag=2');
//
mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select bu.tulaj_szov,f.id,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1)
from flottak f, bolygok bu, hexa_bolygo hb
where bu.id=hb.id and bu.tulaj_szov!=0 and f.id='.$_REQUEST['cel_id'].'
and f.hexa_x=hb.x and f.hexa_y=hb.y
and sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))/2<=bu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(bu.x-f.x,2)+pow(bu.y-f.y,2))<=bu.latotav-f.rejtozes,2,1))');
mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select fu.tulaj_szov,f.id,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1)
from flottak f, flottak fu, hexa_flotta hf
where fu.id=hf.id and fu.tulaj_szov!=0 and f.id='.$_REQUEST['cel_id'].'
and f.hexa_x=hf.x and f.hexa_y=hf.y
and sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))/2<=fu.latotav-f.rejtozes
on duplicate key update lathatosag=greatest(lathatosag,if(sqrt(pow(fu.x-f.x,2)+pow(fu.y-f.y,2))<=fu.latotav-f.rejtozes,2,1))');
//onmaga
if ($cel_flotta['tulaj_szov']>0) mysql_query('insert into lat_szov_flotta (szid,fid,lathatosag)
select f.tulaj_szov,f.id,2 from flottak f where f.id='.$_REQUEST['cel_id'].'
on duplicate key update lathatosag=2');


kilep('###'.$_REQUEST['cel_id']);
?>