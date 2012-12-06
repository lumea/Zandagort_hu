<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$adataim['admin']) kilep();


//automatikus atarazodas
$hatralevo_masodpercek_szama=strtotime($szerver_varhato_vege)-time();
if ($hatralevo_masodpercek_szama<0) $hatralevo_masodpercek_szama=0;
$hatralevo_honapok_szama=ceil($hatralevo_masodpercek_szama/3600/24/30);
if ($hatralevo_honapok_szama>5) $hatralevo_honapok_szama=5;


//uj, penz alapu
$_REQUEST['ft']=(int)$_REQUEST['ft'];
if ($_REQUEST['ft']>0) {
	$hany_honapra=0;$milyen_szint=-1;
	switch($_REQUEST['ft']) {
		case 490:$hany_honapra=1;$milyen_szint=-1;break;
		case 950:$hany_honapra=2;$milyen_szint=-1;break;
		case 1390:$hany_honapra=3;$milyen_szint=-1;break;
		case 1790:$hany_honapra=4;$milyen_szint=-1;break;
		case 2190:$hany_honapra=5;$milyen_szint=-1;break;
		case 790:$hany_honapra=1;$milyen_szint=1;break;
		case 1550:$hany_honapra=2;$milyen_szint=1;break;
		case 2290:$hany_honapra=3;$milyen_szint=1;break;
		case 2890:$hany_honapra=4;$milyen_szint=1;break;
		case 3490:$hany_honapra=5;$milyen_szint=1;break;
	}
	if ($hany_honapra>=$hatralevo_honapok_szama) {
		$_REQUEST['nap']=-1;
	} else {
		$_REQUEST['nap']=30*$hany_honapra;
	}
	$_REQUEST['upgrade']=$milyen_szint;
}



$_REQUEST['id']=(int)$_REQUEST['id'];
$_REQUEST['mod']=(int)$_REQUEST['mod'];if ($_REQUEST['mod']<0) $_REQUEST['mod']=0;if ($_REQUEST['mod']>6) $_REQUEST['mod']=0;//max 6-ig mehet
if ($_REQUEST['mod']!=5) $_REQUEST['nap']=(int)$_REQUEST['nap'];//twitternel a nap a twitter account neve
/*
0 = egyeb
1 = atutalas
2 = paypal
3 = sms
4 = meghivo
5 = twitter!!! userek.twitter_nev
6 = ajandek, pl bugreport-ert
*/

