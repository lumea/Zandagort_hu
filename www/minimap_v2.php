<?
include('csatlak.php');$font_cim='img/arial.ttf';
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
if (premium_szint()<2) kilep();


$radarjog=$jogaim[10];
$nagyradarjog=$jogaim[11];


$maxx=800;$maxy=800;
$kep=imagecreatetruecolor($maxx,$maxy);
$fekete=imagecolorallocate($kep,0,0,0);
imagefill($kep,0,0,$fekete);

function hex2color($kep,$s) {
	return imagecolorallocate($kep,hexdec(substr($s,0,2)),hexdec(substr($s,2,2)),hexdec(substr($s,4,2)));
}

$psz=array();
if (isset($_REQUEST['x'])) {
	$rx=2*((int)$_REQUEST['x']);
	$ry=2*((int)$_REQUEST['y']);
	$felszel=(int)$_REQUEST['zoom'];if ($felszel<1000) $felszel=1000;if ($felszel>128000) $felszel=128000;
	$x1=$rx-$felszel;$y1=$ry-$felszel;
	$x2=$rx+$felszel;$y2=$ry+$felszel;
	$asz=(int)$_REQUEST['asz'];
	$kis_bolygo_meret=(int)$_REQUEST['kbm'];if ($kis_bolygo_meret<1) $kis_bolygo_meret=1;if ($kis_bolygo_meret>50) $kis_bolygo_meret=50;
	$nagy_bolygo_meret=(int)$_REQUEST['nbm'];if ($nagy_bolygo_meret<1) $nagy_bolygo_meret=1;if ($nagy_bolygo_meret>50) $nagy_bolygo_meret=50;
	$plusz_bolygo_meret=(int)$_REQUEST['pbm'];if ($plusz_bolygo_meret<1) $plusz_bolygo_meret=1;if ($plusz_bolygo_meret>50) $plusz_bolygo_meret=50;
	$bolygo_nevek=(int)$_REQUEST['bn'];
	for($i=1;$i<=8;$i++) if (isset($_REQUEST['psz'.$i.'n'])) {
		$er=mysql_query('select id from szovetsegek where nev="'.sanitstr($_REQUEST['psz'.$i.'n']).'"');
		$aux=mysql_fetch_array($er);
		if ($aux[0]>0) $psz[]=array('sz',$aux[0],hex2color($kep,$_REQUEST['psz'.$i.'sz']));
	}
	for($i=1;$i<=8;$i++) if (isset($_REQUEST['psz'.$i.'n'])) {
		$er=mysql_query('select id from userek where nev="'.sanitstr($_REQUEST['psz'.$i.'n']).'" and (karrier!=3 or speci!=3)');
		$aux=mysql_fetch_array($er);
		if ($aux[0]>0) $psz[]=array('u',$aux[0],hex2color($kep,$_REQUEST['psz'.$i.'sz']));
	}
	$ter=(int)$_REQUEST['ter'];
	$flottak=(int)$_REQUEST['flottak'];
} else {
	$felszel=128000;
	$x1=-$felszel;$y1=-$felszel;
	$x2=$felszel;$y2=$felszel;
	$asz=1;
	$kis_bolygo_meret=1;
	$nagy_bolygo_meret=2;
	$plusz_bolygo_meret=4;
	$bolygo_nevek=1;
	$ter=1;
	$flottak=1;
}
if ($asz<0) $asz=0;if ($asz>2) $asz=0;
if ($bolygo_nevek<0) $bolygo_nevek=0;if ($bolygo_nevek>2) $bolygo_nevek=0;
if ($ter<0) $ter=0;if ($ter>3) $ter=0;

$s_zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);
$feher=imagecolorallocate($kep,255,255,255);
$piros=imagecolorallocate($kep,160,0,0);
$zanda_szin=imagecolorallocate($kep,220,42,180);
//
$zold=imagecolorallocate($kep,0,255,0);
$kek=imagecolorallocate($kep,100,160,255);
$hadi_szin=imagecolorallocate($kep,255,0,0);
$beke_szin=imagecolorallocate($kep,255,160,0);
$mnt_szin=imagecolorallocate($kep,255,255,0);
//
$hexa_zold=imagecolorallocate($kep,0,128,0);
$hexa_kek=imagecolorallocate($kep,50,80,128);
$hexa_hadi_szin=imagecolorallocate($kep,128,0,0);
$hexa_beke_szin=imagecolorallocate($kep,128,80,0);
$hexa_mnt_szin=imagecolorallocate($kep,128,128,0);
//
$osztaly_szin[1]=imagecolorallocate($kep,83,154,148);
$osztaly_szin[2]=imagecolorallocate($kep,236,164,62);
$osztaly_szin[3]=imagecolorallocate($kep,196,199,110);
$osztaly_szin[4]=imagecolorallocate($kep,70,97,56);
$osztaly_szin[5]=imagecolorallocate($kep,225,234,241);

