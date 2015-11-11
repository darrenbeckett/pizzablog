</div>

<script src="/oven/js/jquery.min.js"></script>
<script src="/oven/js/jquery.ellipsis.min.js"></script>
<script>
function popupwindow(url, title, w, h) {
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);
	return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}
$(function() { 
	$('#content').on('click', '#nav', function() {
		if ($(this).hasClass('expanded')) {
			$('#nav').removeClass('expanded');
		} else {
			$('#nav').addClass('expanded');
		}
	});
});
$('.ellipsis3').ellipsis({
	row: preview_lines,
	onlyFullWords: true
});
if (google_tracking) {
var _gaq = [['_setAccount', google_tracking], ['_trackPageview']];
(function(d, t) {
	var g = d.createElement(t),
		s = d.getElementsByTagName(t)[0];
	g.async = true;
	g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g, s);
})(document, 'script');
}
</script>
</body>
</html>