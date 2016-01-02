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
 
class JFormFieldQuestioncategoryList extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        var    $_name = 'QuestionCategoryList';
 
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

        $this->multiple = false;

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


		function getOptions()
        {
			$cats = $this->getCats ();
			return $cats;
		}


		function getCats ($options = array(), $level = 0)
        {
                $cats = JoomdleHelperContent::call_method ('quiz_get_question_categories');

                if (!is_array ($cats))
                        return $options;

                foreach ($cats as $cat)
                {
						$val = $cat['id'];
						$text = $cat['name'];
                        for ($i = 0; $i < $level; $i++)
                                $text = "-".$text;
                        $options[] = JHTML::_('select.option', $val, $text);
                }

                return $options;
        }

}

?>