$er=mysql_query('select * from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$user=mysql_fetch_array($er);

//UPGRADE, ha szukseges
$_REQUEST['upgrade']=(int)$_REQUEST['upgrade'];
if ($_REQUEST['upgrade']==1) {
	if ($user) if ($user['premium_szint']!=2) {
		$mennyi_alap_ido=0;
		if ($user['premium']==1) $mennyi_alap_ido=strtotime($szerver_varhato_vege)-time();
		elseif (time()<strtotime($user['premium_alap'])) $mennyi_alap_ido=strtotime($user['premium_alap'])-time();
		$mennyi_emelt_ido=round($mennyi_alap_ido/1.6);
		$meddig_emelt=date('Y-m-d H:i:s',time()+$mennyi_emelt_ido);
		mysql_query('update userek set premium=0,premium_alap="'.$meddig_emelt.'",premium_emelt="'.$meddig_emelt.'",premium_szint=2 where id='.$user['id']);
	}
}
//DOWNGRADE, ha szukseges
if ($_REQUEST['upgrade']==-1) {
	if ($user) if ($user['premium_szint']!=1) {
		//mysql_query('update userek set premium_szint=1 where id='.$user['id']);
		$mennyi_emelt_ido=0;
		if ($user['premium']==2) $mennyi_emelt_ido=strtotime($szerver_varhato_vege)-time();
		elseif (time()<strtotime($user['premium_emelt'])) $mennyi_emelt_ido=strtotime($user['premium_emelt'])-time();
		$mennyi_alap_ido=round($mennyi_emelt_ido*1.6);
		$meddig_alap=date('Y-m-d H:i:s',time()+$mennyi_alap_ido);
		mysql_query('update userek set premium=0,premium_alap="'.$meddig_alap.'",premium_emelt="0000-00-00 00:00:00",premium_szint=1 where id='.$user['id']);
	}
}

$er=mysql_query('select * from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$user=mysql_fetch_array($er);


function premium_hosszabbitas($kinek,$hany_nappal,$mod) {
	global $szerver_prefix,$hatralevo_honapok_szama,$database_mmog,$database_mmog_nemlog,$zanda_homepage_url,$zanda_admin_email;
	if ($mod==5) {$twitter_nev=$hany_nappal;$hany_nappal=30;}
	$er=mysql_query('select * from userek where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
	$user=mysql_fetch_array($er);
	if ($user) {//letezik-e a user
		if ($mod==5) if (strlen($user['twitter_nev'])>0) return;//csak egyszer lehet elsutni a twittert
		if ($hany_nappal==-1) {
			mysql_query('update userek set premium_lejar_ertesito=1,premium='.$user['premium_szint'].' where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
		} else {
			if (time()<strtotime($user['premium_alap'])) {//meghosszabbitas
				mysql_query('update userek set premium_lejar_ertesito=1,premium_alap=timestampadd(day,'.$hany_nappal.',premium_alap) where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
			} else {//uj premium idoszak
				mysql_query('update userek set premium_lejar_ertesito=1,premium_alap=timestampadd(day,'.$hany_nappal.',now()) where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
			}
			if ($user['premium_szint']==2) {//EMELT
				mysql_query('update userek set premium_lejar_ertesito=1,premium_emelt=premium_alap where id='.$kinek);
			}
		}
		$becsult_hany_nappal=$hany_nappal;if ($hany_nappal==-1) $becsult_hany_nappal=30*$hatralevo_honapok_szama;
		mysql_select_db($database_mmog_nemlog);
		mysql_query('insert into premium_elofizetesek (user_id,fizetesi_mod,idotartam,becsult_idotartam,szint,penznem,szamlazasi_nev,szamlazasi_cim) values('.$kinek.','.$mod.','.$hany_nappal.','.$becsult_hany_nappal.','.$user['premium_szint'].',"'.(($user['nyelv']=='hu')?'HUF':'USD').'","'.$user['szamlazasi_nev'].'","'.$user['szamlazasi_cim'].'")') or hiba(__FILE__,__LINE__,mysql_error());
		mysql_select_db($database_mmog);
		//
		$er=mysql_query('select * from userek where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
		$user=mysql_fetch_array($er);
		if ($user['nyelv']=='hu') {
			if ($mod==4) $message="Kedves ".$user['nev']."!\n\nÉrtesítünk róla, hogy az egyik általad meghívott játékos előfizetett a prémium szolgáltatásokra, és mivel te hívtad meg a játékba, ezért a napjainak 10%-át, vagyis $hany_nappal napot te is megkapsz. Így most ".$user['premium_alap']."-ig elő vagy fizetve. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
			elseif ($mod==5) {
				$message="Kedves ".$user['nev']."!\n\nKöszönjük, hogy követed Zandagortot Twitteren. Ezért kapsz 30 prémium napot ingyen, így most ".$user['premium_alap']."-ig elő vagy fizetve. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
				mysql_query('update userek set twitter_nev="'.sanitstr($twitter_nev).'" where id='.$kinek) or hiba(__FILE__,__LINE__,mysql_error());
			} elseif ($mod==6) {
				if ($hany_nappal==-1) $message="Kedves ".$user['nev']."!\n\nBugriportért vagy más hasonló jótéteményért cserébe a forduló végéig ingyen prémiumot kapsz. Köszönjük a segítséget. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
				else $message="Kedves ".$user['nev']."!\n\nBugriportért vagy más hasonló jótéteményért cserébe $hany_nappal nap ingyen prémiumot kapsz, így most ".$user['premium_alap']."-ig elő vagy fizetve. Köszönjük a segítséget. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
			} else {
				if ($hany_nappal==-1) $message="Kedves ".$user['nev']."!\n\nNagyon szépen köszönjük, hogy prémium előfizetéssel is támogatod a játék fejlesztését és fenntartását. Az általad befizetett összeggel az $szerver_prefix szerver végéig elő vagy fizetve. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
				else $message="Kedves ".$user['nev']."!\n\nNagyon szépen köszönjük, hogy prémium előfizetéssel is támogatod a játék fejlesztését és fenntartását. Az általad befizetett $hany_nappal nappal ".$user['premium_alap']."-ig elő vagy fizetve. A prémiumokról részletesen itt olvashatsz:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
			}
			zandamail($user['nyelv'],array(
				'email'	=>	$user['email'],
				'name'	=>	$user['nev'],
				'subject'	=>	'Zandagort prémium előfizetés',
				'html'	=>	'<p>'.nl2br($message).'</p>',
				'plain'	=>	$message
			));
		} else {
			if ($mod==4) $message="Dear ".$user['nev']."!\n\nWe inform you, that a player invited by you subscribed to premium. And since you have invited him/her, you get 10% of his/her subscription, $hany_nappal days. So now you are subscribed until ".$user['premium_alap'].". You can read about premium features here:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort and his people\n".$zanda_homepage_url[$user['nyelv']];
			elseif ($mod==5) {//twitter
			} elseif ($mod==6) {
				if ($hany_nappal==-1) $message="Dear ".$user['nev']."!\n\nFor bugreport or other good deed you get premium gratis until the end of the server. Thank you for your help. You can read about premium features here:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort and his people\n".$zanda_homepage_url[$user['nyelv']];
				else $message="Dear ".$user['nev']."!\n\nFor bugreport or other good deed you get $hany_nappal days of premium gratis, so you are now subscribed until ".$user['premium_alap'].". Thank you for your help. You can read about premium features here:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort and his people\n".$zanda_homepage_url[$user['nyelv']];
			} else {
				if ($hany_nappal==-1) $message="Dear ".$user['nev']."!\n\nThank you very much for supporting the maintenance and development of the game by subscribing to premium. With your payment you are subscribed until the end of the $szerver_prefix server. You can read about premium features here:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort and his people\n".$zanda_homepage_url[$user['nyelv']];
				else $message="Dear ".$user['nev']."!\n\nThank you very much for supporting the maintenance and development of the game by subscribing to premium. With your payment of $hany_nappal days you are now subscribed until ".$user['premium_alap'].". You can read about premium features here:\n".$zanda_homepage_url[$user['nyelv']]."premium/\n\n\nZandagort and his people\n".$zanda_homepage_url[$user['nyelv']];
			}
			zandamail($user['nyelv'],array(
				'email'	=>	$user['email'],
				'name'	=>	$user['nev'],
				'subject'	=>	'Zandagort premium subscription',
				'html'	=>	'<p>'.nl2br($message).'</p>',
				'plain'	=>	$message
			));
		}
	}
}

premium_hosszabbitas($_REQUEST['id'],$_REQUEST['nap'],$_REQUEST['mod']);

if ($_REQUEST['mod']<5) {//twitter-re, ajandekra nem jar plusz a meghivonak
	$er=mysql_query('select kin_keresztul_id from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]>0) {
		if ($_REQUEST['nap']>0) premium_hosszabbitas($aux[0],round($_REQUEST['nap']/10),4);
		elseif ($_REQUEST['nap']==-1) premium_hosszabbitas($aux[0],round($hatralevo_honapok_szama*30/10),4);//szerver vegeig
	}
}


$penz=0;
if ($_REQUEST['mod']==1 || $_REQUEST['mod']==2) {
	if ($user['premium_szint']==2) {//EMELT
		if ($_REQUEST['nap']==-1) {//szerver vegeig
			switch($hatralevo_honapok_szama) {
				case 1:$penz=790;break;
				case 2:$penz=1550;break;
				case 3:$penz=2290;break;
				case 4:$penz=2890;break;
				case 5:$penz=3490;break;
			}
		} elseif ($_REQUEST['nap']<=30) $penz=790/30*$_REQUEST['nap'];
		elseif ($_REQUEST['nap']<=60) $penz=1550/60*$_REQUEST['nap'];
		elseif ($_REQUEST['nap']<=90) $penz=2290/90*$_REQUEST['nap'];
		else $penz=2890/120*$_REQUEST['nap'];
		$alap_emelt='emelt';
	} else {//ALAP
		if ($_REQUEST['nap']==-1) {//szerver vegeig
			switch($hatralevo_honapok_szama) {
				case 1:$penz=490;break;
				case 2:$penz=950;break;
				case 3:$penz=1390;break;
				case 4:$penz=1790;break;
				case 5:$penz=2190;break;
			}
		} elseif ($_REQUEST['nap']<=30) $penz=490/30*$_REQUEST['nap'];
		elseif ($_REQUEST['nap']<=60) $penz=950/60*$_REQUEST['nap'];
		elseif ($_REQUEST['nap']<=90) $penz=1390/90*$_REQUEST['nap'];
		else $penz=1790/120*$_REQUEST['nap'];
		$alap_emelt='alap';
	}
}
$penz=round($penz);

if ($penz>0) {
	mysql_query('update userek set premium_ertesito=1 where id='.$user['id']);//valojaban azt jelzi, hogy o mar fizett elo (penzzel)
	if ($user['nyelv']=='hu') {
		$message="[".$user['id']."] ".$user['nev']." ".($_REQUEST['nap']==-1?((30*$hatralevo_honapok_szama).' (-1)'):($_REQUEST['nap']))." napot előfizetett $alap_emelt prémiumra. A számlázási adatok:\n\nNév: ".$user['szamlazasi_nev']."\nCím: ".$user['szamlazasi_cim']."\nÖsszeg: ".$penz." forint\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
	} else {
		$message="[".$user['id']."] ".$user['nev']." ".($_REQUEST['nap']==-1?((30*$hatralevo_honapok_szama).' (-1)'):($_REQUEST['nap']))." napot előfizetett $alap_emelt prémiumra. A számlázási adatok:\n\nNév: ".$user['szamlazasi_nev']."\nCím: ".$user['szamlazasi_cim']."\nÖsszeg: ".number_format($penz/100,2,',',' ')." dollár\n\n\nZandagort és népe\n".$zanda_homepage_url[$user['nyelv']];
	}
	zandamail('hu',array(
		'email'	=>	$zanda_admin_email['hu'],
		'name'	=>	'Zandagort admin',
		'subject'	=>	'Zandagort premium elofizetes - szamla',
		'html'	=>	'<p>'.nl2br($message).'</p>',
		'plain'	=>	$message
	));
}

kilep();
?>