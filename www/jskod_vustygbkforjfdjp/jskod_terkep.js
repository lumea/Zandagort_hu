var szinkocka=[[29,29,29],[49,49,49],[67,67,67],[83,83,83],[98,98,98],[112,112,112],[126,126,126],[139,139,139],[152,152,152],[164,164,164],[176,176,176],[188,188,188],[200,200,200],[211,211,211],[222,222,222],[233,233,233],[244,244,244],[255,255,255],[67,67,67],[112,67,67],[152,67,67],[188,67,67],[222,67,67],[255,67,67],[67,67,152],[112,67,152],[152,67,152],[188,67,152],[222,67,152],[255,67,152],[67,67,222],[112,67,222],[152,67,222],[188,67,222],[222,67,222],[255,67,222],[67,112,67],[112,112,67],[152,112,67],[188,112,67],[222,112,67],[255,112,67],[67,112,152],[112,112,152],[152,112,152],[188,112,152],[222,112,152],[255,112,152],[67,112,222],[112,112,222],[152,112,222],[188,112,222],[222,112,222],[255,112,222],[67,152,67],[112,152,67],[152,152,67],[188,152,67],[222,152,67],[255,152,67],[67,152,152],[112,152,152],[152,152,152],[188,152,152],[222,152,152],[255,152,152],[67,152,222],[112,152,222],[152,152,222],[188,152,222],[222,152,222],[255,152,222],[67,188,67],[112,188,67],[152,188,67],[188,188,67],[222,188,67],[255,188,67],[67,188,152],[112,188,152],[152,188,152],[188,188,152],[222,188,152],[255,188,152],[67,188,222],[112,188,222],[152,188,222],[188,188,222],[222,188,222],[255,188,222],[67,222,67],[112,222,67],[152,222,67],[188,222,67],[222,222,67],[255,222,67],[67,222,152],[112,222,152],[152,222,152],[188,222,152],[222,222,152],[255,222,152],[67,222,222],[112,222,222],[152,222,222],[188,222,222],[222,222,222],[255,222,222],[67,255,67],[112,255,67],[152,255,67],[188,255,67],[222,255,67],[255,255,67],[67,255,152],[112,255,152],[152,255,152],[188,255,152],[222,255,152],[255,255,152],[67,255,222],[112,255,222],[152,255,222],[188,255,222],[222,255,222],[255,255,222],[67,67,112],[112,67,112],[152,67,112],[188,67,112],[222,67,112],[255,67,112],[67,67,188],[112,67,188],[152,67,188],[188,67,188],[222,67,188],[255,67,188],[67,67,255],[112,67,255],[152,67,255],[188,67,255],[222,67,255],[255,67,255],[67,112,112],[112,112,112],[152,112,112],[188,112,112],[222,112,112],[255,112,112],[67,112,188],[112,112,188],[152,112,188],[188,112,188],[222,112,188],[255,112,188],[67,112,255],[112,112,255],[152,112,255],[188,112,255],[222,112,255],[255,112,255],[67,152,112],[112,152,112],[152,152,112],[188,152,112],[222,152,112],[255,152,112],[67,152,188],[112,152,188],[152,152,188],[188,152,188],[222,152,188],[255,152,188],[67,152,255],[112,152,255],[152,152,255],[188,152,255],[222,152,255],[255,152,255],[67,188,112],[112,188,112],[152,188,112],[188,188,112],[222,188,112],[255,188,112],[67,188,188],[112,188,188],[152,188,188],[188,188,188],[222,188,188],[255,188,188],[67,188,255],[112,188,255],[152,188,255],[188,188,255],[222,188,255],[255,188,255],[67,222,112],[112,222,112],[152,222,112],[188,222,112],[222,222,112],[255,222,112],[67,222,188],[112,222,188],[152,222,188],[188,222,188],[222,222,188],[255,222,188],[67,222,255],[112,222,255],[152,222,255],[188,222,255],[222,222,255],[255,222,255],[67,255,112],[112,255,112],[152,255,112],[188,255,112],[222,255,112],[255,255,112],[67,255,188],[112,255,188],[152,255,188],[188,255,188],[222,255,188],[255,255,188],[67,255,255],[112,255,255],[152,255,255],[188,255,255],[222,255,255],[255,255,255]];

var terkep_drag_x=-1;
var terkep_drag_y=-1;
var terkep_x_mod=0;
var terkep_y_mod=0;
var terkep_x_mod_racs=0;
var terkep_y_mod_racs=0;
var terkep_x_mod_hexaracs=0;
var terkep_y_mod_hexaracs=0;
var terkep_racstipus=0;//0=sima, 1=hexa
var terkep_x=0;
var terkep_y=0;
var terkep_felszelesseg=358;
var terkep_felmagassag=300;

var celpont_mod=0;

var terkep_hexak=[];
var terkep_bolygok=[];
var terkep_flottak=[];
var jelolo2;
var jelolo_felszelesseg=17;
var jelolo_felmagassag=17;
var jelolo2_felszelesseg=9;
var jelolo2_felmagassag=9;
var flotta_felszelesseg=8;
var flotta_felmagassag=8;

var minx=1;
var maxx=0;
var miny=1;
var maxy=0;
var rminx;
var rmaxx;
var rminy;
var rmaxy;

var zoom=1;

function jump_to_xy(x,y) {
	terkep_csusztat(Math.round(terkep_x-x/zoom),Math.round(terkep_y-y/zoom));
	return false;
};
function jump_to_aktiv_bolygo() {
	terkep_csusztat(Math.round(terkep_x-aktiv_bolygo_x/zoom),Math.round(terkep_y-aktiv_bolygo_y/zoom));
	return false;
};
function jump_to_aktiv_flotta() {
	terkep_csusztat(Math.round(terkep_x-aktiv_flotta_x/zoom),Math.round(terkep_y-aktiv_flotta_y/zoom));
	jelolo2.style.left=Math.round(aktiv_flotta_x/zoom-terkep_x+terkep_felszelesseg-jelolo2_felszelesseg)+'px';
	jelolo2.style.top=Math.round(aktiv_flotta_y/zoom-terkep_y+terkep_felmagassag-jelolo2_felmagassag)+'px';
	return false;
};

