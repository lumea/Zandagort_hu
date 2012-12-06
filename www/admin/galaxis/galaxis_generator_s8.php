<?
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);$font_cim='arial.ttf';

$meret=800;$felmeret=$meret/2;$kep_zoom=5;
$kep=imagecreatetruecolor($meret,$meret);
$feher=imagecolorallocate($kep,255,255,255);
$sarga=imagecolorallocate($kep,255,255,200);
$piros=imagecolorallocate($kep,255,0,0);
$kek=imagecolorallocate($kep,100,160,255);
$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);
$regio_szinek[1]=imagecolorallocate($kep,255,20,40);
$regio_szinek[2]=imagecolorallocate($kep,247,91,51);
$regio_szinek[3]=imagecolorallocate($kep,255,180,0);
$regio_szinek[4]=imagecolorallocate($kep,255,255,0);
$regio_szinek[5]=imagecolorallocate($kep,0,255,0);
$regio_szinek[6]=imagecolorallocate($kep,0,255,200);
$regio_szinek[7]=imagecolorallocate($kep,20,160,255);
$regio_szinek[8]=imagecolorallocate($kep,40,80,255);
$regio_szinek[9]=imagecolorallocate($kep,160,30,255);
$regio_szinek[10]=imagecolorallocate($kep,255,64,128);
$regio_szinek[11]=imagecolorallocate($kep,255,128,192);
$regio_szinek[12]=imagecolorallocate($kep,255,255,255);

