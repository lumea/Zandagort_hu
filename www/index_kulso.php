<script type="text/javascript">
function pozi_valaszto_fv(x) {
	document.getElementById('pozi_valaszto_div_1').style.display=(x==1?'block':'none');
	document.getElementById('pozi_valaszto_div_2').style.display=(x==2?'block':'none');
	if (x==1) document.getElementById('reg_koord').value='';
	if (x==2) {
		if (document.getElementById('reg_osztaly_valaszto_input_1').checked
		|| document.getElementById('reg_osztaly_valaszto_input_2').checked
		|| document.getElementById('reg_osztaly_valaszto_input_3').checked
		|| document.getElementById('reg_osztaly_valaszto_input_4').checked
		|| document.getElementById('reg_osztaly_valaszto_input_5').checked
		) document.getElementById('reg_osztaly_valaszto_input_0').checked=true;
	}
};
</script>
</head>
<? flush(); ?>
<body>

<div id="kulso_resz_v2">

<div style="width: 100%; text-align: center">
<a href="<?=$zanda_homepage_url[$lang_lang]?>"><img src="img/logo_szurke.gif" alt="Zandagort" /></a><br />
<h1><?=$szerver_prefix;?> <?=$lang[$lang_lang]['index_kulso.php']['szerver'];?></h1>
</div>


<? if ($_REQUEST['sikeres_akt']==1) { ?>
<p><?=$lang[$lang_lang]['index_kulso.php']['Sikeresen aktiváltad regisztrációdat.'];?></p>
<? } ?>



<?
$_REQUEST['reg_kin_keresztul_uid']=(int)$_REQUEST['reg_kin_keresztul_uid'];
if ($_REQUEST['reg_kin_keresztul_uid']>0) $_REQUEST['ref_uid']=$_REQUEST['reg_kin_keresztul_uid'];
$_REQUEST['ref_uid']=(int)$_REQUEST['ref_uid'];
if ($_REQUEST['ref_uid']>0) {
	$er=mysql_query('select * from userek where id='.$_REQUEST['ref_uid']) or hiba(__FILE__,__LINE__,mysql_error());
	$meghivo=mysql_fetch_array($er);
}
?>

