<?php
$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

$params = $app->getTemplate(true)->params;

// active variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// load javascript
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/theme.js');
$doc->addScript('templates/' . $this->template . '/js/jquery.mmenu.min.all.js');

// load stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/bootstrap.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/jquery.mmenu.all.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/theme.css');

// load rtl bootstrap css
JHtml::_('bootstrap.loadCss', false, $this->direction);

if ($this->countModules('right') && $this->countModules('left'))
{
	$span = "span6";
}
elseif ($this->countModules('right') && !$this->countModules('left'))
{
	$span = "span9";
}
elseif (!$this->countModules('right') && $this->countModules('left'))
{
	$span = "span9";
}
else
{
	$span = "span12";
}

// logo file or site title
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>