var bolygo_osztalyok=['a','b','c','d','e'];
var bolygo_osztalyok_nagy=['A','B','C','D','E'];
var oldal_lista=['bolygo','flotta','birod','szovetseg','terkep','felder','komm','komm2','komm3','help','user','profil','cset'];
var aktiv_oldal='terkep';//ld stilus.css/oldal_birod, index_belso.php/aktiv_menu
var aktiv_bolygo=0;
var aktiv_bolygo_x=0;
var aktiv_bolygo_y=0;
var aktiv_bolygo_nev='';

var karrier_nevek=['***karrier 1***','***karrier 2***','***karrier 3***','***karrier 4***'];
var speci_nevek=[
	['***speci 1/1***','***speci 1/2***','***speci 1/3***']
	,['***speci 2/1***','***speci 2/2***','***speci 2/3***','***speci 2/4***']
	,['***speci 3/1***','***speci 3/2***','***speci 3/3***']
	,['***speci 4/1***','***speci 4/2***','***speci 4/3***']
];

var aktiv_user=0;

var aktiv_szovetseg=0;
var sajat_szovetseg=0;
var cset_vendeg_szov=0;
var szov_aloldal_lista=['forum','diplo','kozos','tagok','tisztek'];
var aktiv_szov_aloldal='';
var szov_forum_aktiv_tema_id=0;

var aloldal_lista=['oko','gazd','haboru','tozsde','szabadpiac','egyeb'];
var flotta_diplok=['npc','sajat','szovi','semli','hadban','beke','mnt'];

var attekintes_aloldal_lista=['birodalom','bolygok','flottak','kozos_flottak','hajok','egyeb'];
var aktiv_attekintes_aloldal='';

var felder_uj_bolygo_obj=new Object();

var aktiv_flotta=0;
var aktiv_flotta_x=0;
var aktiv_flotta_y=0;
var aktiv_flotta_tied=0;
var aktiv_flotta_nev='';

var iparag_idk=[1,2,3,4,7,5,6];
var iparag_sorszamok=[-1,0,1,2,3,5,6,4];
var iparag_nevek=['***Erőművek***','***Élelmiszeripar***','***Kitermelés***','***Feldolgozó ipar***','***Hadiipar***','***Települések***','***Speciális épületek***'];

var levelek_oldal=0;
var levelek_szama=0;
var levelek_oldal2=0;
var levelek_szama2=0;
var levelek_oldal3=0;
var levelek_szama3=0;
var aktiv_level=0;

var kommentek_oldal=0;
var kommentek_szama=0;
var komment_per_oldal=10;

var privat_cset_tabok=[0,0,0];
var aktiv_cset_aloldal=1;
var cset_uccso_idk=[0,0,0,0,0,0];
var cset_tabok_olvasatlan=[0,0,0,0,0,0];
var elso_cset_frissites=true;
var elso_5_perces_frissites=true;

var aktiv_regio=0;
var tozsde_graf_felbontas=0;
var tozsde_graf_piac=0;

var mobilrol_jatszik=false;

function bolygo_katt(id,haboru) {
	if (haboru) aloldal_nyit('haboru');
	set_aktiv_bolygo(id);
	oldal_nyit('bolygo');
	return false;
};
function set_aktiv_bolygo(id) {
	if (id>0) set_aktiv_flotta(0,0);
	aktiv_bolygo=id;
	var x=$('bolygo_lista').childNodes;
	for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
	if ($('menu_sajat_bolygo_'+aktiv_bolygo)) $('menu_sajat_bolygo_'+aktiv_bolygo).childNodes[0].style.fontWeight='bold';
	return false;
};
function flotta_katt(id) {
	set_aktiv_flotta(id,0);
	oldal_nyit('flotta');
	return false;
};
function set_aktiv_flotta(id,tied) {
	if (id>0) set_aktiv_bolygo(0);
	if (id==0) {
		jelolo2.style.left='-1000px';
		jelolo2.style.top='-1000px';
	} else {
		var sorszam=get_terkepflotta_sorszam_by_id(id);
		if (sorszam>-1) {
			jelolo2.style.left=(parseInt($('terkepflotta'+sorszam).style.left)+flotta_felszelesseg-jelolo2_felszelesseg)+'px';
			jelolo2.style.top=(parseInt($('terkepflotta'+sorszam).style.top)+flotta_felmagassag-jelolo2_felmagassag)+'px';
		}
	}
	aktiv_flotta=id;
	aktiv_flotta_tied=tied;
	aktiv_flotta_nev='';
	var x=$('flotta_lista').childNodes;
	for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
	if ($('menu_sajat_flotta_'+aktiv_flotta)) {
		$('menu_sajat_flotta_'+aktiv_flotta).childNodes[0].style.fontWeight='bold';
		aktiv_flotta_nev=$('menu_sajat_flotta_'+aktiv_flotta).childNodes[0].innerHTML;
	}
	return false;
};
function user_katt(id) {
	aktiv_user=id;
	oldal_nyit('user');
	return false;
};
function szovetseg_katt(id) {
	aktiv_szovetseg=id;
	oldal_nyit('szovetseg');
	return false;
};


function init() {
	$('terkep_context').style.left='0px';
	$('terkep_context').style.top='0px';
	frissit_terkep(1);
	jump_to_xy(kezdo_x,kezdo_y);
	//
	jelolo2=document.createElement('img');
	jelolo2.setAttribute('src','img/flottajelolo.gif');
	$('terkep_keret').appendChild(jelolo2);
	jelolo2.className='terkep_jelolo_div';
	jelolo2.style.left='-1000px';
	jelolo2.style.top='-1000px';
	jelolo2.style.width='18px';
	jelolo2.style.height='18px';
	jelolo2.style.zIndex='25';
	//
	if (isTouchDevice()) mobilrol_jatszik=true;
	//idozitok inditasa
	setInterval(frissit_minden_5_perces_dolgot,300000);//5 perc, levelek, forum, inaktiv cset, menu
	setInterval(frissit_bolygo_epulo,60000);//1 perc, csak ha nyitva van a bolygo adatlapja; ami majd kellene: kliens oldalon szamol (es frissit!), es csak akkor tolt le, ha lejar valami
	setInterval(frissit_cset,5000);//5 mp, csak ha nyitva van a nagy cset
	//
	new actb($('penzat_kinek_nev'),'ajax_autocomplete_userek',0);
	new actb($('terkep_gotoxy'),'ajax_autocomplete_bolygok',0);
	new actb($('jatekos_kereso_nev'),'ajax_autocomplete_userek',0);
	new actb($('szovetseg_kereso_nev'),'ajax_autocomplete_szovetsegek',0);
	if ($('attekinto_szures_nev')) new actb($('attekinto_szures_nev'),'ajax_autocomplete_userek_es_szovetsegek',0);
	new actb($('level_kereso_input_felado'),'ajax_autocomplete_userek_es_szovetsegek',0);
	new actb($('level_kereso_input_mappa'),'ajax_autocomplete_mappak',0);
	for(var i=1;i<=5;i++) if ($('fullscreen_terkep_par_psz'+i+'n')) new actb($('fullscreen_terkep_par_psz'+i+'n'),'ajax_autocomplete_userek_es_szovetsegek',0);
	frissit_mindent();
	frissit_szov_forum();
	aloldal_nyit('gazd');
	szov_aloldal_nyit('forum');
	frissit_minden_5_perces_dolgot();elso_5_perces_frissites=false;
	return false;
};

function frissit_mindent() {
	frissit_menu();
	oldal_nyit(aktiv_oldal);
};

function aloldal_nyit(x) {
	for(var i=0;i<aloldal_lista.length;i++) {
		if ($('bolygo_aloldal_'+aloldal_lista[i])) $('bolygo_aloldal_'+aloldal_lista[i]).style.display='none';
		if ($('menu_aloldal_'+aloldal_lista[i])) $('menu_aloldal_'+aloldal_lista[i]).className='';
	}
	$('bolygo_aloldal_'+x).style.display='block';
	$('menu_aloldal_'+x).className='aktiv_aloldal';
	return false;
}
function szov_aloldal_nyit(x) {
	aktiv_szov_aloldal=x;
	for(var i=0;i<szov_aloldal_lista.length;i++) {
		$('szovetseg_aloldal_'+szov_aloldal_lista[i]).style.display='none';
		$('menu_szov_aloldal_'+szov_aloldal_lista[i]).className='';
	}
	$('szovetseg_aloldal_'+x).style.display='block';
	$('menu_szov_aloldal_'+x).className='aktiv_aloldal';
	return false;
}
function attekintes_aloldal_nyit(x) {
	aktiv_attekintes_aloldal=x;
	for(var i=0;i<attekintes_aloldal_lista.length;i++) {
		$('attekintes_aloldal_'+attekintes_aloldal_lista[i]).style.display='none';
		$('menu_attekintes_aloldal_'+attekintes_aloldal_lista[i]).className='';
	}
	$('attekintes_aloldal_'+x).style.display='block';
	$('menu_attekintes_aloldal_'+x).className='aktiv_aloldal';
	return false;
}

function oldal_nyit(x) {
	aktiv_oldal=x;
	var lista=$('menu_egyeb').childNodes;
	for(var i=0;i<lista.length;i++) if (lista[i].nodeType==1) if (lista[i].tagName=='A') lista[i].className='';
	if ($('menu_egyeb_'+x)) $('menu_egyeb_'+x).className='aktiv_menu';
	frissit_aktiv_oldal();
	for(var i=0;i<oldal_lista.length;i++) $('oldal_'+oldal_lista[i]).style.display='none';
	$('oldal_'+x).style.display='block';
	return false;
};

function frissit_aktiv_oldal() {
	switch(aktiv_oldal) {
		case 'bolygo':frissit_bolygo();break;
		case 'flotta':frissit_flotta();break;
		case 'birod':frissit_birod();break;
		case 'szovetseg':frissit_szovetseg();break;
		case 'terkep':frissit_terkep();break;
		case 'felder':frissit_felder();break;
		case 'komm':frissit_komm(1);break;
		case 'komm2':frissit_komm(2);break;
		case 'komm3':frissit_komm(3);break;
		case 'user':frissit_user();break;
		case 'profil':frissit_profil();break;
		case 'cset':frissit_cset();frissit_cset_szobak();break;
	}
	return false;
};

function frissit_menu() {
	sendRequest('menu_adatok.php',function(req) {
		var valasz=json2obj(req.responseText);
		//bolygok
		var bolygok=valasz.bolygok;
		var s='';
		for(var i=0;i<bolygok.length;i++) {
			if (bolygok[i][4]>0) s+='<span class="bolygo_utes_jelzo" style="height:'+(12-Math.round(bolygok[i][5]/100*12))+'px" title="***morál***: '+bolygok[i][5]+'%"></span>';
			s+='<a id="menu_sajat_bolygo_'+bolygok[i][0]+'" class="menu_bolygo_osztaly_'+bolygok[i][2]+'" href="#" onclick="return bolygo_katt('+bolygok[i][0]+');">';
			s+='<span>'+bolygok[i][1]+'</span>';
			s+='</a>'+(bolygok[i][3]>0?'':'*')+'<br />';
		}
		$('bolygo_lista').innerHTML=s;
		var x=$('bolygo_lista').childNodes;
		for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
		if ($('menu_sajat_bolygo_'+aktiv_bolygo)) $('menu_sajat_bolygo_'+aktiv_bolygo).childNodes[0].style.fontWeight='bold';
		//flottak
		var flottak=valasz.flottak;
		var s='';
		for(var i=0;i<flottak.length;i++) s=s+'<a id="menu_sajat_flotta_'+flottak[i][0]+'" class="menu_flotta_'+flotta_diplok[flottak[i][2]]+'" href="#" onclick="return flotta_katt('+flottak[i][0]+');"><span>'+flottak[i][1]+'</span></a>'+((flottak[i][4]>0 || flottak[i][5]>0)?'*':'')+'<br />';
		$('flotta_lista').innerHTML=s;
		var x=$('flotta_lista').childNodes;
		for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
		if ($('menu_sajat_flotta_'+aktiv_flotta)) $('menu_sajat_flotta_'+aktiv_flotta).childNodes[0].style.fontWeight='bold';
		//levelek
		frissit_olvasatlan_levelek_szama();
		//szov_forum
		frissit_olvasatlan_temak_szama();
	});
	return false;
};

function frissit_bolygo() {
	$('tolto_ikon').style.display='block';
	sendRequest('bolygo_adatok.php?id='+aktiv_bolygo,function(req) {
		$('tolto_ikon').style.display='none';
		$('aktiv_bolygo_alapadatai').innerHTML='';
		$('aktiv_bolygo_eroforrasai').innerHTML='';
		$('aktiv_bolygo_gyarai').innerHTML='';
		$('aktiv_bolygo_leendo_gyarai').innerHTML='';
		$('aktiv_bolygo_fajai').innerHTML='';
		$('bolygo_aloldal_egyeb_szallitas').innerHTML='';
		$('aktiv_bolygo_riportjai').innerHTML='';
		var valasz=json2obj(req.responseText);
		if (valasz.letezik) {
			$('oldal_bolygo_hatter').style.backgroundImage='url(img/kepek/bolygo_'+bolygo_osztalyok[valasz.osztaly-1]+'_'+valasz.bolygokepmeret+(valasz.hold?'_h':'')+'.jpg)';
			aktiv_bolygo_nev=valasz.nev;
			aktiv_bolygo_x=valasz.x;
			aktiv_bolygo_y=valasz.y;
			jump_to_aktiv_bolygo();
			if (valasz.tied) {
				frissit_tozsde();
				if ($('bolygo_aloldal_szabadpiac')) frissit_szabadpiac();
				var bolygok_nav='<a href="" onclick="return bolygo_katt('+valasz.elozo+');" title="***előző***"><img src="img/resultset_previous.gif" /></a>';
				bolygok_nav+=' <a href="" onclick="return bolygo_katt('+valasz.kovetkezo+');" title="***következő***"><img src="img/resultset_next.gif" /></a>';
				var atnev='<a href="" onclick="return bolygo_rename_kerdez('+aktiv_bolygo+',\''+(valasz.esc_nev)+'\');" title="***bolygó átnevezése***"><img src="img/ikonok/szerk.gif" /></a>';
				if (valasz.magan_bolygok_szama>1) atnev+=' <a href="" title="***bolygó feladása***" onclick="if (confirm(\'***Biztosan fel akarod adni ezt a bolygódat?***\')) bolygo_feladasa();return false"><img src="img/ikonok/cross.gif" /></a>';
				if (valasz.techszint<=3) atnev+=' <span style="font-size:8pt">(<a href="" onclick="if (confirm(\'***Biztosan újra akarod indítani ezt a bolygódat?***\')) return bolygo_reset('+aktiv_bolygo+');return false" title="***bolygó újraindítása***">RESET</a>)</span>';
				$('aktiv_bolygo_neve').innerHTML=bolygok_nav+' '+valasz.nev+' '+atnev;
				//
				var tulaj='<a href="#" onclick="return user_katt('+valasz.tulaj_id+')">'+valasz.tulaj+'</a>';
				if (valasz.kezelo_id>0) {
					tulaj+=' (<a href="#" onclick="return user_katt('+valasz.kezelo_id+')">'+valasz.kezelo+'</a>';
					if (valasz.kezelo_id==valasz.te) tulaj+=' <a href="#" title="***tutorság visszautasítása***" onclick="return bolygo_tutort_visszadob('+aktiv_bolygo+')"><img src="img/ikonok/cross.gif" /></a>';
					tulaj+=')';
				}
				var pop_cel=valasz.kaja;if (valasz.lakohely<pop_cel) pop_cel=valasz.lakohely;
				var kov_pop=valasz.pop;
				if (valasz.pop<1000) kov_pop=1000;
				else if (pop_cel>valasz.pop+10) kov_pop+=(pop_cel-valasz.pop)/500*valasz.moral;
				else if (pop_cel<valasz.pop-10) kov_pop+=(pop_cel-valasz.pop)*(0.15-valasz.moral/1000);
				else kov_pop=pop_cel;
				$('aktiv_bolygo_alapadatai').innerHTML=json2table([
				['***Tulajdonos*** (<a href="#" onclick="return inline_toggle(\'bolygo_kezelo_form\')">***tutor***</a>)',tulaj+'<form id="bolygo_kezelo_form" style="display:none" onsubmit="return bolygo_uj_kezelo('+aktiv_bolygo+')"><br />***új tutor***: <input type="text" id="uj_bolygo_kezelo_nev" class="efszam" /></form>'],
				['***Pozíció (régió)***','<a href="" onclick="jump_to_aktiv_bolygo();return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(valasz.y)+', '+xkoordinata(valasz.x)+'</a> ('+valasz.regio+')'],
				['***Terület***/***Osztály***/***Hold***',valasz.terulet+'M / '+'<a href="" style="cursor: help" onclick="return jump_to_help(-1,'+valasz.osztaly+')">'+bolygo_osztalyok_nagy[valasz.osztaly-1]+'</a> / '+((valasz.hold>0)?'***van***':'-')],
				['***Beépített/effektív***',ezresito(valasz.terulet_foglalt)+' km&sup2; / '+ezresito(valasz.terulet_foglalt_effektiv)+' km&sup2; ('+Math.round(valasz.terulet_foglalt/valasz.terulet/10000)+'%/'+Math.round(valasz.terulet_foglalt_effektiv/valasz.terulet/10000)+'%)'],
				['***Épületek lábnyoma***',szazadresz(valasz.kornyezeti_fejlettseg)+'% (<a href="#" onclick="return kornyezetet_fejleszt_kerdez('+aktiv_bolygo+')">***fejleszt***</a>)'],
				['***Védelmi bónusz***',valasz.vedelmi_bonusz+' ***pont***'],
				['***Foglalható***/***Fosztható***',(valasz.vedelmi_bonusz<800?'***igen***':'***nem***')+'/'+((valasz.foszthato==0)?'***nem***':((parseInt(valasz.moratorium)>0)?('<a href="" onclick="return frissit_bolygo()">*** perc múlva prefix***'+valasz.moratorium+'*** perc múlva***</a> ***fosztható***'):'***igen***'))],
				['***Szabotálható***',(valasz.vedelmi_bonusz<800?'***igen***':((valasz.szabot!='-')?('***-ig prefix***'+valasz.szabot+'***-ig***'):'***nem***'))],
				['***Népesség***'+((valasz.pop!=Math.round(kov_pop))?' (***következő***)':''),ezresito(valasz.pop)+'*** fő***'+((valasz.pop!=Math.round(kov_pop))?(' ('+ezresito(Math.round(kov_pop))+'*** fő***)'):'')],
				['***Élelmiszer/termelés***',ezresito(valasz.kaja)+' RDA / '+ezresito(valasz.kajatermeles)+' RDA'],
				['<a href="" style="cursor: help" onclick="return jump_to_help(2,55)">***Lakóhely***</a>'+((valasz.lakohelytermeles!=valasz.lakohely)?' (***következő***)':''),ezresito(valasz.lakohely)+'*** fő***'+((valasz.lakohelytermeles!=valasz.lakohely)?' ('+ezresito(valasz.lakohelytermeles)+'*** fő***)':'')],
				['<a href="" style="cursor: help" onclick="return jump_to_help(2,57)">***Munkaerő***</a>/***igény***',ezresito(valasz.munkaero)+'*** fő*** / '+ezresito(valasz.munkaeroigeny)+'*** fő***'+(valasz.munkaero?(' ('+Math.round(valasz.munkaeroigeny/valasz.munkaero*100)+'%)'):'')],
				['***Szabad munkaerő***',(valasz.munkaero-valasz.munkaeroigeny>=0)?(ezresito(valasz.munkaero-valasz.munkaeroigeny)+'*** fő***'):('<span style="color:rgb(200,0,0)">'+ezresito(valasz.munkaeroigeny-valasz.munkaero)+'*** fő hiányzik***</span>')],
				['***Munkaerőhelyzet***',munkaerocsik(valasz.munkaeroigeny,valasz.munkaero,1)],
				['<a href="" style="cursor: help" onclick="return jump_to_help(2,58)">***Képzett munkaerő***</a>/***igény***',ezresito(valasz.kepzettmunkaero)+'*** fő*** / '+ezresito(valasz.kepzettmunkaeroigeny)+'*** fő***'+(valasz.kepzettmunkaero?(' ('+Math.round(valasz.kepzettmunkaeroigeny/valasz.kepzettmunkaero*100)+'%)'):'')],
				['***Morál***',statcsik(valasz.moral,1)],
				['***Termelés***','*** perc múlva prefix***<span id="term_mikor_span">'+valasz.term_mikor+'</span>*** perc múlva***<span id="term_mikor_frissit_span"></span>']
				],[]);
				new actb($('uj_bolygo_kezelo_nev'),'ajax_autocomplete_userek',0);
				$('tozsde_teleport_kapacitas').innerHTML=valasz.teleporttoltes+' / '+valasz.teleportkapacitas+' (+'+Math.round(valasz.teleportkapacitas/100)+'/***kör***)';
				if ($('szabadpiac_teleport_kapacitas')) $('szabadpiac_teleport_kapacitas').innerHTML=valasz.teleporttoltes+' / '+valasz.teleportkapacitas+' (+'+Math.round(valasz.teleportkapacitas/100)+'/***kör***)';
				$('egyeb_teleport_kapacitas').innerHTML=valasz.teleporttoltes+' / '+valasz.teleportkapacitas+' (+'+Math.round(valasz.teleportkapacitas/100)+'/***kör***)';
				//
				if (hajoszuro(valasz.hajok).length>0) {
					$('aktiv_bolygo_urhajoi').innerHTML='<form onsubmit="return false">'+
					json2table(hajoszuro(valasz.hajok),['','','***darab***','<a href="" onclick="return hany_hajot_rendez_mindet(\'bolygo\')">(***mindet***)</a>','***egyenérték***'],[
					function(sor) {return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.gif" /></a>';},
					function(sor) {return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';},
					function(sor) {return '<span style="font-weight: bold" id="max_bolygo_hajoszam_'+sor[0]+'" title="'+sor[1]+'">'+szazadresz(sor[2])+'</span>';},
					function(sor) {return '<input type="text" tabindex="'+sor[0]+'" class="hajoszam" id="bolygo_hany_hajot_rendez_'+sor[0]+'" /> (<a href="" onclick="$(\'bolygo_hany_hajot_rendez_'+sor[0]+'\').value='+sor[1]+';return false;">max</a>)';},
					function(sor) {return szazadresz(sor[3]);}
					],['34px','150px','100px','100px','100px'],null,null,['center','left','right','center','right'])+
					'</form>';
					$('aktiv_bolygo_flottai').innerHTML=
					json2table(valasz.flottak,[],[
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+(sor[2]?'sajat':'szovi')+'.gif" /></a>';},
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+(sor[2]?'':' (<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>)');},
					function(sor) {return '<a href="" onclick="return flotta_atrendezes(\'bolygo\','+sor[0]+')" title="***Kiválasztott hajók átpakolása ebbe a flottába***"><img src="img/ikonok/bepakol.gif" /></a>';}
					],['34px','333px','50px'])+
					json2table([[
					'<img src="img/ikonok/flotta_ikon_sajat.gif" />',
					'<input type="text" class="ujflottanev" id="bolygo_uj_flotta_nev" /> (***új***)',
					'<a href="" onclick="return flotta_atrendezes(\'bolygo\',0)" title="***Kiválasztott hajók átpakolása ebbe az új flottába***"><img src="img/ikonok/bepakol.gif" /></a>']],
					[],[0,1,2],['34px','333px','50px']);
				} else {
					$('aktiv_bolygo_urhajoi').innerHTML='***Nincsenek***';
					$('aktiv_bolygo_flottai').innerHTML=
					json2table(valasz.flottak,[],[
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+(sor[2]?'sajat':'szovi')+'.gif" /></a>';},
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+(sor[2]?'':' (<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>)');}
					],['34px','333px']);
				}
				//
				$('aktiv_bolygo_kikoto').innerHTML=json2table([
				['***Teljes flottaérték***',Math.floor(valasz.teljesflottaertek/100)+'***,***'+((valasz.teljesflottaertek%100<10)?'0':'')+(valasz.teljesflottaertek%100)],
				['***Szondák nélküli flottaérték***',Math.floor(valasz.teljesflottaertek_szondanelkul/100)+'***,***'+((valasz.teljesflottaertek_szondanelkul%100<10)?'0':'')+(valasz.teljesflottaertek_szondanelkul%100)],
				['***Kocsmakapacitás***',valasz.kocsmaszam],
				['***Kocsmatelítettség***',(valasz.kocsmaszam>0)?(Math.round(valasz.teljesflottaertek_szondanelkul/valasz.kocsmaszam)+'%'):'-']
				],[]);
				//
				$('aktiv_bolygo_eroforrasai').innerHTML=json2table(valasz.eroforrasok,['','***név***','***készlet***','***term/fogy***','***nettó***'],[
				function(sor) {
					return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';
				},
				function(sor) {//nev
					var s='<span style="font-weight:bold';
					if (sor[3]<0) s+=';color:rgb(255,0,0)';
					else if (sor[3]>100) s+=';color:rgb(255,0,0)';
					else if (sor[3]>80) s+=';color:rgb(255,'+(600-6*sor[3])+',0)';
					return s+'">'+eroforrasok_neve[sor[0]]+'</span>';
				},
				function(sor) {//keszlet, raktar
					var s='<span id="keszlet_span_'+sor[0]+'" title="'+sor[1]+'">'+ezresito(sor[1])+'</span>';
					if (sor[9]!=-2) {//premium
						if (sor[9]==-1) {
							if (sor[10]!=-1) {//betelik
								/*s+='<br /><span style="font-size:8pt;color:rgb(40,150,40)">+'+ezresito(sor[10])+'</span>';*/
							}
						} else {//kiurul
							if (sor[9]==0) {
								s+='<br /><span style="font-size:8pt" class="keszlet_kifogy">0</span>';
							} else {
								s+='<br /><span style="font-size:8pt" class="keszlet_kifogy">-'+ezresito(sor[9])+'</span>';
							}
						}
					}
					return s;
				},
				function(sor) {//term/fogy
					if ((sor[6]==undefined || sor[6]==0) && (sor[5]==undefined || sor[5]==0) && (sor[11]==undefined || sor[11]==0)) return '';
					var s='<span style="font-size:8pt">';
					s+='+'+ezresito(sor[6]==undefined?0:parseInt(sor[6]));
					s+='<br />';
					s+='<span id="brutto_fogy_span_'+sor[0]+'" title="'+(sor[5]==undefined?0:parseInt(sor[5]))+'">-'+ezresito(sor[5]==undefined?0:parseInt(sor[5]))+'</span>';
					if (sor[11]>0) s+='<br /><span class="eroforras_transzfer_be">+'+ezresito(sor[11]==undefined?0:parseInt(sor[11]))+'</span>';
					if (sor[11]<0) s+='<br /><span class="eroforras_transzfer_ki">'+ezresito(sor[11]==undefined?0:parseInt(sor[11]))+'</span>';
					s+='</span>';
					return s;
				},
				function(sor) {//netto
					var netto=sor[2]+(sor[11]==undefined?0:parseInt(sor[11]));
					if (netto>0) return '<span class="eroforras_netto_poz" id="netto_term_span_'+sor[0]+'" title="'+netto+'">+'+ezresito(netto)+'</span>';
					if (netto<0) {//piros szam -> hover help
						if (sor[13].length>0) {
							return '<img src="img/ikonok/help_ikon.gif" title="'+htmlspecialchars(sor[13])+'" /> <span class="eroforras_netto_neg" id="netto_term_span_'+sor[0]+'" title="'+netto+'">'+ezresito(netto)+'</span>';
						} else return '<span class="eroforras_netto_neg" id="netto_term_span_'+sor[0]+'" title="'+netto+'">'+ezresito(netto)+'</span>';
					}
					return '<span id="netto_term_span_'+sor[0]+'" title="0"></span>';
				}
				],['34px','90px','','',''],null,null,['center','left','right','right','right']);
				//
				var iparag_jelzok=false;if (valasz.iparag_jelzok==1) iparag_jelzok=true;
				var gyar_ikonok=true;if (valasz.gyar_ikonok==0) gyar_ikonok=false;
				var abgy='';
				abgy=json2table([],['','***név***','***aktív<br />(üzemel)***','***inaktív***'],[],['34px','187px','60px','50px']);
				var iparag='';
				for(var ii=0;ii<valasz.gyarak.length;ii++) {
					if (iparag_jelzok) if (iparag!=valasz.gyarak[ii][5]) abgy+='<h3>'+valasz.gyarak[ii][5]+'</h3>';
					iparag=valasz.gyarak[ii][5];
					if (gyar_ikonok) abgy+=json2table([valasz.gyarak[ii]],[],[
					function(sor) {
						var s='';
						s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[1]+')">';
						s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[1]]+'_index.jpg" />';
						s+='</a>';
						return s;
					},
					function(sor) {
						var s='';
						s+='<span style="font-weight: bold">'+sor[2]+'</span>';
						if (sor[7]>1) s+=' ('+eroforrasok_neve[sor[6]]+')';
						return s;
					},
					function(sor) {
						var s=sor[4];
						if (sor[8]<100*sor[4]) {
							s+=' <span style="color:rgb(200,0,0)">('+Math.floor(sor[8]/100)+',';
							if (sor[8]%100<10) s+='0';
							s+=sor[8]%100;
							s+=')'+'</span>';
						}
						return s;
					},
					function(sor) {
						var s='';
						if (sor[3]>sor[4]) s=sor[3]-sor[4];
						else s='&nbsp;';
						return s;
					}
					],['34px','187px','60px','50px'],function(sor) {
						if (mobilrol_jatszik) return ' style="cursor: pointer" onmouseover="this.style.background=\'rgb(100,100,100)\'" onmouseout="this.style.background=\'none\'" onclick="listaz_gyar_uzemmodok('+sor[0]+');toggle(\'aktiv_bolygo_epulet_'+sor[0]+'\');return false"';
						else return ' style="cursor: pointer" onmouseover="this.style.background=\'rgb(100,100,100)\'" onmouseout="this.style.background=\'none\'" onclick="listaz_gyar_uzemmodok('+sor[0]+');toggle(\'aktiv_bolygo_epulet_'+sor[0]+'\');$(\'hanyat_epit_'+sor[0]+'\').focus();return false"';
					});
					else abgy+=json2table([valasz.gyarak[ii]],[],[
					function(sor) {
						return '';
					},
					function(sor) {
						var s='';
						s+='<span style="font-weight: bold">'+sor[2]+'</span>';
						if (sor[7]>1) s+=' ('+eroforrasok_neve[sor[6]]+')';
						return s;
					},
					function(sor) {
						var s=sor[4];
						if (sor[8]<100*sor[4]) {
							s+=' <span style="color:rgb(200,0,0)">('+Math.floor(sor[8]/100)+',';
							if (sor[8]%100<10) s+='0';
							s+=sor[8]%100;
							s+=')'+'</span>';
						}
						return s;
					},
					function(sor) {
						var s='';
						if (sor[3]>sor[4]) s=sor[3]-sor[4];
						else s='&nbsp;';
						return s;
					}
					],['34px','187px','60px','50px'],function(sor) {
						if (mobilrol_jatszik) return ' style="cursor: pointer" onmouseover="this.style.background=\'rgb(100,100,100)\'" onmouseout="this.style.background=\'none\'" onclick="listaz_gyar_uzemmodok('+sor[0]+');toggle(\'aktiv_bolygo_epulet_'+sor[0]+'\');return false"';
						else return ' style="cursor: pointer" onmouseover="this.style.background=\'rgb(100,100,100)\'" onmouseout="this.style.background=\'none\'" onclick="listaz_gyar_uzemmodok('+sor[0]+');toggle(\'aktiv_bolygo_epulet_'+sor[0]+'\');$(\'hanyat_epit_'+sor[0]+'\').focus();return false"';
					});
					abgy+='<div id="aktiv_bolygo_epulet_'+valasz.gyarak[ii][0]+'" style="display: none">'+json2table([valasz.gyarak[ii]],[],[
					function(sor) {return '';},
					function(sor) {
						var s='';
						var rombol_aktiv=1;
						if (sor[3]>sor[4]) rombol_aktiv=0;
						s+='<ul class="akcio_menu">';
						s+='<li>';
						s+='</li>';
						if (sor[3]>0) {
							s+='<li>';
							s+='<input type="text" class="szovegdoboz" style="width:80px" id="hanyat_aktival_'+sor[0]+'" value="'+sor[3]+'" />';
							s+=' <a href="" onclick="return set_gyar_aktiv_db('+aktiv_bolygo+','+sor[0]+','+sor[4]+'+parseInt($(\'hanyat_aktival_'+sor[0]+'\').value))" title="***aktivál***" class="link_aktival">***aktivál***</a> ';
							s+=' <a href="" onclick="return set_gyar_aktiv_db('+aktiv_bolygo+','+sor[0]+','+sor[4]+'-parseInt($(\'hanyat_aktival_'+sor[0]+'\').value))" title="***inaktivál***" class="link_inaktival">***inaktivál***</a> ';
							if (gyarak_inputja[sor[0]].length) s+='<br /><span style="font-size: 8pt"><b>***Fogy***</b>: '+gyarak_inputja[sor[0]]+'</span>';
							var s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: '+gyarak_outputja[sor[0]]+'</span>';
							if (sor[1]==31) {
								if (sor[0]==84 && valasz.osztaly==3) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[60]+'</span>';
								if (sor[0]==85 && valasz.osztaly==2) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[61]+'</span>';
								if (sor[0]==86 && valasz.osztaly==5) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[62]+'</span>';
								if (sor[0]==90 && valasz.osztaly==1) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 50 '+eroforrasok_neve[63]+'</span>';
							}
							if (gyarak_outputja[sor[0]].length) s+=s_kut;
							s+='</li>';
						}
						s+='<li><input type="text" class="szovegdoboz" style="width:80px" id="hanyat_epit_'+sor[0]+'" value="1" />';
						s+=' <a href="" onclick="return gyar_epit_tobb('+aktiv_bolygo+','+sor[0]+',1,$(\'hanyat_epit_'+sor[0]+'\').value)" title="***épít***" class="link_epit">***épít***</a>';
						s+=' <a href="" onclick="return gyar_epit_tobb('+aktiv_bolygo+','+sor[0]+',0,$(\'hanyat_epit_'+sor[0]+'\').value)" title="***inaktívat épít***" class="link_epit_inaktiv">***inaktívat épít***</a>';
						s+='<br /><span style="font-size: 8pt">'+epuletek_gyartasi_koltsege[sor[1]]+', <img src="img/ikonok/clock.gif" /> '+sec2hm(60*epuletek_gyartasi_ideje[sor[1]])+'</span>';
						var hany=hanyat_lehet_epiteni(sor[1]);
						if (hany>0) s+='<br /><span style="font-size: 8pt" class="gyar_mennyi_epitheto"><b>'+ezresito(hany)+'</b>*** építhető most***.</span>';
						if (sor[10]>0) {//premium
							if (hany==0) {
								var mikor_lehet=new Array();
								mikor_lehet=mikor_lehet_epiteni(sor[1]);mikor=mikor_lehet[0];hiany=mikor_lehet[1];
								if (mikor>=0) s+='<br /><span style="font-size: 8pt" class="gyar_hany_kor_mulva">*** kör múlva lesz hozzá elég építőanyag prefix***<b>'+ezresito(mikor)+'</b>*** kör múlva lesz hozzá elég építőanyag***. ***Hiányzik***: '+hiany+'.</span>';
								else s+='<br /><span style="font-size: 8pt" class="gyar_soha_nem_eleg">***<b>Soha nem</b> lesz hozzá elég építőanyag***. ***Hiányzik***: '+hiany+'.</span>';
							}
						}
						//
						s+='<br /><a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+sor[0]+',1,$(\'hanyat_epit_'+sor[0]+'\').value,1)" title="***sor elejére***" class="link_epit">***sor elejére***</a>';
						s+=' <a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+sor[0]+',0,$(\'hanyat_epit_'+sor[0]+'\').value,1)" title="***inaktívat sor elejére***" class="link_epit_inaktiv">***inaktívat sor elejére***</a>';
						s+='<br /><a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+sor[0]+',1,$(\'hanyat_epit_'+sor[0]+'\').value,2)" title="***sor végére***" class="link_epit">***sor végére***</a>';
						s+=' <a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+sor[0]+',0,$(\'hanyat_epit_'+sor[0]+'\').value,2)" title="***inaktívat sor végére***" class="link_epit_inaktiv">***inaktívat sor végére***</a>';
						//
						s+='</li>';
						s+='<li><input type="text" class="szovegdoboz" style="width:80px" id="hanyat_lerombol_'+sor[0]+'" value="" /> <a href="" onclick="return gyar_lerombol_tobb('+aktiv_bolygo+','+sor[0]+','+rombol_aktiv+',$(\'hanyat_lerombol_'+sor[0]+'\').value)" title="***lerombol***" class="link_rombol">***lerombol***</a></li>';
						s+='<li id="gyar_uzemmod_lista_'+sor[0]+'"></li>';
						s+='</ul>';
						return s;
					}
					],['34px','309px'],function(sor) {
						return ' class="gyar_context_menu"';
					})+'</div>';
				}
				abgy+='<h2><a href="" onclick="listaz_uj_gyarak();return toggle(\'aktiv_bolygo_uj_epuletek\');">***Új épület***</a></h2>';
				abgy+='<div id="aktiv_bolygo_uj_epuletek" style="display: none; width: 355px" class="uj_epuletek"></div>';
				$('aktiv_bolygo_gyarai').innerHTML=abgy;
				//
				frissit_bolygo_epulo();
				$('aktiv_bolygo_fajai').innerHTML=json2table(valasz.fajok,[],[
				function(sor) {
					return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')" title="'+eroforrasok_neve[sor[0]]+'"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';
				},
				function(sor) {
					if (sor[2]==1) return fajcsik(sor[1],5000,sor[3],eroforrasok_neve[sor[0]],680,aktiv_bolygo,sor[0],sor[2]);
					else return fajcsik(sor[1],1000,sor[3],eroforrasok_neve[sor[0]],680,aktiv_bolygo,sor[0],sor[2]);
				}
				]);
				if (valasz.premium) {//beepitett okoszim
					var okoszim='';
					okoszim+='<h2>***Ökoszimulátor***</h2>';
					okoszim+='<form onsubmit="return beepitett_okoszim()">';
					okoszim+='<input type="hidden" id="okoszim_input_bolygoosztaly" value="'+valasz.osztaly+'" />';
					okoszim+='<input type="hidden" id="okoszim_input_bolygoterulet" value="'+(valasz.terulet*1000000)+'" />';
					okoszim+='<input type="hidden" id="okoszim_input_fajlista" value="';
					for(var i=0;i<valasz.fajok.length;i++) {
						if (i>0) okoszim+=',';
						okoszim+=valasz.fajok[i][0];
					}
					okoszim+='" />';
					okoszim+='<input type="hidden" id="okoszim_input_gyarlista" value="';
					for(var i=0;i<valasz.bio_kitermeles.length;i++) {
						if (i>0) okoszim+=',';
						okoszim+=valasz.bio_kitermeles[i][2];
					}
					okoszim+='" />';
					okoszim+='<table valign="top"><tr valign="top">';
					okoszim+='<td>';
					okoszim+=json2table(valasz.fajok,[],[
					function(sor) {
						return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')" title="'+eroforrasok_neve[sor[0]]+'"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';
					},
					function(sor) {
						return '<input type="text" id="okoszim_input_faj_'+sor[0]+'" value="'+sor[1]+'" class="szovegdoboz okoszim_fajszam" /><input type="hidden" id="okoszim_input_orig_faj_'+sor[0]+'" value="'+sor[1]+'" /><input type="hidden" id="okoszim_faj_celszam_'+sor[0]+'" value="'+sor[3]+'" />';
					}
					]);
					okoszim+='<h3>***Ipar***</h3>';
					okoszim+=json2table(valasz.bio_kitermeles,[],[
					function(sor) {
						return '<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[0]+')" title="'+epuletek_neve[sor[0]]+'"><img src="img/ikonok/'+epuletek_fajlneve[sor[0]]+'_index.jpg" /></a>';
					},
					function(sor) {
						return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[1]+')" title="'+eroforrasok_neve[sor[1]]+'"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[1]]+'_index.jpg" /></a>';
					},
					function(sor) {
						return '<input type="text" id="okoszim_input_gyar_'+sor[2]+'" value="'+sor[3]+'" class="szovegdoboz okoszim_gyarszam" /><input type="hidden" id="okoszim_input_orig_gyar_'+sor[2]+'" value="'+sor[3]+'" />';
					}
					]);
					okoszim+='</td>';
					okoszim+='<td>';
					okoszim+='<img src="img/blank.gif" style="width: 401px; height: '+(valasz.fajok.length*38)+'px" id="okoszim_graf" />';
					okoszim+='<br />';
					okoszim+='<p style="text-align: center">***Napok száma***: <input type="text" id="okoszim_input_napszam" value="10" class="szovegdoboz" style="width: 30px" /> (max 10)</p>';
					okoszim+='<br />';
					okoszim+='<p style="text-align: center"><input type="submit" value="***Mehet!***" /></p>';
					okoszim+='<br />';
					okoszim+='<p style="text-align: center"><a href="" onclick="return beepitett_okoszim_import(1)">***ökoszféra input*** &lt;- ***szimuláció eredménye***</a></p>';
					okoszim+='<br />';
					okoszim+='<p style="text-align: center"><a href="" onclick="return beepitett_okoszim_import(2)">***ökoszféra input*** &lt;- ***tényleges***</a></p>';
					okoszim+='<p style="text-align: center"><a href="" onclick="return beepitett_okoszim_import(3)">***ipar*** &lt;- ***tényleges***</a></p>';
					okoszim+='</td>';
					okoszim+='<td>';
					okoszim+=json2table(valasz.fajok,[],[
					function(sor) {
						return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')" title="'+eroforrasok_neve[sor[0]]+'"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';
					},
					function(sor) {
						return '<span id="okoszim_output_faj_'+sor[0]+'" title="'+sor[1]+'">'+ezresito(sor[1])+'</span>';
					},
					function(sor) {
						return '(<span id="okoszim_output_faj_szazalek_'+sor[0]+'">'+Math.round(sor[1]/sor[3]*100)+'</span>%)';
					}
					],null,null,null,['center','right','right']);
					okoszim+='<h3>Output</h3>';
					okoszim+=json2table([[56],[74],[64],[59]],[],[
					function(sor) {
						return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')" title="'+eroforrasok_neve[sor[0]]+'"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';
					},
					function(sor) {
						return '<span id="okoszim_output_gyar_'+sor[0]+'" title="0">0</span>';
					}
					],null,null,null,['center','right']);
					okoszim+='</td>';
					okoszim+='</tr></table>';
					okoszim+='</form>';
					$('aktiv_bolygo_okoszim').innerHTML=okoszim;
				} else {
					var okoszim='';
					okoszim+='<h2>***Ökoszimulátor***</h2>';
					okoszim+='<p>***A beépített ökoszimulátor egy prémium szolgáltatás. Ha szeretnéd igénybe venni, elő kell fizetned. Erről bővebben <a href="***zanda_homepage_url***premium/" target="_blank">ITT</a> olvashatsz.***</p>';
					okoszim+='<p>***A másik lehetőség, hogy az egyszerűbb verzióját használod, ami <a href="***zanda_homepage_url***okoszim/" target="_blank">ITT</a> érhető el.***</p>';
					$('aktiv_bolygo_okoszim').innerHTML=okoszim;
				}
				//
				var efselect='';
				var szallithato_eroforrasok=transzefszuro(valasz.eroforrasok);
				for(var i=0;i<szallithato_eroforrasok.length;i++) efselect+='<option value="'+szallithato_eroforrasok[i][0]+'">'+eroforrasok_neve[szallithato_eroforrasok[i][0]]+'</option>';
				$('bolygo_aloldal_egyeb_szallitas').innerHTML='<form onsubmit="return false">'+
				json2table(szallithato_eroforrasok,['','','***készlet***','***szállítandó***','***jelenlegi töltéssel szállítható***'],[
				function(sor) {return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.jpg" /></a>';},
				function(sor) {return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';},
				function(sor) {return '<span style="font-weight: bold">'+ezresito(sor[1])+'</span>';},
				function(sor) {return '<input type="text" tabindex="'+sor[0]+'" class="efszam" id="bolygo_hany_eroforrast_rendez_'+sor[0]+'" /> (<a href="" onclick="$(\'bolygo_hany_eroforrast_rendez_'+sor[0]+'\').value='+sor[1]+';return false;">max</a>)';},
				function(sor) {return ezresito(valasz.teleporttoltes*eroforrasok_savszele[sor[0]]);}
				],['34px','150px','100px','150px','100px'],null,null,['center','left','right','center','right'])+
				'</form>'+
				json2table(valasz.bolygok,[],[
				function(sor) {
					if (sor[3]==1) return '<img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[2]-1]+'32.gif" />';
					return '<a href="" onclick="return bolygo_katt('+sor[0]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[2]-1]+'32.gif" /></a>';
				},
				function(sor) {
					if (sor[3]==1) return '<span style="font-weight: bold" class="nem_kattinthato_bolygo">'+sor[1]+'</a>';
					return '<a href="" onclick="return bolygo_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>';
				},
				function(sor) {
					if (sor[3]==1) return '';
					return '<a href="" onclick="return eroforras_transzfer('+sor[0]+')" title="***Kiválasztott erőforrások szállítása erre a bolygóra***"><img src="img/ikonok/bepakol.gif" /></a>';
				}
				],['34px','300px','106px'])+
				((valasz.premium==2)?(
					'<h2>***Ütemezett szállítás***</h2>'
					+'<h3>***Kimenő***</h3>'
					+(valasz.auto_transz.length>0?(
					json2table(valasz.auto_transz,['***mennyit***','***miből***','***hova***','***TT***','',''],[
					function(sor) {return ezresito(sor[1]);},
					function(sor) {return eroforrasok_neve[sor[0]];},
					function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[2]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[3]-1]+'.gif" /> '+sor[4]+'</a>';},
					function(sor) {return ezresito(Math.ceil(sor[1]/eroforrasok_savszele[sor[0]]));},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_del('+sor[6]+')">***törlés***</a>';},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_mod_kerdez('+sor[6]+','+sor[1]+')">***módosítás***</a>';}
					],['120px','150px','220px','50px','50px','50px'],null,null,['right','left','left','right','center','center'])
					+json2table([[
					'',
					'',
					'',
					'<span id="auto_transzfer_szumma_tt"></span>',
					'',
					''
					]],
					[],[0,1,2,3,4],['120px','150px','220px','50px','50px','50px'],null,null,['right','left','left','right','center','center'])
					):'')
					+json2table([[
					'<input type="text" class="szovegmezo" style="width:100px" id="auto_szallitas_darab" />',
					'<select class="szovegmezo" style="width:100px" id="auto_szallitas_ef">'+efselect+'</select>',
					'<input type="text" class="szovegmezo" style="width:200px" id="auto_szallitas_bolygo_nev" />',
					'',
					'<a href="" onclick="return eroforras_auto_transzfer()">***Mehet!***</a>'
					]],
					['***mennyit***','***miből***','***hova***','',''],[0,1,2,3,4],['120px','150px','220px','50px','50px'],null,null,['left','left','left','right','center'])
					+'<h3>***Bejövő***</h3>'
					+(valasz.auto_transz_in.length>0?(
					json2table(valasz.auto_transz_in,['***mennyit***','***miből***','***honnan***'],[
					function(sor) {return ezresito(sor[1]);},
					function(sor) {return eroforrasok_neve[sor[0]];},
					function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[2]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[3]-1]+'.gif" /> '+sor[4]+'</a>';}
					],['120px','150px','220px'],null,null,['right','left','left'])
					):'')
				):'');
				if ($('auto_transzfer_szumma_tt')) {
					var szumma_tt=0;
					for(var i=0;i<valasz.auto_transz.length;i++) szumma_tt+=Math.ceil(valasz.auto_transz[i][1]/eroforrasok_savszele[valasz.auto_transz[i][0]]);
					$('auto_transzfer_szumma_tt').innerHTML=ezresito(szumma_tt);
				}
				if ($('auto_szallitas_bolygo_nev')) new actb($('auto_szallitas_bolygo_nev'),'ajax_autocomplete_sajat_bolygok',0);
				//
				$('aktiv_bolygo_reszletei').style.display='block';
				$('aktiv_bolygo_riportjai').style.display='none';
			} else {
				$('aktiv_bolygo_reszletei').style.display='none';
				$('aktiv_bolygo_neve').innerHTML=valasz.nev+(valasz.koltozheto?' <a style="font-size:8pt" href="#" onclick="if (confirm(\'***Biztosan ide akarsz költözni?***\')) bolygo_move();return false" title="***költözés ide***"><img src="img/ikonok/rubber-balloons.png" /> ***költözés ide***</a>':'');
				var tulaj='';
				if (valasz.tulaj_id>0) {
					tulaj='<a href="" onclick="return user_katt('+valasz.tulaj_id+')">'+valasz.tulaj+'</a>';
				} else {
					tulaj=valasz.tulaj+(valasz.alapbol_regisztralhato?'':' (***új regisztrációnál nem adható***)');
				}
				//
				$('aktiv_bolygo_alapadatai').innerHTML=json2table([
				['***Tulajdonos***',tulaj]
				,['***Pozíció (régió)***','<a href="" onclick="jump_to_aktiv_bolygo();return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(valasz.y)+', '+xkoordinata(valasz.x)+'</a> ('+valasz.regio+')']
				,['***Terület***/***Osztály***/***Hold***',valasz.terulet+'M / '+'<a href="" style="cursor: help" onclick="return jump_to_help(-1,'+valasz.osztaly+')">'+bolygo_osztalyok_nagy[valasz.osztaly-1]+'</a> / '+((valasz.hold>0)?'***van***':'-')]
				,['***Védelmi bónusz***',valasz.vedelmi_bonusz+' ***pont***']
				,['***Foglalható***/***Fosztható***',(valasz.vedelmi_bonusz<800?'***igen***':'***nem***')+'/'+((valasz.foszthato==0)?'***nem***':((parseInt(valasz.moratorium)>0)?('<a href="" onclick="return frissit_bolygo()">*** perc múlva prefix***'+valasz.moratorium+'*** perc múlva***</a> '+((valasz.foszthato==1)?'***fosztható***':'***üthető***')):((valasz.foszthato==1)?'***igen***':'***üthető***')))]
				,['***Szabotálható***',(valasz.vedelmi_bonusz<800?'***igen***':((valasz.szabot!='-')?('***-ig prefix***'+valasz.szabot+'***-ig***'):'***nem***'))]
				],[]);
				var rip='';
				if (valasz.kemriportok[1]) if (valasz.kemriportok[1].length>0) rip+='<h2>***Gyárak***</h2>'+json2table(
					valasz.kemriportok[1],['','***pontos***','***aktív***','***mikor***','***becsült***','***mikor***'],[
						function(sor) {return epuletek_neve[sor[0]];}
						,function(sor) {if (sor[1]>0) return ezresito(sor[2]);return '';}
						,function(sor) {if (sor[1]>0) return '('+ezresito(sor[3])+')';return '';}
						,function(sor) {if (sor[1]>0) return '<span class="halvany_pici">'+sor[4]+'</span>';return '';}
						,function(sor) {if (sor[5]>0) return ezresito(sor[6])+'+';return '';}
						,function(sor) {if (sor[5]>0) return '<span class="halvany_pici">'+sor[8]+'</span>';return '';}
					],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','right','right','center','right','center']
				);
				if (valasz.kemriportok[2]) if (valasz.kemriportok[2].length>0) rip+='<h2>***Erőforrások***</h2>'+json2table(
					valasz.kemriportok[2],['','***pontos***','***mikor***','***becsült***','***mikor***'],[
						function(sor) {return eroforrasok_neve[sor[0]];}
						,function(sor) {if (sor[1]>0) return ezresito(sor[2]);return '';}
						,function(sor) {if (sor[1]>0) return '<span class="halvany_pici">'+sor[4]+'</span>';return '';}
						,function(sor) {if (sor[5]>0) return ezresito(sor[6])+'+';return '';}
						,function(sor) {if (sor[5]>0) return '<span class="halvany_pici">'+sor[8]+'</span>';return '';}
					],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','right','center','right','center']
				);
				$('aktiv_bolygo_riportjai').innerHTML=rip;
				$('aktiv_bolygo_riportjai').style.display='block';
			}
		} else {
			aktiv_bolygo=0;
			$('aktiv_bolygo_reszletei').style.display='none';
			$('aktiv_bolygo_riportjai').style.display='none';
			$('aktiv_bolygo_neve').innerHTML='';
		}
	});
	return false;
};