function terkepen_van(x,y) {
	if (x<minx) return false;
	if (x>maxx) return false;
	if (y<miny) return false;
	if (y>maxy) return false;
	return true;
};

function frissit_terkep(elso,klir) {
	if (klir==1) {
		rminx=1;rmaxx=0;
		rminy=1;rmaxy=0;
		if (zoom<4) {
			jelolo_felszelesseg=17;
			jelolo_felmagassag=17;
		} else {
			jelolo_felszelesseg=9;
			jelolo_felmagassag=9;
		}
		if (terkep_racstipus==1) {
			switch(zoom) {
				case 1:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_hexaracs_500.gif)';break;
				case 2:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_hexaracs_500_2.gif)';break;
				case 4:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_hexaracs_500_4.gif)';break;
				case 8:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_hexaracs_500_8.gif)';break;
			}
		} else {
			switch(zoom) {
				case 1:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_racs_500.gif)';break;
				case 2:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_racs_500_2.gif)';break;
				case 4:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_racs_500_4.gif)';break;
				case 8:document.getElementById('terkep_klikkento').style.backgroundImage='url(img/terkep_racs_500_8.gif)';break;
			}
		}
	} else {
		rminx=minx;rmaxx=maxx;
		rminy=miny;rmaxy=maxy;
	}
	minx=(terkep_x-2*terkep_felszelesseg)*zoom;
	maxx=(terkep_x+2*terkep_felszelesseg)*zoom;
	miny=(terkep_y-2*terkep_felmagassag)*zoom;
	maxy=(terkep_y+2*terkep_felmagassag)*zoom;
	//kieso hexak torlese
	for(var i=0;i<terkep_hexak.length;i++) if ((!terkepen_van(terkep_hexak[i].x,terkep_hexak[i].y)) || klir) {
		var x=document.getElementById('terkep_keret').removeChild(document.getElementById('terkephexa'+i));
		delete x;
		if (i+1<terkep_hexak.length) {
			document.getElementById('terkephexa'+(terkep_hexak.length-1)).id='terkephexa'+i;
			terkep_hexak[i]=terkep_hexak[terkep_hexak.length-1];
			terkep_hexak[i].sorszam=i;
		}
		terkep_hexak.pop();
		i--;
	}
	//kieso bolygok torlese
	for(var i=0;i<terkep_bolygok.length;i++) if ((!terkepen_van(terkep_bolygok[i].x,terkep_bolygok[i].y)) || klir) {
		var x=document.getElementById('terkep_keret').removeChild(document.getElementById('terkepbolygo'+i));
		delete x;
		if (i+1<terkep_bolygok.length) {
			document.getElementById('terkepbolygo'+(terkep_bolygok.length-1)).id='terkepbolygo'+i;
			terkep_bolygok[i]=terkep_bolygok[terkep_bolygok.length-1];
			terkep_bolygok[i].sorszam=i;
		}
		terkep_bolygok.pop();
		i--;
	}
	//klir eseten flottak torlese
	if (klir) for(var i=0;i<terkep_flottak.length;i++) {
		var x=document.getElementById('terkep_keret').removeChild(document.getElementById('terkepflotta'+i));
		delete x;
		if (i+1<terkep_flottak.length) {
			document.getElementById('terkepflotta'+(terkep_flottak.length-1)).id='terkepflotta'+i;
			terkep_flottak[i]=terkep_flottak[terkep_flottak.length-1];
			terkep_flottak[i].sorszam=i;
		}
		terkep_flottak.pop();
		i--;
	}
	var kveri='terkep_adatok.php?minx='+minx+'&maxx='+maxx+'&miny='+miny+'&maxy='+maxy+'&rminx='+rminx+'&rmaxx='+rmaxx+'&rminy='+rminy+'&rmaxy='+rmaxy;
	sendRequest(kveri,function(req) {
		var valasz=json2obj(req.responseText);
		//uj hexak
		var uj_terkep_hexak=valasz.hexak;
		for(var i=0;i<uj_terkep_hexak.length;i++) {
			uj_terkep_hexak[i].sorszam=terkep_hexak.length;
			uj_terkephexa_felrakasa(
				uj_terkep_hexak[i].sorszam,
				uj_terkep_hexak[i].x,
				uj_terkep_hexak[i].y,
				uj_terkep_hexak[i].szin
			);
			terkep_hexak.push(uj_terkep_hexak[i]);
		}
		//uj bolygok
		var uj_terkep_bolygok=valasz.bolygok;
		for(var i=0;i<uj_terkep_bolygok.length;i++) {
			uj_terkep_bolygok[i].sorszam=terkep_bolygok.length;
			uj_terkepbolygo_felrakasa(
				uj_terkep_bolygok[i].id,
				uj_terkep_bolygok[i].sorszam,
				uj_terkep_bolygok[i].x,
				uj_terkep_bolygok[i].y,
				uj_terkep_bolygok[i].nev,
				uj_terkep_bolygok[i].tied,
				uj_terkep_bolygok[i].tulaj,
				uj_terkep_bolygok[i].osztaly,
				uj_terkep_bolygok[i].vedelem,
				uj_terkep_bolygok[i].szovi,
				(valasz.diplok[1])?(in_array(uj_terkep_bolygok[i].tulaj_szov,valasz.diplok[1])?1:0):0,
				(valasz.diplok[2])?(in_array(uj_terkep_bolygok[i].tulaj_szov,valasz.diplok[2])?1:0):0,
				(valasz.diplok[3])?(in_array(uj_terkep_bolygok[i].tulaj_szov,valasz.diplok[3])?1:0):0,
				uj_terkep_bolygok[i].hold,
				uj_terkep_bolygok[i].terulet
			);
			terkep_bolygok.push(uj_terkep_bolygok[i]);
		}
		//flottak
		for(var i=0;i<terkep_flottak.length;i++) terkep_flottak[i].latszik=0;
		var friss_terkep_flottak=valasz.flottak;
		for(var i=0;i<friss_terkep_flottak.length;i++) {
			var sorszam=get_terkepflotta_sorszam_by_id(friss_terkep_flottak[i].id);
			if (sorszam>=0) {
				if (terkep_flottak[sorszam].x!=friss_terkep_flottak[i].x || terkep_flottak[sorszam].y!=friss_terkep_flottak[i].y) {
					terkep_flottak[sorszam].x=friss_terkep_flottak[i].x;
					terkep_flottak[sorszam].y=friss_terkep_flottak[i].y;
					document.getElementById('terkepflotta'+sorszam).style.left=Math.round(friss_terkep_flottak[i].x/zoom-terkep_x+terkep_felszelesseg-flotta_felszelesseg)+'px';
					document.getElementById('terkepflotta'+sorszam).style.top=Math.round(friss_terkep_flottak[i].y/zoom-terkep_y+terkep_felmagassag-flotta_felmagassag)+'px';
					if (friss_terkep_flottak[i].id==aktiv_flotta) {
						aktiv_flotta_x=friss_terkep_flottak[i].x;
						aktiv_flotta_y=friss_terkep_flottak[i].y;
						jelolo2.style.left=Math.round(aktiv_flotta_x/zoom-terkep_x+terkep_felszelesseg-jelolo2_felszelesseg)+'px';
						jelolo2.style.top=Math.round(aktiv_flotta_y/zoom-terkep_y+terkep_felmagassag-jelolo2_felmagassag)+'px';
					}
				}
				terkep_flottak[sorszam]=friss_terkep_flottak[i];
				terkep_flottak[sorszam].latszik=1;
			} else {
				friss_terkep_flottak[i].sorszam=terkep_flottak.length;
				friss_terkep_flottak[i].latszik=1;
				uj_terkepflotta_felrakasa(
					friss_terkep_flottak[i].id,
					friss_terkep_flottak[i].sorszam,
					friss_terkep_flottak[i].x,
					friss_terkep_flottak[i].y,
					friss_terkep_flottak[i].nev,
					friss_terkep_flottak[i].tied,
					friss_terkep_flottak[i].tulaj,
					friss_terkep_flottak[i].kezeled,
					friss_terkep_flottak[i].kezelo,
					friss_terkep_flottak[i].szovi,
					friss_terkep_flottak[i].egyenertek,
					(valasz.diplok[1])?(in_array(friss_terkep_flottak[i].tulaj_szov,valasz.diplok[1])?1:0):0,
					(valasz.diplok[2])?(in_array(friss_terkep_flottak[i].tulaj_szov,valasz.diplok[2])?1:0):0,
					(valasz.diplok[3])?(in_array(friss_terkep_flottak[i].tulaj_szov,valasz.diplok[3])?1:0):0
				);
				terkep_flottak.push(friss_terkep_flottak[i]);
			}
		}
		for(var i=0;i<terkep_flottak.length;i++) if (terkep_flottak[i].latszik==0) {
			if (terkep_flottak[i].id==aktiv_flotta) {
				jelolo2.style.left='-1000px';
				jelolo2.style.top='-1000px';
				aktiv_flotta=0;
			}
			var x=document.getElementById('terkep_keret').removeChild(document.getElementById('terkepflotta'+i));
			delete x;
			if (i+1<terkep_flottak.length) {
				document.getElementById('terkepflotta'+(terkep_flottak.length-1)).id='terkepflotta'+i;
				terkep_flottak[i]=terkep_flottak[terkep_flottak.length-1];
				terkep_flottak[i].sorszam=i;
			}
			terkep_flottak.pop();
			i--;
		}
		if (klir) {frissit_menu();frissit_aktiv_oldal();}
	});
	return false;
};


