<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']>0) kilep($lang[$lang_lang]['kisphpk']['Már tagja vagy egy szövetségnek.']);

$_REQUEST['nev']=sanitstr(strtr($_REQUEST['nev'],array(','=>'')));
$_REQUEST['rovid_nev']=sanitstr(strtr($_REQUEST['rovid_nev'],array(','=>'')));
$_REQUEST['alapnev']=sanitstr($_REQUEST['alapnev']);
$_REQUEST['motto']=sanitstr($_REQUEST['motto']);
$_REQUEST['udvozlet']=sanitstr($_REQUEST['udvozlet']);
$_REQUEST['szabalyzat']=sanitstr($_REQUEST['szabalyzat']);
$_REQUEST['zart']=(int)$_REQUEST['zart'];

if (strlen($_REQUEST['nev'])==0) kilep($lang[$lang_lang]['kisphpk']['Adj nevet a szövetségednek!']);
if (strlen($_REQUEST['rovid_nev'])==0) kilep($lang[$lang_lang]['kisphpk']['Adj nevet a szövetségednek!']);
if (strlen($_REQUEST['alapnev'])==0) $_REQUEST['alapnev']='Alapító';
$er=mysql_query('select * from szovetsegek where nev="'.$_REQUEST['nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van szövetség.']);
$er=mysql_query('select * from szovetsegek where rovid_nev="'.$_REQUEST['rovid_nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van szövetség.']);
$er=mysql_query('select * from userek where nev="'.$_REQUEST['nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van játékos, és szövetségnek nem lehet játékossal azonos neve.']);

$datum=date('Y-m-d H:i:s');

mysql_query('insert into szovetsegek (nev,rovid_nev,motto,alapitas,alapito,tagletszam,alapito_elnevezese,cimer_crc,minicimer_crc,zart) values("'.$_REQUEST['nev'].'","'.$_REQUEST['rovid_nev'].'","'.$_REQUEST['motto'].'","'.$datum.'",'.$uid.',1,"'.$_REQUEST['alapnev'].'","'.randomgen(32).'","'.randomgen(32).'",'.$_REQUEST['zart'].')') or hiba(__FILE__,__LINE__,mysql_error());
$er=mysql_query('select last_insert_id() from szovetsegek') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
mysql_query('insert into szovetseg_szabalyzatok (id,szabalyzat,udvozlet) values('.$aux[0].',"'.$_REQUEST['szabalyzat'].'","'.$_REQUEST['udvozlet'].'")') or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update userek set szovetseg='.$aux[0].',tulaj_szov='.$aux[0].',tisztseg=-1,szov_belepes="'.$datum.'" where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygok set tulaj_szov='.$aux[0].' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set tulaj_szov='.$aux[0].' where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());

//privat diplo orokitese a szovire:
mysql_query('delete from diplomacia_ajanlatok where ki=-'.$uid.' or kinek=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update diplomacia_statuszok set ki='.$aux[0].',kezdemenyezo=if(kezdemenyezo=-'.$uid.','.$aux[0].',kezdemenyezo) where ki=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update diplomacia_statuszok set kivel='.$aux[0].',kezdemenyezo=if(kezdemenyezo=-'.$uid.','.$aux[0].',kezdemenyezo) where kivel=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update diplomacia_leendo_statuszok set ki='.$aux[0].',kezdemenyezo=if(kezdemenyezo=-'.$uid.','.$aux[0].',kezdemenyezo) where ki=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update diplomacia_leendo_statuszok set kivel='.$aux[0].',kezdemenyezo=if(kezdemenyezo=-'.$uid.','.$aux[0].',kezdemenyezo) where kivel=-'.$uid) or hiba(__FILE__,__LINE__,mysql_error());

jatekos_szovivaltas($uid,0,$aux[0]);

kilep();
?>