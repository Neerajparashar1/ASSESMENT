<?php

class __Mustache_8b33a67e92f6b6b7c591a95def38de07 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="usermenu">
';
        $value = $context->find('unauthenticateduser');
        $buffer .= $this->section92357c25975071254cf68048c25bc77d($context, $indent, $value);
        $value = $context->find('unauthenticateduser');
        if (empty($value)) {
            
            $buffer .= $indent . '        <div class="dropdown show">
';
            $buffer .= $indent . '            <a href="#" role="button" id="user-menu-toggle" data-toggle="dropdown" aria-label="';
            $value = $context->find('str');
            $buffer .= $this->section4e1672c73dd70427ed6b223d1fa8d13b($context, $indent, $value);
            $buffer .= '"
';
            $buffer .= $indent . '               aria-haspopup="true" aria-controls="user-action-menu" class="btn dropdown-toggle">
';
            $buffer .= $indent . '                <span class="userbutton">
';
            if ($partial = $this->mustache->loadPartial('core/user_menu_metadata')) {
                $buffer .= $partial->renderInternal($context, $indent . '                    ');
            }
            $buffer .= $indent . '                </span>
';
            $buffer .= $indent . '            </a>
';
            $buffer .= $indent . '            <div id="user-action-menu" class="dropdown-menu dropdown-menu-right">
';
            $buffer .= $indent . '                <div id="usermenu-carousel" class="carousel slide" data-touch="false" data-interval="false" data-keyboard="false">
';
            $buffer .= $indent . '                    <div class="carousel-inner">
';
            $buffer .= $indent . '                        <div id="carousel-item-main" class="carousel-item active" role="menu" tabindex="-1" aria-label="';
            $value = $context->find('str');
            $buffer .= $this->sectionC9f8116799aecab2a637bf9b97e3b17a($context, $indent, $value);
            $buffer .= '">
';
            $value = $context->find('showfullnameinusermenu');
            $buffer .= $this->section717029e9bc570a11a52f106b775e5dba($context, $indent, $value);
            if ($partial = $this->mustache->loadPartial('core/user_action_menu_items')) {
                $buffer .= $partial->renderInternal($context, $indent . '                            ');
            }
            $buffer .= $indent . '                        </div>
';
            $value = $context->find('submenus');
            $buffer .= $this->sectionFd7bb94e9be77d82271d2704ed117cbc($context, $indent, $value);
            $buffer .= $indent . '                    </div>
';
            $buffer .= $indent . '                </div>
';
            $buffer .= $indent . '            </div>
';
            $buffer .= $indent . '        </div>
';
        }
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->section9fabd883c42f661de9048a32b433b843($context, $indent, $value);

        return $buffer;
    }

    private function section372dada88a87ec5a5336cd0b597b76c0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' loggedinasguest, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' loggedinasguest, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section60903ea1441adb99832035d30c1076ed(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#str}} loggedinasguest, core {{/str}}
                <div class="divider border-start h-75 align-self-center mx-2"></div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section372dada88a87ec5a5336cd0b597b76c0($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                <div class="divider border-start h-75 align-self-center mx-2"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section55e850885d71934e17db03eabd94757c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' class="btn btn-primary"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' class="btn btn-primary"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section709c7103df2192436d0891976f85ca16(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' login, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' login, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section64645743cf0585d6befc708991fe4d0f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <a href="{{url}}"{{#loginlinkbuttonenabled}} class="btn btn-primary"{{/loginlinkbuttonenabled}}>{{#str}} login, core {{/str}}</a>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $value = $context->find('loginlinkbuttonenabled');
                $buffer .= $this->section55e850885d71934e17db03eabd94757c($context, $indent, $value);
                $buffer .= '>';
                $value = $context->find('str');
                $buffer .= $this->section709c7103df2192436d0891976f85ca16($context, $indent, $value);
                $buffer .= '</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section92357c25975071254cf68048c25bc77d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <span class="login ps-2">
            {{#guest}}
                {{#str}} loggedinasguest, core {{/str}}
                <div class="divider border-start h-75 align-self-center mx-2"></div>
            {{/guest}}
            {{#url}}
                <a href="{{url}}"{{#loginlinkbuttonenabled}} class="btn btn-primary"{{/loginlinkbuttonenabled}}>{{#str}} login, core {{/str}}</a>
            {{/url}}
        </span>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <span class="login ps-2">
';
                $value = $context->find('guest');
                $buffer .= $this->section60903ea1441adb99832035d30c1076ed($context, $indent, $value);
                $value = $context->find('url');
                $buffer .= $this->section64645743cf0585d6befc708991fe4d0f($context, $indent, $value);
                $buffer .= $indent . '        </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4e1672c73dd70427ed6b223d1fa8d13b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'usermenu';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'usermenu';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC9f8116799aecab2a637bf9b97e3b17a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'user';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'user';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section51bdb6cb143df84bf220f6012c23533c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'showfullnameinusermenussetting_loggedinas, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'showfullnameinusermenussetting_loggedinas, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section717029e9bc570a11a52f106b775e5dba(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <div class="loggedinas">
                                    <small>{{#str}}showfullnameinusermenussetting_loggedinas, theme_boost_union{{/str}}</small><br />
                                    <strong>{{userfullname}}</strong>
                                </div>
                                <div class="dropdown-divider"></div>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <div class="loggedinas">
';
                $buffer .= $indent . '                                    <small>';
                $value = $context->find('str');
                $buffer .= $this->section51bdb6cb143df84bf220f6012c23533c($context, $indent, $value);
                $buffer .= '</small><br />
';
                $buffer .= $indent . '                                    <strong>';
                $value = $this->resolveValue($context->find('userfullname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</strong>
';
                $buffer .= $indent . '                                </div>
';
                $buffer .= $indent . '                                <div class="dropdown-divider"></div>
';
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

    private function sectionE7b0419295591cd4a997af0baedd7083(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{text}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE93e645b37d6e8e860597a24a01f1b49(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                    {{! Make submenu headers fully clickable. }}
                                    <div class="header">
                                        <a href="#" class="carousel-navigation-link" data-carousel-target-id="carousel-item-{{#returnid}}{{returnid}}{{/returnid}}{{^returnid}}main{{/returnid}}"
                                            aria-label="{{#str}}usermenugoback{{/str}}">
                                            <button type="button" class="btn btn-icon text-decoration-none text-body">
                                                <span class="dir-rtl-hide">{{#pix}}i/arrow-left{{/pix}}</span>
                                                <span class="dir-ltr-hide">{{#pix}}i/arrow-right{{/pix}}</span>
                                            </button>
                                            <span class="ps-2" id="carousel-item-title-{{id}}">{{#text}}{{{text}}}{{/text}}{{^text}}{{title}}{{/text}}</span>
                                        </a>
                                    </div>
                                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                    <div class="header">
';
                $buffer .= $indent . '                                        <a href="#" class="carousel-navigation-link" data-carousel-target-id="carousel-item-';
                $value = $context->find('returnid');
                $buffer .= $this->section96a25d5a110d45037a6d32ac20b3d5ca($context, $indent, $value);
                $value = $context->find('returnid');
                if (empty($value)) {
                    
                    $buffer .= 'main';
                }
                $buffer .= '"
';
                $buffer .= $indent . '                                            aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section9f5fb4563075558fe19532db49b843db($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                                            <button type="button" class="btn btn-icon text-decoration-none text-body">
';
                $buffer .= $indent . '                                                <span class="dir-rtl-hide">';
                $value = $context->find('pix');
                $buffer .= $this->section1ae1ec3288c57cc16cf11024cfaa8d4e($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                                                <span class="dir-ltr-hide">';
                $value = $context->find('pix');
                $buffer .= $this->section3fb306b309973c917530dde4229e8877($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                                            </button>
';
                $buffer .= $indent . '                                            <span class="ps-2" id="carousel-item-title-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $context->find('text');
                $buffer .= $this->sectionE7b0419295591cd4a997af0baedd7083($context, $indent, $value);
                $value = $context->find('text');
                if (empty($value)) {
                    
                    $value = $this->resolveValue($context->find('title'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                }
                $buffer .= '</span>
';
                $buffer .= $indent . '                                        </a>
';
                $buffer .= $indent . '                                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFd7bb94e9be77d82271d2704ed117cbc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div id="carousel-item-{{id}}" role="menu" class="carousel-item submenu" tabindex="-1" aria-label="{{title}}">
                                <div class="d-flex flex-column h-100">
                                    {{#includesmartmenu}}
                                    {{! Make submenu headers fully clickable. }}
                                    <div class="header">
                                        <a href="#" class="carousel-navigation-link" data-carousel-target-id="carousel-item-{{#returnid}}{{returnid}}{{/returnid}}{{^returnid}}main{{/returnid}}"
                                            aria-label="{{#str}}usermenugoback{{/str}}">
                                            <button type="button" class="btn btn-icon text-decoration-none text-body">
                                                <span class="dir-rtl-hide">{{#pix}}i/arrow-left{{/pix}}</span>
                                                <span class="dir-ltr-hide">{{#pix}}i/arrow-right{{/pix}}</span>
                                            </button>
                                            <span class="ps-2" id="carousel-item-title-{{id}}">{{#text}}{{{text}}}{{/text}}{{^text}}{{title}}{{/text}}</span>
                                        </a>
                                    </div>
                                    {{/includesmartmenu}}
                                    {{^includesmartmenu}}
                                    <div class="header">
                                        <button type="button" class="btn btn-icon carousel-navigation-link text-decoration-none text-body" data-carousel-target-id="carousel-item-main" aria-label="{{#str}}usermenugoback{{/str}}">
                                            <span class="dir-rtl-hide">{{#pix}}i/arrow-left{{/pix}}</span>
                                            <span class="dir-ltr-hide">{{#pix}}i/arrow-right{{/pix}}</span>
                                        </button>
                                        <span class="ps-2" id="carousel-item-title-{{id}}">{{title}}</span>
                                    </div>
                                    {{/includesmartmenu}}
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
                
                $buffer .= $indent . '                            <div id="carousel-item-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menu" class="carousel-item submenu" tabindex="-1" aria-label="';
                $value = $this->resolveValue($context->find('title'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                                <div class="d-flex flex-column h-100">
';
                $value = $context->find('includesmartmenu');
                $buffer .= $this->sectionE93e645b37d6e8e860597a24a01f1b49($context, $indent, $value);
                $value = $context->find('includesmartmenu');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                                    <div class="header">
';
                    $buffer .= $indent . '                                        <button type="button" class="btn btn-icon carousel-navigation-link text-decoration-none text-body" data-carousel-target-id="carousel-item-main" aria-label="';
                    $value = $context->find('str');
                    $buffer .= $this->section9f5fb4563075558fe19532db49b843db($context, $indent, $value);
                    $buffer .= '">
';
                    $buffer .= $indent . '                                            <span class="dir-rtl-hide">';
                    $value = $context->find('pix');
                    $buffer .= $this->section1ae1ec3288c57cc16cf11024cfaa8d4e($context, $indent, $value);
                    $buffer .= '</span>
';
                    $buffer .= $indent . '                                            <span class="dir-ltr-hide">';
                    $value = $context->find('pix');
                    $buffer .= $this->section3fb306b309973c917530dde4229e8877($context, $indent, $value);
                    $buffer .= '</span>
';
                    $buffer .= $indent . '                                        </button>
';
                    $buffer .= $indent . '                                        <span class="ps-2" id="carousel-item-title-';
                    $value = $this->resolveValue($context->find('id'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '">';
                    $value = $this->resolveValue($context->find('title'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '</span>
';
                    $buffer .= $indent . '                                    </div>
';
                }
                $buffer .= $indent . '                                    <div class="dropdown-divider"></div>
';
                $buffer .= $indent . '                                    <div class="items h-100 overflow-auto" role="menu" aria-labelledby="carousel-item-title-';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                if ($partial = $this->mustache->loadPartial('core/user_action_menu_submenu_items')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                        ');
                }
                $buffer .= $indent . '                                    </div>
';
                $buffer .= $indent . '                                </div>
';
                $buffer .= $indent . '                            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9fabd883c42f661de9048a32b433b843(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    require([\'core/usermenu\'], function(UserMenu) {
        UserMenu.init();
    });
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    require([\'core/usermenu\'], function(UserMenu) {
';
                $buffer .= $indent . '        UserMenu.init();
';
                $buffer .= $indent . '    });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
