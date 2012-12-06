<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

mysql_query('delete from cset_hozzaszolasok where szov_id>-1000 and mikor<"'.date('Y-m-d H:i:s',time()-3600).'"');
mysql_query('delete from cset_hozzaszolasok where szov_id<=-1000 and mikor<"'.date('Y-m-d H:i:s',time()-3600*24).'"');

$_REQUEST['szov']=(int)$_REQUEST['szov'];
if ($_REQUEST['szov']>0) {//vendeg szovi?
	$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$_REQUEST['szov'].' and user_id='.$uid);
	if ($aux) {
		$adataim['szovetseg']=$_REQUEST['szov'];
	}
}

$csat_mapping=array(0,-1,$adataim['szovetseg'],-500,-500,-500);
if ($adataim['szovetseg']<=0) $csat_mapping[2]=-500;//nemletezo csetszoba
$r=mysql_query('select cssz.id
from cset_szobak cssz
left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id
where cssz.tulaj='.$uid.' or csszu.user_id='.$uid.'
group by cssz.id limit 3');
$cs=0;while($aux=mysql_fetch_array($r)) {
	$cs++;
	$csat_mapping[2+$cs]=-$aux[0];
}

$cstab=(int)$_REQUEST['cstab'];

if ($cstab==3) if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);
if ($csat_mapping[$cstab-1]==-500) kilep();//nemletezo csatorna

switch($cstab) {
	case 1:
	case 2:
		mysql_query('update userek set kommakt_cset=kommakt_cset+1 where id='.$uid);
	break;
	case 3:
		mysql_query('update userek set kommakt_szovicset=kommakt_szovicset+1 where id='.$uid);
	break;
}

$_REQUEST['mit']=sanitstr($_REQUEST['mit']);
mysql_query('insert into cset_hozzaszolasok (szov_id,ki,mit) values('.$csat_mapping[$cstab-1].','.$uid.',"'.$_REQUEST['mit'].'")') or hiba(__FILE__,__LINE__,mysql_error());
mysql_select_db($database_mmog_nemlog);
mysql_query('insert into cset_hozzaszolasok_hist (szov_id,ki,mit) values('.$csat_mapping[$cstab-1].','.$uid.',"'.$_REQUEST['mit'].'")') or hiba(__FILE__,__LINE__,mysql_error());
mysql_select_db($database_mmog);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>