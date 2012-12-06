<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;
if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_3']) kilep($lang[$lang_lang]['kisphpk']['Nincs kirúgási jogod.']);

$_REQUEST['kit']=sanitstr($_REQUEST['kit']);
$er=mysql_query('select * from userek where szovetseg='.$adataim['szovetseg'].' and nev="'.$_REQUEST['kit'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$kirugott=mysql_fetch_array($er);
if (!$kirugott) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tag.']);
if ($kirugott['id']==$uid) kilep($lang[$lang_lang]['kisphpk']['Magadat nem rúghatod ki. Lépj ki inkább, ha el akarod hagyni a szövetséget.']);
if ($kirugott['tisztseg']<0) kilep($lang[$lang_lang]['kisphpk']['Az alapítót nem lehet kirúgni.']);

$er=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$szovetseg_neve=$aux['nev'];

$er=mysql_query('select id from userek where szovetseg='.$adataim['szovetseg'].' and tisztseg=-1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$alapito=$aux[0];

mysql_query('update userek set tisztseg=0, szovetseg=0, tulaj_szov='.(-$kirugott['id']).' where id='.$kirugott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygok set tulaj_szov='.(-$kirugott['id']).' where tulaj='.$kirugott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set tulaj_szov='.(-$kirugott['id']).' where tulaj='.$kirugott['id']) or hiba(__FILE__,__LINE__,mysql_error());

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


szovi_belepo_kilepo_uzenet($adataim['szovetseg']
,'Kirúgott tag: '.$kirugott['nev']
,$kirugott['nev'].' ki lett rúgva a szövetségből.'
,'Dismissed member: '.$kirugott['nev']
,$kirugott['nev'].' has been dismissed from the alliance.');
rendszeruzenet($kirugott['id']
,'Kirúgtak innen: '.$szovetseg_neve
,'Kirúgtak a(z) '.$szovetseg_neve.' szövetségből.'
,'Dismissed from: '.$szovetseg_neve
,'You are dismissed from '.$szovetseg_neve.'.');




//szovi diplo orokitese privatra:
$privat_id=$kirugott['id'];
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


jatekos_szovivaltas($kirugott['id'],$adataim['szovetseg'],0);


kilep();
?>