//hexak
if ($ter>0) {
	if (count($psz)>0) {
		for($i=0;$i<count($psz);$i++) {
			$szin_rgb=imagecolorsforindex($kep,$psz[$i][2]);
			$sotet_verzio=imagecolorallocate($kep,$szin_rgb['red']/2,$szin_rgb['green']/2,$szin_rgb['blue']/2);
			//
			if ($psz[$i][0]=='u') $where='tulaj='.$psz[$i][1];
			else $where='tulaj_szov='.$psz[$i][1];
			$er=mysql_query('select h.x,h.y from bolygok b, hexak h where b.'.$where.' and h.voronoi_bolygo_id=b.id');
			while($aux=mysql_fetch_array($er)) {
				$hexa_x=$aux['x'];
				$hexa_y=$aux['y'];
				$orig_x=$hexa_x*round(BOLYGOK_KOZTI_TAVOLSAG*sqrt(3));
				$orig_y=$hexa_y*BOLYGOK_KOZTI_TAVOLSAG*2-(($hexa_x%2==0)?0:BOLYGOK_KOZTI_TAVOLSAG);
				unset($csucsok);
				for($alfa=0;$alfa<360;$alfa+=60) {
					$csucsok[]=round(($orig_x+(BOLYGOK_KOZTI_TAVOLSAG*1.15+1)*cos($alfa/180*M_PI)-$x1)/($x2-$x1)*$maxx);
					$csucsok[]=round(($orig_y+(BOLYGOK_KOZTI_TAVOLSAG*1.15+1)*sin($alfa/180*M_PI)-$y1)/($y2-$y1)*$maxy);
				}
				imagefilledpolygon($kep,$csucsok,6,$sotet_verzio);
			}
		}
	}
}


//racs
if ($felszel>32000) {
	$x1_zold=floor($x1/10000);$x1_zold_pix=round((10000*$x1_zold-$x1)/($x2-$x1)*$maxx);
	$y1_zold=floor($y1/10000);$y1_zold_pix=round((10000*$y1_zold-$y1)/($y2-$y1)*$maxy);
	$x2_zold=ceil($x2/10000);$x2_zold_pix=round((10000*$x2_zold-$x1)/($x2-$x1)*$maxx);
	$y2_zold=ceil($y2/10000);$y2_zold_pix=round((10000*$y2_zold-$y1)/($y2-$y1)*$maxy);
	for($i=$x1_zold;$i<=$x2_zold;$i++) {$x=round((10000*$i-$x1)/($x2-$x1)*$maxx);imageline($kep,$x,$y1_zold_pix,$x,$y2_zold_pix,($i==0)?$v_zold:$s_zold);}
	for($i=$y1_zold;$i<=$y2_zold;$i++) {$y=round((10000*$i-$y1)/($y2-$y1)*$maxy);imageline($kep,$x1_zold_pix,$y,$x2_zold_pix,$y,($i==0)?$v_zold:$s_zold);}
} else {
	$x1_zold=floor($x1/2000);$x1_zold_pix=round((2000*$x1_zold-$x1)/($x2-$x1)*$maxx);
	$y1_zold=floor($y1/2000);$y1_zold_pix=round((2000*$y1_zold-$y1)/($y2-$y1)*$maxy);
	$x2_zold=ceil($x2/2000);$x2_zold_pix=round((2000*$x2_zold-$x1)/($x2-$x1)*$maxx);
	$y2_zold=ceil($y2/2000);$y2_zold_pix=round((2000*$y2_zold-$y1)/($y2-$y1)*$maxy);
	for($i=$x1_zold;$i<=$x2_zold;$i++) {$x=round((2000*$i-$x1)/($x2-$x1)*$maxx);imageline($kep,$x,$y1_zold_pix,$x,$y2_zold_pix,($i==0)?$v_zold:$s_zold);}
	for($i=$y1_zold;$i<=$y2_zold;$i++) {$y=round((2000*$i-$y1)/($y2-$y1)*$maxy);imageline($kep,$x1_zold_pix,$y,$x2_zold_pix,$y,($i==0)?$v_zold:$s_zold);}
}


