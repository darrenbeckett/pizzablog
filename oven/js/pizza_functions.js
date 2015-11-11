/* GET THE LINKED CATEGORY */
function getBox(id) {
	//need to show the category title (editable)
	//need a list of posts linked to the category
	
	if (id.replace('e','')<1) {
		getPizza('e0');
		return;
	}

	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'getbx', bx: id }
	}).done(function( msg ) {
		//console.log(msg);
		showBox(msg);
	});
}

/* GET THE LINKED BLOG POST */
function getPizza(id) {
	if (id.replace('e','')<1) {
		showAbout();
		return;
	}
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'getpi', pi: id }
	}).done(function( msg ) {
		//console.log(msg);
		showPizza(msg);
	});
}

/* GET BOX OR PIZZA BASED ON SLUG */
function getSlug(mainslug) {
	mainslug = mainslug.split('/');
	
	if (mainslug[1]) {
		slug = mainslug[1];
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'getpislug', slug: slug }
		}).done(function( msg ) {
			//console.log(msg);
			showPizza(msg);
		});
	} else {
		slug = mainslug[0];
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'getbxslug', slug: slug }
		}).done(function( msg ) {
			//console.log(msg);
			showBox(msg);	
		});
	}
}

/* SHOW THE CONTENT */
function showBox(msg) {
	var df = $('#format').val();
	if (df.search(/g/i)<0 || df.search(/h/i)<0) df+= ' @ h:i a';
	var obj = jQuery.parseJSON(msg);
	if (obj.slug) {
		var hash = obj.slug;
		window.location.hash = hash;
		var link = hash.replace('#','');
		$('#eL a').attr('href','/pages/'+link);
	} else {
		window.location.hash = '';
	}

	$('#boxlist li').removeClass('on');
	$('#e'+obj.id).addClass('on');
	$('#form').show();
	$('#form').removeClass('about');
	$('#id').val(obj.id);
	$('#box_name').html(htmlspecialchars_decode(obj.name));
	$('#box_name').show();
	$('#pizzalist').html('');
	$('#pizzalist').show();
	$('#slug').val(obj.slug);
	$('#sort').val(obj.sort);
	$('#deleted').val(obj.deleted);

	$('#pizza_name').html('');
	$('#pizza_name').css('display','none');
	$('#cheese').html('');
	$('#cheese').css('display','none');
	$('#displayed').html('');
	$('#displayed').css('display','none');
	$('#published').html('');
	$('#published').css('display','none');
	$('#switch').html('');
	$('#switch').hide();
	$('#setbox').hide();

	$.each(obj.pizzas, function (index, blob) {
		if (blob) {			
		var cls = 'pza';
		var now = new Date().getTime()/1000;
		var display_date = date(df,strtotime(blob.display));
		if (!blob.published) {
			cls+= ' disabled';
			display_date = 'Inactive';
		} else if (strtotime(blob.display)>now) {
			cls+= ' waiting';
			display_date = 'Scheduled for '+display_date;
		}
		
		var shorty = blob.topcheese ? blob.topcheese : '&nbsp;';

		var field = '<li id="e'+blob.id+'" class="'+cls+'">\
						<span class="date">'+display_date+'</span>\
						<span class="title">'+blob.name+'</span>';
		if (blob.topmeat && blob.topmeat!='undefined') {
			field+= '	<span class="thumb" style="background-image:url(/'+blob.topmeat+');"></span>';
		}
			field+= '	<span class="blob ellipsis3">'+shorty+'</span>\
						</li>';
		$('#pizzalist').append(field);
		$('.ellipsis3').ellipsis({
			row: preview_lines,
			onlyFullWords: true
		});
		}
	});

	$('textarea').html5_editor({
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
}
function showPizza(msg) {
	var df = $('#format').val();

	var obj = jQuery.parseJSON(msg);
	if (obj.slug) {
		var hash = window.location.hash;
		hash = hash.split('/');
		hash = hash[0]+'/'+obj.slug;
		window.location.hash = hash;
		var link = hash.replace('#','');
		$('#eL a').attr('href','/pages/'+link);
	} else {
		window.location.hash = '';
	}
	$('#form').show();
	$('#form').removeClass('about');
	$('#id').val(obj.id);
	$('#box_name').html('');
	$('#box_name').css('display','none');
	$('#pizzalist').html('');
	$('#pizzalist').css('display','none');
	$('#pizza_name').html(htmlspecialchars_decode(obj.name));
	$('#pizza_name').show();
	$('#slug').val(obj.slug);
	$('#sort').val(obj.sort);
	$('#deleted').val(obj.deleted);
	$('#cheese').html('');
	$('#cheese').show();

	$('#displayed').html(date('m/d/Y g:i a',strtotime(obj.display)));
	$('#published').html(date(df,strtotime(obj.display)));
	$('#displayed').show();
	$('#published').show();

	//$('#toggle').show();
	$('#switch').html('<input type="checkbox" class="js-switch js-check-change" />');
	$('#switch').show();
	if (!obj.published || obj.published==null) {
		$('#switch .js-switch').prop('checked', false);
		$('#displayed').removeClass('published');
		$('#displayed').removeClass('scheduled');
		//$('#toggle').html('Turn On');
		//$('#toggle').switchClass('enabled','disabled');
		//$('#displayed').css('visibility','hidden');
		//$('#published').css('visibility','hidden');
	} else {
		$('#switch .js-switch').prop('checked', true);

		var now = new Date().getTime()/1000;
		if (strtotime(obj.display)<now) {
			$('#displayed').addClass('published');
			$('#displayed').removeClass('scheduled');
			$('#switch').addClass('published');
			$('#switch').removeClass('scheduled');
		} else {
			$('#displayed').addClass('scheduled');
			$('#displayed').removeClass('published');
			$('#switch').addClass('scheduled');
			$('#switch').removeClass('published');
		}

		//$('#toggle').html('Turn Off');
		//$('#toggle').switchClass('disabled','enabled');
		//$('#displayed').css('visibility','visible');
		//$('#published').css('visibility','visible');
	}
	switcheroo();

	$.each(obj.cheese, function (index, blob) {
		var field = '<li id="li'+blob.id+'" class="chz"><div id="drop'+blob.mid+'" class="drop">';
		if (blob.data) {
			field+= '<span class="rem" rel="meat" id="rem'+blob.mid+'">remove image</span>';
		} else {
			field+= '<input type="file" id="meat'+blob.mid+'" class="droparea spot" name="xfile" data-post="/oven/upload.php" data-type="jpg,jpeg,png,gif" />';
		}
			field+= '</div><textarea class="ta" name="ta'+blob.id+'" id="ta'+blob.id+'"></textarea>';
			field+= '<span class="rem" rel="cheese" id="rem'+blob.id+'"><p><em>&times;</em> delete text & image</p></span></li>';
		$('#cheese').append(field);
		chount++;
		if (blob.data) {
			$('#drop'+blob.mid).css('background-image','url(/'+blob.data+')');
			$('#drop'+blob.mid).addClass('full');
		}
		blob.text = blob.text.replace(/&gt;/g,'>');
		blob.text = blob.text.replace(/&lt;/g,'<');
		blob.text = blob.text.replace(/&quot;/g,'"');
		blob.text = blob.text.replace(/&amp;/g,'&');
		$('#ta'+blob.id).html(blob.text);
	});

	$('#setbox').html('<option value="">Set Category...</option>');
	$.each(obj.boxes, function (index, blob) {
		var field = '<option value="'+blob.id+'">'+blob.name+'</option>';
		$('#setbox').append(field);
	});
	if (obj.parent) $('#setbox').val(obj.parent);
	$('#setbox').show();

	initDroparea('.droparea');
	$('textarea').html5_editor({
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

}
function showAbout() {
	var df = $('#format').val();
	//window.location.hash = '';
	history.pushState("", document.title, window.location.pathname);
	
	$('#form').show();
	$('#form').addClass('about');
	$('#id').val('0');
	$('#box_name').html('');
	$('#box_name').css('display','none');
	$('#pizzalist').html('');
	$('#pizzalist').css('display','none');
	$('#pizza_name').html('');
	$('#pizza_name').css('display','none');
	$('#slug').val('');
	$('#sort').val('');
	$('#displayed').html('');
	$('#displayed').css('display','none');
	$('#published').html('');
	$('#published').css('display','none');
	$('#deleted').val('');
	$('#cheese').html('');
	$('#cheese').show();
	//$('#toggle').hide();
	$('#switch').html('');
	$('#switch').hide();
	$('#setbox').val('');
	$('#setbox').hide();
	var blob_text = $('#about').val();
	var blob_data = $('#image').val();
	var field = '<li id="li0" class="chz"><div id="drop0" class="drop">';
	if (blob_data) {
		field+= '<span class="rem" rel="meat" id="rem0">remove image</span>';
	} else {
		field+= '<input type="file" id="meat0" class="droparea spot" name="xfile" data-post="/oven/upload.php" data-type="jpg,jpeg,png,gif" />';
	}
		field+= '</div><textarea class="ta" name="ta0" id="ta0"></textarea>';
		field+= '</li>';
	$('#cheese').append(field);
	if (blob_data) {
		$('#drop0').css('background-image','url(/'+blob_data+')');
		$('#drop0').addClass('full');
	}
	blob_text = blob_text.replace(/&gt;/g,'>');
	blob_text = blob_text.replace(/&lt;/g,'<');
	blob_text = blob_text.replace(/&quot;/g,'"');
	blob_text = blob_text.replace(/&amp;/g,'&');
	$('#ta0').html(blob_text);
	initDroparea('.droparea');
	$('textarea').html5_editor({
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
}





/* MAKE TOGGLE WORK */
function switcheroo() {
	if (Array.prototype.forEach) {
		var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
		elems.forEach(function(html) {
			var switchery = new Switchery(html);
		});
	} else {
		var elems = document.querySelectorAll('.js-switch');
		for (var i = 0; i < elems.length; i++) {
			var switchery = new Switchery(elems[i]);
		}
	}
	var changeCheckbox = document.querySelector('.js-check-change'), changeField = document.querySelector('.js-check-change-field');
	changeCheckbox.onchange = function() {
		id = $('#id').val();

		if (changeCheckbox.checked) {
			pub = 'on';
			//$('#e'+id).removeClass('disabled');
		} else {
			pub = 'off';
			//$('#e'+id).addClass('disabled');
		}

		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'cookpi', id: id, pub: pub }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);
			var now = new Date().getTime()/1000;
			if (pub=="off") {
				$('#displayed').removeClass('scheduled');
				$('#displayed').removeClass('published');
			} else if (strtotime(obj.display)<now) {
				$('#displayed').addClass('published');
				$('#displayed').removeClass('scheduled');
				$('#switch').addClass('published');
				$('#switch').removeClass('scheduled');
			} else {
				$('#displayed').addClass('scheduled');
				$('#displayed').removeClass('published');
				$('#switch').addClass('scheduled');
				$('#switch').removeClass('published');
			}

			//enabled/disabled a post... rebuild page
			rebuild_all();

		});
	};
}

/* UPDATE THE DISPLAY DATE */
function updateDate(spl,data) {
	id = $('#id').val();
	df = $('#format').val();
	$.ajax({
		type: "POST",
		url: "/oven/dateajax.php",
		data: { data: data, format: df }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
		$('#displayed').html(obj.human);
		$('#published').html(obj.post);
		
		var now = new Date().getTime()/1000;
		if (strtotime(obj.db)>now) {
			//$('#e'+id).addClass('waiting');
			$('#displayed').addClass('scheduled');
			$('#displayed').removeClass('published');
			$('#switch').addClass('scheduled');
			$('#switch').removeClass('published');
		} else {
			//$('#e'+id).removeClass('waiting');
			$('#displayed').addClass('published');
			$('#displayed').removeClass('scheduled');
			$('#switch').addClass('published');
			$('#switch').removeClass('scheduled');
		}

		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'setdate', data: obj.db, id: id }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);

			//set the post's date... rebuild page
			rebuild_all();

		});

	});
}

