<?php

class __Mustache_3acf53928620069435445106819e8bc6 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="theme-boost-union-recommendationnotification">
';
        $buffer .= $indent . '    <div class="row g-2 align-items-start">
';
        $buffer .= $indent . '        <div class="col-12 col-md pr-3">
';
        $buffer .= $indent . '            <strong>';
        $value = $context->find('str');
        $buffer .= $this->section5c2301f830ec8aca5c9a0822bb8623e5($context, $indent, $value);
        $buffer .= ': ';
        $value = $this->resolveValue($context->find('recommendationtitle'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</strong><br />
';
        $buffer .= $indent . '            <span class="badge ';
        $value = $this->resolveValue($context->find('statusbadgeclass'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= ' me-1">';
        $value = $this->resolveValue($context->find('statuslabel'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</span>';
        $value = $this->resolveValue($context->find('recommendationsummary'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '        <div class="col-12 col-md-auto recommendations-actions text-start text-md-end">
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

    private function section5c2301f830ec8aca5c9a0822bb8623e5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'recommendationsnotificationtitle, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'recommendationsnotificationtitle, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
