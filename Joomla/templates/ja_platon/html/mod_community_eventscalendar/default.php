<?php
    /**
     * @copyright (C) 2015 iJoomla, Inc. - All rights reserved.
     * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
     * @author iJoomla.com <webmaster@ijoomla.com>
     * @url https://www.jomsocial.com/license-agreement
     * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
     * More info at https://www.jomsocial.com/license-agreement
     */
    defined('_JEXEC') or die('Unauthorized Access');

    $config = CFactory::getConfig();
    $showAmpm = $config->get('eventshowampm');
	$document = JFactory::getDocument();
	$document->addScript(JURI::root(true) . "/modules/mod_community_eventscalendar/calendar.js");
?>

<div class="joms-module">

    <?php // if($user->isOnline()):?>
    <?php // else:?>
    <!-- If not logged in -->
    <?php // endif;?>

    <div class="joms-module--eventscalendar">
        <div id="calendar<?php echo $module->id; ?>"></div>
    </div>

</div>

<script type="text/javascript">
	joms_mod_eventscalendar_init(jQuery);
    // initialize calender
    function joms_mod_eventscalendar_init($) {
        $('#calendar<?php echo $module->id; ?>').eCalendar({
            firstDay: <?php echo $firstDay; ?>,
            weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            textArrows: {previous: '◀', next: '▶'},
            eventTitle: 'Events',
            url: '',
            events: [
                <?php
                    foreach ($events as $event) {
                        $date = CTimeHelper::convertSQLtimetoChunk($event->startdate);
                ?>
                {
                    title: '<?php echo str_replace("'", "\'", $event->title); ?>',
                    description: '<?php echo str_replace("'", "\'", preg_replace( "/\r?\n/", " ", $event->description )); ?>',
                    url: '<?php echo CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid='.$event->id); ?>',
                    showAmpm: +'<?php echo $showAmpm ?>',
                    datetime: new Date(
                        <?php echo $date['year'];?>,
                        <?php echo intval($date['month']) - 1;?>,
                        <?php echo $date['day'];?>,
                        <?php echo $date['hour'];?>,
                        <?php echo $date['minute'];?>,
                        <?php echo $date['second'];?>

                    )
                },
                <?php } ?>
            ]
        });
    }

</script>
