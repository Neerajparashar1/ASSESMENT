<?php

class __Mustache_62c9270ed8d9a5bc3eda9151cb312bae extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $value = $context->find('showjavascriptdisabledhint');
        $buffer .= $this->section03cee04a1ab556331bff0420ed8709cd($context, $indent, $value);

        return $buffer;
    }

    private function sectionE969f9f2741e2ccbc9f0842f71a30b21(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'javascriptdisabledhinttext,theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'javascriptdisabledhinttext,theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section03cee04a1ab556331bff0420ed8709cd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <noscript>
        <div id="javascriptdisabledhint" class="alert alert-danger mt-2 mb-5 p-4" role="alert">
            {{#str}}javascriptdisabledhinttext,theme_boost_union{{/str}}
        </div>
    </noscript>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <noscript>
';
                $buffer .= $indent . '        <div id="javascriptdisabledhint" class="alert alert-danger mt-2 mb-5 p-4" role="alert">
';
                $buffer .= $indent . '            ';
                $value = $context->find('str');
                $buffer .= $this->sectionE969f9f2741e2ccbc9f0842f71a30b21($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $buffer .= $indent . '    </noscript>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