<? if ($lehet_regelni==0) { ?>

<? if ($lang_lang=='hu') { ?>
<p>Regisztrálni most még nem lehet<? if (strlen($szerver_indulasa)>0) echo ' ('.substr($szerver_indulasa,0,-3).'-ig)'; ?>. <a href="<?=$facebook_link;?>">Kövess minket <img src="img/facebook_icon.gif" alt="Facebook" /> Facebook-on, hogy értesülj a szerver indulásáról!</a></p>
<p><br /></p>
<p>Belépés adminoknak:</p>
<form action="." method="post">
<p style="text-align: center">
<input type="text" class="szoveg" name="login_nev" size="10" />
<input type="password" class="szoveg" name="login_jelszo" size="10" />
</p>
<p style="text-align: center"><input type="image" src="img/login_button.png" value="" /></p>
</form>
<? } else { ?>
<p>You cannot sign up right now<? if (strlen($szerver_indulasa)>0) echo ' (until '.substr($szerver_indulasa,0,-3).')'; ?>, but if you <a href="<?=$facebook_link;?>">like us on <img src="img/facebook_icon.gif" alt="Facebook" /> Facebook you get noted when the server starts!</a></p>
<p><br /></p>
<p>Login for admins:</p>
<form action="." method="post">
<p style="text-align: center">
<input type="text" class="szoveg" name="login_nev" size="10" />
<input type="password" class="szoveg" name="login_jelszo" size="10" />
</p>
<p style="text-align: center"><input type="image" src="img/login_button.png" value="" /></p>
</form>
<? } ?>

<? } else { ?>

<? if ($_REQUEST['reg']=='siker') { ?>
<? } else { ?>

<h1 style="padding-left: 5px"><?=$lang[$lang_lang]['index_kulso.php']['BELÉPÉS'];?></h1>
<form action="." method="post">
<p style="text-align: center">
<input type="text" class="szoveg" name="login_nev" size="10" />
<input type="password" class="szoveg" name="login_jelszo" size="10" />
</p>
<p style="text-align: center"><input type="image" src="img/login_button.png" value="" /></p>
</form>


<h1 style="padding-left: 5px"><?=$lang[$lang_lang]['index_kulso.php']['REGISZTRÁCIÓ'];?></h1>

<? $er=mysql_query('select count(1) from bolygok where letezik=1 and tulaj=0 and alapbol_regisztralhato=1');
$aux=mysql_fetch_array($er);
if ($aux[0]>0) { ?>
<p class="regi"><?
echo str_replace('XXX',number_format($aux[0],0,',',' '),$lang[$lang_lang]['index_kulso.php']['Még XXX szabad bolygó van.']);
?></p>

<form action="." method="post">


<? if ($_REQUEST['reg_hiba']=='rulez') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Olvasd el és fogadd el a játékszabályokat!'];?></p><? } ?>
<?
if (($_REQUEST['reg_hiba']=='captcha')&&($_REQUEST['reg_captcha_hiba']=='kapcsa')) { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Hibásan gépelted be a szót.'];?></p><? } else {
?>
<? if ($_REQUEST['reg_hiba']=='captcha') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['reCAPTCHA-hiba'];?>: <?
if ($_REQUEST['reg_captcha_hiba']=='incorrect-captcha-sol') echo $lang[$lang_lang]['index_kulso.php']['Hibásan gépelted be a két szót.'];
else echo $_REQUEST['reg_captcha_hiba'];
?></p><? }
} ?>
<? if ($_REQUEST['reg_hiba']=='nincs_bolygo') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nincs már szabad bolygó!'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='nem_lehet_meg_regisztralni') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Erre a szerverre nem lehet még regisztrálni!'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='tulrovid') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Adj meg egy nick nevet!'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='tulhosszu') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['36 karakternél rövidebb nevet adj meg!'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='zandanev') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem lehet az a neved, hogy "Zandagort".'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='vesszo') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem lehet vessző a nevedben.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='speckokar') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem lehet speckó karakter a nevedben.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='van_ilyen_user' || $_REQUEST['reg_hiba']=='van_ilyen_szovi') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Van ilyen nevű játékos vagy szövetség a játékban. Válassz másikat!'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='rossz_email') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Hibás e-mail címed adtál meg.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='eltero_email') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem ugyanazt az e-mail címet adtad meg másodszor.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='foglalt_email') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Ezzel az e-mail címmel már regisztráltak.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='sikertelen_email') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem tudtuk kiküldeni a regisztrációs levelet erre az e-mail címre. Próbáld meg egy másikkal.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='tulrovid_jelszo') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Túl rövid jelszót adtál meg. Legalább 6 karakterből kell állnia.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='eltero_jelszo') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Nem ugyanazt a jelszót írtad be kétszer.'];?></p><? } ?>
<? if ($_REQUEST['reg_hiba']=='rossz_koord') { ?><p class="reg_hiba"><?=$lang[$lang_lang]['index_kulso.php']['Hibás koordináta. Vesszővel elválasztva add meg az észak-déli, majd a kelet-nyugati pozíciót. Vagy az É,D,K,Ny előtagokkal, vagy előjellel.'];?></p><? } ?>

