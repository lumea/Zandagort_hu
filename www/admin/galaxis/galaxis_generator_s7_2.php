<?
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);$font_cim='arial.ttf';


$szerver_prefix='';
$szerver_ip='';

$meret=800;$felmeret=$meret/2;$kep_zoom=5;$max_tavolsag=80;
$kep=imagecreatefrompng('s7_gala_v4e.png');
$feher=imagecolorallocate($kep,255,255,255);
$sarga=imagecolorallocate($kep,255,255,200);
$piros=imagecolorallocate($kep,255,0,0);
$kek=imagecolorallocate($kep,100,160,255);
$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);


//bolygok
$bolygok_szama=0;
$max_hexa_tav=360;
$hexa_kep_zoom=1/200;
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) $bolygok[$hx][$hy]=0;
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) {
	$xx=$hx*round(125*sqrt(3));$yy=$hy*125*2-(($hx%2)?0:125);
	$szin=imagecolorsforindex($kep,imagecolorat($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy));
	$szin1=imagecolorsforindex($kep,imagecolorat($kep,$felmeret+$hexa_kep_zoom*$xx-1,$felmeret+$hexa_kep_zoom*$yy));
	$szin2=imagecolorsforindex($kep,imagecolorat($kep,$felmeret+$hexa_kep_zoom*$xx+1,$felmeret+$hexa_kep_zoom*$yy));
	$szin3=imagecolorsforindex($kep,imagecolorat($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy-1));
	$szin4=imagecolorsforindex($kep,imagecolorat($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy+1));
	if ($szin1['red']>=255)
	if ($szin2['red']>=255)
	if ($szin3['red']>=255)
	if ($szin4['red']>=255)
	if ($szin['red']>=255) {
		if (mt_rand(0,100)<80) {
			$bolygok[$hx][$hy]=1;
			$bolygok_szama++;
		}
	}
}



/*
$mysql_username = '';
$mysql_password = '';
$mysql_csatlakozas=mysql_connect($szerver_ip,$mysql_username,$mysql_password);
$result=mysql_select_db('mmog');
mysql_query('set names "utf8"');
mysql_query('truncate bolygok_'.$szerver_prefix);
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) if ($bolygok[$hx][$hy]) {
	mysql_query('insert into bolygok_'.$szerver_prefix.' (hexa_x,hexa_y) values('.$hx.','.$hy.')');
}

//tavolsagok
mysql_query('update bolygok_'.$szerver_prefix.'
set x=hexa_x*round(125*sqrt(3))+rand()*100-50,
y=hexa_y*125*2-if(hexa_x%2=0,0,125)+rand()*60-30');

//paros koorinatak
mysql_query('update bolygok_'.$szerver_prefix.' set x=round(x/2)*2,y=round(y/2)*2');


echo $bolygok_szama;exit;
*/





/*

update bolygok_s7
set terulet=if(
pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000<950
,

if(rand()*100<

((pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000)/950-0.5)*100

,2
,if(rand()*100<23,4
,if(rand()*100<47,6
,if(rand()*100<75,8
,10))))*1000000

,if(
pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000>1350
,

if(rand()*100<

(1.5-(pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000)/1350)*10

,2
,if(rand()*100<23,4
,if(rand()*100<47,6
,if(rand()*100<75,8
,10))))*1000000

,2000000
)
)
,


alapbol_regisztralhato=if(
pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000<950
,0
,if(
pow(cos(-pi()/3)*(x-24000)-sin(-pi()/3)*(y+17000),2)/2000000+pow(sin(-pi()/3)*(x-24000)+cos(-pi()/3)*(y+17000),2)/1150000>1350
,0
,

#1
if(rand()*100<
pow(((pow(cos(-pi()/8)*(x-24000)-sin(-pi()/8)*(y+17000),2)/2000000+pow(sin(-pi()/8)*(x-24000)+cos(-pi()/8)*(y+17000),2)/1150000)-950)/105,2)
+pow((1350-(pow(cos(-pi()/8)*(x-24000)-sin(-pi()/8)*(y+17000),2)/2000000+pow(sin(-pi()/8)*(x-24000)+cos(-pi()/8)*(y+17000),2)/1150000))/105,2)
,0,1)

#if(rand()*100<67,1,0)
#if(rand()*100<70,1,0)


)
);



update bolygok_s7 set osztaly=1+floor(rand()*5);
update bolygok_s7 set hold=case osztaly
when 1 then if(rand()<1/2,1,0)
when 2 then if(rand()<1/2,1,0)
when 3 then if(rand()<2/3,1,0)
when 4 then if(rand()<1/3,1,0)
when 5 then 1
end;

*/











