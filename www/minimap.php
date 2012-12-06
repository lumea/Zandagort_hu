<?
include('csatlak.php');$font_cim='img/arial.ttf';
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: image/png');

$q_id=0;$van_q=0;
if (premium_szint()>0) {
	$er=mysql_query('select id from szovetsegek where nev="'.sanitstr($_REQUEST['q']).'"') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if ($aux[0]>0) {
		$q_tipus='sz';
		$q_id=$aux[0];
		$van_q=1;
	} else {
		$er=mysql_query('select id from userek where nev="'.sanitstr($_REQUEST['q']).'" and (karrier!=3 or speci!=3)');
		$aux=mysql_fetch_array($er);
		if ($aux[0]>0) {
			$q_tipus='u';
			$q_id=$aux[0];
			$van_q=1;
		}
	}
}

$meret=600;$skala=280;$zoom=1;
$felmeret_x=round($meret/2)+58;
$felmeret=round($meret/2)-10;
$kep=imagecreatetruecolor($meret+116,$meret);
$fekete=imagecolorallocate($kep,0,0,0);
imagefill($kep,0,0,$fekete);

$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);
$hatar=80000/$zoom;
for($x=-$hatar;$x<=$hatar;$x+=10000) {
	imageline($kep,round($felmeret_x+$x/$skala),round($felmeret-$hatar/$skala),round($felmeret_x+$x/$skala),round($felmeret+$hatar/$skala),($x==-2*$eltolas_x)?$v_zold:$zold);
	imageline($kep,round($felmeret_x-$hatar/$skala),round($felmeret+$x/$skala),round($felmeret_x+$hatar/$skala),round($felmeret+$x/$skala),($x==-2*$eltolas_y)?$v_zold:$zold);
	imagettftext($kep,7,0,round($felmeret_x-$hatar/$skala-25),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad(($x/2+$eltolas_y)/1000,3,' ',STR_PAD_LEFT));
	imagettftext($kep,7,90,round($felmeret_x+$x/$skala+4),round($felmeret+$hatar/$skala+20),$zold,$font_cim,str_pad(($x/2+$eltolas_x)/1000,3,' ',STR_PAD_LEFT));
	imagettftext($kep,7,0,round($felmeret_x+$hatar/$skala+15),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad(($x/2+$eltolas_y)/1000,3,' ',STR_PAD_LEFT));
}

$npc_szin[0]=imagecolorallocate($kep,200,200,200);
$semleges_szin[0]=imagecolorallocate($kep,160,0,0);
$sajat_szin[0]=imagecolorallocate($kep,0,255,0);
$szovtars_szin[0]=imagecolorallocate($kep,100,160,255);
$hadi_szin[0]=imagecolorallocate($kep,255,0,0);
$beke_szin[0]=imagecolorallocate($kep,255,160,0);
$mnt_szin[0]=imagecolorallocate($kep,255,255,0);

$npc_szin[1]=imagecolorallocate($kep,255,255,255);
$semleges_szin[1]=imagecolorallocate($kep,160,0,0);
$sajat_szin[1]=imagecolorallocate($kep,0,255,0);
$szovtars_szin[1]=imagecolorallocate($kep,100,160,255);
$hadi_szin[1]=imagecolorallocate($kep,255,0,0);
$beke_szin[1]=imagecolorallocate($kep,255,160,0);
$mnt_szin[1]=imagecolorallocate($kep,255,255,0);

$kijelolt_szin=imagecolorallocate($kep,255,0,0);

$hadi=array();$beke=array();$mnt=array();
$er=mysql_query('select * from diplomacia_statuszok where ki='.$adataim['tulaj_szov']);
while($aux=mysql_fetch_array($er)) {
	switch($aux['mi']) {
		case 1:$hadi[]=$aux['kivel'];break;
		case 2:$beke[]=$aux['kivel'];break;
		case 3:$mnt[]=$aux['kivel'];break;
	}
}
//alap terkep
$q_sel=1-$van_q;
$er=mysql_query('select b.x,b.y
,if(u.karrier=3 and u.speci=3,0,b.tulaj) as tulaj
,if(u.karrier=3 and u.speci=3,0,b.tulaj_szov) as tulaj_szov
from bolygok b
left join userek u on u.id=b.tulaj
where b.letezik=1');
while($aux=mysql_fetch_array($er)) {
	if ($aux['tulaj']==$uid) $szin=$sajat_szin[$q_sel];
	elseif ($aux['tulaj_szov']==$adataim['tulaj_szov']) $szin=$szovtars_szin[$q_sel];
	elseif ($aux['tulaj']) {
		if (in_array($aux['tulaj_szov'],$hadi)) $szin=$hadi_szin[$q_sel];
		elseif (in_array($aux['tulaj_szov'],$beke)) $szin=$beke_szin[$q_sel];
		elseif (in_array($aux['tulaj_szov'],$mnt)) $szin=$mnt_szin[$q_sel];
		else $szin=$semleges_szin[$q_sel];
	} else $szin=$npc_szin[$q_sel];
	if ($van_q) {
		imagesetpixel($kep,round($felmeret_x+$aux[0]/$skala),round($felmeret+$aux[1]/$skala),$szin);
	} else {
		if ($aux['tulaj']==0) imagesetpixel($kep,round($felmeret_x+$aux[0]/$skala),round($felmeret+$aux[1]/$skala),$szin);
		else imagefilledellipse($kep,round($felmeret_x+$aux[0]/$skala),round($felmeret+$aux[1]/$skala),3,3,$szin);
	}
}
//a kijelolt bolygokat _utolag_ felulirni, h mindenkepp latszodjon
if ($van_q) {
	$er=mysql_query('select x,y,tulaj,tulaj_szov from bolygok where letezik=1');
	while($aux=mysql_fetch_array($er)) {
		$q_sel=0;
		if ($q_tipus=='u') {
			if ($aux['tulaj']==$q_id) $q_sel=1;
		} else {
			if ($aux['tulaj_szov']==$q_id) $q_sel=1;
		}
		if ($q_sel) {
			imagefilledellipse($kep,round($felmeret_x+$aux[0]/$skala),round($felmeret+$aux[1]/$skala),3,3,$kijelolt_szin);
		}
	}
}

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);

imagepng($kep);
?>