//rajz racs
$hatar=80000;$skala=200;
for($x=-$hatar;$x<=$hatar;$x+=10000) {
	imageline($kep,round($felmeret+$x/$skala),round($felmeret-$hatar/$skala),round($felmeret+$x/$skala),round($felmeret+$hatar/$skala),($x==-2*$eltolas_x)?$v_zold:$zold);
	imageline($kep,round($felmeret-$hatar/$skala),round($felmeret+$x/$skala),round($felmeret+$hatar/$skala),round($felmeret+$x/$skala),($x==-2*$eltolas_y)?$v_zold:$zold);
	imagettftext($kep,8,90,round($felmeret+$x/$skala+4),round($felmeret-$hatar/$skala-5),$zold,$font_cim,str_pad($x/2+$eltolas_x,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,0,round($felmeret-$hatar/$skala-35),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad($x/2+$eltolas_y,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,90,round($felmeret+$x/$skala+4),round($felmeret+$hatar/$skala+38),$zold,$font_cim,str_pad($x/2+$eltolas_x,6,' ',STR_PAD_LEFT));
	imagettftext($kep,8,0,round($felmeret+$hatar/$skala+5),round($felmeret+$x/$skala+4),$zold,$font_cim,str_pad($x/2+$eltolas_y,6,' ',STR_PAD_LEFT));
}

$bolygok_szama=0;
$reg_bolygok_szama=0;
$max_hexa_tav=360;
$hexa_kep_zoom=1/200;

$zoom_x=round(125*sqrt(3));
$zoom_y=125*2;

function put_bolygo($hx,$hy,$regio,$meret,$zona) {
	global $suruseg,$bolygok,$bolygok_szama,$reg_bolygok_szama;
	if (!isset($bolygok[$hx][$hy])) {
		$bolygok_szama++;
		if ($zona) $reg_bolygok_szama++;
		$bolygok[$hx][$hy]=array($regio,$meret,$zona);
		$suruseg[$hx/40][$hy/40]++;
	}
}
function remove_bolygo($hx,$hy) {
	global $bolygok,$bolygok_szama,$reg_bolygok_szama;
	if (isset($bolygok[$hx][$hy])) {
		$bolygok_szama--;
		if ($bolygok[$hx][$hy][2]) $reg_bolygok_szama--;
		unset($bolygok[$hx][$hy]);
	}
}
function random_nagybolygo() {
	$rnd=mt_rand(0,99);
	if ($rnd<3) return 2;
	if ($rnd<26) return 4;
	if ($rnd<59) return 6;
	if ($rnd<90) return 8;
	return 10;
}


//mag
$cx=-50;$cy=100;$rx=190;$ry=190;
for($y=$cy-$ry;$y<=$cy+$ry;$y++) for($x=$cx-$rx;$x<=$cx+$rx;$x++) {
	$vx=$x-$cx;$vy=$y-$cy;
	$r2=pow($vx/$rx,2)+pow($vy/$ry,2);
	$irany=atan2($vy,$vx)/M_PI*180;
	if ($r2>=0.1 and $r2<=1) {
		if ($r2>=0.4 and $r2<=0.8) $prob=30;
		if ($r2<0.4) $prob=30*($r2-0.1)/0.3;
		if ($r2>0.8) $prob=30*(1-$r2)/0.2;
		if (mt_rand(0,999)<$prob) {
			$hx=round($x/$zoom_x*100);
			$hy=round($y/$zoom_y*100);
			$regio=1;
			if ($irany<-80) $regio=2;
			if ($irany>40) $regio=3;
			if ($irany>160) $regio=2;
			put_bolygo($hx,$hy,$regio,2,1);
		}
	}
}

//karok
$a=0.09;$b=0.4;
$r0a=220;$r1a=550;
$sig=1;
$elf=0;
$cx=-50;$cy=100;
$z=1;
$min_kissugar=0;$max_kissugar=40;
$also_kissugar=10;$felso_kissugar=30;$kozep_kissugar=20;
for($kar=1;$kar<=4;$kar++) {
	$limit[$kar]=0;
	$kissugar[$kar]=mt_rand($min_kissugar,$max_kissugar);
}
for($r=$r0a;$r<=$r1a;$r+=1) {
	$t=$sig*sqrt(pow(log($r/$a)/$b,2));
	for($kar=1;$kar<=4;$kar++) if ($kar!=3 or $r<=430) if ($kar!=1 or $r<=500) if ($kar!=4 or $r<=500) {
		$eltolas=($elf+$kar*90)/180*M_PI;
		$x=round($cx+$r*cos($t+$eltolas)/$z);
		$y=round($cy-$r*sin($t+$eltolas)/$z);
		//
		$kissugar[$kar]+=mt_rand(0,10)-5;
		if ($kissugar[$kar]>$max_kissugar) $kissugar[$kar]=$max_kissugar;
		if ($kissugar[$kar]<$min_kissugar) $kissugar[$kar]=$min_kissugar;
		if ($kissugar[$kar]>$felso_kissugar) $limit[$kar]++;
		if ($kissugar[$kar]<$also_kissugar) $limit[$kar]++;
		if ($limit[$kar]>30) {
			$limit[$kar]=0;
			if ($kissugar[$kar]>$felso_kissugar) $kissugar[$kar]+=mt_rand($also_kissugar,$kozep_kissugar);
			else $kissugar[$kar]+=mt_rand($kozep_kissugar,$felso_kissugar);
		}
		for($j=-$kissugar[$kar];$j<=$kissugar[$kar];$j++) for($i=-$kissugar[$kar];$i<=$kissugar[$kar];$i++) if ($i*$i+$j*$j<=$kissugar[$kar]*$kissugar[$kar]) if (mt_rand(0,999)<3) {
			$hx=round(($x+$i)/$zoom_x*100);
			$hy=round(($y+$j)/$zoom_y*100);
			$regio=3+$kar;//4-7
			put_bolygo($hx,$hy,$regio,random_nagybolygo(),0);
		}
	}
}




//mag
$cx=180;$cy=-280;$rx=100;$ry=100;
for($y=$cy-$ry;$y<=$cy+$ry;$y++) for($x=$cx-$rx;$x<=$cx+$rx;$x++) {
	$vx=$x-$cx;$vy=$y-$cy;
	$r2=pow($vx/$rx,2)+pow($vy/$ry,2);
	$irany=atan2($vy,$vx)/M_PI*180;
	if ($r2>=0 and $r2<=1) {
		if ($r2>=0.1 and $r2<=0.8) $prob=40;
		if ($r2<0.1) $prob=40*$r2/0.1;
		if ($r2>0.7) $prob=40*(1-$r2)/0.3;
		if (mt_rand(0,999)<$prob) {
			$hx=round($x/$zoom_x*100);
			$hy=round($y/$zoom_y*100);
			$regio=8;
			if ($irany<-30) $regio=9;
			if ($irany>150) $regio=9;
			put_bolygo($hx,$hy,$regio,2,1);
		}
	}
}

//karok
$a=0.09;$b=0.3;
$r0a=120;$r1a=350;
$sig=-1;
$elf=-80;
$cx=180;$cy=-280;
$z=1;
$min_kissugar=10;$max_kissugar=40;
for($kar=1;$kar<=2;$kar++) $kissugar[$kar]=mt_rand($min_kissugar,$max_kissugar);
for($r=$r0a;$r<=$r1a;$r+=1) {
	$t=$sig*sqrt(pow(log($r/$a)/$b,2));
	for($kar=1;$kar<=2;$kar++) if ($kar!=1 or $r<=250) {
		$eltolas=($elf+$kar*110)/180*M_PI;
		$x=round($cx+$r*cos($t+$eltolas)/$z);
		$y=round($cy-$r*sin($t+$eltolas)/$z);
		//
		$kissugar[$kar]+=mt_rand(0,10)-5;
		if ($kissugar[$kar]>$max_kissugar) $kissugar[$kar]=$max_kissugar;
		if ($kissugar[$kar]<$min_kissugar) $kissugar[$kar]=$min_kissugar;
		for($j=-$kissugar[$kar];$j<=$kissugar[$kar];$j++) for($i=-$kissugar[$kar];$i<=$kissugar[$kar];$i++) if ($i*$i+$j*$j<=$kissugar[$kar]*$kissugar[$kar]) if (mt_rand(0,999)<3) {
			$hx=round(($x+$i)/$zoom_x*100);
			$hy=round(($y+$j)/$zoom_y*100);
			$regio=9+$kar;//10-11
			put_bolygo($hx,$hy,$regio,random_nagybolygo(),0);
		}
	}
}




//antihalo
$maxtav=50;
$r_cluster=10;
for($cl=0;$cl<2000;$cl++) {
	$x=mt_rand(-700,700);
	$y=mt_rand(-700,700);
	$hx=round($x/$zoom_x*100);
	$hy=round($y/$zoom_y*100);
	if ($suruseg[$hx/40][$hy/40]>0) {
		$regio=12;
		$kozeli_bolygo=0;$tav=-1;
		for($by=-$maxtav;$by<=$maxtav;$by++) for($bx=-$maxtav;$bx<=$maxtav;$bx++) if (isset($bolygok[$hx+$bx][$hy+$by])) {
			if ($tav<0 or $tav>$bx*$bx+$by*$by) {
				$tav=$bx*$bx+$by*$by;
				$kozeli_bolygo=$bolygok[$hx+$bx][$hy+$by][0];
			}
		}
		if ($kozeli_bolygo>0) $regio=$kozeli_bolygo;
		//
		if ($regio>3 and $regio!=8 and $regio!=9) {
			$r_cluster=mt_rand(2,8);
			for($j=-$r_cluster;$j<=$r_cluster;$j++) for($i=-$r_cluster;$i<=$r_cluster;$i++) if ($i*$i+$j*$j<=$r_cluster*$r_cluster) {
				$hx=round(($x+$i)/$zoom_x*100);
				$hy=round(($y+$j)/$zoom_y*100);
				remove_bolygo($hx,$hy);
			}
		}
		/*remove_bolygo($hx,$hy);
		remove_bolygo($hx-1,$hy);
		remove_bolygo($hx+1,$hy);
		remove_bolygo($hx,$hy-1);
		remove_bolygo($hx,$hy+1);*/
	}
}

//halo
$maxtav=50;
$r_cluster=20;
for($cl=0;$cl<3000;$cl++) {
	$x=mt_rand(-700,700);
	$y=mt_rand(-700,700);
	$hx=round($x/$zoom_x*100);
	$hy=round($y/$zoom_y*100);
	if ($suruseg[$hx/40][$hy/40]>0) {
		$regio=12;
		$kozeli_bolygo=0;$tav=-1;
		for($by=-$maxtav;$by<=$maxtav;$by++) for($bx=-$maxtav;$bx<=$maxtav;$bx++) if (isset($bolygok[$hx+$bx][$hy+$by])) {
			if ($tav<0 or $tav>$bx*$bx+$by*$by) {
				$tav=$bx*$bx+$by*$by;
				$kozeli_bolygo=$bolygok[$hx+$bx][$hy+$by][0];
			}
		}
		if ($kozeli_bolygo>0) $regio=$kozeli_bolygo;
		if (mt_rand(0,99)<5 and $regio>3 and $regio!=8 and $regio!=9) {
			for($j=-$r_cluster;$j<=$r_cluster;$j++) for($i=-$r_cluster;$i<=$r_cluster;$i++) if ($i*$i+$j*$j<=$r_cluster*$r_cluster) if (mt_rand(0,999)<30) {
				$hx=round(($x+$i)/$zoom_x*100);
				$hy=round(($y+$j)/$zoom_y*100);
				put_bolygo($hx,$hy,$regio,random_nagybolygo(),0);
			}
		} else {
			put_bolygo($hx,$hy,$regio,2,0);
		}
	}
}


//bolygok kirajzolasa
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) if (isset($bolygok[$hx][$hy])) {
	$reg_b[$bolygok[$hx][$hy][0]]++;
	$meret_b[$bolygok[$hx][$hy][1]]++;
	$xx=$hx*round(125*sqrt(3));
	$yy=$hy*125*2-(($hx%2)?0:125);
	imagefilledellipse($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy,3,3,$regio_szinek[$bolygok[$hx][$hy][0]]);
}
imagettftext($kep,8,0,10,30,$feher,$font_cim,$bolygok_szama);
imagettftext($kep,8,0,10,45,$feher,$font_cim,$reg_bolygok_szama.' (R)');

for($regio=1;$regio<=12;$regio++) {
	imagettftext($kep,8,0,10,45+15*$regio,$feher,$font_cim,$regio);
	imagettftext($kep,8,0,30,45+15*$regio,$feher,$font_cim,$reg_b[$regio]);
}

for($meret=1;$meret<=5;$meret++) {
	imagettftext($kep,8,0,10,245+15*$meret,$feher,$font_cim,2*$meret);
	imagettftext($kep,8,0,30,245+15*$meret,$feher,$font_cim,$meret_b[2*$meret]);
}



header('Content-type: image/png');imagepng($kep);exit;


$szerver_prefix='';
$szerver_ip='';

/*
x,y
,hexa_x,hexa_y
,galaktikus_regio,terulet,alapbol_regisztralhato
,osztaly,hold
*/
$mysql_username = '';
$mysql_password = '';
$mysql_csatlakozas=mysql_connect($szerver_ip,$mysql_username,$mysql_password);
$result=mysql_select_db('mmog');
mysql_query('set names "utf8"');
mysql_query('truncate bolygok_'.$szerver_prefix);
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) if (isset($bolygok[$hx][$hy])) {
	mysql_query('insert into bolygok_'.$szerver_prefix.' (hexa_x,hexa_y,galaktikus_regio,terulet,alapbol_regisztralhato) values('.$hx.','.$hy.','.$bolygok[$hx][$hy][0].','.$bolygok[$hx][$hy][1].'*1000000,'.$bolygok[$hx][$hy][2].')');
}