function listaz_gyar_uzemmodok(gyid) {
	if (aktiv_bolygo) {
		sendRequest('listaz_gyar_uzemmodok.php?bid='+aktiv_bolygo+'&gyid='+gyid,function(req) {
			var valasz=json2obj(req.responseText);
			if (valasz.oke) {
				if (valasz.lista.length>1) $('gyar_uzemmod_lista_'+gyid).innerHTML='***Üzemmódváltás***:'+json2table(valasz.lista,[],[
				function(sor) {
					var s='';
					s+='<input type="text" class="szovegdoboz" style="width:30px" id="hanyat_atvalt_'+sor[0]+'" value="1" /> ***átvált***: <a href="" onclick="return set_gyar_uzemmod_tobb('+aktiv_bolygo+','+gyid+','+sor[0]+',$(\'hanyat_atvalt_'+sor[0]+'\').value)">';
					s+=eroforrasok_neve[sor[0]];
					s+='</a>';
					return s;
				}
				]);else $('gyar_uzemmod_lista_'+gyid).innerHTML='';
			} else $('gyar_uzemmod_lista_'+gyid).innerHTML='';
		});
	}
	return false;
};

function listaz_uj_gyarak() {
	if (aktiv_bolygo) {
		sendRequest('listaz_uj_gyarak.php?bid='+aktiv_bolygo,function(req) {
			var valasz=json2obj(req.responseText);
			if (valasz.oke) {
				var s='';
				s+='<table>';
				for(var i=0;i<valasz.lista.length;i++) {
					s+='<tr><td>';
					s+='<a href="" style="font-weight: bold" onclick="return toggle(\'uj_gyar_lista_iparag_'+i+'\')">'+valasz.lista[i].iparag+'</a><br />';
					s+='<div id="uj_gyar_lista_iparag_'+i+'" style="display:none">';
					s+='<table>';
					for(var j=0;j<valasz.lista[i].gyarak.length;j++) {
						s+='<tr>';
						s+='<td style="vertical-align: top"><a href="" style="cursor: help" onclick="return jump_to_help(1,'+valasz.lista[i].gyarak[j][0]+')"><img src="img/ikonok/'+valasz.lista[i].gyarak[j][4]+'_index.jpg" /></a></td>';
						s+='<td>';
						s+='<span style="font-weight: bold">'+valasz.lista[i].gyarak[j][2]+'</span>';
						if (valasz.lista[i].gyarak[j][3]>0) s+=' ('+eroforrasok_neve[valasz.lista[i].gyarak[j][3]]+')';
						s+='<br /><input type="text" class="szovegdoboz" style="width:30px" id="hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'" value="1" />';
						s+=' <a href="" onclick="return gyar_epit_tobb('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',1,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value)" title="***épít***" class="link_epit">***épít***</a>';
						s+=' <a href="" onclick="return gyar_epit_tobb('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',0,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value)" title="***inaktívat épít***" class="link_epit_inaktiv">***inaktívat épít***</a>';
						s+='<br /><span style="font-size: 8pt">'+epuletek_gyartasi_koltsege[valasz.lista[i].gyarak[j][0]]+', <img src="img/ikonok/clock.gif" /> '+sec2hm(60*epuletek_gyartasi_ideje[valasz.lista[i].gyarak[j][0]])+'</span>';
						var hany=hanyat_lehet_epiteni(valasz.lista[i].gyarak[j][0]);
						if (hany>0) s+='<br /><span style="font-size: 8pt" class="gyar_mennyi_epitheto"><b>'+ezresito(hany)+'</b>*** építhető most***.</span>';
						if (valasz.premium) {
							if (hany==0) {
								var mikor_lehet=new Array();
								mikor_lehet=mikor_lehet_epiteni(valasz.lista[i].gyarak[j][0]);mikor=mikor_lehet[0];hiany=mikor_lehet[1];
								if (mikor>=0) s+='<br /><span style="font-size: 8pt" class="gyar_hany_kor_mulva">*** kör múlva lesz hozzá elég építőanyag prefix***<b>'+ezresito(mikor)+'</b>*** kör múlva lesz hozzá elég építőanyag***. ***Hiányzik***: '+hiany+'.</span>';
								else s+='<br /><span style="font-size: 8pt" class="gyar_soha_nem_eleg">***<b>Soha nem</b> lesz hozzá elég építőanyag***. ***Hiányzik***: '+hiany+'.</span>';
							}
						}
						//
						s+='<br /><a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',1,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value,1)" title="***sor elejére***" class="link_epit">***sor elejére***</a>';
						s+=' <a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',0,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value,1)" title="***inaktívat sor elejére***" class="link_epit_inaktiv">***inaktívat sor elejére***</a>';
						s+='<br /><a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',1,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value,2)" title="***sor végére***" class="link_epit">***sor végére***</a>';
						s+=' <a href="" onclick="return epitkezes_queue_hozzaad('+aktiv_bolygo+','+valasz.lista[i].gyarak[j][1]+',0,$(\'hany_ujat_epit_'+valasz.lista[i].gyarak[j][1]+'\').value,2)" title="***inaktívat sor végére***" class="link_epit_inaktiv">***inaktívat sor végére***</a>';
						//
						if (gyarak_inputja[valasz.lista[i].gyarak[j][1]].length) s+='<br /><span style="font-size: 8pt"><b>***Fogy***</b>: '+gyarak_inputja[valasz.lista[i].gyarak[j][1]]+'</span>';
						//
						var s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: '+gyarak_outputja[valasz.lista[i].gyarak[j][1]]+'</span>';
						if (valasz.lista[i].gyarak[j][0]==28) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: '+gyarak_outputja[valasz.lista[i].gyarak[j][1]]+'</span>';
						if (valasz.lista[i].gyarak[j][0]==31) {
							if (valasz.lista[i].gyarak[j][1]==84 && valasz.osztaly==3) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[60]+'</span>';
							if (valasz.lista[i].gyarak[j][1]==85 && valasz.osztaly==2) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[61]+'</span>';
							if (valasz.lista[i].gyarak[j][1]==86 && valasz.osztaly==5) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 1000 '+eroforrasok_neve[62]+'</span>';
							if (valasz.lista[i].gyarak[j][1]==90 && valasz.osztaly==1) s_kut='<br /><span style="font-size: 8pt"><b>***Term***</b>: 50 '+eroforrasok_neve[63]+'</span>';
						}
						if (gyarak_outputja[valasz.lista[i].gyarak[j][1]].length) s+=s_kut;
						s+='</td>';
						s+='</tr>';
					}
					s+='</table>';
					s+='</div>';
					s+='</td></tr>';
				}
				s+='</table>';
				$('aktiv_bolygo_uj_epuletek').innerHTML=s;
			} else {
				$('aktiv_bolygo_uj_epuletek').innerHTML='';
			}
		});
	}
	return false;
};
function frissit_bolygo_epulo() {
	if (aktiv_oldal=='bolygo') if (aktiv_bolygo) {
		sendRequest('bolygo_adatok_epulo.php?id='+aktiv_bolygo,function(req) {
			var valasz=json2obj(req.responseText);
			if (valasz.oke) {
				if (parseInt($('term_mikor_span').innerHTML)<parseInt(valasz.term_mikor)) $('term_mikor_frissit_span').innerHTML=' (<a href="" onclick="return frissit_bolygo()">***frissítsd***</a>)';
				$('term_mikor_span').innerHTML=valasz.term_mikor;
				//volt
				if (valasz.volt_gyarak.length>0)
				$('aktiv_bolygo_volt_gyarai').innerHTML='<h2>***Dózerfélben***</h2>'+
				json2table(valasz.volt_gyarak,[],[
				function(sor) {
					var s='';
					s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[3]+');">';
					s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[3]]+'_index-ffr.jpg" />';
					s+='</a>';
					return s;
				},
				function(sor) {
					var s='';
					if (sor[9]>1) s+=sor[9]+'x ';
					s+='<span style="font-weight: bold">'+sor[2]+'</span>';
					if (sor[6]>1) s+=' ('+eroforrasok_neve[sor[5]]+')';
					return s;
				},
				function(sor) {
					return '<img src="img/ikonok/clock.gif" /> '+sec2hm(sor[0]);
				},function(sor) {
					return ((sor[10]>=0)?((sor[10]>100?100:sor[10])+'% '):'')+'<a href="" onclick="return gyar_lerombol_cancel('+sor[7]+')" title="***dózerolás leállítása***"><img src="img/ikonok/cross.gif" /></a>';
				}
				],['34px','167px','100px','60px']);
				else $('aktiv_bolygo_volt_gyarai').innerHTML='';
				//leendo
				if (valasz.leendo_gyarak.length>0)
				$('aktiv_bolygo_leendo_gyarai').innerHTML='<h2>***Épülőfélben***</h2>'+
				json2table(valasz.leendo_gyarak,[],[
				function(sor) {
					var s='';
					s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[3]+');">';
					s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[3]]+'_index-ff.jpg" />';
					s+='</a>';
					return s;
				},
				function(sor) {
					var s='';
					if (sor[9]>1) s+=sor[9]+'x ';
					s+='<span style="font-weight: bold">'+sor[2]+'</span>';
					if (sor[6]>1) s+=' ('+eroforrasok_neve[sor[5]]+')';
					if (sor[8]==0) {
						s+='<br />[***inaktív***]';
						s+=' (<a href="" onclick="return gyar_epit_aktival('+sor[7]+',1)">***aktivál***</a>)';
					} else {
						s+='<br />(<a href="" onclick="return gyar_epit_aktival('+sor[7]+',0)">***inaktivál***</a>)';
					}
					return s;
				},
				function(sor) {
					return '<img src="img/ikonok/clock.gif" /> '+sec2hm(sor[0]);
				},function(sor) {
					return ((sor[10]>=0)?((sor[10]>100?100:sor[10])+'% '):'')+'<a href="" onclick="return gyar_epit_cancel('+sor[7]+')" title="***építkezés leállítása***"><img src="img/ikonok/cross.gif" /></a>';
				}
				],['34px','167px','100px','60px']);
				else $('aktiv_bolygo_leendo_gyarai').innerHTML='';
				//queue
				/*if (valasz.premium>0) {*/
					var eplista_header='';
					eplista_header='<h2>***Építési lista***';
					if (valasz.queue.length>0) eplista_header+=' <a href="" onclick="if (confirm(\'***Biztosan törölni akarod az egész listát?***\')) epitkezes_queue_deleteall('+aktiv_bolygo+');return false;" title="***egész lista törlése***"><img src="img/ikonok/cross.gif" /></a>';
					if (valasz.befagy_eplista>0) eplista_header+='</h2><p style="font-size:8pt">***Az építési listád be van fagyasztva, vagyis nem indulnak el róla építkezések. Ha szeretnéd újra engedélyezni, kattints*** <a href="" onclick="return epitkezes_queue_befagy(0)">***IDE***</a>.</p>';
					else eplista_header+=' <a href="" onclick="return epitkezes_queue_befagy(1)" title="***építési lista befagyasztása***"><img src="img/ikonok/snowman-hat.png" /></a></h2>';
					if (valasz.queue.length>0) {
						var kumme=0;
						for(var i=0;i<valasz.queue.length;i++) {
							kumme+=valasz.queue[i][8];
							valasz.queue[i][18]=kumme;
							if (i==0) valasz.queue[i][19]=1;
							else if (i==valasz.queue.length-1) valasz.queue[i][19]=2;
							else valasz.queue[i][19]=0;
						}
						//
						var kovetkezo_epites='';
						var hany=hanyat_lehet_epiteni_biztonsagosan(valasz.queue[0][2]);
						if (hany==0) {
							kovetkezo_epites+='<p style="font-size:8pt">';
							var mikor_lehet=new Array();
							mikor_lehet=mikor_lehet_epiteni_biztonsagosan(valasz.queue[0][2]);mikor=mikor_lehet[0];hiany=mikor_lehet[1];
							if (mikor>=0) kovetkezo_epites+='*** kör múlva lesz elég építőanyag a következő építéshez, hogy maradjon még utána elég készlet a termeléshez prefix***<b>'+ezresito(mikor)+'</b>*** kör múlva lesz elég építőanyag a következő építéshez, hogy maradjon még utána elég készlet a termeléshez***. ***Hiányzik***: '+hiany+'.';
							else kovetkezo_epites+='***<b>Soha nem</b> lesz elég építőanyag a következő építéshez***. ***Hiányzik***: '+hiany+'.';
							kovetkezo_epites+='</p>';
						}
						//
						kovetkezo_epites+='<p style="font-size:8pt">***A teljes lista építéséhez hiányzik***: '+mikor_lehet_megepiteni_a_teljes_listat_biztonsagosan(valasz.queue_darabszam)+'.</p>';
						//
						$('aktiv_bolygo_epit_queue').innerHTML=eplista_header+kovetkezo_epites+
						json2table(valasz.queue,['','***név***','***munkaerő***',''],[
						function(sor) {
							var s='';
							s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[2]+');">';
							s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[2]]+'_index-ff.jpg" />';
							s+='</a>';
							return s;
						},
						function(sor) {
							var s='';
							if (sor[7]>1) s+=sor[7]+'x ';
							s+='<span style="font-weight: bold">'+sor[1]+'</span>';
							if (sor[4]>1) s+=' ('+eroforrasok_neve[sor[3]]+')';
							if (sor[6]==0) {
								s+='<br />[***inaktív***]';
								s+=' (<a href="" onclick="return epitkezes_queue_aktival('+sor[5]+',1)">***aktivál***</a>)';
							} else {
								s+='<br />(<a href="" onclick="return epitkezes_queue_aktival('+sor[5]+',0)">***inaktivál***</a>)';
							}
							return s;
						},
						function(sor) {
							return sor[18];
						},
						function(sor) {
							var s='';
							if (sor[19]!=1) s+='<a href="" onclick="return epitkezes_queue_atsorol('+sor[5]+',-1)" title="***előresorol***">';
							s+='<img src="img/ikonok/arrow_up'+((sor[19]==1)?'-ff':'')+'.gif" />';
							if (sor[19]!=1) s+='</a>';
							if (sor[19]!=2) s+='<a href="" onclick="return epitkezes_queue_atsorol('+sor[5]+',1)" title="***hátrasorol***">';
							s+='<img src="img/ikonok/arrow_down'+((sor[19]==2)?'-ff':'')+'.gif" />';
							if (sor[19]!=2) s+='</a>';
							s+='<a href="" onclick="return epitkezes_queue_szerk_kerdez('+sor[5]+','+sor[7]+')" title="***módosít***"><img src="img/ikonok/szerk.gif" /></a>';
							s+='<a href="" onclick="return epitkezes_queue_torol('+sor[5]+')" title="***töröl***"><img src="img/ikonok/cross.gif" /></a>';
							return s;
						}
						],['34px','167px'],null,null,['left','left','right','right']);
					} else $('aktiv_bolygo_epit_queue').innerHTML=eplista_header;
				/*} else $('aktiv_bolygo_epit_queue').innerHTML='';*/
			}
		});
	}
	return false;
};