function get_terkepflotta_sorszam_by_id(id) {
	for(var i=0;i<terkep_flottak.length;i++) if (terkep_flottak[i].id==id) return i;
	return -1;
};
function get_terkepbolygo_sorszam_by_id(id) {
	for(var i=0;i<terkep_bolygok.length;i++) if (terkep_bolygok[i].id==id) return i;
	return -1;
};


function uj_terkepbolygo_felrakasa(id,sorszam,x,y,nev,tied,tulaj,osztaly,vedelem,szovi,hadi,beke,mnt,hold,terulet) {
	var ujbdiv=document.createElement('div');
	ujbdiv.className='terkep_bolygo_div';
	ujbdiv.id='terkepbolygo'+sorszam;
	ujbdiv.title=nev;
	var parancsok='';
	if (vedelem==2) {
		parancsok+='if (celpont_mod==1) {flotta_parancs_megy_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==2) {flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==3) {flotta_parancs_tamad_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();alert(\'***Védett bolygó***\');return false;}else';
		parancsok+=' if (celpont_mod==4) {flotta_parancs_raid_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();alert(\'***Védett bolygó***\');return false;}else';
	} else if (vedelem==1) {
		parancsok+='if (celpont_mod==1) {flotta_parancs_megy_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==2) {flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==3) {flotta_parancs_tamad_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();alert(\'***Foglalás ellen védett bolygó***\');return false;}else';
		parancsok+=' if (celpont_mod==4) {flotta_parancs_raid_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();alert(\'***Foglalás ellen védett bolygó***\');return false;}else';
	} else {
		parancsok+='if (celpont_mod==1) {flotta_parancs_megy_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==2) {flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==3) {flotta_parancs_tamad_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();return false;}else';
		parancsok+=' if (celpont_mod==4) {flotta_parancs_raid_bolygo(aktiv_flotta,'+id+');clear_celpont_mod();return false;}else';
	}
	parancsok+=' return bolygo_katt('+id+');';
	var tartalom='<a href="" onclick="'+parancsok+'"';
	tartalom+=' oncontextmenu="return bolygo_context(event,'+id+','+x+','+y+','+tied+','+vedelem+','+szovi+');"';
	if (tied) tartalom+=' style="color:rgb(0,255,0)'+(vedelem?';font-style: italic':'')+'"';
	else if (tulaj>0) {
		if (szovi) tartalom+=' style="color:rgb(100,160,255)'+(vedelem?';font-style: italic':'')+'"';
		else if (hadi) tartalom+=' style="color:rgb(255,0,0)'+(vedelem?';font-style: italic':'')+'"';
		else if (beke) tartalom+=' style="color:rgb(255,160,0)'+(vedelem?';font-style: italic':'')+'"';
		else if (mnt) tartalom+=' style="color:rgb(255,255,0)'+(vedelem?';font-style: italic':'')+'"';
		else tartalom+=' style="color:rgb(160,0,0)'+(vedelem?';font-style: italic':'')+'"';
	}
	if (zoom==2) tartalom+=' class="apro_szoveg_2"';
	if (zoom==4) tartalom+=' class="apro_szoveg_4"';
	if (zoom==8) tartalom+=' class="apro_szoveg_8"';
	tartalom+='><img src="img/ikonok/bolygo_v2_'+bolygo_osztalyok[osztaly-1];
	if (zoom==2) tartalom+='24';
	if (zoom==1) tartalom+='32';
	if (hold) tartalom+='_h';
	tartalom+='.gif" style="vertical-align:-';
	if (zoom==1) tartalom+='12';
	else if (zoom==2) tartalom+='9';
	else tartalom+='5';
	tartalom+='px" />';
	if (terulet>2000000) tartalom+='<span style="font-size:7pt">'+(terulet/1000000)+'</span>';
	tartalom+='<br />'+nev;
	if (zoom<2) tartalom+='<br />'+ykoordinata(y)+', '+xkoordinata(x);
	tartalom+='</a>';
	ujbdiv.innerHTML=tartalom;
	document.getElementById('terkep_keret').appendChild(ujbdiv);
	var ikon_fel=17;
	if (zoom==2) ikon_fel=13;
	if (zoom==4) ikon_fel=9;
	if (zoom==8) ikon_fel=9;
	ujbdiv.style.left=Math.round(x/zoom-terkep_x+terkep_felszelesseg-ikon_fel)+'px';
	ujbdiv.style.top=Math.round(y/zoom-terkep_y+terkep_felmagassag-ikon_fel)+'px';
	return false;
};
function uj_terkepflotta_felrakasa(id,sorszam,x,y,nev,tied,tulaj,kezeled,kezelo,szovi,egyenertek,hadi,beke,mnt) {
	var ujbdiv=document.createElement('div');
	document.getElementById('terkep_keret').appendChild(ujbdiv);
	ujbdiv.className='terkep_flotta_div';
	ujbdiv.style.left=Math.round(x/zoom-terkep_x+terkep_felszelesseg-8)+'px';
	ujbdiv.style.top=Math.round(y/zoom-terkep_y+terkep_felmagassag-8)+'px';
	ujbdiv.id='terkepflotta'+sorszam;
	if (egyenertek=='?' || isNaN(egyenertek)) ujbdiv.title=nev+' (?)';
	else ujbdiv.title=nev+' ('+szazadresz_title(egyenertek)+')';
	var parancsok='';
	parancsok+='if (celpont_mod==1) {flotta_parancs_megy_flotta(aktiv_flotta,'+id+');hide_terkep_menus();clear_celpont_mod();return false;}else';
	parancsok+=' if (celpont_mod==2) {flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');hide_terkep_menus();clear_celpont_mod();return false;}else';
	parancsok+=' if (celpont_mod==3) {flotta_parancs_tamad_flotta(aktiv_flotta,'+id+');hide_terkep_menus();clear_celpont_mod();return false;}else';
	parancsok+=' {hide_terkep_menus();return flotta_katt('+id+');}';
	var tartalom='<a href="" onclick="'+parancsok+'"';
	tartalom+=' oncontextmenu="return flotta_context(event,'+id+','+tied+','+kezeled+','+szovi+');"';
	tartalom+=' onmouseover="return flotta_hover('+id+','+x+','+y+');"';
	if (tied) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_sajat.gif" />';
	else if (tulaj>0) {
		if (szovi) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_szovi.gif" />';
		else if (hadi) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_hadban.gif" />';
		else if (beke) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_beke.gif" />';
		else if (mnt) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_mnt.gif" />';
		else tartalom+='><img src="img/ikonok/flotta_ikon_16x16_semli.gif" />';
	} else if (tulaj<0) tartalom+='><img src="img/ikonok/flotta_ikon_16x16_zanda.gif" />';
	else tartalom+='><img src="img/ikonok/flotta_ikon_16x16_npc.gif" />';
	tartalom+='</a>';
	ujbdiv.innerHTML=tartalom;
	return false;
};
function uj_terkephexa_felrakasa(sorszam,x,y,szin) {
	var ujbdiv=document.createElement('div');
	document.getElementById('terkep_keret').appendChild(ujbdiv);
	ujbdiv.className='terkep_hexa_div';
	ujbdiv.style.left=Math.round(x/zoom-terkep_x+terkep_felszelesseg-286/2/zoom)+'px';
	ujbdiv.style.top=Math.round(y/zoom-terkep_y+terkep_felmagassag-248/2/zoom)+'px';
	ujbdiv.id='terkephexa'+sorszam;
	ujbdiv.style.width=Math.round(286/zoom)+'px';
	ujbdiv.style.height=Math.round(248/zoom)+'px';
	if (szin==1) ujbdiv.style.background='url(img/terkep_hexa_sajat'+(zoom>1?('_'+zoom):'')+'.png) repeat-y';
	else if (szin==2) ujbdiv.style.background='url(img/terkep_hexa_szovi'+(zoom>1?('_'+zoom):'')+'.png) repeat-y';
	else if (szin==3) ujbdiv.style.background='url(img/terkep_hexa_testver'+(zoom>1?('_'+zoom):'')+'.png) repeat-y';
	else ujbdiv.style.background='url(img/terkep_hexa_piros'+(zoom>1?('_'+zoom):'')+'.png) repeat-y';
	return false;
};

