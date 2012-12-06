<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$er=mysql_query('select count(1) from userek where szovetseg='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);

if ($adataim['tisztseg']==-1) if ($aux[0]>1) {
	$_REQUEST['helyettes']=sanitstr($_REQUEST['helyettes']);
	$er=mysql_query('select * from userek where szovetseg='.$adataim['szovetseg'].' and nev="'.$_REQUEST['helyettes'].'"') or hiba(__FILE__,__LINE__,mysql_error());
	$helyettes=mysql_fetch_array($er);
	if (!$helyettes) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tag (utódnak).']);
	if ($helyettes['tisztseg']<0) kilep($lang[$lang_lang]['kisphpk']['Önmagad helyére nem léphetsz.']);
	mysql_query('update userek set tisztseg=-1 where id='.$helyettes['id']) or hiba(__FILE__,__LINE__,mysql_error());
	mysql_query('update szovetsegek set alapito='.$helyettes['id'].' where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
}
$er=mysql_query('select alapito from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$alapito=$aux[0];

//szov_uzenet a kilepesrol
szovi_belepo_kilepo_uzenet($adataim['szovetseg']
,'Kilépett tag: '.$adataim['nev']
,$adataim['nev'].' kilépett a szövetségből.'
,'Member left the alliance: '.$adataim['nev']
,$adataim['nev'].' has left the alliance.');


mysql_query('update userek set tisztseg=0, szovetseg=0, tulaj_szov='.(-$uid).' where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygok set tulaj_szov='.(-$uid).' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set tulaj_szov='.(-$uid).' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());

mysql_query('update flottak f,(
select f.id,min(b2.id) as uj_bazis_id
from flottak f, bolygok b, bolygok b2
where f.bazis_bolygo=b.id and b.tulaj_szov<>f.tulaj_szov and b2.tulaj=f.tulaj
group by f.id
) t
set f.bazis_bolygo=t.uj_bazis_id
where f.id=t.id') or hiba(__FILE__,__LINE__,mysql_error());

$er=mysql_query('select count(1) from userek where szovetseg='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
mysql_query('update szovetsegek set tagletszam='.$aux[0].' where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());


//szovi diplo orokitese privatra:
$privat_id=$uid;
//
$er2=mysql_query('select ds.*,dsz.szoveg as teljes_szoveg from diplomacia_statuszok ds, diplomacia_szovegek dsz where ds.ki='.$adataim['szovetseg'].' and ds.szoveg_id=dsz.id and ds.felbontasi_ido>0');
while($aux2=mysql_fetch_array($er2)) {
	mysql_query('insert into diplomacia_szovegek (szoveg) values("'.sanitstr($aux2['teljes_szoveg']).'")');
	$er3=mysql_query('select last_insert_id() from diplomacia_szovegek');
	$aux3=mysql_fetch_array($er3);
	mysql_query('insert ignore into diplomacia_statuszok (ki,kivel,mi,miota,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos) values(-'.$privat_id.','.$aux2['kivel'].','.$aux2['mi'].',"'.$aux2['miota'].'",'.$aux3[0].','.($aux2['kezdemenyezo']==$adataim['szovetseg']?(-$privat_id):$aux2['kezdemenyezo']).',"'.sanitstr($aux2['szoveg_reszlet']).'",'.$aux2['felbontasi_ido'].','.$aux2['felbontas_alatt'].',"'.$aux2['felbontas_mikor'].'",'.$aux2['diplo_1'].','.$aux2['diplo_2'].','.$aux2['nyilvanos'].')');
}
$er2=mysql_query('select ds.*,dsz.szoveg as teljes_szoveg from diplomacia_statuszok ds, diplomacia_szovegek dsz where ds.kivel='.$adataim['szovetseg'].' and ds.szoveg_id=dsz.id and ds.felbontasi_ido>0');
while($aux2=mysql_fetch_array($er2)) {
	mysql_query('insert into diplomacia_szovegek (szoveg) values("'.sanitstr($aux2['teljes_szoveg']).'")');
	$er3=mysql_query('select last_insert_id() from diplomacia_szovegek');
	$aux3=mysql_fetch_array($er3);
	mysql_query('insert ignore into diplomacia_statuszok (ki,kivel,mi,miota,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos) values('.$aux2['ki'].',-'.$privat_id.','.$aux2['mi'].',"'.$aux2['miota'].'",'.$aux3[0].','.($aux2['kezdemenyezo']==$adataim['szovetseg']?(-$privat_id):$aux2['kezdemenyezo']).',"'.sanitstr($aux2['szoveg_reszlet']).'",'.$aux2['felbontasi_ido'].','.$aux2['felbontas_alatt'].',"'.$aux2['felbontas_mikor'].'",'.$aux2['diplo_1'].','.$aux2['diplo_2'].','.$aux2['nyilvanos'].')');
}
//
$er2=mysql_query('select ds.*,dsz.szoveg as teljes_szoveg from diplomacia_leendo_statuszok ds, diplomacia_szovegek dsz where ds.ki='.$adataim['szovetseg'].' and ds.szoveg_id=dsz.id and ds.felbontasi_ido>0');
while($aux2=mysql_fetch_array($er2)) {
	mysql_query('insert into diplomacia_szovegek (szoveg) values("'.sanitstr($aux2['teljes_szoveg']).'")');
	$er3=mysql_query('select last_insert_id() from diplomacia_szovegek');
	$aux3=mysql_fetch_array($er3);
	mysql_query('insert ignore into diplomacia_leendo_statuszok (ki,kivel,mi,miota,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos) values(-'.$privat_id.','.$aux2['kivel'].','.$aux2['mi'].',"'.$aux2['miota'].'",'.$aux3[0].','.($aux2['kezdemenyezo']==$adataim['szovetseg']?(-$privat_id):$aux2['kezdemenyezo']).',"'.sanitstr($aux2['szoveg_reszlet']).'",'.$aux2['felbontasi_ido'].','.$aux2['felbontas_alatt'].',"'.$aux2['felbontas_mikor'].'",'.$aux2['diplo_1'].','.$aux2['diplo_2'].','.$aux2['nyilvanos'].')');
}
$er2=mysql_query('select ds.*,dsz.szoveg as teljes_szoveg from diplomacia_leendo_statuszok ds, diplomacia_szovegek dsz where ds.kivel='.$adataim['szovetseg'].' and ds.szoveg_id=dsz.id and ds.felbontasi_ido>0');
while($aux2=mysql_fetch_array($er2)) {
	mysql_query('insert into diplomacia_szovegek (szoveg) values("'.sanitstr($aux2['teljes_szoveg']).'")');
	$er3=mysql_query('select last_insert_id() from diplomacia_szovegek');
	$aux3=mysql_fetch_array($er3);
	mysql_query('insert ignore into diplomacia_leendo_statuszok (ki,kivel,mi,miota,szoveg_id,kezdemenyezo,szoveg_reszlet,felbontasi_ido,felbontas_alatt,felbontas_mikor,diplo_1,diplo_2,nyilvanos) values('.$aux2['ki'].',-'.$privat_id.','.$aux2['mi'].',"'.$aux2['miota'].'",'.$aux3[0].','.($aux2['kezdemenyezo']==$adataim['szovetseg']?(-$privat_id):$aux2['kezdemenyezo']).',"'.sanitstr($aux2['szoveg_reszlet']).'",'.$aux2['felbontasi_ido'].','.$aux2['felbontas_alatt'].',"'.$aux2['felbontas_mikor'].'",'.$aux2['diplo_1'].','.$aux2['diplo_2'].','.$aux2['nyilvanos'].')');
}
//


if ($aux[0]==0) {//szovetseg feloszlatasa
	mysql_query('delete from szovetseg_meghivok where hova='.$adataim['szovetseg']);
	mysql_query('delete from szovetseg_szabalyzatok where id='.$adataim['szovetseg']);
	mysql_query('delete from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg']);
	mysql_query('delete from szovetsegek where id='.$adataim['szovetseg']);
	mysql_query('update bolygok set tulaj_szov='.(-$uid).' where tulaj='.$uid);
	mysql_query('update flottak set tulaj_szov='.(-$uid).' where tulaj='.$uid);
	mysql_query('delete from diplomacia_ajanlatok where ki='.$adataim['szovetseg'].' or kinek='.$adataim['szovetseg']);
	mysql_query('delete from diplomacia_statuszok where ki='.$adataim['szovetseg'].' or kivel='.$adataim['szovetseg']);
	mysql_query('delete from diplomacia_leendo_statuszok where ki='.$adataim['szovetseg'].' or kivel='.$adataim['szovetseg']);
}

jatekos_szovivaltas($uid,$adataim['szovetseg'],0);

kilep();
?>