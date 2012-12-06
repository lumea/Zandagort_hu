<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);
if ($adataim['tisztseg']!=-1) kilep($lang[$lang_lang]['kisphpk']['Nem te vagy az alapító.']);

$_REQUEST['nev']=sanitstr($_REQUEST['nev']);
$jog_str='';$jog_val_str='';
for($jj=1;$jj<=$jogok_szama;$jj++) {
	if ($_REQUEST['jog'.$jj]!=='1') $_REQUEST['jog'.$jj]=0;
	$jog_str.=',jog_'.$jj;
	$jog_val_str.=','.$_REQUEST['jog'.$jj];
}

if (strlen($_REQUEST['nev'])==0) kilep($lang[$lang_lang]['kisphpk']['Adj nevet a tisztségnek!']);

$datum=date('Y-m-d H:i:s');

$er=mysql_query('select count(1),coalesce(max(id),0) from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux[0]>=100) kilep($lang[$lang_lang]['kisphpk']['Nem lehet 100-nál több tisztség egy szövetségben.']);

mysql_query('insert into szovetseg_tisztsegek (szov_id,id,nev'.$jog_str.') values('.$adataim['szovetseg'].','.($aux[1]+1).',"'.$_REQUEST['nev'].'"'.$jog_val_str.')') or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>