function flotta_context(e,id,tied,kezeled,szovi) {
	if (!e) var e = window.event;
	var btn = e.which ? e.which : event.button;
	if (btn!=1) {
		var sorszam=get_terkepflotta_sorszam_by_id(id);
		if (tied || kezeled) {
			set_aktiv_flotta(id,tied);
			var parancsok='';
			parancsok+='<ul>';
			parancsok+='<li style="font-weight:bold">'+terkep_flottak[sorszam].nev+'</li>';
			parancsok+='<li><a href="" onclick="set_celpont_mod(1);hide_terkep_menus();return false">***Menj***</a></li>';
			parancsok+='<li><a href="" onclick="set_celpont_mod(2);hide_terkep_menus();return false">***Járőrözz***</a></li>';
			parancsok+='<li><a href="" onclick="set_celpont_mod(3);hide_terkep_menus();return false">***Támadj***</a></li>';
			parancsok+='<li><a href="" onclick="set_celpont_mod(4);hide_terkep_menus();return false">***Portyázz***</a></li>';
			parancsok+='<li><a href="" onclick="hide_terkep_menus();return flotta_parancs_vissza('+id+');">***Vissza***</a></li>';
			parancsok+='<li><a href="" onclick="hide_terkep_menus();return flotta_parancs_allj('+id+');">***Állj***</a></li>';
			parancsok+='</ul>';
			document.getElementById('terkep_context').innerHTML=parancsok;
			document.getElementById('terkep_context').style.left=(parseInt(document.getElementById('terkepflotta'+sorszam).style.left)+16)+'px';
			document.getElementById('terkep_context').style.top=(parseInt(document.getElementById('terkepflotta'+sorszam).style.top)+16)+'px';
			document.getElementById('terkep_context').style.display='block';
		} else {
			if (aktiv_flotta>0) if (aktiv_flotta_tied) {
				var parancsok='';
				parancsok+='<ul>';
				parancsok+='<li style="font-weight:bold">*** flottáddal prefix***'+aktiv_flotta_nev+'*** flottáddal***</li>';
				if (szovi) {
					parancsok+='<li><a href="" onclick="flotta_parancs_megy_flotta(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Menj***</a></li>';
					parancsok+='<li><a href="" class="agresszio" onclick="flotta_parancs_tamad_flotta(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Támadj***</a></li>';
				} else {
					parancsok+='<li><a href="" onclick="flotta_parancs_tamad_flotta(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Támadj***</a></li>';
				}
				parancsok+='</ul>';
				document.getElementById('terkep_context').innerHTML=parancsok;
				document.getElementById('terkep_context').style.left=(parseInt(document.getElementById('terkepflotta'+sorszam).style.left)+16)+'px';
				document.getElementById('terkep_context').style.top=(parseInt(document.getElementById('terkepflotta'+sorszam).style.top)+16)+'px';
				document.getElementById('terkep_context').style.display='block';
			}
		}
	}
	return false;
};
function bolygo_context(e,id,x,y,tied,vedelem,szovi) {
	if (!e) var e = window.event;
	var btn = e.which ? e.which : event.button;
	if (btn!=1) {
		var sorszam=get_terkepbolygo_sorszam_by_id(id);
		if (aktiv_flotta>0) {
			if (aktiv_flotta_tied) {
				var parancsok='<ul>';
				if (tied) {
					parancsok+='<li style="font-weight:bold">*** flottáddal prefix***'+aktiv_flotta_nev+'*** flottáddal***</li>';
					parancsok+='<li><a href="" onclick="flotta_parancs_megy_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Menj***</a></li>';
					parancsok+='<li><a href="" onclick="flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');hide_terkep_menus();return false">***Járőrözz***</a></li>';
				} else {
					if (szovi) {
						parancsok+='<li style="font-weight:bold">*** flottáddal prefix***'+aktiv_flotta_nev+'*** flottáddal***</li>';
						parancsok+='<li><a href="" onclick="flotta_parancs_megy_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Menj***</a></li>';
						parancsok+='<li><a href="" onclick="flotta_parancs_patrol(aktiv_flotta,'+x+','+y+');hide_terkep_menus();return false">***Járőrözz***</a></li>';
						parancsok+='<li><a href="" class="agresszio" onclick="flotta_parancs_tamad_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Támadj***</a>'+(vedelem>0?(vedelem==1?' (***foglalás ellen védett***)':' (***védett***)'):'')+'</li>';
						parancsok+='<li><a href="" class="agresszio" onclick="flotta_parancs_raid_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Portyázz***</a>'+(vedelem>0?(vedelem==1?' (***foglalás ellen védett***)':' (***védett***)'):'')+'</li>';
					} else {
						parancsok+='<li style="font-weight:bold">*** flottáddal prefix***'+aktiv_flotta_nev+'*** flottáddal***</li>';
						parancsok+='<li><a href="" onclick="flotta_parancs_tamad_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Támadj***</a>'+(vedelem>0?(vedelem==1?' (***foglalás ellen védett***)':' (***védett***)'):'')+'</li>';
						parancsok+='<li><a href="" onclick="flotta_parancs_raid_bolygo(aktiv_flotta,'+id+');hide_terkep_menus();return false">***Portyázz***</a>'+(vedelem>0?(vedelem==1?' (***foglalás ellen védett***)':' (***védett***)'):'')+'</li>';
					}
				}
				parancsok+='</ul>';
				document.getElementById('terkep_context').innerHTML=parancsok;
				document.getElementById('terkep_context').style.left=Math.round(terkep_bolygok[sorszam].x/zoom-terkep_x+terkep_felszelesseg-jelolo_felszelesseg)+'px';
				document.getElementById('terkep_context').style.top=Math.round(terkep_bolygok[sorszam].y/zoom-terkep_y+terkep_felmagassag-jelolo_felmagassag-20)+'px';
				document.getElementById('terkep_context').style.display='block';
			}
		}
	}
	return false;
};

