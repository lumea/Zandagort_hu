<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);
if ($adataim['tisztseg']!=-1) kilep($lang[$lang_lang]['kisphpk']['Nem te vagy az alapító.']);

$er=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	$_REQUEST['nev_'.$aux['id']]=sanitstr($_REQUEST['nev_'.$aux['id']]);
	$jog_str='';
	for($jj=1;$jj<=$jogok_szama;$jj++) {
		if ($_REQUEST['jog'.$jj]!=='1') $_REQUEST['jog'.$jj]=0;
		if ($_REQUEST['jog_'.$aux['id'].'_'.$jj]!=='1') $_REQUEST['jog_'.$aux['id'].'_'.$jj]=0;
		$jog_str.=',jog_'.$jj.'='.$_REQUEST['jog_'.$aux['id'].'_'.$jj];
	}
	$nev_update='';if (strlen($_REQUEST['nev_'.$aux['id']])>0) $nev_update=', nev="'.$_REQUEST['nev_'.$aux['id']].'"';
	mysql_query('update szovetseg_tisztsegek set '.substr($jog_str,1).$nev_update.' where szov_id='.$adataim['szovetseg'].' and id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
}

kilep();
?>