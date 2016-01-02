<?php
/**
 * @version     $Id: default.php 21321 2011-05-11 01:05:59Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.getElementById('mailtoForm');

        // do field validation
        if (form.mailto.value == "" || form.from.value == "") {
            alert('<?php echo JText::_('COM_JOOMDLE_EMAIL_ERR_NOINFO'); ?>');
            return false;
        }
        form.submit();
    }
</script>
<?php
$data   = $this->get('data');
?>

<div id="mailto-window">
    <h2>
        <?php echo JText::_('COM_JOOMDLE_EMAIL_CERTIFICATE'); ?>
    </h2>
    <div class="mailto-close">
        <a href="javascript: void window.close()" title="<?php echo JText::_('COM_JOOMDLE_CLOSE_WINDOW'); ?>">
         <span><?php echo JText::_('COM_JOOMDLE_CLOSE_WINDOW'); ?> </span></a>
    </div>

    <form action="<?php echo JURI::base() ?>index.php" id="mailtoForm" method="post">
        <div class="formelm">
            <label for="mailto_field"><?php echo JText::_('COM_JOOMDLE_EMAIL_TO'); ?></label>
            <input type="text" id="mailto_field" name="mailto" class="inputbox" size="25" value="<?php echo $data->mailto ?>"/>
        </div>
        <div class="formelm">
            <label for="sender_field">
            <?php echo JText::_('COM_JOOMDLE_SENDER'); ?></label>
            <input type="text" id="sender_field" name="sender" class="inputbox" value="<?php echo $data->sender ?>" size="25" />
        </div>
        <div class="formelm">
            <label for="from_field">
            <?php echo JText::_('COM_JOOMDLE_YOUR_EMAIL'); ?></label>
            <input type="text" id="from_field" name="from" class="inputbox" value="<?php echo $data->from ?>" size="25" />
        </div>
        <div class="formelm">
            <label for="subject_field">
            <?php echo JText::_('COM_JOOMDLE_SUBJECT'); ?></label>
            <input type="text" id="subject_field" name="subject" class="inputbox" value="<?php echo $data->subject ?>" size="25" />
        </div>
        <p>
            <button class="button" onclick="return Joomla.submitbutton('send');">
                <?php echo JText::_('COM_JOOMDLE_SEND'); ?>
            </button>
            <button class="button" onclick="window.close();return false;">
                <?php echo JText::_('COM_JOOMDLE_CANCEL'); ?>
            </button>
        </p>
        <input type="hidden" name="layout" value="<?php echo $this->getLayout();?>" />
        <input type="hidden" name="option" value="com_joomdle" />
        <input type="hidden" name="task" value="certificate.send_certificate" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="cert_id" value="<?php echo $data->cert_id; ?>" />
        <input type="hidden" name="simple" value="<?php echo $this->simple; ?>" />
        <?php echo JHtml::_('form.token'); ?>

    </form>
</div>