/*
select b.alapbol_regisztralhato,b.terulet,count(1) as darab,round(count(1)/mind*100) as szazalek
from bolygok b, (select count(1) as mind from bolygok) t
group by b.alapbol_regisztralhato,b.terulet

select b.alapbol_regisztralhato,b.terulet,count(1) as darab,round(count(1)/mind*100) as szazalek,sum(tulaj>0) as foglalt,round(sum(tulaj>0)/count(1)*100) as foglalt_szazalek
from bolygok b, (select count(1) as mind from bolygok) t
group by b.alapbol_regisztralhato,b.terulet
*/







$zoom=1;
$meret=1120;
$skala=150/$zoom;

$felmeret=round($meret/2);
$kep=imagecreatetruecolor($meret,$meret);

$zold=imagecolorallocate($kep,0,100,0);
$v_zold=imagecolorallocate($kep,0,200,0);

$nagyon_sotet_szurke=imagecolorallocate($kep,30,30,30);
$sotet_szurke=imagecolorallocate($kep,50,50,50);
$feher=imagecolorallocate($kep,255,255,255);
$szurke=imagecolorallocate($kep,160,160,160);
$piros=imagecolorallocate($kep,255,0,0);
$sotet_piros=imagecolorallocate($kep,160,0,0);
$bolygo_szin[1]=imagecolorallocate($kep,83,154,148);
$bolygo_szin[2]=imagecolorallocate($kep,236,164,62);
$bolygo_szin[3]=imagecolorallocate($kep,196,199,110);
$bolygo_szin[4]=imagecolorallocate($kep,70,97,56);
$bolygo_szin[5]=imagecolorallocate($kep,225,234,241);


$reg_bolygoszam=0;
$bolygoszam=0;
$meret=5;$phi=-M_PI/3;
for($hy=-$max_hexa_tav;$hy<=$max_hexa_tav;$hy++) for($hx=-$max_hexa_tav;$hx<=$max_hexa_tav;$hx++) if ($bolygok[$hx][$hy]) {
	$szin=$bolygo_szin[mt_rand(1,5)];
	$xx=$hx*round(125*sqrt(3));
	$yy=$hy*125*2-(($hx%2)?0:125);
	$ii=cos($phi)*($xx-24000)-sin($phi)*($yy+17000);
	$jj=sin($phi)*($xx-24000)+cos($phi)*($yy+17000);
	$e=$ii*$ii/2000000+$jj*$jj/1150000;
	if ($e>=950) if ($e<=1350) {$szin=$piros;$reg_bolygoszam++;}
	$bolygoszam++;
	//imagefilledellipse($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy,3,3,$piros);
	//imagesetpixel($kep,$felmeret+$hexa_kep_zoom*$xx,$felmeret+$hexa_kep_zoom*$yy,$piros);
	imagefilledellipse($kep,round($felmeret+($xx-2*$eltolas_x)/$skala),round($felmeret+($yy-2*$eltolas_y)/$skala),$meret,$meret,$szin);
}
imagettftext($kep,8,0,10,45,$feher,$font_cim,$bolygoszam);
imagettftext($kep,8,0,10,65,$feher,$font_cim,$reg_bolygoszam);

header('Content-type: image/png');imagepng($kep);exit;
?>