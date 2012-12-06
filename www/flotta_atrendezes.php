<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$megjelenitendo_flotta_id=0;

$_REQUEST['forras_id']=(int)$_REQUEST['forras_id'];
$_REQUEST['cel_id']=(int)$_REQUEST['cel_id'];
$_REQUEST['uj_flotta_nev']=sanitstr(megengedhetove_tesz($_REQUEST['uj_flotta_nev']));


if ($_REQUEST['cel_id']<0) kilep();

$ossz_mennyiseg=0;
$hajok=explode(',',$_REQUEST['mennyisegek']);
for($i=0;$i<count($hajok)-1;$i++) {
	$hajo=explode(':',$hajok[$i]);
	$ossz_mennyiseg+=sanitint($hajo[1]);
}
if ($ossz_mennyiseg<=0) kilep($lang[$lang_lang]['kisphpk']['Válassz ki legalább egy hajót.']);

$log_ertek=0;

if ($_REQUEST['honnan']=='flotta') {/*************************** FLOTTABOL ****************************/

$er=mysql_query('select * from flottak where id='.$_REQUEST['forras_id']) or hiba(__FILE__,__LINE__,mysql_error());
$forras_flotta=mysql_fetch_array($er);$aux=$forras_flotta;
if (!$forras_flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
//if ($forras_flotta['tulaj']!=$uid && $forras_flotta['kezelo']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);
//if ($forras_flotta['tulaj']!=$uid && $forras_flotta['kezelo']!=$uid && ($forras_flotta['kozos']!=1 || $jogaim[5]!=1)) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);
if ($forras_flotta['tulaj']!=$uid && $forras_flotta['kezelo']!=$uid && ($forras_flotta['kozos']!=1 || $jogaim[5]!=1 || $forras_flotta['tulaj_szov']!=$adataim['tulaj_szov'])) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

$forras_flotta_tulaj_adatai=mysql2row('select * from userek where id='.$forras_flotta['tulaj']);
//if ($forras_flotta['kezelo']==$uid) {$uid=$forras_flotta['kezelo'];$adataim=mysql2row('select * from userek where id='.$uid);}

$log_tulaj_1=$forras_flotta['tulaj'];
$log_tulaj_szov_1=$forras_flotta['tulaj_szov'];
$log_forras_id=$forras_flotta['id'];

if ($_REQUEST['cel_id']==0) {
	$flottak_szama=(int)mysql2num('select count(1) from flottak where tulaj='.$forras_flotta['tulaj']);
	if ($vegjatek==0) $flottalimit=max($forras_flotta_tulaj_adatai['min_flotta_limit'],3*$forras_flotta_tulaj_adatai['bolygo_limit']);else $flottalimit=max($forras_flotta_tulaj_adatai['min_flotta_limit'],10*$forras_flotta_tulaj_adatai['bolygo_limit']);
	if ($flottalimit<=$flottak_szama) kilep($lang[$lang_lang]['kisphpk']['A flottalimited '].$flottalimit.$lang[$lang_lang]['kisphpk'][', miközben '].$flottak_szama.$lang[$lang_lang]['kisphpk'][' flottád van, ezért nem hozhatsz létre még egyet.']);
	$uj_flotta=1;
	if ($aux['bolygo']) {
		$bolygo=$aux['bolygo'];
		$statusz=STATUSZ_ALLOMAS;
	} else {
		$bolygo=0;
		$statusz=STATUSZ_ALL;
	}
	mysql_query('insert into flottak (nev,tulaj,tulaj_szov,kezelo,kozos,bolygo,bazis_bolygo,statusz,sebesseg,x,y) values("'.$_REQUEST['uj_flotta_nev'].'",'.$aux['tulaj'].','.$aux['tulaj_szov'].','.$aux['kezelo'].','.$aux['kozos'].','.$bolygo.','.$aux['bazis_bolygo'].','.$statusz.',0,'.$aux['x'].','.$aux['y'].')') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from flottak') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$aux[0].',0,100)') or hiba(__FILE__,__LINE__,mysql_error());
	$_REQUEST['cel_id']=$aux[0];
	if (strlen($_REQUEST['uj_flotta_nev'])==0) mysql_query('update flottak set nev="F'.$_REQUEST['cel_id'].'" where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select id from eroforrasok where tipus='.EROFORRAS_TIPUS_URHAJO) or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp,moral) values('.$_REQUEST['cel_id'].','.$aux[0].',0,0)') or hiba(__FILE__,__LINE__,mysql_error());
} else $uj_flotta=0;