<table style="margin: 0 auto">
<tr><td><b><?=$lang[$lang_lang]['index_kulso.php']['Nick név'];?>*</b>:</td><td><input type="text" class="szoveg" name="reg_nev" value="<?=$_REQUEST['reg_nev'];?>" size="30" /></td></tr>
<tr><td><b><?=$lang[$lang_lang]['index_kulso.php']['E-mail cím'];?></b>:</td><td><input type="text" class="szoveg" name="reg_email_1" value="<?=$_REQUEST['reg_email_1'];?>" size="30" /></td></tr>
<tr><td><b><?=$lang[$lang_lang]['index_kulso.php']['Jelszó'];?>**</b>:</td><td><input type="password" class="szoveg" name="reg_jelszo_1" value="<?=$_REQUEST['reg_jelszo_1'];?>" size="30" /></td></tr>
<tr><td><b><?=$lang[$lang_lang]['index_kulso.php']['Jelszó még egyszer'];?></b>:</td><td><input type="password" class="szoveg" name="reg_jelszo_2" value="<?=$_REQUEST['reg_jelszo_2'];?>" size="30" /></td></tr>
</table>

<p class="regi">* <?=$lang[$lang_lang]['index_kulso.php']['Max 36 karakter, nem lehet benne vessző vagy egyéb speckó jel, nem lehet létező játékos vagy szövetség neve, és nem lehet "Zandagort" :-)'];?><br />
** <?=$lang[$lang_lang]['index_kulso.php']['Legalább 6 karakter.'];?></p>

<p class="regi"><br /></p>

<p class="regi"><b><?=$lang[$lang_lang]['index_kulso.php']['Hova kéred a bolygódat?'];?></b></p>
<p class="regi">
<input type="radio" name="reg_pozi_valaszto" id="reg_pozi_valaszto_input_1" value="1"<? if ($_REQUEST['reg_pozi_valaszto']!=2) echo ' checked="checked"'; ?> onclick="pozi_valaszto_fv(1)" /><label for="reg_pozi_valaszto_input_1"> <?=$lang[$lang_lang]['index_kulso.php']['Legyen meglepetés.'];?></label>
<input type="radio" name="reg_pozi_valaszto" id="reg_pozi_valaszto_input_2" value="2"<? if ($_REQUEST['reg_pozi_valaszto']==2) echo ' checked="checked"'; ?> onclick="pozi_valaszto_fv(2)" /><label for="reg_pozi_valaszto_input_2"> <?=$lang[$lang_lang]['index_kulso.php']['Megadom a koordinátákat.'];?></label>
</p>
<div id="pozi_valaszto_div_1"<? if ($_REQUEST['reg_pozi_valaszto']==2) echo ' style="display:none"';?>></div>
<div id="pozi_valaszto_div_2"<? if ($_REQUEST['reg_pozi_valaszto']!=2) echo ' style="display:none"';?>>
<p class="regi"><?=$lang[$lang_lang]['index_kulso.php']['Add meg a koordinátákat (ha pl egy haverod mellé akarsz kerülni, aki megadta a bolygója pozícióját)'];?>:</p>
<p class="regi"><input type="text" class="szoveg" id="reg_koord" name="reg_koord" value="<?=$_REQUEST['reg_koord'];?>" /><br /><?=$lang[$lang_lang]['index_kulso.php']['(É xxx, Ny xxx formátumban)'];?></p>
<p class="regi">(<a href="#" onclick="window.open('map/?reg=1','reg_ablak','width=1000,height=650,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0');return false"><?=$lang[$lang_lang]['index_kulso.php']['Kiválasztom térképen.'];?></a>)</p>
</div>

<?
if (!isset($_REQUEST['reg_osztaly_valaszto'])) $_REQUEST['reg_osztaly_valaszto']=3;
?>
<p class="regi"><b><?=$lang[$lang_lang]['index_kulso.php']['Milyen osztályú bolygót szeretnél?'];?></b></p>
<p class="regi">
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_0" value="0"<? if ((int)$_REQUEST['reg_osztaly_valaszto']==0) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_0"> <?=$lang[$lang_lang]['index_kulso.php']['Legyen meglepetés.'];?></label>
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_1" value="1"<? if ((int)$_REQUEST['reg_osztaly_valaszto']==1) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_1"> A</label>
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_2" value="2"<? if ((int)$_REQUEST['reg_osztaly_valaszto']==2) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_2"> B</label>
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_3" value="3"<? if (((int)$_REQUEST['reg_osztaly_valaszto']<0) || ((int)$_REQUEST['reg_osztaly_valaszto']>5) || ((int)$_REQUEST['reg_osztaly_valaszto']==3)) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_3"> C</label>
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_4" value="4"<? if ((int)$_REQUEST['reg_osztaly_valaszto']==4) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_4"> D</label>
<input type="radio" name="reg_osztaly_valaszto" id="reg_osztaly_valaszto_input_5" value="5"<? if ((int)$_REQUEST['reg_osztaly_valaszto']==5) echo ' checked="checked"'; ?> /><label for="reg_osztaly_valaszto_input_5"> E</label>
</p>