/* UPDATE TEXT */
function updateText(spl,data) {	
	origtext=data;
	spl = spl.split('_');
	
	if (spl[0]=="xta0") {
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'updateabout', data: data }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);
			$('#about').val(data);
			if (data.replace("<br>","") || $('#image').val()) {
				$('#e0').removeClass('disabled');
			} else {
				$('#e0').addClass('disabled');
			}
			
			//updated about text... rebuild
			rebuild_pizza('e0');

			return;
		});
	} else {
	
		if (spl.length>1) {
			table = spl[0];
			field = spl[1];
			if (table=="box" && data=="DELETE ME") {
				id = $('#id').val();
				deleteBx(id);
				return;
			} else if (table=="box") {
				id = $('#id').val();
				$('#e'+id+' .title').text(strip_tags(htmlspecialchars_decode(data)));
			} else if (table=="pizza" && data=="DELETE ME") {
				id = $('#id').val();
				deletePi(id);
				return;
			} else if (table=="pizza") {
				id = $('#id').val();
				//$('#e'+id).text(strip_tags(htmlspecialchars_decode(data)));
			} else {
				id = '';
			}
		} else {
			table = "cheese";
			field = "text";
			id = spl[0].replace('xta','');
			id = id.replace('ta','');
			$('#ta'+id).val('1');
			
			//try to replace STRONG with B
			data = data.replace(/<strong(.*?)>(.*?)<\/strong>/gi, "<b>$2</b>")
			//try to replace EM with I
			data = data.replace(/<em(.*?)>(.*?)<\/em>/gi, "<b>$2</b>");

			//try to remove SPAN, P, and FONT tags (any others?)
			data = data.replace(/<p(.*?)>(<(span|font|p)(.*?)>)*(.*?)<\/(p|font|span)>/gi, "<div>$5</div>");
			
			if (data=="<br>") data="";
			
			$('#'+spl[0]).html(data);

		}
		var oldslug = $('#slug').val();
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'updatetext', table: table, field: field, id: id, data: data }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);
			
			var hash = window.location.hash;
			
			if (table=="pizza") {
				hash = hash.split('/');
				hash = hash[0]+'/'+obj.slug;
				window.location.hash = hash;
				rebuild_all();
			} else if (table=="box") {
				window.location.hash = obj.slug;
				rebuild_all();
			} else if (table=="meat"||table=="cheese") {
				rebuild_pizza( $('#id').val() );
			} else {
				rebuild_menu();
			}
			
		});	
		
	}
}

