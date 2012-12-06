<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['faj_id']=(int)$_REQUEST['faj_id'];
$_REQUEST['db']=(int)$_REQUEST['db'];

if ($_REQUEST['db']<=0) kilep($lang[$lang_lang]['kisphpk']['Legalább 1-et kell kérned.']);

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid)  kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$osztaly=1;for($i=1;$i<$aux2['osztaly'];$i++) $osztaly*=2;
$er=mysql_query('select * from eroforrasok where tipus='.EROFORRAS_TIPUS_FAJ.' and bolygo_osztaly&'.$osztaly.'>0 and id='.$_REQUEST['faj_id']) or hiba(__FILE__,__LINE__,mysql_error());
$faj=mysql_fetch_array($er);
if (!$faj) kilep($lang[$lang_lang]['kisphpk']['Ilyen fajt nem lehet betelepíteni erre a bolygóra.']);

$er=mysql_query('select db from bolygo_eroforras where bolygo_id='.$aux2['id'].' and eroforras_id='.$_REQUEST['faj_id']) or hiba(__FILE__,__LINE__,mysql_error());
$tenyleges_szam=mysql_fetch_array($er);
$er=mysql_query('select db from bolygo_faj_celszam where osztaly='.$aux2['osztaly'].' and terulet='.round($aux2['terulet']/100000).' and eroforras_id='.$_REQUEST['faj_id']) or hiba(__FILE__,__LINE__,mysql_error());
$celszam=mysql_fetch_array($er);
if ($tenyleges_szam[0]>$celszam[0]) kilep();//nem lehet a celszam fole vinni
if ($tenyleges_szam[0]+$_REQUEST['db']>$celszam[0]) $_REQUEST['db']=$celszam[0]-$tenyleges_szam[0];//max addig

$egysegar=100;
if ($faj['trofikus_szint']==2) $egysegar=1000;
if ($faj['trofikus_szint']==3) $egysegar=10000;

if ($adataim['vagyon']<$_REQUEST['db']*$egysegar) $_REQUEST['db']=floor($adataim['vagyon']/$egysegar);

mysql_query('update userek set vagyon=vagyon-'.($_REQUEST['db']*$egysegar).' where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update bolygo_eroforras set db=db+'.$_REQUEST['db'].' where bolygo_id='.$_REQUEST['bolygo_id'].' and eroforras_id='.$_REQUEST['faj_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>