function frissit_flotta() {
	$('tolto_ikon').style.display='block';
	sendRequest('flotta_adatok.php?id='+aktiv_flotta,function(req) {
		$('tolto_ikon').style.display='none';
		$('aktiv_flotta_alapadatai').innerHTML='';
		var valasz=json2obj(req.responseText);
		if (valasz.letezik) {
			aktiv_flotta_x=valasz.x;
			aktiv_flotta_y=valasz.y;
			aktiv_flotta_tied=valasz.tied;
			jump_to_aktiv_flotta();
			//
			var resztulajok='';
			for(var i=0;i<valasz.resztulajok.length;i++) {
				if (i>0) resztulajok+=', ';
				resztulajok+=Math.floor(valasz.resztulajok[i][2]/10)+'***,***'+(valasz.resztulajok[i][2]%10)+'% <a href="#" onclick="return user_katt('+valasz.resztulajok[i][0]+')">'+valasz.resztulajok[i][1]+'</a>';
			}
			if (resztulajok!='') resztulajok=' <span style="font-size:8pt">['+resztulajok+']</span>';
			if (valasz.letezik>1) {
				var reszflottak='';
				for(var i=0;i<valasz.reszflottahajok.length;i++) {
					reszflottak+='<h3><a href="#" onclick="return user_katt('+valasz.reszflottahajok[i][0]+')">'+valasz.reszflottahajok[i][1]+'</a> ('+Math.floor(valasz.reszflottahajok[i][2]/10)+'***,***'+(valasz.reszflottahajok[i][2]%10)+'%)';
					if (valasz.reszflottahajok[i][4]) {//te reszflottad
						if (valasz.tulaj_id!=valasz.reszflottahajok[i][0]) {//kivalas
							reszflottak+=' <span style="font-size:8pt">(<a href="#" onclick="return flotta_kivonasa()">***részflotta kivonása***</a>)</span>';
						}
					} else {//mas reszflottaja
						if (valasz.tulaj_id==valasz.te) {//atruhazas
							reszflottak+=' <span style="font-size:8pt">(<a href="#" onclick="return flotta_iranyitas_atadasa('+valasz.reszflottahajok[i][0]+')">***irányítás átruházása***</a>)</span>';
						}
					}
					reszflottak+='</h3>';
					reszflottak+=json2table(hajoszuro(valasz.reszflottahajok[i][3]),['','','***darab***','***egyenérték***'],[
					function(sor) {return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.gif" /></a>';},
					function(sor) {return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';},
					function(sor) {return '<span style="font-weight: bold">'+szazadresz(sor[2])+'</span>';},
					function(sor) {return szazadresz(sor[3]);}
					],['34px','150px','150px','150px'],null,null,['center','left','right','right']);
				}
				//
				var flotta_nav='';
				if (valasz.tied) {
					flotta_nav+='<a href="" onclick="return flotta_katt('+valasz.elozo+');" title="***előző***"><img src="img/resultset_previous.gif" /></a>';
					flotta_nav+=' <a href="" onclick="return flotta_katt('+valasz.kovetkezo+');" title="***következő***"><img src="img/resultset_next.gif" /></a>';
					var atnev='<a href="" onclick="return flotta_rename_kerdez('+aktiv_flotta+',\''+(valasz.esc_nev)+'\');" title="***flotta átnevezése***"><img src="img/ikonok/szerk.gif" /></a>';
					if (valasz.statusz==1) {
						if (valasz.moral<1000) {
							atnev+=' <a href="" style="font-size:8pt" title="***flotta visszavonása bolygóra***" onclick="if (confirm(\'***Biztosan vissza akarod vonni ezt a flottádat a(z) ***'+valasz.bolygo.nev+'*** bolygóra?***';
							atnev+=' (***a morál miatt csak ***'+Math.floor(valasz.moral/10)+'***,***'+(valasz.moral%10)+'***%-osan tudod***)';
							atnev+='\')) flotta_visszavonasa();return false"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[valasz.bolygo.osztaly-1]+'.gif" /></a>';
						} else {
							atnev+=' <a href="" style="font-size:8pt" title="***flotta visszavonása bolygóra***" onclick="return flotta_visszavonasa()"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[valasz.bolygo.osztaly-1]+'.gif" /></a>';
						}
					}
					//flotta kozosbe/bol ki
					if (valasz.kozos) {
						atnev+=' <a href="" title="***flotta közösből való kivétele***" onclick="return flotta_kozosbol_ki('+aktiv_flotta+')"><img src="img/ikonok/user.gif" /></a>';
					} else {
						atnev+=' <a href="" title="***flotta közösbe adása***" onclick="return flotta_kozosbe('+aktiv_flotta+')"><img src="img/ikonok/group.gif" /></a>';
					}
					//flotta torlese, amig nem lesz kulon feladas es scuttling
					atnev+=' <a href="" title="***flotta törlése***" onclick="if (confirm(\'***Biztosan törölni akarod ezt a flottádat?***\')) flotta_torlese();return false"><img src="img/ikonok/cross.gif" /></a>';
					//
					$('aktiv_flotta_neve').innerHTML=flotta_nav+' '+valasz.nev+' '+atnev;
					var flotta_statusz='';
					switch(valasz.statusz) {
						case 1:flotta_statusz='*** felett állomásozik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.bolygo.id+',1);">'+valasz.bolygo.nev+'</a>*** felett állomásozik***';break;
						case 2:flotta_statusz='***Várakozik***';break;
						case 3:case 4:flotta_statusz='*** között járőrözik prefix***'+ykoordinata(valasz.bazis_y)+',&nbsp;'+xkoordinata(valasz.bazis_x)+' ***és*** '+ykoordinata(valasz.cel_y)+',&nbsp;'+xkoordinata(valasz.cel_x)+'*** között járőrözik***';break;
						case 5:flotta_statusz='*** felé tart prefix***'+ykoordinata(valasz.cel_y)+',&nbsp;'+xkoordinata(valasz.cel_x)+'*** felé tart***';break;
						case 6:flotta_statusz='*** felé tart prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.cel_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.cel_bolygo.id+',1);">'+valasz.cel_bolygo.nev+'</a>*** felé tart***';break;
						case 7:flotta_statusz='*** felé tart támadólag prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.cel_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.cel_bolygo.id+',1);">'+valasz.cel_bolygo.nev+'</a>*** felé tart támadólag***';break;
						case 8:flotta_statusz='*** ellen támad prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.cel_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.cel_bolygo.id+',1);">'+valasz.cel_bolygo.nev+'</a>*** ellen támad***';break;
						case 9:flotta_statusz='*** felé tart portyázni prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.cel_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.cel_bolygo.id+',1);">'+valasz.cel_bolygo.nev+'</a>*** felé tart portyázni***';break;
						case 10:flotta_statusz='*** ellen portyázik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.cel_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.cel_bolygo.id+',1);">'+valasz.cel_bolygo.nev+'</a>*** ellen portyázik***';break;
						case 11:flotta_statusz='***Visszavonul***';break;
						case 12:flotta_statusz='*** felé tart prefix***<a href="" onclick="return flotta_katt('+valasz.cel_flotta.id+')">'+valasz.cel_flotta.nev+'</a>*** felé tart***';break;
						case 13:flotta_statusz='*** felé tart támadólag prefix***<a href="" onclick="return flotta_katt('+valasz.cel_flotta.id+')">'+valasz.cel_flotta.nev+'</a>*** felé tart támadólag***';break;
						case 14:flotta_statusz='*** ellen támad prefix***<a href="" onclick="return flotta_katt('+valasz.cel_flotta.id+')">'+valasz.cel_flotta.nev+'</a>*** ellen támad***';break;
					}
					if (valasz.statusz>=3) flotta_statusz+=' (<a href="" onclick="return flotta_parancs_allj('+aktiv_flotta+')">***Állj!***</a>)';
					var tulaj='';
					if (valasz.tulaj_id>0) tulaj='<a href="" onclick="return user_katt('+valasz.tulaj_id+')">'+valasz.tulaj+'</a>';
					else tulaj=valasz.tulaj;
					if (valasz.kozos) {
						tulaj+=' (***közös***)';
					}
					$('aktiv_flotta_alapadatai').innerHTML=json2table([
					['***Tulajdonos***',tulaj+resztulajok],
					['***Pozíció***','<a href="" onclick="jump_to_aktiv_flotta();return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(valasz.y)+',&nbsp;'+xkoordinata(valasz.x)+'</a>'],
					['***Utolsó parancsot kiadta***',(valasz.uccso_parancs_by_id>0)?('<a href="" onclick="return user_katt('+valasz.uccso_parancs_by_id+')">'+valasz.uccso_parancs_by+'</a>'):'?'],
					['***Bázis***',(valasz.bazis_bolygo.id?('<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+valasz.bazis_bolygo.osztaly+'" onclick="return bolygo_katt('+valasz.bazis_bolygo.id+',1)">'+valasz.bazis_bolygo.nev+'</a>'+' (<a href="" onclick="return flotta_parancs_vissza('+aktiv_flotta+')">***Visszavonulás!***</a>) '):'')+
					'<form style="display: inline" onsubmit="$(\'uj_bazis_submit_ikon\').focus();return flotta_rebazis('+aktiv_flotta+')"><input type="text" id="uj_bazis_nev" class="bolygonev" /> <input type="image" id="uj_bazis_submit_ikon" src="img/ikonok/bullet_go.gif" title="***Új bázis beállítása***"></form>'],
					['***Tevékenység***',flotta_statusz],
					['***Parancs***','<form style="display: inline" onsubmit="return false"><input type="text" id="parancs_celpont_nev" class="bolygonev" /> <a href="" onclick="return flotta_parancs_multi(1)">***Menj***</a> / <a href="" onclick="return flotta_parancs_multi(2)">***Járőrözz***</a> / <a href="" onclick="return flotta_parancs_multi(3)">***Támadj***</a> / <a href="" onclick="return flotta_parancs_multi(4)">***Portyázz***</a></form> (***koordináta***/***bolygó***)<br /><br /><form style="display: inline" onsubmit="return false"><input type="text" id="parancs_celflotta_nev" class="bolygonev" /> <a href="" onclick="return flotta_parancs_multi(5)">***Menj***</a> / <a href="" onclick="return flotta_parancs_multi(6)">***Támadj***</a></form> (***flotta***)'],
					['***Távolság***',valasz.tavolsag+' pc'],
					['***Hátralévő idő***',valasz.hatralevo_ido+' m'],
					['***Saját területen***',valasz.hazai_palyan?'***igen***':'***nem***'],
					['***Ellenséges területen***',valasz.piros_teruleten?'***igen***':'***nem***'],
					['***Morál***',flottamoralcsik(valasz.moral)],
					['***Összérték***',ezresito(Math.floor(valasz.ossz_ertek/100))+'***,***'+((valasz.ossz_ertek%100<10)?'0':'')+(valasz.ossz_ertek%100)],
					['***Tapasztalat***',Math.floor(valasz.tapasztalat/100)+'***,***'+((valasz.tapasztalat%100<10)?'0':'')+(valasz.tapasztalat%100)+' ***TP***'],
					['***Sebesség***',Math.round(valasz.sebesseg/2)+' pc/m'],
					['***Látótávolság***',valasz.latotav+' pc'],
					['***Rejtőzés***',valasz.rejtes+' pc']
					],[]);
					if (valasz.tulaj_id!=valasz.te) {//nem a sajat bolygoid, hanem akie a flotta (kezeles vagy kozos)
						new actb($('uj_bazis_nev'),'ajax_autocomplete_valaki_bolygoi',0,valasz.tulaj_id);
					} else {//sajat bolygok
						new actb($('uj_bazis_nev'),'ajax_autocomplete_sajat_bolygok',0);
					}
					new actb($('parancs_celpont_nev'),'ajax_autocomplete_bolygok',0);
					new actb($('parancs_celflotta_nev'),'ajax_autocomplete_flottak',0);
					$('aktiv_flotta_urhajoi').innerHTML='<form onsubmit="return false">'+
					json2table(hajoszuro(valasz.hajok),['','','***darab***','<a href="" onclick="return hany_hajot_rendez_mindet(\'flotta\')">(***mindet***)</a>','***egyenérték***'],[
					function(sor) {return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.gif" /></a>';},
					function(sor) {return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';},
					function(sor) {return '<span style="font-weight: bold" id="max_flotta_hajoszam_'+sor[0]+'" title="'+sor[1]+'">'+szazadresz(sor[2])+'</span>';},
					function(sor) {return '<input type="text" tabindex="'+sor[0]+'" class="hajoszam" id="flotta_hany_hajot_rendez_'+sor[0]+'" /> (<a href="" onclick="$(\'flotta_hany_hajot_rendez_'+sor[0]+'\').value='+sor[1]+';return false;">max</a>)';},
					function(sor) {return szazadresz(sor[3]);}
					],['34px','150px','150px','100px','150px'],null,null,['center','left','right','center','right'])+
					'</form>';
					$('aktiv_flotta_kornyezete').innerHTML='<form onsubmit="return false">'+
					json2table(valasz.flottak,[],[
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+(sor[2]?'sajat':'szovi')+'.gif" /></a>';},
					function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+(sor[2]?'':' (<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>)');},
					function(sor) {return '<a href="" onclick="return flotta_atrendezes(\'flotta\','+sor[0]+')" title="***Kiválasztott hajók átpakolása ebbe a flottába***"><img src="img/ikonok/bepakol.gif" /></a>';}
					],['34px','328px','50px'])+
					json2table([[
					'<img src="img/ikonok/flotta_ikon_sajat.gif" />',
					'<input type="text" class="ujflottanev" id="flotta_uj_flotta_nev" /> (***új***)',
					'<a href="" onclick="return flotta_atrendezes(\'flotta\',0)" title="***Kiválasztott hajók átpakolása ebbe az új flottába***"><img src="img/ikonok/bepakol.gif" /></a>']],
					[],[0,1,2],['34px','328px','50px'])+
					'</form>';
					$('aktiv_flotta_egyeb_kornyezete').innerHTML=json2table(valasz.idegen_flottak,[],[
					function(sor) {
						var s='semli';
						switch(sor[2]) {
							case 1:s='hadban';break;
							case 2:s='beke';break;
							case 3:s='mnt';break;
						}
						if (sor[3]<0) s='zanda';
						if (sor[3]==0) s='npc';
						return '<a href="#" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+s+'.gif" /></a>';
					},
					function(sor) {
						var s='';
						if (sor[3]<0) s='Zandagort';
						else if (sor[3]==0) s='***kalózok***';
						else s='<a href="#" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>';
						return '<a href="#" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a> ('+s+')';
					}
					],['34px','328px']);
					$('aktiv_flotta_sajat_reszletei').style.display='block';
					//
					$('aktiv_flotta_reszflottai').innerHTML=reszflottak;
				} else {
					var poz_hajok=hajoszuro(valasz.hajok);
					var csataszim='';
					for(var i=0;i<poz_hajok.length;i++) {
						if (csataszim!='') csataszim+='&';
						csataszim+='vedo'+poz_hajok[i][0]+'='+poz_hajok[i][1];
					}
					$('aktiv_flotta_neve').innerHTML=flotta_nav+' '+valasz.nev+' <span style="font-size:8pt">(<a href="***zanda_homepage_url_csataszim***.?'+csataszim+'" target="_blank">***csataszimulátor***</a>)</span>';
					var tulaj='';
					if (valasz.tulaj_id>0) tulaj='<a href="" onclick="return user_katt('+valasz.tulaj_id+')">'+valasz.tulaj+'</a>';
					else tulaj=valasz.tulaj;
					$('aktiv_flotta_alapadatai').innerHTML=json2table([
					['***Tulajdonos***',tulaj+resztulajok],
					['***Pozíció***','<a href="" onclick="jump_to_aktiv_flotta();return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(valasz.y)+',&nbsp;'+xkoordinata(valasz.x)+'</a>'],
					['***Utolsó parancsot kiadta***',(valasz.uccso_parancs_by_id>0)?('<a href="" onclick="return user_katt('+valasz.uccso_parancs_by_id+')">'+valasz.uccso_parancs_by+'</a>'):'?'],
					['***Összérték***',ezresito(Math.floor(valasz.ossz_ertek/100))+'***,***'+((valasz.ossz_ertek%100<10)?'0':'')+(valasz.ossz_ertek%100)],
					['***Sebesség***',Math.round(valasz.sebesseg/2)+' pc/m'],
					['***Látótávolság***',valasz.latotav+' pc'],
					['***Rejtőzés***',valasz.rejtes+' pc']
					],[]);
					$('aktiv_flotta_urhajoi').innerHTML=json2table(hajoszuro(valasz.hajok),['','','***darab***','***egyenérték***'],[
					function(sor) {return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.gif" /></a>';},
					function(sor) {return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';},
					function(sor) {return '<span style="font-weight: bold">'+szazadresz(sor[2])+'</span>';},
					function(sor) {return szazadresz(sor[3]);}
					],['34px','150px','150px','150px'],null,null,['center','left','right','right']);
					$('aktiv_flotta_sajat_reszletei').style.display='none';
					//
					$('aktiv_flotta_reszflottai').innerHTML=reszflottak;
				}
				$('aktiv_flotta_reszletei').style.display='block';
			} else {
				$('aktiv_flotta_reszletei').style.display='none';
				$('aktiv_flotta_neve').innerHTML=valasz.nev;
				var tulaj='';
				if (valasz.tulaj_id>0) tulaj='<a href="" onclick="return user_katt('+valasz.tulaj_id+')">'+valasz.tulaj+'</a>';
				else tulaj=valasz.tulaj;
				$('aktiv_flotta_alapadatai').innerHTML=json2table([
				['***Tulajdonos***',tulaj+resztulajok],
				['***Pozíció***','<a href="" onclick="jump_to_aktiv_flotta();return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(valasz.y)+',&nbsp;'+xkoordinata(valasz.x)+'</a>'],
				['***Utolsó parancsot kiadta***',(valasz.uccso_parancs_by_id>0)?('<a href="" onclick="return user_katt('+valasz.uccso_parancs_by_id+')">'+valasz.uccso_parancs_by+'</a>'):'?']
				],[]);
			}
		} else {
			set_aktiv_flotta(0,0);
			$('aktiv_flotta_reszletei').style.display='none';
			$('aktiv_flotta_neve').innerHTML='';
		}
	});
	return false;
};

function frissit_komm(melyik_levelek) {
	$('tolto_ikon').style.display='block';
	var x='';
	if (melyik_levelek==1) if ($('level_kereso_div').style.display=='block') {
		x+='&targy='+encodeURIComponent($('level_kereso_input_targy').value);
		x+='&felado='+encodeURIComponent($('level_kereso_input_felado').value);
		x+='&mappa='+encodeURIComponent($('level_kereso_input_mappa').value);
	}
	var lev_old=0;
	if (melyik_levelek==1) lev_old=levelek_oldal;
	else if (melyik_levelek==2) lev_old=levelek_oldal2;
	else lev_old=levelek_oldal3;
	sendRequest('levelek.php?offset='+lev_old+'&felado_szuro='+melyik_levelek+x,function(req) {
		$('tolto_ikon').style.display='none';
		if (melyik_levelek==1) $('levelek_div').innerHTML='';else $('levelek_div'+melyik_levelek).innerHTML='';
		var valasz=json2obj(req.responseText);
		if (melyik_levelek==3) {//csatajelentesek
			var csata_id=0;
			var csat_id_lista='';
			for(var i=0;i<valasz.levelek.length;i++) {
				if (valasz.levelek[i][0]!=csata_id) {
					csata_id=valasz.levelek[i][0];
					if (csat_id_lista!='') csat_id_lista+=',';
					csat_id_lista+=valasz.levelek[i][0];
				}
			}
			//
			var s='';
			s+='<br />';
			s+='<table style="width:750px">';
			s+='<tr style="background:rgb(120,120,120)">';
			s+='<td style="text-align:left"><a href="" onclick="return osszes_csatajelentest_torol(\''+csat_id_lista+'\')"><img src="img/ikonok/cross.gif" /> ***az oldalon lévő csatajelentések törlése***</a></td>';
			s+='<td style="text-align:right"><a href="" onclick="return osszes_csatajelentest_torol(\'\')"><img src="img/ikonok/cross.gif" /> ***összes csatajelentés törlése***</a></td>';
			s+='</tr>';
			s+='</table>';
			s+='<br />';
			s+='<ul class="csj_csata_lista">';
			csata_id=0;
			for(var i=0;i<valasz.levelek.length;i++) {
				if (valasz.levelek[i][0]!=csata_id) {
					if (csata_id>0) s+='</ul></li>';
					s+='<li>';
					if (valasz.levelek[i][17]==0) s+='<b>';
					s+=valasz.levelek[i][3]+' <a href="#" onclick="jump_to_xy('+valasz.levelek[i][1]+','+valasz.levelek[i][2]+');return oldal_nyit(\'terkep\');">'+ykoordinata(valasz.levelek[i][2])+', '+xkoordinata(valasz.levelek[i][1])+'</a>, ***résztvett***: '+szazadresz(valasz.levelek[i][4])+', ***megsemmisült***: '+szazadresz(valasz.levelek[i][5]);
					if (valasz.levelek[i][17]==0) s+='</b>';
					s+=' (<a href="#" id="csj_megnyito_link_'+valasz.levelek[i][0]+'" onclick="return csatajelentest_megnyit('+valasz.levelek[i][0]+')">***megnyit***</a><a href="#" style="display:none" id="csj_toggle_link_'+valasz.levelek[i][0]+'" onclick="return csatajelentes_toggle('+valasz.levelek[i][0]+')">***becsuk***</a>)';
					s+=' <a href="" onclick="return csatajelentest_torol('+valasz.levelek[i][0]+')" title="***csatajelentés törlése***"><img src="img/ikonok/cross.gif" /></a>';
					s+='<ul class="csj_flotta_lista">';
					csata_id=valasz.levelek[i][0];
				}
				s+='<li>';
				s+='<a href="" class="flotta_lista_elem menu_flotta_';
				switch(valasz.levelek[i][16]) {
					case -1:s+='zanda';break;
					case 0:s+='npc';break;
					case 1:s+='sajat';break;
					case 2:s+='szovi';break;
					case 3:s+='semli';break;
					case 4:s+='hadban';break;
					case 5:s+='beke';break;
					case 6:s+='mnt';break;
				}
				s+='" onclick="return flotta_katt('+valasz.levelek[i][6]+')">'+valasz.levelek[i][7]+'</a>';
				s+=', ***tulaj***: ';
				if (valasz.levelek[i][8]>0) s+='<a href="" onclick="return user_katt('+valasz.levelek[i][8]+')">';
				s+=valasz.levelek[i][9];
				if (valasz.levelek[i][8]>0) s+='</a>';
				if (valasz.levelek[i][10]>0) s+=' (<a href="" onclick="return szovetseg_katt('+valasz.levelek[i][10]+')">'+valasz.levelek[i][11]+'</a>)';
				if (valasz.levelek[i][18]>0) if (valasz.levelek[i][18]!=valasz.levelek[i][8]) s+=', ***irányító***: <a href="" onclick="return user_katt('+valasz.levelek[i][18]+')">'+valasz.levelek[i][19]+'</a>';
				s+=', ***egyenérték***: '+szazadresz(valasz.levelek[i][14])+' &gt; '+szazadresz(valasz.levelek[i][15])+' (&Delta;='+szazadresz(valasz.levelek[i][14]-valasz.levelek[i][15])+')';
				s+='<span id="csj_'+valasz.levelek[i][0]+'_'+valasz.levelek[i][6]+'"></span>';
				s+='</li>';
			}
			s+='</ul></li></ul>';
		} else if (melyik_levelek==2) {//rendszeruzik
			var lev_id_lista='';
			for(var i=0;i<valasz.levelek.length;i++) {
				if (i>0) lev_id_lista+=',';
				lev_id_lista+=valasz.levelek[i][0];
			}
			var s='';
			s+='<br />';
			s+='<table style="width:750px">';
			s+='<tr style="background:rgb(120,120,120)">';
			s+='<td style="text-align:left"><a href="" onclick="return osszes_levelet_torol(\''+lev_id_lista+'\')"><img src="img/ikonok/cross.gif" /> ***az oldalon lévő események törlése***</a></td>';
			s+='<td style="text-align:right"><a href="" onclick="return osszes_levelet_torol(\'\')"><img src="img/ikonok/cross.gif" /> ***összes esemény törlése***</a></td>';
			s+='</tr>';
			s+='</table>';
			s+='<br />';
			for(var i=0;i<valasz.levelek.length;i++) {
				s+='<br />';
				s+='<table style="width:750px">';
				s+='<tr style="background:rgb(100,100,100)">';
				s+='<td style="text-align:left;width:700px">';
				if (valasz.levelek[i][4]==0) s+='<b>';
				s+=valasz.levelek[i][1]+' '+valasz.levelek[i][2];
				if (valasz.levelek[i][4]==0) s+='</b>';
				s+='</td>';
				s+='<td style="text-align:right"><a href="" onclick="return rendszerlevelet_torol('+valasz.levelek[i][0]+')" title="***üzenet törlése***"><img src="img/ikonok/cross.gif" /></a></td>';
				s+='</tr>';
				s+='<tr>';
				s+='<td colspan="2" style="text-align:justify">';
				if (valasz.levelek[i][4]==0) s+='<b>';
				s+=valasz.levelek[i][3];
				if (valasz.levelek[i][4]==0) s+='</b>';
				s+='</td>';
				s+='</tr>';
				s+='</table>';
			}
		} else {//szemelyes uzik
			if ($('level_kereso_span')) $('level_kereso_span').style.display='inline';
			$('egy_konkret_level').style.display='block';
			//
			var s='<table>';
			var klassz2='';
			s+='<tr><th style="width: 400px">***Tárgy***</th><th style="width: 200px">***Feladó/Címzett***</th><th style="width: 120px">***Dátum***</th></tr>';
			for(var i=0;i<valasz.levelek.length;i++) {
				s+='<tr id="level_sor'+valasz.levelek[i][0]+'"';
				s+=' style="cursor: pointer"';
				klassz2='';
				if (i%2==0) {
					if (valasz.levelek[i][0]==aktiv_level) klassz2+='aktiv_level';
					if (valasz.levelek[i][5]==0) klassz2+=' olvasatlan_level';
					s+=' class="paros_level_sor'+klassz2+'"';
					s+=' onmouseover="this.className=\'level_sor_hover'+klassz2+'\'" onmouseout="this.className=\'paros_level_sor'+klassz2+'\'"';
				} else {
					if (valasz.levelek[i][0]==aktiv_level) klassz2+='aktiv_level';
					if (valasz.levelek[i][5]==0) klassz2+=' olvasatlan_level';
					s+=' class="'+klassz2+'"';
					s+=' onmouseover="this.className=\'level_sor_hover'+klassz2+'\'" onmouseout="this.className=\''+klassz2+'\'"';
				}
				s+=' onclick="return levelet_megnyit('+valasz.levelek[i][0]+')"';
				s+='>';
				s+='<td>'+valasz.levelek[i][4]+'</td>';
				s+='<td><img src="img/ikonok/mail_'+(valasz.levelek[i][6]==0?'incoming':'outgoing')+'.gif" /> '+valasz.levelek[i][3]+'</td>';
				s+='<td>'+valasz.levelek[i][1]+'</td>';
				s+='</tr>';
			}
			s+='</table>';
		}
		if (melyik_levelek==1) $('levelek_div').innerHTML=s;else $('levelek_div'+melyik_levelek).innerHTML=s;
		if (valasz.olvasatlan_levelek_szama_bontas[0]) $('olvasatlan_levelek_szama').innerHTML=' ('+valasz.olvasatlan_levelek_szama_bontas[0]+')';else $('olvasatlan_levelek_szama').innerHTML='';
		if (valasz.olvasatlan_levelek_szama_bontas[1]) $('olvasatlan_levelek_szama2').innerHTML=' ('+valasz.olvasatlan_levelek_szama_bontas[1]+')';else $('olvasatlan_levelek_szama2').innerHTML='';
		if (valasz.olvasatlan_levelek_szama_bontas[2]) $('olvasatlan_levelek_szama3').innerHTML=' ('+valasz.olvasatlan_levelek_szama_bontas[2]+')';else $('olvasatlan_levelek_szama3').innerHTML='';
		for(var i=1;i<=3;i++) {
			if (valasz.olvasatlan_levelek_szama_bontas[i-1]) {
				$('olv_lev_'+i).innerHTML=' ('+valasz.olvasatlan_levelek_szama_bontas[i-1]+'/'+valasz.levelek_szama_bontas[i-1]+')';
			} else {
				$('olv_lev_'+i).innerHTML=' (0/'+valasz.levelek_szama_bontas[i-1]+')';
			}
		}
		//
		levelek_szama=valasz.levelek_szama_bontas[0];
		if (levelek_oldal>0) $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal+10<levelek_szama) $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span').innerHTML=Math.ceil(levelek_szama/10);
		$('level_oldalszam_span').innerHTML=Math.round(levelek_oldal/10)+1;
		//
		levelek_szama2=valasz.levelek_szama_bontas[1];
		if (levelek_oldal2>0) $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal2+10<levelek_szama2) $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span2').innerHTML=Math.ceil(levelek_szama2/10);
		$('level_oldalszam_span2').innerHTML=Math.round(levelek_oldal2/10)+1;
		//
		levelek_szama3=valasz.levelek_szama_bontas[2];
		if (levelek_oldal3>0) $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal3+10<levelek_szama3) $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span3').innerHTML=Math.ceil(levelek_szama3/10);
		$('level_oldalszam_span3').innerHTML=Math.round(levelek_oldal3/10)+1;
		//
	});
	return false;
};
function levelek_elozo_oldal(mit) {
	if (mit==1) {
		if (levelek_oldal>0) levelek_oldal=levelek_oldal-10;
	} else if (mit==2) {
		if (levelek_oldal2>0) levelek_oldal2=levelek_oldal2-10;
	} else {
		if (levelek_oldal3>0) levelek_oldal3=levelek_oldal3-10;
	}
	return frissit_komm(mit);
};
function levelek_kovetkezo_oldal(mit) {
	if (mit==1) {
		if (levelek_oldal+10<levelek_szama) levelek_oldal=levelek_oldal+10;
	} else if (mit==2) {
		if (levelek_oldal2+10<levelek_szama2) levelek_oldal2=levelek_oldal2+10;
	} else {
		if (levelek_oldal3+10<levelek_szama3) levelek_oldal3=levelek_oldal3+10;
	}
	return frissit_komm(mit);
};
function levelet_megnyit(id) {
	sendRequest('level.php?id='+id,function(req) {
		$('egy_konkret_level').innerHTML='';
		var valasz=json2obj(req.responseText);
		aktiv_level=0;
		if (valasz.oke) {
			aktiv_level=id;
			var s='';
			s+='<br /><hr align="left" style="width: 50%; margin-bottom: 10px" />';
			s+='<h3>***Tárgy***: <span id="megnyitott_level_targy">'+valasz.targy+'</span>';
			if (valasz.felado_id) s+=' <a href="" onclick="window.open(\'level_irasa.php?id='+id+'\',\'\',\'width=600,height=350\');return false;" style="font-size:8pt"><img src="img/ikonok/mail_reply.gif" /> ***válasz***</a> <a href="" onclick="window.open(\'level_irasa.php?id='+id+'&to_all=1\',\'\',\'width=600,height=350\');return false;" style="font-size:8pt"><img src="img/ikonok/mail_reply.gif" /> ***válasz mindenkinek***</a>';
			s+=' <a href="" onclick="return levelet_torol('+id+');" style="font-size:8pt"><img src="img/ikonok/cross.gif" /> ***üzenet törlése***</a>';
			s+='</h3>';
			if (valasz.felado_id) s+='<h3>***Feladó***: <a href="" onclick="return user_katt('+valasz.felado_id+')" id="megnyitott_level_felado">'+valasz.felado_nev+'</a></h3>';
			else s+='<h3>***Feladó***: <span id="megnyitott_level_felado">'+valasz.felado_nev+'</span></h3>';
			s+='<h3>***Dátum***: '+valasz.ido+'</h3>';
			s+='<h3>***Címzettek***: <span id="megnyitott_level_cimzettek">'+valasz.cimzettek+'</span></h3>';
			s+='<h3>***Mappa***: <span id="megnyitott_level_mappa">'+valasz.mappa+'</span>'+
			((valasz.premium==2)?(' <form style="display:inline" onsubmit="return levelet_atmappaz('+id+')"><span style="font-size:10pt">(***átmozgatás ebbe a mappába***: <input type="text" id="megnyitott_level_ujmappa" class="szovegmezo10" style="width:200px" />)</span></form>'):'')
			+'</h3>';
			s+='<hr align="left" style="width: 30%; margin-bottom: 10px" />';
			s+='<p>'+valasz.uzenet_br+'</p>';
			$('egy_konkret_level').innerHTML=s;
			if ($('megnyitott_level_ujmappa')) new actb($('megnyitott_level_ujmappa'),'ajax_autocomplete_mappak',0);
			frissit_komm(1);
		}
	});
	return false;
};
function frissit_olvasatlan_levelek_szama() {
	sendRequest('olvasatlan_levelek_szama.php',function(req) {
		var valasz=json2obj(req.responseText);
		//
		if (valasz.db_bontas[0]) $('olvasatlan_levelek_szama').innerHTML=' ('+valasz.db_bontas[0]+')';else $('olvasatlan_levelek_szama').innerHTML='';
		levelek_szama=valasz.db_ossz_bontas[0];
		if (levelek_oldal>0) $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal+10<levelek_szama) $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span').innerHTML=Math.ceil(levelek_szama/10);
		$('level_oldalszam_span').innerHTML=Math.round(levelek_oldal/10)+1;
		//
		if (valasz.db_bontas[1]) $('olvasatlan_levelek_szama2').innerHTML=' ('+valasz.db_bontas[1]+')';else $('olvasatlan_levelek_szama2').innerHTML='';
		levelek_szama2=valasz.db_ossz_bontas[1];
		if (levelek_oldal2>0) $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal2+10<levelek_szama2) $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span2').innerHTML=Math.ceil(levelek_szama2/10);
		$('level_oldalszam_span2').innerHTML=Math.round(levelek_oldal2/10)+1;
		//
		if (valasz.db_bontas[2]) $('olvasatlan_levelek_szama3').innerHTML=' ('+valasz.db_bontas[2]+')';else $('olvasatlan_levelek_szama3').innerHTML='';
		levelek_szama3=valasz.db_ossz_bontas[2];
		if (levelek_oldal3>0) $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal3+10<levelek_szama3) $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span3').innerHTML=Math.ceil(levelek_szama3/10);
		$('level_oldalszam_span3').innerHTML=Math.round(levelek_oldal3/10)+1;
		//
		for(var i=1;i<=3;i++) {
			if (valasz.db_bontas[i-1]) {
				$('olv_lev_'+i).innerHTML=' ('+valasz.db_bontas[i-1]+'/'+valasz.db_ossz_bontas[i-1]+')';
			} else {
				$('olv_lev_'+i).innerHTML=' (0/'+valasz.db_ossz_bontas[i-1]+')';
			}
		}
	});
	return false;
};
function csatajelentest_megnyit(id) {
	sendRequest('csatajelentes.php?id='+id,function(req) {
		var valasz=json2obj(req.responseText);
		var s='';
		if (valasz.oke) {
			$('csj_megnyito_link_'+id).style.display='none';
			$('csj_toggle_link_'+id).style.display='inline';
			for(var i=0;i<valasz.flottak.length;i++) {
				if ($('csj_'+id+'_'+valasz.flottak[i].f)) {
					s='<ul class="csj_hajo_lista">';
					for(var j=0;j<valasz.flottak[i].h.length;j++) {
						s+='<li>';
						s+=szazadresz(valasz.flottak[i].h[j].e)+' &gt; '+szazadresz(valasz.flottak[i].h[j].u)+' <a href="#" onclick="return jump_to_help(2,'+valasz.flottak[i].h[j].h+')"><img src="img/ikonok/'+valasz.flottak[i].h[j].k+'_index.gif" style="width:16px" /></a> '+valasz.flottak[i].h[j].n+' (&Delta;='+szazadresz(valasz.flottak[i].h[j].e-valasz.flottak[i].h[j].u)+')';
						s+='</li>';
					}
					s+='</ul>';
					$('csj_'+id+'_'+valasz.flottak[i].f).innerHTML=s;
				}
			}
		}
	});
	return false;
};
function csatajelentes_toggle(id) {
	var x=document.getElementsByTagName('span');
	for(var i=0;i<x.length;i++) if (x[i].id.lastIndexOf('_')>=0) if (x[i].id.substring(0,x[i].id.lastIndexOf('_')+1)=='csj_'+id+'_') inline_toggle(x[i].id);
	if ($('csj_toggle_link_'+id).innerHTML=='***becsuk***') $('csj_toggle_link_'+id).innerHTML='***megnyit***';
	else $('csj_toggle_link_'+id).innerHTML='***becsuk***';
	return false;
};