//diplomacia
$hadi=array();$beke=array();$mnt=array();
$er=mysql_query('select * from diplomacia_statuszok where ki='.$adataim['tulaj_szov']);
while($aux=mysql_fetch_array($er)) {
	switch($aux['mi']) {
		case 1:$hadi[]=$aux['kivel'];break;
		case 2:$beke[]=$aux['kivel'];break;
		case 3:$mnt[]=$aux['kivel'];break;
	}
}
//
$er=mysql_query('select b.x,b.y
,if(u.karrier=3 and u.speci=3,0,b.tulaj) as tulaj
,if(u.karrier=3 and u.speci=3,0,b.tulaj_szov) as tulaj_szov
,b.osztaly,b.kulso_nev
from bolygok b
left join userek u on u.id=b.tulaj
where b.letezik=1');
while($aux=mysql_fetch_array($er)) {
	$x=round(($aux['x']-$x1)/($x2-$x1)*$maxx);
	$y=round(($aux['y']-$y1)/($y2-$y1)*$maxy);
	if ($x>0) if ($x<$maxx) if ($y>0) if ($y<$maxy) {
		if ($aux['tulaj']>0) $meret=$nagy_bolygo_meret;else $meret=$kis_bolygo_meret;
		//alapszin
		switch($asz) {
			case 0://semmi
				$szin=$feher;
			break;
			case 1://osztaly
				$szin=$osztaly_szin[$aux['osztaly']];
			break;
			case 2://diplo
				if ($aux['tulaj']==$uid) $szin=$zold;
				elseif ($aux['tulaj_szov']==$adataim['szovetseg']) $szin=$kek;
				elseif ($aux['tulaj']) {
					if (in_array($aux['tulaj_szov'],$hadi)) $szin=$hadi_szin;
					elseif (in_array($aux['tulaj_szov'],$beke)) $szin=$beke_szin;
					elseif (in_array($aux['tulaj_szov'],$mnt)) $szin=$mnt_szin;
					else $szin=$piros;
				} else $szin=$feher;
			break;
		}
		//extra szin
		for($i=0;$i<count($psz);$i++) {
			if (($psz[$i][0]=='u' && $psz[$i][1]==$aux['tulaj']) || ($psz[$i][0]=='sz' && $psz[$i][1]==$aux['tulaj_szov'])) {$szin=$psz[$i][2];$meret=$plusz_bolygo_meret;}
		}
		//
		imagefilledellipse($kep,$x,$y,2*$meret-1,2*$meret-1,$szin);
		if (($felszel<=2000 && $bolygo_nevek==2) || ($felszel<=4000 && $bolygo_nevek>0 && $aux['tulaj']>0)) imagettftext($kep,7,0,$x+$meret,$y-$meret,$szin,$font_cim,$aux['kulso_nev']);
		//if ($felszel<=4000) if ($bolygo_nevek==1) if ($aux['tulaj']>0) imagettftext($kep,7,0,$x+$meret,$y-$meret,$szin,$font_cim,$aux['kulso_nev']);
	}
}

if ($flottak) {
	if ($nagyradarjog) {
		$er=mysql_query('select f.x,f.y,f.tulaj,f.tulaj_szov,f.nev from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].'
union all
select fid,lathatosag from lat_szov_flotta lszf, diplomacia_statuszok dsz where dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=lszf.szid and dsz.mi='.DIPLO_TESTVER.') t
group by fid) lt where f.id=lt.fid');
	} elseif ($radarjog) {
		$er=mysql_query('select f.x,f.y,f.tulaj,f.tulaj_szov,f.nev from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].') t
group by fid) lt where f.id=lt.fid');
	} else {
		$er=mysql_query('select f.x,f.y,f.tulaj,f.tulaj_szov,f.nev from flottak f, lat_user_flotta lt where f.id=lt.fid and lt.uid='.$uid);
	}
	while($aux=mysql_fetch_array($er)) {
		$x=round(($aux['x']-$x1)/($x2-$x1)*$maxx);
		$y=round(($aux['y']-$y1)/($y2-$y1)*$maxy);
		if ($x>0) if ($x<$maxx) if ($y>0) if ($y<$maxy) {
			if ($aux['tulaj']>0) $meret=$nagy_bolygo_meret;else $meret=$kis_bolygo_meret;
			//alapszin=diplo
			if ($aux['tulaj']==$uid) $szin=$zold;
			elseif ($aux['tulaj_szov']==$adataim['szovetseg']) $szin=$kek;
			elseif ($aux['tulaj']>0) {
				if (in_array($aux['tulaj_szov'],$hadi)) $szin=$hadi_szin;
				elseif (in_array($aux['tulaj_szov'],$beke)) $szin=$beke_szin;
				elseif (in_array($aux['tulaj_szov'],$mnt)) $szin=$mnt_szin;
				else $szin=$piros;
			} elseif ($aux['tulaj']<0) $szin=$zanda_szin;
			else $szin=$feher;
			//extra szin
			for($i=0;$i<count($psz);$i++) {
				if (($psz[$i][0]=='u' && $psz[$i][1]==$aux['tulaj']) || ($psz[$i][0]=='sz' && $psz[$i][1]==$aux['tulaj_szov'])) {$szin=$psz[$i][2];$meret=$plusz_bolygo_meret;}
			}
			//
			imageline($kep,$x-2*$meret,$y,$x+2*$meret,$y,$szin);
			imageline($kep,$x,$y-2*$meret,$x,$y+2*$meret,$szin);
			if (($felszel<=2000 && $bolygo_nevek==2) || ($felszel<=4000 && $bolygo_nevek>0 && $aux['tulaj']>0)) imagettftext($kep,7,0,$x+$meret,$y-$meret,$szin,$font_cim,$aux['nev']);
		}
	}
}




