<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');

class JoomdleControllerCertificate extends JControllerLegacy
{

	function send_certificate ()
	{
		$app                = JFactory::getApplication();
		$params = $app->getParams();
        $moodle_url = $params->get( 'MOODLE_URL' );

		$cert_id =  JRequest::getVar( 'cert_id' );
		$simple =  JRequest::getVar( 'simple' );

        $email              = JRequest::getString('mailto', '', 'post');
        $sender             = JRequest::getString('sender', '', 'post');
        $from               = JRequest::getString('from', '', 'post');
        $user = JFactory::getUser();
        $username = $user->username;

        $subject_default    = JText::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_SUBJECT', $user->name);
        $subject            = JRequest::getString('subject', $subject_default, 'post');
		if (!$subject)
			$subject = $subject_default;

		$mailer = JFactory::getMailer();

        $config = JFactory::getConfig();
        $sender = array(
        $config->get( 'mailfrom' ),
        $config->get( 'fromname' ) );
    
        $mailer->setSender($sender);
        $mailer->addRecipient($email);

        $body   = JText::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_BODY', $user->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

		$session                = JFactory::getSession();
        $token = md5 ($session->getId());

        $pdf =file_get_contents ($moodle_url.'/auth/joomdle/'.$simple.'certificate_view.php?id='.$cert_id.'&certificate=1&action=review&username='.$username.'&token='.$token);
		$tmp_path = $config->get('tmp_path');
		$filename = 'certificate-'.$cert_id.'-'.$user->name.'.pdf';
        file_put_contents ($tmp_path.'/'.$filename, $pdf);
        $mailer->addAttachment($tmp_path.'/'.$filename);

        $send = $mailer->Send();
		unlink ($tmp_path.'/'.$filename);
        if ( $send !== true ) {
            JError::raiseNotice(500, JText:: _ ('COM_JOOMDLE_EMAIL_NOT_SENT'));
        } else {
?>
<div style="padding: 10px;">
    <div style="text-align:right">
        <a href="javascript: void window.close()">
            <?php echo JText::_('COM_JOOMDLE_CLOSE_WINDOW'); ?> <?php echo JHtml::_('image','mailto/close-x.png', NULL, NULL, true); ?></a>
    </div>

    <h2>
        <?php echo JText::_('COM_JOOMDLE_EMAIL_SENT'); ?>
    </h2>
</div>

<?php
        }

	}
}
