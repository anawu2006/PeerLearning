<?php
/**
 * ------------------------------------------------------------------------
 * JA Platon Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
 
  $videoLink 		= $helper->get('video-link');
  $video_src    = '';
  $video_link   = '';
	if ($videoLink) {
	  $arr = preg_split ('/=/', $videoLink, 2);
	  if (count($arr) == 2) {    
	    switch (trim($arr[0])) {
	      case 'vimeo':
	        $video_src = '//player.vimeo.com/video/' . trim($arr[1]) . '?title=0&amp;byline=0&amp;portrait=0&amp;&amp;loop=1';
	        $video_link = trim($arr[1]);
	        break;
	      case 'youtube':
	        $video_src = '//www.youtube.com/embed/' . trim($arr[1]) . '?rel=0&amp;controls=0&amp;showinfo=0&amp;loop=1&amp;html5=1';
	        $video_link = trim($arr[1]);
	        break;
	      case 'local':
	      	$video_src = 'images/' . trim($arr[1]);
	        $video_link = trim($arr[1]);
	      	break;
	      default:
	        break;
	    }
	  }
	}
?>

<div id="acm-video-<?php echo $module->id; ?>" class="acm-video style-1">

	<div class="video-player"><div class="video-wrapper">
		<?php if ($videoLink) { ?>
			<div id="videoplayer">
			<?php
				if (count($arr) == 2) :
				switch (trim($arr[0])) {
				  case 'vimeo':
					?>
					<iframe id="player1" src="https://player.vimeo.com/video/<?php echo $video_link; ?>?api=1&amp;player_id=player1&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=1" width="630" height="354" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<?php
					break;
				  case 'youtube':
					?>
					<img width="100%" height="100%" src="http://img.youtube.com/vi/<?php echo $video_link; ?>/maxresdefault.jpg" />
					<?php
					break;
					case 'local':
					?>
					<video>
			      <source type="video/ogg" src="<?php echo $video_src; ?>.ogv" id="ogv"></source>
						<source type="video/mp4" src="<?php echo $video_src; ?>.mp4" id="mp4"></source>
			      <source type="video/webm" src="<?php echo $video_src; ?>.webm" id="webm"></source>
					</video>
					<?php
					break;
				  default:
					break;
				}
			  endif;
			?>
			</div>

			<a <?php if (count($arr) == 2) {if (trim($arr[0]) == 'youtube') {echo ' onclick="javideoPlay();" ';}} ?> title="Play" class="btn btn-border btn-light btn-play"></a>
		<?php } ?>
  </div></div>
</div>
<script>
(function($){
	
  $(document).ready(function(){
  	
    // for vimeo js api player. need jquery so we set inside.
    <?php
    	if (count($arr) == 2) {
	    switch (trim($arr[0])) {
	      case 'vimeo':
			?>
			var player = $('iframe#player1');
			var playerOrigin = '*';
			var status = $('.status');

			// Listen for messages from the player
			if (window.addEventListener) {
				window.addEventListener('message', onMessageReceived, false);
			}
			else {
				window.attachEvent('onmessage', onMessageReceived, false);
			}

			// Handle messages received from the player
			function onMessageReceived(event) {
				// Handle messages from the vimeo player only
				if (!(/^https?:\/\/player.vimeo.com/).test(event.origin)) {
					return false;
				}
		
				if (playerOrigin === '*') {
					playerOrigin = event.origin;
				}
		
				var data = JSON.parse(event.data);
		
				switch (data.event) {
					case 'ready':
						onReady();
						break;
			   
					case 'playProgress':
						onPlayProgress(data.data);
						break;
				
					case 'pause':
						onPause();
						break;
			   
					case 'finish':
						onFinish();
						break;
				}
			}

			// Call the API when a button is pressed
			$('.btn-play').on('click', function() {
				jQuery('div.video-wrapper').addClass('playing');
				post($(this).attr('title').toLowerCase());
			});

			// Helper function for sending a message to the player
			function post(action, value) {
				var data = {
				  method: action
				};
		
				if (value) {
					data.value = value;
				}
		
				var message = JSON.stringify(data);
				player[0].contentWindow.postMessage(data, playerOrigin);
			}

			function onReady() {
				status.text('ready');
		
				post('addEventListener', 'pause');
				post('addEventListener', 'finish');
				post('addEventListener', 'playProgress');
			}

			function onPause() {
				status.text('paused');
			}

			function onFinish() {
				status.text('finished');
			}

			function onPlayProgress(data) {
				status.text(data.seconds + 's played');
			}
			<?php
	        break;
	      default:
	        break;
	    }
	  }
    ?>
  });

})(jQuery);
<?php
	// for youtube js api player. don't need jquery so we set outside.
	if (count($arr) == 2) :
	switch (trim($arr[0])) {
	  case 'youtube':
		?>
		  var tag = document.createElement('script');

		  tag.src = "https://www.youtube.com/iframe_api";
		  var firstScriptTag = document.getElementsByTagName('script')[0];
		  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		  var player;
		  function onYouTubeIframeAPIReady() {
		  	
		  }
		  function onPlayerReady(event) {
		  	jQuery('iframe#videoplayer').css('width', '100%').css('height', '100%');
			event.target.playVideo();
		  }

		  var done = false;
		  function onPlayerStateChange(event) {}
		  function stopVideo() {}

		  function javideoPlay() {
				jQuery('div.video-wrapper').addClass('playing');
			 	player = new YT.Player('videoplayer', {
			  height: '390',
			  width: '640',
			  videoId: '<?php echo $video_link; ?>',
			  events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			  }
			});
		  }
		<?php
		break;
	  default:
		break;
	}
  endif;
?>

<?php
	// for youtube js api player. don't need jquery so we set outside.
	if (count($arr) == 2) :
	switch (trim($arr[0])) {
	  case 'local':
?>

(function($){
  $(document).ready(function(){
		var v = $('#acm-video-<?php echo $module->id; ?> video')[0];
		$('.btn-play').on('click', function() {
			jQuery('div.video-wrapper').addClass('playing');
			v.play();
			v.onended = function(e) {
	      jQuery('div.video-wrapper').removeClass('playing');
	    };

		});
	});
})(jQuery);

<?php 
	}
	endif;
?>
</script>