<?
include('csatlak.php');include('ujkuki.php');
include_once('langjs_s.php');

header('Cache-Control: no-cache');header('Expires: -1');//az állandó fejlesztgetések miatt van így beállítva; lehetne helyette az index_belso.php-ban ?v=1,2,...-t is megadni belinkeléskor
header('Content-type: text/javascript; charset=utf-8');

//kvázi titkosítás, ha nem akarjuk, hogy valaki el tudja érni az unpack-elt kódot
//bár a pack lényege fõleg inkább a tömörség
//ennek a szkriptnek pedig a leglényege, hogy a nyelvfüggõ részeket a lang_js.php ($langjs) alapján behelyettesítse
$konyvtar='jskod_vustygbkforjfdjp';

//alapértelmezett az éles verzió
$verzio=1;

//admin-nak fejlesztõi verziót betenni
if ($ismert) if ($uid==1) $verzio=2;

//ez esetben másik fájlokkal dolgozunk
if ($verzio==2) $konyvtar.='_v2';

//ezek a js fájlok csomagolódnak egybe
$lista=array('jskod.js','jskod_terkep.js','jskod_akciok.js','jskod_aux.js');
$cel_lista=array('jskod'.$lang__lang.'.js','jskod_terkep'.$lang__lang.'.js','jskod_akciok'.$lang__lang.'.js','jskod_aux'.$lang__lang.'.js');

clearstatcache();
$packed='';
for($i=0;$i<count($lista);$i++) {
	$src=$konyvtar.'/'.$lista[$i];
	$dest=$konyvtar.'/'.$cel_lista[$i].'_packed';
	if (!file_exists($dest) || filemtime($src)>filemtime($dest)) {//ha változott a js fájl, újragenerálni
		if (isset($langjs[$lang_lang][$lista[$i]])) $script=strtr(file_get_contents($src),$langjs[$lang_lang][$lista[$i]]);
		else $script=file_get_contents($src);
		if ($verzio==2) {//fejlesztõi verzió: egyrészt nincs pack-elve, így könnyebb debug-olni, másrészt eltérhet az élestõl, így élesítés elõtt mindig itt lehet tesztelni
			$x=$script;
		} else {//éles verzió
			require_once $konyvtar.'/class.JavaScriptPacker.php';
			$packer=new JavaScriptPacker($script,'Normal',true,false);
			$x=$packer->pack();
		}
		file_put_contents($dest,$x);
		$packed.=$x;
	} else {//ha nem, akkor mehet a régi
		$packed.=file_get_contents($dest);
	}
}


echo $packed;

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);
?>