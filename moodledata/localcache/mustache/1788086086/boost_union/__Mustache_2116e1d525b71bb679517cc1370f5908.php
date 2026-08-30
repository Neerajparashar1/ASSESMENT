<?php

class __Mustache_2116e1d525b71bb679517cc1370f5908 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="theme-boost-union-settingoverridenotification">
';
        $buffer .= $indent . '    <div class="row g-2 align-items-start">
';
        $buffer .= $indent . '        <div class="col-12 col-md pr-3">
';
        $buffer .= $indent . '            <strong>';
        $value = $context->find('str');
        $buffer .= $this->sectionFf2dd29c6b15f65b9d4ec005e1db840e($context, $indent, $value);
        $buffer .= '</strong><br />
';
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->find('message'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '        <div class="col-12 col-md-auto settingoverride-actions text-start text-md-end">
';
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->find('actionshtml'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function sectionFf2dd29c6b15f65b9d4ec005e1db840e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'settingoverridenotificationtitle, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'settingoverridenotificationtitle, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