/* RESET ALL THE CATEGORY NUMBERS */
function reset_numbers() {
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'resetnum' }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);

		$.each(obj.count, function (pizza, blob) {
			$.each(blob, function (state, number) {
				
				$('#e'+pizza+' .'+state).html(number);
				
			});
		});

	});	
}

/* DELETE CATEGORY */
function deleteBx(id) {
	$('#e'+id).remove();
	$('#boxlist').trigger('sortupdate');
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'deletebx', id: id }
	}).done(function( msg ) {
		//console.log(msg);
		window.location = '/oven/editor/';
		//deleted a category... rebuild all
		rebuild_all();
	});	
}

/* DELETE BLOG POST */
function deletePi(id) {
	//$('#e'+id).remove();
	$('#pizzalist').trigger('sortupdate');
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'deletepi', id: id }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
		if (obj.next>0 || $('#about').val().replace("<br>","") || $('#image').val()) {

		} else {
			$('#form').removeClass('about');
			$('#form').hide();
			$('#id').val('');
			$('#pizza_name').html('');
			$('#pizza_name').css('display','none');
			$('#slug').val('');
			$('#sort').val('');
			$('#displayed').html('');
			$('#displayed').css('display','none');
			$('#published').html('');
			$('#published').css('display','none');
			$('#deleted').val('');
			$('#cheese').html('');
			$('#switch').html('');
			$('#switch').hide();
		}

		if (obj.next!=null) {
			window.location.hash = obj.next;
			window.location.reload();
		} else {
			window.location = '/oven/editor/';
		}
		
		//deleted a post... rebuild page
		rebuild_all();

	});	
}

