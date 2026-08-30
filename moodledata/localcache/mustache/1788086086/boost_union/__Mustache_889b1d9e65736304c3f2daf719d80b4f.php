<?php

class __Mustache_889b1d9e65736304c3f2daf719d80b4f extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div id="month-navigation-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('calendarinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="controls">
';
        $buffer .= $indent . '    <div class="calendar-controls">
';
        $buffer .= $indent . '        <a';
        $buffer .= ' href="';
        $value = $this->resolveValue($context->find('previousperiodlink'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' class="arrow_link previous"';
        $buffer .= ' ';
        $value = $context->find('viewinginblock');
        $buffer .= $this->sectionAa1e8fc6f6ce94822a50a8bf271a7ea6($context, $indent, $value);
        $buffer .= ' ';
        $buffer .= ' data-year="';
        $value = $this->resolveValue($context->findDot('previousperiod.year'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-month="';
        $value = $this->resolveValue($context->findDot('previousperiod.mon'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-drop-zone="nav-link" ';
        $buffer .= ' aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section400bf0c5baed0a80d43ba846cb29a9d7($context, $indent, $value);
        $buffer .= '" ';
        $buffer .= '>
';
        $buffer .= $indent . '            <span class="arrow" aria-hidden="true">';
        $value = $this->resolveValue($context->find('larrow'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '            &nbsp;
';
        $buffer .= $indent . '            <span class="arrow_text">';
        $value = $this->resolveValue($context->find('previousperiodname'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</span>
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <span class="hide"> | </span>
';
        $value = $context->find('viewinginblock');
        $buffer .= $this->section91e662d125a5e06bee2f995b85dda114($context, $indent, $value);
        $value = $context->find('viewinginblock');
        if (empty($value)) {
            
            $buffer .= $indent . '            <h2 class="current">';
            $value = $this->resolveValue($context->find('periodname'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '</h2>
';
        }
        $buffer .= $indent . '        <span class="hide"> | </span>
';
        $buffer .= $indent . '        <a';
        $buffer .= ' href="';
        $value = $this->resolveValue($context->find('nextperiodlink'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' class="arrow_link next"';
        $buffer .= ' ';
        $value = $context->find('viewinginblock');
        $buffer .= $this->section4d8e20e6893e0efbebcacb431d775232($context, $indent, $value);
        $buffer .= ' ';
        $buffer .= ' data-year="';
        $value = $this->resolveValue($context->findDot('nextperiod.year'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-month="';
        $value = $this->resolveValue($context->findDot('nextperiod.mon'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-drop-zone="nav-link" ';
        $buffer .= ' aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section3b721fa492e6f72a4d44c2ea085aaaca($context, $indent, $value);
        $buffer .= '" ';
        $buffer .= '>
';
        $buffer .= $indent . '            <span class="arrow_text">';
        $value = $this->resolveValue($context->find('nextperiodname'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</span>
';
        $buffer .= $indent . '            &nbsp;
';
        $buffer .= $indent . '            <span class="arrow" aria-hidden="true">';
        $value = $this->resolveValue($context->find('rarrow'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->sectionD2a4d54646a4b8d37b100c1136d773d0($context, $indent, $value);

        return $buffer;
    }

    private function section400bf0c5baed0a80d43ba846cb29a9d7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'monthprevwithname, calendar, {{previousperiodname}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'monthprevwithname, calendar, ';
                $value = $this->resolveValue($context->find('previousperiodname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAa1e8fc6f6ce94822a50a8bf271a7ea6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'title="{{#str}}monthprevwithname, calendar, {{previousperiodname}}{{/str}}" ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'title="';
                $value = $context->find('str');
                $buffer .= $this->section400bf0c5baed0a80d43ba846cb29a9d7($context, $indent, $value);
                $buffer .= '" ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section91e662d125a5e06bee2f995b85dda114(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <h4 class="current">{{periodname}}</h4>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <h4 class="current">';
                $value = $this->resolveValue($context->find('periodname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</h4>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3b721fa492e6f72a4d44c2ea085aaaca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'monthnextwithname, calendar, {{nextperiodname}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'monthnextwithname, calendar, ';
                $value = $this->resolveValue($context->find('nextperiodname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4d8e20e6893e0efbebcacb431d775232(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'title="{{#str}}monthnextwithname, calendar, {{nextperiodname}}{{/str}}" ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'title="';
                $value = $context->find('str');
                $buffer .= $this->section3b721fa492e6f72a4d44c2ea085aaaca($context, $indent, $value);
                $buffer .= '" ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD2a4d54646a4b8d37b100c1136d773d0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([\'jquery\', \'core_calendar/month_navigation_drag_drop\'], function($, DragDrop) {
    var root = $(\'#month-navigation-{{uniqid}}-{{calendarinstanceid}}\');
    DragDrop.init(root);
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([\'jquery\', \'core_calendar/month_navigation_drag_drop\'], function($, DragDrop) {
';
                $buffer .= $indent . '    var root = $(\'#month-navigation-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-';
                $value = $this->resolveValue($context->find('calendarinstanceid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\');
';
                $buffer .= $indent . '    DragDrop.init(root);
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
