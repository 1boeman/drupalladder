(function($){
	hC.twitter = function(){
		var $l = $('.location')
		var twit = $l.data('twitter'); 
		var twidget = $l.data('twitterwidgetid'); 
		
		if (twidget.length){
			$l.eq(0).prepend('<div class="twittercontainer"><h4>'+twit+' op Twitter</h4><a class="twitter-timeline" href="https://twitter.com/'+twit+'"'+
			'data-widget-id="'+twidget+'">Tweets by @'+twit+'</a>'+
			'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>');
		}	
	}
	$(function(){
		$('.event').on('click',function(){
			location.href = $(this).find('a')[0].href;
		});
		hC.twitter();
	});


}(jQuery)); 