/* DROP IMAGE FUNCTION */
function initDroparea($elements) {
	$($elements).droparea({
		'instructions': 'Drop Image Here',
		'init' : function(result){
			//console.log('init',result);
		},
		'start' : function(area){
			//console.log('start');
			//area.find('.error').remove(); 
		},
		'error' : function(result, input, area){
			//console.log('custom error',result.error);
			var id = $(input).attr('id');
				id = id.replace('meat','');

			//$('<div class="error">').html(result.error).prependTo(area); 

			$('#ins_'+id).html(result.error);
			$('#ins_'+id).addClass('error');
			return 0;
		},
		'complete' : function(result, file, input, area){
			var id = $(input).attr('id');
				id = id.replace('meat','');
				
			if (id<1) $('#image').val(result.filename);
			
			if((/image/i).test(file.type)){
				//area.find('img').remove();
				//area.data('value',result.filename);
				//area.append($('<img>',{'src': result.path + result.filename + '?' + Math.random()}));
				$('#drop'+id).html('');
				$('#drop'+id).css('background-image','url(/'+result.filename+'?'+Math.random()+')');
				$('#drop'+id).addClass('full');
				$.ajax({
					type: "POST",
					url: "/oven/pizzajax.php",
					data: { action: 'newmeat', id: id, data: result.filename }
				}).done(function( msg ) {
					//console.log(msg);
					var obj = jQuery.parseJSON(msg);
					$('#drop'+id).append('<span class="rem" rel="meat" id="rem'+id+'">remove image</span>');
					
					//added an image... rebuild page
					rebuild_pizza( $('#id').val() );

				});
				
			} 
			//console.log('custom complete',result);
		}
	});
}

/* ORDER TEXT BLOCKS */
function cheeseOrder(arr) {
	parent = $('#id').val();
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'sortcheese', parent: parent, order: arr }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
	});
}

/* GET POSITION OF CURSOR */
function getCaretCharacterOffsetWithin(element) {
	var caretOffset = 0;
	var doc = element.ownerDocument || element.document;
	var win = doc.defaultView || doc.parentWindow;
	var sel;
	if (typeof win.getSelection != "undefined") {
		var range = win.getSelection().getRangeAt(0);
		var preCaretRange = range.cloneRange();
		preCaretRange.selectNodeContents(element);
		preCaretRange.setEnd(range.endContainer, range.endOffset);
		caretOffset = preCaretRange.toString().length;
	} else if ( (sel = doc.selection) && sel.type != "Control") {
		var textRange = sel.createRange();
		var preCaretTextRange = doc.body.createTextRange();
		preCaretTextRange.moveToElementText(element);
		preCaretTextRange.setEndPoint("EndToEnd", textRange);
		caretOffset = preCaretTextRange.text.length;
	}
	return caretOffset;
}





