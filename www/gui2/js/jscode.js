var terkep_kozeppont = new Array();
var terkep_zoom = 1;
var terkep_bolygo_ikon_meret = 32;
var terkep_hexa_x_meret_default = 286;
var terkep_hexa_y_meret_default = 248;
var terkep_hexa_x_meret = terkep_hexa_x_meret_default;
var terkep_hexa_y_meret = terkep_hexa_y_meret_default;
var terkep_hexa_keret_x_meret = 290;
var terkep_hexa_keret_y_meret = 252;
var terkep_hexa_keret_vastagsag = 4;
var terkep_meretek = new Array();
var terkep_drag = false;
var terkep_drag_tav = 0;
var terkep_drag_start = new Array();
var terkep_drag_start_px = new Array();

var aktiv_bolygo_id = 0;
var nyitott_bolygo_id = 0;
var aktiv_flotta = null;
var bolygo_osztalyok = ['a','b','c','d','e'];
var bolygo_osztalyok_nagy = ['A','B','C','D','E'];
var loading_timeout = null;

$(document).ready(function() {
	//
	/*$(document).ajaxError(function(event,request,settings,thrownerror) {
        console.log('request: '+request);
        console.log('settings: '+settings);
        console.log('error: '+thrownerror);
	});*/
	//scrollbarok
	$('#bolygo_lista').niceScroll({cursoropacitymin:1,cursorborder:'',cursorborderradius:'5px',cursorwidth:10,cursorcolor:'rgb(27,62,68)',railoffset:{top:0,left:-2},railpaddingv2:5});
    $('#flotta_lista').niceScroll({cursoropacitymin:1,cursorborder:'',cursorborderradius:'5px',cursorwidth:10,cursorcolor:'rgb(27,62,68)',railoffset:{top:0,left:-2},railpaddingv2:5});
    $('#bolygo_profil').children('.ablak_tartalom').niceScroll({cursoropacitymin:1,cursorborder:'',cursorborderradius:'5px',cursorwidth:10,cursorcolor:'rgb(27,62,68)',railoffset:{top:0,left:-2},railpaddingv2:5});
	//$('.ablak_tartalom').niceScroll({cursoropacitymin:1,cursorborder:'',cursorwidth:10,cursorcolor:'rgb(27,62,68)'});
	//terkep
	terkep_kozeppont=[0,0];
	terkep_meretek=[$('#terkep').width(),$('#terkep').height()];
	terkep_drag_start=[0,0];
	terkep_drag_start_px=[0,0];
	//adatok betoltese
	kezdokoordi();//elso terkepbetoltes
	//esemenykezelok
	$('#terkep_eger_reteg').mousedown(function(event) {
		terkep_drag = true;
		$('#terkep').addClass('unselectable');
		$('.menu').addClass('unselectable');
		$('.ablak').addClass('unselectable');
		$('#terkep_eger_reteg').addClass('grabbing');
		terkep_drag_tav = 0;
		terkep_drag_start=[terkep_kozeppont[0]+event.pageX*terkep_zoom,terkep_kozeppont[1]+event.pageY*terkep_zoom];
		terkep_drag_start_px=[event.pageX,event.pageY];
	});
	$(document).mouseup(function(event) {
		$('#terkep').removeClass('unselectable');
		$('.menu').removeClass('unselectable');
		$('.ablak').removeClass('unselectable');
		if (terkep_drag_tav<5) {//kattintas volt
			var t = $(event.target);
			if (t.closest('#terkep').length == 1) {//a terkepen
				var katt_x=Math.round(terkep_kozeppont[0]+event.pageX*terkep_zoom-terkep_meretek[0]/2*terkep_zoom);
				var katt_y=Math.round(terkep_kozeppont[1]+event.pageY*terkep_zoom-terkep_meretek[1]/2*terkep_zoom);
				var s='';
				if (t.hasClass('bolygo_klikk_terulet')) {
					t=t.closest('li');
					$('#aktiv_flotta').html('<b>BOLYGÓ</b><br /><b>Név:</b> '+t.data('d').nev+'<br /><b>Pozíció:</b> '+koordinata_szepito(t.data('d').x,t.data('d').y));
					aktiv_flotta=t.data('d');
					aktiv_bolygo_id=t.data('d').id;
					s+='<div class="context_menu_item" id="context_menu_item_a">A</div>';
					s+='<div class="context_menu_item" id="context_menu_item_b">B</div>';
					s+='<div class="context_menu_item" id="context_menu_item_e">E</div>';
					s+='<div class="context_menu_item" id="context_menu_item_f">F</div>';
					$('#context_menu').html(s);
					$('.context_menu_item').click(function(event) {
						$('#context_menu').hide();
						$('#context_menu').html('');
						if (event.target.id=='context_menu_item_a') {
							nyitott_bolygo_id=aktiv_bolygo_id;
							frissit_bolygo();
						}
						$('#aktiv_flotta').html('');
					});
				} else if (t.closest('#terkep_flottak').length == 1) {
					t=t.closest('li');
					$('#aktiv_flotta').html('<b>FLOTTA</b><br /><b>Név:</b> '+t.data('d').nev+'<br /><b>Pozíció:</b> '+koordinata_szepito(t.data('d').x,t.data('d').y));
					aktiv_flotta=t.data('d');
					s+='<div class="context_menu_item" id="context_menu_item_a">A</div>';
					s+='<div class="context_menu_item" id="context_menu_item_b">B</div>';
					s+='<div class="context_menu_item" id="context_menu_item_c">C</div>';
					s+='<div class="context_menu_item" id="context_menu_item_d">D</div>';
					s+='<div class="context_menu_item" id="context_menu_item_e">E</div>';
					s+='<div class="context_menu_item" id="context_menu_item_f">F</div>';
					s+='<div class="context_menu_item" id="context_menu_item_g">G</div>';
					$('#context_menu').html(s);
					$('.context_menu_item').click(function(event) {
						$('#context_menu').hide();
						$('#context_menu').html('');
						if (event.target.id=='context_menu_item_a') {
							$('#kivalasztott_flotta').html($('#aktiv_flotta').html());
						}
						$('#aktiv_flotta').html('');
					});
				} else if (t.closest('#terkep_hexak').length == 1) {
					t=t.closest('li');
					$('#aktiv_flotta').html('<b>VILÁGŰR</b><br /><b>Pozíció:</b> '+koordinata_szepito(katt_x,katt_y)+'<br /><b>Bolygó:</b> '+t.data('d').voronoi_nev+'<br /><b>Diplo:</b> '+(t.data('d').szin==1?'saját':(t.data('d').szin==2?'szövi':(t.data('d').szin==3?'testvér':(t.data('d').szin==4?'hadi állapot':'semleges')))));
					s+='<div class="context_menu_item" style="left:  0px;top:  0px" id="context_menu_item_a"><span style="font-size: 10pt">'+koordinata_szepito(katt_x,katt_y)+'</span></div>';
					$('#context_menu').html(s);
					$('.context_menu_item').click(function(event) {
						$('#context_menu').hide();
						$('#context_menu').html('');
						if (event.target.id=='context_menu_item_a') {
						}
						$('#aktiv_flotta').html('');
					});
				} else {
					$('#aktiv_flotta').html('<b>VILÁGŰR</b><br /><b>Pozíció:</b> '+koordinata_szepito(katt_x,katt_y)+'<br /><b>Diplo:</b>: NPC vagy MNT');
					s+='<div class="context_menu_item" style="left:  0px;top:  0px" id="context_menu_item_a"><span style="font-size: 10pt">'+koordinata_szepito(katt_x,katt_y)+'</span></div>';
					$('#context_menu').html(s);
					$('.context_menu_item').click(function(event) {
						$('#context_menu').hide();
						$('#context_menu').html('');
						if (event.target.id=='context_menu_item_a') {
						}
						$('#aktiv_flotta').html('');
					});
				}
				$('#context_menu').css({
					'display': 'block'
					,'left': (event.pageX-32)+'px'
					,'top': (event.pageY-32)+'px'
				});
			}
		}
		if (terkep_drag) {
			terkep_drag = false;
			$('#terkep_eger_reteg').removeClass('grabbing');
			if (terkep_drag_tav>=5) frissit_terkep();
		}
	});
	$(document).mousemove(function(event) {
		if (terkep_drag) {
			terkep_drag_tav+=Math.abs(terkep_drag_start_px[0]-event.pageX)+Math.abs(terkep_drag_start_px[1]-event.pageY);
			terkep_kozeppont[0]=terkep_drag_start[0]-event.pageX*terkep_zoom;
			terkep_kozeppont[1]=terkep_drag_start[1]-event.pageY*terkep_zoom;
			terkep_ujrarajzolas();
			$('#context_menu').hide();
			$('#context_menu').html('');
			$('#aktiv_flotta').html('');
		}
	});
	$('#terkep_eger_reteg').mousewheel(function(event, delta, deltaX, deltaY) {
		if (delta<0) {
			if (terkep_zoom<8) {
				terkep_zoom=2*terkep_zoom;
				frissit_terkep();
			}
		}
		if (delta>0) {
			if (terkep_zoom>1) {
				terkep_zoom=terkep_zoom/2;
				frissit_terkep();
			}
		}
		terkep_bolygo_ikon_meret=(terkep_zoom==1?32:(terkep_zoom==2?24:16));
		terkep_hexa_x_meret=Math.round(terkep_hexa_x_meret_default/terkep_zoom);
		terkep_hexa_y_meret=Math.round(terkep_hexa_y_meret_default/terkep_zoom);
	});
	$('#miniterkep_belso').mousewheel(function(event, delta, deltaX, deltaY) {
		if (delta<0) {
			if (terkep_zoom<8) {
				terkep_zoom=2*terkep_zoom;
				frissit_terkep();
			}
		}
		if (delta>0) {
			if (terkep_zoom>1) {
				terkep_zoom=terkep_zoom/2;
				frissit_terkep();
			}
		}
		terkep_bolygo_ikon_meret=(terkep_zoom==1?32:(terkep_zoom==2?24:16));
		terkep_hexa_x_meret=Math.round(terkep_hexa_x_meret_default/terkep_zoom);
		terkep_hexa_y_meret=Math.round(terkep_hexa_y_meret_default/terkep_zoom);
	});
	document.onselectstart = function() {//for ie?
		return !terkep_drag;
	};
	$(window).resize(function() {
		terkep_meretek=[$('#terkep').width(),$('#terkep').height()];
		terkep_ujrarajzolas();
	});
	$('#miniterkep_belso').click(function(event) {
		var offset = $('#miniterkep_belso').offset();
		terkep_kozeppont[0]=Math.round(((event.pageX-offset.left)/$('#miniterkep_belso').width()-0.5)*160000);
		terkep_kozeppont[1]=Math.round(((event.pageY-offset.top)/$('#miniterkep_belso').height()-0.5)*160000);
		frissit_terkep();
	});
	//
	$(document).scroll(function() {
		$(document).scrollTop(0);
		$(document).scrollLeft(0);
	});
	//keyboard
	$(document).keyup(function(event) {
		if (event.which == 27) {//esc
			$('.ablak').each(function(index) {
				if ($(this).offset().left>0) {
					var t=$(this);
					t.children('.ablak_tartalom').getNiceScroll().hide();
					t.animate({
						left: '-'+t.outerWidth()+'px'
					},'fast',function() {
						t.children('.ablak_tartalom').getNiceScroll().remove();
						t.hide();
					});
				}
			});
			//event.preventDefault();
		}
	});
	//windows
	$('.ablak').children('.ablak_fejlec').children('.close_icon').click(function(event) {
		var t=$(event.target).closest('.ablak');
		t.children('.ablak_tartalom').getNiceScroll().hide();
		t.animate({
			left: '-'+t.outerWidth()+'px'
		},'fast',function() {
			t.children('.ablak_tartalom').getNiceScroll().remove();
			t.hide();
		});
	});
	//tabs
	$(document).delegate('.tab_list','click',function(event) {
		var tabs=$(event.target).closest('ul');
		var tabs_id=tabs.attr('id').substr(9);
		$('#'+tabs_id).children('.tab_content').hide();
		$('#'+tabs_id).children('.tab_content').removeClass('active_tab_content');
		tabs.find('li').removeClass('active_tab');
		//
		var tab=$(event.target);
		var tab_id=tab.attr('id').substr(8);
		$('#'+tab_id).show();
		$('#'+tab_id).addClass('active_tab_content');
		$('#tab_for_'+tab_id).addClass('active_tab');
		//
		$('#bolygo_profil').children('.ablak_tartalom').getNiceScroll().onResize();
	});
	//tooltips
	$(document).delegate('.tooltipped','mousemove',function(event) {
		var t=$(event.target).closest('.tooltipped');
		if (t.attr('id')) if ($('#tooltip_for_'+t.attr('id'))) if ($('#tooltip_for_'+t.attr('id')).html()) if ($('#tooltip').css('display')!='block') {
			$('#tooltip').html($('#tooltip_for_'+t.attr('id')).html());
			//var x=Math.round(event.pageX-$('#tooltip').outerWidth()/2);
			//var y=event.pageY+1;
			//
			//var x=Math.round(t.offset().left+10);
			//var y=Math.round(t.offset().top+t.outerHeight());
			//
			var x=Math.round(t.offset().left+t.outerWidth()/2);
			var y=Math.round(t.offset().top+t.outerHeight()-5);
			//
			//var x=Math.round(t.offset().left+t.outerWidth()/2-$('#tooltip').outerWidth()/2);
			//var y=Math.round(t.offset().top+t.outerHeight()-5);
			//
			//var x=Math.round(event.pageX-$('#tooltip').outerWidth()/2);
			//if (x<$('#bal_menu').outerWidth()+5) x=$('#bal_menu').outerWidth()+5;
			//if (x+$('#tooltip').outerWidth()>$('body').width()-$('#jobb_menu').outerWidth()-5) x=$('body').width()-$('#jobb_menu').outerWidth()-5-$('#tooltip').outerWidth();
			//var y=event.pageY-10-$('#tooltip').outerHeight();
			//if (y<$('#felso_menu').height()+5) y=event.pageY+10+16;
			$('#tooltip').css({
				'display': 'block'
				,'left': x+'px'
				,'top': y+'px'
			});
		}
	});
	$(document).delegate('.tooltipped','mouseleave',function(event) {
		if (event.pageX>=$('#tooltip').offset().left-5)
		if (event.pageX<=$('#tooltip').offset().left+$('#tooltip').outerWidth()+5)
		if (event.pageY>=$('#tooltip').offset().top-5)
		if (event.pageY<=$('#tooltip').offset().top+$('#tooltip').outerHeight()+5)
		return false;
		$('#tooltip').hide();
		$('#tooltip').html('');
	});
	$('#tooltip').mouseleave(function(event) {
		$('#tooltip').hide();
		$('#tooltip').html('');
	});
	//disable image dragging
	$(document).delegate('img','mousedown',function() {return false;});
	$(document).delegate('#terkep_hexak li','mousedown',function() {return false;});
	$(document).delegate('#terkep_bolygok li','mousedown',function() {return false;});
	$(document).delegate('#terkep_flottak li','mousedown',function() {return false;});
});
function terkep_ujrarajzolas() {
	$('#terkep').css('backgroundPosition',Math.round((terkep_meretek[0]/2-terkep_kozeppont[0]/terkep_zoom)/16*terkep_zoom)+'px '+Math.round((terkep_meretek[1]/2-terkep_kozeppont[1]/terkep_zoom)/16*terkep_zoom)+'px');
	$('#terkep_hexak').children().each(function(index) {
		$(this).css({
			'left': Math.round(terkep_meretek[0]/2-terkep_kozeppont[0]/terkep_zoom+$(this).data('d').x/terkep_zoom-terkep_hexa_x_meret/2)+'px'
			,'top': Math.round(terkep_meretek[1]/2-terkep_kozeppont[1]/terkep_zoom+$(this).data('d').y/terkep_zoom-terkep_hexa_y_meret/2)+'px'
		});
	});
	$('#terkep_bolygok').children().each(function(index) {
		$(this).css({
			'left': Math.round(terkep_meretek[0]/2-terkep_kozeppont[0]/terkep_zoom+$(this).data('d').x/terkep_zoom-terkep_bolygo_ikon_meret/2)+'px'
			,'top': Math.round(terkep_meretek[1]/2-terkep_kozeppont[1]/terkep_zoom+$(this).data('d').y/terkep_zoom-terkep_bolygo_ikon_meret/2)+'px'
		});
	});
	$('#terkep_flottak').children().each(function(index) {
		$(this).css({
			'left': Math.round(terkep_meretek[0]/2-terkep_kozeppont[0]/terkep_zoom+$(this).data('d').x/terkep_zoom-8)+'px'
			,'top': Math.round(terkep_meretek[1]/2-terkep_kozeppont[1]/terkep_zoom+$(this).data('d').y/terkep_zoom-8)+'px'
		});
	});
	//
	$('#miniterkep_koordi').html(koordinata_szepito(terkep_kozeppont[0],terkep_kozeppont[1]));
	$('#miniterkep_jelolo').css({
		'left': Math.round((0.5+(terkep_kozeppont[0]-terkep_meretek[0]/2*terkep_zoom)/160000)*$('#miniterkep_belso').width())+'px'
		,'right': Math.round((0.5-(terkep_kozeppont[0]+terkep_meretek[0]/2*terkep_zoom)/160000)*$('#miniterkep_belso').width())+'px'
		,'top': Math.round((0.5+(terkep_kozeppont[1]-terkep_meretek[1]/2*terkep_zoom)/160000)*$('#miniterkep_belso').height())+'px'
		,'bottom': Math.round((0.5-(terkep_kozeppont[1]+terkep_meretek[1]/2*terkep_zoom)/160000)*$('#miniterkep_belso').height())+'px'
	});
	$('#miniterkep_jelolo_x').css({
		'left': Math.round((0.5+(terkep_kozeppont[0])/160000)*$('#miniterkep_belso').width())+'px'
	});
	$('#miniterkep_jelolo_y').css({
		'top': Math.round((0.5+(terkep_kozeppont[1])/160000)*$('#miniterkep_belso').height())+'px'
	});
	return false;
};
function start_loading() {
	loading_timeout=setTimeout(function(){$('#loading').show();},500);
	return false;
};
function stop_loading() {
	clearTimeout(loading_timeout);
	$('#loading').hide();
	return false;
};
function kezdokoordi() {
	start_loading();
	$.getJSON('kezdokoordi.php',{
	},function(data) {
		stop_loading();
		//
		terkep_kozeppont=[data.kezdokoordi[0],data.kezdokoordi[1]];
		frissit_terkep();
	});
	return false;
};
function frissit_terkep() {
	start_loading();
	$.getJSON('terkep_adatok.php',{
		'minx': Math.round(terkep_kozeppont[0]-terkep_meretek[0]*(terkep_zoom+1)/2)
		,'maxx': Math.round(terkep_kozeppont[0]+terkep_meretek[0]*(terkep_zoom+1)/2)
		,'miny': Math.round(terkep_kozeppont[1]-terkep_meretek[1]*(terkep_zoom+1)/2)
		,'maxy': Math.round(terkep_kozeppont[1]+terkep_meretek[1]*(terkep_zoom+1)/2)
	},function(data) {
		stop_loading();
		//hexak
		$('#terkep_hexak').children().each(function(index) {
			$(this).data('d').torlendo=true;
		});
		$.each(data.hexak,function(index, value) {
			if ($('#terkep_hexa_'+value.hexa_x+'_'+value.hexa_y).length) {//frissit
			} else {//uj
				$('#terkep_hexak').append('<li id="terkep_hexa_'+value.hexa_x+'_'+value.hexa_y+'"><div class="keret_0"></div><div class="keret_1"></div><div class="keret_2"></div><div class="keret_3"></div><div class="keret_4"></div><div class="keret_5"></div></li>');
				//$('#terkep_hexak').append('<li id="terkep_hexa_'+value.hexa_x+'_'+value.hexa_y+'"></li>');
			}
			$('#terkep_hexa_'+value.hexa_x+'_'+value.hexa_y).css({
				'background-image': 'url(img/terkep_hexa_'+(value.szin==1?'sajat':(value.szin==2?'szovi':(value.szin==3?'testver':(value.szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'width': terkep_hexa_x_meret+'px'
				,'height': terkep_hexa_y_meret+'px'
			});
			$('#terkep_hexa_'+value.hexa_x+'_'+value.hexa_y).data('d',{
				'torlendo': false
				,'x': parseInt(value.x)
				,'y': parseInt(value.y)
				,'szin': parseInt(value.szin)
				,'hexa_x': parseInt(value.hexa_x)
				,'hexa_y': parseInt(value.hexa_y)
				,'voronoi': parseInt(value.voronoi)
				,'voronoi_nev': value.voronoi_nev
			});
		});
		$('#terkep_hexak').children().each(function(index) {
			if ($(this).data('d').torlendo) $(this).remove();
		});
		//hexakeretek
		$('#terkep_hexak').children().each(function(index) {
			var jobb_also=$('#terkep_hexa_'+($(this).data('d').hexa_x+1)+'_'+($(this).data('d').hexa_y+1-Math.abs($(this).data('d').hexa_x%2)));
			if (!jobb_also || !jobb_also.data('d') || $(this).data('d').voronoi!=jobb_also.data('d').voronoi)
			$(this).children('.keret_0').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -0px'
			});else $(this).children('.keret_0').css({'background-image': 'none'});
			var also=$('#terkep_hexa_'+$(this).data('d').hexa_x+'_'+($(this).data('d').hexa_y+1));
			if (!also || !also.data('d') || $(this).data('d').voronoi!=also.data('d').voronoi)
			$(this).children('.keret_1').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -'+(terkep_hexa_keret_y_meret)+'px'
			});else $(this).children('.keret_1').css({'background-image': 'none'});
			var bal_also=$('#terkep_hexa_'+($(this).data('d').hexa_x-1)+'_'+($(this).data('d').hexa_y+1-Math.abs($(this).data('d').hexa_x%2)));
			if (!bal_also || !bal_also.data('d') || $(this).data('d').voronoi!=bal_also.data('d').voronoi)
			$(this).children('.keret_2').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -'+(2*terkep_hexa_keret_y_meret)+'px'
			});else $(this).children('.keret_2').css({'background-image': 'none'});
			//
			var bal_felso=$('#terkep_hexa_'+($(this).data('d').hexa_x-1)+'_'+($(this).data('d').hexa_y-Math.abs($(this).data('d').hexa_x%2)));
			if (!bal_felso || !bal_felso.data('d') || $(this).data('d').voronoi!=bal_felso.data('d').voronoi)
			$(this).children('.keret_3').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -'+(3*terkep_hexa_keret_y_meret)+'px'
			});else $(this).children('.keret_3').css({'background-image': 'none'});
			var felso=$('#terkep_hexa_'+$(this).data('d').hexa_x+'_'+($(this).data('d').hexa_y-1));
			if (!felso || !felso.data('d') || $(this).data('d').voronoi!=felso.data('d').voronoi)
			$(this).children('.keret_4').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -'+(4*terkep_hexa_keret_y_meret)+'px'
			});else $(this).children('.keret_4').css({'background-image': 'none'});
			var jobb_felso=$('#terkep_hexa_'+($(this).data('d').hexa_x+1)+'_'+($(this).data('d').hexa_y-Math.abs($(this).data('d').hexa_x%2)));
			if (!jobb_felso || !jobb_felso.data('d') || $(this).data('d').voronoi!=jobb_felso.data('d').voronoi)
			$(this).children('.keret_5').css({
				'background-image': 'url(img/terkep_hexa_keret_'+($(this).data('d').szin==1?'sajat':($(this).data('d').szin==2?'szovi':($(this).data('d').szin==3?'testver':($(this).data('d').szin==4?'hadi':'piros'))))+(terkep_zoom>1?('_'+terkep_zoom):'')+'.png)'
				,'background-position': '-'+(terkep_hexa_keret_vastagsag*terkep_hexa_keret_x_meret)+'px -'+(5*terkep_hexa_keret_y_meret)+'px'
			});else $(this).children('.keret_5').css({'background-image': 'none'});
		});
		//bolygok
		$('#terkep_bolygok').children().each(function(index) {
			$(this).data('d').torlendo=true;
		});
		$.each(data.bolygok,function(index, value) {
			if ($('#terkep_bolygo_'+value[0]).length) {//frissit
				$('#terkep_bolygo_'+value[0]).html('<img src="img/bolygoikonok/bolygo_'+bolygo_osztalyok[value[5]-1]+(terkep_zoom==1?'32':(terkep_zoom==2?'24':''))+'.gif" class="bolygo_klikk_terulet" /><br /><span class="bolygo_klikk_terulet">'+value[3]+'</span>');
			} else {//uj
				$('#terkep_bolygok').append('<li id="terkep_bolygo_'+value[0]+'"><img src="img/bolygoikonok/bolygo_'+bolygo_osztalyok[value[5]-1]+(terkep_zoom==1?'32':(terkep_zoom==2?'24':''))+'.gif" class="bolygo_klikk_terulet" /><br /><span class="bolygo_klikk_terulet">'+value[3]+'</span></li>');
			}
			$('#terkep_bolygo_'+value[0]).data('d',{
				'torlendo': false
				,'id': value[0]
				,'x': value[1]
				,'y': value[2]
				,'nev': value[3]
				,'osztaly': value[5]
			});
		});
		$('#terkep_bolygok').children().each(function(index) {
			if ($(this).data('d').torlendo) $(this).remove();
		});
		var s='';
		s+='<ul>';
		$('#terkep_bolygok').children().each(function(index) {
			s+='<li style="background: url(img/bolygoikonok/bolygo_'+bolygo_osztalyok[$(this).data('d').osztaly-1]+'.gif) no-repeat 0px 1px"><a href="#" onclick="return bolygo_katt('+$(this).data('d').id+')">'+$(this).data('d').nev+'</a></li>';
		});
		s+='</ul>';
		$('#bolygo_lista').html(s);
		$('#bolygo_lista').getNiceScroll().onResize();
		//flottak
		$('#terkep_flottak').children().each(function(index) {
			$(this).data('d').torlendo=true;
		});
		$.each(data.flottak,function(index, value) {
			if ($('#terkep_flotta_'+value.id).length) {//frissit
				$('#terkep_flotta_'+value.id).html('<img src="img/flottaikonok/flotta_ikon_16x16_npc.gif" />');
			} else {//uj
				$('#terkep_flottak').append('<li id="terkep_flotta_'+value.id+'"><img src="img/flottaikonok/flotta_ikon_16x16_npc.gif" /></li>');
			}
			$('#terkep_flotta_'+value.id).data('d',{
				'torlendo': false
				,'id': value.id
				,'x': value.x
				,'y': value.y
				,'nev': value.nev
			});
		});
		$('#terkep_flottak').children().each(function(index) {
			if ($(this).data('d').torlendo) $(this).remove();
		});
		//
		terkep_ujrarajzolas();
	});
	return false;
};
function frissit_bolygo() {
	start_loading();
	ablak_nyit('#bolygo_profil');
	$.getJSON('bolygo_adatok.php',{
		'id': nyitott_bolygo_id
	},function(data) {
		stop_loading();
		//
		$('#bolygo_profil').children('.ablak_fejlec').children('.caption').html(data.nev+' :: <a href="#" onclick="return false">'+data.tulaj_nev+'</a>'+(data.tulaj_szov>0?(' <span style="font-size:8pt">(<a href="#" onclick="return false">'+data.szovetseg_nev+'</a>)</span>'):'')+' @ '+koordinata_szepito(data.x,data.y)+' ('+data.regio+')');
		//
		if (data.tied) {//sajat bolygo
			$('#bolygo_adat_kov_termeles').html('12 perc múlva');
			$('#bolygo_adat_moral').html('100%');
			$('#bolygo_adat_vedelmi_bonusz').html('1000 pont');
			$('#bolygo_adat_foglalhato').html('nem/nem');
			$('#bolygo_adat_szabotalhato').html('nem');
			//gyarak
			$('#bolygo_gyarak_tabla').find('.bolygo_gyar_tabla_sor').each(function(index) {
				var id=$(this).attr('id').substr(12);
				if (data.gyarak[id]) {
					$(this).show();
					$(this).children('.gyar_aktiv').html(ezresito(data.gyarak[id][0]));
				} else {
					$(this).hide();
				}
			});
			$('#bolygo_gyarak_tabla').show();
			//eroforrasok
			$('#bolygo_eroforras_lakohely').find('.eroforras_keszlet').html('most');
			$('#bolygo_eroforras_lakohely').find('.eroforras_delta').html('+/-');
			$('#bolygo_eroforras_lakohely').find('.eroforras_netto').html('jövőben');
			//
			$('#bolygo_eroforras_nepesseg').find('.eroforras_keszlet').html('most');
			$('#bolygo_eroforras_nepesseg').find('.eroforras_delta').html('+/-');
			$('#bolygo_eroforras_nepesseg').find('.eroforras_netto').html('jövőben');
			//
			$('#bolygo_eroforras_munkaero').find('.eroforras_keszlet').html('készlet');
			$('#bolygo_eroforras_munkaero').find('.eroforras_delta').html('igény');
			$('#bolygo_eroforras_munkaero').find('.eroforras_netto').html('szabad');
			//
			$('#bolygo_eroforras_kepzett_munkaero').find('.eroforras_keszlet').html('készlet');
			$('#bolygo_eroforras_kepzett_munkaero').find('.eroforras_delta').html('igény');
			$('#bolygo_eroforras_kepzett_munkaero').find('.eroforras_netto').html('szabad');
			//
			$('#bolygo_eroforrasok_tabla').find('.bolygo_eroforras_tabla_sor').each(function(index) {
				var id=$(this).attr('id').substr(17);
				if (data.eroforrasok[id] && parseInt(data.eroforrasok[id])>0 && (parseInt(data.eroforrasok[id][0])!=0 || parseInt(data.eroforrasok[id][1])!=0)) {
					$(this).show();
					$(this).children('.eroforras_keszlet').html(ezresito(data.eroforrasok[id][0]));
					$(this).children('.eroforras_delta').html(ezresito(data.eroforrasok[id][1]));
				} else {
					$(this).hide();
				}
			});
			$('#bolygo_nyers_eroforrasok_tabla').find('.bolygo_eroforras_tabla_sor').each(function(index) {
				var id=$(this).attr('id').substr(17);
				if (data.eroforrasok[id] && parseInt(data.eroforrasok[id])>0 && parseInt(data.eroforrasok[id][1])!=0) {
					$(this).show();
					$(this).children('.eroforras_keszlet').html(ezresito(data.eroforrasok[id][0]));
					$(this).children('.eroforras_delta').html(ezresito(data.eroforrasok[id][1]));
				} else {
					$(this).hide();
				}
			});
			$('#bolygo_specialis_eroforrasok_tabla').show();
			$('#bolygo_eroforrasok_tabla').show();
			$('#bolygo_nyers_eroforrasok_tabla').show();
			//speckok
			$('#bolygo_adat_beepitett').html('145 630 km² (7%)');
			$('#bolygo_adat_effektiv_beepitett').html('145 630 km² (7%)');
			$('#bolygo_adat_szabad').html('1 854 370 km² (93%)');
			$('#bolygo_adat_labnyom').html('100,00%');
		} else {//idegen bolygo
			$('#bolygo_adat_kov_termeles').html('12 perc múlva');
			$('#bolygo_adat_moral').html('100%');
			$('#bolygo_adat_vedelmi_bonusz').html('1000 pont');
			$('#bolygo_adat_foglalhato').html('nem/nem');
			$('#bolygo_adat_szabotalhato').html('nem');
			//gyarak
			$('#bolygo_gyarak_tabla').hide();
			//eroforrasok
			$('#bolygo_specialis_eroforrasok_tabla').hide();
			$('#bolygo_eroforrasok_tabla').hide();
			$('#bolygo_nyers_eroforrasok_tabla').hide();
			//speckok
			$('#bolygo_adat_beepitett').html('?');
			$('#bolygo_adat_effektiv_beepitett').html('?');
			$('#bolygo_adat_szabad').html('?');
			$('#bolygo_adat_labnyom').html('?');
		}
	});
	return false;
};
function bolygo_katt(id) {
	nyitott_bolygo_id=id;frissit_bolygo();
	return false;
};
function ablak_nyit(x) {
	if ($(x).css('display')=='none') {
		$(x).children('.ablak_tartalom').niceScroll({cursoropacitymin:1,cursorborder:'',cursorborderradius:'5px',cursorwidth:10,cursorcolor:'rgb(27,62,68)',railoffset:{top:0,left:-2},railpaddingv2:5});
		$(x).show();
		$(x).children('.ablak_tartalom').getNiceScroll().onResize();
		$(x).animate({
			left: '150px'
		},'fast',function() {
			$(x).children('.ablak_tartalom').getNiceScroll().onResize();
		});
	}
	return false;
};
function ezresito(s) {
	s+='';
	x=s.split('.');
	x1=x[0];
	x2=x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) x1=x1.replace(rgx,'$1'+'&nbsp;'+'$2');
	return x1 + x2;
};
function koordinata_szepito(x,y) {	
	var s='';
	if (y<0) s+='É&nbsp;'+ezresito(Math.round(-y/2));
	else if (y>0) s+='D&nbsp;'+ezresito(Math.round(y/2));
	else s+='0';
	s+=', ';
	if (x<0) s+='Ny&nbsp;'+ezresito(Math.round(-x/2));
	else if (x>0) s+='K&nbsp;'+ezresito(Math.round(x/2));
	else s+='0';
	return s;
};
