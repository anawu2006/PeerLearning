<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-eventlist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
$lang = JoomdleHelperContent::get_lang ();

/* Para cada curso, mostramos todos los eventos */
if (is_array($this->my_events))
foreach ($this->my_events as $id => $curso) :
    if ((is_array($curso['events'])) && (count($curso['events']))) :

?>
    <div class="joomdle_course_events">
        <h4>
                <?php echo $curso['fullname']; ?>
        </h4>
		<br>

<?php
    foreach ($curso['events'] as  $event) : ?>
        <div class="joomdle_event_list_item">
            <div class="joomdle_item_title joomdle_event_list_item_date">
            <?php
                $day = date('d', $event['timestart']);
                $mon = date('m', $event['timestart']);
                $year = date('Y', $event['timestart']);

                $link = $this->jump_url."&mtype=event&id=".$event['courseid']."&day=$day&mon=$mon&year=$year";

                if ($lang)
                    $link .= "&lang=$lang";

                $linkstarget = $this->params->get( 'linkstarget' );
                            if ($linkstarget == "new")
                                     $target = " target='_blank'";
                             else $target = "";

                echo "<a $target href=\"$link\">".JHTML::_('date', $event['timestart'] , JText::_('DATE_FORMAT_LC2'))."</a>";

            ?>
            </div>
            <div class="joomdle_item_content joomdle_event_list_item_name">
            <?php echo $event['name']; ?>
            </div>
        </div>
    <?php endforeach;  //events ?>
    </div>
    <?php endif;  ?>
<?php endforeach;  //courses ?>
</div>
