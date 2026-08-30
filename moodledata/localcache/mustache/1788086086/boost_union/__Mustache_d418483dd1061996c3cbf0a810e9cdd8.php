<?php

class __Mustache_d418483dd1061996c3cbf0a810e9cdd8 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<nav class="moremenu navigation">
';
        $buffer .= $indent . '    <ul id="moremenu-';
        $value = $this->resolveValue($context->find('moremenuid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('navbarstyle'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" role="';
        $value = $context->find('istablist');
        $buffer .= $this->sectionE8d02550eef8dd78cfe22b730ceb5426($context, $indent, $value);
        $value = $context->find('istablist');
        if (empty($value)) {
            
            $buffer .= 'menubar';
        }
        $buffer .= '" class="nav more-nav ';
        $value = $this->resolveValue($context->find('navbarstyle'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $value = $context->find('nodecollection');
        $buffer .= $this->section1d31938f1cb388cf82cc153df7f564a2($context, $indent, $value);
        $value = $context->find('nodearray');
        $buffer .= $this->sectionD5184d2201ba368930ab2c2fcc53f39f($context, $indent, $value);
        $value = $context->find('drawer');
        if (empty($value)) {
            
            $buffer .= $indent . '            <li role="none" class="nav-item dropdown dropdownmoremenu d-none" data-region="morebutton">
';
            $buffer .= $indent . '                <a class="dropdown-toggle nav-link ';
            $value = $context->find('isactive');
            $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
            $buffer .= '" href="#" id="moremenu-dropdown-';
            $value = $this->resolveValue($context->find('moremenuid'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '" role="';
            $value = $context->find('istablist');
            $buffer .= $this->sectionC225dbeabea012d1739058de23648ffe($context, $indent, $value);
            $value = $context->find('istablist');
            if (empty($value)) {
                
                $buffer .= 'menuitem';
            }
            $buffer .= '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" tabindex="-1">
';
            $buffer .= $indent . '                    ';
            $value = $context->find('str');
            $buffer .= $this->section5575f70c07cf53c82349ae8d67eb5e68($context, $indent, $value);
            $buffer .= '
';
            $buffer .= $indent . '                </a>
';
            $buffer .= $indent . '                <ul class="dropdown-menu dropdown-menu-left" data-region="moredropdown" aria-labelledby="moremenu-dropdown-';
            $value = $this->resolveValue($context->find('moremenuid'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '" role="menu">
';
            $buffer .= $indent . '                </ul>
';
            $buffer .= $indent . '            </li>
';
        }
        $value = $context->find('drawer');
        $buffer .= $this->sectionDf6d136f92745c67ac6e0ca6ba93ba46($context, $indent, $value);
        $buffer .= $indent . '    </ul>
';
        $buffer .= $indent . '</nav>
';
        $value = $context->find('js');
        $buffer .= $this->section959b7a1506f4d5c3c0b4e21402dfcca2($context, $indent, $value);

        return $buffer;
    }

    private function sectionE8d02550eef8dd78cfe22b730ceb5426(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'tablist';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'tablist';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section71e0bd77552c30eb67db8ce19bbe2aab(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{> core/moremenu_children}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/moremenu_children')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1d31938f1cb388cf82cc153df7f564a2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{#children}}
                {{> core/moremenu_children}}
            {{/children}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('children');
                $buffer .= $this->section71e0bd77552c30eb67db8ce19bbe2aab($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0c8a12e08ab0bbebb5cb460c7f7c9429(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{> theme_boost_union/smartmenus-cardmenu-children}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/smartmenus-cardmenu-children')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD5184d2201ba368930ab2c2fcc53f39f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{!-- Menu with card style --}}
            {{#card}}
                {{> theme_boost_union/smartmenus-cardmenu-children}}
            {{/card}}
            {{!-- Menu with list style --}}
            {{^card}}
                {{> theme_boost_union/smartmenus-moremenu-children}}
            {{/card}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('card');
                $buffer .= $this->section0c8a12e08ab0bbebb5cb460c7f7c9429($context, $indent, $value);
                $value = $context->find('card');
                if (empty($value)) {
                    
                    if ($partial = $this->mustache->loadPartial('theme_boost_union/smartmenus-moremenu-children')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5749c750acb0d7477dd5257d00cc6d53(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'active';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'active';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC225dbeabea012d1739058de23648ffe(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'tab';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'tab';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5575f70c07cf53c82349ae8d67eb5e68(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'moremenu, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'moremenu, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8b6bedbca7f2a24b1e454048196e50c9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' i/menu, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' i/menu, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB88b20c96dd523877b35fd7e4389a3fd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'sidepanel, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'sidepanel, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDf6d136f92745c67ac6e0ca6ba93ba46(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <li role="none" class="nav-item dropdown dropdownmoremenu d-none" data-region="morebutton">
                <button class="navbar-toggler aabtn d-block d-md-none px-1 my-1 border-0" data-toggler="drawers" data-action="toggle" data-target="theme_boost-drawers-primary">
                    {{#pix}} i/menu, core {{/pix}}
                    {{#str}}moremenu, core{{/str}}
                    <span class="sr-only">{{#str}}sidepanel, core{{/str}}</span>
                </button>
                <div class="hidden">
                    <a class="dropdown-toggle nav-link {{#isactive}}active{{/isactive}}" href="#" id="moremenu-dropdown-{{moremenuid}}" role="{{#istablist}}tab{{/istablist}}{{^istablist}}menuitem{{/istablist}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" tabindex="-1">
                        {{#str}}moremenu, core{{/str}}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-left" data-region="moredropdown" aria-labelledby="moremenu-dropdown-{{moremenuid}}" role="menu">
                    </ul>
                </div>
            </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <li role="none" class="nav-item dropdown dropdownmoremenu d-none" data-region="morebutton">
';
                $buffer .= $indent . '                <button class="navbar-toggler aabtn d-block d-md-none px-1 my-1 border-0" data-toggler="drawers" data-action="toggle" data-target="theme_boost-drawers-primary">
';
                $buffer .= $indent . '                    ';
                $value = $context->find('pix');
                $buffer .= $this->section8b6bedbca7f2a24b1e454048196e50c9($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $context->find('str');
                $buffer .= $this->section5575f70c07cf53c82349ae8d67eb5e68($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionB88b20c96dd523877b35fd7e4389a3fd($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                </button>
';
                $buffer .= $indent . '                <div class="hidden">
';
                $buffer .= $indent . '                    <a class="dropdown-toggle nav-link ';
                $value = $context->find('isactive');
                $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                $buffer .= '" href="#" id="moremenu-dropdown-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="';
                $value = $context->find('istablist');
                $buffer .= $this->sectionC225dbeabea012d1739058de23648ffe($context, $indent, $value);
                $value = $context->find('istablist');
                if (empty($value)) {
                    
                    $buffer .= 'menuitem';
                }
                $buffer .= '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" tabindex="-1">
';
                $buffer .= $indent . '                        ';
                $value = $context->find('str');
                $buffer .= $this->section5575f70c07cf53c82349ae8d67eb5e68($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                    <ul class="dropdown-menu dropdown-menu-left" data-region="moredropdown" aria-labelledby="moremenu-dropdown-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menu">
';
                $buffer .= $indent . '                    </ul>
';
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '            </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section959b7a1506f4d5c3c0b4e21402dfcca2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    require([\'core/moremenu\'], function(moremenu) {
        moremenu(document.querySelector(\'#moremenu-{{moremenuid}}-{{navbarstyle}}\'));
    });
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    require([\'core/moremenu\'], function(moremenu) {
';
                $buffer .= $indent . '        moremenu(document.querySelector(\'#moremenu-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-';
                $value = $this->resolveValue($context->find('navbarstyle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\'));
';
                $buffer .= $indent . '    });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
