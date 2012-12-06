<?
include_once('torlist.php');

$suti_hossz=86400;$suti_hossz_percben=1440;
function tokencsekk() {
	global $ismert,$adataim;
	if ($ismert) if ($adataim['token']==$_REQUEST['token']) if (time()<strtotime($adataim['session_ervenyesseg'])) return true;
	return false;
}

//session ellenőrzése, megújítása:
$ismert=0;$szerk=0;$uid=false;$usernev='';$ertek='';
if (!empty($_COOKIE['uid'])) {
	$suti_uid=(int)(substr($_COOKIE['uid'],32));
	$suti_session_so=substr($_COOKIE['uid'],0,32);
	$most=date('Y-m-d H:i:s');
	$r=mysql_query('select * from userek where id='.$suti_uid) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($r);
	if ($aux['kitiltva']!=0) unset($aux);
	if ($aux['kitiltva_meddig']>=$most) unset($aux);
	if ($aux['inaktiv']!=0) unset($aux);
	if ($aux['session_so']==$suti_session_so) {
		if (time()<strtotime($aux['session_ervenyesseg'])) {
			//
			if ($aux['epp_most_helyettes_id']>0) {
				$helyettes_aux=mysql2row('select * from userek where id='.$aux['epp_most_helyettes_id']);
				$log_uid=$helyettes_aux['id'];
				$log_uccso_multicsekk=$helyettes_aux['uccso_multicsekk'];
			} else {
				$log_uid=$aux['id'];
				$log_uccso_multicsekk=$aux['uccso_multicsekk'];
			}
			//
			$kulon_ip=$_SERVER['REMOTE_ADDR'];$kulon_dn=gethostbyaddr($_SERVER['REMOTE_ADDR']);$ip_cim=$kulon_dn.' ('.$kulon_ip.')';
			//ideiglenes kitiltas egyik
			$er2=mysql_query('select * from ideiglenes_kitiltasok where uid='.$aux['id'].' and mettol<="'.$most.'" and meddig>="'.$most.'"') or hiba(__FILE__,__LINE__,mysql_error());
			$aux2=mysql_fetch_array($er2);
			if ($aux2) $kitiltva=1;
			if ($aux['epp_most_helyettes_id']>0) {
				//ideiglenes kitiltas masik
				$er2=mysql_query('select * from ideiglenes_kitiltasok where uid='.$helyettes_aux['id'].' and mettol<="'.$most.'" and meddig>="'.$most.'"') or hiba(__FILE__,__LINE__,mysql_error());
				$aux2=mysql_fetch_array($er2);
				if ($aux2) $kitiltva=1;
			}
			//aktivacios kenyszer
			if ($admin_nyaral==0) {
				if ($aux['aktivalo_kulcs']!='') if (time()-strtotime($aux['mikortol'])>3600*24*7) $kitiltva=1;
				if ($aux['epp_most_helyettes_id']>0) {
					if ($helyettes_aux['aktivalo_kulcs']!='') if (time()-strtotime($helyettes_aux['mikortol'])>3600*24*7) $kitiltva=1;
				}
			}
			//TOR-hasznalat
			if (in_array($_SERVER['REMOTE_ADDR'],$torlist)) $kitiltva=1;
			//helyettesites problemai
			if ($aux['epp_most_helyettes_id']>0) {
				if ($aux['helyettes_id']!=$aux['epp_most_helyettes_id']) $kitiltva=1;
				if ($aux['helyettesitett_ido']+sanitint_poz(min(time(),strtotime($aux['uccso_akt']))-strtotime($aux['uccso_login']))>3600*24) $kitiltva=1;
			}
			//fenti okokbol kitiltva
			if ($kitiltva) {
				//helyettesites?
				if ($aux['epp_most_helyettes_id']>0) {
					mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$aux['epp_most_helyettes_id']);
					mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($aux['uccso_akt']))-strtotime($aux['uccso_login'])).' where id='.$aux['id']);
				}
				//
				mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
				setcookie('uid','',time()-3600,'/');
			} else {
				$ttt=time()+$suti_hossz;
				$datum=date('Y-m-d H:i:s',$ttt);
				if ($nem_szamit_aktivitasnak) mysql_query('update userek set session_ervenyesseg="'.$datum.'" where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
				else mysql_query('update userek set session_ervenyesseg="'.$datum.'",uccso_akt="'.date('Y-m-d H:i:s').'" where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
				$adataim=$aux;
				$adataim['session_ervenyesseg']=$datum;
				$uid=$adataim['id'];
				$token=$adataim['token'];
				$szerk=$adataim['szerk'];
				$ismert=1;
				setcookie('uid',$adataim['session_so'].$uid,$ttt,'/');
				//jogok
				$jogok_szama_aux=12;
				for($i=1;$i<=$jogok_szama_aux;$i++) $jogaim[$i]=0;
				if ($adataim['szovetseg']>0) {
					if ($adataim['tisztseg']==-1) {
						for($i=1;$i<=$jogok_szama_aux;$i++) $jogaim[$i]=1;
					} else {
						$er_jog=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
						$aux_jog=mysql_fetch_array($er_jog);
						if ($aux_jog) {
							for($i=1;$i<=$jogok_szama_aux;$i++) if ($aux_jog['jog_'.$i]) $jogaim[$i]=1;
						}
					}
				}
				$eredeti_jogaim=$jogaim;
				//kozos flotta jog -> radar jog
				if ($jogaim[5]) $jogaim[10]=1;
				//bekebiro v tanacsnok -> diplomata jog
				if ($adataim['karrier']==4) if ($adataim['speci']>0) $jogaim[7]=1;
				//user_beallitasok
				$user_beallitasok=mysql2row('select * from user_beallitasok where user_id='.$uid);
				//valos aktivitas logolas
				if ($nem_szamit_aktivitasnak) {
				} else {
					mysql_select_db($database_mmog_nemlog);
					$akt_mikor=substr($most,0,15).'0:00';//10p-re kerekitve
					$akt_hetnapja=date('w');if ($akt_hetnapja==0) $akt_hetnapja=7;//h-v = 1-7
					$akt_tizperc=substr($akt_mikor,11);
					mysql_query('insert into aktivitasok (uid,mikor,db,hetnapja,tizperc) values('.$log_uid.',"'.$akt_mikor.'",1,'.$akt_hetnapja.',"'.$akt_tizperc.'") on duplicate key update db=db+1');
					mysql_select_db($database_mmog);
				}
				//multicsekk
				$datum_multi=date('Y-m-d H:i:s',time()-900);//15 perc, technikai login
				if ($log_uccso_multicsekk<$datum_multi) {
					mysql_select_db($database_mmog_nemlog);
					mysql_query('insert into loginek (uid,mikor,ip,technikai_login,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$log_uid.',"'.$most.'","'.$ip_cim.'",1,"'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
					mysql_query('insert into loginek_osszes (uid,mikor,ip,technikai_login,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$log_uid.',"'.$most.'","'.$ip_cim.'",1,"'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
					$er3=mysql_query('select uid,timestampdiff(hour,mikor,now()) as elteres from loginek where uid!='.$log_uid.' and timestampdiff(hour,mikor,now())<=24 and ip="'.$ip_cim.'" order by mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
					$aux3=mysql_fetch_array($er3);
					if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
						$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
						mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$log_uid.','.$aux3[0].','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
						mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$aux3[0].','.$log_uid.','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
					}
					//minusz pont
					$er3=mysql_query('select kivel,timestampdiff(hour,l.mikor,now()) from multi_matrix mm, loginek l
where mm.ki='.$log_uid.' and mm.kivel=l.uid and timestampdiff(hour,l.mikor,now())<=24 and l.kulon_ip!="'.$kulon_ip.'"
order by l.mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
					$aux3=mysql_fetch_array($er3);
					if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
						$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
						mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$log_uid.','.$aux3[0].','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
						mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$aux3[0].','.$log_uid.','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
					}
					//
					mysql_select_db($database_mmog);
					mysql_query('update userek set uccso_multicsekk="'.date('Y-m-d H:i:s').'" where id='.$log_uid) or hiba(__FILE__,__LINE__,mysql_error());
				}
			}
		} else {//lejart session
			//helyettesites?
			if ($aux['epp_most_helyettes_id']>0) {
				mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$aux['epp_most_helyettes_id']);
				mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($aux['uccso_akt']))-strtotime($aux['uccso_login'])).' where id='.$aux['id']);
			}
			//
			mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
			setcookie('uid','',time()-3600,'/');
		}
	} else {//hekkelesi kiserlet
		setcookie('uid','',time()-3600,'/');
	}
}