function flotta_hover(id,x,y) {
	var id_lista=[];
	id_lista.push(id);
	for(var i=0;i<terkep_flottak.length;i++) if (terkep_flottak[i].id!=id) if (Math.abs(terkep_flottak[i].x-x)<10*zoom) if (Math.abs(terkep_flottak[i].y-y)<10*zoom) {
		id_lista.push(terkep_flottak[i].id);
	}
	var sorszam=0;
	var alap_sorszam=get_terkepflotta_sorszam_by_id(id);
	var tf_anchor='';
	var parancsok='';
	parancsok+='<ul>';
	for(var i=0;i<id_lista.length;i++) {
		sorszam=get_terkepflotta_sorszam_by_id(id_lista[i]);
		tf_anchor=document.getElementById('terkepflotta'+sorszam).innerHTML;
		tf_anchor=tf_anchor.replace(/onmouseover="[^"]*"/,'');
		parancsok+='<li>'+tf_anchor.substr(0,tf_anchor.length-4)+' '+document.getElementById('terkepflotta'+sorszam).title+'</a></li>';
	}
	parancsok+='</ul>';
	document.getElementById('terkep_hover').innerHTML=parancsok;
	document.getElementById('terkep_hover').style.left=(parseInt(document.getElementById('terkepflotta'+alap_sorszam).style.left))+'px';
	document.getElementById('terkep_hover').style.top=(parseInt(document.getElementById('terkepflotta'+alap_sorszam).style.top))+'px';
	document.getElementById('terkep_hover').style.display='block';
	return false;
};

