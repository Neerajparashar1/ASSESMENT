<?php

class __Mustache_188596505d4ba44dc9a364ae5a07ac93 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $value = $context->find('infobanners');
        $buffer .= $this->section081cabc8006ca6d49d8e37429c9cc279($context, $indent, $value);

        return $buffer;
    }

    private function sectionDd9a6923817dc6c2d7ead72e79024446(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'alert-{{{ bsclass }}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'alert-';
                $value = $this->resolveValue($context->find('bsclass'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC7deabd6710e4b86eaf9a304dbb70132(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'infobannerclose, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'infobannerclose, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6cc8b5bde2bccea9f9bb8ec656fad242(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <button type="button" id="themeboostunioninfobanner{{no}}close" class="close" data-dismiss="alert" data-infobanner-no="{{no}}" aria-label="{{#str}}infobannerclose, theme_boost_union{{/str}}">
                <span aria-hidden="true">&times;</span>
            </button>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <button type="button" id="themeboostunioninfobanner';
                $value = $this->resolveValue($context->find('no'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= 'close" class="close" data-dismiss="alert" data-infobanner-no="';
                $value = $this->resolveValue($context->find('no'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC7deabd6710e4b86eaf9a304dbb70132($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                <span aria-hidden="true">&times;</span>
';
                $buffer .= $indent . '            </button>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section081cabc8006ca6d49d8e37429c9cc279(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div id="themeboostunioninfobanner{{no}}" class="themeboostunioninfobanner alert {{#bsclass}}alert-{{{ bsclass }}}{{/bsclass}}" role="alert">
        {{#dismissible}}
            <button type="button" id="themeboostunioninfobanner{{no}}close" class="close" data-dismiss="alert" data-infobanner-no="{{no}}" aria-label="{{#str}}infobannerclose, theme_boost_union{{/str}}">
                <span aria-hidden="true">&times;</span>
            </button>
        {{/dismissible}}
        {{{content}}}
    </div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div id="themeboostunioninfobanner';
                $value = $this->resolveValue($context->find('no'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="themeboostunioninfobanner alert ';
                $value = $context->find('bsclass');
                $buffer .= $this->sectionDd9a6923817dc6c2d7ead72e79024446($context, $indent, $value);
                $buffer .= '" role="alert">
';
                $value = $context->find('dismissible');
                $buffer .= $this->section6cc8b5bde2bccea9f9bb8ec656fad242($context, $indent, $value);
                $buffer .= $indent . '        ';
                $value = $this->resolveValue($context->find('content'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