$er=mysql_query('select * from flottak where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
$cel_flotta=mysql_fetch_array($er);
if (!$cel_flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($cel_flotta['tulaj_szov']!=$forras_flotta['tulaj_szov']) kilep($lang[$lang_lang]['kisphpk']['Csak saját vagy szövi flottába pakolhatsz át.']);
if (($cel_flotta['x']!=$forras_flotta['x']) || ($cel_flotta['y']!=$forras_flotta['y'])) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta a közeledben.']);

$forras_flotta_tulaj_techszint=mysql2num('select techszint from userek where id='.$forras_flotta['tulaj']);
$cel_flotta_tulaj_techszint=mysql2num('select techszint from userek where id='.$cel_flotta['tulaj']);
if ($cel_flotta['tulaj']!=$forras_flotta['tulaj']) if ($forras_flotta_tulaj_techszint<5 or $cel_flotta_tulaj_techszint<5) kilep($lang[$lang_lang]['kisphpk']['Tech 5 alatt nem lehet hajókat átadni egymásnak.']);

$log_tulaj_2=$cel_flotta['tulaj'];
$log_tulaj_szov_2=$cel_flotta['tulaj_szov'];
$log_cel_id=$cel_flotta['id'];



flotta_reszflotta_frissites($forras_flotta['id']);
flotta_reszflotta_frissites($cel_flotta['id']);

//ha a celflottanak csak egy tulaja van, akkor is legeneralni a reszeket, max utana kiveszi a frissites
$vannak_e_reszflottai_a_celnak=sanitint(mysql2num('select coalesce(sum(rfh.hp*h.ar),0) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$cel_flotta['id']));
if ($vannak_e_reszflottai_a_celnak==0) {
	mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) select flotta_id,hajo_id,'.$cel_flotta['tulaj'].',ossz_hp,ossz_hp from flotta_hajo where flotta_id='.$cel_flotta['id'].' and hajo_id>0');
}

$ossz_mennyiseg=0;$eredeti_flotta_meret=0;

$vannak_e_reszflottai_a_forrasnak=sanitint(mysql2num('select coalesce(sum(rfh.hp*h.ar),0) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$forras_flotta['id']));
if ($vannak_e_reszflottai_a_forrasnak>0) {
//LOCK ELEJE
mysql_query('lock tables flotta_hajo write, flotta_hajo fh read, hajok h read, resz_flotta_hajo write, resz_flotta_hajo rfh read');
for($i=0;$i<count($hajok)-1;$i++) {
	$hajo=explode(':',$hajok[$i]);
	$tipus=(int)$hajo[0];
	$mennyiseg=sanitint($hajo[1]);
	$er=mysql_query('select fh.ossz_hp,ceil(fh.ossz_hp/100),fh.tapasztalat,fh.moral,h.ar from flotta_hajo fh, hajok h where fh.flotta_id='.$forras_flotta['id'].' and fh.hajo_id='.$tipus.' and h.id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$eredeti_flotta_meret+=$aux[1];
	if ($aux[1]<$mennyiseg) $mennyiseg=$aux[1];
	$ossz_mennyiseg+=$mennyiseg;
	if ($mennyiseg>0) {
		$arany=$aux[0]/$aux[1];
		$log_ertek+=round($mennyiseg/100*$arany*$aux[4]);
		mysql_query('update flotta_hajo set ossz_hp=if(ossz_hp-'.round($mennyiseg*$arany).'<0,0,ossz_hp-'.round($mennyiseg*$arany).') where flotta_id='.$forras_flotta['id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update flotta_hajo
set
tapasztalat=if(ossz_hp>0,round((ossz_hp*tapasztalat+'.($mennyiseg*$arany*$aux[2]).')/(ossz_hp+'.($mennyiseg*$arany).')),'.$aux[2].'),
moral=greatest(least(if(ossz_hp>0,round((ossz_hp*moral+'.($mennyiseg*$arany*$aux[3]).')/(ossz_hp+'.($mennyiseg*$arany).')),'.$aux[3].'),10000),0),
ossz_hp=ossz_hp+'.round($mennyiseg*$arany).'
where flotta_id='.$cel_flotta['id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		//reszenkenti atpakolas
		$er=mysql_query('select * from resz_flotta_hajo rfh where rfh.flotta_id='.$forras_flotta['id'].' and rfh.hajo_id='.$tipus);
		while($aux=mysql_fetch_array($er)) if ($aux['ossz_hp']>0) {
			mysql_query('update resz_flotta_hajo set hp=hp-'.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']).', ossz_hp=ossz_hp-'.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']).' where flotta_id='.$aux['flotta_id'].' and hajo_id='.$aux['hajo_id'].' and user_id='.$aux['user_id']);
			mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) values('.$cel_flotta['id'].','.$tipus.','.$aux['user_id'].','.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']).','.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']).') on duplicate key update hp=hp+'.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']).', ossz_hp=ossz_hp+'.round($mennyiseg*$arany*$aux['hp']/$aux['ossz_hp']));
		}
	}
}
mysql_query('update resz_flotta_hajo set hp=greatest(hp,0),ossz_hp=greatest(ossz_hp,0) where flotta_id='.$forras_flotta['id']);
mysql_query('unlock tables');
//LOCK VEGE
} else {
//LOCK ELEJE
mysql_query('lock tables flotta_hajo write, flotta_hajo fh read, hajok h read, resz_flotta_hajo write');
for($i=0;$i<count($hajok)-1;$i++) {
	$hajo=explode(':',$hajok[$i]);
	$tipus=(int)$hajo[0];
	$mennyiseg=sanitint($hajo[1]);
	$er=mysql_query('select fh.ossz_hp,ceil(fh.ossz_hp/100),fh.tapasztalat,fh.moral,h.ar from flotta_hajo fh, hajok h where fh.flotta_id='.$forras_flotta['id'].' and fh.hajo_id='.$tipus.' and h.id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$eredeti_flotta_meret+=$aux[1];
	if ($aux[1]<$mennyiseg) $mennyiseg=$aux[1];
	$ossz_mennyiseg+=$mennyiseg;
	if ($mennyiseg>0) {
		$arany=$aux[0]/$aux[1];
		$log_ertek+=round($mennyiseg/100*$arany*$aux[4]);
		mysql_query('update flotta_hajo set ossz_hp=if(ossz_hp-'.round($mennyiseg*$arany).'<0,0,ossz_hp-'.round($mennyiseg*$arany).') where flotta_id='.$forras_flotta['id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update flotta_hajo
set
tapasztalat=if(ossz_hp>0,round((ossz_hp*tapasztalat+'.($mennyiseg*$arany*$aux[2]).')/(ossz_hp+'.($mennyiseg*$arany).')),'.$aux[2].'),
moral=greatest(least(if(ossz_hp>0,round((ossz_hp*moral+'.($mennyiseg*$arany*$aux[3]).')/(ossz_hp+'.($mennyiseg*$arany).')),'.$aux[3].'),10000),0),
ossz_hp=ossz_hp+'.round($mennyiseg*$arany).'
where flotta_id='.$cel_flotta['id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) values('.$cel_flotta['id'].','.$tipus.','.$forras_flotta['tulaj'].','.round($mennyiseg*$arany).','.round($mennyiseg*$arany).') on duplicate key update hp=hp+'.round($mennyiseg*$arany).', ossz_hp=ossz_hp+'.round($mennyiseg*$arany));
	}
}
mysql_query('unlock tables');
//LOCK VEGE
}

flotta_reszflotta_frissites($forras_flotta['id']);
flotta_reszflotta_frissites($cel_flotta['id']);
$hany_reszbol_all=mysql2num('select count(distinct user_id) from resz_flotta_hajo where flotta_id='.$forras_flotta['id']);
if ($hany_reszbol_all<2) mysql_query('delete from resz_flotta_hajo where flotta_id='.$forras_flotta['id']);


//a forras flotta elleni tamadasokat neha a cel flotta ellen iranyitani
if (2*$ossz_mennyiseg>$eredeti_flotta_meret) {
	mysql_query('update flottak set cel_flotta='.$_REQUEST['cel_id'].' where cel_flotta='.$_REQUEST['forras_id']);
}

mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['cel_id']);
mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['forras_id']);
flotta_minden_frissites($_REQUEST['cel_id']);
flotta_minden_frissites($_REQUEST['forras_id']);
$er=mysql_query('select sum(ossz_hp) from flotta_hajo where flotta_id='.$_REQUEST['forras_id'].' and hajo_id>0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux[0]==0) {
	flotta_torles($_REQUEST['forras_id']);
	$megjelenitendo_flotta_id=$_REQUEST['cel_id'];
}


} else {/*************************** BOLYGOROL ****************************/

$er=mysql_query('select * from bolygok where id='.$_REQUEST['forras_id']) or hiba(__FILE__,__LINE__,mysql_error());
$forras_bolygo=mysql_fetch_array($er);$aux=$forras_bolygo;
if (!$forras_bolygo) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($forras_bolygo['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);//tutor ne tudjon flottat felloni

$log_tulaj_1=$forras_bolygo['tulaj'];
$log_tulaj_szov_1=$forras_bolygo['tulaj_szov'];
$log_forras_id=-$forras_bolygo['id'];//a bolygo az negativnak szamit

if ($_REQUEST['cel_id']==0) {
	$flottak_szama=(int)mysql2num('select count(1) from flottak where tulaj='.$forras_bolygo['tulaj']);
	if ($vegjatek==0) $flottalimit=max($adataim['min_flotta_limit'],3*$adataim['bolygo_limit']);else $flottalimit=max($adataim['min_flotta_limit'],10*$adataim['bolygo_limit']);
	if ($flottalimit<=$flottak_szama) kilep($lang[$lang_lang]['kisphpk']['A flottalimited '].$flottalimit.$lang[$lang_lang]['kisphpk'][', miközben '].$flottak_szama.$lang[$lang_lang]['kisphpk'][' flottád van, ezért nem hozhatsz létre még egyet.']);
	$uj_flotta=1;
	//a tutor ne oroklodjon, vagyis a flotta kezelo nelkuli
	mysql_query('insert into flottak (nev,tulaj,tulaj_szov,kezelo,bolygo,bazis_bolygo,statusz,sebesseg,x,y) values("'.$_REQUEST['uj_flotta_nev'].'",'.$aux['tulaj'].','.$aux['tulaj_szov'].',0,'.$aux['id'].','.$aux['id'].','.STATUSZ_ALLOMAS.',0,'.$aux['x'].','.$aux['y'].')') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from flottak') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$aux[0].',0,100)') or hiba(__FILE__,__LINE__,mysql_error());
	$_REQUEST['cel_id']=$aux[0];
	if (strlen($_REQUEST['uj_flotta_nev'])==0) mysql_query('update flottak set nev="F'.$_REQUEST['cel_id'].'" where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select id from eroforrasok where tipus='.EROFORRAS_TIPUS_URHAJO) or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp,moral) values('.$_REQUEST['cel_id'].','.$aux[0].',0,0)') or hiba(__FILE__,__LINE__,mysql_error());
} else $uj_flotta=0;

$er=mysql_query('select * from flottak where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
$cel_flotta=mysql_fetch_array($er);
if (!$cel_flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($cel_flotta['tulaj_szov']!=$forras_bolygo['tulaj_szov']) kilep($lang[$lang_lang]['kisphpk']['Csak saját vagy szövi flottába pakolhatsz át.']);
if ($cel_flotta['bolygo']!=$forras_bolygo['id'] || $cel_flotta['statusz']!=1) kilep($lang[$lang_lang]['kisphpk']['Nem állomásozik ilyen flotta a bolygó felett.']);

$forras_bolygo_tulaj_techszint=mysql2num('select techszint from userek where id='.$forras_bolygo['tulaj']);
$cel_flotta_tulaj_techszint=mysql2num('select techszint from userek where id='.$cel_flotta['tulaj']);
if ($cel_flotta['tulaj']!=$forras_bolygo['tulaj']) if ($forras_bolygo_tulaj_techszint<5 or $cel_flotta_tulaj_techszint<5) kilep($lang[$lang_lang]['kisphpk']['Tech 5 alatt nem lehet hajókat átadni egymásnak.']);

$log_tulaj_2=$cel_flotta['tulaj'];
$log_tulaj_szov_2=$cel_flotta['tulaj_szov'];
$log_cel_id=$cel_flotta['id'];

//ha a celflottanak csak egy tulaja van, akkor is legeneralni a reszeket, max utana kiveszi a frissites
$vannak_e_reszflottai_a_celnak=sanitint(mysql2num('select coalesce(sum(rfh.hp*h.ar),0) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$cel_flotta['id']));
if ($vannak_e_reszflottai_a_celnak==0) {
	mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) select flotta_id,hajo_id,'.$cel_flotta['tulaj'].',ossz_hp,ossz_hp from flotta_hajo where flotta_id='.$cel_flotta['id'].' and hajo_id>0');
}

$ossz_mennyiseg=0;
//LOCK ELEJE
mysql_query('lock tables bolygo_eroforras write, flotta_hajo write, bolygo_eroforras be read, hajok h read, resz_flotta_hajo write');
for($i=0;$i<count($hajok)-1;$i++) {
	$hajo=explode(':',$hajok[$i]);
	$tipus=(int)$hajo[0];
	$mennyiseg=sanitint($hajo[1]);
	$er=mysql_query('select floor(be.db/100),h.ar,be.db from bolygo_eroforras be, hajok h where be.bolygo_id='.$_REQUEST['forras_id'].' and be.eroforras_id='.$tipus.' and h.id='.$tipus);
	$aux=mysql_fetch_array($er);
	$hp=100*$mennyiseg;
	if ($aux[2]<$hp) $hp=$aux[2];
/*
	if ($tipus==HAJO_TIPUS_SZONDA) {//szondabol csak egeszet
		if ($aux[0]<$mennyiseg) $mennyiseg=$aux[0];
		$hp=100*$mennyiseg;
	} else {
		$hp=100*$mennyiseg;
		if ($aux[2]<$hp) $hp=$aux[2];
	}
*/
	if ($hp>0) {
		$ossz_mennyiseg+=$hp;
		$log_ertek+=round($hp/100*$aux[1]);
		mysql_query('update bolygo_eroforras set db=if(db-'.$hp.'<0,0,db-'.$hp.') where bolygo_id='.$_REQUEST['forras_id'].' and eroforras_id='.$tipus);
		mysql_query('update flotta_hajo
set
tapasztalat=if(ossz_hp>0,round((ossz_hp*tapasztalat)/(ossz_hp+'.$hp.')),0),
moral=greatest(least(if(ossz_hp>0,round((ossz_hp*moral+'.($hp*10000).')/(ossz_hp+'.$hp.')),10000),10000),0),
ossz_hp=ossz_hp+'.$hp.'
where flotta_id='.$cel_flotta['id'].' and hajo_id='.$tipus);
		mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) values('.$cel_flotta['id'].','.$tipus.','.$uid.','.$hp.','.$hp.') on duplicate key update hp=hp+'.$hp.', ossz_hp=ossz_hp+'.$hp);
	}//indulo moral: 100% -> 10000
	/*
	//regi: csak egesz hajokat lehet
	$mennyiseg=sanitint($hajo[1]);
	$er=mysql_query('select floor(be.db/100),h.ar from bolygo_eroforras be, hajok h where be.bolygo_id='.$_REQUEST['forras_id'].' and be.eroforras_id='.$tipus.' and h.id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]<$mennyiseg) $mennyiseg=$aux[0];
	$ossz_mennyiseg+=$mennyiseg;
	if ($mennyiseg>0) {
		$log_ertek+=round($mennyiseg*$aux[1]);
		mysql_query('update bolygo_eroforras set db=if(db-'.(100*$mennyiseg).'<0,0,db-'.(100*$mennyiseg).') where bolygo_id='.$_REQUEST['forras_id'].' and eroforras_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('update flotta_hajo
set
tapasztalat=if(ossz_hp>0,round((ossz_hp*tapasztalat)/(ossz_hp+'.(100*$mennyiseg).')),0),
moral=greatest(least(if(ossz_hp>0,round((ossz_hp*moral+'.(100*$mennyiseg*10000).')/(ossz_hp+'.(100*$mennyiseg).')),10000),10000),0),
ossz_hp=ossz_hp+'.(100*$mennyiseg).'
where flotta_id='.$cel_flotta['id'].' and hajo_id='.$tipus) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('insert into resz_flotta_hajo (flotta_id,hajo_id,user_id,hp,ossz_hp) values('.$cel_flotta['id'].','.$tipus.','.$uid.','.(100*$mennyiseg).','.(100*$mennyiseg).') on duplicate key update hp=hp+'.(100*$mennyiseg).', ossz_hp=ossz_hp+'.(100*$mennyiseg));
	}//indulo moral: 100% -> 10000
	*/
}
mysql_query('unlock tables');
//LOCK VEGE

flotta_reszflotta_frissites($cel_flotta['id']);


}


insert_into_hajo_transzfer_log($log_tulaj_1,$log_tulaj_szov_1,$log_forras_id,$log_tulaj_2,$log_tulaj_szov_2,$log_cel_id,$log_ertek);


mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['cel_id']);
flotta_minden_frissites($_REQUEST['cel_id']);
if ($ossz_mennyiseg<=0) {
	if ($uj_flotta) {//letrejott egy uj flotta, de nem raktunk bele semmit
		mysql_query('delete from flotta_hajo where flotta_id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('delete from flottak where id='.$_REQUEST['cel_id']) or hiba(__FILE__,__LINE__,mysql_error());
	}
	kilep($lang[$lang_lang]['kisphpk']['Válassz ki legalább egy hajót.']);
}



if ($uj_flotta) {//fog of war-t generalni (ehhez kell az uj flotta rejtozese (ld flotta_minden_frissites fentebb) es hexa koordinatai (ld lentebb)
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
}

if ($megjelenitendo_flotta_id>0) kilep('###'.$megjelenitendo_flotta_id);

kilep();
?>