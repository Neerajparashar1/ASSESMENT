<?php

class __Mustache_182553c0a049079d205a682f4826593a extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<header id="page-header" class="header-maxwidth d-print-none">
';
        $buffer .= $indent . '    <div class="w-100">
';
        $buffer .= $indent . '        <div class="d-flex flex-wrap">
';
        $value = $context->find('hasnavbar');
        $buffer .= $this->section2292e61b29f2de29daf222b96b952dad($context, $indent, $value);
        $buffer .= $indent . '            <div class="ms-auto d-flex">
';
        $buffer .= $indent . '                ';
        $value = $this->resolveValue($context->find('pageheadingbutton'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '            <div id="course-header">
';
        $buffer .= $indent . '                ';
        $value = $this->resolveValue($context->find('courseheader'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '        </div>
';
        $value = $context->find('welcomemessage');
        $buffer .= $this->sectionDb80333a3b76109fc690aae6835107c3($context, $indent, $value);
        $value = $context->find('welcomemessage');
        if (empty($value)) {
            
            $value = $context->find('courseheaderimageurl');
            if (empty($value)) {
                
                $buffer .= $indent . '                <div class="d-flex align-items-center">
';
                $buffer .= $indent . '                    <div class="me-auto d-flex flex-column">
';
                $value = $context->find('contextheader');
                $buffer .= $this->sectionF983bdf256d30909a28e847871b43518($context, $indent, $value);
                $buffer .= $indent . '                        <div>
';
                if ($partial = $this->mustache->loadPartial('core/welcome')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                            ');
                }
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                    </div>
';
                $buffer .= $indent . '                    <div class="header-actions-container ms-auto" data-region="header-actions-container">
';
                $value = $context->find('headeractions');
                $buffer .= $this->sectionA0aa4aeed786ee2e4bb362770cba57e9($context, $indent, $value);
                $buffer .= $indent . '                    </div>
';
                $buffer .= $indent . '                </div>
';
            }
            $value = $context->find('courseheaderimageurl');
            $buffer .= $this->sectionFe792b02257534e94f156d5f6dfe7699($context, $indent, $value);
        }
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</header>
';

        return $buffer;
    }

    private function section2292e61b29f2de29daf222b96b952dad(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div id="page-navbar">
                {{{navbar}}}
            </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div id="page-navbar">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->find('navbar'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF9f4d608c7eb0c7131886d50a0821e9d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div>
                        {{{contextheader}}}
                    </div>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div>
';
                $buffer .= $indent . '                        ';
                $value = $this->resolveValue($context->find('contextheader'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8925f1307c9e659ed6fe23c2ea522d1e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="header-action ms-2">{{{.}}}</div>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="header-action ms-2">';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDb80333a3b76109fc690aae6835107c3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="d-flex align-items-center">
                <div class="me-auto d-flex flex-column">
                    {{#contextheader}}
                    <div>
                        {{{contextheader}}}
                    </div>
                    {{/contextheader}}
                    <div>
                        {{> core/welcome }}
                    </div>
                </div>
                <div class="header-actions-container ms-auto" data-region="header-actions-container">
                    {{#headeractions}}
                    <div class="header-action ms-2">{{{.}}}</div>
                    {{/headeractions}}
                </div>
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="d-flex align-items-center">
';
                $buffer .= $indent . '                <div class="me-auto d-flex flex-column">
';
                $value = $context->find('contextheader');
                $buffer .= $this->sectionF9f4d608c7eb0c7131886d50a0821e9d($context, $indent, $value);
                $buffer .= $indent . '                    <div>
';
                if ($partial = $this->mustache->loadPartial('core/welcome')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                        ');
                }
                $buffer .= $indent . '                    </div>
';
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '                <div class="header-actions-container ms-auto" data-region="header-actions-container">
';
                $value = $context->find('headeractions');
                $buffer .= $this->section8925f1307c9e659ed6fe23c2ea522d1e($context, $indent, $value);
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF983bdf256d30909a28e847871b43518(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <div>
                            {{{contextheader}}}
                        </div>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <div>
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->find('contextheader'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA0aa4aeed786ee2e4bb362770cba57e9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <div class="header-action ms-2">{{{.}}}</div>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <div class="header-action ms-2">';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section788326a3090c2b5e475becf750a48ffd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div>
                                {{{contextheader}}}
                            </div>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <div>
';
                $buffer .= $indent . '                                ';
                $value = $this->resolveValue($context->find('contextheader'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1f598c359f0fd809b4c5c86a2db7b756(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div class="header-action ms-2">{{{.}}}</div>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <div class="header-action ms-2">';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section73b0baa7177d3f65307f4b42b0de092b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'min-height: {{{.}}};';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'min-height: ';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= ';';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section359bd57f4b5a30ac858643515fd30e46(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'background-position: {{{.}}};';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'background-position: ';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= ';';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section41b419097521fa4f9167e459f92218a0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="d-flex align-items-center">
                        <div class="me-auto d-flex flex-column">
                            {{#contextheader}}
                            <div>
                                {{{contextheader}}}
                            </div>
                            {{/contextheader}}
                            <div>
                                {{> core/welcome }}
                            </div>
                        </div>
                        <div class="header-actions-container ms-auto" data-region="header-actions-container">
                            {{#headeractions}}
                            <div class="header-action ms-2">{{{.}}}</div>
                            {{/headeractions}}
                        </div>
                    </div>
                    <div id="courseheaderimage" class="p-3 mb-3"
                         style="background-image: url(\'{{{courseheaderimageurl}}}\');
                             {{#courseheaderimageheight}}min-height: {{{.}}};{{/courseheaderimageheight}}
                             {{#courseheaderimageposition}}background-position: {{{.}}};{{/courseheaderimageposition}}">
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="d-flex align-items-center">
';
                $buffer .= $indent . '                        <div class="me-auto d-flex flex-column">
';
                $value = $context->find('contextheader');
                $buffer .= $this->section788326a3090c2b5e475becf750a48ffd($context, $indent, $value);
                $buffer .= $indent . '                            <div>
';
                if ($partial = $this->mustache->loadPartial('core/welcome')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                ');
                }
                $buffer .= $indent . '                            </div>
';
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                        <div class="header-actions-container ms-auto" data-region="header-actions-container">
';
                $value = $context->find('headeractions');
                $buffer .= $this->section1f598c359f0fd809b4c5c86a2db7b756($context, $indent, $value);
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                    </div>
';
                $buffer .= $indent . '                    <div id="courseheaderimage" class="p-3 mb-3"
';
                $buffer .= $indent . '                         style="background-image: url(\'';
                $value = $this->resolveValue($context->find('courseheaderimageurl'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '\');
';
                $buffer .= $indent . '                             ';
                $value = $context->find('courseheaderimageheight');
                $buffer .= $this->section73b0baa7177d3f65307f4b42b0de092b($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                             ';
                $value = $context->find('courseheaderimageposition');
                $buffer .= $this->section359bd57f4b5a30ac858643515fd30e46($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBbf0165ccd181b31c5b6ca8b6d9ea9e6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'courseheaderimage-{{{.}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'courseheaderimage-';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section46a1a6e89e1d64884ff566741bfaec1f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <div>
                                    {{{contextheader}}}
                                </div>
                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <div>
';
                $buffer .= $indent . '                                    ';
                $value = $this->resolveValue($context->find('contextheader'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                                </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB87d28e009f79162190333c69a2a3539(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <div class="header-action ms-2">{{{.}}}</div>
                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <div class="header-action ms-2">';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFe792b02257534e94f156d5f6dfe7699(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#courseheaderimagelayoutheadingabove}}
                    <div class="d-flex align-items-center">
                        <div class="me-auto d-flex flex-column">
                            {{#contextheader}}
                            <div>
                                {{{contextheader}}}
                            </div>
                            {{/contextheader}}
                            <div>
                                {{> core/welcome }}
                            </div>
                        </div>
                        <div class="header-actions-container ms-auto" data-region="header-actions-container">
                            {{#headeractions}}
                            <div class="header-action ms-2">{{{.}}}</div>
                            {{/headeractions}}
                        </div>
                    </div>
                    <div id="courseheaderimage" class="p-3 mb-3"
                         style="background-image: url(\'{{{courseheaderimageurl}}}\');
                             {{#courseheaderimageheight}}min-height: {{{.}}};{{/courseheaderimageheight}}
                             {{#courseheaderimageposition}}background-position: {{{.}}};{{/courseheaderimageposition}}">
                    </div>
                {{/courseheaderimagelayoutheadingabove}}
                {{^courseheaderimagelayoutheadingabove}}
                    <div id="courseheaderimage" class="p-3 mb-3 {{#courseheaderimagelayoutstackedclass}}courseheaderimage-{{{.}}}{{/courseheaderimagelayoutstackedclass}}"
                            style="background-image: url(\'{{{courseheaderimageurl}}}\');
                                    {{#courseheaderimageheight}}min-height: {{{.}}};{{/courseheaderimageheight}}
                                    {{#courseheaderimageposition}}background-position: {{{.}}};{{/courseheaderimageposition}}">
                        <div class="d-flex align-items-center">
                            <div class="me-auto d-flex flex-column">
                                {{#contextheader}}
                                <div>
                                    {{{contextheader}}}
                                </div>
                                {{/contextheader}}
                                <div>
                                    {{> core/welcome }}
                                </div>
                            </div>
                            <div class="header-actions-container ms-auto" data-region="header-actions-container">
                                {{#headeractions}}
                                <div class="header-action ms-2">{{{.}}}</div>
                                {{/headeractions}}
                            </div>
                        </div>
                    </div>
                {{/courseheaderimagelayoutheadingabove}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('courseheaderimagelayoutheadingabove');
                $buffer .= $this->section41b419097521fa4f9167e459f92218a0($context, $indent, $value);
                $value = $context->find('courseheaderimagelayoutheadingabove');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                    <div id="courseheaderimage" class="p-3 mb-3 ';
                    $value = $context->find('courseheaderimagelayoutstackedclass');
                    $buffer .= $this->sectionBbf0165ccd181b31c5b6ca8b6d9ea9e6($context, $indent, $value);
                    $buffer .= '"
';
                    $buffer .= $indent . '                            style="background-image: url(\'';
                    $value = $this->resolveValue($context->find('courseheaderimageurl'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '\');
';
                    $buffer .= $indent . '                                    ';
                    $value = $context->find('courseheaderimageheight');
                    $buffer .= $this->section73b0baa7177d3f65307f4b42b0de092b($context, $indent, $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                                    ';
                    $value = $context->find('courseheaderimageposition');
                    $buffer .= $this->section359bd57f4b5a30ac858643515fd30e46($context, $indent, $value);
                    $buffer .= '">
';
                    $buffer .= $indent . '                        <div class="d-flex align-items-center">
';
                    $buffer .= $indent . '                            <div class="me-auto d-flex flex-column">
';
                    $value = $context->find('contextheader');
                    $buffer .= $this->section46a1a6e89e1d64884ff566741bfaec1f($context, $indent, $value);
                    $buffer .= $indent . '                                <div>
';
                    if ($partial = $this->mustache->loadPartial('core/welcome')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                                    ');
                    }
                    $buffer .= $indent . '                                </div>
';
                    $buffer .= $indent . '                            </div>
';
                    $buffer .= $indent . '                            <div class="header-actions-container ms-auto" data-region="header-actions-container">
';
                    $value = $context->find('headeractions');
                    $buffer .= $this->sectionB87d28e009f79162190333c69a2a3539($context, $indent, $value);
                    $buffer .= $indent . '                            </div>
';
                    $buffer .= $indent . '                        </div>
';
                    $buffer .= $indent . '                    </div>
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