//tavolsagok
mysql_query('update bolygok_'.$szerver_prefix.'
set x=hexa_x*round(125*sqrt(3))+rand()*100-50,
y=hexa_y*125*2-if(hexa_x%2=0,0,125)+rand()*60-30');

//paros koorinatak
mysql_query('update bolygok_'.$szerver_prefix.' set x=round(x/2)*2,y=round(y/2)*2');

//osztaly
mysql_query('update bolygok_'.$szerver_prefix.' set osztaly=1+floor(rand()*5)');
//hold
mysql_query('update bolygok_'.$szerver_prefix.' set hold=case osztaly
when 1 then if(rand()<1/2,1,0)
when 2 then if(rand()<1/2,1,0)
when 3 then if(rand()<2/3,1,0)
when 4 then if(rand()<1/3,1,0)
when 5 then 1
end');


/*
select b.alapbol_regisztralhato,b.terulet,count(1) as darab,round(count(1)/mind*100) as szazalek
from bolygok b, (select count(1) as mind from bolygok) t
group by b.alapbol_regisztralhato,b.terulet

select b.alapbol_regisztralhato,b.terulet,count(1) as darab,round(count(1)/mind*100) as szazalek,sum(tulaj>0) as foglalt,round(sum(tulaj>0)/count(1)*100) as foglalt_szazalek
from bolygok b, (select count(1) as mind from bolygok) t
group by b.alapbol_regisztralhato,b.terulet
*/

header('Content-type: image/png');imagepng($kep);
?>