function jump_to_help(domen,id) {
	if (domen!=0 || id!=0) {
		window.open('helpp.php?domen='+domen+'&id='+id,'help_ablak','width=1000,height=650,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0');
	}
	return false;
};

function frissit_cset() {
	if (aktiv_oldal=='cset') {
		var s='';
		for(var cstab=1;cstab<=6;cstab++) s+=cset_uccso_idk[cstab-1]+',';
		sendRequest('cset_hozzaszolasok_v2.php?uccsok='+s+'&akt='+aktiv_cset_aloldal+(elso_cset_frissites?'&elso=1':'')+'&szov='+cset_vendeg_szov,function(req) {
			elso_cset_frissites=false;
			var valasz=json2obj(req.responseText);
			//
			for(var cstab=1;cstab<=6;cstab++) {
				if (valasz.lat[cstab-1]>0) {
					$('cset_tab_'+cstab).className='cset_tab_visible';
					if (aktiv_cset_aloldal==cstab) if ($('cset_aloldal_'+cstab).style.display!='block') $('cset_aloldal_'+cstab).style.display='block';
					if (cstab>3) {
						$('cset_tab_'+cstab).childNodes[0].innerHTML=valasz.szobak[cstab-4][2];
						$('cset_tab_'+cstab+'_hiv').innerHTML=valasz.szobak[cstab-4][1]?'*':'';
					}
				} else {
					$('cset_tab_'+cstab).className='';
					$('cset_aloldal_'+cstab).style.display='none';
					if (cstab>3) {
						$('cset_tab_'+cstab).childNodes[0].innerHTML='';
						$('cset_tab_'+cstab+'_hiv').innerHTML='';
					}
					if (aktiv_cset_aloldal==cstab) katt_cset_tab(3);
					cset_uccso_idk[cstab-1]=0;
					$('cset_csat_'+cstab).innerHTML='';
				}
			}
			//
			for(var cstab=1;cstab<=6;cstab++) if (valasz.lat[cstab-1]>0) {
				var h=valasz.h[cstab-1];
				var rh=valasz.rh[cstab-1];
				if (cstab!=aktiv_cset_aloldal) {
					if (h.length>0) cset_tabok_olvasatlan[cstab-1]=1;
					if (cset_tabok_olvasatlan[cstab-1]>0) {
						$('cset_tab_'+cstab).childNodes[0].className='olvasatlan_aloldal';
					} else $('cset_tab_'+cstab).childNodes[0].className='';
				}
				var x=$('cset_csat_'+cstab).innerHTML;
				var k=$('cset_csat_'+cstab).scrollHeight-$('cset_csat_'+cstab).scrollTop;
				for(var i=0;i<rh.length;i++) {
					x+=rh[i][4]+' ';
					if (rh[i][5]>0) x+='<a href="" onclick="return user_katt('+rh[i][1]+')" class="cset_user_nev">'+rh[i][2]+'</a>';else x+='***törölt játékos***';
					x+=': '+rh[i][3];
					x+='<br />';
				}
				for(var i=0;i<h.length;i++) {
					x+=h[i][4]+' ';
					if (h[i][5]>0) x+='<a href="" onclick="return user_katt('+h[i][1]+')" class="cset_user_nev">'+h[i][2]+'</a>';else x+='***törölt játékos***';
					x+=': '+h[i][3];
					x+='<br />';
				}
				cset_uccso_idk[cstab-1]=valasz.uccso[cstab-1];
				$('cset_csat_'+cstab).innerHTML=x;
				if (k<=$('cset_csat_'+cstab).offsetHeight+100) $('cset_csat_'+cstab).scrollTop=$('cset_csat_'+cstab).scrollHeight;
				//$('cset_csat_'+cstab).scrollTop=$('cset_csat_'+cstab).scrollHeight;
				//online
				if (valasz.online[cstab-1]==0) {
					if ($('cset_online_'+cstab)) $('cset_online_'+cstab).innerHTML='';
				} else {
					var s='';
					if (cstab>3) {
						for(var i=0;i<valasz.online[cstab-1].length;i++) {
							s+='<a href="" onclick="return user_katt('+valasz.online[cstab-1][i][2]+')"'+(valasz.online[cstab-1][i][1]>15?' class="cset_user_nev_inakt"':(valasz.online[cstab-1][i][1]<=5?' class="cset_user_nev"':''))+'>';
							s+=valasz.online[cstab-1][i][0]+(valasz.online[cstab-1][i][3]>0?(' ('+valasz.online[cstab-1][i][4]+')'):'');
							s+='</a><br />';
						}
					} else {
						for(var i=0;i<valasz.online[cstab-1].length;i++) s+='<a href="" onclick="return user_katt('+valasz.online[cstab-1][i][2]+')"'+(valasz.online[cstab-1][i][1]<=5?' class="cset_user_nev"':'')+'>'+valasz.online[cstab-1][i][0]+'</a><br />';
					}
					$('cset_online_'+cstab).innerHTML=s;
				}
				if (valasz.szoba_idk[cstab-1]!=-500) $('cset_alsoresz_'+cstab).innerHTML='<p><a href="***zanda_game_url***chat/?c='+valasz.szoba_idk[cstab-1]+'" target="_blank">LOG</a></p>';else $('cset_alsoresz_'+cstab).innerHTML='';
			}
			//
			var van_e_olvasatlan=0;
			for(var i=0;i<6;i++) if (cset_tabok_olvasatlan[i]>0) van_e_olvasatlan=1;
			if (van_e_olvasatlan>0) $('olvasatlan_csetek_jelzo').innerHTML='C';
			else $('olvasatlan_csetek_jelzo').innerHTML='';
			//
		});
	}
	return false;
};
function torol_csetek_ablak(cstab) {
	$('cset_csat_'+cstab).innerHTML='';
	return false;
};
function katt_cset_tab(id) {
	$('cset_aloldal_'+aktiv_cset_aloldal).style.display='none';
	$('cset_tab_'+aktiv_cset_aloldal).childNodes[0].className='';
	aktiv_cset_aloldal=id;
	$('cset_aloldal_'+aktiv_cset_aloldal).style.display='block';
	$('cset_tab_'+aktiv_cset_aloldal).childNodes[0].className='aktiv_aloldal';
	//
	if (id<6) {//misc tab nem
		if ($('cset_csat_'+aktiv_cset_aloldal)) $('cset_csat_'+aktiv_cset_aloldal).scrollTop=$('cset_csat_'+aktiv_cset_aloldal).scrollHeight;
		cset_tabok_olvasatlan[id-1]=0;
		var van_e_olvasatlan=0;
		for(var i=0;i<6;i++) if (cset_tabok_olvasatlan[i]>0) van_e_olvasatlan=1;
		if (van_e_olvasatlan>0) $('olvasatlan_csetek_jelzo').innerHTML='C';
		else $('olvasatlan_csetek_jelzo').innerHTML='';
	}
	return false;
};

function frissit_cset_szobak() {
	sendRequest('cset_szobak.php',function(req) {
		var valasz=json2obj(req.responseText);
		if (valasz.diplomata) {
			var s='';
			var sorszam=0;
			for(var i=0;i<valasz.sajat_szobak.idk.length;i++) {
				sorszam++;
				var szoba_id=valasz.sajat_szobak.idk[i];
				s+='<h3>#'+sorszam+' (<a href="#" onclick="if (confirm(\'***Biztos?***\')) cset_szobat_zar('+szoba_id+');return false">***bezár***</a>)</h3>';
				s+='<h4>***Tagok***</h4>';
				s+='<ul class="classic_lista">';
				for(var j=0;j<valasz.sajat_szobak.tagok.length;j++) {
					var tag=valasz.sajat_szobak.tagok[j];
					if (tag[0]==szoba_id) {
						s+='<li>'+tag[2]+' (<a href="#" onclick="if (confirm(\'***Biztos?***\')) cset_szobabol_kirug('+szoba_id+','+tag[1]+');return false">***kirúg***</a>)</li>';
					}
				}
				s+='</ul>';
				s+='<h4>***Meghívók***</h4>';
				s+='<ul class="classic_lista">';
				for(var j=0;j<valasz.sajat_szobak.meghivok.length;j++) {
					var meghivo=valasz.sajat_szobak.meghivok[j];
					if (meghivo[0]==szoba_id) {
						s+='<li>'+meghivo[2]+' (<a href="#" onclick="return cset_meghivot_visszavon('+szoba_id+','+meghivo[1]+')">***visszavon***</a>)</li>';
					}
				}
				s+='<li>';
				s+='<form onsubmit="return cset_meghivot_kuld('+szoba_id+')" style="display:inline">';
				s+='***új***: <input type="text" class="szovegdoboz" id="meghiv_nev_'+szoba_id+'" />';
				s+='</form>';
				s+='</li>';
				s+='</ul>';
			}
			$('cset_szobak_sajatok').innerHTML=s;
			$('cset_szoba_admin').style.display='block';
		} else {
			$('cset_szoba_admin').style.display='none';
		}
		//meghivok
		s='';
		s+='<ul class="classic_lista">';
		for(var j=0;j<valasz.meghivok.length;j++) {
			var meghivo=valasz.meghivok[j];
			s+='<li>'+meghivo[1];
			s+=' (<a href="#" onclick="if (confirm(\'***Biztos?***\')) cset_meghivot_elfogad('+meghivo[0]+');return false">***elfogad***</a>)';
			s+=' (<a href="#" onclick="if (confirm(\'***Biztos?***\')) cset_meghivot_elutasit('+meghivo[0]+');return false">***elutasít***</a>)';
			s+'</li>';
		}
		s+='</ul>';
		$('cset_szobak_masoke_meghivok').innerHTML=s;
		//tagsagok
		s='';
		s+='<ul class="classic_lista">';
		for(var j=0;j<valasz.tagsagok.length;j++) {
			var tagsag=valasz.tagsagok[j];
			s+='<li>'+tagsag[1];
			s+=' (<a href="#" onclick="if (confirm(\'***Biztos?***\')) cset_szobabol_kilep('+tagsag[0]+');return false">***kilép***</a>)';
			s+'</li>';
		}
		s+='</ul>';
		$('cset_szobak_masoke_tagsagok').innerHTML=s;
		//hivatalos szobak
		s='';
		s+='<ul class="classic_lista">';
		for(var j=0;j<valasz.hivatalos_szobak.length;j++) {
			var szoba=valasz.hivatalos_szobak[j];
			s+='<li>'+szoba[1]+' '+szoba[3]+': <a href="***zanda_game_url***chat/?c=-'+szoba[0]+'" target="_blank">LOG</a></li>';
		}
		s+='</ul>';
		$('cset_szobak_hivatalos_szobak').innerHTML=s;
		//vendegsegek
		if (valasz.vendegsegek.length>0) {
			s='<p><select class="szovegmezo" onchange="cset_vendeg_szov=this.options[this.selectedIndex].value;cset_uccso_idk[2]=0;$(\'cset_csat_3\').innerHTML=\'\';frissit_cset()">';
			s+='<option value="0" style="font-weight:bold"'+((cset_vendeg_szov==0)?' selected="selected"':'')+'>***saját szövetség***</option>';
			for(var j=0;j<valasz.vendegsegek.length;j++) s+='<option value="'+valasz.vendegsegek[j][0]+'"'+((cset_vendeg_szov==valasz.vendegsegek[j][0])?' selected="selected"':'')+'>'+valasz.vendegsegek[j][1]+'</option>';
			s+='</select></p><br />';
			$('cset_vendeglista_div').innerHTML=s;
		} else {
			$('cset_vendeglista_div').innerHTML='';
		}
	});
}


function frissit_tozsde_graf() {
	if ($('tozsde_graf_img')) $('tozsde_graf_img').src='tozsde_graf.php?felbontas='+tozsde_graf_felbontas+'&termek='+tozsde_graf_piac+'&regio='+aktiv_regio+'&rnd='+Math.random();
	return false;
}
function frissit_tozsde() {
	sendRequest('tozsde.php?bolygo_id='+aktiv_bolygo+'&regio='+aktiv_regio,function(req) {
		var valasz=json2obj(req.responseText);
		var s='';
		var hasznalhato_regio=0;
		//
		if (aktiv_regio==0) aktiv_regio=valasz.aktualis_regio;
		if (valasz.kereskedo>0) {
			if (aktiv_regio==valasz.aktualis_regio) hasznalhato_regio=1;
			if (aktiv_regio==valasz.aktualis_regio2) hasznalhato_regio=1;
			s+='<p>***Jelenlegi régiók***: R'+leftpad(valasz.aktualis_regio,2)+' ***és*** R'+leftpad(valasz.aktualis_regio2,2);
			if (valasz.kovetkezo_regiovaltas_v2>0) {
				s+='. ***Következő régióváltás leghamarabb*** '+min2hm_mulva(valasz.kovetkezo_regiovaltas_v2)+'.';
			} else {
				s+='. ***Ha váltani szeretnél, válaszd ki az új régiókat***: <select id="kov_regio_1">';
				for(var ridx in valasz.elerheto_regiok) s+='<option value="'+valasz.elerheto_regiok[ridx]+'"'+(valasz.elerheto_regiok[ridx]==valasz.aktualis_regio?' selected="selected" style="font-weight:bold"':'')+'>R'+leftpad(valasz.elerheto_regiok[ridx],2)+'</option>';
				s+='</select> ***és*** <select id="kov_regio_2">';
				for(var ridx in valasz.elerheto_regiok) s+='<option value="'+valasz.elerheto_regiok[ridx]+'"'+(valasz.elerheto_regiok[ridx]==valasz.aktualis_regio2?' selected="selected" style="font-weight:bold"':'')+'>R'+leftpad(valasz.elerheto_regiok[ridx],2)+'</option>';
				s+='</select> (<a href="#" onclick="return tozsde_regio_valtas_most()">***Régió váltás most***</a>)';
			}
			s+='</p>';
			s+='<p><br /></p>';
			s+='<p><select class="szovegmezo" onchange="aktiv_regio=this.options[this.selectedIndex].value;frissit_tozsde()">';
			s+='<option style="font-weight:bold" value="'+valasz.aktualis_regio+'"'+(valasz.aktualis_regio==aktiv_regio?' selected="selected"':'')+'>R'+leftpad(valasz.aktualis_regio,2)+'</option>';
			if (valasz.aktualis_regio2!=valasz.aktualis_regio) s+='<option style="font-weight:bold" value="'+valasz.aktualis_regio2+'"'+(valasz.aktualis_regio2==aktiv_regio?' selected="selected"':'')+'>R'+leftpad(valasz.aktualis_regio2,2)+'</option>';
			for(var r=1;r<=valasz.regiok_szama;r++) if (r!=valasz.aktualis_regio) if (r!=valasz.aktualis_regio2) s+='<option value="'+r+'"'+(r==aktiv_regio?' selected="selected"':'')+'>R'+leftpad(r,2)+'</option>';
			s+='</select></p>';
		} else {
			if (aktiv_regio==valasz.aktualis_regio) hasznalhato_regio=1;
			s+='<p><select class="szovegmezo" onchange="aktiv_regio=this.options[this.selectedIndex].value;frissit_tozsde()">';
			s+='<option style="font-weight:bold" value="'+valasz.aktualis_regio+'"'+(valasz.aktualis_regio==aktiv_regio?' selected="selected"':'')+'>R'+leftpad(valasz.aktualis_regio,2)+'</option>';
			for(var r=1;r<=valasz.regiok_szama;r++) if (r!=valasz.aktualis_regio) s+='<option value="'+r+'"'+(r==aktiv_regio?' selected="selected"':'')+'>R'+leftpad(r,2)+'</option>';
			s+='</select></p>';
		}
		//
		s+='<table>';
		s+='<tr>';
		s+='<th></th>';
		s+='<th style="width:100px">***erőforrás***</th>';
		s+='<th>***ár***</th>';
		s+='<th>***vétel***/***eladás***</th>';
		s+='<th>***készlet***</th>';
		s+='<th>***nem eladható***</th>';
		s+='<th>***napi limit***</th>';
		s+='<th>***felhasznált***</th>';
		s+='</tr>';
		var e=0;
		for(var i=0;i<valasz.piacok.length;i++) {
			e=valasz.piacok[i][0];
			s+='<tr';
			if (i%2) s+=' class="tozsde_paros_sor"';
			s+='>';
			if (eroforrasok_fajlneve[e].length>0) s+='<td><a href="" style="cursor: help" onclick="return jump_to_help(2,'+e+')"><img src="img/ikonok/'+eroforrasok_fajlneve[e]+'_index.jpg" /></a></td>';
			else s+='<td></td>';
			s+='<td><span style="font-weight: bold">'+eroforrasok_neve[e]+'</span></td>';
			s+='<td style="text-align: right">';
			if (valasz.premium==2) s+='<a href="" style="font-weight: bold" onclick="tozsde_graf_piac='+e+';return frissit_tozsde_graf()">';else s+='<span style="font-weight: bold">';
			s+=ezresito(valasz.piacok[i][1]);
			if (valasz.premium==2) s+='</a>';else s+='</span>';
			s+='</td>';
			//
			if (hasznalhato_regio>0) {
				s+='<td>';
				s+='<table>';
				s+='<tr>';
				s+='<td><input type="text" class="tozsde_mennyiseg" id="uj_ajanlat_mennyiseg_'+e+'" /></td>';
				s+='<td><a href="" title="***vétel***" onclick="return tozsdezik(1,'+e+');" style="font-weight: bold" class="ajanlat_veteli">***vétel***</a>';
				s+=' <a href="" title="***eladás***" onclick="return tozsdezik(0,'+e+');" style="font-weight: bold" class="ajanlat_eladasi">***eladás***</a>';
				s+='</td>';
				s+='</tr>';
				s+='</table>';
				s+='</td>';
				s+='<td style="text-align: right"><a href="" onclick="$(\'uj_ajanlat_mennyiseg_'+e+'\').value='+valasz.keszletek[e][0]+';return false;">'+ezresito(valasz.keszletek[e][0])+'</a></td>';
			} else {
				s+='<td></td>';
				s+='<td style="text-align: right">'+ezresito(valasz.keszletek[e][0])+'</td>';
			}
			//
			s+='<td style="text-align: right">';
			if (valasz.keszletek[e][1]>=0) s+=ezresito(valasz.keszletek[e][1]);
			s+='</td>';
			s+='<td style="text-align: right">'+ezresito(valasz.limitek[e][0])+'</td>';
			s+='<td style="text-align: right">'+ezresito(valasz.limitek[e][1])+'</td>';
			s+='</tr>';
		}
		s+='</table>';
		if (valasz.premium==2) {
			var efselect='';
			for(var i=0;i<valasz.piacok.length;i++) efselect+='<option value="'+valasz.piacok[i][0]+'">'+eroforrasok_neve[valasz.piacok[i][0]]+'</option>';
			var szumma_tt=0;
			for(var i=0;i<valasz.auto_transz.length;i++) szumma_tt+=Math.ceil(valasz.auto_transz[i][1]/eroforrasok_savszele[valasz.auto_transz[i][0]]);
			s+='<h2>***Ütemezett eladás***</h2>';
			if (valasz.kereskedo>0) {
				if (valasz.auto_transz.length>0) s+=
					json2table(valasz.auto_transz,['***mennyit***','***miből***','***hova***','***TT***','',''],[
					function(sor) {return ezresito(sor[1]);},
					function(sor) {return eroforrasok_neve[sor[0]];},
					function(sor) {if (sor[7]==1) return 'R'+leftpad(valasz.aktualis_regio,2);return 'R'+leftpad(valasz.aktualis_regio2,2);},
					function(sor) {return ezresito(Math.ceil(sor[1]/eroforrasok_savszele[sor[0]]));},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_del('+sor[6]+')">***törlés***</a>';},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_mod_kerdez('+sor[6]+','+sor[1]+')">***módosítás***</a>';}
					],['120px','150px','100px','50px','50px','50px'],null,null,['right','left','center','right','center','center'])+
					json2table([[
					'',
					'***Összesen***',
					'',
					ezresito(szumma_tt),
					'',
					''
					]],
					[],[0,1,2,3,4],['120px','150px','100px','50px','50px','50px'],null,null,['right','left','center','right','center','center']);
				s+=json2table([[
					'<input type="text" class="szovegmezo" style="width:100px" id="auto_tozsde_darab" />',
					'<select class="szovegmezo" style="width:130px" id="auto_tozsde_ef">'+efselect+'</select>',
					'<select class="szovegmezo" id="auto_tozsde_regio_slot"><option value="1">R'+leftpad(valasz.aktualis_regio,2)+'</option>'+((valasz.aktualis_regio!=valasz.aktualis_regio2)?'<option value="2">R'+leftpad(valasz.aktualis_regio2,2)+'</option>':'')+'</select>',
					'',
					'<a href="" onclick="return eroforras_auto_tozsde()">***Mehet!***</a>'
					]],
					['***mennyit***','***miből***','***hova***','',''],[0,1,2,3,4],['120px','150px','100px','50px','50px'],null,null,['left','left','center','right','center']);
			} else {
				if (valasz.auto_transz.length>0) s+=
					json2table(valasz.auto_transz,['***mennyit***','***miből***','***TT***','',''],[
					function(sor) {return ezresito(sor[1]);},
					function(sor) {return eroforrasok_neve[sor[0]];},
					function(sor) {return ezresito(Math.ceil(sor[1]/eroforrasok_savszele[sor[0]]));},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_del('+sor[6]+')">***törlés***</a>';},
					function(sor) {return '<a href="" onclick="return eroforras_auto_transzfer_mod_kerdez('+sor[6]+','+sor[1]+')">***módosítás***</a>';}
					],['120px','150px','50px','50px','50px'],null,null,['right','left','right','center','center'])+
					json2table([[
					'',
					'***Összesen***',
					ezresito(szumma_tt),
					'',
					''
					]],
					[],[0,1,2,3],['120px','150px','50px','50px','50px'],null,null,['right','left','right','center','center']);
				s+=json2table([[
					'<input type="text" class="szovegmezo" style="width:100px" id="auto_tozsde_darab" />',
					'<select class="szovegmezo" style="width:130px" id="auto_tozsde_ef">'+efselect+'</select>',
					'',
					'<a href="" onclick="return eroforras_auto_tozsde()">***Mehet!***</a>'
					]],
					['***mennyit***','***miből***','',''],[0,1,2,3],['120px','150px','50px','50px'],null,null,['left','left','right','center']);
			}
		}
		$('tozsde_tablazat').innerHTML=s;
		//
		$('tozsde_vagyonod').innerHTML=ezresito(valasz.vagyon);
		$('napi_veteli_limit_reset').innerHTML=min2hm_mulva(valasz.kovetkezo_napi_limit);
	});
	return false;
};
function frissit_szabadpiac() {
	sendRequest('szabadpiac.php?bolygo_id='+aktiv_bolygo,function(req) {
		var valasz=json2obj(req.responseText);
		var s='<table>';
		s+='<tr>';
		s+='<th></th>';
		s+='<th style="width:70px">***erőforrás***</th>';
		s+='<th>***ár***</th>';
		s+='<th>***vétel***</th>';
		if (valasz.speki>0) s+='<th>***eladás***</th>';
		s+='<th>***készlet***</th>';
		s+='</tr>';
		var e=0;
		for(var i=0;i<valasz.piacok.length;i++) {
			e=valasz.piacok[i][0];
			s+='<tr';
			if (i%2) s+=' class="tozsde_paros_sor"';
			s+='>';
			if (eroforrasok_fajlneve[e].length>0) s+='<td><a href="" style="cursor: help" onclick="return jump_to_help(2,'+e+')"><img src="img/ikonok/'+eroforrasok_fajlneve[e]+'_index.jpg" /></a></td>';
			else s+='<td></td>';
			s+='<td><span style="font-weight: bold">'+eroforrasok_neve[e]+'</span></td>';
			s+='<td style="text-align: right">';
			s+='<span style="font-weight: bold">';
			if (valasz.piacok[i][2]>0) s+=ezresito(valasz.piacok[i][2]);else s+='-';
			s+='</span>';
			s+='<br /><span class="halvany_pici">('+ezresito(valasz.piacok[i][1])+' - '+ezresito(valasz.piacok[i][3])+')</span>';
			s+='</td>';
			//vetel
			s+='<td>';
			s+='<table>';
			if (valasz.legjobb_ajanlatok[e]) if (valasz.legjobb_ajanlatok[e][2]>0) {
				s+='<tr class="">';
				s+='<td style="text-align:right">'+ezresito(valasz.legjobb_ajanlatok[e][3])+'</td>';
				s+='<td>@</td>';
				s+='<td>'+ezresito(valasz.legjobb_ajanlatok[e][2])+' SHY</td>';
				s+='<td><a href="" title="***vétel***" onclick="$(\'uj_ajanlat_mennyiseg_1_'+e+'\').value='+valasz.legjobb_ajanlatok[e][3]+';$(\'uj_ajanlat_arfolyam_1_'+e+'\').value='+valasz.legjobb_ajanlatok[e][2]+';return szabadpiaci_ajanlatot_kuld(1,'+e+')" style="font-weight: bold" class="ajanlat_veteli">***V***</a></td>';
				s+='</tr>';
			}
			s+='<tr>';
			s+='<td><input type="text" class="tozsde_mennyiseg" id="uj_ajanlat_mennyiseg_1_'+e+'" /></td>';
			s+='<td>@</td>';
			s+='<td><input type="text" class="tozsde_arfolyam" value="'+((valasz.piacok[i][2]>0)?valasz.piacok[i][2]:valasz.piacok[i][1])+'" id="uj_ajanlat_arfolyam_1_'+e+'" /></td>';
			s+='<td><a href="" title="***vétel***" onclick="return szabadpiaci_ajanlatot_kuld(1,'+e+');" style="font-weight: bold" class="ajanlat_veteli">***V***</a></td>';
			s+='</tr>';
			if (valasz.sajat_ajanlatok[e]) for(var j=0;j<valasz.sajat_ajanlatok[e].length;j++) if (valasz.sajat_ajanlatok[e][j][1]==1) {
				s+='<tr class="ajanlat_veteli">';
				s+='<td style="text-align:right">'+ezresito(valasz.sajat_ajanlatok[e][j][3])+'</td>';
				s+='<td>@</td>';
				s+='<td>'+ezresito(valasz.sajat_ajanlatok[e][j][2])+' SHY</td>';
				s+='<td><a href="" title="***törlés***" onclick="return szabadpiaci_ajanlatot_visszavon('+valasz.sajat_ajanlatok[e][j][0]+');" style="font-weight: bold">***T***</a>';
				s+=' <a href="" title="***módosítás***" onclick="return szabadpiaci_ajanlatot_modosit_kerdez('+valasz.sajat_ajanlatok[e][j][0]+','+valasz.sajat_ajanlatok[e][j][2]+');" style="font-weight: bold">***M***</a></td>';
				s+='</tr>';
			}
			s+='</table>';
			s+='</td>';
			if (valasz.speki>0) {
				//eladas
				s+='<td>';
				s+='<table>';
				if (valasz.legjobb_ajanlatok[e]) if (valasz.legjobb_ajanlatok[e][0]>0) {
					s+='<tr class="">';
					s+='<td style="text-align:right">'+ezresito(valasz.legjobb_ajanlatok[e][1])+'</td>';
					s+='<td>@</td>';
					s+='<td>'+ezresito(valasz.legjobb_ajanlatok[e][0])+' SHY</td>';
					s+='<td><a href="" title="***eladás***" onclick="$(\'uj_ajanlat_mennyiseg_0_'+e+'\').value='+valasz.legjobb_ajanlatok[e][1]+';$(\'uj_ajanlat_arfolyam_0_'+e+'\').value='+valasz.legjobb_ajanlatok[e][0]+';return szabadpiaci_ajanlatot_kuld(0,'+e+')" style="font-weight: bold" class="ajanlat_eladasi">***E***</a></td>';
					s+='</tr>';
				}
				s+='<tr>';
				s+='<td><input type="text" class="tozsde_mennyiseg" id="uj_ajanlat_mennyiseg_0_'+e+'" /></td>';
				s+='<td>@</td>';
				s+='<td><input type="text" class="tozsde_arfolyam" value="'+((valasz.piacok[i][2]>0)?valasz.piacok[i][2]:valasz.piacok[i][1])+'" id="uj_ajanlat_arfolyam_0_'+e+'" /></td>';
				s+='<td><a href="" title="***eladás***" onclick="return szabadpiaci_ajanlatot_kuld(0,'+e+');" style="font-weight: bold" class="ajanlat_eladasi">***E***</a></td>';
				s+='</tr>';
				if (valasz.sajat_ajanlatok[e]) for(var j=0;j<valasz.sajat_ajanlatok[e].length;j++) if (valasz.sajat_ajanlatok[e][j][1]==0) {
					s+='<tr class="ajanlat_eladasi">';
					s+='<td style="text-align:right">'+ezresito(valasz.sajat_ajanlatok[e][j][3])+'</td>';
					s+='<td>@</td>';
					s+='<td>'+ezresito(valasz.sajat_ajanlatok[e][j][2])+' SHY</td>';
					s+='<td><a href="" title="***törlés***" onclick="return szabadpiaci_ajanlatot_visszavon('+valasz.sajat_ajanlatok[e][j][0]+');" style="font-weight: bold">***T***</a>';
					s+=' <a href="" title="***módosítás***" onclick="return szabadpiaci_ajanlatot_modosit_kerdez('+valasz.sajat_ajanlatok[e][j][0]+','+valasz.sajat_ajanlatok[e][j][2]+');" style="font-weight: bold">***M***</a></td>';
					s+='</tr>';
				}
				s+='</table>';
				s+='</td>';
			}
			//keszlet
			s+='<td style="text-align: right"><a href="" onclick="$(\'uj_ajanlat_mennyiseg_0_'+e+'\').value='+valasz.keszletek[e][0]+';return false;">'+ezresito(valasz.keszletek[e][0])+'</a></td>';
			s+='</tr>';
		}
		s+='</table>';
		document.getElementById('szabadpiac_tablazat').innerHTML=s;
		document.getElementById('szabadpiac_vagyonod').innerHTML=ezresito(valasz.vagyon);
	});
	return false;
};