/* LIKE PHP FUNCTIONS */
function isInt(n) {
	return typeof n === 'number' && n % 1 == 0;
}
function htmlspecialchars_decode(string, quote_style) {
	var optTemp = 0,
	i = 0,
	noquotes = false;
	if (typeof quote_style === 'undefined') {
	quote_style = 2;
	}
	string = string.toString()
	.replace(/&lt;/g, '<')
	.replace(/&gt;/g, '>');
	var OPTS = {
	'ENT_NOQUOTES': 0,
	'ENT_HTML_QUOTE_SINGLE': 1,
	'ENT_HTML_QUOTE_DOUBLE': 2,
	'ENT_COMPAT': 2,
	'ENT_QUOTES': 3,
	'ENT_IGNORE': 4
	};
	if (quote_style === 0) {
	noquotes = true;
	}
	if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
	quote_style = [].concat(quote_style);
	for (i = 0; i < quote_style.length; i++) {
	  // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
	  if (OPTS[quote_style[i]] === 0) {
		noquotes = true;
	  } else if (OPTS[quote_style[i]]) {
		optTemp = optTemp | OPTS[quote_style[i]];
	  }
	}
	quote_style = optTemp;
	}
	if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
	string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
	// string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
	}
	if (!noquotes) {
	string = string.replace(/&quot;/g, '"');
	}
	// Put this in last place to avoid escape being double-decoded
	string = string.replace(/&amp;/g, '&');
	string = string.replace(/&nbsp;/g, ' ');
	
	return string;
}
function strip_tags (input, allowed) {
	allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
	commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
	return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	});
}
function date (format, timestamp) {
    var that = this,
      jsdate,
      f,
      // Keep this here (works, but for code commented-out
      // below for file size reasons)
      //, tal= [],
      txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
      // trailing backslash -> (dropped)
      // a backslash followed by any character (including backslash) -> the character
      // empty string -> empty string
      formatChr = /\\?(.?)/gi,
      formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s;
      },
      _pad = function (n, c) {
        n = String(n);
        while (n.length < c) {
          n = '0' + n;
        }
        return n;
      };
  f = {
    // Day
    d: function () { // Day of month w/leading 0; 01..31
      return _pad(f.j(), 2);
    },
    D: function () { // Shorthand day name; Mon...Sun
      return f.l().slice(0, 3);
    },
    j: function () { // Day of month; 1..31
      return jsdate.getDate();
    },
    l: function () { // Full day name; Monday...Sunday
      return txt_words[f.w()] + 'day';
    },
    N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
      return f.w() || 7;
    },
    S: function(){ // Ordinal suffix for day of month; st, nd, rd, th
      var j = f.j(),
        i = j%10;
      if (i <= 3 && parseInt((j%100)/10, 10) == 1) {
        i = 0;
      }
      return ['st', 'nd', 'rd'][i - 1] || 'th';
    },
    w: function () { // Day of week; 0[Sun]..6[Sat]
      return jsdate.getDay();
    },
    z: function () { // Day of year; 0..365
      var a = new Date(f.Y(), f.n() - 1, f.j()),
        b = new Date(f.Y(), 0, 1);
      return Math.round((a - b) / 864e5);
    },

    // Week
    W: function () { // ISO-8601 week number
      var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
        b = new Date(a.getFullYear(), 0, 4);
      return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
    },

    // Month
    F: function () { // Full month name; January...December
      return txt_words[6 + f.n()];
    },
    m: function () { // Month w/leading 0; 01...12
      return _pad(f.n(), 2);
    },
    M: function () { // Shorthand month name; Jan...Dec
      return f.F().slice(0, 3);
    },
    n: function () { // Month; 1...12
      return jsdate.getMonth() + 1;
    },
    t: function () { // Days in month; 28...31
      return (new Date(f.Y(), f.n(), 0)).getDate();
    },

    // Year
    L: function () { // Is leap year?; 0 or 1
      var j = f.Y();
      return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
    },
    o: function () { // ISO-8601 year
      var n = f.n(),
        W = f.W(),
        Y = f.Y();
      return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
    },
    Y: function () { // Full year; e.g. 1980...2010
      return jsdate.getFullYear();
    },
    y: function () { // Last two digits of year; 00...99
      return f.Y().toString().slice(-2);
    },

    // Time
    a: function () { // am or pm
      return jsdate.getHours() > 11 ? "pm" : "am";
    },
    A: function () { // AM or PM
      return f.a().toUpperCase();
    },
    B: function () { // Swatch Internet time; 000..999
      var H = jsdate.getUTCHours() * 36e2,
        // Hours
        i = jsdate.getUTCMinutes() * 60,
        // Minutes
        s = jsdate.getUTCSeconds(); // Seconds
      return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
    },
    g: function () { // 12-Hours; 1..12
      return f.G() % 12 || 12;
    },
    G: function () { // 24-Hours; 0..23
      return jsdate.getHours();
    },
    h: function () { // 12-Hours w/leading 0; 01..12
      return _pad(f.g(), 2);
    },
    H: function () { // 24-Hours w/leading 0; 00..23
      return _pad(f.G(), 2);
    },
    i: function () { // Minutes w/leading 0; 00..59
      return _pad(jsdate.getMinutes(), 2);
    },
    s: function () { // Seconds w/leading 0; 00..59
      return _pad(jsdate.getSeconds(), 2);
    },
    u: function () { // Microseconds; 000000-999000
      return _pad(jsdate.getMilliseconds() * 1000, 6);
    },

    // Timezone
    e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
      // The following works, but requires inclusion of the very large
      // timezone_abbreviations_list() function.