<? if ($meghivo) { ?>
<input type="hidden" name="reg_kin_keresztul_uid" value="<?=$_REQUEST['ref_uid'];?>" />
<? } ?>


<p class="regi"><br /></p>
<p class="regi"><b><?=$lang[$lang_lang]['index_kulso.php']['Szeretnél fantom lenni?'];?></b> <input type="checkbox" name="reg_fantom"<? if (isset($_REQUEST['reg_fantom'])) echo ' checked="checked"';?> /></p>
<p class="regi"><?=$lang[$lang_lang]['index_kulso.php']['Miért legyél fantom?'];?></p>

<p class="regi"><br /></p>


<?
$kapcsa_szo=strtoupper(randomgen(8));
mysql_select_db($database_mmog_nemlog);
mysql_query('insert into kapcsak (kapcsa) values("'.$kapcsa_szo.'")') or hiba(__FILE__,__LINE__,mysql_error());
$er=mysql_query('select last_insert_id() from kapcsak') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$kapcsa_id=$aux[0];
mysql_select_db($database_mmog);
?>
<input type="hidden" name="reg_kapcsa_id" value="<?=$kapcsa_id;?>" />
<p class="regi"><img src="kapcsa.php?x=<?=$kapcsa_id;?>" /></p>
<p class="regi"><?=$lang[$lang_lang]['index_kulso.php']['Gépeld be a fenti szót ide'];?>: <input type="text" class="szoveg" name="reg_kapcsa_szo" value="" size="10" /></p>


<p class="regi"><br /></p>

<p class="regi"><input type="checkbox" name="reg_rulez"<? if (isset($_REQUEST['reg_rulez'])) echo ' checked="checked"';?> /> <?=$lang[$lang_lang]['index_kulso.php']['Elolvastam és elfogadtam a '];?><a href="<?=$zanda_homepage_url[$lang_lang]?>rulez/" target="_blank"><?=$lang[$lang_lang]['index_kulso.php']['játékszabályokat'];?></a>.</p>

<p class="regi"><br /></p>

<input type="hidden" name="reg_reg" value="1" />
<p class="regi"><input type="image" src="img/reg_button_<?=$lang_lang;?>.png" value="" /></p>

</form>

<? } else {//nincs szabad bolygo ?>

<? if ($lang_lang=='hu') { ?>
<p>Már nem lehet regisztrálni, mert elfogytak a szabad bolygók. <a href="<?=$facebook_link;?>">Kövess minket <img src="img/facebook_icon.gif" alt="Facebook" /> Facebook-on, hogy értesülj a következő szerver indulásáról!</a></p>
<? } else { ?>
<p>You cannot sign up, because there are no free planets left. <a href="<?=$facebook_link;?>">Like us on <img src="img/facebook_icon.gif" alt="Facebook" /> Facebook so you get noted when the next server starts!</a></p>
<? } ?>

<? } ?>

<? } ?>

<? }//regisztacio legyen/ne legyen ?>


<p style="text-align: right"><a href="<?=$zanda_homepage_url[$lang_lang]?>"><?=$lang[$lang_lang]['index_kulso.php']['Vissza a Zandagort főoldalára...'];?></a></p>

</div>
