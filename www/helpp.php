<?
include('csatlak.php');

$er=mysql_query('select * from leirasok where domen='.((int)$_REQUEST['domen']).' and id='.((int)$_REQUEST['id']));
$aux=mysql_fetch_array($er);
header('Location: '.$zanda_wiki_url[$lang_lang].str_replace('%2F','/',urlencode(strtr($aux['cim'.$lang__lang],' ','_'))));
