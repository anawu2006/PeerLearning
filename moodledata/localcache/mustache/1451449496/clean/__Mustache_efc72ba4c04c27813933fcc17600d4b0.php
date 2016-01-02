<?php

class __Mustache_efc72ba4c04c27813933fcc17600d4b0 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<div class="alert alert-success">';
        $value = $this->resolveValue($context->find('message'), $context, $indent);
        $buffer .= $value;
        $buffer .= '</div>
';

        return $buffer;
    }
}
