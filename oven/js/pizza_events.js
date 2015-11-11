$(function() { 


		
	/* SORT MENU */
	$('#boxlist').sortable({
		//connectWith: ".other_div",
		placeholder: "ui-state-highlight",
	}).disableSelection();
	$('#boxlist').on('sortupdate',function() {
		var sortedIDs = $(this).sortable('toArray');
		//console.log(sortedIDs);
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'sortbx', order: sortedIDs }
		}).done(function( msg ) {
			//console.log(msg);

			//added a category or sorted categories... rebuild all
			rebuild_menu();

		});
	});
	$('#pizzalist').sortable({
		//connectWith: ".other_div",
		placeholder: "ui-state-highlight",
	}).disableSelection();
	$('#pizzalist').on('sortupdate',function() {
		var bx = $('#id').val();
		var sortedIDs = $(this).sortable('toArray');
		//console.log(sortedIDs);
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'sortpi', bx: bx, order: sortedIDs }
		}).done(function( msg ) {
			//console.log(msg);

			//deleted a post, or sorted posts... rebuild the parent category page
			rebuild_all();

		});
	});


	/* EXPAND MOBILE MENU */
	$('#content').on('click', '#nav', function() {
		if ($(this).hasClass('expanded')) {
			$('#nav').removeClass('expanded');
		} else {
			$('#nav').addClass('expanded');
		}
	});


	/* CREATE NEW CATEGORY */
	$(document).on('click', '#newbx', function() {
		var html = '	<li id="e#id#"><span class="title">My Category</span></li>\n';
		var ed = '';
		if ($('#e0').hasClass('disabled')) ed='disabled';
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'newbx' }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);
			html = html.replace(/#id#/g,obj.id);
			html+= '<li id="e0" class="'+ed+'"><span>About</span></li>';
			$('#e0').remove();
			$('#boxlist').append(html);
			$('#boxlist li').removeClass('on');
			//$('#e'+obj.id).addClass('disabled');
			$('#e'+obj.id).addClass('on');
			getBox('e'+obj.id);
			$('#boxlist').trigger('sortupdate');

			//new category... make category page
			rebuild_box(obj.id);

		});
	});
	
	
	/* SET THE POST CATEGORY */
	$(document).on('change', '#setbox', function() {
		var bx = $(this).val();
		var pi = $('#id').val();
		$(this).blur();

		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'setbx', bx:bx, pi:pi }
		}).done(function( msg ) {
			//console.log(msg);

			rebuild_all();
			reset_numbers();
		});
	});
		
	
	/* CREATE NEW PAGE/POST */
	$(document).on('click', '#newpi', function() {
		//var html = '	<li id="e#id#"><span>New Pi</span></li>\n';
		//var ed = '';
		//if ($('#e0').hasClass('disabled')) ed='disabled';
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'newpi' }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);

			getPizza('e'+obj.id);
			//new post... don't need to do anything yet
			
			//set the count on the uncategorized menu
			$('#eX .active').html(obj.unc);
		});
	});
	
	
	/* DISPLAY CATEGORY */
	$(document).on('click', '#boxlist li', function(e) {
		id = $(this).attr('id');
		$('#boxlist2 li').removeClass('on');
		$('#boxlist li').removeClass('on');
		$(this).addClass('on');
		if (id=="e0") {
			getPizza(id);
		} else {
			getBox(id);
		}
	});

	/* DISPLAY UNCATEGORIZED */
	$(document).on('click', '#boxlist2 li', function(e) {
		id = $(this).attr('id');
		$('#boxlist li').removeClass('on');
		$(this).addClass('on');
		getBox('x');
	});
	
	
	/* DISPLAY PAGE/POST */
	$(document).on('click', '#pizzalist li', function(e) {
		id = $(this).attr('id');
		$('#pizzalist li').removeClass('on');
		$(this).addClass('on');
		getPizza(id);
	});
	
	
	/* DELETE IMAGE OR TEXT BLOCK */
	$(document).on('click', '.rem', function(e) {
		var which = $(this).attr('rel');
		var id = $(this).attr('id');
			id = id.replace('rem','');
			
		if (id<1) $('#image').val('');
				
		if (which=="meat") {
			$('#drop'+id).html('<input type="file" id="meat'+id+'" class="droparea spot" name="xfile" data-post="/oven/upload.php" data-type="jpg,jpeg,png,gif" />');
			$('#drop'+id).css('background-image','');
			$('#drop'+id).removeClass('full');
			initDroparea('#meat'+id);
			$.ajax({
				type: "POST",
				url: "/oven/pizzajax.php",
				data: { action: 'remmeat', id: id }
			}).done(function( msg ) {
				//console.log(msg);
				var obj = jQuery.parseJSON(msg);

				//deleted an image... rebuild post page
				rebuild_pizza( $('#id').val() );
				
			});
		}
		
		if (which=="cheese" && chount>1) {
			chount--;
			$('#li'+id).remove();
			var sortedIDs = new Array(); var snum=0;
			$('li.chz').each(function( index ) {
				sortedIDs[snum] = $(this).attr('id'); snum++;
			});
			cheeseOrder(sortedIDs);
	
			$.ajax({
				type: "POST",
				url: "/oven/pizzajax.php",
				data: { action: 'remcheese', id: id }
			}).done(function( msg ) {
				//console.log(msg);
				var obj = jQuery.parseJSON(msg);

				//sorted the text on the page... rebuild post page
				rebuild_pizza( $('#id').val() );

			});
		}
	
	});
	
	
	/* DELETE AND MERGE BLOCKS */
	$(document).on('keydown', '.html5-editor', function(e) {
		if (e.keyCode==8) {
		
			var id = $(this).attr('id');
			var el = document.getElementById(id);
			var position = getCaretCharacterOffsetWithin(el);
	
				id = $(this).attr('rel');
				id = id.replace('ta','');
			var full = $('#drop'+id).hasClass('full');
			if (position<1 && !full) {
				var sortedIDs = new Array(); var snum=0;
				$('li.chz').each(function( index ) {
					sortedIDs[snum] = $(this).attr('id'); snum++;
				});
				//console.log(sortedIDs);
				var index = $('#cheese li.chz').index($('#li'+id));
				if (index>0) {
					var prev = sortedIDs[index-1];
					var html = $(this).html();
					if (html && html!="<br>" ) {
						html = '<div>'+html+'</div>';
						$('#'+prev+' .html5-editor').focus();
						$('#'+prev+' .html5-editor').append('<br>');
						$('#'+prev+' .html5-editor').focusEnd();
						$('#'+prev+' .html5-editor').append(html);
					} else {
						$('#'+prev+' .html5-editor').focus();
						$('#'+prev+' .html5-editor').append('<br>');
						$('#'+prev+' .html5-editor').focusEnd();
					}
					$('#li'+id).remove();
					$.ajax({
						type: "POST",
						url: "/oven/pizzajax.php",
						data: { action: 'remcheese', id: id }
					}).done(function( msg ) {
						//console.log(msg);
						var obj = jQuery.parseJSON(msg);

						//removed a text block... rebuild page
						rebuild_pizza( $('#id').val() );

					});
					sortedIDs = new Array(); snum=0;
					$('li.chz').each(function( index ) {
						sortedIDs[snum] = $(this).attr('id'); snum++;
					});
					cheeseOrder(sortedIDs);
				}
			}
	
		}
	
	});
	
	
	/* ADD NEW TEXT BLOCK ON 5 NEW LINES */
	$(document).on('keyup', '.html5-editor', function(e) {
	
		var tid;
		if ($(this).attr('id'))
			tid = $(this).attr('id');
		else if ($(this).attr('rel'))
			tid = $(this).attr('rel');
		
		if (tid=="xta0") return;
		
		//if (e.keyCode == 13) {  }
	
		var val = $(this).html();
		if ( val.match(/<div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div>/img) && val.match(/<div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div><div>(<.?>)*(<br>)+(<\/.?>)*<\/div>/img).length >= 1 ) {
	
			var lid = tid.replace(/xta/g,'li');
				lid = lid.replace(/ta/g,'li');
	
			var spl1 = val.split(/(<div>(<.?>)*?(<br>)+?(<\/.?>)*?<\/div><div>(<.?>)*?(<br>)+?(<\/.?>)*?<\/div><div>(<.?>)*?(<br>)+?(<\/.?>)*?<\/div><div>(<.?>)*?(<br>)+?(<\/.?>)*?<\/div><div>(<.?>)*?(<br>)+?(<\/.?>)*?<\/div>)/img);
			var first_half = spl1[0];
			var second_half = spl1[spl1.length-1];
	
			//save block one to db with spl1 data
			//console.log(tid+":"+spl1);
			$(this).html(first_half);
			updateText(tid,first_half);
	
			//create block two and save to db with spl2 data
			//console.log(spl2);
			var html = '<li id="liNEW" class="chz"><div id="dropNEW" class="drop">';
				html+= '<input type="file" id="meatNEW" class="droparea spot" name="xfile" data-post="/oven/upload.php" data-type="jpg,jpeg,png,gif" />';
				html+= '</div><textarea class="ta" name="taNEW" id="taNEW">'+second_half+'</textarea>';
				html+= '<span class="rem" rel="cheese" id="remNEW"><p><em>&times;</em> delete text & image</p></span></li>';
			$('#'+lid).after(html);
			$('#taNEW').html5_editor({
				'left-toolbar': false,
				'auto-hide-toolbar': true,
				'fix-toolbar-on-top': true,
				'toolbar-items': [
					[
						['bold', '&#xe600;', 'Bold'],
						['italic', '&#xe601;', 'Italicize'],
						['strike', '&#xe602;', 'Strikethrough'],
					],[
						['link', '&#xe603;', 'Insert Link'],
					],[
						['p', '¶', 'Paragraph'],
						['blockquote', '&#xe606;', 'Blockquote'],
						['ul', '&#xe604;', 'Unordered list'],
						['ol', '&#xe605;', 'Ordered list'],
					],[
						['remove', '⌫', 'Remove Formating'],
					],[
						['custom', '&nbsp; Save &nbsp;', 'Save Changes', function() { $('#'+thisfield).focus();$('#'+thisfield).blur(); } ]
					],
				]
			});
			$('#liNEW .html5-editor').focus();
			parent = $('#id').val();
			$.ajax({
				type: "POST",
				url: "/oven/pizzajax.php",
				data: { action: 'newcheese', parent: parent, text: second_half }
			}).done(function( msg ) {
				//console.log(msg);
				var obj = jQuery.parseJSON(msg);
				$('#liNEW .html5-editor').attr('rel','ta'+obj.id);
				$('#liNEW').attr('id','li'+obj.id);
				$('#remNEW').attr('id','rem'+obj.id);
				$('#taNEW').attr('name','ta'+obj.id);
				$('#taNEW').attr('id','ta'+obj.id);
				$('#xtaNEW').attr('id','xta'+obj.id);
				$('#dropNEW').attr('id','drop'+obj.mid);
				$('#meatNEW').attr('id','meat'+obj.mid);
				var sortedIDs = new Array(); var snum=0;
				$('li.chz').each(function( index ) {
					sortedIDs[snum] = $(this).attr('id'); snum++;
				});
				cheeseOrder(sortedIDs);
	
				initDroparea('#meat'+obj.mid);
				chount++;
			});
	
		}
	});
	
	
	/* STORE TEXT ON ENTRY (SO WE KNOW IF WE NEED TO SAVE) */
	$(document).on('focus', '.html5-editor, .editable', function(event) {
		origtext=$(this).html();
		thisfield=$(this).attr('id');
		if (thisfield=="displayed") {
			div = document.getElementById('displayed');
			window.setTimeout(function() {
				var sel, range;
				if (window.getSelection && document.createRange) {
					range = document.createRange();
					range.selectNodeContents(div);
					sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				} else if (document.body.createTextRange) {
					range = document.body.createTextRange();
					range.moveToElementText(div);
					range.select();
				}
			}, 1);
		}
	});
	
	
	/* IF COMMAND-ENTER, BLUR (SAVE) */
	$(document).on('keydown', '.html5-editor, .editable', function(event) {
		if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
			$(this).blur();
		}
	});
	
	
	/* IF ENTER ON NON-HTML5 FIELDS, BLUR (SAVE) */
	$(document).on('keydown', '.editable', function(event) {
		if (event.keyCode == 13) {
			$(this).blur();
		}
	});
	
	
	/* IF BLUR, SAVE */
	$(document).on('blur', '.editable', function(event) {	
		var data = $(this).html();
		if ($(this).attr('id'))
			var spl = $(this).attr('id');
	
		if (spl=="published") {
			setFormat(spl,data);

			//set the post's pub date... rebuild page
			rebuild_all();

		}
		if (spl=="displayed") {
			updateDate(spl,data);
		}
		if (data!=origtext) {
			if (spl=="published"||spl=="displayed") {
				//updateDate(spl,data);
			} else {
				data = strip_tags(data);
				$(this).html( data );
				updateText(spl,data);
			}
		}
	});	
	$(document).on('blur', '.html5-editor', function(event) {	
		var pasttext=origtext;
		var div = $(this);
		window.setTimeout(function () {
			var data = div.html();
			if (div.attr('id'))
				var spl = div.attr('id');
			if (data!=pasttext) 
				updateText(spl,data);
		}, 50);
	});	



	/* IF ESC KEY, REVERT TO ORIGINAL TEXT
		(ends up being a problem with safari's auto correct, so it's been commented out
	$(document).on('keyup', '.html5-editor, .editable', function(event) {
		if (event.keyCode==27) {
			$(this).html(origtext);
			$(this).blur();
			//console.log( $(this).attr('id') +': undo');
		}
	}); */



});



/* ON FOCUS MOVE TO END OF THE FIELD */
$.fn.focusEnd = function() {
    $(this).focus();
    var tmp = $('<span />').appendTo($(this)),
        node = tmp.get(0),
        range = null,
        sel = null;

    if (document.selection) {
        range = document.body.createTextRange();
        range.moveToElementText(node);
        range.select();
    } else if (window.getSelection) {
        range = document.createRange();
        range.selectNode(node);
        sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }
    tmp.remove();
    return this;
}
