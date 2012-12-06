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

if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_2']) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívási jogod.']);

$_REQUEST['kit']=(int)$_REQUEST['kit'];
$er=mysql_query('select * from userek where id='.$_REQUEST['kit']) or hiba(__FILE__,__LINE__,mysql_error());
$meghivott=mysql_fetch_array($er);
if (!$meghivott) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);
if ($meghivott['szovetseg']==$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Ő már tagja a szövetségednek.']);


$er=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($er);
if (!$szovetseg) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség.']);


//ha van diplo statusza, akkor nem lephet be, kiveve a 0 orasak, amik automatan bontodnak (utobbit nem kell kulon leprogramozni, mert minden bontodik, ha eljut odaig a kod)
//NEM UGYANAZ, MINT A meghivo_elfogadasa.php-BAN, MERT $adataim HELYETT $meghivott VAN!!!
//mnt-k
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where felbontasi_ido>0 and ki='.$meghivott['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_MNT);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_MNT.','.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat az MNT-ket és testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}
//testverszovik
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where felbontasi_ido>0 and ki='.$meghivott['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_TESTVER);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat a testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}

/*
//ha van diplo statusza, akkor nem lephet be (uj verzio)
//mnt-k
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where ki='.$meghivott['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_MNT);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_MNT.','.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat az MNT-ket és testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}
//testverszovik
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where ki='.$meghivott['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_TESTVER);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat a testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}
*/

if ($meghivott['szovetseg']>0) {
	if ($meghivott['tisztseg']==-1) kilep($lang[$lang_lang]['kisphpk']['Más szövetség alapítója nem léphet át a szövetségedbe, amíg át nem adja az alapítói címet.']);
	$regi_szovetsege=$meghivott['szovetseg'];
	//szov_uzenet a kilepesrol
	szovi_belepo_kilepo_uzenet($regi_szovetsege
	,'Kilépett tag: '.$meghivott['nev']
	,$meghivott['nev'].' kilépett a szövetségből.'
	,'Member left the alliance: '.$meghivott['nev']
	,$meghivott['nev'].' has left the alliance.');
}


mysql_query('update userek set szovetseg='.$szovetseg['id'].',tulaj_szov='.$szovetseg['id'].',tisztseg=0,szov_belepes="'.date('Y-m-d H:i:s').'" where id='.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygok set tulaj_szov='.$szovetseg['id'].' where tulaj='.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set tulaj_szov='.$szovetseg['id'].' where tulaj='.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak f,(
select f.id,min(b2.id) as uj_bazis_id
from flottak f, bolygok b, bolygok b2
where f.bazis_bolygo=b.id and b.tulaj_szov<>f.tulaj_szov and b2.tulaj=f.tulaj
group by f.id
) t
set f.bazis_bolygo=t.uj_bazis_id
where f.id=t.id') or hiba(__FILE__,__LINE__,mysql_error());

$er=mysql_query('select count(1) from userek where szovetseg='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);mysql_query('update szovetsegek set tagletszam='.$aux[0].' where id='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());
if ($regi_szovetsege) {
	$er=mysql_query('select count(1) from userek where szovetseg='.$regi_szovetsege) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);mysql_query('update szovetsegek set tagletszam='.$aux[0].' where id='.$regi_szovetsege) or hiba(__FILE__,__LINE__,mysql_error());
}
mysql_query('delete from szovetseg_meghivas_kerelmek where ki='.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from szovetseg_meghivok where kit='.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_ajanlatok where ki=-'.$meghivott['id'].' or kinek=-'.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_statuszok where ki=-'.$meghivott['id'].' or kivel=-'.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_leendo_statuszok where ki=-'.$meghivott['id'].' or kivel=-'.$meghivott['id']) or hiba(__FILE__,__LINE__,mysql_error());


szovi_belepo_kilepo_uzenet($szovetseg['id']
,'Új tag: '.$meghivott['nev']
,$meghivott['nev'].' belépett a szövetségbe.'
,'New member: '.$meghivott['nev']
,$meghivott['nev'].' has become a member of the alliance.');
rendszeruzenet($meghivott['id']
,'Új szövetséged: '.$szovetseg['nev']
,'Beléptél a(z) '.$szovetseg['nev'].' szövetségbe.'
,'Your new alliance: '.$szovetseg['nev']
,'You are now a member of '.$szovetseg['nev'].'.');


jatekos_szovivaltas($meghivott['id'],$meghivott['szovetseg'],$szovetseg['id']);

kilep();
?>