function hide_terkep_menus() {
	document.getElementById('terkep_hover').style.display='none';
	document.getElementById('terkep_context').style.display='none';
	return false;
};
function set_celpont_mod(m) {
	celpont_mod=m;
	document.getElementById('terkep_klikkento').style.cursor='crosshair';
	var divek=document.getElementById('terkep_keret').getElementsByTagName('div');
	for(var i=0;i<divek.length;i++) if (divek[i].className=='terkep_bolygo_div') {
		divek[i].style.cursor='crosshair';
		divek[i].childNodes[0].style.cursor='crosshair';
	}
	return false;
};
function clear_celpont_mod() {
	celpont_mod=0;
	document.getElementById('terkep_klikkento').style.cursor='-moz-grab';
	var divek=document.getElementById('terkep_keret').getElementsByTagName('div');
	for(var i=0;i<divek.length;i++) if (divek[i].className=='terkep_bolygo_div') {
		divek[i].style.cursor='-moz-grab';
		divek[i].childNodes[0].style.cursor='-moz-grab';
	}
	return false;
};
function terkep_klikk(e) {
	if (!e) var e = window.event;
	var btn = e.which ? e.which : event.button;
	if (celpont_mod==1) {
		var kur=getPosition(e);
		flotta_parancs_megy_xy(aktiv_flotta,
		(kur.x-findPos(document.getElementById('terkep_keret'))[0]+terkep_x-terkep_felszelesseg)*zoom,
		(kur.y-findPos(document.getElementById('terkep_keret'))[1]+terkep_y-terkep_felmagassag)*zoom);
		clear_celpont_mod();
	} else if (celpont_mod==2) {
		var kur=getPosition(e);
		flotta_parancs_patrol(aktiv_flotta,
		(kur.x-findPos(document.getElementById('terkep_keret'))[0]+terkep_x-terkep_felszelesseg)*zoom,
		(kur.y-findPos(document.getElementById('terkep_keret'))[1]+terkep_y-terkep_felmagassag)*zoom);
		clear_celpont_mod();
	}
	return false;
};

function terkep_eger(e,f) {
	if (celpont_mod>0) return false;
	if (!e) var e = window.event;
	var btn = e.which ? e.which : event.button;
	if (f==2) {//move
		if (terkep_drag_x>=0) {
			hide_terkep_menus();
			terkep_csusztat(e.clientX-terkep_drag_x, e.clientY-terkep_drag_y);
			terkep_drag_x = e.clientX;
			terkep_drag_y = e.clientY;
		}
		return true;
	}
	if (btn==1) {
		switch (f) {
			case 0://down
				hide_terkep_menus();
				terkep_drag_x = e.clientX;
				terkep_drag_y = e.clientY;
				document.getElementById('terkep_klikkento').style.cursor='-moz-grabbing';
				break;
			case 1://up,out
				terkep_drag_x = -1;
				document.getElementById('terkep_klikkento').style.cursor='-moz-grab';
				frissit_terkep();
				break;
		}
	} else {
		//if (f==0) alert('JOBB');
	}
	return false;
};