/*              return that.date_default_timezone_get();
*/
      throw 'Not supported (see source code of date() for timezone on how to add support)';
    },
    I: function () { // DST observed?; 0 or 1
      // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
      // If they are not equal, then DST is observed.
      var a = new Date(f.Y(), 0),
        // Jan 1
        c = Date.UTC(f.Y(), 0),
        // Jan 1 UTC
        b = new Date(f.Y(), 6),
        // Jul 1
        d = Date.UTC(f.Y(), 6); // Jul 1 UTC
      return ((a - c) !== (b - d)) ? 1 : 0;
    },
    O: function () { // Difference to GMT in hour format; e.g. +0200
      var tzo = jsdate.getTimezoneOffset(),
        a = Math.abs(tzo);
      return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
    },
    P: function () { // Difference to GMT w/colon; e.g. +02:00
      var O = f.O();
      return (O.substr(0, 3) + ":" + O.substr(3, 2));
    },
    T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
      // The following works, but requires inclusion of the very
      // large timezone_abbreviations_list() function.
/*              var abbr = '', i = 0, os = 0, default = 0;
      if (!tal.length) {
        tal = that.timezone_abbreviations_list();
      }
      if (that.php_js && that.php_js.default_timezone) {
        default = that.php_js.default_timezone;
        for (abbr in tal) {
          for (i=0; i < tal[abbr].length; i++) {
            if (tal[abbr][i].timezone_id === default) {
              return abbr.toUpperCase();
            }
          }
        }
      }
      for (abbr in tal) {
        for (i = 0; i < tal[abbr].length; i++) {
          os = -jsdate.getTimezoneOffset() * 60;
          if (tal[abbr][i].offset === os) {
            return abbr.toUpperCase();
          }
        }
      }
*/
      return 'UTC';
    },
    Z: function () { // Timezone offset in seconds (-43200...50400)
      return -jsdate.getTimezoneOffset() * 60;
    },

    // Full Date/Time
    c: function () { // ISO-8601 date.
      return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
    },
    r: function () { // RFC 2822
      return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
    },
    U: function () { // Seconds since UNIX epoch
      return jsdate / 1000 | 0;
    }
  };
  this.date = function (format, timestamp) {
    that = this;
    jsdate = (timestamp === undefined ? new Date() : // Not provided
      (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
      new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
    );
    return format.replace(formatChr, formatChrCb);
  };
  return this.date(format, timestamp);
}
function strtotime(text, now) {
    var parsed, match, year, date, days, ranges, len, times, regex, i;

    if (!text) {
        return null;
    }

    // Unecessary spaces
    text = text.replace(/^\s+|\s+$/g, '')
        .replace(/\s{2,}/g, ' ')
        .replace(/[\t\r\n]/g, '')
        .toLowerCase();

    if (text === 'now') {
        return now === null || isNaN(now) ? new Date().getTime() / 1000 | 0 : now | 0;
    }

    match = text.match(/^(\d{2,4})-(\d{2})-(\d{2})(?:\s(\d{1,2}):(\d{2})(?::\d{2})?)?(?:\.(\d+)?)?$/);
    if (match) {
        year = match[1] >= 0 && match[1] <= 69 ? + match[1] + 2000 : match[1];
        return new Date(year, parseInt(match[2], 10) - 1, match[3],
            match[4] || 0, match[5] || 0, match[6] || 0, match[7] || 0) / 1000 | 0;
    }

    date = now ? new Date(now * 1000) : new Date();
    days = {
        'sun': 0,
        'mon': 1,
        'tue': 2,
        'wed': 3,
        'thu': 4,
        'fri': 5,
        'sat': 6
    };
    ranges = {
        'yea': 'FullYear',
        'mon': 'Month',
        'day': 'Date',
        'hou': 'Hours',
        'min': 'Minutes',
        'sec': 'Seconds'
    };

    function lastNext(type, range, modifier) {
        var diff, day = days[range];

        if (typeof day !== 'undefined') {
            diff = day - date.getDay();

            if (diff === 0) {
                diff = 7 * modifier;
            }
            else if (diff > 0 && type === 'last') {
                diff -= 7;
            }
            else if (diff < 0 && type === 'next') {
                diff += 7;
            }

            date.setDate(date.getDate() + diff);
        }
    }
    function process(val) {
        var splt = val.split(' '), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
            type = splt[0],
            range = splt[1].substring(0, 3),
            typeIsNumber = /\d+/.test(type),
            ago = splt[2] === 'ago',
            num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

        if (typeIsNumber) {
            num *= parseInt(type, 10);
        }

        if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
            return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
        }
        if (range === 'wee') {
            return date.setDate(date.getDate() + (num * 7));
        }

        if (type === 'next' || type === 'last') {
            lastNext(type, range, num);
        }
        else if (!typeIsNumber) {
            return false;
        }
        return true;
    }

    times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
        '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
        '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
    regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

    match = text.match(new RegExp(regex, 'gi'));
    if (!match) {
        return false;
    }

    for (i = 0, len = match.length; i < len; i++) {
        if (!process(match[i])) {
            return false;
        }
    }

    // ECMAScript 5 only
    //if (!match.every(process))
    //    return false;

    return (date.getTime() / 1000) | 0;
}

