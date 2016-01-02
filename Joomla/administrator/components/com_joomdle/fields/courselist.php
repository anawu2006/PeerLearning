<?php
/**
* @copyright    Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license      GNU/GPL
*/
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

JFormHelper::loadFieldClass('list');
/**
 * Renders a multiple item select element
 *
 */
 
class JFormFieldCourseList extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        public    $type = 'CourseList';

		protected $multiple = true;

		protected function getInput()
		{
        // Initialize variables.
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

		$this->multiple = true;

        $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
        }
        // Create a regular list.
        else {
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }

        return implode($html);
    }


		function JFormFieldCourseListx ()
		{
			$this->multiple = true;
		}
 
 //       function getInput($name, $value, &$node, $control_name)
        function getOptions()
        {
                // Base name of the HTML control.
            //    $ctrl  = $control_name .'['. $name .']';
 
		$courses = JoomdleHelperContent::getCourseList (0);
                // Construct an array of the HTML OPTION statements.
		$options = array ();
		$c = array ();
		foreach ($courses as $course)
                {
                        $val = $course['remoteid'];
                        $text = $course['fullname'];
                        $options[] = JHtml::_('select.option', $val, $text);

				//		$op->id = $course['remoteid'];
				//		$op->text = $course['fullname'];
				//		$c[] = $op;
                }
 
		$options = array_merge(parent::getOptions(), $options);

		return $options;
                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = $node->attributes( 'size' )) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = $node->attributes( 'class' )) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = $node->attributes( 'multiple' ))
                {
                        $attribs       .= ' multiple="multiple"';
                        $ctrl          .= '[]';
                }
 
                // Render the HTML SELECT list.
                return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );

        }
}

?>
