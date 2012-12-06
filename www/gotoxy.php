<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

if ($lang_lang=='en') {
/********** ENGLISH ************/
if (substr($_REQUEST['q'],0,1)=='#') {
	$er=mysql_query('select * from bolygok where id='.((int)substr($_REQUEST['q'],1))) or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er=mysql_query('select * from bolygok where concat(kulso_nev," (",if(y>0,concat("S ",round(y/2)),if(y<0,concat("N ",round(-y/2)),0)),", ",if(x>0,concat("E ",round(x/2)),if(x<0,concat("W ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['q']).'" and letezik=1');
}
$aux=mysql_fetch_array($er);
if ($aux) {
	$x=$aux['x'];
	$y=$aux['y'];
} else {
	$koord=explode(',',$_REQUEST['q']);
	$y_str=trim($koord[0]);
	$x_str=trim($koord[1]);
	$k=mb_substr($y_str,0,1,'UTF-8');$y_num=(int)strtr(trim(mb_substr($y_str,1,1000,'UTF-8')),array(' '=>''));
	if ($k=='N' || $k=='n') $y=-2*$y_num;
	elseif ($k=='S' || $k=='s') $y=2*$y_num;
	else $y=2*((int)$y_str);
	$k=mb_substr($x_str,0,1,'UTF-8');$x_num=(int)strtr(trim(mb_substr($x_str,1,1000,'UTF-8')),array(' '=>''));
	if ($k=='W' || $k=='w') $x=-2*$x_num;
	elseif ($k=='E' || $k=='e') $x=2*$x_num;
	else $x=2*((int)$x_str);
}
/********** ENGLISH ************/
} else {
/********** MAGYAR ************/
if (substr($_REQUEST['q'],0,1)=='#') {
	$er=mysql_query('select * from bolygok where id='.((int)substr($_REQUEST['q'],1))) or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er=mysql_query('select * from bolygok where concat(kulso_nev," (",if(y>0,concat("D ",round(y/2)),if(y<0,concat("É ",round(-y/2)),0)),", ",if(x>0,concat("K ",round(x/2)),if(x<0,concat("Ny ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['q']).'" and letezik=1');
}
$aux=mysql_fetch_array($er);
if ($aux) {
	$x=$aux['x'];
	$y=$aux['y'];
} else {
	$koord=explode(',',$_REQUEST['q']);
	$y_str=trim($koord[0]);
	$x_str=trim($koord[1]);
	$k=mb_substr($y_str,0,1,'UTF-8');$y_num=(int)strtr(trim(mb_substr($y_str,1,1000,'UTF-8')),array(' '=>''));
	if ($k=='É' || $k=='E' || $k=='é' || $k=='e') $y=-2*$y_num;
	elseif ($k=='D' || $k=='d') $y=2*$y_num;
	else $y=2*((int)$y_str);
	$k=mb_substr($x_str,0,1,'UTF-8');$x_num=(int)strtr(trim(mb_substr($x_str,1,1000,'UTF-8')),array(' '=>''));
	if ($k=='N' || $k=='n') $x=-2*(int)strtr(trim(mb_substr($x_str,2,1000,'UTF-8')),array(' '=>''));
	elseif ($k=='K' || $k=='k') $x=2*$x_num;
	else $x=2*((int)$x_str);
}
/********** MAGYAR ************/
}


?>
/*{"x":<?=(int)$x;
?>,"y":<?=(int)$y;
?>}*/
<?

?>
<? mysql_close($mysql_csatlakozas);?>