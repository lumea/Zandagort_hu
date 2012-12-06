<?
set_time_limit(0);//levelek miatt
include('csatlak.php');
include('ujkuki.php');

if ($ismert) if ($uid==1) {

if (isset($_REQUEST['ujert'])) {
	$cim=$_REQUEST['cim'];
	$url=$_REQUEST['url'];
	$url_forum=$_REQUEST['url_forum'];
	if (strpos($url,'.blog.hu')!==false) {//magyar
		$targy='Új blogbejegyzés: '.$cim;
		$uzenet='<a target="_blank" href="'.$url.'">'.$url.'</a>';
		$uzenet.="\n\n";
		$uzenet.='Ha a fórumon szeretnél hozzászólni, itt teheted meg: <a target="_blank" href="'.$url_forum.'">'.$url_forum.'</a>';
		$er=mysql_query('select * from userek where nyelv="hu"');
		//$er=mysql_query('select * from userek where id=1');
		while($aux=mysql_fetch_array($er)) {
			rendszeruzenet_html($aux['id'],$targy,$uzenet);
		}
	} else {//angol
		$targy_en='New blog entry: '.$cim;
		$uzenet_en='<a target="_blank" href="'.$url.'">'.$url.'</a>';
		$uzenet_en.="\n\n";
		$uzenet_en.='For discussion, check the forum: <a target="_blank" href="'.$url_forum.'">'.$url_forum.'</a>';
		$er=mysql_query('select * from userek where nyelv="en"');
		//$er=mysql_query('select * from userek where id=1');
		while($aux=mysql_fetch_array($er)) {
			rendszeruzenet_html($aux['id'],$targy_en,$uzenet_en,$targy_en,$uzenet_en);
		}
	}
	header('Location: blog_ertesites.php');exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort blog értesítés</title>
</head>
<body>
<form action="blog_ertesites.php" method="post">
<p>cím: <input type="text" name="cim" /></p>
<p>blog url: <input type="text" name="url" /></p>
<p>fórum url: <input type="text" name="url_forum" /></p>
<p><input type="submit" name="ujert" /></p>
</form>
</body>
</html>
<?
}
mysql_close($mysql_csatlakozas);
?>