function frissit_felder() {
	$('tolto_ikon').style.display='block';
	$('felder_attekintes').innerHTML='';
	$('felder_reszletek').innerHTML='';
	sendRequest('felder_adatok.php',function(req) {
		$('tolto_ikon').style.display='none';
		var valasz=json2obj(req.responseText);
		$('felder_attekintes').innerHTML=json2table([
			['***Ügynökök száma***',ezresito(valasz.ugynokszam)+' / '+ezresito(valasz.ugynokkapacitas)],
			['***Eloszlás***',
				(valasz.eloszlas.s0?(valasz.eloszlas.s0[1]+'% ***alvó ügynök*** '):'')
				+(valasz.eloszlas.s1?(valasz.eloszlas.s1[1]+'% ***elhárító*** '):'')
				+(valasz.eloszlas.s2?(valasz.eloszlas.s2[1]+'% ***aktív elhárító*** '):'')
				+(valasz.eloszlas.s3?(valasz.eloszlas.s3[1]+'% ***kém*** '):'')
				+(valasz.eloszlas.s4?(valasz.eloszlas.s4[1]+'% ***szabotőr*** '):'')
			],
			['***Vagyonod***',ezresito(valasz.vagyon)+' SHY'],
			['***Körönkénti költség***',ezresito(valasz.fogyasztas)+' SHY']
		],[]);
		//
		var s='';
		var lista='';
		for(bolygo_id in valasz.csoportok) {
			s+='<h2>';
			if (bolygo_id>0) {
				//tulaj_szov, tulaj???
				s+='<a href="" class="bolygo_osztaly_'+valasz.csoportok[bolygo_id][0][11]+'_24" onclick="return bolygo_katt('+bolygo_id+')">';
				s+=valasz.csoportok[bolygo_id][0][4];
				s+='</a>';
			} else s+='***Űrben***';
			s+='</h2>';
			//
			if (bolygo_id>0) {
				s+='<form id="felder_ucs_form_'+bolygo_id+'">';
				s+=json2table(valasz.csoportok[bolygo_id],['#','***átcsop***','***ügynök***','***tevékenység***','***időtartam***','***költőpénz***','(***új célpont***)'],[
					function(sor,sorszam) {return sorszam+1;},
					function(sor) {return '<input type="text" class="ugynokszam" id="felder_ucs_atcsop_'+sor[0]+'" />';},
					function(sor) {return ezresito(sor[1]);},
					function(sor) {
						var s='';
						var ss='';
						switch(sor[2]) {
							case 0:s='***alvó***';break;
							case 1:s='***elhárító***';break;
							case 2:s='***aktív elhárító***';break;
							case 3:
								s='***kém*** / ';
								switch(sor[14]) {
									case 1:
										s+=epuletek_neve[sor[15]];
									break;
									case 2:
										s+=eroforrasok_neve[sor[15]];
									break;
								}
							break;
							case 4:
								s='***szabotőr*** / ';
								switch(sor[14]) {
									case 1:
										s+='***inaktiválása prefix***'+epuletek_neve[sor[15]]+'***inaktiválása postfix***';
									break;
								}
							break;
						}
						ss='<option value="0"'+(sor[2]==0?' selected="selected"':'')+'>***alvó***</option>';
						ss+='<option value="1"'+(sor[2]==1?' selected="selected"':'')+'>***elhárító***</option>';
						ss+='<option value="2"'+(sor[2]==2?' selected="selected"':'')+'>***aktív elhárító***</option>';
						//kem
						for(var gyar_id in epuletek_neve) ss+='<option value="3_1_'+gyar_id+'"'+(((sor[2]==3)&&(sor[14]==1)&&(sor[15]==gyar_id))?' selected="selected"':'')+'>***kém*** / '+epuletek_neve[gyar_id]+'</option>';
						for(var ef_id in kemkedheto_eroforrasok_neve) ss+='<option value="3_2_'+ef_id+'"'+(((sor[2]==3)&&(sor[14]==2)&&(sor[15]==ef_id))?' selected="selected"':'')+'>***kém*** / '+kemkedheto_eroforrasok_neve[ef_id]+'</option>';
						//szabotor
						for(var gyar_id in epuletek_neve) ss+='<option value="4_1_'+gyar_id+'"'+(((sor[2]==4)&&(sor[14]==1)&&(sor[15]==gyar_id))?' selected="selected"':'')+'>***szabotőr*** / ***inaktiválása prefix***'+epuletek_neve[gyar_id]+'***inaktiválása postfix***</option>';
						//
						return '<span id="felder_ucs_'+sor[0]+'_tevekenyseg_span">'+s+'</span><select style="display:none" id="felder_ucs_'+sor[0]+'_tevekenyseg_input">'+ss+'</select>';
					},
					function(sor) {
						var s='';
						if (sor[2]==0) s='';
						else if (sor[12]==0) s='***korlátlan***';
						else s=sor[12];
						return '<span id="felder_ucs_'+sor[0]+'_idotartam_span">'+s+'</span><input class="szovegmezo8" style="width:45px;display:none" id="felder_ucs_'+sor[0]+'_idotartam_input" value="'+sor[12]+'" />';
					},
					function(sor) {
						var s='';
						if (sor[2]==0) s='';
						else s=sor[13];
						return '<span id="felder_ucs_'+sor[0]+'_koltopenz_span">'+s+'</span><input class="szovegmezo8" style="width:45px;display:none" id="felder_ucs_'+sor[0]+'_koltopenz_input" value="'+sor[13]+'" />';
					},
					function(sor) {
						return '<a href="#" id="felder_mod_link_'+sor[0]+'" onclick="return felder_mod_show('+sor[0]+')"><img src="img/ikonok/szerk.gif" alt="" /></a>'
						+' <a href="#" style="display:none" id="felder_cancel_link_'+sor[0]+'" onclick="return felder_mod_hide('+sor[0]+')"><img src="img/ikonok/cross.gif" alt="" /></a>'
						+' <a href="#" style="display:none" id="felder_save_link_'+sor[0]+'" onclick="return felder_mod_save('+sor[0]+')"><img src="img/ikonok/tick.gif" alt="" /></a>'
						+' <input class="szovegmezo8" style="width:145px;display:none" id="felder_ucs_'+sor[0]+'_uj_bolygo_input" value="" />';
					}
					],['20px','60px','60px','250px','50px','50px','200px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['right','center','right','left','center','right','left']);
				lista='<option value="0">***új***</option>';
				for(var i=0;i<valasz.csoportok[bolygo_id].length;i++) lista+='<option value="'+valasz.csoportok[bolygo_id][i][0]+'">#'+(i+1)+'</option>';
				s+=json2table([[
					'<a href="" onclick="return ugynok_atrendezes('+bolygo_id+')" title="***Mehet!***"><img src="img/ikonok/arrow_refresh.gif" alt="" /></a>',
					'<select style="width:50px" id="felder_ucs_hova_'+bolygo_id+'">'+lista+'</select>'
					]],
					[],[0,1],['20px','60px','60px'],null,null,['right','center','right']);
				s+='</form>';
			} else {
				s+=json2table(valasz.csoportok[bolygo_id],['***pozíció***','***célpont***','***idő***','***ügynök***','***tevékenység***','***időtartam***','***költőpénz***','(***új célpont***)'],[
					function(sor) {return ykoordinata(sor[6])+', '+xkoordinata(sor[5]);},
					function(sor) {return '<a href="" class="bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+')">'+sor[8]+'</a>';},
					function(sor) {if (sor[7]>0) return sor[10];else return '';},
					function(sor) {return ezresito(sor[1]);},
					function(sor) {
						var s='';
						var ss='';
						switch(sor[2]) {
							case 0:s='***alvó***';break;
							case 1:s='***elhárító***';break;
							case 2:s='***aktív elhárító***';break;
							case 3:
								s='***kém*** / ';
								switch(sor[14]) {
									case 1:
										s+=epuletek_neve[sor[15]];
									break;
									case 2:
										s+=eroforrasok_neve[sor[15]];
									break;
								}
							break;
							case 4:
								s='***szabotőr*** / ';
								switch(sor[14]) {
									case 1:
										s+='***inaktiválása prefix***'+epuletek_neve[sor[15]]+'***inaktiválása postfix***';
									break;
								}
							break;
						}
						ss='<option value="0"'+(sor[2]==0?' selected="selected"':'')+'>***alvó***</option>';
						ss+='<option value="1"'+(sor[2]==1?' selected="selected"':'')+'>***elhárító***</option>';
						ss+='<option value="2"'+(sor[2]==2?' selected="selected"':'')+'>***aktív elhárító***</option>';
						//kem
						for(var gyar_id in epuletek_neve) ss+='<option value="3_1_'+gyar_id+'"'+(((sor[2]==3)&&(sor[14]==1)&&(sor[15]==gyar_id))?' selected="selected"':'')+'>***kém*** / '+epuletek_neve[gyar_id]+'</option>';
						for(var ef_id in kemkedheto_eroforrasok_neve) ss+='<option value="3_2_'+ef_id+'"'+(((sor[2]==3)&&(sor[14]==2)&&(sor[15]==ef_id))?' selected="selected"':'')+'>***kém*** / '+kemkedheto_eroforrasok_neve[ef_id]+'</option>';
						//szabotor
						for(var gyar_id in epuletek_neve) ss+='<option value="4_1_'+gyar_id+'"'+(((sor[2]==4)&&(sor[14]==1)&&(sor[15]==gyar_id))?' selected="selected"':'')+'>***szabotőr*** / ***inaktiválása prefix***'+epuletek_neve[gyar_id]+'***inaktiválása postfix***</option>';
						//
						return '<span id="felder_ucs_'+sor[0]+'_tevekenyseg_span">'+s+'</span><select style="display:none" id="felder_ucs_'+sor[0]+'_tevekenyseg_input">'+ss+'</select>';
					},
					function(sor) {
						var s='';
						if (sor[2]==0) s='';
						else if (sor[12]==0) s='***korlátlan***';
						else s=sor[12];
						return '<span id="felder_ucs_'+sor[0]+'_idotartam_span">'+s+'</span><input class="szovegmezo8" style="width:45px;display:none" id="felder_ucs_'+sor[0]+'_idotartam_input" value="'+sor[12]+'" />';
					},
					function(sor) {
						var s='';
						if (sor[2]==0) s='';
						else s=sor[13];
						return '<span id="felder_ucs_'+sor[0]+'_koltopenz_span">'+s+'</span><input class="szovegmezo8" style="width:45px;display:none" id="felder_ucs_'+sor[0]+'_koltopenz_input" value="'+sor[13]+'" />';
					},
					function(sor) {
						return '<a href="#" id="felder_mod_link_'+sor[0]+'" onclick="return felder_mod_show('+sor[0]+')"><img src="img/ikonok/szerk.gif" alt="" /></a>'
						+' <a href="#" style="display:none" id="felder_cancel_link_'+sor[0]+'" onclick="return felder_mod_hide('+sor[0]+')"><img src="img/ikonok/cross.gif" alt="" /></a>'
						+' <a href="#" style="display:none" id="felder_save_link_'+sor[0]+'" onclick="return felder_mod_save('+sor[0]+')"><img src="img/ikonok/tick.gif" alt="" /></a>'
						+' <input class="szovegmezo8" style="width:95px;display:none" id="felder_ucs_'+sor[0]+'_uj_bolygo_input" value="" />';
					}
					],['70px','80px','40px','60px','250px','50px','50px','150px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','left','right','right','left','center','right','left']);
			}
		}
		$('felder_reszletek').innerHTML=s;
	});
	return false;
};

function iparag_optionok(ipar) {
	var iparagak='';
	for(var i=0;i<iparag_nevek.length;i++) iparagak+='<option value="'+iparag_idk[i]+'"'+(ipar==iparag_idk[i]?' selected="selected"':'')+'>'+iparag_nevek[i]+'</option>';
	return iparagak;
};

function frissit_birod() {
	$('tolto_ikon').style.display='block';
	sendRequest('attekintes.php',function(req) {
		$('tolto_ikon').style.display='none';
		var valasz=json2obj(req.responseText);
		$('attekintes_birod').innerHTML=json2table([
		['***Pontszámod***',ezresito(valasz.pontszam)]
		,['***Helyezésed***',valasz.helyezes+'. <a href="***zanda_game_url***top/" target="_blank">***globálisan***</a> ('+valasz.kohorsz_helyezes+'. <a href="***zanda_game_url***top/?k='+valasz.kohorsz+'" target="_blank">***a kohorszodban***</a>)']
		,['***Vagyonod***',ezresito(valasz.vagyon)+' SHY']
		,['***Maximális területed az elmúlt 3 hétben***',valasz.max_terulet+'M']
		,['***Teljesen védett területed***',valasz.abszolut_vedett+'M']
		,['***Részben védett területed***',valasz.reszben_vedett+'M']
		,['***Bolygóid száma***',ezresito(valasz.bolygoszam)]
		,['***Bolygólimit***',ezresito(valasz.bolygolimit)+' (***a következő népességszint***: '+ezresito(valasz.limitnoveles)+'*** fő***)']
		,['***Bolygóid össznépessége***',ezresito(valasz.nepesseg)+'*** fő***']
		,['***Flottáid száma***',ezresito(valasz.flottaszam)]
		,['***Flottalimit***',ezresito(valasz.flottalimit)]
		,['***Flottáid összértéke***',ezresito(Math.floor(valasz.ossz_ertek/100))+'***,***'+((valasz.ossz_ertek%100<10)?'0':'')+(valasz.ossz_ertek%100)]
		,['***Legyártott hajóid összértéke***',ezresito(Math.floor(valasz.ossz_legyartott_ertek/100))+'***,***'+((valasz.ossz_legyartott_ertek%100<10)?'0':'')+(valasz.ossz_legyartott_ertek%100)]
		,['***Lelőtt játékos flották összértéke***',ezresito(Math.floor(valasz.lelott_ember/100))+'***,***'+((valasz.lelott_ember%100<10)?'0':'')+(valasz.lelott_ember%100)]
		,['***Lelőtt NPC flották összértéke***',ezresito(Math.floor(valasz.lelott_kaloz/100))+'***,***'+((valasz.lelott_kaloz%100<10)?'0':'')+(valasz.lelott_kaloz%100)]
		,['***Lelőtt idegen flották összértéke***',ezresito(Math.floor(valasz.lelott_zanda/100))+'***,***'+((valasz.lelott_zanda%100<10)?'0':'')+(valasz.lelott_zanda%100)]
		,['***Lelőtt flották összértéke***',ezresito(Math.floor(valasz.lelott_minden/100))+'***,***'+((valasz.lelott_minden%100<10)?'0':'')+(valasz.lelott_minden%100)]
		,['***Napi eladott összforgalom***',ezresito(valasz.napi_tozsdei_eladas)+' SHY']
		],[])
		+'<h2>***Helyezésed***</h2><p><img src="***zanda_game_url***top/user_top.php?u='+valasz.uid+'" alt="" style="height:240px" /></p>';
		$('attekintes_tech').innerHTML=valasz.tech;
		$('attekintes_most_szint').innerHTML='<p>***Ezen szinten elérhető új épületek***:</p>'+json2table(valasz.most_gyarak,[],[
			function(sor) {
				var s='';
				s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[0]+');">';
				s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[0]]+'_index.jpg" />';
				s+='</a>';
				return s;
			},
			function(sor) {
				return '<span style="font-weight: bold">'+sor[1]+'</span>';
			}
		]);
		if (valasz.tech<6) {
			$('tech_kov_hatar').innerHTML='***A következő szinthez szükséges népesség***: <b>'+valasz.kov_hatar+' 000*** fő***</b>.<br />***Ekkor az alábbiak válnak elérhetővé***:';
			$('attekintes_kov_szint').innerHTML=json2table(valasz.kov_gyarak,[],[
				function(sor) {
					var s='';
					s+='<a href="" style="cursor: help" onclick="return jump_to_help(1,'+sor[0]+');">';
					s+='<img src="img/ikonok/'+epuletek_fajlneve[sor[0]]+'_index-ff.jpg" />';
					s+='</a>';
					return s;
				},
				function(sor) {
					return '<span style="font-weight: bold">'+sor[1]+'</span>';
				}
			]);
		} else {
			$('tech_kov_hatar').innerHTML='';
			$('attekintes_kov_szint').innerHTML='';
		}
		$('attekintes_kutatas').innerHTML=json2table([
			['***KP***',ezresito(valasz.kp)],
			['***Eladható KP***',ezresito(valasz.megoszthato_kp)]
		],[]);
		$('attekintes_fejlesztes').innerHTML=json2table(valasz.kf,['***téma***','***KP***','%','***max***','***feltétel***'],[
			function(sor) {return sor[1];},
			function(sor) {return ezresito(sor[2]);},
			function(sor) {return Math.round(sor[2]/sor[3]*100)+'%';},
			function(sor) {return ezresito(sor[3]);},
			function(sor) {
				if (sor[2]==sor[3]) return '<img src="img/ikonok/tick.gif" alt="***kész***" />';
				else {
					if (sor[4]=='')	return '<a href="#" onclick="return fejleszt_kerdez('+sor[0]+')">***fejleszt***</a>';
					else {
						return sor[4]+(sor[5]==''?'':('<span class="kesz">, '+sor[5]+'</span>'));
					}
				}
			}
		],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','right','right','right','left']);
		//penz
		$('attekintes_penztranszfer').innerHTML=json2table([
		['***Napi küldési limit***',ezresito(valasz.penz_adhato_max)+' SHY'],
		['***Felhasznált***',ezresito(valasz.penz_adott)+' SHY ('+(valasz.penz_adhato_max>0?Math.round(valasz.penz_adott/valasz.penz_adhato_max*100):'100')+'%)']
		],[]);
		//premium
		if (valasz.premium==2) {
			$('attekintes_transzfer_osszefoglalo').innerHTML=json2table(valasz.transzfer_osszefoglalo,[''
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[56]+'_index.jpg" alt="'+eroforrasok_neve[56]+'" title="'+eroforrasok_neve[56]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[64]+'_index.jpg" alt="'+eroforrasok_neve[64]+'" title="'+eroforrasok_neve[64]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[65]+'_index.jpg" alt="'+eroforrasok_neve[65]+'" title="'+eroforrasok_neve[65]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[66]+'_index.jpg" alt="'+eroforrasok_neve[66]+'" title="'+eroforrasok_neve[66]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[67]+'_index.jpg" alt="'+eroforrasok_neve[67]+'" title="'+eroforrasok_neve[67]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[68]+'_index.jpg" alt="'+eroforrasok_neve[68]+'" title="'+eroforrasok_neve[68]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[69]+'_index.jpg" alt="'+eroforrasok_neve[69]+'" title="'+eroforrasok_neve[69]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[70]+'_index.jpg" alt="'+eroforrasok_neve[70]+'" title="'+eroforrasok_neve[70]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[71]+'_index.jpg" alt="'+eroforrasok_neve[71]+'" title="'+eroforrasok_neve[71]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[72]+'_index.jpg" alt="'+eroforrasok_neve[72]+'" title="'+eroforrasok_neve[72]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[73]+'_index.jpg" alt="'+eroforrasok_neve[73]+'" title="'+eroforrasok_neve[73]+'" />'
				,'<img src="img/ikonok/'+eroforrasok_fajlneve[74]+'_index.jpg" alt="'+eroforrasok_neve[74]+'" title="'+eroforrasok_neve[74]+'" />'
				,'<span title="'+eroforrasok_neve[150]+'">***KP***</span>'
				],[
				function(sor) {if (sor[0]>0) return '<a href="" onclick="return bolygo_katt('+sor[0]+')" style="font-weight: bold">'+sor[2]+'</a>';else return '***TŐZSDE***';}
				,function(sor) {return transzfer_adat(sor[3]);}
				,function(sor) {return transzfer_adat(sor[4]);}
				,function(sor) {return transzfer_adat(sor[5]);}
				,function(sor) {return transzfer_adat(sor[6]);}
				,function(sor) {return transzfer_adat(sor[7]);}
				,function(sor) {return transzfer_adat(sor[8]);}
				,function(sor) {return transzfer_adat(sor[9]);}
				,function(sor) {return transzfer_adat(sor[10]);}
				,function(sor) {return transzfer_adat(sor[11]);}
				,function(sor) {return transzfer_adat(sor[12]);}
				,function(sor) {return transzfer_adat(sor[13]);}
				,function(sor) {return transzfer_adat(sor[14]);}
				,function(sor) {return transzfer_adat(sor[15]);}
			],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','right','right','right','right','right','right','right','right','right','right','right','right','right']);
		} else {
			$('attekintes_transzfer_osszefoglalo').innerHTML='';
		}
		if (valasz.premium==2) {
			$('attekintes_bolygoid').innerHTML=json2table(valasz.bolygok,['','***név***','***pozíció***','***sorszám***','***védelmi bónusz***','***méret***','***népesség***','***legyártott egyenérték***','***állomásozó egyenérték***'],[
			function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[2]-1]+'32.gif" /></a>';},
			function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>';},
			function(sor) {return '<a href="" onclick="jump_to_xy('+sor[3]+','+sor[4]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[4])+', '+xkoordinata(sor[3])+'</a>';},
			function(sor) {return sor[12]+'.';},
			function(sor) {return sor[11];},
			function(sor) {return sor[10]+'M';},
			function(sor) {return ezresito(sor[7]);},
			function(sor) {return szazadresz(sor[8]);},
			function(sor) {return szazadresz(sor[9]);}
			],null,null,null,['center','left','left','right','right','right','right','right','right']);
		} else {
			$('attekintes_bolygoid').innerHTML=json2table(valasz.bolygok,['','***név***','***pozíció***'],[
			function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[2]-1]+'32.gif" /></a>';},
			function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>';},
			function(sor) {return '<a href="" onclick="jump_to_xy('+sor[3]+','+sor[4]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[4])+', '+xkoordinata(sor[3])+'</a>';}
			]);
		}
		if (valasz.premium==2) {
			if (valasz.flottak.length>0) $('attekintes_flottaid').innerHTML=json2table(valasz.flottak,['','***név***','***tulaj***','***pozíció***','***tevékenység***','***egyenérték***','***morál***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[20]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0 || sor[21]>0)?'*':'');},
				function(sor) {if (sor[20]==1) return '';else return '<a href="" onclick="return user_katt('+sor[22]+')" style="font-weight: bold">'+sor[23]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {
					var flotta_statusz='';
					switch(sor[6]) {
						case 1:flotta_statusz='*** felett állomásozik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felett állomásozik***';break;
						case 2:flotta_statusz='***Várakozik***';break;
						case 3:case 4:flotta_statusz='*** között járőrözik prefix***'+ykoordinata(sor[13])+',&nbsp;'+xkoordinata(sor[12])+' ***és*** '+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** között járőrözik***';break;
						case 5:flotta_statusz='*** felé tart prefix***'+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** felé tart***';break;
						case 6:flotta_statusz='*** felé tart prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart***';break;
						case 7:flotta_statusz='*** felé tart támadólag prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart támadólag***';break;
						case 8:flotta_statusz='*** ellen támad prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen támad***';break;
						case 9:flotta_statusz='*** felé tart portyázni prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart portyázni***';break;
						case 10:flotta_statusz='*** ellen portyázik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen portyázik***';break;
						case 11:flotta_statusz='***Visszavonul*** *** bázisra prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** bázisra***';break;
						case 12:flotta_statusz='*** felé tart prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart***';break;
						case 13:flotta_statusz='*** felé tart támadólag prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart támadólag***';break;
						case 14:flotta_statusz='*** ellen támad prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** ellen támad***';break;
					}
					if (sor[17]>=0) flotta_statusz+=' ('+sor[17]+'m)';
					return flotta_statusz;
				},
				function(sor) {return szazadresz(sor[16]);},
				function(sor) {return Math.floor(sor[19]/10)+'***,***'+(sor[19]%10)+'%';},
				function(sor) {if (sor[24]<0) return '';else return Math.floor(sor[24]/10)+'***,***'+(sor[24]%10)+'%';}
			],null,null,null,['center','left','left','left','left','left','right','right']);
			else $('attekintes_flottaid').innerHTML='';
			//
			var s='';
			if (valasz.kozos_flottak.length>0) s+=json2table(valasz.kozos_flottak,['','***név***','***tulaj***','***pozíció***','***tevékenység***','***egyenérték***','***morál***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[20]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0 || sor[21]>0)?'*':'');},
				function(sor) {if (sor[20]==1) return '';else return '<a href="" onclick="return user_katt('+sor[22]+')" style="font-weight: bold">'+sor[23]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {
					var flotta_statusz='';
					switch(sor[6]) {
						case 1:flotta_statusz='*** felett állomásozik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felett állomásozik***';break;
						case 2:flotta_statusz='***Várakozik***';break;
						case 3:case 4:flotta_statusz='*** között járőrözik prefix***'+ykoordinata(sor[13])+',&nbsp;'+xkoordinata(sor[12])+' ***és*** '+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** között járőrözik***';break;
						case 5:flotta_statusz='*** felé tart prefix***'+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** felé tart***';break;
						case 6:flotta_statusz='*** felé tart prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart***';break;
						case 7:flotta_statusz='*** felé tart támadólag prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart támadólag***';break;
						case 8:flotta_statusz='*** ellen támad prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen támad***';break;
						case 9:flotta_statusz='*** felé tart portyázni prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart portyázni***';break;
						case 10:flotta_statusz='*** ellen portyázik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen portyázik***';break;
						case 11:flotta_statusz='***Visszavonul*** *** bázisra prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** bázisra***';break;
						case 12:flotta_statusz='*** felé tart prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart***';break;
						case 13:flotta_statusz='*** felé tart támadólag prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart támadólag***';break;
						case 14:flotta_statusz='*** ellen támad prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** ellen támad***';break;
					}
					if (sor[17]>=0) flotta_statusz+=' ('+sor[17]+'m)';
					return flotta_statusz;
				},
				function(sor) {return szazadresz(sor[16]);},
				function(sor) {return Math.floor(sor[19]/10)+'***,***'+(sor[19]%10)+'%';},
				function(sor) {if (sor[24]<0) return '';else return Math.floor(sor[24]/10)+'***,***'+(sor[24]%10)+'%';}
			],null,null,null,['center','left','left','left','left','left','right','right']);
			if (valasz.resz_flottak.length>0) s+='<h2>***Részflották***</h2>'+json2table(valasz.resz_flottak,['','***név***','***tulaj***','***pozíció***','***tevékenység***','***egyenérték***','***morál***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[20]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0 || sor[21]>0)?'*':'');},
				function(sor) {if (sor[20]==1) return '';else return '<a href="" onclick="return user_katt('+sor[22]+')" style="font-weight: bold">'+sor[23]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {
					var flotta_statusz='';
					switch(sor[6]) {
						case 1:flotta_statusz='*** felett állomásozik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felett állomásozik***';break;
						case 2:flotta_statusz='***Várakozik***';break;
						case 3:case 4:flotta_statusz='*** között járőrözik prefix***'+ykoordinata(sor[13])+',&nbsp;'+xkoordinata(sor[12])+' ***és*** '+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** között járőrözik***';break;
						case 5:flotta_statusz='*** felé tart prefix***'+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** felé tart***';break;
						case 6:flotta_statusz='*** felé tart prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart***';break;
						case 7:flotta_statusz='*** felé tart támadólag prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart támadólag***';break;
						case 8:flotta_statusz='*** ellen támad prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen támad***';break;
						case 9:flotta_statusz='*** felé tart portyázni prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart portyázni***';break;
						case 10:flotta_statusz='*** ellen portyázik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen portyázik***';break;
						case 11:flotta_statusz='***Visszavonul*** *** bázisra prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** bázisra***';break;
						case 12:flotta_statusz='*** felé tart prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart***';break;
						case 13:flotta_statusz='*** felé tart támadólag prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart támadólag***';break;
						case 14:flotta_statusz='*** ellen támad prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** ellen támad***';break;
					}
					if (sor[17]>=0) flotta_statusz+=' ('+sor[17]+'m)';
					return flotta_statusz;
				},
				function(sor) {return szazadresz(sor[16]);},
				function(sor) {return Math.floor(sor[19]/10)+'***,***'+(sor[19]%10)+'%';},
				function(sor) {if (sor[24]<0) return '';else return Math.floor(sor[24]/10)+'***,***'+(sor[24]%10)+'%';}
			],null,null,null,['center','left','left','left','left','left','right','right']);
			$('attekintes_kozos_flottaid').innerHTML=s;
			//
			if (hajoszuro6(valasz.hajok).length>0) $('attekintes_hajoid').innerHTML=json2table(hajoszuro6(valasz.hajok),['','','***legyártott***','***egyenérték***','***fellőtt***','***egyenérték***','***összes***','***egyenérték***'],[
			function(sor) {if (sor[0]>0) return '<a href="" style="cursor: help" onclick="return jump_to_help(2,'+sor[0]+')"><img src="img/ikonok/'+eroforrasok_fajlneve[sor[0]]+'_index.gif" /></a>';else return '';},
			function(sor) {if (sor[0]>0) return '<span style="font-weight: bold">'+eroforrasok_neve[sor[0]]+'</span>';else return '<span style="font-weight: bold">***ÖSSZES***</span>';},
			function(sor) {return ezresito(sor[1]);},
			function(sor) {return szazadresz(sor[2]);},
			function(sor) {return ezresito(sor[3]);},
			function(sor) {return szazadresz(sor[4]);},
			function(sor) {return ezresito(sor[5]);},
			function(sor) {return szazadresz(sor[6]);}
			],['34px','120px','70px','110px','70px','110px','70px','110px'],null,null,['center','left','right','right','right','right','right','right']);
			else $('attekintes_hajoid').innerHTML='';
		} else {
			if (valasz.flottak.length>0) $('attekintes_flottaid').innerHTML=json2table(valasz.flottak,['','***név***','***tulaj***','***pozíció***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[5]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0)?'*':'');},
				function(sor) {if (sor[5]==1) return '';else return '<a href="" onclick="return user_katt('+sor[6]+')" style="font-weight: bold">'+sor[7]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {if (sor[9]<0) return '';else return Math.floor(sor[9]/10)+'***,***'+(sor[9]%10)+'%';}
			],null,null,null,['center','left','left','left','right']);
			else $('attekintes_flottaid').innerHTML='';
			//
			var s='';
			if (valasz.kozos_flottak.length>0) s+=json2table(valasz.kozos_flottak,['','***név***','***tulaj***','***pozíció***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[5]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0)?'*':'');},
				function(sor) {if (sor[5]==1) return '';else return '<a href="" onclick="return user_katt('+sor[6]+')" style="font-weight: bold">'+sor[7]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {if (sor[9]<0) return '';else return Math.floor(sor[9]/10)+'***,***'+(sor[9]%10)+'%';}
			],null,null,null,['center','left','left','left','right']);
			if (valasz.resz_flottak.length>0) s+='<h2>***Részflották***</h2>'+json2table(valasz.resz_flottak,['','***név***','***tulaj***','***pozíció***','***részesedés***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[5]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>'+((sor[4]>0)?'*':'');},
				function(sor) {if (sor[5]==1) return '';else return '<a href="" onclick="return user_katt('+sor[6]+')" style="font-weight: bold">'+sor[7]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {if (sor[9]<0) return '';else return Math.floor(sor[9]/10)+'***,***'+(sor[9]%10)+'%';}
			],null,null,null,['center','left','left','left','right']);
			$('attekintes_kozos_flottaid').innerHTML=s;
			//
			$('attekintes_hajoid').innerHTML='<p>***Ez a menüpont csak emelt szintű prémium esetén érhető el.***</p>';
		}
	});
	return false;
};

function frissit_profil() {
	$('tolto_ikon').style.display='block';
	sendRequest('profil_adatok.php',function(req) {
		$('tolto_ikon').style.display='none';
		var valasz=json2obj(req.responseText);
		var s='';
		if (valasz.epp_most_helyettes_id==0) {
			s+=json2table([
			['***Neved***','<a href="" onclick="return user_katt('+valasz.id+')">'+valasz.nev+'</a>'],
			['***Azonosítód***',valasz.id],
			['***Ajánló linked***','***zanda_game_url***?ref_uid='+valasz.id],
			['***E-mail címed***',valasz.mail],
			['***Regisztráltál***',ezresito(valasz.reg)+' ***napja***'],
			['***Aki meghívott***',(valasz.meghivo_id>0)?('<a href="" onclick="return user_katt('+valasz.meghivo_id+')">'+valasz.meghivo_nev+'</a>'):'***senki***'],
			['<a href="***zanda_homepage_url***premium/" target="_blank" class="kulso_link">***Prémium vagy?***</a>',((valasz.premium==2)?'***Igen, emeltszintű.***':((valasz.premium==1)?'***Igen.***':'***Nem.***'))+((valasz.premium_szint==1)?(' (<a href="" onclick="if (confirm(\'***Biztos, hogy emelt szintűre akarsz váltani? Ha átváltasz, utána nem lehet visszaváltani!***\')) return upgrade_premium();return false;">***Upgrade emelt szintűre***</a>)'):(valasz.premium==0?' (***De már upgrade-eltél emelt szintűre.***)':''))],
			['***Meddig vagy prémium?***',valasz.premium_meddig],
			['***Számlázási neved***','<div id="szamla_nev_sima_div" style="display:block">'+valasz.szamla_nev+' <a href="" onclick="return szamla_szerk_toggle(\'nev\')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a></div><div id="szamla_nev_szerk_div" style="display:none"><input type="text" id="szamla_nev_szerk_input" value="'+valasz.szamla_nev_input+'" class="szovegmezo" style="width:300px" /> <a href="" onclick="return szamla_szerk_save(\'nev\')" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>'],
			['***Számlázási címed***','<div id="szamla_cim_sima_div" style="display:block">'+valasz.szamla_cim+' <a href="" onclick="return szamla_szerk_toggle(\'cim\')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a></div><div id="szamla_cim_szerk_div" style="display:none"><input type="text" id="szamla_cim_szerk_input" value="'+valasz.szamla_cim_input+'" class="szovegmezo" style="width:500px" /> <a href="" onclick="return szamla_szerk_save(\'cim\')" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>']
			],[],null,null,null,null,['left','left']);
			s+='<h2>***Karrier***</h2>';
			if (valasz.karrier) {
				s+='<p>'+karrier_nevek[valasz.karrier-1]+'</p>';
			} else {
				s+='<p>***Karrier warning*** '+ezresito(valasz.nepesseg)+'</p>';
				s+='<ul class="classic_lista">';
				for(var k=1;k<=4;k++) {
					s+='<li>';
					if (valasz.leendo_karrier==k) s+='<b>'+karrier_nevek[k-1]+'</b>';
					else s+='<a href="#" onclick="return select_karrier('+k+',0)">'+karrier_nevek[k-1]+'</a>';
					s+='</li>';
				}
				s+='</ul>';
				s+='<h3>***Azonnali karrier választás***</h3>';
				s+='<p>***Vigyázz! Ez a választásod azonnali és erre a fordulóra végleges.***</p>';
				s+='<ul class="classic_lista">';
					s+='<li><a href="#" onclick="if (confirm(\'***Biztos?***\')) select_karrier(3,1);return false">'+karrier_nevek[2]+' / '+speci_nevek[2][2]+'</a></li>';
					s+='<li><a href="#" onclick="if (confirm(\'***Biztos?***\')) select_karrier(4,1);return false">'+karrier_nevek[3]+'</a></li>';
				s+='</ul>';
			}
			if (valasz.karrier>0) {
				s+='<h2>***Specializáció***</h2>';
				if (valasz.speci) {
					s+='<p>'+speci_nevek[valasz.karrier-1][valasz.speci-1]+'</p>';
				} else {
					s+='<ul class="classic_lista">';
					for(var k=1;k<=valasz.elerheto_specik.length;k++) if ((valasz.karrier!=2)||(k!=4)) {//zelota kulon
						s+='<li>';
						if (valasz.elerheto_specik[k-1]==0) s+=speci_nevek[valasz.karrier-1][k-1];
						else s+='<a href="#" onclick="if (confirm(\'***Biztos?***\')) select_speci('+k+');return false">'+speci_nevek[valasz.karrier-1][k-1]+'</a>';
						s+='</li>';
					}
					s+='</ul>';
				}
				if (valasz.karrier==2) if (valasz.speci!=4) if (valasz.elerheto_specik[3]) {//zelota
					s+='<h3>'+speci_nevek[1][3]+'</h3>';
					s+='<p>***Zélóta warning.*** <a href="#" onclick="if (confirm(\'***Biztos?***\')) select_speci(4);return false">***Mehet!***</a></p>';
				}
			}
			if (valasz.beallitasok) {
				s+='<h2>***Beállítások***</h2>'+json2table([
				['***Bolygó adatlap***/***Iparágak***',valasz.beallitasok.iparag_jelzok?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'iparag_jelzok\',0)">***ne mutasd***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'iparag_jelzok\',1)">***mutasd***</a>)')]
				,['***Bolygó adatlap***/***Gyárikonok***',valasz.beallitasok.gyar_ikonok?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'gyar_ikonok\',0)">***ne mutasd***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'gyar_ikonok\',1)">***mutasd***</a>)')]
				,['***Flottalista***/***Közös flották***',valasz.beallitasok.kozos_flottak_listaban?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'kozos_flottak_listaban\',0)">***ne mutasd***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'kozos_flottak_listaban\',1)">***mutasd***</a>)')]
				,['***Email értesítés***/***Építési lista kifogy***',valasz.beallitasok.email_noti_eplista?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'email_noti_eplista\',0)">***nem kérek***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'email_noti_eplista\',1)">***kérek***</a>)')]
				,['***Email értesítés***/***Minden megépül***',valasz.beallitasok.email_noti_epites_alatt?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'email_noti_epites_alatt\',0)">***nem kérek***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'email_noti_epites_alatt\',1)">***kérek***</a>)')]
				,['***Munkahelybarát kinézet*** (***gagyi verzió***)',(valasz.beallitasok.css_munkahelyi?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'css_munkahelyi\',0)">***kikapcsolás***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'css_munkahelyi\',1)">***bekapcsolás***</a>)'))+' (***átváltás után nyomd meg a Ctrl-R-t***)']
				,['***Chat***/***Magyar***',valasz.beallitasok.chat_hu?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'chat_hu\',0)">***ne mutasd***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'chat_hu\',1)">***mutasd***</a>)')]
				,['***Chat***/***Angol***',valasz.beallitasok.chat_en?('<img src="img/ikonok/tick.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'chat_en\',0)">***ne mutasd***</a>)'):('<img src="img/ikonok/cross.gif" alt="" /> (<a href="#" onclick="return user_beallitasok(\'chat_en\',1)">***mutasd***</a>)')]
				,['***Plecsnik alapértelmezett nyilvánossága***',
					((valasz.beallitasok.badge_pub==0)?'<b>***privát***</b>':'<a href="#" onclick="return user_beallitasok(\'badge_pub\',0)">***legyen privát***</a>')
					+' / '
					+((valasz.beallitasok.badge_pub==1)?'<b>***szöviben látható***</b>':'<a href="#" onclick="return user_beallitasok(\'badge_pub\',1)">***legyen szöviben látható***</a>')
					+' / '
					+((valasz.beallitasok.badge_pub==2)?'<b>***publikus***</b>':'<a href="#" onclick="return user_beallitasok(\'badge_pub\',2)">***legyen publikus***</a>')
				]
				],[],null,null,null,null,['left','left']);
			}
			s+='<h2>***Helyettesítés***</h2>'+json2table([
			['***Ki helyettesíthet?***','<a href="" onclick="return user_katt('+valasz.helyettes_id+')">'+valasz.helyettes_id_nev+'</a> <form style="display: inline" onsubmit="return set_new_proxy()"><input type="text" id="uj_helyettes_nev" class="bolygonev" /> <input type="submit" value="***Új helyettes***"></form>'],
			['***Mennyi időd járt le?***',sec2hms(valasz.helyettesitett_ido)],
			['***Mennyi időd van még?***',sec2hms(3600*24-valasz.helyettesitett_ido)]
			],[],null,null,null,null,['left','left']);
		} else {
			s+=json2table([
			['***Neved***','<a href="" onclick="return user_katt('+valasz.id+')">'+valasz.nev+'</a>'],
			['***Azonosítód***',valasz.id],
			['***Ajánló linked***','***zanda_game_url***?ref_uid='+valasz.id],
			['***Regisztráltál***',ezresito(valasz.reg)+' ***napja***'],
			['<a href="***zanda_homepage_url***premium/" target="_blank" class="kulso_link">***Prémium vagy?***</a>',((valasz.premium==2)?'***Igen, emeltszintű.***':((valasz.premium==1)?'***Igen.***':'***Nem.***'))+((valasz.premium_szint==1)?(' (<a href="" onclick="if (confirm(\'***Biztos, hogy emelt szintűre akarsz váltani? Ha átváltasz, utána nem lehet visszaváltani!***\')) return upgrade_premium();return false;">***Upgrade emelt szintűre***</a>)'):(valasz.premium==0?' (***De már upgrade-eltél emelt szintűre.***)':''))],
			['***Meddig vagy prémium?***',valasz.premium_meddig]
			],[],null,null,null,null,['left','left']);
			s+='<h2>***Helyettesítés***</h2>'+json2table([
			['***Mennyi időd járt le?***',sec2hms(valasz.helyettesitett_ido)],
			['***Mennyi időd van még?***',sec2hms(3600*24-valasz.helyettesitett_ido)]
			],[],null,null,null,null,['left','left']);
		}
		s+=(valasz.premium>0?(
			'<h2>***Jegyzeteid*** <span style="font-size:8pt">(***csak te láthatod***)</span></h2>'+
			json2table(valasz.jegyzetek,[],[
			function(sor) {return '<p id="jegyzet_p_'+sor[0]+'" class="post_it_jegyzet" style="display:block">'+sor[2]+'</p><div id="jegyzet_div_'+sor[0]+'" style="position:relative;display:none;width: 520px"><textarea id="jegyzet_ta_'+sor[0]+'" class="post_it_szerk">'+sor[5]+'</textarea><a href="" onclick="return jegyzet_szerk_save('+sor[0]+')" style="position:absolute;top:0;right:0" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>';},
			function(sor) {
				var s='';
				if (sor[3]>1) s+='<a href="" onclick="return jegyzet_atsorol('+sor[0]+',-1)" title="***előresorol***"><img src="img/ikonok/arrow_up.gif" /></a>';
				else s+='<img src="img/ikonok/arrow_up-ff.gif" />';
				if (sor[3]<sor[4]) s+=' <a href="" onclick="return jegyzet_atsorol('+sor[0]+',1)" title="***hátrasorol***"><img src="img/ikonok/arrow_down.gif" /></a>';
				else s+=' <img src="img/ikonok/arrow_down-ff.gif" />';
				s+=' <a href="" onclick="return jegyzet_szerk_toggle('+sor[0]+')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a>';
				return s+' <a href="" onclick="return jegyzet_torol('+sor[0]+')" title="***töröl***"><img src="img/ikonok/cross.gif" /></a>';
			}
			],['520px','80px'],function(sor) {return ' valign="top"';},null,['left','center'])+
			'<h3><a href="" onclick="return toggle(\'jegyzet_div_0\')">***Új jegyzet***</a></h3><div id="jegyzet_div_0" style="position:relative;display:none;width: 520px"><textarea id="jegyzet_ta_0" class="post_it_szerk"></textarea><a href="" onclick="return jegyzet_szerk_save(0)" style="position:absolute;top:0;right:0" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>'
		):'');
		s+='<h2>***Barátaid aktivitása***</h2>'
		+json2table(valasz.akik_megosztottak_veled,['','***név***','***utolsó aktivitás***','***szövetség***','***pontszám***','***bolygók száma***'],[
		function(sor) {
			if (sor[7].length>0) return '<img src="img/user_avatarok/'+sor[7]+'" /> ';
			else return '<img src="img/ikonok/fantom_avatar.gif" /> ';
		},
		function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
		function(sor) {return (sor[2]>60?(Math.floor(sor[2]/60)+' ***óra*** '+(sor[2]-60*Math.floor(sor[2]/60))+' ***perce***'):(sor[2]+' ***perce***'));},
		function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[3]+')">'+sor[4]+'</a>';},
		function(sor) {return ezresito(sor[5]);},
		function(sor) {return sor[6];}
		],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','center','left','right','right']);
		s+='<h2>***Akikkel megosztottad az aktivitásodat***</h2>'
		+'<form onsubmit="return aktivitas_megosztasa()">'+json2table(valasz.akikkel_megosztottad,['','***név***',''],[
		function(sor) {
			if (sor[2].length>0) return '<img src="img/user_avatarok/'+sor[2]+'" /> ';
			else return '<img src="img/ikonok/fantom_avatar.gif" /> ';
		},
		function(sor) {
			if (sor[0]>0) return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';
			else return '<input type="text" id="aktivitas_megosztas_ujnev" class="szovegmezo" style="width:150px" />';
		},
		function(sor) {
			if (sor[0]>0) return '<a href="" onclick="return aktivitas_megosztas_torlese('+sor[0]+')" title="***mégsem***"><img src="img/ikonok/cross.gif" /></a>';
			else return '<input type="submit" value="***Megosztom vele***" />';
		}
		],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','center'])+'</form>';
		s+=(
			'<h2>***Mini avatárod***</h2>'+
			((valasz.avatar_fajlnev=='')?'<p>***nincs***</p>':('<p><img src="img/user_avatarok/'+valasz.avatar_fajlnev+'?'+Math.random()+'" id="uj_user_avatar_ikon" /> <span style="font-size:8pt">(<a href="" onclick="if (confirm(\'***Biztosan törlöd a mini avatárod?***\')) user_avatar_torlese();return false">***töröl***</a>)</span></p>'))+
			'<form action="user_avatar_feltoltese.php" method="post" enctype="multipart/form-data" target="uj_user_avatar_iframe" onsubmit="inline_toggle(\'uj_user_avatar_tolto\');return true" >'+
			'<p>***Legfeljebb 32x32-es JPG, PNG vagy GIF fájl***: <input name="kep" type="file" /></p>'+
			'<p><input type="submit" value="***Feltölt!***" /> <img src="img/ikonok/ajax-loader.gif" id="uj_user_avatar_tolto" style="display:none" /></p>'+
			'</form>'+
			'<iframe id="uj_user_avatar_iframe" name="uj_user_avatar_iframe" src="#" style="display:none"></iframe>'
		);
		s+=(valasz.premium>0?(
			'<h2>***Avatárod***</h2>'+
			((valasz.nagy_avatar_fajlnev=='')?'<p>***nincs***</p>':('<p><img src="img/user_nagy_avatarok/'+valasz.nagy_avatar_fajlnev+'?'+Math.random()+'" id="uj_user_nagy_avatar_ikon" /> <span style="font-size:8pt">(<a href="" onclick="if (confirm(\'***Biztosan törlöd az avatárod?***\')) user_nagy_avatar_torlese();return false">***töröl***</a>)</span></p>'))+
			'<form action="user_nagy_avatar_feltoltese.php" method="post" enctype="multipart/form-data" target="uj_user_nagy_avatar_iframe" onsubmit="inline_toggle(\'uj_user_nagy_avatar_tolto\');return true" >'+
			'<p>***Legfeljebb 320x320-as JPG, PNG vagy GIF fájl***: <input name="kep" type="file" /></p>'+
			'<p><input type="submit" value="***Feltölt!***" /> <img src="img/ikonok/ajax-loader.gif" id="uj_user_nagy_avatar_tolto" style="display:none" /></p>'+
			'</form>'+
			'<iframe id="uj_user_nagy_avatar_iframe" name="uj_user_nagy_avatar_iframe" src="#" style="display:none"></iframe>'
		):'');
		s+=(valasz.egyeb_info?(
		'<h2><a href="" onclick="return toggle(\'admin_profil\')">ADMIN</a></h2><div id="admin_profil" style="display:none">'+json2table(valasz.egyeb_info,[])+'</div>'
		):'');
		$('profil_alap').innerHTML=s;
		new actb($('aktivitas_megosztas_ujnev'),'ajax_autocomplete_userek',0);
		if ($('uj_helyettes_nev')) new actb($('uj_helyettes_nev'),'ajax_autocomplete_userek',0);
	});
	return false;
};

