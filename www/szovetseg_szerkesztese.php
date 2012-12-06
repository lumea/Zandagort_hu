<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($res);

if ($szovetseg['alapito']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem te vagy az alapító.']);

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
$er=mysql_query('select * from szovetsegek where nev="'.$_REQUEST['nev'].'" and id!='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van szövetség.']);
$er=mysql_query('select * from szovetsegek where rovid_nev="'.$_REQUEST['rovid_nev'].'" and id!='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van szövetség.']);
$er=mysql_query('select * from userek where nev="'.$_REQUEST['nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep($lang[$lang_lang]['kisphpk']['Ilyen néven már van játékos, és szövetségnek nem lehet játékossal azonos neve.']);

mysql_query('update szovetsegek set nev="'.$_REQUEST['nev'].'", rovid_nev="'.$_REQUEST['rovid_nev'].'", motto="'.$_REQUEST['motto'].'", alapito_elnevezese="'.$_REQUEST['alapnev'].'", zart='.$_REQUEST['zart'].' where id='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update szovetseg_szabalyzatok set szabalyzat="'.$_REQUEST['szabalyzat'].'", udvozlet="'.$_REQUEST['udvozlet'].'" where id='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());


kilep();
?>