/************************************************ HELYETTESÍTÉS ELEJE **************************************************************************/
if ($_REQUEST['login_nev']!='') if (strpos($_REQUEST['login_nev'],'#')!==false) {
	$ki_nev=substr($_REQUEST['login_nev'],0,strpos($_REQUEST['login_nev'],'#'));
	$kit_nev=substr($_REQUEST['login_nev'],strpos($_REQUEST['login_nev'],'#')+1);
	$ki_aux=mysql2row('select * from userek where (nev="'.sanitstr($ki_nev).'" or email="'.sanitstr($ki_nev).'") and kitiltva=0 and inaktiv=0');
	$kit_aux=mysql2row('select * from userek where (nev="'.sanitstr($kit_nev).'" or email="'.sanitstr($kit_nev).'") and kitiltva=0 and inaktiv=0');
	$jelszo_hash=hash('whirlpool',$_REQUEST['login_jelszo'].$ki_aux['jelszo_so'].$rendszer_so);
	if ($jelszo_hash==$ki_aux['jelszo_hash']) {//aki helyettesit, annak kell a jelszava
		if (time()<strtotime($ki_aux['kitiltva_meddig'])) {//ideiglenes kitiltas egyik
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$ki_aux['nyelv']]['kisphpk']['Ideiglenesen ki vagy tiltva '].$ki_aux['kitiltva_meddig'].$lang[$ki_aux['nyelv']]['kisphpk']['-ig. Ha nem tudod, miért, írj az <a href="mailto:'.$zanda_admin_email['hu'].'">'.$zanda_admin_email['hu'].'</a> címre.']);
		}
		if (time()<strtotime($kit_aux['kitiltva_meddig'])) {//ideiglenes kitiltas masik
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$kit_aux['nyelv']]['kisphpk']['Ideiglenesen ki vagy tiltva '].$kit_aux['kitiltva_meddig'].$lang[$kit_aux['nyelv']]['kisphpk']['-ig. Ha nem tudod, miért, írj az <a href="mailto:'.$zanda_admin_email['hu'].'">'.$zanda_admin_email['hu'].'</a> címre.']);
		}
		//aktivacios kenyszer egyik
		if ($admin_nyaral==0) {
			if ($ki_aux['aktivalo_kulcs']!='') if (time()-strtotime($ki_aux['mikortol'])>3600*24*7) {//tobb mint 7 napja nem aktivalt
				header('Content-type: text/html;charset=utf-8');
				kilep($lang[$ki_aux['nyelv']]['kisphpk']['Aktiválnod kell a regisztrációdat. Ha nem kaptad meg a regisztrációs levelet, írj egy emailt az <a href="mailto:'.$zanda_admin_email['hu'].'?subject=aktivacio">'.$zanda_admin_email['hu'].'</a> címre arról az email címről, amiről regisztráltál, és akkor megkapod újra az aktiváló linket.']);
			}
		}
		//aktivacios kenyszer masik
		if ($admin_nyaral==0) {
			if ($kit_aux['aktivalo_kulcs']!='') if (time()-strtotime($kit_aux['mikortol'])>3600*24*7) {//tobb mint 7 napja nem aktivalt
				header('Content-type: text/html;charset=utf-8');
				kilep($lang[$kit_aux['nyelv']]['kisphpk']['Aktiválnod kell a regisztrációdat. Ha nem kaptad meg a regisztrációs levelet, írj egy emailt az <a href="mailto:'.$zanda_admin_email['hu'].'?subject=aktivacio">'.$zanda_admin_email['hu'].'</a> címre arról az email címről, amiről regisztráltál, és akkor megkapod újra az aktiváló linket.']);
			}
		}
		//TOR-hasznalat
		if (in_array($_SERVER['REMOTE_ADDR'],$torlist)) {
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$ki_aux['nyelv']]['kisphpk']['Ha játszani szeretnél, ne használd a Tor anonimizáló programot. Ha kérdésed van ezzel kapcsolatban, írj egy emailt az <a href="mailto:'.$zanda_admin_email['hu'].'">'.$zanda_admin_email['hu'].'</a> címre.']);
		}
		//be vagy-e allitva helyetteskent (a session reset elott, hgy ne tudj akarkinek bezavarni a session-jebe)
		if ($kit_aux['helyettes_id']!=$ki_aux['id']) {
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$ki_aux['nyelv']]['kisphpk']['Nem vagy beállítva helyettesnek.']);
		}
		//korabbi session-ok resetelese
		if ($ki_aux['epp_most_helyettes_id']>0) {
			mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$ki_aux['epp_most_helyettes_id']);
			mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($ki_aux['uccso_akt']))-strtotime($ki_aux['uccso_login'])).' where id='.$ki_aux['id']);
		}
		if ($kit_aux['epp_most_helyettes_id']>0) {
			mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$kit_aux['epp_most_helyettes_id']);
			mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($kit_aux['uccso_akt']))-strtotime($kit_aux['uccso_login'])).' where id='.$kit_aux['id']);
		}
		mysql_query('update userek set epp_kit_helyettesit=0,epp_most_helyettes_id=0,session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$ki_aux['id']);
		mysql_query('update userek set epp_kit_helyettesit=0,epp_most_helyettes_id=0,session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$kit_aux['id']);
		//van-e meg idod
		$kit_aux=mysql2row('select * from userek where id='.$kit_aux['id']);//a resetelt session is legyen mar hozzaadva
		if ($kit_aux['helyettesitett_ido']>3600*24) {
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$ki_aux['nyelv']]['kisphpk']['Ezt az accountot ezen a szerveren már nem lehet többet helyettesíteni.']);
		}
		//
		$kulon_ip=$_SERVER['REMOTE_ADDR'];$kulon_dn=gethostbyaddr($_SERVER['REMOTE_ADDR']);$ip_cim=$kulon_dn.' ('.$kulon_ip.')';
		$session_so=randomgen(32);
		$token=randomgen(32);
		$ttt=time()+$suti_hossz;
		$datum=date('Y-m-d H:i:s',$ttt);
		$mostani_datum=date('Y-m-d H:i:s');
		//kvazi az lep be, akit helyettesitenek
		mysql_query('update userek set epp_kit_helyettesit='.$kit_aux['id'].' where id='.$ki_aux['id']);
		mysql_query('update userek set epp_most_helyettes_id='.$ki_aux['id'].' where id='.$kit_aux['id']);
		mysql_query('update userek set session_so="'.$session_so.'",token="'.$token.'",session_ervenyesseg="'.$datum.'",uccso_multicsekk="'.$mostani_datum.'",uccso_akt="'.$mostani_datum.'",uccso_login="'.$mostani_datum.'",uccso_login_ip="'.$ip_cim.'",inaktivitasi_ertesito=0 where id='.$kit_aux['id']);
		$adataim=mysql2row('select * from userek where id='.$kit_aux['id']);
		$uid=$adataim['id'];
		setcookie('uid',$session_so.$uid,$ttt,'/');
		//
		mysql_select_db($database_mmog_nemlog);
		mysql_query('insert into loginek (uid,mikor,ip,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$ki_aux['id'].',"'.$mostani_datum.'","'.$ip_cim.'","'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('insert into loginek_osszes (uid,mikor,ip,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$ki_aux['id'].',"'.$mostani_datum.'","'.$ip_cim.'","'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('insert into helyettesitesek (ki,kit) values('.$ki_aux['id'].','.$kit_aux['id'].')') or hiba(__FILE__,__LINE__,mysql_error());
		//multipontozas
		$er3=mysql_query('select uid,timestampdiff(hour,mikor,now()) as elteres from loginek where uid!='.$ki_aux['id'].' and timestampdiff(hour,mikor,now())<=24 and ip="'.$ip_cim.'" order by mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
		$aux3=mysql_fetch_array($er3);
		if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
			$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
			mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$ki_aux['id'].','.$aux3[0].','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$aux3[0].','.$ki_aux['id'].','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
		}
		//minusz pont
		$er3=mysql_query('select kivel,timestampdiff(hour,l.mikor,now()) from multi_matrix mm, loginek l
where mm.ki='.$ki_aux['id'].' and mm.kivel=l.uid and timestampdiff(hour,l.mikor,now())<=24 and l.kulon_ip!="'.$kulon_ip.'"
order by l.mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
		$aux3=mysql_fetch_array($er3);
		if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
			$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
			mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$ki_aux['id'].','.$aux3[0].','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$aux3[0].','.$ki_aux['id'].','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
		}
		//
		mysql_select_db($database_mmog);
		//
		header('Location: .');exit;
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);exit;
}
/************************************************ HELYETTESÍTÉS VÉGE **************************************************************************/

