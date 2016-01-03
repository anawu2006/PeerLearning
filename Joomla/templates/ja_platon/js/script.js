/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org 
 *------------------------------------------------------------------------------
 */

 (function($){

  // add scroll/show/hide for event calendar
  $(window).load(function () {
	  ////////////////////////////////
    // equalheight for col
    ////////////////////////////////
    var ehArray = ehArray2 = [],
      i = 0;

    $('.equal-height').each (function(){
      var $ehc = $(this);
      if ($ehc.has ('.equal-height')) {
        ehArray2[ehArray2.length] = $ehc;
      } else {
        ehArray[ehArray.length] = $ehc;
      }
    });
    for (i = ehArray2.length -1; i >= 0; i--) {
      ehArray[ehArray.length] = ehArray2[i];
    }

    var equalHeight = function() {
      for (i = 0; i < ehArray.length; i++) {
        var $cols = ehArray[i].children().filter('.col'),
          maxHeight = 0,
          equalChildHeight = ehArray[i].hasClass('equal-height-child');

      // reset min-height
        if (equalChildHeight) {
          $cols.each(function(){$(this).children().first().css('min-height', 0)});
        } else {
          $cols.css('min-height', 0);
        }
        $cols.each (function() {
          maxHeight = Math.max(maxHeight, equalChildHeight ? $(this).children().first().innerHeight() : $(this).innerHeight());
        });
        if (equalChildHeight) {
          $cols.each(function(){$(this).children().first().css('min-height', maxHeight)});
        } else {
          $cols.css('min-height', maxHeight);
        }
      }
      // store current size
      $('.equal-height > .col').each (function(){
        var $col = $(this);
        $col.data('old-width', $col.width()).data('old-height', $col.innerHeight());
      });
    };

    equalHeight();

    // monitor col width and fire equalHeight
    setInterval(function() {
      $('.equal-height > .col').each(function(){
        var $col = $(this);
        if (($col.data('old-width') && $col.data('old-width') != $col.width()) ||
            ($col.data('old-height') && $col.data('old-height') != $col.innerHeight())) {
          equalHeight();
          // break each loop
          return false;
        }
      });
    }, 500);
	
  });
  // add scroll/show/hide for event calendar

  $(document).ready(function(){
  	if($('.joms-toolbar--desktop').length > 0) {
  		// Add Affix for header
  		$heightBlocktop = $('.t3-topbar ').outerHeight() + $('.t3-mainnav ').outerHeight();
  		$('#community-wrap > .jomsocial').affix({
  		  offset: {
  			top: $heightBlocktop,
  		  }
  		})


  		$(window).resize(function(){
  			$heightBlocktop = $('.t3-topbar ').outerHeight() + $('.t3-mainnav ').outerHeight();
  			$('#community-wrap > .jomsocial').affix({
  			  offset: {
  				top: $heightBlocktop,
  			  }
  			})
  		});
  	}

    $('.btn-search').click(function() {
      $('.head-search').toggleClass('btn-open');
      $('.topbar-right').toggleClass('btn-open');
      if ($('.head-search').hasClass('btn-open')) {
        $('#mod-search-searchword').focus();
      }
    });

	// Reset header search when click outsite.
	$(document).click(function (e)
	{
		var container = $('.head-search');

		if (!container.is(e.target) && container.has(e.target).length === 0)
		{
			$('.head-search').removeClass('btn-open');
		}
	});
  });

})(jQuery);


// TAB
// -----------------
(function($){
  $(document).ready(function(){
    if($('.nav.nav-tabs').length > 0 && !$('.nav.nav-tabs').hasClass('nav-stacked')){
      $('.nav.nav-tabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
      })
     }
  });
})(jQuery);


// JOMS CALENDAR MODULE
// Add navigator for event list
//-----------------------
jQuery(document).ready(function($){ 

  var ec_add_nav = function ($module) {
    $module.find('.joms-event__grid').append('<div class="eventscalendar-nav"><span class="prev-event" title="Prev Event"><i class="fa fa-long-arrow-left"></i></span><span class="next-event" title="Next Event"><i class="fa fa-long-arrow-right"></i></span></div>');
    var $events = $module.find('.joms-calendar__event-list').children();
    // bind click for nav
    $module.find('.eventscalendar-nav span').on('click', function () {
      var $this = $(this),
        curridx = $module.data('current-idx'),
        dir = $this.hasClass('prev-event') ? -1 : +1,
        nextidx = curridx + dir;

      if (nextidx < 0) {
        // trigger prev month, then show the last item
        $module.data('current-idx', -1);
        $module.find('.joms-calendar--prev').trigger('click');
        return;
      }
      if (nextidx >= $events.length) {
        // trigger next month, then show the first item
        $module.find('.joms-calendar--next').trigger('click');
        return;
      }
      // else, show event
      show_event(nextidx);
    });

    var show_event = function (idx) {
      if (!$events.length) {
        $module.data('current-idx', 0);
        return;
      }

      // detect the most recent new event for the first show
      if ($module.data('current-idx') == undefined) {
        var $today = $module.find('.joms-calendar--today');
        if ($today.length) {
          var today = $today.text(),
            $next_event = $events.filter(function(){
              return $(this).data('event-day') >= today;
            }).first();
          if ($next_event.length) idx = $next_event.index();
        }
      }
      if (idx == -1 || $module.data('current-idx') == -1) idx = $events.length - 1;
      $events.addClass('hide').eq(idx).removeClass('hide');
      $module.data('current-idx', idx);
      // hightligh day
      var $event_days = $module.find('.joms-calendar__event'),
        $event = $events.eq(idx),
        event_day = $event.data('event-day');
      $event_days.removeClass('hilite').filter(function(){return $(this).data('event-day') == event_day}).addClass('hilite');
    };

    $('.joms-calendar--prev, .joms-calendar--next').on('click', function () {
      setTimeout(function(){
        // find the module withouth nav and reinit
        var $module = $('.joms-module--eventscalendar').filter(function(){
          return $(this).find('.eventscalendar-nav').length == 0;
        });
        ec_add_nav ($module);
      }, 50);
    });

    show_event (0);

    // click day to show event for that day
    $('.joms-calendar__event').on('click', function (){
      var event_day = $(this).data('event-day'),
          day_events = $events.filter(function(){
              return $(this).data('event-day') == event_day;
            }),
          idx = day_events.first().index();
      show_event(idx);
    });
  };

  $('.joms-module--eventscalendar').each (function(){
    ec_add_nav($(this));
  })
});