function frissit_user() {
	$('tolto_ikon').style.display='block';
	sendRequest('user_adatok.php?id='+aktiv_user,function(req) {
		$('tolto_ikon').style.display='none';
		var valasz=json2obj(req.responseText);
		if (valasz.letezik) {
			var tagtabla='';
			if (valasz.te_vagy) {
				if (valasz.premium>0) tagtabla='<h2>***Egyéb*** <span style="font-size:8pt">(***mindenki látja, aki megnézi az adatlapodat***)</span></h2>'+json2table(valasz.tagek,[],[
					function(sor) {return sor[1];},
					function(sor) {return sor[2];},
					function(sor) {
						var s='';
						if (sor[3]>1) s+='<a href="" onclick="return usertag_atsorol('+sor[0]+',-1)" title="***előresorol***"><img src="img/ikonok/arrow_up.gif" /></a>';
						else s+='<img src="img/ikonok/arrow_up-ff.gif" />';
						if (sor[3]<sor[4]) s+=' <a href="" onclick="return usertag_atsorol('+sor[0]+',1)" title="***hátrasorol***"><img src="img/ikonok/arrow_down.gif" /></a>';
						else s+=' <img src="img/ikonok/arrow_down-ff.gif" />';
						s+=' <a href="" onclick="return usertag_szerk('+sor[0]+',\''+sor[5]+'\',\''+sor[6]+'\')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a>';
						return s+' <a href="" onclick="return usertag_torol('+sor[0]+')" title="***töröl***"><img src="img/ikonok/cross.gif" /></a>';
					}
				])+'<p><a href="" onclick="return usertag_szerk(0,\'\',\'\')">***új sor***</a></p>';
			} else {
				if (valasz.tagek.length>0) tagtabla='<h2>***Egyéb***</h2>'+json2table(valasz.tagek,[],[
					function(sor) {return sor[1];},
					function(sor) {return sor[2];}
				]);
			}
			var badge_str='';
			if (valasz.badgek) {
				for(id in valasz.badgek) {
					if (badge_str!='') badge_str+=' ';
					badge_str+='<div style="position:relative;display:inline-block;width:64px;height:64px;background:transparent url(img/ikonok/zanda_badge_'+valasz.badgek[id][3]+'.png)" title="'+valasz.badgek[id][5]+(valasz.badgek[id][4]!=''?(' @ '+valasz.badgek[id][4]):'')+'">';
					badge_str+='<div style="text-align:center;font-size:14pt;font-weight:bold;color:rgb(42,43,45);margin-top:20px">'+valasz.badgek[id][1]+'</div>';
					badge_str+='<div style="text-align:center;font-size:8pt;font-weight:bold;color:rgb(42,43,45);margin-top:0px">'+valasz.badgek[id][2]+'</div>';
					if (valasz.te_vagy) badge_str+='<div style="position:absolute;right:0;bottom:5px"><a href="#" onclick="return set_badge_pub('+valasz.badgek[id][0]+','+(valasz.badgek[id][6]<2?(valasz.badgek[id][6]+1):0)+')" title="'+(valasz.badgek[id][6]==0?'***privát***':(valasz.badgek[id][6]==1?'***szöviben látható***':'***publikus***'))+'"><img src="img/ikonok/lock'+(valasz.badgek[id][6]==0?'':(valasz.badgek[id][6]==1?'_break':'_open'))+'.png" style="width:8px;height:8px" alt="" /></a></div>';
					badge_str+='</div>';
				}
			}
			var avatar_html='';
			if (valasz.avatar_fajlnev.length>0) avatar_html='<img src="img/user_avatarok/'+valasz.avatar_fajlnev+'" /> ';
			else avatar_html='<img src="img/ikonok/fantom_avatar.gif" /> ';
			if (valasz.te_vagy) $('aktiv_user_neve').innerHTML=avatar_html+valasz.nev;
			else $('aktiv_user_neve').innerHTML=avatar_html+valasz.nev+' <a href="" onclick="window.open(\'level_irasa.php?kinek='+aktiv_user+'\',\'\',\'width=600,height=350\');return false;" style="font-size:8pt"><img src="img/ikonok/mail_edit.gif" /> ***levél küldése***</a>'+((valasz.viewer_premium==2)?(' <span style="font-size:8pt">(<a href="#" onclick="$(\'level_kereso_div\').style.display=\'block\';$(\'level_kereso_input_felado\').value=\''+valasz.nev+'\';return oldal_nyit(\'komm\');">***levelezésed vele***</a>)</span>'):'');
			$('aktiv_user_adatai').innerHTML='<div style="float:left;width:410px">'+json2table([
			['***Karrier***',(valasz.karrier>0)?karrier_nevek[valasz.karrier-1]:'-'],
			['***Specializáció***',((valasz.karrier>0)&&(valasz.speci>0))?speci_nevek[valasz.karrier-1][valasz.speci-1]:'-'],
			['***Regisztrált***',ezresito(valasz.reg)+' ***napja***'+((valasz.torles_alatt>0)?' (***törölt***)':'')],
			['***Szövetség***',(valasz.szovetseg.id>0)?('<a href="" onclick="return szovetseg_katt('+valasz.szovetseg.id+')">'+valasz.szovetseg.nev+'</a>'):'***magányos farkas***'],
			['***Tisztség***',(valasz.szovetseg.id>0)?valasz.szovetseg.tisztseg:'-'],
			['***Pontszám***',(valasz.pontszam=='?')?'?':ezresito(valasz.pontszam)],
			['***Helyezés***',valasz.helyezes+'.'],
			['***Nyelv***',(valasz.nyelv=='hu')?'<img src="img/flag_hu.gif" alt="***magyar***" style="vertical-align: -1px" /> ***magyar***':'<img src="img/flag_gb.gif" alt="***angol***" style="vertical-align: -1px" /> ***angol***']
			],[])
			+'</div><div style="float:right;width:340px;text-align:left">'+((valasz.nagy_avatar_fajlnev.length>0)?'<img src="img/user_nagy_avatarok/'+valasz.nagy_avatar_fajlnev+'?'+valasz.kepfajl_random+'" />':'')+'</div><div style="clear:both"></div>'
			+(valasz.badgek?(
				'<h2>***Plecsnik*** <span style="font-size:8pt">(<a href="/badge/" target="_blank">***plecsnik listája***</a>)</span></h2>'
				+'<p>'+badge_str+'</p>'
			):'')
			+'<h2>***Helyezés***</h2><p><img src="***zanda_game_url***top/user_top.php?u='+valasz.id+'" alt="" style="height:240px" /></p>'
			+(valasz.admin?('<h2>Admin</h2>'+
			json2table([
			['Azonosító','#'+valasz.id],
			['Email',valasz.email+' <form onsubmit="return set_user_email();" style="display:inline"><input type="name" id="user_uj_email_cime" class="bolygonev" /> <input type="submit" value="Email cím javítása" /></form>'],
			['Utolsó aktivitás',valasz.uccso_akt>60?(Math.floor(valasz.uccso_akt/60)+' óra '+(valasz.uccso_akt-60*Math.floor(valasz.uccso_akt/60))+' perce'):(valasz.uccso_akt+' perce')],
			['Zanda_ref',valasz.zanda_ref],
			['Aki meghívta',((valasz.meghivo_id>0)?('<a href="" onclick="return user_katt('+valasz.meghivo_id+')">'+valasz.meghivo_nev+'</a>'):'senki')+' <form onsubmit="return set_user_meghivo();" style="display:inline"><input type="name" id="meghivo_neve" class="bolygonev" /> <input type="submit" value="Meghívó beállítása" /></form>'],
			['Számlázási név','<div id="szamla_nev_sima_div" style="display:block">'+valasz.szamla_nev+' <a href="" onclick="return szamla_szerk_toggle(\'nev\')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a></div><div id="szamla_nev_szerk_div" style="display:none"><input type="text" id="szamla_nev_szerk_input" value="'+valasz.szamla_nev_input+'" class="szovegmezo" style="width:300px" /> <a href="" onclick="return szamla_szerk_save_masnak('+valasz.id+',\'nev\')" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>'],
			['Számlázási cím','<div id="szamla_cim_sima_div" style="display:block">'+valasz.szamla_cim+' <a href="" onclick="return szamla_szerk_toggle(\'cim\')" title="***szerkeszt***"><img src="img/ikonok/szerk.gif" /></a></div><div id="szamla_cim_szerk_div" style="display:none"><input type="text" id="szamla_cim_szerk_input" value="'+valasz.szamla_cim_input+'" class="szovegmezo" style="width:500px" /> <a href="" onclick="return szamla_szerk_save_masnak('+valasz.id+',\'cim\')" title="***ment***"><img src="img/ikonok/disk.gif" /></a></div>'],
			['Prémium?',(valasz.premium==1)?'Igen.':((valasz.premium==2)?'Igen, emeltszintű.':'Nem.')],
			['Meddig prémium?',valasz.premium_meddig+' <input type="name" id="premium_plusz_hany_nap" class="hajoszam" /> nap <input type="name" id="premium_upgrade_is" class="hajoszam" value="0" /> upgrade <a href="" onclick="return set_user_premium(1)">bank</a> <a href="" onclick="return set_user_premium(2)">paypal</a> <a href="" onclick="return set_user_premium(3)">sms</a> <a href="" onclick="return set_user_premium(0)">egyéb/kápé</a> <a href="" onclick="return set_user_premium(5)">twitter</a> <a href="" onclick="return set_user_premium(6)">ajándék</a>'],
			['BANK','<a href="#" onclick="return set_user_premium_ft(1,490)">490</a> <a href="#" onclick="return set_user_premium_ft(1,950)">950</a> <a href="#" onclick="return set_user_premium_ft(1,1390)">1390</a> <a href="#" onclick="return set_user_premium_ft(1,1790)">1790</a> <a href="#" onclick="return set_user_premium_ft(1,2190)">2190</a><br /><a href="#" onclick="return set_user_premium_ft(1,790)">790</a> <a href="#" onclick="return set_user_premium_ft(1,1550)">1550</a> <a href="#" onclick="return set_user_premium_ft(1,2290)">2290</a> <a href="#" onclick="return set_user_premium_ft(1,2890)">2890</a> <a href="#" onclick="return set_user_premium_ft(1,3490)">3490</a>'],
			['PAYPAL','<a href="#" onclick="return set_user_premium_ft(2,490)">490</a> <a href="#" onclick="return set_user_premium_ft(2,950)">950</a> <a href="#" onclick="return set_user_premium_ft(2,1390)">1390</a> <a href="#" onclick="return set_user_premium_ft(2,1790)">1790</a> <a href="#" onclick="return set_user_premium_ft(2,2190)">2190</a><br /><a href="#" onclick="return set_user_premium_ft(2,790)">790</a> <a href="#" onclick="return set_user_premium_ft(2,1550)">1550</a> <a href="#" onclick="return set_user_premium_ft(2,2290)">2290</a> <a href="#" onclick="return set_user_premium_ft(2,2890)">2890</a> <a href="#" onclick="return set_user_premium_ft(2,3490)">3490</a>'],
			['Twitter név','<a href="http://twitter.com/'+valasz.twitter_nev+'">'+valasz.twitter_nev+'</a>']
			],[])):'')
			+tagtabla;
			$('aktiv_user_bolygoszama').innerHTML=valasz.bolygok.length;
			$('aktiv_user_bolygoi').innerHTML=json2table(valasz.bolygok,['','***név***','***pozíció***','***méret***','***ügynökeid száma***'],[
				function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')"><img src="img/ikonok/bolygo_'+bolygo_osztalyok[sor[2]-1]+'32.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return bolygo_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[3]+','+sor[4]+');return oldal_nyit(\'terkep\');" title="térkép">'+ykoordinata(sor[4])+', '+xkoordinata(sor[3])+'</a>';},
				function(sor) {return sor[8]+'M';},
				function(sor) {
					if (sor[7]>0) return sor[7];
					return '';
				}
			],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','left','center','right']);
		} else {
			$('aktiv_user_neve').innerHTML='';
			$('aktiv_user_adatai').innerHTML='';
			$('aktiv_user_bolygoi').innerHTML='';
		}
	});
	return false;
};