/************************************************ LOGIN ELEJE **************************************************************************/
if ($_REQUEST['login_nev']!='') {
	$r=mysql_query('select * from userek where (nev="'.sanitstr($_REQUEST['login_nev']).'" or email="'.sanitstr($_REQUEST['login_nev']).'") and kitiltva=0 and inaktiv=0') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($r);
	$jelszo_hash=hash('whirlpool',$_REQUEST['login_jelszo'].$aux['jelszo_so'].$rendszer_so);
	if ($jelszo_hash==$aux['jelszo_hash']) {
		if (time()<strtotime($aux['kitiltva_meddig'])) {//ideiglenes kitiltas
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$aux['nyelv']]['kisphpk']['Ideiglenesen ki vagy tiltva '].$aux['kitiltva_meddig'].$lang[$aux['nyelv']]['kisphpk']['-ig. Ha nem tudod, miért, írj az <a href="mailto:'.$zanda_admin_email['hu'].'">'.$zanda_admin_email['hu'].'</a> címre.']);
		}
		//aktivacios kenyszer
		if ($admin_nyaral==0) {
			if ($aux['aktivalo_kulcs']!='') if (time()-strtotime($aux['mikortol'])>3600*24*7) {//tobb mint 7 napja nem aktivalt
				header('Content-type: text/html;charset=utf-8');
				kilep($lang[$aux['nyelv']]['kisphpk']['Aktiválnod kell a regisztrációdat. Ha nem kaptad meg a regisztrációs levelet, írj egy emailt az <a href="mailto:'.$zanda_admin_email['hu'].'?subject=aktivacio">'.$zanda_admin_email['hu'].'</a> címre arról az email címről, amiről regisztráltál, és akkor megkapod újra az aktiváló linket.']);
			}
		}
		//TOR-hasznalat
		if (in_array($_SERVER['REMOTE_ADDR'],$torlist)) {
			header('Content-type: text/html;charset=utf-8');
			kilep($lang[$aux['nyelv']]['kisphpk']['Ha játszani szeretnél, ne használd a Tor anonimizáló programot. Ha kérdésed van ezzel kapcsolatban, írj egy emailt az <a href="mailto:'.$zanda_admin_email['hu'].'">'.$zanda_admin_email['hu'].'</a> címre.']);
		}
		//korabbi session-ok resetelese
		if ((time()<strtotime($aux['session_ervenyesseg'])) && ($aux['session_so']!='') && $aux['epp_kit_helyettesit']==0 && $aux['epp_most_helyettes_id']==0) {//elo, nem helyettesitos session meghosszabbitasa
			$session_so=$aux['session_so'];
			$token=$aux['token'];
		} else {
			if ($aux['epp_most_helyettes_id']>0) {
				mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$aux['epp_most_helyettes_id']);
				mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($aux['uccso_akt']))-strtotime($aux['uccso_login'])).' where id='.$aux['id']);
			}
			if ($aux['epp_kit_helyettesit']>0) {
				$kit_aux=mysql2row('select * from userek where id='.$aux['epp_kit_helyettesit']);
				mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(min(time(),strtotime($kit_aux['uccso_akt']))-strtotime($kit_aux['uccso_login'])).' where id='.$kit_aux['id']);
				mysql_query('update userek set epp_kit_helyettesit=0,epp_most_helyettes_id=0,session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$kit_aux['id']);
			}
			mysql_query('update userek set epp_kit_helyettesit=0,epp_most_helyettes_id=0,session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$aux['id']);
			$session_so=randomgen(32);
			$token=randomgen(32);
		}
		//
		$kulon_ip=$_SERVER['REMOTE_ADDR'];$kulon_dn=gethostbyaddr($_SERVER['REMOTE_ADDR']);$ip_cim=$kulon_dn.' ('.$kulon_ip.')';
		$ttt=time()+$suti_hossz;
		$datum=date('Y-m-d H:i:s',$ttt);
		$mostani_datum=date('Y-m-d H:i:s');
		mysql_query('update userek set session_so="'.$session_so.'",token="'.$token.'",session_ervenyesseg="'.$datum.'",uccso_multicsekk="'.$mostani_datum.'",uccso_akt="'.$mostani_datum.'",uccso_login="'.$mostani_datum.'",uccso_login_ip="'.$ip_cim.'",inaktivitasi_ertesito=0 where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
		$r=mysql_query('select * from userek where id='.$aux['id']) or hiba(__FILE__,__LINE__,mysql_error());
		$adataim=mysql_fetch_array($r);
		$uid=$adataim['id'];
		setcookie('uid',$session_so.$uid,$ttt,'/');
		//
		mysql_select_db($database_mmog_nemlog);
		mysql_query('insert into loginek (uid,mikor,ip,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$uid.',"'.$mostani_datum.'","'.$ip_cim.'","'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
		mysql_query('insert into loginek_osszes (uid,mikor,ip,kulon_ip,kulon_dn,sub_ip,sub_dn) values('.$uid.',"'.$mostani_datum.'","'.$ip_cim.'","'.$kulon_ip.'","'.$kulon_dn.'",substring_index("'.$kulon_ip.'",".",2),substring_index("'.$kulon_dn.'",".",-2))') or hiba(__FILE__,__LINE__,mysql_error());
		//multipontozas
		$er3=mysql_query('select uid,timestampdiff(hour,mikor,now()) as elteres from loginek where uid!='.$uid.' and timestampdiff(hour,mikor,now())<=24 and ip="'.$ip_cim.'" order by mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
		$aux3=mysql_fetch_array($er3);
		if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
			$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
			mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$uid.','.$aux3[0].','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('insert into multi_matrix (ki,kivel,pont) values('.$aux3[0].','.$uid.','.$bunti_pont.') on duplicate key update pont=pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
		}
		//minusz pont
		$er3=mysql_query('select kivel,timestampdiff(hour,l.mikor,now()) from multi_matrix mm, loginek l
where mm.ki='.$uid.' and mm.kivel=l.uid and timestampdiff(hour,l.mikor,now())<=24 and l.kulon_ip!="'.$kulon_ip.'"
order by l.mikor desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
		$aux3=mysql_fetch_array($er3);
		if ($aux3[0]>0) if ($aux3[1]<=24) {//24 oran belul
			$bunti_pont=round(100/($aux3[1]+1));//0-1 oraig 0, 1-2 oraig 1...
			mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$uid.','.$aux3[0].','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
			mysql_query('insert into multi_matrix (ki,kivel,minusz_pont) values('.$aux3[0].','.$uid.','.$bunti_pont.') on duplicate key update minusz_pont=minusz_pont+'.$bunti_pont) or hiba(__FILE__,__LINE__,mysql_error());
		}
		//
		mysql_select_db($database_mmog);
		//
		header('Location: .');exit;
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);exit;
}
/************************************************ LOGIN VÉGE **************************************************************************/

/************************************************ LOGOUT ELEJE **************************************************************************/
if (isset($_REQUEST['logout'])) {
	if ((int)$uid>0) {
		//helyettesites?
		if ($adataim['epp_most_helyettes_id']>0) {
			mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00",epp_kit_helyettesit=0 where id='.$adataim['epp_most_helyettes_id']);
			mysql_query('update userek set epp_most_helyettes_id=0, helyettesitett_ido=helyettesitett_ido+'.sanitint_poz(time()-strtotime($adataim['uccso_login'])).' where id='.$uid);
		}
		//
		mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
		setcookie('uid','',time()-3600,'/');
	}
	header('Location: .');exit;
}
/************************************************ LOGOUT VÉGE **************************************************************************/

/************************************************ REG TORLES ELEJE **************************************************************************/
if (isset($_REQUEST['del_reg'])) {
	if ((int)$uid>0) {
		$jelszo_hash=hash('whirlpool',$_REQUEST['jelszo'].$adataim['jelszo_so'].$rendszer_so);
		if ($jelszo_hash==$adataim['jelszo_hash']) if ($adataim['epp_most_helyettes_id']==0) {//a helyettesem ne torolhessen, csak en magamat
			if ($adataim['techszint']>4) {//24 oras torlesre jelolni
				mysql_query('insert into torlendo_userek (user_id,mikor) values('.$uid.',"'.date('Y-m-d H:i:s',time()+24*3600).'")');
				mysql_query('update userek set jelszo_so="",jelszo_hash="",kozos_jelszo_hash="",helyettes_id=0,email="",zanda_id=0 where id='.$uid);
				mysql_query('update userek set session_so="",token="",session_ervenyesseg="0000-00-00 00:00:00" where id='.$uid);
			} else {//azonnal
				del_ures_user($uid);
			}
			setcookie('uid','',time()-3600,'/');
		}
	}
	header('Location: .');exit;
}
/************************************************ REG TORLES VÉGE **************************************************************************/

if ($nyilvanos_oldal!=1) if (!$ismert) kilep();


?>