function terkep_csusztat(x,y) {
	terkep_x-=x;terkep_y-=y;
	//hexak
	for(var i=0;i<terkep_hexak.length;i++) {
		document.getElementById('terkephexa'+i).style.left=(parseInt(document.getElementById('terkephexa'+i).style.left)+x)+'px';
		document.getElementById('terkephexa'+i).style.top=(parseInt(document.getElementById('terkephexa'+i).style.top)+y)+'px';
	}
	//bolygok
	for(var i=0;i<terkep_bolygok.length;i++) {
		document.getElementById('terkepbolygo'+i).style.left=(parseInt(document.getElementById('terkepbolygo'+i).style.left)+x)+'px';
		document.getElementById('terkepbolygo'+i).style.top=(parseInt(document.getElementById('terkepbolygo'+i).style.top)+y)+'px';
	}
	//flottak
	for(var i=0;i<terkep_flottak.length;i++) {
		document.getElementById('terkepflotta'+i).style.left=(parseInt(document.getElementById('terkepflotta'+i).style.left)+x)+'px';
		document.getElementById('terkepflotta'+i).style.top=(parseInt(document.getElementById('terkepflotta'+i).style.top)+y)+'px';
	}
	if (aktiv_flotta) {
		jelolo2.style.left=(parseInt(jelolo2.style.left)+x)+'px';
		jelolo2.style.top=(parseInt(jelolo2.style.top)+y)+'px';
	}
	//terkep_context
	document.getElementById('terkep_context').style.left=(parseInt(document.getElementById('terkep_context').style.left)+x)+'px';
	document.getElementById('terkep_context').style.top=(parseInt(document.getElementById('terkep_context').style.top)+y)+'px';
	//csillagos hatter, parallax scrollozassal, erzekelteti a zoom szintet, mert adott parsec-nyi scrollozasnal mindig ugyanannyit mozdul el a hatter
	terkep_x_mod -= x*zoom/16;while (terkep_x_mod<0) terkep_x_mod+=1024;
	terkep_x_mod %= 1024;
	terkep_y_mod -= y*zoom/16;while (terkep_y_mod<0) terkep_y_mod+=1024;
	terkep_y_mod %= 1024;
	document.getElementById('terkep_mozgo_hatter').style.left=(-terkep_x_mod)+'px';//kicserelni backgroundPosition-ra
	document.getElementById('terkep_mozgo_hatter').style.top=(-terkep_y_mod)+'px';
	//racs
	terkep_x_mod_racs -= x;while (terkep_x_mod_racs<0) terkep_x_mod_racs+=500;
	terkep_x_mod_racs %= 500;
	terkep_y_mod_racs -= y;while (terkep_y_mod_racs<0) terkep_y_mod_racs+=500;
	terkep_y_mod_racs %= 500;
	terkep_x_mod_hexaracs -= x;while (terkep_x_mod_hexaracs<0) terkep_x_mod_hexaracs+=644;
	terkep_x_mod_hexaracs %= 644;
	terkep_y_mod_hexaracs -= y;while (terkep_y_mod_hexaracs<0) terkep_y_mod_hexaracs+=372;
	terkep_y_mod_hexaracs %= 372;
	if (terkep_racstipus==1) document.getElementById('terkep_klikkento').style.backgroundPosition=(terkep_felszelesseg-terkep_x_mod_hexaracs)+'px '+(terkep_felmagassag-terkep_y_mod_hexaracs)+'px';
	else document.getElementById('terkep_klikkento').style.backgroundPosition=(terkep_felszelesseg-terkep_x_mod_racs)+'px '+(terkep_felmagassag-terkep_y_mod_racs)+'px';
	//tengelyek
	document.getElementById('terkep_x_koord1').style.left=(4+terkep_felszelesseg-terkep_x_mod_racs)+'px';
	document.getElementById('terkep_x_koord2').style.left=(504+terkep_felszelesseg-terkep_x_mod_racs)+'px';
	document.getElementById('terkep_x_koord1').innerHTML=xkoordinata(Math.floor(terkep_x/500)*500*zoom);
	document.getElementById('terkep_x_koord2').innerHTML=xkoordinata(Math.floor((500+terkep_x)/500)*500*zoom);
	document.getElementById('terkep_y_koord1').style.top=(-18+terkep_felmagassag-terkep_y_mod_racs)+'px';
	document.getElementById('terkep_y_koord2').style.top=(482+terkep_felmagassag-terkep_y_mod_racs)+'px';
	document.getElementById('terkep_y_koord1').innerHTML=ykoordinata(Math.floor(terkep_y/500)*500*zoom);
	document.getElementById('terkep_y_koord2').innerHTML=ykoordinata(Math.floor((500+terkep_y)/500)*500*zoom);
	return false;
};

