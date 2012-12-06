<?
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);$font_cim='arial.ttf';

$meret=800;$felmeret=$meret/2;$kep_zoom=5;$max_tavolsag=100;
$kep=imagecreatetruecolor($meret,$meret);
$feher=imagecolorallocate($kep,255,255,255);
$sarga=imagecolorallocate($kep,255,255,200);
$piros=imagecolorallocate($kep,255,0,0);
$kek=imagecolorallocate($kep,100,160,255);
$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);
for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) $suruseg[$i][$j]=0;





/*
//szigetek
$szigetek_szama=0;
while($szigetek_szama>0) {
	$x=mt_rand(-$max_tavolsag,$max_tavolsag);
	$y=mt_rand(-$max_tavolsag,$max_tavolsag);
	if ($x*$x+$y*$y<=$max_tavolsag*$max_tavolsag) {
		$suruseg[$x][$y]=10;
		$szigetek_szama--;
	}
}

//test
$eltolas_xx=5;$eltolas_yy=5;$phi=-M_PI/8;$a=4000;$b=1000;
for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) {
	$x=cos($phi)*$i-sin($phi)*$j;
	$y=sin($phi)*$i+cos($phi)*$j;
	if ($x*$x/$a+$y*$y/$b<=1) {
		$suruseg[$i+$eltolas_xx][$j+$eltolas_yy]+=mt_rand(1,100*pow($x*$x/$a+$y*$y/$b,5));
	}
}

//belsosegek
put_galaxy(20,20,0.09,0.4,1,1,250,250,10,0,3,-1);
put_elliptic_galaxy(-24,0,-M_PI/8+M_PI/2,40,40);
put_elliptic_galaxy(-5,-12,-M_PI/8+M_PI/2,40,40);
put_elliptic_galaxy(-27,-15,-M_PI/8+M_PI/2,30,30);
put_elliptic_galaxy(-8,+7,-M_PI/8+M_PI/2,50,50);

//farok
//put_elliptic_galaxy(-45,-25,-M_PI/16+M_PI/2,300,100,100,1);
//put_elliptic_galaxy(-50,-28,-M_PI/16+M_PI/2,300,100,100,1);
put_elliptic_galaxy(-55,-33,-M_PI/16+M_PI/2,300,30,100,1);

//uszok
put_elliptic_galaxy(20,-30,M_PI/2+M_PI/6,300,100);
put_elliptic_galaxy(-20,+35,-M_PI/16,300,100);

//szaj
$eltolas_xx=62;$eltolas_yy=25;$phi=-M_PI/8+M_PI/2;$a=100;$b=100;
for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) {
	$x=cos($phi)*$i-sin($phi)*$j;$y=sin($phi)*$i+cos($phi)*$j;if ($x*$x/$a+$y*$y/$b<=1) {
		$suruseg[$i+$eltolas_xx][$j+$eltolas_yy]-=mt_rand(1,200-200*($x*$x/$a+$y*$y/$b));
		if ($suruseg[$i+$eltolas_xx][$j+$eltolas_yy]<0) $suruseg[$i+$eltolas_xx][$j+$eltolas_yy]=0;
	}
}

//egyeb hatul
put_elliptic_galaxy(-13,-38,0,20,20,50,1);
put_elliptic_galaxy(-35,-35,0,20,20,50,1);
put_elliptic_galaxy(-50,+10,-M_PI/4,200,15,50,1);

//egyeb elol
put_elliptic_galaxy(30,47,M_PI/10,100,10,50,1);
*/





$kep2=imagecreatefrompng('cartwheel_grey_200_v2.png');
for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) {
	$szin=imagecolorsforindex($kep2,imagecolorat($kep2,100+$i,100+$j));
	$suruseg[$i][$j]=pow($szin['red']/255,5)*10;
}


