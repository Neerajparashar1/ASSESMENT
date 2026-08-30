<?php

class __Mustache_59db996442d2376c61c8c8dbaf9ad855 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $buffer .= $indent . '<div id="';
        $value = $this->resolveValue($context->find('id'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="theme_boost_union-loginmethod ';
        $value = $context->find('loginlayouttabs');
        $buffer .= $this->section800c7dd4c2d1000effc71026be4cfc1b($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->find('loginlayoutaccordion');
        $buffer .= $this->section3319045cd12f93fe2a7ffc2a4d8bfc62($context, $indent, $value);
        $buffer .= '" ';
        $value = $context->find('loginlayouttabs');
        $buffer .= $this->section8e3e687016a604b391e3f5cd51274fda($context, $indent, $value);
        $buffer .= '>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    ';
        $value = $context->find('loginlayoutvertical');
        $buffer .= $this->section1755c940106e5f1ba67abf741d17d71b($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '
';
        $value = $context->find('loginlayoutaccordion');
        $buffer .= $this->section715ed57befffaa68ecd3baf553ddd44c($context, $indent, $value);

        return $buffer;
    }

    private function section76ec4ac183fa48dfde766e45fb95572b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' show active';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' show active';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section800c7dd4c2d1000effc71026be4cfc1b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'tab-pane fade{{#active}} show active{{/active}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'tab-pane fade';
                $value = $context->find('active');
                $buffer .= $this->section76ec4ac183fa48dfde766e45fb95572b($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3319045cd12f93fe2a7ffc2a4d8bfc62(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'card';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'card';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8e3e687016a604b391e3f5cd51274fda(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'role="tabpanel" aria-labelledby="{{id}}-tab"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'role="tabpanel" aria-labelledby="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-tab"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1755c940106e5f1ba67abf741d17d71b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{^isfirst}}<div class="login-divider"></div>{{/isfirst}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('isfirst');
                if (empty($value)) {
                    
                    $buffer .= '<div class="login-divider"></div>';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF854d18562fade349ef0648032e403f8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-expanded="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-expanded="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section14c724f5a6859d4cc56d9befdffaeac5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'show';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'show';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section715ed57befffaa68ecd3baf553ddd44c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div class="card-header" id="{{id}}-accordion-header">
        <h5 class="mb-0">
            <button class="btn btn-link {{^active}}collapsed{{/active}}" type="button" data-toggle="collapse" data-target="#{{id}}-accordion-content" {{#active}}aria-expanded="true"{{/active}}{{^active}}aria-expanded="false"{{/active}} aria-controls="{{id}}-accordion-content">
                <span class="login-heading text-break w-100">{{label}}</span>
            </button>
        </h5>
    </div>
    <div id="{{id}}-accordion-content" class="collapse {{#active}}show{{/active}}" aria-labelledby="{{id}}-accordion-header" data-parent="#login-layout-accordion"><div class="card-body">
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div class="card-header" id="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-accordion-header">
';
                $buffer .= $indent . '        <h5 class="mb-0">
';
                $buffer .= $indent . '            <button class="btn btn-link ';
                $value = $context->find('active');
                if (empty($value)) {
                    
                    $buffer .= 'collapsed';
                }
                $buffer .= '" type="button" data-toggle="collapse" data-target="#';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-accordion-content" ';
                $value = $context->find('active');
                $buffer .= $this->sectionF854d18562fade349ef0648032e403f8($context, $indent, $value);
                $value = $context->find('active');
                if (empty($value)) {
                    
                    $buffer .= 'aria-expanded="false"';
                }
                $buffer .= ' aria-controls="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-accordion-content">
';
                $buffer .= $indent . '                <span class="login-heading text-break w-100">';
                $value = $this->resolveValue($context->find('label'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '            </button>
';
                $buffer .= $indent . '        </h5>
';
                $buffer .= $indent . '    </div>
';
                $buffer .= $indent . '    <div id="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-accordion-content" class="collapse ';
                $value = $context->find('active');
                $buffer .= $this->section14c724f5a6859d4cc56d9befdffaeac5($context, $indent, $value);
                $buffer .= '" aria-labelledby="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-accordion-header" data-parent="#login-layout-accordion"><div class="card-body">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
