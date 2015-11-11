//need a cron job to find pages going live!

function rebuild_all() {
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'rebuild_all' }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
	});
}
function rebuild_menu() {
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'rebuild_menu' }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
	});
}
function rebuild_box(id) {
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'rebuild_box', id: id }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
	});
}
function rebuild_pizza(id) {
	$.ajax({
		type: "POST",
		url: "/oven/pizzajax.php",
		data: { action: 'rebuild_pizza', id: id }
	}).done(function( msg ) {
		//console.log(msg);
		var obj = jQuery.parseJSON(msg);
	});
}