function frissit_szovetseg() {
	$('tolto_ikon').style.display='block';
	sendRequest('szovetseg_adatok.php?id='+aktiv_szovetseg,function(req) {
		$('tolto_ikon').style.display='none';
		var valasz=json2obj(req.responseText);
		//
		if (valasz.letezik || valasz.nincs_bolygod) {
			$('szovetsegek_listaja').innerHTML=json2table(valasz.szovetsegek,['','***név***','***alapító***','***megnevezése***','***alapítva***<br />(***napja***)','***mottó***','***taglétszám***'],[
			function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[0]+')"><img src="img/'+(sor[8].length>0?('minicimerek/'+sor[8]):'ikonok/fantom_szovetseg.gif')+'" /></a>';},
			function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[0]+')"'+((sor[10]==0)?' style="font-weight:bold"':'')+'>'+sor[1]+'</a>';},
			function(sor) {return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';},
			function(sor) {return sor[7];},
			function(sor) {return sor[4];},
			function(sor) {return sor[5];},
			function(sor) {return sor[6];}
			],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','left','left','right','left','right']);
		} else {
			$('szovetsegek_listaja').innerHTML='***Ha új játékos vagy...***'+json2table(valasz.szovetsegek,['***távolság***','','***név***','***alapító***','***megnevezése***','***alapítva***<br />(***napja***)','***mottó***','***taglétszám***'],[
			function(sor) {return ezresito(sor[9]);},
			function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[0]+')"><img src="img/'+(sor[8].length>0?('minicimerek/'+sor[8]):'ikonok/fantom_szovetseg.gif')+'" /></a>';},
			function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[0]+')"'+((sor[10]==0)?' style="font-weight:bold"':'')+'>'+sor[1]+'</a>';},
			function(sor) {return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';},
			function(sor) {return sor[7];},
			function(sor) {return sor[4];},
			function(sor) {return sor[5];},
			function(sor) {return sor[6];}
			],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['right','center','left','left','left','right','left','right']);
		}
		//
		if (valasz.letezik) {
			aktiv_szovetseg=valasz.id;frissit_szov_forum();
			var minicimer_html='';
			if (valasz.minicimer_fajlnev.length>0) minicimer_html='<img src="img/minicimerek/'+valasz.minicimer_fajlnev+'?'+valasz.kepfajl_random+'" /> ';
			else minicimer_html='<img src="img/ikonok/fantom_szovetseg.gif" /> ';
			var egyeb='';
			$('aktiv_szovetseg_neve').innerHTML=minicimer_html+'['+valasz.rovid_nev+'] '+valasz.nev+egyeb;
			$('aktiv_szovetseg_neve').style.display='block';
			$('szovetsegek_listaja').style.display='none';
			if (valasz.tag_vagy) {
				if (valasz.jogaid[0]) $('szov_forum_uj_tema_belso_span').style.display='inline';else $('szov_forum_uj_tema_belso_span').style.display='none';
				if (valasz.jogaid[0]) $('szov_forum_uj_tema_vendeg_span').style.display='inline';else $('szov_forum_uj_tema_vendeg_span').style.display='none';
				if (valasz.jogaid[0]) $('szov_forum_szerk_tema_belso_span').style.display='inline';else $('szov_forum_szerk_tema_belso_span').style.display='none';
				if (valasz.jogaid[0]) $('szov_forum_szerk_tema_vendeg_span').style.display='inline';else $('szov_forum_szerk_tema_vendeg_span').style.display='none';
				//
				sajat_szovetseg=1;
				var s2='';
				if (valasz.meghivoid.length>0) {
					s2+='<h2>***Meghívások számodra***</h2>';
					s2+=json2table(valasz.meghivoid,['***ki***','***hova***','***mikor***','***elfogadás***','***elutasítás***'],[
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
					function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
					function(sor) {return sor[4]+' napja';},
					function(sor) {return '<a href="" onclick="if (confirm(\'***Ha elfogadod ezt a meghívást, automatikusan kilépsz a jelenlegi szövetségedből. Biztosan ezt akarod?***\')) meghivo_elfogadasa('+sor[2]+');return false" title="***elfogadom***"><img src="img/ikonok/tick.gif" /></a>';},
					function(sor) {return '<a href="" onclick="if (confirm(\'***Biztosan visszautasítod ezt a meghívást?***\')) meghivo_elutasitasa('+sor[2]+');return false" title="***elutasítom***"><img src="img/ikonok/cross.gif" /></a>';}
					],null,null,null,['left','left','left','center','center']);
				}
				if (valasz.meghivo_kerelmeid.length>0) {
					s2+='<h2>***Belépési kérelmeid***</h2>';
					s2+=json2table(valasz.meghivo_kerelmeid,['***hova***','***mikor***','***visszavonás***'],[
					function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
					function(sor) {return sor[4]+' napja';},
					function(sor) {return '<a href="" onclick="return meghivo_kerelem_visszavonasa('+sor[2]+')"><img src="img/ikonok/cross.gif" /></a>';}
					],null,null,null,['left','left','center']);
				}
				if (valasz.alapito_vagy) {
					$('aktiv_szovetseg_neve').innerHTML=minicimer_html+'['+valasz.rovid_nev+'] '+valasz.nev+' <span style="font-size:8pt">(<a href="" onclick="return toggle(\'szovetseg_szerk_div\')">***szerk***</a>)</span>';
					var szerk_div='';
					szerk_div+='<form onsubmit="return szovetseg_szerkesztese()">';
					szerk_div+=json2table([
					['<b>***Név***</b><br />(***kötelező***)','<input type="text" id="szerk_szovetseg_nev" class="bolygonev" value="'+htmlspecialchars(valasz.nev)+'" />'],
					['<b>***Tag*** (***rövid név***)</b><br />(***kötelező***)','<input type="text" id="szerk_szovetseg_rovid_nev" class="bolygonev" value="'+htmlspecialchars(valasz.rovid_nev)+'" />'],
					['<b>***Alapító megnevezése***</b><br />(***kötelező***)','<input type="text" id="szerk_szovetseg_alapnev" class="bolygonev" value="'+htmlspecialchars(valasz.alapito_elnevezese)+'" />'],
					['***Mottó***<br />(***opcionális***)','<input type="text" id="szerk_szovetseg_motto" class="szovegmezo" style="width: 400px" value="'+htmlspecialchars(valasz.motto)+'" />'],
					['***Üdvözlet (külsősöknek)***<br />(***opcionális***)','<textarea id="szerk_szovetseg_udvozlet" class="szovegdoboz" style="width: 400px; height: 100px">'+(valasz.udvozlet)+'</textarea>'],
					['***Szabályzat (tagoknak)***<br />(***opcionális***)','<textarea id="szerk_szovetseg_szabalyzat" class="szovegdoboz" style="width: 400px; height: 300px">'+(valasz.szabalyzat)+'</textarea>'],
					['***Zárt***','<input type="checkbox" id="szerk_szovetseg_zart" value="1"'+(valasz.zart?' checked="checked"':'')+' />']
					],[])+'<br />';
					szerk_div+='<input type="submit" class="gomb" value="***Változások mentése***" />';
					szerk_div+='</form>';
					szerk_div+='<h2>***Minicímer***</h2>'+
					((valasz.minicimer_fajlnev=='')?'<p>***nincs***</p>':('<p><img src="img/minicimerek/'+valasz.minicimer_fajlnev+'?'+valasz.kepfajl_random+'" id="uj_minicimer_ikon" /> <span style="font-size:8pt">(<a href="" onclick="if (confirm(\'***Biztosan törlöd a szövetség minicímerét?***\')) szov_minicimer_torlese();return false">***töröl***</a>)</span></p>'))+
					'<form action="szov_minicimer_feltoltese.php" method="post" enctype="multipart/form-data" target="uj_minicimer_iframe" onsubmit="inline_toggle(\'uj_minicimer_tolto\');return true" >'+
					'<p>***Legfeljebb 32x32-es JPG, PNG vagy GIF fájl***: <input name="kep" type="file" /></p>'+
					'<p><input type="submit" value="***Feltölt!***" /> <img src="img/ikonok/ajax-loader.gif" id="uj_minicimer_tolto" style="display:none" /></p>'+
					'</form>'+
					'<iframe id="uj_minicimer_iframe" name="uj_minicimer_iframe" src="#" style="display:none"></iframe>';
					szerk_div+='<h2>***Címer/zászló*** <span style="font-size:8pt">(***csak akkor fog látszódni, ha legalább egy tag előfizet az alapszintű prémiumra***)</span></h2>'+
					((valasz.cimer_fajlnev=='')?'<p>***nincs***</p>':('<p><img src="img/cimerek/'+valasz.cimer_fajlnev+'?'+valasz.kepfajl_random+'" id="uj_cimer_ikon" /> <span style="font-size:8pt">(<a href="" onclick="if (confirm(\'***Biztosan törlöd a szövetség címerét/zászlaját?***\')) szov_cimer_torlese();return false">***töröl***</a>)</span></p>'))+
					'<form action="szov_cimer_feltoltese.php" method="post" enctype="multipart/form-data" target="uj_cimer_iframe" onsubmit="inline_toggle(\'uj_cimer_tolto\');return true" >'+
					'<p>***Legfeljebb 320x320-as JPG, PNG vagy GIF fájl***: <input name="kep" type="file" /></p>'+
					'<p><input type="submit" value="***Feltölt!***" /> <img src="img/ikonok/ajax-loader.gif" id="uj_cimer_tolto" style="display:none" /></p>'+
					'</form>'+
					'<iframe id="uj_cimer_iframe" name="uj_cimer_iframe" src="#" style="display:none"></iframe>';
				}
				$('aktiv_szovetseg_alapadatai').innerHTML='<div style="float:left;width:410px">'+json2table([
				[valasz.alapito_elnevezese,'<a href="" onclick="return user_katt('+valasz.alapito+')">'+valasz.alapito_neve+'</a>'],
				['***Alapítva***',ezresito(valasz.alapitva)+' ***napja***'],
				['***Mottó***',valasz.motto],
				['***Taglétszám***',valasz.tagletszam+'*** fő***'],
				['***Tisztséged***',valasz.tisztseged],
				['***Üdvözlet (külsősöknek)***',(valasz.udvozlet.length>0)?('<a href="" onclick="return toggle(\'szovetseg_udvozlet\')">***megnézem***</a><div id="szovetseg_udvozlet" style="display:none">'+nl2br(valasz.udvozlet)+'</div>'):'-'],
				['***Szabályzat (tagoknak)***',(valasz.szabalyzat.length>0)?('<a href="" onclick="return toggle(\'szovetseg_szabalyzat\')">***megnézem***</a><div id="szovetseg_szabalyzat" style="display:none">'+nl2br(valasz.szabalyzat)+'</div>'):'-'],
				['***Zárt***/***Nyitott***',valasz.zart?'***zárt***':'***nyitott***']
				],[])
				+'</div><div style="float:right;width:340px;text-align:left">'+((valasz.cimer_fajlnev.length>0)?'<img src="img/cimerek/'+valasz.cimer_fajlnev+'?'+valasz.kepfajl_random+'" />':'')+'</div><div style="clear:both"></div>'
				+(valasz.alapito_vagy?('<div id="szovetseg_szerk_div" style="display:none">'+szerk_div+'</div>'):'')
				+s2;
				//tisztsegek jogok
				var s='';
				$('tiszt_jog_szerk_gomb').innerHTML='';
				if (valasz.alapito_vagy) {
					$('tiszt_jog_szerk_gomb').innerHTML=' (<a href="" onclick="return toggle(\'tiszt_jog_szerk_div\')">***szerk***</a>)';
					s=json2table([
					['<input type="text" id="uj_tisztseg_nev" class="tisztnev" /><br /><a href="" onclick="return uj_tisztseg_felvetele()" style="font-weight:bold">***új tisztség***</a>',
					'<input type="checkbox" id="uj_tisztseg_jog_2" />',
					'<input type="checkbox" id="uj_tisztseg_jog_3" />',
					'<input type="checkbox" id="uj_tisztseg_jog_4" />',
					'<input type="checkbox" id="uj_tisztseg_jog_5" />',
					'<input type="checkbox" id="uj_tisztseg_jog_7" />',
					'<input type="checkbox" id="uj_tisztseg_jog_6" />',
					'<input type="checkbox" id="uj_tisztseg_jog_8" />',
					'<input type="checkbox" id="uj_tisztseg_jog_9" />',
					'<input type="checkbox" id="uj_tisztseg_jog_1" />',
					'<input type="checkbox" id="uj_tisztseg_jog_10" />',
					'<input type="checkbox" id="uj_tisztseg_jog_11" />',
					'<input type="checkbox" id="uj_tisztseg_jog_12" />'
					]
					],[],null,['100px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px'],null,null,['left','center','center','center','center','center','center','center','center','center','center','center']);
					s+='<div id="tiszt_jog_szerk_div" style="display:none">';
					s+='<br /><h3>***Tisztségek szerkesztése***</h3>';
					s+='<form onsubmit="return tisztsegek_szerkesztese()" id="tiszt_jog_szerk_form">';
					s+=json2table(valasz.tisztsegek,['','***meghív***','***kirúg***','***kinev.***','***közös***','***diplo.***','***vendég***','***újtéma***','***mod.***','***belső fórum***','***radar***','***nagy radar***','***kém***'],[
					function(sor) {return '<input type="text" class="tisztnev" id="tiszt_jog_nev_'+sor[0]+'" value="'+htmlspecialchars(sor[1])+'" />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_2"'+(sor[3]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_3"'+(sor[4]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_4"'+(sor[5]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_5"'+(sor[6]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_7"'+(sor[8]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_6"'+(sor[7]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_8"'+(sor[9]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_9"'+(sor[10]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_1"'+(sor[2]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_10"'+(sor[11]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_11"'+(sor[12]?' checked="checked"':'')+' />';},
					function(sor) {return '<input type="checkbox" id="tiszt_jog_'+sor[0]+'_12"'+(sor[13]?' checked="checked"':'')+' />';}
					],['100px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','center','center','center','center','center','center','center','center','center','center','center','center']);
					s+='<input type="submit" class="gomb" value="***Változások mentése***" />';
					s+='</form>';
					s+='</div>';
				}
				$('aktiv_szovetseg_tiszt_jogai').innerHTML=json2table(valasz.tisztsegek,['','***meghív***','***kirúg***','***kinev.***','***közös***','***diplo.***','***vendég***','***újtéma***','***mod.***','***belső fórum***','***radar***','***nagy radar***','***kém***'],[
				function(sor) {return '<b>'+sor[1]+'</b>'+(valasz.alapito_vagy?' <a href="" onclick="if (confirm(\'***Biztosan törölni akarod ezt a tisztséget?***\')) tisztseg_torlese('+sor[0]+');return false"><img src="img/ikonok/cross.gif" /></a>':'');},
				function(sor) {return sor[3]?'+':'';},
				function(sor) {return sor[4]?'+':'';},
				function(sor) {return sor[5]?'+':'';},
				function(sor) {return sor[6]?'+':'';},
				function(sor) {return sor[8]?'+':'';},
				function(sor) {return sor[7]?'+':'';},
				function(sor) {return sor[9]?'+':'';},
				function(sor) {return sor[10]?'+':'';},
				function(sor) {return sor[2]?'+':'';},
				function(sor) {return sor[11]?'+':'';},
				function(sor) {return sor[12]?'+':'';},
				function(sor) {return sor[13]?'+':'';}
				],['100px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px','48px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','center','center','center','center','center','center','center','center','center','center','center','center'])+s;
				//tagok
				$('aktiv_szovetseg_tagjai').innerHTML=json2table(valasz.tagok,['','***név***','***tisztség***','***belépett***<br />(***napja***)','***pontszám***','***bolygók száma***','***utolsó aktivitás***'],[
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')"><img src="img/'+(sor[8].length>0?('user_avatarok/'+sor[8]):'ikonok/fantom_avatar.gif')+'" /></a>';},
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')"><img src="img/flag_'+(sor[9]=='hu'?'hu':'gb')+'.gif" alt="'+(sor[9]=='hu'?'***magyar***':'***angol***')+'" style="vertical-align: -1px" /> '+sor[1]+'</a>';},
					function(sor) {
						if (sor[3]==-1) return valasz.alapito_elnevezese;
						if (sor[3]==0) return '***Tag***';
						return sor[4];
					},
					function(sor) {return sor[2];},
					function(sor) {if (sor[6]=='?') return '?';return ezresito(sor[6]);},
					function(sor) {return sor[7];},
					function(sor) {return sor[5]<60?(sor[5]+' ***perce***'):(sor[5]<1440?(Math.round(sor[5]/60)+' ***órája***'):(Math.round(sor[5]/1440)+' ***napja***'));}
				],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','left','right','right','right','right','right']);
				s='';
				if (valasz.jogaid[3]) {//kinevezes
					s+='<br /><h2>***Tag kinevezése***</h2><form onsubmit="return tag_kinevezese()">';
					s+='<input type="text" class="usernev" id="kinevez_kit" /> ';
					s+='<select class="tisztseg" id="kinevez_hova">';
					s+='<option value="0">***Tag***</option>';
					for(var i=0;i<valasz.tisztsegek.length;i++) s+='<option value="'+valasz.tisztsegek[i][0]+'">'+htmlspecialchars(valasz.tisztsegek[i][1])+'</option>';
					s+='</select> ';
					s+='<input type="submit" class="gomb" value="***Kinevezem***" />';
					s+='</form>';
				}
				if (valasz.jogaid[2]) {//kirugas
					s+='<br /><h2>***Tag kirúgása***</h2><form onsubmit="if (confirm(\'***Biztosan ki akarod rúgni ezt a tagot?***\')) tag_kirugasa();return false">';
					s+='<input type="text" class="usernev" id="kirug_kit" /> ';
					s+='<input type="submit" class="gomb" value="***Kirúgom***" />';
					s+='</form>';
				}
				if (valasz.jogaid[1]) {//felvetel
					if (valasz.meghivok.length>0) {
						s+='<br /><h2>***Függő meghívók***</h2>';
						s+=json2table(valasz.meghivok,['***ki***','***kit***','***mikor***','***visszavonás***'],[
						function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
						function(sor) {return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';},
						function(sor) {return (sor[4]<0?0:sor[4])+' ***napja***';},
						function(sor) {return '<a href="" onclick="return meghivo_visszavonasa('+sor[2]+')"><img src="img/ikonok/cross.gif" /></a>';}
						],null,null,null,['left','left','right','center']);
					}
					if (valasz.meghivo_kerelmek.length>0) {
						s+='<br /><h2>***Belépési kérelmek***</h2>';
						s+=json2table(valasz.meghivo_kerelmek,['***ki***','***mikor***','***elfogadás***','***elutasítás***'],[
						function(sor) {return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';},
						function(sor) {return (sor[4]<0?0:sor[4])+' ***napja***';},
						function(sor) {return '<a href="" onclick="if (confirm(\'***Biztosan elfogadod ezt a kérelmet?***\')) meghivo_kerelem_elfogadasa('+sor[2]+');return false" title="***elfogadom***"><img src="img/ikonok/tick.gif" /></a>';},
						function(sor) {return '<a href="" onclick="if (confirm(\'***Biztosan elutasítod ezt a kérelmet?***\')) meghivo_kerelem_elutasitasa('+sor[2]+');return false" title="***elutasítom***"><img src="img/ikonok/cross.gif" /></a>';}
						],null,null,null,['left','right','center','center']);
					}
					s+='<br /><h2>***Új tag meghívása***</h2><form onsubmit="return tag_meghivasa()">';
					s+='<input type="text" class="usernev" id="meghiv_kit" /><br />';
					s+='***megjegyzés***:<br /><textarea class="szovegdoboz" style="width: 300px; height: 100px" id="meghiv_megjegyzes"></textarea><br />';
					s+='<input type="checkbox" checked="checked" id="meghiv_csatol" /> ***szabályzat csatolása***<br />';
					s+='<input type="submit" class="gomb" value="***Meghívom***" />';
					s+='</form>';
					if (valasz.maganyos_farkasok.length>0) {
						s+='<br /><h2>***Magányos farkasok a közelben***</h2>';
						s+=json2table(valasz.maganyos_farkasok,['***távolság***','***ki***'],[
						function(sor) {return ezresito(sor[0]);},
						function(sor) {return '<a href="" onclick="return user_katt('+sor[1]+')"><img src="img/flag_'+(sor[3]=='hu'?'hu':'gb')+'.gif" alt="'+(sor[3]=='hu'?'***magyar***':'***angol***')+'" style="vertical-align: -1px" /> '+sor[2]+'</a>';}
						],null,null,null,['right','left']);
					}
				}
				s+='<br /><h2>***Kilépés***</h2><form onsubmit="if (confirm(\'***Biztosan ki akarsz lépni a szövetségből?***\')) kilepes_szovetsegbol();return false">';
				if (valasz.alapito_vagy) {
					if (valasz.tagletszam>1) s+='***Ki lépjen a helyedre alapítóként?*** <input type="text" class="usernev" id="uj_alapito_neve" /><br />';
					else s+='(***Ha kilépsz, feloszlik a szövetség.***)<br />';
				}
				s+='<input type="submit" class="gomb" value="***Kilépek***" />';
				s+='</form>';
				$('aktiv_szovetseg_tagjai_egyebek').innerHTML=s;
				if (valasz.jogaid[3]) new actb($('kinevez_kit'),'ajax_autocomplete_tagok_kiveve_alapito',0);
				if (valasz.jogaid[2]) new actb($('kirug_kit'),'ajax_autocomplete_tagok_kiveve_alapito',0);
				if (valasz.jogaid[1]) new actb($('meghiv_kit'),'ajax_autocomplete_nemtagok',0);
				if (valasz.alapito_vagy) if (valasz.tagletszam>1) new actb($('uj_alapito_neve'),'ajax_autocomplete_tagok_kiveve_alapito',0);
				//kozos flottak
				if (valasz.jogaid[4]) {//picit elter az attekintes.php-tol, pl mindig latszik a tulaj, nincs csillag, stb...
				if (valasz.kozos_flottak.length>0) $('aktiv_szovetseg_kozos_flottai').innerHTML=json2table(valasz.kozos_flottak,['','***név***','***tulaj***','***pozíció***','***tevékenység***','***egyenérték***','***morál***'],[
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')"><img src="img/ikonok/flotta_ikon_'+flotta_diplok[sor[20]]+'.gif" /></a>';},
				function(sor) {return '<a href="" onclick="return flotta_katt('+sor[0]+')" style="font-weight: bold">'+sor[1]+'</a>';},
				function(sor) {return '<a href="" onclick="return user_katt('+sor[22]+')" style="font-weight: bold">'+sor[23]+'</a>';},
				function(sor) {return '<a href="" onclick="jump_to_xy('+sor[2]+','+sor[3]+');return oldal_nyit(\'terkep\');" title="***térkép***">'+ykoordinata(sor[3])+', '+xkoordinata(sor[2])+'</a>';},
				function(sor) {
					var flotta_statusz='';
					switch(sor[6]) {
						case 1:flotta_statusz='*** felett állomásozik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felett állomásozik***';break;
						case 2:flotta_statusz='***Várakozik***';break;
						case 3:case 4:flotta_statusz='*** között járőrözik prefix***'+ykoordinata(sor[13])+',&nbsp;'+xkoordinata(sor[12])+' ***és*** '+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** között járőrözik***';break;
						case 5:flotta_statusz='*** felé tart prefix***'+ykoordinata(sor[15])+',&nbsp;'+xkoordinata(sor[14])+'*** felé tart***';break;
						case 6:flotta_statusz='*** felé tart prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart***';break;
						case 7:flotta_statusz='*** felé tart támadólag prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart támadólag***';break;
						case 8:flotta_statusz='*** ellen támad prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen támad***';break;
						case 9:flotta_statusz='*** felé tart portyázni prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** felé tart portyázni***';break;
						case 10:flotta_statusz='*** ellen portyázik prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** ellen portyázik***';break;
						case 11:flotta_statusz='***Visszavonul*** *** bázisra prefix***<a href="" style="padding-left: 20px" class="menu_bolygo_osztaly_'+sor[9]+'" onclick="return bolygo_katt('+sor[7]+',1);">'+sor[8]+'</a>*** bázisra***';break;
						case 12:flotta_statusz='*** felé tart prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart***';break;
						case 13:flotta_statusz='*** felé tart támadólag prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** felé tart támadólag***';break;
						case 14:flotta_statusz='*** ellen támad prefix***<a href="" onclick="return flotta_katt('+sor[10]+')">'+sor[11]+'</a>*** ellen támad***';break;
					}
					if (sor[17]>=0) flotta_statusz+=' ('+sor[17]+'m)';
					return flotta_statusz;
				},
				function(sor) {return szazadresz(sor[16]);},
				function(sor) {return Math.floor(sor[19]/10)+'***,***'+(sor[19]%10)+'%';}
				],null,null,null,['center','left','left','left','left','left','right']);
				else $('aktiv_szovetseg_kozos_flottai').innerHTML='***Nincsenek közös flották a szövetségben.***';
				} else {
					$('aktiv_szovetseg_kozos_flottai').innerHTML='***Nincs közös flotta jogosultságod.***';
				}
				//diplomacia
				//statuszok
				if (valasz.statuszok.length>0) $('aktiv_szovetseg_diplo_statuszok').innerHTML='<h2>***Státuszok***</h2>'+json2table(valasz.statuszok,['***státusz***','***kivel***','***mióta***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***','***érvényes***'],[
				function(sor) {
					var s='';
					if (sor[3]==0) s+='***Semleges***';
					if (sor[3]==1) s+='***Háború***';
					if (sor[3]==2) s+='***Testvérszövi***';
					if (sor[3]==3) s+='***MNT***';
					return '<b>'+s+'</b>';
				},
				function(sor) {
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {return sor[4];},
				function(sor) {
					if (sor[6]) return valasz.nev;
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
				function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
				function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
				function(sor) {
					return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
				},
				function(sor) {return sor[8]+' ***óra***';},
				function(sor) {
					if (sor[9]>0) return sor[10];
					return '';
				}
				]);else $('aktiv_szovetseg_diplo_statuszok').innerHTML='';
				//leendo statuszok
				if (valasz.leendo_statuszok.length>0) $('aktiv_szovetseg_diplo_leendo_statuszok').innerHTML='<h2>***Leendő státuszok***</h2>'+json2table(valasz.leendo_statuszok,['***státusz***','***kivel***','***mikortól***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***'],[
				function(sor) {
					var s='';
					if (sor[3]==0) s+='***Semleges***';
					if (sor[3]==1) s+='***Háború***';
					if (sor[3]==2) s+='***Testvérszövi***';
					if (sor[3]==3) s+='***MNT***';
					return '<b>'+s+'</b>';
				},
				function(sor) {
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {return sor[4];},
				function(sor) {
					if (sor[6]) return valasz.nev;
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
				function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
				function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
				function(sor) {
					return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
				},
				function(sor) {return sor[8]+' ***óra***';}
				]);else $('aktiv_szovetseg_diplo_leendo_statuszok').innerHTML='';
				//uj statusz
				if (valasz.diplomata_jogod) {
					$('aktiv_szovetseg_diplo_uj_statusz').innerHTML=
					'<h2>***Státuszváltás***</h2><form onsubmit="return diplo_uj_statusz()">'
					+'***kivel***:<br /><input type="text" class="usernev" id="statusz_kinek" /><br />'
					+'***miről***:<br /><select id="statusz_mirol" class="diplo_statusz">'
					+'<option value="1">***Hadüzenet***</option>'
					+'<option value="-2">***Testvérszövetség felbontása***</option>'
					+'<option value="-3">***Megnemtámadási egyezmény felbontása***</option>'
					+'</select><br />'
					+'***megjegyzés*** (***opcionális***):<br /><textarea class="szovegdoboz" style="width: 300px; height: 100px" id="statusz_szoveg"></textarea><br />'
					+'<input type="submit" class="gomb" value="***Elküldöm***" />'
					+'</form>';
					new actb($('statusz_kinek'),'ajax_autocomplete_nemtagok_es_masszovetsegek',0);
				} else $('aktiv_szovetseg_diplo_uj_statusz').innerHTML='';
				//ajanlatok
				if (valasz.ajanlatok.length>0) {
					if (valasz.diplomata_jogod) {
						$('aktiv_szovetseg_diplo_ajanlatok').innerHTML='<h2>***Ajánlatok***</h2>'+json2table(valasz.ajanlatok,['***mikor***','***kitől***','***diplomata***','***kinek***','***miről***','***megjegyzés***','***felbontási idő***','***nyilvános***','***akció***'],[
							function(sor) {return sor[5];},
							function(sor) {
								if (sor[8]==1) return valasz.nev;
								if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
								return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
							},
							function(sor) {if (sor[10]>0) return '<a href="" onclick="return user_katt('+sor[10]+')">'+sor[11]+'</a>';return '';},
							function(sor) {
								if (sor[8]==0) return valasz.nev;
								if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
								return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
							},
							function(sor) {
								var s='';
								if (sor[4]==0) s+='***Tűzszünet***';
								if (sor[4]==2) s+='***Testvérszövi***';
								if (sor[4]==3) s+='***MNT***';
								return '<b>'+s+'</b>';
							},
							function(sor) {
								return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[6]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
							},
							function(sor) {return sor[9]+' ***óra***';},
							function(sor) {if (sor[12]>0) return '***nyilvános***';else return '***titkos***';},
							function(sor) {
								if (sor[8]) {
									return '<a href="" onclick="if (confirm(\'***Biztosan visszavonod?***\')) return diplo_ajanlat_visszavon('+sor[0]+');return false" title="***Visszavonás***"><img src="img/ikonok/cross.gif" /></a>';
								} else {
									return '<a href="" onclick="if (confirm(\'***Biztosan elfogadod?***\')) return diplo_ajanlat_elfogad('+sor[0]+');return false" title="***Elfogadás***"><img src="img/ikonok/tick.gif" /></a> <a href="" onclick="if (confirm(\'***Biztosan elutasítod?***\')) return diplo_ajanlat_elutasit('+sor[0]+');return false" title="***Elutasítás***"><img src="img/ikonok/cross.gif" /></a>';
								}
							}
						]);
					} else {
						$('aktiv_szovetseg_diplo_ajanlatok').innerHTML='<h2>***Ajánlatok***</h2>'+json2table(valasz.ajanlatok,['***mikor***','***kitől***','***diplomata***','***kinek***','***miről***','***megjegyzés***','***felbontási idő***','***nyilvános***'],[
							function(sor) {return sor[5];},
							function(sor) {
								if (sor[8]==1) return valasz.nev;
								if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
								return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
							},
							function(sor) {if (sor[10]>0) return '<a href="" onclick="return user_katt('+sor[10]+')">'+sor[11]+'</a>';return '';},
							function(sor) {
								if (sor[8]==0) return valasz.nev;
								if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
								return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
							},
							function(sor) {
								var s='';
								if (sor[4]==0) s+='***Tűzszünet***';
								if (sor[4]==2) s+='***Testvérszövi***';
								if (sor[4]==3) s+='***MNT***';
								return '<b>'+s+'</b>';
							},function(sor) {
								return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[6]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
							},
							function(sor) {return sor[9]+' ***óra***';},
							function(sor) {if (sor[12]>0) return '***nyilvános***';else return '***titkos***';}
						]);
					}
				} else $('aktiv_szovetseg_diplo_ajanlatok').innerHTML='';
				//uj ajanlat
				if (valasz.diplomata_jogod) {
					$('aktiv_szovetseg_diplo_uj_ajanlat').innerHTML=
					'<h2>***Új ajánlat***</h2><form onsubmit="return diplo_uj_ajanlat()">'
					+'***kinek***:<br /><input type="text" class="usernev" id="ajanlat_kinek" /><br />'
					+'***miről***:<br /><select id="ajanlat_mirol" class="diplo_statusz">'
					+'<option value="0">***Tűzszünet***</option>'
					+'<option value="2">***Testvérszövetség***</option>'
					+'<option value="3">***Megnemtámadási egyezmény***</option>'
					+'</select><br />'
					+'***felbontási idő***:<br /><input type="text" class="usernev" id="ajanlat_lejarat" /> ***óra*** (***0 és 48 között lehet***)<br />'
					+'***megjegyzés*** (***opcionális***):<br /><textarea class="szovegdoboz" style="width: 300px; height: 100px" id="ajanlat_szoveg"></textarea><br />'
					+'<input type="submit" class="gomb" value="***Elküldöm***" />'
					+'</form>';
					new actb($('ajanlat_kinek'),'ajax_autocomplete_nemtagok_es_masszovetsegek',0);
				} else $('aktiv_szovetseg_diplo_uj_ajanlat').innerHTML='';
				//vendegek
				if (valasz.vendegek.length>0) {
					$('aktiv_szovetseg_diplo_vendegek').innerHTML='<h2>***Vendégek***</h2>'+json2table(valasz.vendegek,['***név***','***mikortól***','***ki hívta***',''],[
						function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
						function(sor) {return sor[2];},
						function(sor) {if (sor[3]>0) return '<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>';return '';},
						function(sor) {if (valasz.vendeg_jogod) return '<a href="#" onclick="if (confirm(\'***Biztos?***\')) diplo_vendeg_kirug('+sor[0]+');return false">***kirúg***</a>';return '';}
					],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','left','left','left']);
				} else $('aktiv_szovetseg_diplo_vendegek').innerHTML='';
				//uj vendeg
				if (valasz.vendeg_jogod) {
					$('aktiv_szovetseg_diplo_uj_vendeg').innerHTML=
					'<h2>***Új vendég***</h2><form onsubmit="return diplo_uj_vendeg()">'
					+'<p><input type="text" class="usernev" id="vendeg_kit" /></p>'
					+'<p><input type="submit" class="gomb" value="***Meghívom***" /></p>'
					+'</form>';
					new actb($('vendeg_kit'),'ajax_autocomplete_tanacsnokok',0);
				} else $('aktiv_szovetseg_diplo_uj_vendeg').innerHTML='';
				//
				$('aktiv_szovetseg_reszletei').style.display='block';
			} else {//kulsoskent
				sajat_szovetseg=0;
				var s='';
				s+='<div style="float:left;width:410px">'+json2table([
				[valasz.alapito_elnevezese,'<a href="" onclick="return user_katt('+valasz.alapito+')">'+valasz.alapito_neve+'</a>'],
				['***Alapítva***',ezresito(valasz.alapitva)+' ***napja***'],
				['***Mottó***',valasz.motto],
				['***Taglétszám***',valasz.tagletszam+'*** fő***'],
				['***Üdvözlet***',(valasz.udvozlet.length>0)?('<a href="" onclick="return toggle(\'szovetseg_udvozlet\')">***megnézem***</a><div id="szovetseg_udvozlet" style="display:none">'+nl2br(valasz.udvozlet)+'</div>'):'-'],
				['***Belépési kérelem***',valasz.zart?'***Ez a szövetség zárt, nem fogadnak új tagokat.***':(valasz.meghivo_kerelmed_ide?'***Már küldtél kérelmet.***':('<a href="#" onclick="return meghivo_kerelem('+aktiv_szovetseg+')" title="***belépési kérelem***"><img src="img/ikonok/group.gif" /> ***belépési kérelem küldése***</a>'))]
				],[])
				+'</div><div style="float:right;width:340px;text-align:left">'+((valasz.cimer_fajlnev.length>0)?'<img src="img/cimerek/'+valasz.cimer_fajlnev+'?'+valasz.kepfajl_random+'" />':'')+'</div><div style="clear:both"></div>';
				if (valasz.meghivoid.length>0) {
					s+='<h1>***Meghívások számodra***</h1>';
					s+=json2table(valasz.meghivoid,['***ki***','***hova***','***mikor***','***elfogadás***','***elutasítás***'],[
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
					function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
					function(sor) {return sor[4]+' ***napja***';},
					function(sor) {return '<a href="" onclick="return meghivo_elfogadasa('+sor[2]+')" title="***elfogadom***"><img src="img/ikonok/tick.gif" /></a>';},
					function(sor) {return '<a href="" onclick="if (confirm(\'***Biztosan visszautasítod ezt a meghívást?***\')) meghivo_elutasitasa('+sor[2]+');return false" title="***elutasítom***"><img src="img/ikonok/cross.gif" /></a>';}
					],null,null,null,['left','left','left','center','center']);
				}
				if (valasz.meghivo_kerelmeid.length>0) {
					s+='<h2>***Belépési kérelmeid***</h2>';
					s+=json2table(valasz.meghivo_kerelmeid,['***hova***','***mikor***','***visszavonás***'],[
					function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
					function(sor) {return sor[4]+' napja';},
					function(sor) {return '<a href="" onclick="return meghivo_kerelem_visszavonasa('+sor[2]+')"><img src="img/ikonok/cross.gif" /></a>';}
					],null,null,null,['left','left','center']);
				}
				$('aktiv_szovetseg_alapadatai').innerHTML=s;
				//tagok
				$('aktiv_szovetseg_tagjai').innerHTML=json2table(valasz.tagok,['','***név***','***tisztség*** (***diplomata***)','***belépett***<br />(***napja***)','***pontszám***','***bolygók száma***','***utolsó aktivitás***'],[
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')"><img src="img/'+(sor[8].length>0?('user_avatarok/'+sor[8]):'ikonok/fantom_avatar.gif')+'" /></a>';},
					function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')"><img src="img/flag_'+(sor[9]=='hu'?'hu':'gb')+'.gif" alt="'+(sor[9]=='hu'?'***magyar***':'***angol***')+'" style="vertical-align: -1px" /> '+sor[1]+'</a>';},
					function(sor) {
						if (sor[3]==-1) return valasz.alapito_elnevezese+' (D)';
						if (sor[3]==0) return '***Tag***'+(sor[10]?' (D)':'');
						return sor[4]+(sor[10]?' (D)':'');
					},
					function(sor) {return sor[2];},
					function(sor) {if (sor[6]=='?') return '?';return ezresito(sor[6]);},
					function(sor) {return sor[7];},
					function(sor) {if (sor[5]=='?') return '?';return sor[5]<60?(sor[5]+' ***perce***'):(sor[5]<1440?(Math.round(sor[5]/60)+' ***órája***'):(Math.round(sor[5]/1440)+' ***napja***'));}
				],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','left','right','right','right','right','right']);
				$('aktiv_szovetseg_tagjai_egyebek').innerHTML='';
				//kozos flottak
				$('aktiv_szovetseg_kozos_flottai').innerHTML='***Ez nem a te szövetséged.***';
				//tisztsegek
				$('aktiv_szovetseg_tiszt_jogai').innerHTML='***Ez nem a te szövetséged.***';
				$('tiszt_jog_szerk_gomb').innerHTML='';
				//diplo
				//
				//
				//statuszok
				if (valasz.statuszok.length>0) $('aktiv_szovetseg_diplo_statuszok').innerHTML='<h2>***Státuszok***</h2>'+json2table(valasz.statuszok,['***státusz***','***kivel***','***mióta***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***','***érvényes***'],[
				function(sor) {
					var s='';
					if (sor[3]==0) s+='***Semleges***';
					if (sor[3]==1) s+='***Háború***';
					if (sor[3]==2) s+='***Testvérszövi***';
					if (sor[3]==3) s+='***MNT***';
					return '<b>'+s+'</b>';
				},
				function(sor) {
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {return sor[4];},
				function(sor) {
					if (sor[6]) return valasz.nev;
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
				function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
				function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
				function(sor) {
					return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
				},
				function(sor) {return sor[8]+' ***óra***';},
				function(sor) {
					if (sor[9]>0) return sor[10];
					return '';
				}
				]);else $('aktiv_szovetseg_diplo_statuszok').innerHTML='';
				//leendo statuszok
				if (valasz.leendo_statuszok.length>0) $('aktiv_szovetseg_diplo_leendo_statuszok').innerHTML='<h2>***Leendő státuszok***</h2>'+json2table(valasz.leendo_statuszok,['***státusz***','***kivel***','***mikortól***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***'],[
				function(sor) {
					var s='';
					if (sor[3]==0) s+='***Semleges***';
					if (sor[3]==1) s+='***Háború***';
					if (sor[3]==2) s+='***Testvérszövi***';
					if (sor[3]==3) s+='***MNT***';
					return '<b>'+s+'</b>';
				},
				function(sor) {
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {return sor[4];},
				function(sor) {
					if (sor[6]) return valasz.nev;
					if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
				},
				function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
				function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
				function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
				function(sor) {
					return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
				},
				function(sor) {return sor[8]+' ***óra***';}
				]);else $('aktiv_szovetseg_diplo_leendo_statuszok').innerHTML='';
				//uj statusz
				$('aktiv_szovetseg_diplo_uj_statusz').innerHTML='';
				//ajanlatok
				if (valasz.ajanlatok.length>0) {
					$('aktiv_szovetseg_diplo_ajanlatok').innerHTML='<h2>***Ajánlatok***</h2>'+json2table(valasz.ajanlatok,['***mikor***','***kitől***','***diplomata***','***kinek***','***miről***','***megjegyzés***','***felbontási idő***','***nyilvános***'],[
						function(sor) {return sor[5];},
						function(sor) {
							if (sor[8]==1) return valasz.nev;
							if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
							return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
						},
						function(sor) {if (sor[10]>0) return '<a href="" onclick="return user_katt('+sor[10]+')">'+sor[11]+'</a>';return '';},
						function(sor) {
							if (sor[8]==0) return valasz.nev;
							if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
							return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
						},
						function(sor) {
							var s='';
							if (sor[4]==0) s+='***Tűzszünet***';
							if (sor[4]==2) s+='***Testvérszövi***';
							if (sor[4]==3) s+='***MNT***';
							return '<b>'+s+'</b>';
						},function(sor) {
							return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[6]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
						},
						function(sor) {return sor[9]+' ***óra***';},
						function(sor) {if (sor[12]>0) return '***nyilvános***';else return '***titkos***';}
					]);
				} else $('aktiv_szovetseg_diplo_ajanlatok').innerHTML='';
				//uj ajanlat
				$('aktiv_szovetseg_diplo_uj_ajanlat').innerHTML='';
				//vendegek
				if (valasz.vendegek) {
					if (valasz.vendegek.length>0) {
						$('aktiv_szovetseg_diplo_vendegek').innerHTML='<h2>***Vendégek***</h2>'+json2table(valasz.vendegek,['***név***','***mikortól***','***ki hívta***',''],[
							function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
							function(sor) {return sor[2];},
							function(sor) {if (sor[3]>0) return '<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>';return '';},
							function(sor) {if (valasz.diplomata_jogod) return '<a href="#" onclick="if (confirm(\'***Biztos?***\')) diplo_vendeg_kirug('+sor[0]+');return false">***kirúg***</a>';return '';}
						],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','left','left','left']);
					} else $('aktiv_szovetseg_diplo_vendegek').innerHTML='';
				} else $('aktiv_szovetseg_diplo_vendegek').innerHTML='';
				//uj vendeg
				$('aktiv_szovetseg_diplo_uj_vendeg').innerHTML='';
				//
				$('aktiv_szovetseg_reszletei').style.display='block';
			}
		} else {
			aktiv_szovetseg=0;sajat_szovetseg=0;
			//
			$('szov_forum_tema_lista').innerHTML='';
			$('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='none';
			$('olvasatlan_kommentek_szama').innerHTML='';
			//
			$('aktiv_szovetseg_reszletei').style.display='none';
			$('aktiv_szovetseg_neve').style.display='none';
			$('szovetsegek_listaja').style.display='block';
			var s='';
			s+='<h1><a href="" onclick="return toggle(\'uj_szov_alapitas_div\')">***Új szövetség alapítása***</a></h1>';
			s+='<div id="uj_szov_alapitas_div" style="display:none"><form onsubmit="return szovetseg_alapitasa()">';
			s+=json2table([
			['<b>***Név***</b><br />(***kötelező***)','<input type="text" id="uj_szovetseg_nev" class="bolygonev" />'],
			['<b>***Tag*** (***rövid név***)</b><br />(***kötelező***)','<input type="text" id="uj_szovetseg_rovid_nev" class="bolygonev" />'],
			['<b>***Alapító megnevezése***</b><br />(***kötelező***)','<input type="text" id="uj_szovetseg_alapnev" class="bolygonev" value="***Alapító***" />'],
			['***Mottó***<br />(***opcionális***)','<input type="text" id="uj_szovetseg_motto" class="szovegmezo" style="width: 400px" />'],
			['***Üdvözlet (külsősöknek)***<br />(***opcionális***)','<textarea id="uj_szovetseg_udvozlet" class="szovegdoboz" style="width: 400px; height: 100px"></textarea>'],
			['***Szabályzat (tagoknak)***<br />(***opcionális***)','<textarea id="uj_szovetseg_szabalyzat" class="szovegdoboz" style="width: 400px; height: 300px"></textarea>'],
			['***Zárt***','<input type="checkbox" id="uj_szovetseg_zart" value="1" />']
			],[])+'<br />';
			s+='<input type="submit" class="gomb" value="***Megalapítom***" />';
			s+='</form></div>';
			if (valasz.meghivoid.length>0) {
				s+='<h1>***Meghívások számodra*** <a href="" onclick="return jump_to_help(0,13)"><img src="img/ikonok/help_ikon.gif" /></a></h1>';
				s+=json2table(valasz.meghivoid,['***ki***','***hova***','***mikor***','***elfogadás***','***elutasítás***'],[
				function(sor) {return '<a href="" onclick="return user_katt('+sor[0]+')">'+sor[1]+'</a>';},
				function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
				function(sor) {return sor[4]+' ***napja***';},
				function(sor) {return '<a href="" onclick="return meghivo_elfogadasa('+sor[2]+')" title="***elfogadom***"><img src="img/ikonok/tick.gif" /></a>';},
				function(sor) {return '<a href="" onclick="if (confirm(\'***Biztosan visszautasítod ezt a meghívást?***\')) meghivo_elutasitasa('+sor[2]+');return false" title="***elutasítom***"><img src="img/ikonok/cross.gif" /></a>';}
				],null,null,null,['left','left','left','center','center']);
			}
			if (valasz.meghivo_kerelmeid.length>0) {
				s+='<h2>***Belépési kérelmeid***</h2>';
				s+=json2table(valasz.meghivo_kerelmeid,['***hova***','***mikor***','***visszavonás***'],[
				function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';},
				function(sor) {return sor[4]+' napja';},
				function(sor) {return '<a href="" onclick="return meghivo_kerelem_visszavonasa('+sor[2]+')"><img src="img/ikonok/cross.gif" /></a>';}
				],null,null,null,['left','left','center']);
			}
			//diplomacia
			s+='<br /><h1>***Diplomácia*** <a href="" onclick="return jump_to_help(0,17)"><img src="img/ikonok/help_ikon.gif" /></a></h1>';
			//statuszok
			if (valasz.statuszok.length>0) s+='<h2>***Státuszok***</h2>'+json2table(valasz.statuszok,['***státusz***','***kivel***','***mióta***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***','***érvényes***'],[
			function(sor) {
				var s='';
				if (sor[3]==0) s+='***Semleges***';
				if (sor[3]==1) s+='***Háború***';
				if (sor[3]==2) s+='***Testvérszövi***';
				if (sor[3]==3) s+='***MNT***';
				return '<b>'+s+'</b>';
			},
			function(sor) {
				if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
				return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
			},
			function(sor) {return sor[4];},
			function(sor) {
				if (sor[6]) return valasz.nev;
				if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
				return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
			},
			function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
			function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
			function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
			function(sor) {
				return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
			},
			function(sor) {return sor[8]+' ***óra***';},
			function(sor) {
				if (sor[9]>0) return sor[10];
				return '';
			}
			]);
			//leendo statuszok
			if (valasz.leendo_statuszok.length>0) s+='<h2>***Leendő státuszok***</h2>'+json2table(valasz.leendo_statuszok,['***státusz***','***kivel***','***mikortól***','***kezdeményező***','***kezdeményező diplomata***','***elfogadó diplomata***','***nyilvános***','***megjegyzés***','***felbontási idő***'],[
			function(sor) {
				var s='';
				if (sor[3]==0) s+='***Semleges***';
				if (sor[3]==1) s+='***Háború***';
				if (sor[3]==2) s+='***Testvérszövi***';
				if (sor[3]==3) s+='***MNT***';
				return '<b>'+s+'</b>';
			},
			function(sor) {
				if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
				return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
			},
			function(sor) {return sor[4];},
			function(sor) {
				if (sor[6]) return valasz.nev;
				if (sor[0]>0) return '<a href="" onclick="return szovetseg_katt('+sor[1]+')">'+sor[2]+'</a>';
				return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a>';
			},
			function(sor) {if (sor[11]>0) return '<a href="" onclick="return user_katt('+sor[11]+')">'+sor[12]+'</a>';return '';},
			function(sor) {if (sor[13]>0) return '<a href="" onclick="return user_katt('+sor[13]+')">'+sor[14]+'</a>';return '';},
			function(sor) {if (sor[15]>0) return '***nyilvános***';else return '***titkos***';},
			function(sor) {
				return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[5]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
			},
			function(sor) {return sor[8]+' ***óra***';}
			]);
			//uj statuszok
			s+='<h2>***Státuszváltás***</h2><form onsubmit="return diplo_uj_statusz()">'
			+'***kivel***:<br /><input type="text" class="usernev" id="statusz_kinek" /><br />'
			+'***miről***:<br /><select id="statusz_mirol" class="diplo_statusz">'
			+'<option value="1">***Hadüzenet***</option>'
			+'<option value="-2">***Testvérszövetség felbontása***</option>'
			+'<option value="-3">***Megnemtámadási egyezmény felbontása***</option>'
			+'</select><br />'
			+'***megjegyzés*** (***opcionális***):<br /><textarea class="szovegdoboz" style="width: 300px; height: 100px" id="statusz_szoveg"></textarea><br />'
			+'<input type="submit" class="gomb" value="***Elküldöm***" />'
			+'</form>';
			//ajanlatok
			if (valasz.ajanlatok.length>0) s+='<h2>***Ajánlatok***</h2>'+json2table(valasz.ajanlatok,['***mikor***','***kitől***','***diplomata***','***kinek***','***miről***','***megjegyzés***','***felbontási idő***','***nyilvános***','***akció***'],[
				function(sor) {return sor[5];},
				function(sor) {
					if (sor[8]==1) return valasz.nev;
					if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
				},
				function(sor) {if (sor[10]>0) return '<a href="" onclick="return user_katt('+sor[10]+')">'+sor[11]+'</a>';return '';},
				function(sor) {
					if (sor[8]==0) return valasz.nev;
					if (sor[1]>0) return '<a href="" onclick="return szovetseg_katt('+sor[2]+')">'+sor[3]+'</a>';
					return '<a href="" onclick="return user_katt('+sor[2]+')">'+sor[3]+'</a>';
				},
				function(sor) {
					var s='';
					if (sor[4]==0) s+='***Tűzszünet***';
					if (sor[4]==2) s+='***Testvérszövi***';
					if (sor[4]==3) s+='***MNT***';
					return '<b>'+s+'</b>';
				},
				function(sor) {
					return '<a href="" onclick="window.open(\'diplo_szoveg.php?id='+sor[6]+'\',\'diplo_szoveg\',\'width=600,height=350,scrollbars=1\');return false;">'+sor[7]+'</a>';
				},
				function(sor) {return sor[9]+' ***óra***';},
				function(sor) {if (sor[12]>0) return '***nyilvános***';else return '***titkos***';},
				function(sor) {
					if (sor[8]) {
						return '<a href="" onclick="if (confirm(\'***Biztosan visszavonod?***\')) return diplo_ajanlat_visszavon('+sor[0]+');return false" title="***Visszavonás***"><img src="img/ikonok/cross.gif" /></a>';
					} else {
						return '<a href="" onclick="if (confirm(\'***Biztosan elfogadod?***\')) return diplo_ajanlat_elfogad('+sor[0]+');return false" title="***Elfogadás***"><img src="img/ikonok/tick.gif" /></a> <a href="" onclick="if (confirm(\'***Biztosan elutasítod?***\')) return diplo_ajanlat_elutasit('+sor[0]+');return false" title="***Elutasítás***"><img src="img/ikonok/cross.gif" /></a>';
					}
				}
			]);
			//uj ajanlatok
			s+='<h2>***Új ajánlat***</h2><form onsubmit="return diplo_uj_ajanlat()">'
			+'***kinek***:<br /><input type="text" class="usernev" id="ajanlat_kinek" /><br />'
			+'***miről***:<br /><select id="ajanlat_mirol" class="diplo_statusz">'
			+'<option value="0">***Tűzszünet***</option>'
			+'<option value="2">***Testvérszövetség***</option>'
			+'<option value="3">***Megnemtámadási egyezmény***</option>'
			+'</select><br />'
			+'***felbontási idő***:<br /><input type="text" class="usernev" id="ajanlat_lejarat" /> ***óra*** (***0 és 48 között lehet***)<br />'
			+'***megjegyzés*** (***opcionális***):<br /><textarea class="szovegdoboz" style="width: 300px; height: 100px" id="ajanlat_szoveg"></textarea><br />'
			+'<input type="submit" class="gomb" value="***Elküldöm***" />'
			+'</form>';
			//
			$('aktiv_szovetseg_alapadatai').innerHTML=s;
			new actb($('statusz_kinek'),'ajax_autocomplete_masuserek_es_szovetsegek',0);
			new actb($('ajanlat_kinek'),'ajax_autocomplete_masuserek_es_szovetsegek',0);
		}
		//vendegsegek
		//szovetseg_vendegek_lista
		if (valasz.vendegsegek.length>0) {
			$('szovetseg_vendegek_lista').innerHTML=json2table(valasz.vendegsegek,['***név***','***mikortól***','***ki hívott***'],[
				function(sor) {return '<a href="" onclick="return szovetseg_katt('+sor[0]+')">'+sor[1]+'</a>';},
				function(sor) {return sor[2];},
				function(sor) {return '<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a>';}
			],null,null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','left','left']);
			$('szovetseg_vendegek_div').style.display='block';
		} else {
			$('szovetseg_vendegek_lista').innerHTML='';
			$('szovetseg_vendegek_div').style.display='none';
		}
	});
	return false;
};
function goto_szovetseg() {
	sendRequest('goto_szovetseg.php?q='+encodeURIComponent($('szovetseg_kereso_mezo').value),function(req) {
		var valasz=json2obj(req.responseText);
		if (valasz.letezik) szovetseg_katt(valasz.id);
	});
	return false;
};

function frissit_szov_forum() {
	$('szov_forum_tema_lista').innerHTML='<img src="img/ikonok/ajax-loader.gif" />';
	$('szov_forum_temak_div').style.display='block';
	$('szov_forum_kommentek_div').style.display='none';
	sendRequest('szov_forum_tema_lista.php?szov_id='+aktiv_szovetseg,function(req) {
		if (req.responseText=='') {
			$('szov_forum_tema_lista').innerHTML='';
			$('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='none';
			$('olvasatlan_kommentek_szama').innerHTML='';
			return false;
		}
		var valasz=json2obj(req.responseText);
		$('szov_forum_uj_tema_span').style.display=valasz.ujtemajog?'inline':'none';
		$('szov_forum_szerk_tema_span').style.display=valasz.ujtemajog?'inline':'none';
		$('szov_forum_tema_lista').innerHTML=json2table(valasz.temak,['***téma***','***szerző***','***kommentek***','***utolsó hozzászólás***'],[
		function(sor) {return '<a href="" onclick="szov_forum_aktiv_tema_id='+sor[0]+';return frissit_szov_forum_tema()">'+(sor[9]?'<img src="img/ikonok/exclamation.gif" /> ':'')+sor[1]+'</a>'+(sor[11]?' (***belső***)':'')+(sor[12]?' (***vendég***)':'');},
		function(sor) {if (sor[6]>0) return '<a href="" onclick="return user_katt('+sor[6]+')">'+sor[7]+'</a>';else return '-';},
		function(sor) {return sor[2];},
		function(sor) {if (sor[3]>0) return '<a href="" onclick="return user_katt('+sor[3]+')">'+sor[4]+'</a><br />'+sor[5];else return '-<br />'+sor[5];},
		function(sor) {if (sor[10]) return '<a href="" onclick="if (confirm(\'***Biztosan törölni akarod ezt a témát, és az összes benne lévő kommentet?***\')) szov_forum_tema_torlese('+sor[0]+');return false" title="***téma törlése***"><img src="img/ikonok/cross.gif" /></a>';else return '';}
		],['310px','100px','100px','200px','30px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['left','center','center','center','center']);
		var olv=0;for(var i=0;i<valasz.temak.length;i++) if (valasz.temak[i][9]) olv++;
		if (olv) {
			if (olv>1) $('van_e_olvasatlan_szam').innerHTML=olv;
			else $('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='inline';
			$('olvasatlan_kommentek_szama').innerHTML=(olv<10)?olv:'+';
		} else {
			$('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='none';
			$('olvasatlan_kommentek_szama').innerHTML='';
		}
	});
	return false;
};

function frissit_szov_forum_tema(csak_lapozas) {
	$('szov_forum_temak_div').style.display='none';
	$('szov_forum_kommentek_div').style.display='block';
	$('szov_forum_uj_komment_div').style.display='none';
	$('szov_forum_szerk_tema_div').style.display='none';
	if (szov_forum_aktiv_tema_id) {
		if (csak_lapozas!==1) kommentek_oldal=0;
		$('szov_forum_aktiv_tema').innerHTML='<img src="img/ikonok/ajax-loader.gif" />';
		$('szov_forum_uj_tema_div').style.display='none';
		$('szov_forum_regi_tema_div').style.display='block';
		$('szov_forum_regi_tema_div_also').style.display='block';
		sendRequest('szov_forum_aktiv_tema.php?id='+szov_forum_aktiv_tema_id+'&offset='+kommentek_oldal,function(req) {
			var valasz=json2obj(req.responseText);
			$('szov_forum_regi_tema_id').value=szov_forum_aktiv_tema_id;
			$('szov_forum_szerk_tema').value=valasz.tema_cime;
			$('szov_forum_szerk_tema_belso').checked=valasz.belso;
			$('szov_forum_szerk_tema_vendeg').checked=valasz.vendeg;
			$('szov_forum_aktiv_tema_cime').innerHTML=valasz.tema_cime+' <span style="font-size:8pt">('+valasz.kommentek_szama+'*** hozzászólás***)</span>';
			$('szov_forum_aktiv_tema').innerHTML=json2table(valasz.kommentek,['***ki***','***mit***'],[
			function(sor) {return '<a href="" onclick="return user_katt('+sor[1]+')">'+sor[2]+'</a><br />'+sor[3];},
			function(sor) {return sor[4];},
			function(sor) {if (sor[5]) return '<a href="" onclick="if (confirm(\'***Biztosan törölni akarod ezt a kommentet?***\')) szov_forum_komment_torlese('+sor[0]+');return false" title="***komment törlése***"><img src="img/ikonok/cross.gif" /></a>';else return '';}
			],['200px','520px','30px'],null,function(parit) {return parit?' class="paros_riport_sor"':'';},['center','left','center']);
			//
			kommentek_szama=valasz.kommentek_szama;
			if (kommentek_oldal>0) {$('kommentek_elozo_oldal_ikon').src='img/ikonok/arrow_left.gif';$('kommentek_elozo_oldal_ikon_also').src='img/ikonok/arrow_left.gif';}
			else {$('kommentek_elozo_oldal_ikon').src='img/ikonok/arrow_left-ff.gif';$('kommentek_elozo_oldal_ikon_also').src='img/ikonok/arrow_left-ff.gif';}
			if (kommentek_oldal+komment_per_oldal<kommentek_szama) {$('kommentek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right.gif';$('kommentek_kovetkezo_oldal_ikon_also').src='img/ikonok/arrow_right.gif';}
			else {$('kommentek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right-ff.gif';$('kommentek_kovetkezo_oldal_ikon_also').src='img/ikonok/arrow_right-ff.gif';}
			$('komment_oldalak_szama_span').innerHTML=Math.ceil(kommentek_szama/komment_per_oldal);$('komment_oldalak_szama_span_also').innerHTML=Math.ceil(kommentek_szama/komment_per_oldal);
			$('komment_oldalszam_span').innerHTML=Math.round(kommentek_oldal/komment_per_oldal)+1;$('komment_oldalszam_span_also').innerHTML=Math.round(kommentek_oldal/komment_per_oldal)+1;
			//
			if (valasz.olvasatlan) frissit_olvasatlan_temak_szama();
		});
	} else {
		kommentek_oldal=0;
		$('szov_forum_regi_tema_id').value=0;
		$('szov_forum_aktiv_tema_cime').innerHTML='';
		$('szov_forum_aktiv_tema').innerHTML='';
		$('szov_forum_uj_tema_div').style.display='block';
		$('szov_forum_regi_tema_div').style.display='none';
		$('szov_forum_regi_tema_div_also').style.display='none';
		$('szov_forum_uj_komment_div').style.display='block';
		$('szov_forum_szerk_tema_div').style.display='none';
	}
	return false;
};
function kommentek_elozo_oldal() {
	if (kommentek_oldal>0) kommentek_oldal=kommentek_oldal-komment_per_oldal;
	return frissit_szov_forum_tema(1);
};
function kommentek_kovetkezo_oldal() {
	if (kommentek_oldal+komment_per_oldal<kommentek_szama) kommentek_oldal=kommentek_oldal+komment_per_oldal;
	return frissit_szov_forum_tema(1);
};
function frissit_olvasatlan_temak_szama() {
	sendRequest('olvasatlan_temak_szama.php',function(req) {
		var valasz=json2obj(req.responseText);
		if (valasz.db) {
			if (valasz.db>1) $('van_e_olvasatlan_szam').innerHTML=valasz.db;
			else $('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='inline';
			$('olvasatlan_kommentek_szama').innerHTML=(valasz.db<10)?valasz.db:'+';
		} else {
			$('van_e_olvasatlan_ikon').style.display='none';
			$('olvasatlan_kommentek_szama').innerHTML='';
		}
	});
	return false;
};



function beepitett_okoszim() {
	var poszt_adatok='';
	poszt_adatok='napok_szama='+sanitint($('okoszim_input_napszam').value);
	poszt_adatok+='&osztaly='+sanitint($('okoszim_input_bolygoosztaly').value);
	poszt_adatok+='&terulet='+sanitint($('okoszim_input_bolygoterulet').value);
	poszt_adatok+='&bolygo='+aktiv_bolygo;
	//
	var x='';
	var lista=$('okoszim_input_fajlista').value.split(',');
	for(var i=0;i<lista.length;i++) x+=lista[i]+':'+sanitint($('okoszim_input_faj_'+lista[i]).value)+',';
	poszt_adatok+='&input_fajok='+encodeURIComponent(x);
	x='';
	lista=$('okoszim_input_gyarlista').value.split(',');
	for(var i=0;i<lista.length;i++) x+=lista[i]+':'+sanitint($('okoszim_input_gyar_'+lista[i]).value)+',';
	poszt_adatok+='&input_gyarak='+encodeURIComponent(x);
	//
	sendRequest('beepitett_okoszim.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else if (req.responseText.substring(0,8)=='/*{"kep"') {
			var valasz=json2obj(req.responseText);
			$('okoszim_graf').src='img/okoszim/g'+valasz.bolygo+valasz.kep+'.gif';
			for(var i=0;i<valasz.fajok.length;i++) {
				$('okoszim_output_faj_'+valasz.fajok[i][0]).innerHTML=ezresito(valasz.fajok[i][1]);
				$('okoszim_output_faj_'+valasz.fajok[i][0]).title=valasz.fajok[i][1];
				$('okoszim_output_faj_szazalek_'+valasz.fajok[i][0]).innerHTML=Math.round(valasz.fajok[i][1]/$('okoszim_faj_celszam_'+valasz.fajok[i][0]).value*100);
			}
			$('okoszim_output_gyar_56').innerHTML=ezresito(valasz.kiterm[0]);
			$('okoszim_output_gyar_56').title=valasz.kiterm[0];
			$('okoszim_output_gyar_74').innerHTML=ezresito(valasz.kiterm[1]);
			$('okoszim_output_gyar_74').title=valasz.kiterm[1];
			$('okoszim_output_gyar_64').innerHTML=ezresito(valasz.kiterm[2]);
			$('okoszim_output_gyar_64').title=valasz.kiterm[2];
			$('okoszim_output_gyar_59').innerHTML=ezresito(valasz.kiterm[3]);
			$('okoszim_output_gyar_59').title=valasz.kiterm[3];
		}
		else alert(req.responseText);
	},poszt_adatok);
	return false;
};
function beepitett_okoszim_import(x) {
	switch(x) {
		case 1://oko=szim
			var lista=$('okoszim_input_fajlista').value.split(',');
			for(var i=0;i<lista.length;i++) $('okoszim_input_faj_'+lista[i]).value=$('okoszim_output_faj_'+lista[i]).title;
		break;
		case 2://oko=real
			var lista=$('okoszim_input_fajlista').value.split(',');
			for(var i=0;i<lista.length;i++) $('okoszim_input_faj_'+lista[i]).value=$('okoszim_input_orig_faj_'+lista[i]).value;
		break;
		case 3://ipar=real
			var lista=$('okoszim_input_gyarlista').value.split(',');
			for(var i=0;i<lista.length;i++) $('okoszim_input_gyar_'+lista[i]).value=$('okoszim_input_orig_gyar_'+lista[i]).value;
		break;
	}
	return false;
};


function frissit_minden_5_perces_dolgot() {
	sendRequest('minden_5_perces_dolog.php'+(elso_5_perces_frissites?'?elso=1':''),function(req) {
		var valasz=json2obj(req.responseText);
		//bolygok
		var bolygok=valasz.bolygok;
		var s='';
		for(var i=0;i<bolygok.length;i++) {
			if (bolygok[i][4]>0) s+='<span class="bolygo_utes_jelzo" style="height:'+(12-Math.round(bolygok[i][5]/100*12))+'px" title="***morál***: '+bolygok[i][5]+'%"></span>';
			s+='<a id="menu_sajat_bolygo_'+bolygok[i][0]+'" class="menu_bolygo_osztaly_'+bolygok[i][2]+'" href="#" onclick="return bolygo_katt('+bolygok[i][0]+');">';
			s+='<span>'+bolygok[i][1]+'</span>';
			s+='</a>'+(bolygok[i][3]>0?'':'*')+'<br />';
		}
		$('bolygo_lista').innerHTML=s;
		var x=$('bolygo_lista').childNodes;
		for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
		if ($('menu_sajat_bolygo_'+aktiv_bolygo)) $('menu_sajat_bolygo_'+aktiv_bolygo).childNodes[0].style.fontWeight='bold';
		//flottak
		var flottak=valasz.flottak;
		var s='';
		for(var i=0;i<flottak.length;i++) s=s+'<a id="menu_sajat_flotta_'+flottak[i][0]+'" class="menu_flotta_'+flotta_diplok[flottak[i][2]]+'" href="#" onclick="return flotta_katt('+flottak[i][0]+');"><span>'+flottak[i][1]+'</span></a>'+((flottak[i][4]>0 || flottak[i][5]>0)?'*':'')+'<br />';
		$('flotta_lista').innerHTML=s;
		var x=$('flotta_lista').childNodes;
		for(var i=0;i<x.length;i++) if (x[i].childNodes) if (x[i].childNodes[0]) x[i].childNodes[0].style.fontWeight='normal';
		if ($('menu_sajat_flotta_'+aktiv_flotta)) $('menu_sajat_flotta_'+aktiv_flotta).childNodes[0].style.fontWeight='bold';
		//levelek
		if (valasz.db_bontas[0]) $('olvasatlan_levelek_szama').innerHTML=' ('+valasz.db_bontas[0]+')';else $('olvasatlan_levelek_szama').innerHTML='';
		levelek_szama=valasz.db_ossz_bontas[0];
		if (levelek_oldal>0) $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal+10<levelek_szama) $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span').innerHTML=Math.ceil(levelek_szama/10);
		$('level_oldalszam_span').innerHTML=Math.round(levelek_oldal/10)+1;
		//
		if (valasz.db_bontas[1]) $('olvasatlan_levelek_szama2').innerHTML=' ('+valasz.db_bontas[1]+')';else $('olvasatlan_levelek_szama2').innerHTML='';
		levelek_szama2=valasz.db_ossz_bontas[1];
		if (levelek_oldal2>0) $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon2').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal2+10<levelek_szama2) $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon2').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span2').innerHTML=Math.ceil(levelek_szama2/10);
		$('level_oldalszam_span2').innerHTML=Math.round(levelek_oldal2/10)+1;
		//
		if (valasz.db_bontas[2]) $('olvasatlan_levelek_szama3').innerHTML=' ('+valasz.db_bontas[2]+')';else $('olvasatlan_levelek_szama3').innerHTML='';
		levelek_szama3=valasz.db_ossz_bontas[2];
		if (levelek_oldal3>0) $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left.gif';
		else $('levelek_elozo_oldal_ikon3').src='img/ikonok/arrow_left-ff.gif';
		if (10*levelek_oldal3+10<levelek_szama3) $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right.gif';
		else $('levelek_kovetkezo_oldal_ikon3').src='img/ikonok/arrow_right-ff.gif';
		$('level_oldalak_szama_span3').innerHTML=Math.ceil(levelek_szama3/10);
		$('level_oldalszam_span3').innerHTML=Math.round(levelek_oldal3/10)+1;
		//
		for(var i=1;i<=3;i++) {
			if (valasz.db_bontas[i-1]) {
				$('olv_lev_'+i).innerHTML=' ('+valasz.db_bontas[i-1]+'/'+valasz.db_ossz_bontas[i-1]+')';
			} else {
				$('olv_lev_'+i).innerHTML=' (0/'+valasz.db_ossz_bontas[i-1]+')';
			}
		}
		//csetek
		if (valasz.nagycset) $('olvasatlan_csetek_jelzo').innerHTML='C';
		else $('olvasatlan_csetek_jelzo').innerHTML='';
		//szov_forum
		if (valasz.forum_db) {
			if (valasz.forum_db>1) $('van_e_olvasatlan_szam').innerHTML=valasz.forum_db;
			else $('van_e_olvasatlan_szam').innerHTML='';
			$('van_e_olvasatlan_ikon').style.display='inline';
			$('olvasatlan_kommentek_szama').innerHTML=(valasz.forum_db<10)?valasz.forum_db:'+';
		} else {
			$('van_e_olvasatlan_ikon').style.display='none';
			$('olvasatlan_kommentek_szama').innerHTML='';
		}
	});
	frissit_cset_szobak();
	return false;
};
