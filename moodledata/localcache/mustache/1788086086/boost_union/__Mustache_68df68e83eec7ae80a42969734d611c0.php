<?php

class __Mustache_68df68e83eec7ae80a42969734d611c0 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('haschildren');
        $buffer .= $this->section6c9234e8f4c08e84573fd08f6aaaffa6($context, $indent, $value);
        $value = $context->find('haschildren');
        if (empty($value)) {
            
            $buffer .= $indent . '    <li data-key="';
            $value = $this->resolveValue($context->find('key'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '" class="nav-item ';
            $value = $context->find('menuclasses');
            $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
            $buffer .= '" role="none" data-forceintomoremenu="';
            $value = $context->find('forceintomoremenu');
            $buffer .= $this->section03a2cb78adf693fb240638cbbc7ea15e($context, $indent, $value);
            $value = $context->find('forceintomoremenu');
            if (empty($value)) {
                
                $buffer .= 'false';
            }
            $buffer .= '">
';
            $value = $context->find('istablist');
            $buffer .= $this->sectionAf395f5550b461e63163585748c6d625($context, $indent, $value);
            $value = $context->find('istablist');
            if (empty($value)) {
                
                $value = $context->find('is_action_link');
                $buffer .= $this->sectionE340b3bf1bce9f41c445beacbb99e21c($context, $indent, $value);
                $value = $context->find('is_action_link');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                <a role="menuitem" class="nav-link ';
                    $value = $context->findDot('itemdata.classes');
                    $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                    $buffer .= ' ';
                    $value = $context->find('isactive');
                    $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                    $buffer .= ' ';
                    $value = $context->find('classes');
                    $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                    $buffer .= '"
';
                    $buffer .= $indent . '                    href="';
                    $value = $this->resolveValue($context->find('url'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $value = $this->resolveValue($context->find('action'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '"
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('tooltip');
                    $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                    $value = $context->find('tooltip');
                    if (empty($value)) {
                        
                        $value = $context->find('title');
                        $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                    }
                    $buffer .= '
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('attributes');
                    $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('isactive');
                    $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                    data-disableactive="true"
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('isactive');
                    if (empty($value)) {
                        
                        $buffer .= 'tabindex="-1"';
                    }
                    $buffer .= '
';
                    $buffer .= $indent . '                >
';
                    $buffer .= $indent . '                    ';
                    $value = $this->resolveValue($context->find('text'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                </a>
';
                }
            }
            $buffer .= $indent . '    </li>
';
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

    private function section03a2cb78adf693fb240638cbbc7ea15e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'true';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'true';
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

    private function sectionCb30325ba4e5065f061652102e745487(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'title="{{.}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'title="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6805fd502f1e55bd3a63b02c625bf221(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{name}}="{{value}}" ';
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
                $buffer .= '" ';
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

    private function section2bea9aeb3170611aec1705ca0b3828c5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '<div class="menu-helpicon">{{{helpicon}}}</div>';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '<div class="menu-helpicon">';
                $value = $this->resolveValue($context->find('helpicon'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF98878d9e22d92d30940531334ba5e77(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '<div class="menu-description">{{{helptext}}}</div>';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '<div class="menu-description">';
                $value = $this->resolveValue($context->find('helptext'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFf4bb81f58506930f8dbcaf69f142197(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('helptext');
                $buffer .= $this->sectionF98878d9e22d92d30940531334ba5e77($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section344075d077ca448f61c7ae3276b2582a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div id="carousel-item-main" class="carousel-item active" role="menu" tabindex="-1">
                        {{#helpicon}}<div class="menu-helpicon">{{{helpicon}}}</div>{{/helpicon}}
                        {{#abovehelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/abovehelptext}}
                        {{> core/user_action_menu_items }}
                        {{#belowhelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/belowhelptext}}
                    </div>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div id="carousel-item-main" class="carousel-item active" role="menu" tabindex="-1">
';
                $buffer .= $indent . '                        ';
                $value = $context->find('helpicon');
                $buffer .= $this->section2bea9aeb3170611aec1705ca0b3828c5($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                        ';
                $value = $context->find('abovehelptext');
                $buffer .= $this->sectionFf4bb81f58506930f8dbcaf69f142197($context, $indent, $value);
                $buffer .= '
';
                if ($partial = $this->mustache->loadPartial('core/user_action_menu_items')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                        ');
                }
                $buffer .= $indent . '                        ';
                $value = $context->find('belowhelptext');
                $buffer .= $this->sectionFf4bb81f58506930f8dbcaf69f142197($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section694de2277c5bffba4f6962db0eb31056(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                    {{> core/actions }}
                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/actions')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section60b5bcfcf5e3e3e6b95728c5b1c9dbf3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <a class="dropdown-item {{#itemdata.classes}}{{.}} {{/itemdata.classes}}" role="menuitem" {{#actionattributes}}{{name}}="{{value}}" {{/actionattributes}} href="{{{url}}}{{{action}}}"
                                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                                    data-disableactive="true"
                                    tabindex="-1"
                                >
                                    {{{text}}}
                                </a>
                                {{#action_link_actions}}
                                    {{> core/actions }}
                                {{/action_link_actions}}
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <a class="dropdown-item ';
                $value = $context->findDot('itemdata.classes');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= '" role="menuitem" ';
                $value = $context->find('actionattributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= ' href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : $value);
                $value = $this->resolveValue($context->find('action'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '"
';
                $buffer .= $indent . '                                    ';
                $value = $context->find('tooltip');
                $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                $value = $context->find('tooltip');
                if (empty($value)) {
                    
                    $value = $context->find('title');
                    $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                }
                $buffer .= '
';
                $buffer .= $indent . '                                    ';
                $value = $context->find('attributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                                    data-disableactive="true"
';
                $buffer .= $indent . '                                    tabindex="-1"
';
                $buffer .= $indent . '                                >
';
                $buffer .= $indent . '                                    ';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                                </a>
';
                $value = $context->find('action_link_actions');
                $buffer .= $this->section694de2277c5bffba4f6962db0eb31056($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEdf7467a21edd65f2fe67357df5e2dbc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div class="dropdown-divider"></div>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <div class="dropdown-divider"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFc53f7a2aacb3debfeb18b1f84590a4e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{! Smart menus }}
                    {{#submenuid}}
                    <div id="carousel-item-main" class="carousel-item active" role="menu" tabindex="-1">
                        {{#helpicon}}<div class="menu-helpicon">{{{helpicon}}}</div>{{/helpicon}}
                        {{#abovehelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/abovehelptext}}
                        {{> core/user_action_menu_items }}
                        {{#belowhelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/belowhelptext}}
                    </div>
                    {{/submenuid}}
                    {{! Moodle core menus }}
                    {{^items}}
                        {{^divider}}
                            {{#is_action_link}}
                                <a class="dropdown-item {{#itemdata.classes}}{{.}} {{/itemdata.classes}}" role="menuitem" {{#actionattributes}}{{name}}="{{value}}" {{/actionattributes}} href="{{{url}}}{{{action}}}"
                                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                                    data-disableactive="true"
                                    tabindex="-1"
                                >
                                    {{{text}}}
                                </a>
                                {{#action_link_actions}}
                                    {{> core/actions }}
                                {{/action_link_actions}}
                            {{/is_action_link}}
                            {{^is_action_link}}
                                <a class="dropdown-item {{#itemdata.classes}}{{.}} {{/itemdata.classes}}" role="menuitem" href="{{{url}}}{{{action}}}" {{#isactive}}aria-current="true"{{/isactive}}
                                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                                    data-disableactive="true"
                                    tabindex="-1"
                                >
                                    {{{text}}}
                                </a>
                            {{/is_action_link}}
                        {{/divider}}
                        {{#divider}}
                            <div class="dropdown-divider"></div>
                        {{/divider}}
                    {{/items}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('submenuid');
                $buffer .= $this->section344075d077ca448f61c7ae3276b2582a($context, $indent, $value);
                $value = $context->find('items');
                if (empty($value)) {
                    
                    $value = $context->find('divider');
                    if (empty($value)) {
                        
                        $value = $context->find('is_action_link');
                        $buffer .= $this->section60b5bcfcf5e3e3e6b95728c5b1c9dbf3($context, $indent, $value);
                        $value = $context->find('is_action_link');
                        if (empty($value)) {
                            
                            $buffer .= $indent . '                                <a class="dropdown-item ';
                            $value = $context->findDot('itemdata.classes');
                            $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                            $buffer .= '" role="menuitem" href="';
                            $value = $this->resolveValue($context->find('url'), $context);
                            $buffer .= ($value === null ? '' : $value);
                            $value = $this->resolveValue($context->find('action'), $context);
                            $buffer .= ($value === null ? '' : $value);
                            $buffer .= '" ';
                            $value = $context->find('isactive');
                            $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                            $buffer .= '
';
                            $buffer .= $indent . '                                    ';
                            $value = $context->find('tooltip');
                            $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                            $value = $context->find('tooltip');
                            if (empty($value)) {
                                
                                $value = $context->find('title');
                                $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                            }
                            $buffer .= '
';
                            $buffer .= $indent . '                                    ';
                            $value = $context->find('attributes');
                            $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                            $buffer .= '
';
                            $buffer .= $indent . '                                    data-disableactive="true"
';
                            $buffer .= $indent . '                                    tabindex="-1"
';
                            $buffer .= $indent . '                                >
';
                            $buffer .= $indent . '                                    ';
                            $value = $this->resolveValue($context->find('text'), $context);
                            $buffer .= ($value === null ? '' : $value);
                            $buffer .= '
';
                            $buffer .= $indent . '                                </a>
';
                        }
                    }
                    $value = $context->find('divider');
                    $buffer .= $this->sectionEdf7467a21edd65f2fe67357df5e2dbc($context, $indent, $value);
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section96a25d5a110d45037a6d32ac20b3d5ca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{returnid}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('returnid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9f5fb4563075558fe19532db49b843db(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'usermenugoback';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'usermenugoback';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1ae1ec3288c57cc16cf11024cfaa8d4e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'i/arrow-left';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'i/arrow-left';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3fb306b309973c917530dde4229e8877(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'i/arrow-right';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'i/arrow-right';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFacf954f432cc4c4662c146780ef6925(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{title}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('title'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE9338469776b66775507bdf150d7a58f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{! Make submenu headers fully clickable. }}
                    <div id="carousel-item-{{id}}" role="menu" class="carousel-item submenu" tabindex="-1" aria-label="{{title}}">
                        <div class="d-flex flex-column h-100">
                            <div class="header">
                                <a href="#" class=" carousel-navigation-link" data-carousel-target-id="carousel-item-{{#returnid}}{{returnid}}{{/returnid}}{{^returnid}}main{{/returnid}}" aria-label="{{#str}}usermenugoback{{/str}}">
                                    <button type="button" class="btn btn-icon text-decoration-none text-body">
                                        <span class="dir-rtl-hide">{{#pix}}i/arrow-left{{/pix}}</span>
                                        <span class="dir-ltr-hide">{{#pix}}i/arrow-right{{/pix}}</span>
                                    </button>
                                    <span class="ps-2" id="carousel-item-title-{{id}}">{{#title}}{{{title}}}{{/title}}{{^title}}{{{text}}}{{/title}}</span>
                                </a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="items h-100 overflow-auto" role="menu" aria-labelledby="carousel-item-title-{{id}}">
                                {{> core/user_action_menu_submenu_items }}
                            </div>
                        </div>
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div id="carousel-item-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menu" class="carousel-item submenu" tabindex="-1" aria-label="';
                $value = $this->resolveValue($context->find('title'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                        <div class="d-flex flex-column h-100">
';
                $buffer .= $indent . '                            <div class="header">
';
                $buffer .= $indent . '                                <a href="#" class=" carousel-navigation-link" data-carousel-target-id="carousel-item-';
                $value = $context->find('returnid');
                $buffer .= $this->section96a25d5a110d45037a6d32ac20b3d5ca($context, $indent, $value);
                $value = $context->find('returnid');
                if (empty($value)) {
                    
                    $buffer .= 'main';
                }
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section9f5fb4563075558fe19532db49b843db($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                                    <button type="button" class="btn btn-icon text-decoration-none text-body">
';
                $buffer .= $indent . '                                        <span class="dir-rtl-hide">';
                $value = $context->find('pix');
                $buffer .= $this->section1ae1ec3288c57cc16cf11024cfaa8d4e($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                                        <span class="dir-ltr-hide">';
                $value = $context->find('pix');
                $buffer .= $this->section3fb306b309973c917530dde4229e8877($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                                    </button>
';
                $buffer .= $indent . '                                    <span class="ps-2" id="carousel-item-title-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $context->find('title');
                $buffer .= $this->sectionFacf954f432cc4c4662c146780ef6925($context, $indent, $value);
                $value = $context->find('title');
                if (empty($value)) {
                    
                    $value = $this->resolveValue($context->find('text'), $context);
                    $buffer .= ($value === null ? '' : $value);
                }
                $buffer .= '</span>
';
                $buffer .= $indent . '                                </a>
';
                $buffer .= $indent . '                            </div>
';
                $buffer .= $indent . '                            <div class="dropdown-divider"></div>
';
                $buffer .= $indent . '                            <div class="items h-100 overflow-auto" role="menu" aria-labelledby="carousel-item-title-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                if ($partial = $this->mustache->loadPartial('core/user_action_menu_submenu_items')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                ');
                }
                $buffer .= $indent . '                            </div>
';
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6c9234e8f4c08e84573fd08f6aaaffa6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <li class="dropdown theme-boost-union-smartmenu-carousel nav-item {{#menuclasses}}{{.}} {{/menuclasses}} " role="none" data-forceintomoremenu="{{#forceintomoremenu}}true{{/forceintomoremenu}}{{^forceintomoremenu}}false{{/forceintomoremenu}}">
        <a class="dropdown-toggle nav-link {{#isactive}}active{{/isactive}} {{#classes}}{{.}} {{/classes}}" id="drop-down-{{moremenuid}}" role="menuitem" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false" href="#" aria-controls="drop-down-menu-{{moremenuid}}"
            {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
            {{#attributes}}{{name}}="{{value}}" {{/attributes}}
            {{#isactive}}aria-current="true"{{/isactive}}
            {{^isactive}}tabindex="-1"{{/isactive}}
        >
            {{{text}}}
        </a>
        <div class="dropdown-menu boost-union-moremenu" role="menu" id="drop-down-menu-{{moremenuid}}" aria-labelledby="drop-down-{{moremenuid}}">
            <div data-toggle="smartmenu-carousel" class="carousel slide" data-touch="false" data-interval="false" data-keyboard="false">
                <div class="carousel-inner">
                {{#children}}
                    {{! Smart menus }}
                    {{#submenuid}}
                    <div id="carousel-item-main" class="carousel-item active" role="menu" tabindex="-1">
                        {{#helpicon}}<div class="menu-helpicon">{{{helpicon}}}</div>{{/helpicon}}
                        {{#abovehelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/abovehelptext}}
                        {{> core/user_action_menu_items }}
                        {{#belowhelptext}}{{#helptext}}<div class="menu-description">{{{helptext}}}</div>{{/helptext}}{{/belowhelptext}}
                    </div>
                    {{/submenuid}}
                    {{! Moodle core menus }}
                    {{^items}}
                        {{^divider}}
                            {{#is_action_link}}
                                <a class="dropdown-item {{#itemdata.classes}}{{.}} {{/itemdata.classes}}" role="menuitem" {{#actionattributes}}{{name}}="{{value}}" {{/actionattributes}} href="{{{url}}}{{{action}}}"
                                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                                    data-disableactive="true"
                                    tabindex="-1"
                                >
                                    {{{text}}}
                                </a>
                                {{#action_link_actions}}
                                    {{> core/actions }}
                                {{/action_link_actions}}
                            {{/is_action_link}}
                            {{^is_action_link}}
                                <a class="dropdown-item {{#itemdata.classes}}{{.}} {{/itemdata.classes}}" role="menuitem" href="{{{url}}}{{{action}}}" {{#isactive}}aria-current="true"{{/isactive}}
                                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                                    data-disableactive="true"
                                    tabindex="-1"
                                >
                                    {{{text}}}
                                </a>
                            {{/is_action_link}}
                        {{/divider}}
                        {{#divider}}
                            <div class="dropdown-divider"></div>
                        {{/divider}}
                    {{/items}}
                {{/children}}
                {{#submenus}}
                    {{! Make submenu headers fully clickable. }}
                    <div id="carousel-item-{{id}}" role="menu" class="carousel-item submenu" tabindex="-1" aria-label="{{title}}">
                        <div class="d-flex flex-column h-100">
                            <div class="header">
                                <a href="#" class=" carousel-navigation-link" data-carousel-target-id="carousel-item-{{#returnid}}{{returnid}}{{/returnid}}{{^returnid}}main{{/returnid}}" aria-label="{{#str}}usermenugoback{{/str}}">
                                    <button type="button" class="btn btn-icon text-decoration-none text-body">
                                        <span class="dir-rtl-hide">{{#pix}}i/arrow-left{{/pix}}</span>
                                        <span class="dir-ltr-hide">{{#pix}}i/arrow-right{{/pix}}</span>
                                    </button>
                                    <span class="ps-2" id="carousel-item-title-{{id}}">{{#title}}{{{title}}}{{/title}}{{^title}}{{{text}}}{{/title}}</span>
                                </a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="items h-100 overflow-auto" role="menu" aria-labelledby="carousel-item-title-{{id}}">
                                {{> core/user_action_menu_submenu_items }}
                            </div>
                        </div>
                    </div>
                {{/submenus}}
                </div>
            </div>
        </div>
    </li>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <li class="dropdown theme-boost-union-smartmenu-carousel nav-item ';
                $value = $context->find('menuclasses');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= ' " role="none" data-forceintomoremenu="';
                $value = $context->find('forceintomoremenu');
                $buffer .= $this->section03a2cb78adf693fb240638cbbc7ea15e($context, $indent, $value);
                $value = $context->find('forceintomoremenu');
                if (empty($value)) {
                    
                    $buffer .= 'false';
                }
                $buffer .= '">
';
                $buffer .= $indent . '        <a class="dropdown-toggle nav-link ';
                $value = $context->find('isactive');
                $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                $buffer .= ' ';
                $value = $context->find('classes');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= '" id="drop-down-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" data-toggle="dropdown"
';
                $buffer .= $indent . '            aria-haspopup="true" aria-expanded="false" href="#" aria-controls="drop-down-menu-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '            ';
                $value = $context->find('tooltip');
                $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                $value = $context->find('tooltip');
                if (empty($value)) {
                    
                    $value = $context->find('title');
                    $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                }
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('attributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('isactive');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('isactive');
                if (empty($value)) {
                    
                    $buffer .= 'tabindex="-1"';
                }
                $buffer .= '
';
                $buffer .= $indent . '        >
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </a>
';
                $buffer .= $indent . '        <div class="dropdown-menu boost-union-moremenu" role="menu" id="drop-down-menu-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" aria-labelledby="drop-down-';
                $value = $this->resolveValue($context->find('moremenuid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '            <div data-toggle="smartmenu-carousel" class="carousel slide" data-touch="false" data-interval="false" data-keyboard="false">
';
                $buffer .= $indent . '                <div class="carousel-inner">
';
                $value = $context->find('children');
                $buffer .= $this->sectionFc53f7a2aacb3debfeb18b1f84590a4e($context, $indent, $value);
                $value = $context->find('submenus');
                $buffer .= $this->sectionE9338469776b66775507bdf150d7a58f($context, $indent, $value);
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '            </div>
';
                $buffer .= $indent . '        </div>
';
                $buffer .= $indent . '    </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section987df7e610a2cfd3294b5eacfb9fa51b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{> core/actions }}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/actions')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDe7759f6fe44d074438161e60de21b40(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <a role="tab" class="nav-link {{#classes}}{{.}} {{/classes}}" href="{{tab}}" data-toggle="tab" data-text="{{{text}}}"
                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                    data-disableactive="true"
                    tabindex="-1"
                >
                    {{{text}}}
                </a>
                {{#action_link_actions}}
                    {{> core/actions }}
                {{/action_link_actions}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <a role="tab" class="nav-link ';
                $value = $context->find('classes');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= '" href="';
                $value = $this->resolveValue($context->find('tab'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-toggle="tab" data-text="';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '"
';
                $buffer .= $indent . '                    ';
                $value = $context->find('tooltip');
                $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                $value = $context->find('tooltip');
                if (empty($value)) {
                    
                    $value = $context->find('title');
                    $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                }
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $context->find('attributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    data-disableactive="true"
';
                $buffer .= $indent . '                    tabindex="-1"
';
                $buffer .= $indent . '                >
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                </a>
';
                $value = $context->find('action_link_actions');
                $buffer .= $this->section987df7e610a2cfd3294b5eacfb9fa51b($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCe04cacc15f032e9e9f826b761c9b814(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-selected="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-selected="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAf395f5550b461e63163585748c6d625(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{#is_action_link}}
                <a role="tab" class="nav-link {{#classes}}{{.}} {{/classes}}" href="{{tab}}" data-toggle="tab" data-text="{{{text}}}"
                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                    data-disableactive="true"
                    tabindex="-1"
                >
                    {{{text}}}
                </a>
                {{#action_link_actions}}
                    {{> core/actions }}
                {{/action_link_actions}}
            {{/is_action_link}}
            {{^is_action_link}}
                <a role="tab" class="nav-link {{#isactive}}active{{/isactive}} {{#classes}}{{.}} {{/classes}}"
                    href="{{tab}}" data-toggle="tab" data-text="{{{text}}}"
                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                    {{#isactive}}aria-selected="true"{{/isactive}}
                    data-disableactive="true"
                    {{^isactive}}tabindex="-1"{{/isactive}}
                >
                    {{{text}}}
                </a>
            {{/is_action_link}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('is_action_link');
                $buffer .= $this->sectionDe7759f6fe44d074438161e60de21b40($context, $indent, $value);
                $value = $context->find('is_action_link');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                <a role="tab" class="nav-link ';
                    $value = $context->find('isactive');
                    $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                    $buffer .= ' ';
                    $value = $context->find('classes');
                    $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                    $buffer .= '"
';
                    $buffer .= $indent . '                    href="';
                    $value = $this->resolveValue($context->find('tab'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '" data-toggle="tab" data-text="';
                    $value = $this->resolveValue($context->find('text'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '"
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('tooltip');
                    $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                    $value = $context->find('tooltip');
                    if (empty($value)) {
                        
                        $value = $context->find('title');
                        $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                    }
                    $buffer .= '
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('attributes');
                    $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('isactive');
                    $buffer .= $this->sectionCe04cacc15f032e9e9f826b761c9b814($context, $indent, $value);
                    $buffer .= '
';
                    $buffer .= $indent . '                    data-disableactive="true"
';
                    $buffer .= $indent . '                    ';
                    $value = $context->find('isactive');
                    if (empty($value)) {
                        
                        $buffer .= 'tabindex="-1"';
                    }
                    $buffer .= '
';
                    $buffer .= $indent . '                >
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

    private function sectionE340b3bf1bce9f41c445beacbb99e21c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <a role="menuitem" class="nav-link {{#itemdata.classes}}{{.}} {{/itemdata.classes}} {{#classes}}{{.}} {{/classes}}" {{#actionattributes}}{{name}}="{{value}}" {{/actionattributes}} href="{{{url}}}{{{action}}}"
                    {{#tooltip}}title="{{.}}" data-toggle="tooltip"{{/tooltip}}{{^tooltip}}{{#title}}title="{{.}}"{{/title}}{{/tooltip}}
                    {{#attributes}}{{name}}="{{value}}" {{/attributes}}
                    data-disableactive="true"
                    tabindex="-1"
                >
                    {{{text}}}
                </a>
                {{#action_link_actions}}
                    {{> core/actions }}
                {{/action_link_actions}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <a role="menuitem" class="nav-link ';
                $value = $context->findDot('itemdata.classes');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= ' ';
                $value = $context->find('classes');
                $buffer .= $this->section5e96ec75439305fc88c78e77946e47bb($context, $indent, $value);
                $buffer .= '" ';
                $value = $context->find('actionattributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= ' href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : $value);
                $value = $this->resolveValue($context->find('action'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '"
';
                $buffer .= $indent . '                    ';
                $value = $context->find('tooltip');
                $buffer .= $this->section7e5a77b2943a27a9708e89863d9c0e76($context, $indent, $value);
                $value = $context->find('tooltip');
                if (empty($value)) {
                    
                    $value = $context->find('title');
                    $buffer .= $this->sectionCb30325ba4e5065f061652102e745487($context, $indent, $value);
                }
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $context->find('attributes');
                $buffer .= $this->section6805fd502f1e55bd3a63b02c625bf221($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                    data-disableactive="true"
';
                $buffer .= $indent . '                    tabindex="-1"
';
                $buffer .= $indent . '                >
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                </a>
';
                $value = $context->find('action_link_actions');
                $buffer .= $this->section987df7e610a2cfd3294b5eacfb9fa51b($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