/* HIDDEN FEATURE: UPDATE POST DATE FORMAT */
var month = new Array('january','february','march','april','may','june','july','august','september','october','november','december');
var mo = new Array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
var dy = new Array('sun','mon','tue','wed','thu','fri','sat');
function setFormat(spl,data) {
	data = data.replace('<br>',' ');
	data = data.replace('&nbsp;',' ');
	data = data.trim();
	format = 'l, F j, Y';//fallback date format
	//origtext=data;
	
	var currentTime = new Date()
	
	var problem=1;
	var slash = data.match(/\//ig); 
		if (slash) { slash = slash.length; } else { slash = 0; }
	var dash = data.match(/-/ig); 
		if (dash) { dash = dash.length; } else { dash = 0; }
	var space = data.match(/ /ig); 
		if (space) { space = space.length; } else { space = 0; }

	//console.log('slash='+slash+', dash='+dash+', space='+space);
	
	if (slash>0) { 
		var part = data.split('/');
		if (part.length>3) {
			showFormat(format); return;
		} else if (part.length>2) {
			// assume m/d/y
			p0=part[0].split(' ');p1=part[1].split(' ');p2=part[2].split(' ');
			m=p0[0]; d=p1[0]; y=p2[0];
			if (!isInt(m-0) || !isInt(d-0) || !isInt(y-0) || m>12 || d>31 || y>2064) {
				showFormat(format); return;
			}
			dbm=m;
			if (m<10 && m.length<2) { mf='n'; dbm='0'+m; } else { mf='m'; }
			dbd=d
			if (d<10 && d.length<2) { df='j'; dbd='0'+d; } else { df='d'; }
			dby=y
			if (y<100) yf='y'; else yf='Y';
			if (y<10 && y.length<2) y="0"+y;
			if (y<2000&&y.length<3) dby=(y-0)+2000; else if (y<1900&&y.length<3) dby=(y-0)+1900;

			dbdate = dby+'-'+dbm+'-'+dbd+' 00:00:00';
			format = mf+'/'+df+'/'+yf;
			showFormat(format); return;
		} else if (part.length>1) {
			// assume m/d
			p0=part[0].split(' ');p1=part[1].split(' ');
			m=p0[0]; d=p1[0]; y=currentTime.getFullYear();
			if (!isInt(m-0) || !isInt(d-0) || !isInt(y-0) || m>12 || d>31 || y>2064) {
				showFormat(format); return;
			}
			dbm=m;
			if (m<10 && m.length<2) { mf='n'; dbm='0'+m; } else { mf='m'; }
			dbd=d
			if (d<10 && d.length<2) { df='j'; dbd='0'+d; } else { df='d'; }
			dby=y

			dbdate = dby+'-'+dbm+'-'+dbd+' 00:00:00';
			format = mf+'/'+df;
			showFormat(format); return;
		} else {
			showFormat(format); return;
		}
	}

	if (dash>0) { // yyyy-mm-dd, m-d-y
		var part = data.split('-');
		if (part.length>3) {
			showFormat(format); return;
		} else if (part.length>2) {
			p0=part[0].split(' ');p1=part[1].split(' ');p2=part[2].split(' ');
			if (p0[0].length>2) {
				//assume Y-m-d
				y=p0[0]; m=p1[0]; d=p2[0];
				if (!isInt(m-0) || !isInt(d-0) || !isInt(y-0) || m>12 || d>31 || y>2064) {
					showFormat(format); return;
				}
				dbm=m;
				if (m<10 && m.length<2) { dbm='0'+m; }
				dbd=d
				if (d<10 && d.length<2) { dbd='0'+d; }
				dby=y
				if (y<2000&&y.length<3) dby=(y-0)+2000; else if (y<1900&&y.length<3) dby=(y-0)+1900;
	
				dbdate = dby+'-'+dbm+'-'+dbd+' 00:00:00';
				format = 'Y-m-d';
				showFormat(format); return;
			} else {
				//assume m-d-y
				m=p0[0]; d=p1[0]; y=p2[0];
				if (!isInt(m-0) || !isInt(d-0) || !isInt(y-0) || m>12 || d>31 || y>2064) {
					showFormat(format); return;
				}
				dbm=m;
				if (m<10 && m.length<2) { mf='n'; dbm='0'+m; } else { mf='m'; }
				dbd=d
				if (d<10 && d.length<2) { df='j'; dbd='0'+d; } else { df='d'; }
				dby=y
				if (y<100) yf='y'; else yf='Y';
				if (y<10 && y.length<2) y="0"+y;
				if (y<2000&&y.length<3) dby=(y-0)+2000; else if (y<1900&&y.length<3) dby=(y-0)+1900;
	
				dbdate = dby+'-'+dbm+'-'+dbd+' 00:00:00';
				format = mf+'-'+df+'-'+yf;
				showFormat(format); return;
			}
		} else if (part.length>1) {
			// assume m-d
			p0=part[0].split(' ');p1=part[1].split(' ');
			m=p0[0]; d=p1[0]; y=currentTime.getFullYear();
			if (!isInt(m-0) || !isInt(d-0) || !isInt(y-0) || m>12 || d>31 || y>2064) {
				showFormat(format); return;
			}
			dbm=m;
			if (m<10 && m.length<2) { mf='n'; dbm='0'+m; } else { mf='m'; }
			dbd=d
			if (d<10 && d.length<2) { df='j'; dbd='0'+d; } else { df='d'; }
			dby=y

			dbdate = dby+'-'+dbm+'-'+dbd+' 00:00:00';
			format = mf+'-'+df;
			showFormat(format); return;
		} else {
			showFormat(format); return;
		}
	}
	
	if (space>0) {
		var part = data.split(' ');
		var f = new Array();
		var day='',d='',m='',y='';
		for (i=0;i<part.length;i++) {
			p = part[i].replace(',','');
			p = p.replace('.','');
			if (p.match(/:/ig)) {
				//skip this block, looks like a time
			} else if (p.match(/^[0-9]+[(st)*(nd)*(rd)*(th)*]+$/im)) {
				//might be 1st,2nd,3rd,4th
				p = p.replace(/(st)*(nd)*(rd)*(th)*$/gim,"");
				d = p;
				if (p.length==2) f[i] = "dS";
				else f[i] = "jS";
			} else if (!isInt(p-0)) {
				mch = p.toLowerCase();
				
				for (j=0;j<=dy.length;j++) {
					D = null;
					D = mch.match(dy[j],"g");
					//console.log(D);
					if (D && D!="") D = D.length;
					if (D>0 && mch.match(/day/,i)) {
						day = dy[j];
						f[i] = "l";
					} else if (D>0) {
						day = dy[j];
						f[i] = "D";
					}
				}
				M = mo.indexOf(mch);
				if (M>=0) {
					m = M; m+=1;
					f[i] = "M";
				}
				F = month.indexOf(mch);
				if (F>=0) {
					m = F; m+=1;
					f[i] = "F";
				}
			} else if (isInt(p-0)) {
				//these are numbers
				if (p.length==4) {
					y = p;
					f[i] = "Y";
				} else if (p.length==2) {
					d = p;
					f[i] = "d";
				} else {
					d = p;
					f[i] = "j";
				}
			}
			if (f[i]) {
				if (part[i].match(/,/g)) f[i] = f[i]+',';
				if (part[i].match(/\./g)) f[i] = f[i]+'.';
			} else {
				f[i]="";
			}
		}
		if (!y) y=currentTime.getFullYear();

		if ((m-0)<1 || (d-0)<1 || m>12 || d>31 || y>2064) {
			showFormat(format); return;
		}
		if (m<10) m="0"+m;
		if (d<10) d="0"+d;
		dbdate = y+'-'+m+'-'+d+' 00:00:00';
		format = f.join(' ');
		showFormat(format); return;
	}
	
	showFormat(format); return;

}
function showFormat(format) {
	data = $('#displayed').html();
	$('#format').val(format);
	$.ajax({
		type: "POST",
		url: "/oven/dateajax.php",
		data: { data: data, format: format }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
		$('#published').html(obj.post);
		
		$.ajax({
			type: "POST",
			url: "/oven/pizzajax.php",
			data: { action: 'dateformat', format: format }
		}).done(function( msg ) {
			//console.log(msg);
			var obj = jQuery.parseJSON(msg);
		});
		
	});
}





/* ANY NECESSARY GLOBAL VARIABLES */
var chount=0;
var origtext='';
var thisfield='';
