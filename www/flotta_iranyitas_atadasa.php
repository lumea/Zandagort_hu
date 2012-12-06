<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

$_REQUEST['kinek']=(int)$_REQUEST['kinek'];
$er=mysql_query('select * from userek where id='.$_REQUEST['kinek']) or hiba(__FILE__,__LINE__,mysql_error());
$kinek=mysql_fetch_array($er);
if (!$kinek) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

$er=mysql_query('select * from resz_flotta_hajo where flotta_id='.$flotta['id'].' and user_id='.$kinek['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

mysql_query('update flottak set tulaj='.$kinek['id'].', tulaj_szov='.$kinek['tulaj_szov'].' where id='.$flotta['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$flotta['id']);
if (flotta_fejvadasz_frissites($flotta['id'])) flotta_minden_frissites($flotta['id']);

mysql_query('insert ignore into lat_user_flotta (uid,fid,lathatosag) values('.$kinek['id'].','.$flotta['id'].',2)');//az uj tulajnak ne kelljen a kovetkezo fow frissitesig varnia

kilep();
?>