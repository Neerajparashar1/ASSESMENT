<?php

class __Mustache_ce0f0a852c853af5fb482e1c093e635b extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $value = $context->find('menubar');
        $buffer .= $this->sectionA86e4680627f18951c031c8d8f0bb31e($context, $indent, $value);
        $buffer .= $indent . '<nav class="navbar fixed-top ';
        $value = $this->resolveValue($context->find('navbarcolorclasses'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= ' navbar-expand ';
        $value = $context->findDot('bottombar.drawer');
        $buffer .= $this->sectionE71fbacf228494f6666a87d67d3eda03($context, $indent, $value);
        $buffer .= '" aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section1880a930791c830b67e23ff34b5a4123($context, $indent, $value);
        $buffer .= '">
';
        $buffer .= $indent . '    <div class="container-fluid">
';
        $value = $context->findDot('bottombar.drawer');
        if (empty($value)) {
            
            $buffer .= $indent . '        <button class="navbar-toggler aabtn d-block d-md-none px-1 my-1 border-0" data-toggler="drawers" data-action="toggle" data-target="theme_boost-drawers-primary">
';
            $buffer .= $indent . '            <span class="navbar-toggler-icon"></span>
';
            $buffer .= $indent . '            <span class="sr-only">';
            $value = $context->find('str');
            $buffer .= $this->sectionB88b20c96dd523877b35fd7e4389a3fd($context, $indent, $value);
            $buffer .= '</span>
';
            $buffer .= $indent . '        </button>
';
        }
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <a href="';
        $value = $context->find('alternativelogolinkurl');
        $buffer .= $this->section2f14a6bef41f8e817b5b7ce1fe9aba61($context, $indent, $value);
        $value = $context->find('alternativelogolinkurl');
        if (empty($value)) {
            
            $value = $this->resolveValue($context->findDot('config.homeurl'), $context);
            $buffer .= ($value === null ? '' : $value);
        }
        $buffer .= '" class="navbar-brand align-items-center m-0 me-4 p-0 aabtn ';
        $value = $context->findDot('bottombar.drawer');
        $buffer .= $this->section9fcde34d922cc38cdd0b275e038ae6e0($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->findDot('bottombar.drawer');
        if (empty($value)) {
            
            $buffer .= ' d-none d-md-flex ';
        }
        $buffer .= ' ">
';
        $buffer .= $indent . '
';
        $value = $context->findDot('output.should_display_navbar_logo');
        $buffer .= $this->section461071542e460fd411dfc650298f20d9($context, $indent, $value);
        $value = $context->findDot('output.should_display_navbar_logo');
        if (empty($value)) {
            
            $buffer .= $indent . '                <span class="sitename">';
            $value = $this->resolveValue($context->find('sitename'), $context);
            $buffer .= ($value === null ? '' : $value);
            $buffer .= '</span>
';
        }
        $buffer .= $indent . '        </a>
';
        $value = $context->find('primarymoremenu');
        $buffer .= $this->section07e819b1f3f35b4c60bd7e2c826e6088($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->findDot('output.page_heading_menu');
        $buffer .= $this->sectionF1fd492313ef64942639bd2a69db2fa4($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <div id="usernavigation" class="navbar-nav ms-auto h-100">
';
        $value = $context->findDot('output.search_box');
        $buffer .= $this->section8759d71b90e6496204ea4d094c43fb8c($context, $indent, $value);
        $value = $context->find('langmenu');
        $buffer .= $this->section106b751726c18d9d0a2600f98b963484($context, $indent, $value);
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->findDot('output.navbar_plugin_output'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '            <div class="d-flex align-items-stretch usermenu-container" data-region="usermenu">
';
        $value = $context->find('usermenu');
        $buffer .= $this->sectionA9e23d79a85004416d048e2cac960de4($context, $indent, $value);
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->findDot('output.edit_switch'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '
';
        $value = $context->find('userisediting');
        if (empty($value)) {
            
            $value = $context->findDot('regions.offcanvas.hasblocks');
            $buffer .= $this->sectionA4473f7247341db8d5adc32b7a399b23($context, $indent, $value);
        }
        $value = $context->find('userisediting');
        $buffer .= $this->section596dfcf3c1ca57510ee226b25154bdbb($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</nav>
';
        $buffer .= $indent . '
';
        if ($partial = $this->mustache->loadPartial('theme_boost/primary-drawer-mobile')) {
            $buffer .= $partial->renderInternal($context);
        }

        return $buffer;
    }

    private function sectionE71fbacf228494f6666a87d67d3eda03(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' smartmenu-bottom-navigation ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' smartmenu-bottom-navigation ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA86e4680627f18951c031c8d8f0bb31e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<nav class="menubar fixed-top boost-union-menubar navbar navbar-expand {{#bottombar.drawer}} smartmenu-bottom-navigation {{/bottombar.drawer}} {{{menubar.classes}}}">
    {{> core/moremenu}}
</nav>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<nav class="menubar fixed-top boost-union-menubar navbar navbar-expand ';
                $value = $context->findDot('bottombar.drawer');
                $buffer .= $this->sectionE71fbacf228494f6666a87d67d3eda03($context, $indent, $value);
                $buffer .= ' ';
                $value = $this->resolveValue($context->findDot('menubar.classes'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '">
';
                if ($partial = $this->mustache->loadPartial('core/moremenu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $buffer .= $indent . '</nav>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1880a930791c830b67e23ff34b5a4123(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'sitemenubar, admin';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'sitemenubar, admin';
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

    private function section2f14a6bef41f8e817b5b7ce1fe9aba61(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{alternativelogolinkurl}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('alternativelogolinkurl'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9fcde34d922cc38cdd0b275e038ae6e0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' d-flex ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' d-flex ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section461071542e460fd411dfc650298f20d9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <img src="{{output.get_compact_logo_url}}" class="logo me-1" alt="{{sitename}}">
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <img src="';
                $value = $this->resolveValue($context->findDot('output.get_compact_logo_url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="logo me-1" alt="';
                $value = $this->resolveValue($context->find('sitename'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section07e819b1f3f35b4c60bd7e2c826e6088(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="primary-navigation">
                {{> core/moremenu}}
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="primary-navigation">
';
                if ($partial = $this->mustache->loadPartial('core/moremenu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF1fd492313ef64942639bd2a69db2fa4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <ul class="navbar-nav d-none d-md-flex my-1 px-1">
                <!-- page_heading_menu -->
                {{{ output.page_heading_menu }}}
            </ul>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <ul class="navbar-nav d-none d-md-flex my-1 px-1">
';
                $buffer .= $indent . '                <!-- page_heading_menu -->
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('output.page_heading_menu'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </ul>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8759d71b90e6496204ea4d094c43fb8c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{{ output.search_box }}}
                <div class="divider border-start h-75 align-self-center mx-1"></div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('output.search_box'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                <div class="divider border-start h-75 align-self-center mx-1"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section106b751726c18d9d0a2600f98b963484(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{> theme_boost/language_menu }}
                <div class="divider border-start h-75 align-self-center mx-1"></div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost/language_menu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $buffer .= $indent . '                <div class="divider border-start h-75 align-self-center mx-1"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA9e23d79a85004416d048e2cac960de4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{> core/user_menu }}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/user_menu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF746acc33b081ab3a5b4a3745b767fae(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'openoffcanvas, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'openoffcanvas, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA4473f7247341db8d5adc32b7a399b23(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="divider border-start h-75 align-self-center mx-1"></div>
                    <div class="drawer-offcanvas-toggle d-print-none">
                        <button
                            class="btn"
                            id="theme_boost_union-offcanvas-btn"
                            data-toggler="drawers"
                            title="{{#str}}openoffcanvas, theme_boost_union{{/str}}"
                            data-action="toggle"
                            data-target="theme_boost_union-drawers-offcanvas"
                            data-toggle="tooltip"
                            data-placement="bottom"
                        >
                            <span class="sr-only">{{#str}}openoffcanvas, theme_boost_union{{/str}}</span>
                            <i class="fa fa-grip"></i>
                        </button>
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="divider border-start h-75 align-self-center mx-1"></div>
';
                $buffer .= $indent . '                    <div class="drawer-offcanvas-toggle d-print-none">
';
                $buffer .= $indent . '                        <button
';
                $buffer .= $indent . '                            class="btn"
';
                $buffer .= $indent . '                            id="theme_boost_union-offcanvas-btn"
';
                $buffer .= $indent . '                            data-toggler="drawers"
';
                $buffer .= $indent . '                            title="';
                $value = $context->find('str');
                $buffer .= $this->sectionF746acc33b081ab3a5b4a3745b767fae($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                            data-action="toggle"
';
                $buffer .= $indent . '                            data-target="theme_boost_union-drawers-offcanvas"
';
                $buffer .= $indent . '                            data-toggle="tooltip"
';
                $buffer .= $indent . '                            data-placement="bottom"
';
                $buffer .= $indent . '                        >
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionF746acc33b081ab3a5b4a3745b767fae($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                            <i class="fa fa-grip"></i>
';
                $buffer .= $indent . '                        </button>
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section09b0df45bfb5d58d4a8556c0245592e6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="divider border-start h-75 align-self-center mx-1"></div>
                    <div class="drawer-offcanvas-toggle d-print-none">
                        <button
                            class="btn"
                            id="theme_boost_union-offcanvas-btn"
                            data-toggle="collapse"
                            title="{{#str}}openoffcanvas, theme_boost_union{{/str}}"
                            data-target="#theme-block-region-offcanvas-editing"
                            aria-expanded="false"
                            aria-controls="theme-block-region-offcanvas-editing"
                        >
                            <span class="sr-only">{{#str}}openoffcanvas, theme_boost_union{{/str}}</span>
                            <i class="fa fa-grip"></i>
                        </button>
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="divider border-start h-75 align-self-center mx-1"></div>
';
                $buffer .= $indent . '                    <div class="drawer-offcanvas-toggle d-print-none">
';
                $buffer .= $indent . '                        <button
';
                $buffer .= $indent . '                            class="btn"
';
                $buffer .= $indent . '                            id="theme_boost_union-offcanvas-btn"
';
                $buffer .= $indent . '                            data-toggle="collapse"
';
                $buffer .= $indent . '                            title="';
                $value = $context->find('str');
                $buffer .= $this->sectionF746acc33b081ab3a5b4a3745b767fae($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                            data-target="#theme-block-region-offcanvas-editing"
';
                $buffer .= $indent . '                            aria-expanded="false"
';
                $buffer .= $indent . '                            aria-controls="theme-block-region-offcanvas-editing"
';
                $buffer .= $indent . '                        >
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionF746acc33b081ab3a5b4a3745b767fae($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                            <i class="fa fa-grip"></i>
';
                $buffer .= $indent . '                        </button>
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section596dfcf3c1ca57510ee226b25154bdbb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#regions.offcanvas.hasblocks}}
                    <div class="divider border-start h-75 align-self-center mx-1"></div>
                    <div class="drawer-offcanvas-toggle d-print-none">
                        <button
                            class="btn"
                            id="theme_boost_union-offcanvas-btn"
                            data-toggle="collapse"
                            title="{{#str}}openoffcanvas, theme_boost_union{{/str}}"
                            data-target="#theme-block-region-offcanvas-editing"
                            aria-expanded="false"
                            aria-controls="theme-block-region-offcanvas-editing"
                        >
                            <span class="sr-only">{{#str}}openoffcanvas, theme_boost_union{{/str}}</span>
                            <i class="fa fa-grip"></i>
                        </button>
                    </div>
                {{/regions.offcanvas.hasblocks}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->findDot('regions.offcanvas.hasblocks');
                $buffer .= $this->section09b0df45bfb5d58d4a8556c0245592e6($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
