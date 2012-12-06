<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;

function create_user_without_planet($nev,$szov=0,$email='') {
	global $zanda_test_user_email,$database_mmog_nemlog;
	if ($email=='') $email=$zanda_test_user_email;
	$lang_lang='hu';
	$zanda_id=0;$zanda_session_id=0;$zanda_ref='';
	//
	$jelszo_so=randomgen(32);
	$jelszo_hash='';$kozos_jelszo_hash='';//no login
	$datum=date('Y-m-d H:i:s');
	$premium_szint=2;$premium_a_vegeig=2;$premium_alap=$datum;$premium_emelt=$datum;
	$aktivalo_kulcs='';
	$penzlimit=mysql2num('select penz_kaphato_max from userek where id=1');
	//
	mysql_query('insert into userek (nev,email,mikortol,uccso_akt,regip,jelszo_so,jelszo_hash,kozos_jelszo_hash
,kitiltva,avatar_crc,premium,premium_szint,premium_alap,premium_emelt,aktivalo_kulcs,pontszam
,elso_belepes_betoltes,nyelv,zanda_ref,zanda_session_id,zanda_id,penz_adhato_max,penz_kaphato_max)
values("'.sanitstr($nev).'","'.sanitstr($email).'","'.$datum.'","'.$datum.'","'.gethostbyaddr($_SERVER['REMOTE_ADDR']).' ('.$_SERVER['REMOTE_ADDR'].')","'.$jelszo_so.'","'.$jelszo_hash.'","'.$kozos_jelszo_hash.'"
,0,"'.randomgen(32).'",'.$premium_a_vegeig.','.$premium_szint.',"'.$premium_alap.'","'.$premium_emelt.'","'.$aktivalo_kulcs.'",0
,1,"'.$lang_lang.'","'.sanitstr($zanda_ref).'",'.$zanda_session_id.','.$zanda_id.',0,'.sanitint($penzlimit).')');
	$r=mysql_query('select last_insert_id() from userek');
	$aux=mysql_fetch_array($r);$user_id=$aux[0];
	mysql_query('update userek set tulaj_szov=-'.$user_id.',vedelem=2 where id='.$user_id);
	//
	mysql_query('insert into user_beallitasok (user_id,chat_hu,chat_en) values('.$user_id.','.($lang_lang=='hu'?1:0).','.($lang_lang=='en'?1:0).')');
	mysql_query('insert into user_kutatasi_szint (user_id,kf_id) select '.$user_id.',id from kutatasi_temak');
	mysql_query('insert into user_veteli_limit (user_id,termek_id) select '.$user_id.',id from eroforrasok where tozsdezheto=1');
	frissit_user_vedelmi_szintek($user_id,1);
	//mysql_query('insert into '.$database_mmog_nemlog.'.userek_ossz (nev,email,mikortol,uccso_akt,regip,jelszo_so,jelszo_hash,kitiltva,vagyon,avatar_crc,premium_alap,nyelv,eredeti_id,zanda_ref,zanda_session_id,zanda_id) select nev,email,mikortol,uccso_akt,regip,jelszo_so,jelszo_hash,kitiltva,vagyon,avatar_crc,premium_alap,nyelv,id,zanda_ref,zanda_session_id,zanda_id from userek where id='.$user_id);//innen nem fog torlodni
	if ($szov>0) {
		mysql_query('update userek set szovetseg='.$szov.',tulaj_szov='.$szov.',tisztseg=0,szov_belepes="'.date('Y-m-d H:i:s').'" where id='.$user_id);
		mysql_query('update szovetsegek set tagletszam=tagletszam+1 where id='.$szov);
	}
	$user=mysql2row('select * from userek where id='.$user_id);
	return $user;
}

create_user_without_planet('Központi Szolgáltatóház');
create_user_without_planet('Central Services');
create_user_without_planet('Zharg\'al Tanítványai');

echo 'kesz';
mysql_close($mysql_csatlakozas);
?>