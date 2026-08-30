<?php

class __Mustache_699cd1ddf52811b5f133d1e57d970609 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        if ($parent = $this->mustache->loadPartial('theme_boost/drawer')) {
            $context->pushBlockContext(array(
                'id' => array($this, 'block713428f2c53de9aebab859391bdd84ed'),
                'drawerclasses' => array($this, 'blockFa542c0ea67e46f29c52f89455ef51fa'),
                'drawercloseonresize' => array($this, 'blockE052079a625ca42b568ba24af19cc7eb'),
                'drawerheading' => array($this, 'block572f3e068b4f5818a2c9af33d94c48c1'),
                'drawercontent' => array($this, 'block2b695c2c7cc86d7bc7d0c881c8ef5447'),
                'drawerstate' => array($this, 'blockA1dd4f2e68d7796ac435ab08723145ef'),
            ));
            $buffer .= $parent->renderInternal($context, $indent);
            $context->popBlockContext();
        }

        return $buffer;
    }

    private function section39d021c56cefcc1e4b606ec5be290563(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' drawer-bottom ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' drawer-bottom ';
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

    private function section2e735f6c6c00fd8f9caf343bbc399d9f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <img src="{{output.get_compact_logo_url}}" class="logo" alt="{{sitename}}">
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
                $buffer .= '" class="logo" alt="';
                $value = $this->resolveValue($context->find('sitename'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section60996e9194158ab9e8351bcb932b2662(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' menu, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' menu, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD7a94445dcf58c884f5d9dc256ac748e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="menu-title">{{#str}} menu, core {{/str}}</div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="menu-title">';
                $value = $context->find('str');
                $buffer .= $this->section60996e9194158ab9e8351bcb932b2662($context, $indent, $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD1567f4bd33c9d4be2d12a680a1d32bf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{> theme_boost_union/primary-drawer-mobile-child }}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/primary-drawer-mobile-child')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7e5a77b2943a27a9708e89863d9c0e76(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'title="{{.}}" data-toggle="tooltip"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'title="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-toggle="tooltip"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5536f67e8aa17ad465bcdd2e03c87d14(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{.}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' ';
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

    private function section5e96ec75439305fc88c78e77946e47bb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{.}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFc0c0b051caebb6243b5c2bd6d728967(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-current="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-current="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section801bcd19aebdc5786ad20c4358b88203(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{name}}="{{value}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC2e436b184481936bbcfcbd2acb919f6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#name}}{{name}}="{{value}}"{{/name}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('name');
                $buffer .= $this->section801bcd19aebdc5786ad20c4358b88203($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA0c6944c92f0c1657db962544b5f827a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#haschildren}}
                    {{> theme_boost_union/primary-drawer-mobile-child }}
                {{/haschildren}}
                {{^haschildren}}
                <a href="{{{url}}}" {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}} class="{{#menuclasses}} {{.}} {{/menuclasses}} list-group-item list-group-item-action {{#isactive}}active{{/isactive}} {{#classes}}{{.}} {{/classes}}" {{#isactive}}aria-current="true"{{/isactive}} {{#attributes}}{{#name}}{{name}}="{{value}}"{{/name}} {{/attributes}}>
                    {{{text}}}
                </a>
                {{/haschildren}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('haschildren');
                $buffer .= $this->sectionD1567f4bd33c9d4be2d12a680a1d32bf($context, $indent, $value);
                $value = $context->find('haschildren');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                <a href="';
                    $value = $this->resolveValue($context->find('url'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '" ';
                    $value = $context->find('tooltip');
                    $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                    $buffer .= ' class="';
                    $value = $context->find('menuclasses');
                    $buffer .= $this->section5536f67e8aa17ad465bcdd2e03c87d14($context, $indent, $value);
                    $buffer .= ' list-group-item list-group-item-action ';
                    $value = $context->find('isactive');
                    $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                    $buffer .= ' ';
                    $value = $context->find('classes');
                    $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                    $buffer .= '" ';
                    $value = $context->find('isactive');
                    $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                    $buffer .= ' ';
                    $value = $context->find('attributes');
                    $buffer .= $this->sectionC2e436b184481936bbcfcbd2acb919f6($context, $indent, $value);
                    $buffer .= '>
';
                    $buffer .= $indent . '                    ';
                    $value = $this->resolveValue($context->find('text'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                </a>
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function block713428f2c53de9aebab859391bdd84ed($context)
    {
        $indent = $buffer = '';
        $buffer .= $indent . 'theme_boost-drawers-primary';
    
        return $buffer;
    }

    public function blockFa542c0ea67e46f29c52f89455ef51fa($context)
    {
        $indent = $buffer = '';
        $buffer .= 'drawer ';
        $value = $context->findDot('bottombar.drawer');
        $buffer .= $this->section39d021c56cefcc1e4b606ec5be290563($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->findDot('bottombar.drawer');
        if (empty($value)) {
            
            $buffer .= ' drawer-left ';
        }
        $buffer .= ' drawer-primary';
    
        return $buffer;
    }

    public function blockE052079a625ca42b568ba24af19cc7eb($context)
    {
        $indent = $buffer = '';
        $buffer .= '1';
    
        return $buffer;
    }

    public function block572f3e068b4f5818a2c9af33d94c48c1($context)
    {
        $indent = $buffer = '';
        $buffer .= '        <a
';
        $buffer .= $indent . '            href="';
        $value = $context->find('alternativelogolinkurl');
        $buffer .= $this->section2f14a6bef41f8e817b5b7ce1fe9aba61($context, $indent, $value);
        $value = $context->find('alternativelogolinkurl');
        if (empty($value)) {
            
            $value = $this->resolveValue($context->findDot('config.homeurl'), $context);
            $buffer .= ($value === null ? '' : $value);
        }
        $buffer .= '"
';
        $buffer .= $indent . '            title="';
        $value = $this->resolveValue($context->find('sitename'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '"
';
        $buffer .= $indent . '            data-region="site-home-link"
';
        $buffer .= $indent . '            class="aabtn text-reset d-flex align-items-center h-100"
';
        $buffer .= $indent . '        >
';
        $value = $context->findDot('output.should_display_navbar_logo');
        $buffer .= $this->section2e735f6c6c00fd8f9caf343bbc399d9f($context, $indent, $value);
        $value = $context->findDot('output.should_display_navbar_logo');
        if (empty($value)) {
            
            $buffer .= $indent . '                <span class="sitename" title="';
            $value = $this->resolveValue($context->find('sitename'), $context);
            $buffer .= ($value === null ? '' : $value);
            $buffer .= '">';
            $value = $this->resolveValue($context->find('sitename'), $context);
            $buffer .= ($value === null ? '' : $value);
            $buffer .= '</span>
';
        }
        $buffer .= $indent . '        </a>
';
    
        return $buffer;
    }

    public function block2b695c2c7cc86d7bc7d0c881c8ef5447($context)
    {
        $indent = $buffer = '';
        $value = $context->findDot('bottombar.drawer');
        $buffer .= $this->sectionD7a94445dcf58c884f5d9dc256ac748e($context, $indent, $value);
        $buffer .= $indent . '        <div class="list-group">
';
        $value = $context->find('mobileprimarynav');
        $buffer .= $this->sectionA0c6944c92f0c1657db962544b5f827a($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
    
        return $buffer;
    }

    public function blockA1dd4f2e68d7796ac435ab08723145ef($context)
    {
        $indent = $buffer = '';
        $buffer .= $indent . 'show-drawer-primary';
    
        return $buffer;
    }
}