//szigetek
$szigetek_szama=100;
while($szigetek_szama>0) {
	$x=mt_rand(-$max_tavolsag,$max_tavolsag);
	$y=mt_rand(-$max_tavolsag,$max_tavolsag);
	if ($x*$x+$y*$y<=$max_tavolsag*$max_tavolsag) {
		$suruseg[$x][$y]=10;
		$szigetek_szama--;
	}
}



$regi_suruseg=$suruseg;for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) if ($regi_suruseg[$i-1][$j]+$regi_suruseg[$i+1][$j]+$regi_suruseg[$i][$j-1]+$regi_suruseg[$i][$j+1]) $suruseg[$i][$j]+=10*($regi_suruseg[$i-1][$j]+$regi_suruseg[$i+1][$j]+$regi_suruseg[$i][$j-1]+$regi_suruseg[$i][$j+1]);



$max_suruseg=0;for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) if ($suruseg[$i][$j]>$max_suruseg) $max_suruseg=$suruseg[$i][$j];

//bolygok
$bolygok_szama=0;
$max_hexa_tav=360;
$hexa_kep_zoom=1/200;
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) $bolygok[$hx][$hy]=0;
if ($max_suruseg>0) for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) {
	$xx=$hx*round(125*sqrt(3));
	$yy=$hy*125*2-(($hx%2)?0:125);
	$i=floor($hexa_kep_zoom*$xx/$kep_zoom);
	$j=floor($hexa_kep_zoom*$yy/$kep_zoom);
	if (mt_rand(0,100)<pow($suruseg[$i][$j]/$max_suruseg,0.6)*50) {
		$bolygok[$hx][$hy]=1;
		$bolygok_szama++;
	}
}


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

//rajz suruseg
//for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) if ($suruseg[$i][$j]) imagefilledrectangle($kep,$felmeret+$kep_zoom*$i,$felmeret+$kep_zoom*$j,$felmeret+$kep_zoom*($i+1)-1,$felmeret+$kep_zoom*($j+1)-1,$feher);

//rajz bolygok
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) if ($bolygok[$hx][$hy]) {
	$xx=$hx*round(125*sqrt(3));
	$yy=$hy*125*2-(($hx%2)?0:125);
	imagefilledellipse($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy,3,3,$feher);
}
imagettftext($kep,8,0,10,30,$feher,$font_cim,$bolygok_szama);

header('Content-type: image/png');imagepng($kep);