function getPosition(e) {
	e = e || window.event;
	var cursor = {x:0, y:0};
	if (e.pageX || e.pageY) {
		cursor.x = e.pageX;
		cursor.y = e.pageY;
	} else {
		var de = document.documentElement;
		var b = document.body;
		cursor.x = e.clientX + (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
		cursor.y = e.clientY + (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	}
	return cursor;
};
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
};

function terkep_zoom_in() {
	if (zoom>1) {
		zoom/=2;
		hide_terkep_menus();
		frissit_terkep(0,1);
		if (aktiv_flotta) {
			jelolo2.style.left=Math.round(aktiv_flotta_x/zoom-terkep_x+terkep_felszelesseg-jelolo2_felszelesseg)+'px';
			jelolo2.style.top=Math.round(aktiv_flotta_y/zoom-terkep_y+terkep_felmagassag-jelolo2_felmagassag)+'px';
		}
		terkep_csusztat(-terkep_x,-terkep_y);
	}
	if (zoom>1) document.getElementById('terkep_zoom_in_ikon').src='img/ikonok/add.gif';else document.getElementById('terkep_zoom_in_ikon').src='img/ikonok/add-ff.gif';
	if (zoom<8) document.getElementById('terkep_zoom_out_ikon').src='img/ikonok/delete.gif';else document.getElementById('terkep_zoom_out_ikon').src='img/ikonok/delete-ff.gif';
	return false;
}
function terkep_zoom_out() {
	if (zoom<8) {
		zoom*=2;
		hide_terkep_menus();
		frissit_terkep(0,1);
		if (aktiv_flotta) {
			jelolo2.style.left=Math.round(aktiv_flotta_x/zoom-terkep_x+terkep_felszelesseg-jelolo2_felszelesseg)+'px';
			jelolo2.style.top=Math.round(aktiv_flotta_y/zoom-terkep_y+terkep_felmagassag-jelolo2_felmagassag)+'px';
		}
		terkep_csusztat(Math.round(terkep_x/2),Math.round(terkep_y/2));
	}
	if (zoom>1) document.getElementById('terkep_zoom_in_ikon').src='img/ikonok/add.gif';else document.getElementById('terkep_zoom_in_ikon').src='img/ikonok/add-ff.gif';
	if (zoom<8) document.getElementById('terkep_zoom_out_ikon').src='img/ikonok/delete.gif';else document.getElementById('terkep_zoom_out_ikon').src='img/ikonok/delete-ff.gif';
	return false;
}
function gotoxy() {
	sendRequest('gotoxy.php?q='+encodeURIComponent(document.getElementById('terkep_gotoxy').value),function(req) {
		var valasz=json2obj(req.responseText);
		terkep_csusztat(Math.round(terkep_x-valasz.x/zoom),Math.round(terkep_y-valasz.y/zoom));
		frissit_terkep();
		document.getElementById('terkep_attekinto').style.display='none';
	});
	return false;
}
function terkep_scroll(dx,dy) {
	terkep_csusztat(dx,dy);
	frissit_terkep();
	return false;
}
function attekinto_terkep_klikk(e) {
	if (!e) var e = window.event;
	var kur=getPosition(e);
	var x=Math.round((kur.x-findPos(document.getElementById('terkep_attekinto'))[0]-358)*80000/287);
	var y=Math.round((kur.y-findPos(document.getElementById('terkep_attekinto'))[1]-290)*80000/287);
	terkep_csusztat(Math.round(terkep_x-x/zoom),Math.round(terkep_y-y/zoom));
	toggle('terkep_attekinto');
	frissit_terkep();
	return false;
};
function fullscreen_terkep_klikk(e) {
	if (!e) var e = window.event;
	var kur=getPosition(e);
	var ft_zoom=parseInt(document.getElementById('fullscreen_terkep_zoom').value);
	var x1=parseInt(document.getElementById('fullscreen_terkep_x').value)-ft_zoom/2;
	var y1=parseInt(document.getElementById('fullscreen_terkep_y').value)-ft_zoom/2;
	var x=Math.round(((kur.x-findPos(document.getElementById('fullscreen_terkep_div'))[0])/800*ft_zoom+x1)*2);
	var y=Math.round(((kur.y-findPos(document.getElementById('fullscreen_terkep_div'))[1])/800*ft_zoom+y1)*2);
	terkep_csusztat(Math.round(terkep_x-x/zoom),Math.round(terkep_y-y/zoom));
	toggle('fullscreen_terkep');
	frissit_terkep();
	return false;
};
function refresh_fullscreen_terkep() {
	if (parseInt(document.getElementById('fullscreen_terkep_zoom').value)>1000) document.getElementById('fullscreen_terkep_zoom_in_ikon').src='img/ikonok/add.gif';else document.getElementById('fullscreen_terkep_zoom_in_ikon').src='img/ikonok/add-ff.gif';
	if (parseInt(document.getElementById('fullscreen_terkep_zoom').value)<128000) document.getElementById('fullscreen_terkep_zoom_out_ikon').src='img/ikonok/delete.gif';else document.getElementById('fullscreen_terkep_zoom_out_ikon').src='img/ikonok/delete-ff.gif';
	var x='';
	for(var i=1;i<=5;i++) x+='&psz'+i+'n='+encodeURIComponent(document.getElementById('fullscreen_terkep_par_psz'+i+'n').value)+'&psz'+i+'sz='+encodeURIComponent(document.getElementById('fullscreen_terkep_par_psz'+i+'sz').value);
	document.getElementById('fullscreen_terkep_img').src='minimap_v2.php?'+
	'x='+parseInt(document.getElementById('fullscreen_terkep_x').value)+'&y='+parseInt(document.getElementById('fullscreen_terkep_y').value)+'&zoom='+parseInt(document.getElementById('fullscreen_terkep_zoom').value)+
	'&asz='+document.getElementById('fullscreen_terkep_par_asz').options[document.getElementById('fullscreen_terkep_par_asz').selectedIndex].value+
	'&kbm='+document.getElementById('fullscreen_terkep_par_kbm').options[document.getElementById('fullscreen_terkep_par_kbm').selectedIndex].value+
	'&nbm='+document.getElementById('fullscreen_terkep_par_nbm').options[document.getElementById('fullscreen_terkep_par_nbm').selectedIndex].value+
	'&pbm='+document.getElementById('fullscreen_terkep_par_pbm').options[document.getElementById('fullscreen_terkep_par_pbm').selectedIndex].value+
	'&bn='+document.getElementById('fullscreen_terkep_par_bn').options[document.getElementById('fullscreen_terkep_par_bn').selectedIndex].value+
	'&ter='+document.getElementById('fullscreen_terkep_par_ter').options[document.getElementById('fullscreen_terkep_par_ter').selectedIndex].value+
	'&flottak='+document.getElementById('fullscreen_terkep_par_flottak').options[document.getElementById('fullscreen_terkep_par_flottak').selectedIndex].value+
	x+'&rnd='+Math.random();
	return false;
};
function szinkocka_nyit(melyik) {
	for(var i=1;i<=5;i++) {
		if (i==melyik) toggle('fullscreen_terkep_par_psz'+i+'kocka');
		else document.getElementById('fullscreen_terkep_par_psz'+i+'kocka').style.display='none';
	}
	return false;
};
function szinkocka_klikk(e,i) {
	if (!e) var e = window.event;
	var kur=getPosition(e);
	var x=Math.floor((kur.x-findPos(document.getElementById('fullscreen_terkep_par_psz'+i+'kocka'))[0])/10);
	var y=Math.floor((kur.y-findPos(document.getElementById('fullscreen_terkep_par_psz'+i+'kocka'))[1])/10);
	var rgb=szinkocka[18*y+x];
	document.getElementById('fullscreen_terkep_par_psz'+i+'sz').value=dec2hex(rgb[0])+dec2hex(rgb[1])+dec2hex(rgb[2]);
	document.getElementById('fullscreen_terkep_par_psz'+i+'n').style.backgroundColor='rgb('+rgb[0]+','+rgb[1]+','+rgb[2]+')';
	if (rgb[0]+rgb[1]+rgb[2]<255) document.getElementById('fullscreen_terkep_par_psz'+i+'n').style.color='rgb(255,255,255)';
	else document.getElementById('fullscreen_terkep_par_psz'+i+'n').style.color='rgb(0,0,0)';
	return toggle('fullscreen_terkep_par_psz'+i+'kocka');
};
function dec2hex(x) {
	var j1=Math.floor(x/16);
	var j2=x-16*j1;
	return (j1<10?j1:String.fromCharCode(55+j1))+''+(j2<10?j2:String.fromCharCode(55+j2));
};
