<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from szovetsegek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($er);
if (!$szovetseg) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség.']);

$er=mysql_query('select * from szovetseg_meghivok where hova='.$szovetseg['id'].' and kit='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívód ebbe a szövetségbe.']);


//ha van diplo statusza, akkor nem lephet be, kiveve a 0 orasak, amik automatan bontodnak (utobbit nem kell kulon leprogramozni, mert minden bontodik, ha eljut odaig a kod)
//NEM UGYANAZ, MINT A meghivo_kerelem_elfogadasa.php-BAN, MERT $meghivott HELYETT $adataim VAN!!!
//mnt-k
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where felbontasi_ido>0 and ki='.$adataim['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_MNT);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_MNT.','.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat az MNT-ket és testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}
//testverszovik
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where felbontasi_ido>0 and ki='.$adataim['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_TESTVER);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsa fel azokat a testvérszövetségeket, amikkel a szövetséged nem rendelkezik. Csak utána tud csatlakozni.']);
}

/*
//ha van diplo statusza, akkor nem lephet be (uj verzio)
//mnt-k
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where ki='.$adataim['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_MNT);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_MNT.','.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsd fel azokat az MNT-ket és testvérszövetségeket, amikkel a célszövetség nem rendelkezik. Csak utána tudsz csatlakozni.']);
}
//testverszovik
$er=mysql_query('select kivel,felbontasi_ido from diplomacia_statuszok where ki='.$adataim['tulaj_szov'].' and kivel!='.$szovetseg['id'].' and mi='.DIPLO_TESTVER);
while($aux=mysql_fetch_array($er)) {
	$x=mysql2num('select count(1) from diplomacia_statuszok where ki='.$szovetseg['id'].' and kivel='.$aux['kivel'].' and felbontasi_ido>='.$aux['felbontasi_ido'].' and mi in ('.DIPLO_TESTVER.')');
	if ($x==0) kilep($lang[$lang_lang]['kisphpk']['Előbb bontsd fel azokat a testvérszövetségeket, amikkel a célszövetség nem rendelkezik. Csak utána tudsz csatlakozni.']);
}
*/

if ($adataim['szovetseg']>0) {
	if ($adataim['tisztseg']==-1) kilep($lang[$lang_lang]['kisphpk']['Alapítóként előbb lépj ki a szövetségedből (és add át az alapítói címet), csak utána tudsz csatlakozni más szövetséghez.']);
	$regi_szovetsegem=$adataim['szovetseg'];
	//szov_uzenet a kilepesrol
	szovi_belepo_kilepo_uzenet($regi_szovetsegem
	,'Kilépett tag: '.$adataim['nev']
	,$adataim['nev'].' kilépett a szövetségből.'
	,'Member left the alliance: '.$adataim['nev']
	,$adataim['nev'].' has left the alliance.');
}


mysql_query('update userek set szovetseg='.$szovetseg['id'].',tulaj_szov='.$szovetseg['id'].',tisztseg=0,szov_belepes="'.date('Y-m-d H:i:s').'" where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygok set tulaj_szov='.$szovetseg['id'].' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set tulaj_szov='.$szovetseg['id'].' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
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
if ($regi_szovetsegem) {
	$er=mysql_query('select count(1) from userek where szovetseg='.$regi_szovetsegem) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);mysql_query('update szovetsegek set tagletszam='.$aux[0].' where id='.$regi_szovetsegem) or hiba(__FILE__,__LINE__,mysql_error());
}
mysql_query('delete from szovetseg_meghivas_kerelmek where ki='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from szovetseg_meghivok where kit='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_ajanlatok where ki=-'.$uid.' or kinek=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_statuszok where ki=-'.$uid.' or kivel=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_leendo_statuszok where ki=-'.$uid.' or kivel=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());


szovi_belepo_kilepo_uzenet($szovetseg['id']
,'Új tag: '.$adataim['nev']
,$adataim['nev'].' belépett a szövetségbe.'
,'New member: '.$adataim['nev']
,$adataim['nev'].' has become a member of the alliance.');


jatekos_szovivaltas($uid,$adataim['szovetseg'],$szovetseg['id']);

kilep();
?>