function put_galaxy($cx,$cy,$a,$b,$r0a,$r0b,$r1a,$r1b,$z,$elf,$sig=1,$tuskes=0) {
	global $suruseg;
	$tuske_doles=0.5;
	$tuske_min_tav=5;
	$tuske_max_tav=20;
	$kov_tuske=mt_rand($tuske_min_tav,$tuske_max_tav);
	for($r=$r0a;$r<=$r1a;$r+=1) if (!$tuskes || !($r>=310 && $r<=325) && !($r>=230 && $r<=250) && !($r>=140 && $r<=160) && !($r>=20 && $r<=70)) {
		$kov_tuske--;
		$t=$sig*sqrt(pow(log($r/$a)/$b,2));
		$eltolas=$elf/180*M_PI;
		$x=round($cx+$r*cos($t+$eltolas)/$z);
		$y=round($cy-$r*sin($t+$eltolas)/$z);
		$mertek=($r-$r0a)/($r1a-$r0a);
		$kiskor=50*$mertek;
		$kissugar=round(3-2*$mertek);
		for($j=-5;$j<=5;$j++) for($i=-5;$i<=5;$i++) if ($i*$i+$j*$j<=$kissugar*$kissugar) $suruseg[$x+$i][$y+$j]+=(1-0*$mertek)*10/sqrt(1+$kiskor*($i*$i+$j*$j));
		/*if ($tuskes) if ($r%20==10) {*/
		/*if ($tuskes) if ($r%5==2) if (mt_rand(0,100)<30) {*/
		if ($tuskes) if ($kov_tuske<=0) {
			$kov_tuske=mt_rand($tuske_min_tav,$tuske_max_tav);
			$tuske_hossz=round(6-6*pow($mertek,5))*mt_rand(100,200)/100;
			$rp=$r+0.1;
			$tp=$sig*sqrt(pow(log($rp/$a)/$b,2));
			$vx=($cx+$rp*cos($tp+$eltolas)/$z)-($cx+$r*cos($t+$eltolas)/$z);
			$vy=($cy-$rp*sin($tp+$eltolas)/$z)-($cy-$r*sin($t+$eltolas)/$z);
			$vr=sqrt($vx*$vx+$vy*$vy);$vx/=$vr;$vy/=$vr;
			for($i=-$tuske_hossz;$i<=$tuske_hossz;$i++) $suruseg[round($x-$i*$vy+$tuske_doles*(abs($i)*$vx))][round($y+$i*$vx+$tuske_doles*(abs($i)*$vy))]+=10;
		}
	}
	$kov_tuske=mt_rand($tuske_min_tav,$tuske_max_tav);
	for($r=$r0b;$r<=$r1b;$r+=1) if (!$tuskes || !($r>=230 && $r<=250) && !($r>=140 && $r<=150) && !($r>=50 && $r<=60)) {
		$kov_tuske--;
		$t=$sig*sqrt(pow(log($r/$a)/$b,2));
		$eltolas=M_PI+$elf/180*M_PI;
		$x=round($cx+$r*cos($t+$eltolas)/$z);
		$y=round($cy-$r*sin($t+$eltolas)/$z);
		$mertek=($r-$r0a)/($r1a-$r0a);
		$kiskor=50*$mertek;
		$kissugar=round(3-2*$mertek);
		for($j=-5;$j<=5;$j++) for($i=-5;$i<=5;$i++) if ($i*$i+$j*$j<=$kissugar*$kissugar) $suruseg[$x+$i][$y+$j]+=(1-0*$mertek)*10/sqrt(1+$kiskor*($i*$i+$j*$j));
		/*if ($tuskes) if ($r%20==5) {*/
		/*if ($tuskes) if ($r%5==3) if (mt_rand(0,100)<30) {*/
		if ($tuskes) if ($kov_tuske<=0) {
			$kov_tuske=mt_rand($tuske_min_tav,$tuske_max_tav);
			$tuske_hossz=round(6-6*pow($mertek,5))*mt_rand(100,200)/100;
			$rp=$r+0.1;
			$tp=$sig*sqrt(pow(log($rp/$a)/$b,2));
			$vx=($cx+$rp*cos($tp+$eltolas)/$z)-($cx+$r*cos($t+$eltolas)/$z);
			$vy=($cy-$rp*sin($tp+$eltolas)/$z)-($cy-$r*sin($t+$eltolas)/$z);
			$vr=sqrt($vx*$vx+$vy*$vy);$vx/=$vr;$vy/=$vr;
			for($i=-$tuske_hossz;$i<=$tuske_hossz;$i++) $suruseg[round($x-$i*$vy+$tuske_doles*(abs($i)*$vx))][round($y+$i*$vx+$tuske_doles*(abs($i)*$vy))]+=10;
		}
	}
}

function put_elliptic_galaxy($eltolas_xx,$eltolas_yy,$phi,$a,$b,$maxsur=100,$inv=0) {
	global $max_tavolsag,$suruseg;
	for($j=-$max_tavolsag;$j<=$max_tavolsag;$j++) for($i=-$max_tavolsag;$i<=$max_tavolsag;$i++) {
		$x=cos($phi)*$i-sin($phi)*$j;$y=sin($phi)*$i+cos($phi)*$j;if ($x*$x/$a+$y*$y/$b<=1) $suruseg[$i+$eltolas_xx][$j+$eltolas_yy]+=mt_rand(1,$inv*$maxsur+($inv==0?1:-1)*$maxsur*($x*$x/$a+$y*$y/$b));
	}
}

?>