//terkep koordinatak
if ($felszel>32000) {
	for($i=$x1_zold;$i<=$x2_zold;$i++) {
		$x=round((10000*$i-$x1)/($x2-$x1)*$maxx);
		if ($i>0) $szoveg=$lang[$lang_lang]['kisphpk']['K'].' '.(5*$i);elseif ($i<0) $szoveg=$lang[$lang_lang]['kisphpk']['Ny'].' '.(-5*$i);else $szoveg='0';
		$doboz=imagettfbbox(8,90,$font_cim,$szoveg);$w=abs($doboz[6]-$doboz[2]);$h=abs($doboz[7]-$doboz[3]);
		imagefilledrectangle($kep,$x-1-$w,5,$x-1,5+$h,$fekete);
		imagettftext($kep,8,90,$x-1,4+$h,$feher,$font_cim,$szoveg);
	}
	for($i=$y1_zold;$i<=$y2_zold;$i++) {
		$y=round((10000*$i-$y1)/($y2-$y1)*$maxy);
		if ($i>0) $szoveg=$lang[$lang_lang]['kisphpk']['D'].' '.(5*$i);elseif ($i<0) $szoveg=$lang[$lang_lang]['kisphpk']['É'].' '.(-5*$i);else $szoveg='0';
		$doboz=imagettfbbox(8,0,$font_cim,$szoveg);$w=abs($doboz[6]-$doboz[2]);$h=abs($doboz[7]-$doboz[3]);
		imagefilledrectangle($kep,5,$y-2-$h,5+$w,$y-2,$fekete);
		imagettftext($kep,8,0,5,$y-2,$feher,$font_cim,$szoveg);
	}
} else {
	for($i=$x1_zold;$i<=$x2_zold;$i++) {
		$x=round((2000*$i-$x1)/($x2-$x1)*$maxx);
		if ($i>0) $szoveg=$lang[$lang_lang]['kisphpk']['K'].' '.$i;elseif ($i<0) $szoveg=$lang[$lang_lang]['kisphpk']['Ny'].' '.(-$i);else $szoveg='0';
		$doboz=imagettfbbox(8,90,$font_cim,$szoveg);$w=abs($doboz[6]-$doboz[2]);$h=abs($doboz[7]-$doboz[3]);
		imagefilledrectangle($kep,$x-1-$w,5,$x-1,5+$h,$fekete);
		imagettftext($kep,8,90,$x-1,4+$h,$feher,$font_cim,$szoveg);
	}
	for($i=$y1_zold;$i<=$y2_zold;$i++) {
		$y=round((2000*$i-$y1)/($y2-$y1)*$maxy);
		if ($i>0) $szoveg=$lang[$lang_lang]['kisphpk']['D'].' '.$i;elseif ($i<0) $szoveg=$lang[$lang_lang]['kisphpk']['É'].' '.(-$i);else $szoveg='0';
		$doboz=imagettfbbox(8,0,$font_cim,$szoveg);$w=abs($doboz[6]-$doboz[2]);$h=abs($doboz[7]-$doboz[3]);
		imagefilledrectangle($kep,5,$y-2-$h,5+$w,$y-2,$fekete);
		imagettftext($kep,8,0,5,$y-2,$feher,$font_cim,$szoveg);
	}
}


mysql_close($mysql_csatlakozas);

header('Content-type: image/png');imagepng($kep);
?>