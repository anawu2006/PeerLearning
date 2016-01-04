<?php

class __Mustache_85827ab6669cf66e6d2756de0ff13754 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<span style="display:inline-block;" class="';
        $value = $this->resolveValue($context->find('spanclass'), $context, $indent);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '">&nbsp;';
        $value = $this->resolveValue($context->find('rolename'), $context, $indent);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '&nbsp;
';
        // 'imageurl' section
        $value = $context->find('imageurl');
        $buffer .= $this->section223ac3bb1d3c1761ed86b6221902bc78($context, $indent, $value);
        $buffer .= $indent . '</span>
';

        return $buffer;
    }

    private function section223ac3bb1d3c1761ed86b6221902bc78(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <a href="{{adminurl}}roles/permissions.php" class="{{linkclass}}" data-role-id="{{roleid}}" data-action="{{action}}">
            <img src="{{imageurl}}" alt="{{action}}" />
        </a>
    ';
            $result = call_user_func($value, $source, $this->lambdaHelper);
            if (strpos($result, '{{') === false) {
                $buffer .= $result;
            } else {
                $buffer .= $this->mustache
                    ->loadLambda((string) $result)
                    ->renderInternal($context);
            }
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <a href="';
                $value = $this->resolveValue($context->find('adminurl'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= 'roles/permissions.php" class="';
                $value = $this->resolveValue($context->find('linkclass'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '" data-role-id="';
                $value = $this->resolveValue($context->find('roleid'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '" data-action="';
                $value = $this->resolveValue($context->find('action'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '">
';
                $buffer .= $indent . '            <img src="';
                $value = $this->resolveValue($context->find('imageurl'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '" alt="';
                $value = $this->resolveValue($context->find('action'), $context, $indent);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '" />
';
                $buffer .= $indent . '        </a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
