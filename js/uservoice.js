var uservoiceOptions = {
  /* required */
  key: 'xxxxx',
  host: 'xxxxx.uservoice.com', 
  forum: 'xxx',
  showTab: true,  
  /* optional */
  alignment: 'left',
  background_color:'#f00', 
  text_color: 'white',
  hover_color: '#06C',
  lang: 'en'
};

function _loadUserVoice() {
//  var s = document.createElement('script');
//  s.setAttribute('type', 'text/javascript');
//  s.setAttribute('src', ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js");
//  document.getElementsByTagName('head')[0].appendChild(s);

	// new version 2011-11-29
	var uvOptions = {};
	(function() {
		var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
		uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/ZSlrdpxlKaF2yCnUZPh3A.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
	})();
}
_loadSuper = window.onload;
window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };