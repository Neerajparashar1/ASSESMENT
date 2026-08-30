<?php

class __Mustache_a3ba48aa95fc85683f614fb472d59275 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $buffer .= $indent . '<div class="loginform">
';
        $value = $context->find('loginbrandshowlogo');
        $buffer .= $this->section4da9b3882ed26e6068b71e8de2f824c4($context, $indent, $value);
        $value = $context->find('loginbrandshowheading');
        $buffer .= $this->sectionD30087fabc41cccf452cff20d4bc5978($context, $indent, $value);
        $value = $context->find('loginbrandshowheading');
        if (empty($value)) {
            
            $buffer .= $indent . '        <h1 class="login-heading sr-only">';
            $value = $this->resolveValue($context->find('loginheadingtext'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '</h1>
';
        }
        $value = $context->find('loginbrandshowtagline');
        $buffer .= $this->sectionAf03bfac7abd567a8aaf0d3a76ab3d43($context, $indent, $value);
        $value = $context->find('maintenance');
        $buffer .= $this->sectionEf717f60864e507396cb776f401fd613($context, $indent, $value);
        $value = $context->find('error');
        $buffer .= $this->sectionE4c1d2bd846acc036d908d9f76a54c5a($context, $indent, $value);
        $value = $context->find('info');
        $buffer .= $this->sectionC40ddeec53755bbc31a1e17aeea11f56($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    <div id="theme_boost_union-loginform">
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $value = $context->find('logininstructionsabove');
        $buffer .= $this->sectionF54634e2ace7e848d4cfd9d295c4afe0($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $value = $context->find('loginlayouttabs');
        $buffer .= $this->sectionC4eedf5f2ffcd82b1dfea8c3abc28d0a($context, $indent, $value);
        $value = $context->find('loginlayoutaccordion');
        $buffer .= $this->sectionA700efeaaf1023e278b99284d0cbbe87($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('loginmethods');
        $buffer .= $this->sectionD6e5a5cee86741981016c0d54e80cede($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $value = $context->find('loginlayouttabs');
        $buffer .= $this->section7339a3c9b0530cc875c39a1527266311($context, $indent, $value);
        $value = $context->find('loginlayoutaccordion');
        $buffer .= $this->section7339a3c9b0530cc875c39a1527266311($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $value = $context->find('loginlayoutaccordion');
        if (empty($value)) {
            
            $buffer .= $indent . '    <div class="login-divider"></div>
';
        }
        $value = $context->find('loginlayoutaccordion');
        $buffer .= $this->sectionE8093273a32c613eb9fff83b35adaee4($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $value = $context->find('logininstructionsbelow');
        $buffer .= $this->sectionFe71f21b427f54119c255b1e105ff3b5($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    <div class="d-flex">
';
        $value = $context->find('languagemenu');
        $buffer .= $this->sectionF14d80a3e07dfc2666df87d17d3d8cc8($context, $indent, $value);
        $buffer .= $indent . '        <button type="button" class="btn btn-secondary" ';
        $buffer .= ' data-modal="alert"';
        $buffer .= ' data-modal-title-str=\'["cookiesenabled", "core"]\' ';
        $buffer .= ' data-modal-content-str=\'["cookiesenabled_help_html", "core"]\'';
        $buffer .= '>';
        $value = $context->find('str');
        $buffer .= $this->sectionFcb729cc74d31bce5e3746aa60b79a2e($context, $indent, $value);
        $buffer .= '</button>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';
        $value = $context->find('js');
        $buffer .= $this->sectionF109e33cbd41eb902f5e544b8a3b92b6($context, $indent, $value);

        return $buffer;
    }

    private function sectionBb95bfa51f1431ebc7a88dc3246efd1b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{loginlogoclasses}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $this->resolveValue($context->find('loginlogoclasses'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3b7b893158533067e015ed757595dfb2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div id="loginlogo" class="login-logo{{#loginlogoclasses}} {{loginlogoclasses}}{{/loginlogoclasses}}">
                <img id="logoimage" src="{{logourl}}" class="img-fluid" alt="{{sitename}}"/>
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div id="loginlogo" class="login-logo';
                $value = $context->find('loginlogoclasses');
                $buffer .= $this->sectionBb95bfa51f1431ebc7a88dc3246efd1b($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                <img id="logoimage" src="';
                $value = $this->resolveValue($context->find('logourl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="img-fluid" alt="';
                $value = $this->resolveValue($context->find('sitename'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"/>
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4da9b3882ed26e6068b71e8de2f824c4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#logourl}}
            <div id="loginlogo" class="login-logo{{#loginlogoclasses}} {{loginlogoclasses}}{{/loginlogoclasses}}">
                <img id="logoimage" src="{{logourl}}" class="img-fluid" alt="{{sitename}}"/>
            </div>
        {{/logourl}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('logourl');
                $buffer .= $this->section3b7b893158533067e015ed757595dfb2($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section027957c5be3011c6763b5ddd7f55ed45(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '2';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '2';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD30087fabc41cccf452cff20d4bc5978(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <h1 class="login-heading mb-{{#loginbrandshowtagline}}2{{/loginbrandshowtagline}}{{^loginbrandshowtagline}}4{{/loginbrandshowtagline}}">{{loginheadingtext}}</h1>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <h1 class="login-heading mb-';
                $value = $context->find('loginbrandshowtagline');
                $buffer .= $this->section027957c5be3011c6763b5ddd7f55ed45($context, $indent, $value);
                $value = $context->find('loginbrandshowtagline');
                if (empty($value)) {
                    
                    $buffer .= '4';
                }
                $buffer .= '">';
                $value = $this->resolveValue($context->find('loginheadingtext'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</h1>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section89c0586609fa5555f0cf2a5c3dc60573(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <p class="login-tagline text-muted mb-4">{{logintaglinetext}}</p>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <p class="login-tagline text-muted mb-4">';
                $value = $this->resolveValue($context->find('logintaglinetext'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAf03bfac7abd567a8aaf0d3a76ab3d43(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#logintaglinetext}}
            <p class="login-tagline text-muted mb-4">{{logintaglinetext}}</p>
        {{/logintaglinetext}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('logintaglinetext');
                $buffer .= $this->section89c0586609fa5555f0cf2a5c3dc60573($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEf717f60864e507396cb776f401fd613(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="alert alert-danger login-maintenance">
            {{{maintenance}}}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="alert alert-danger login-maintenance">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('maintenance'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE4c1d2bd846acc036d908d9f76a54c5a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="alert alert-danger" id="loginerrormessage" role="alert">{{error}}</div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="alert alert-danger" id="loginerrormessage" role="alert">';
                $value = $this->resolveValue($context->find('error'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC40ddeec53755bbc31a1e17aeea11f56(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="alert alert-info" id="logininfomessage" role="status">{{info}}</div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="alert alert-info" id="logininfomessage" role="status">';
                $value = $this->resolveValue($context->find('info'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF54634e2ace7e848d4cfd9d295c4afe0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div class="login-instructions login-instructions-above mb-4">
        {{{logininstructionsabove}}}
    </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div class="login-instructions login-instructions-above mb-4">
';
                $buffer .= $indent . '        ';
                $value = $this->resolveValue($context->find('logininstructionsabove'), $context);
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

    private function section4b0ccf7cf4d6d0ec9bd57782d5167730(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <li class="nav-item" role="presentation">
                <a class="nav-link {{#active}}active{{/active}}" id="{{id}}-tab" data-toggle="tab" href="#{{id}}" role="tab" aria-controls="{{id}}" {{#active}}aria-selected="true"{{/active}}{{^active}}aria-selected="false" tabindex="-1"{{/active}}>{{label}}</a>
            </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <li class="nav-item" role="presentation">
';
                $buffer .= $indent . '                <a class="nav-link ';
                $value = $context->find('active');
                $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                $buffer .= '" id="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-tab" data-toggle="tab" href="#';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="tab" aria-controls="';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $value = $context->find('active');
                $buffer .= $this->sectionCe04cacc15f032e9e9f826b761c9b814($context, $indent, $value);
                $value = $context->find('active');
                if (empty($value)) {
                    
                    $buffer .= 'aria-selected="false" tabindex="-1"';
                }
                $buffer .= '>';
                $value = $this->resolveValue($context->find('label'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '            </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC4eedf5f2ffcd82b1dfea8c3abc28d0a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <ul class="nav nav-tabs mb-4" id="login-layout-tabs" role="tablist">
            {{#loginmethods}}
            <li class="nav-item" role="presentation">
                <a class="nav-link {{#active}}active{{/active}}" id="{{id}}-tab" data-toggle="tab" href="#{{id}}" role="tab" aria-controls="{{id}}" {{#active}}aria-selected="true"{{/active}}{{^active}}aria-selected="false" tabindex="-1"{{/active}}>{{label}}</a>
            </li>
            {{/loginmethods}}
        </ul>
        <div class="tab-content mb-4" id="login-layout-tabs-content">
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <ul class="nav nav-tabs mb-4" id="login-layout-tabs" role="tablist">
';
                $value = $context->find('loginmethods');
                $buffer .= $this->section4b0ccf7cf4d6d0ec9bd57782d5167730($context, $indent, $value);
                $buffer .= $indent . '        </ul>
';
                $buffer .= $indent . '        <div class="tab-content mb-4" id="login-layout-tabs-content">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA700efeaaf1023e278b99284d0cbbe87(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div id="login-layout-accordion" class="accordion">
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div id="login-layout-accordion" class="accordion">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7700bd879a46a204d3a722d60abcf2ad(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'text-break w-100';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'text-break w-100';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section71921288ff844b90b6fb08bff5826d67(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{localloginintrotext}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('localloginintrotext'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0fe9965944abc1b5ad705db814b156e2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' loginlocalintro, theme_boost_union ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' loginlocalintro, theme_boost_union ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8bf6878a48886026eb91e21978b30b3e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#localloginintrotext}}{{{localloginintrotext}}}{{/localloginintrotext}}{{^localloginintrotext}}{{#str}} loginlocalintro, theme_boost_union {{/str}}{{/localloginintrotext}}</h2>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <h2 class="login-heading ';
                $value = $context->find('loginlayoutaccordion');
                $buffer .= $this->section7700bd879a46a204d3a722d60abcf2ad($context, $indent, $value);
                $buffer .= '">';
                $value = $context->find('localloginintrotext');
                $buffer .= $this->section71921288ff844b90b6fb08bff5826d67($context, $indent, $value);
                $value = $context->find('localloginintrotext');
                if (empty($value)) {
                    
                    $value = $context->find('str');
                    $buffer .= $this->section0fe9965944abc1b5ad705db814b156e2($context, $indent, $value);
                }
                $buffer .= '</h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCf78c7991657d797aaa72c51954b283e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-local mb-3">
            {{{locallogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-local mb-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('locallogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1c0404421f329422ad970c93a3c60890(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#locallogininstructionsbetween}}
        <div class="login-instructions login-instructions-local mb-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbetween}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('locallogininstructionsbetween');
                $buffer .= $this->sectionCf78c7991657d797aaa72c51954b283e($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section27e9419edc620e0e1872d2a6521f1533(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' username ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' username ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section22141a6741c33f407ef6171795337eec(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' usernameemail ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' usernameemail ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section310b720ee7c196969487e5e8484c0520(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        {{#str}} usernameemail {{/str}}
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        ';
                $value = $context->find('str');
                $buffer .= $this->section22141a6741c33f407ef6171795337eec($context, $indent, $value);
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFea69428308e6a733cfeebf7670bdc01(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'username';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'username';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section983b6843353faa33a83a9ec3069863a3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'usernameemail';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'usernameemail';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section118ece6c412804f669c845b43ecc9a01(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#cleanstr}}usernameemail{{/cleanstr}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('cleanstr');
                $buffer .= $this->section983b6843353faa33a83a9ec3069863a3($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE056be559d6d01a9bd2bf6f760f8e3e3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' password ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' password ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4e50d9b1632f258e8c10be3e2ed759be(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'password';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'password';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4060814e986c7dfa6be279f952adb993(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <div class="login-form-recaptcha mb-3">
                    {{{recaptcha}}}
                </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <div class="login-form-recaptcha mb-3">
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->find('recaptcha'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB15dee8971ab065bf4d6402b60d852be(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'login';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'login';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE3afea308016df7243ba8871f7081e79(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'forgotaccount';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'forgotaccount';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section83c448d460e342a4ea8af15ec77f3f62(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-local mt-3">
            {{{locallogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-local mt-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('locallogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section694e672fbf262003ebcfbb52a492a630(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#locallogininstructionsbelow}}
        <div class="login-instructions login-instructions-local mt-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbelow}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('locallogininstructionsbelow');
                $buffer .= $this->section83c448d460e342a4ea8af15ec77f3f62($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9efe120f7d18b673b67a00626641b46e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showlocalloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#localloginintrotext}}{{{localloginintrotext}}}{{/localloginintrotext}}{{^localloginintrotext}}{{#str}} loginlocalintro, theme_boost_union {{/str}}{{/localloginintrotext}}</h2>
        {{/showlocalloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbetween}}
        <div class="login-instructions login-instructions-local mb-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbetween}}
        {{/showlocallogininstruction}}

        <form class="login-form" action="{{loginurl}}" method="post" id="login">
            <input id="anchor" type="hidden" name="anchor" value="">
            <script>document.getElementById(\'anchor\').value = location.hash;</script>
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <div class="login-form-username mb-3">
                <label for="username" class="sr-only">
                    {{^canloginbyemail}}
                        {{#str}} username {{/str}}
                    {{/canloginbyemail}}
                    {{#canloginbyemail}}
                        {{#str}} usernameemail {{/str}}
                    {{/canloginbyemail}}
                </label>
                <input type="text" name="username" id="username" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}value="{{username}}" {{!
                    !}}placeholder="{{^canloginbyemail}}{{#cleanstr}}username{{/cleanstr}}{{/canloginbyemail}}{{!
                    !}}{{#canloginbyemail}}{{#cleanstr}}usernameemail{{/cleanstr}}{{/canloginbyemail}}" {{!
                    !}}autocomplete="username">
            </div>
            <div class="login-form-password mb-3">
                <label for="password" class="sr-only">{{#str}} password {{/str}}</label>
                <input type="password" name="password" id="password" value="" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}placeholder="{{#cleanstr}}password{{/cleanstr}}" {{!
                    !}}autocomplete="current-password">
            </div>
            {{#recaptcha}}
                <div class="login-form-recaptcha mb-3">
                    {{{recaptcha}}}
                </div>
            {{/recaptcha}}
            <div class="login-form-submit mb-3">
                <button class="btn {{localloginbtnclass}}" type="submit" id="loginbtn">{{#str}}login{{/str}}</button>
            </div>
            <div class="login-form-forgotpassword mb-3">
                <a href="{{forgotpasswordurl}}">{{#str}}forgotaccount{{/str}}</a>
            </div>
        </form>

        {{! Instructions (below form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbelow}}
        <div class="login-instructions login-instructions-local mt-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbelow}}
        {{/showlocallogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-start')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $buffer .= $indent . '
';
                $value = $context->find('showlocalloginintro');
                $buffer .= $this->section8bf6878a48886026eb91e21978b30b3e($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('showlocallogininstruction');
                $buffer .= $this->section1c0404421f329422ad970c93a3c60890($context, $indent, $value);
                $buffer .= $indent . '
';
                $buffer .= $indent . '        <form class="login-form" action="';
                $value = $this->resolveValue($context->find('loginurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" method="post" id="login">
';
                $buffer .= $indent . '            <input id="anchor" type="hidden" name="anchor" value="">
';
                $buffer .= $indent . '            <script>document.getElementById(\'anchor\').value = location.hash;</script>
';
                $buffer .= $indent . '            <input type="hidden" name="logintoken" value="';
                $value = $this->resolveValue($context->find('logintoken'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '            <div class="login-form-username mb-3">
';
                $buffer .= $indent . '                <label for="username" class="sr-only">
';
                $value = $context->find('canloginbyemail');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                        ';
                    $value = $context->find('str');
                    $buffer .= $this->section27e9419edc620e0e1872d2a6521f1533($context, $indent, $value);
                    $buffer .= '
';
                }
                $value = $context->find('canloginbyemail');
                $buffer .= $this->section310b720ee7c196969487e5e8484c0520($context, $indent, $value);
                $buffer .= $indent . '                </label>
';
                $buffer .= $indent . '                <input type="text" name="username" id="username" ';
                $buffer .= 'class="form-control form-control-lg" ';
                $buffer .= 'value="';
                $value = $this->resolveValue($context->find('username'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $buffer .= 'placeholder="';
                $value = $context->find('canloginbyemail');
                if (empty($value)) {
                    
                    $value = $context->find('cleanstr');
                    $buffer .= $this->sectionFea69428308e6a733cfeebf7670bdc01($context, $indent, $value);
                }
                $value = $context->find('canloginbyemail');
                $buffer .= $this->section118ece6c412804f669c845b43ecc9a01($context, $indent, $value);
                $buffer .= '" ';
                $buffer .= 'autocomplete="username">
';
                $buffer .= $indent . '            </div>
';
                $buffer .= $indent . '            <div class="login-form-password mb-3">
';
                $buffer .= $indent . '                <label for="password" class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionE056be559d6d01a9bd2bf6f760f8e3e3($context, $indent, $value);
                $buffer .= '</label>
';
                $buffer .= $indent . '                <input type="password" name="password" id="password" value="" ';
                $buffer .= 'class="form-control form-control-lg" ';
                $buffer .= 'placeholder="';
                $value = $context->find('cleanstr');
                $buffer .= $this->section4e50d9b1632f258e8c10be3e2ed759be($context, $indent, $value);
                $buffer .= '" ';
                $buffer .= 'autocomplete="current-password">
';
                $buffer .= $indent . '            </div>
';
                $value = $context->find('recaptcha');
                $buffer .= $this->section4060814e986c7dfa6be279f952adb993($context, $indent, $value);
                $buffer .= $indent . '            <div class="login-form-submit mb-3">
';
                $buffer .= $indent . '                <button class="btn ';
                $value = $this->resolveValue($context->find('localloginbtnclass'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" type="submit" id="loginbtn">';
                $value = $context->find('str');
                $buffer .= $this->sectionB15dee8971ab065bf4d6402b60d852be($context, $indent, $value);
                $buffer .= '</button>
';
                $buffer .= $indent . '            </div>
';
                $buffer .= $indent . '            <div class="login-form-forgotpassword mb-3">
';
                $buffer .= $indent . '                <a href="';
                $value = $this->resolveValue($context->find('forgotpasswordurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $context->find('str');
                $buffer .= $this->sectionE3afea308016df7243ba8871f7081e79($context, $indent, $value);
                $buffer .= '</a>
';
                $buffer .= $indent . '            </div>
';
                $buffer .= $indent . '        </form>
';
                $buffer .= $indent . '
';
                $value = $context->find('showlocallogininstruction');
                $buffer .= $this->section694e672fbf262003ebcfbb52a492a630($context, $indent, $value);
                $buffer .= $indent . '
';
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-end')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section001e4e46d4e851249d82aba08e85755d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#showlocallogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showlocalloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#localloginintrotext}}{{{localloginintrotext}}}{{/localloginintrotext}}{{^localloginintrotext}}{{#str}} loginlocalintro, theme_boost_union {{/str}}{{/localloginintrotext}}</h2>
        {{/showlocalloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbetween}}
        <div class="login-instructions login-instructions-local mb-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbetween}}
        {{/showlocallogininstruction}}

        <form class="login-form" action="{{loginurl}}" method="post" id="login">
            <input id="anchor" type="hidden" name="anchor" value="">
            <script>document.getElementById(\'anchor\').value = location.hash;</script>
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <div class="login-form-username mb-3">
                <label for="username" class="sr-only">
                    {{^canloginbyemail}}
                        {{#str}} username {{/str}}
                    {{/canloginbyemail}}
                    {{#canloginbyemail}}
                        {{#str}} usernameemail {{/str}}
                    {{/canloginbyemail}}
                </label>
                <input type="text" name="username" id="username" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}value="{{username}}" {{!
                    !}}placeholder="{{^canloginbyemail}}{{#cleanstr}}username{{/cleanstr}}{{/canloginbyemail}}{{!
                    !}}{{#canloginbyemail}}{{#cleanstr}}usernameemail{{/cleanstr}}{{/canloginbyemail}}" {{!
                    !}}autocomplete="username">
            </div>
            <div class="login-form-password mb-3">
                <label for="password" class="sr-only">{{#str}} password {{/str}}</label>
                <input type="password" name="password" id="password" value="" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}placeholder="{{#cleanstr}}password{{/cleanstr}}" {{!
                    !}}autocomplete="current-password">
            </div>
            {{#recaptcha}}
                <div class="login-form-recaptcha mb-3">
                    {{{recaptcha}}}
                </div>
            {{/recaptcha}}
            <div class="login-form-submit mb-3">
                <button class="btn {{localloginbtnclass}}" type="submit" id="loginbtn">{{#str}}login{{/str}}</button>
            </div>
            <div class="login-form-forgotpassword mb-3">
                <a href="{{forgotpasswordurl}}">{{#str}}forgotaccount{{/str}}</a>
            </div>
        </form>

        {{! Instructions (below form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbelow}}
        <div class="login-instructions login-instructions-local mt-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbelow}}
        {{/showlocallogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showlocallogin}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('showlocallogin');
                $buffer .= $this->section9efe120f7d18b673b67a00626641b46e($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3aa63d278a09988db81d480f491dbf3a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' text-break w-100';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' text-break w-100';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7e5d8b37fc0bfa16eea45496d4a998dc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#idpsplit}} text-break w-100{{/idpsplit}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('idpsplit');
                $buffer .= $this->section3aa63d278a09988db81d480f491dbf3a($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB192022769101897b91f6e2bbfe9912d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        {{label}}
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        ';
                $value = $this->resolveValue($context->find('label'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1d7e133d341e9a7df6da597ffa391a12(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{idploginintrotext}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('idploginintrotext'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE384f0e9b1fcc321a1a78dba1d43f63f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' potentialidps, auth ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' potentialidps, auth ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section05a7d03bf72f328a11d4a35936a251c7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{#idpsplit}}
                        {{label}}
                    {{/idpsplit}}
                    {{^idpsplit}}
                        {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                    {{/idpsplit}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('idpsplit');
                $buffer .= $this->sectionB192022769101897b91f6e2bbfe9912d($context, $indent, $value);
                $value = $context->find('idpsplit');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                        ';
                    $value = $context->find('idploginintrotext');
                    $buffer .= $this->section1d7e133d341e9a7df6da597ffa391a12($context, $indent, $value);
                    $value = $context->find('idploginintrotext');
                    if (empty($value)) {
                        
                        $value = $context->find('str');
                        $buffer .= $this->sectionE384f0e9b1fcc321a1a78dba1d43f63f($context, $indent, $value);
                    }
                    $buffer .= '
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section20c0b1f36f7fcca4c8612d5de948d01f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}{{#loginlayoutvertical}}{{#idpsplit}} text-break w-100{{/idpsplit}}{{/loginlayoutvertical}}">
                {{#loginlayoutvertical}}
                    {{#idpsplit}}
                        {{label}}
                    {{/idpsplit}}
                    {{^idpsplit}}
                        {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                    {{/idpsplit}}
                {{/loginlayoutvertical}}
                {{^loginlayoutvertical}}
                    {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                {{/loginlayoutvertical}}
            </h2>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <h2 class="login-heading ';
                $value = $context->find('loginlayoutaccordion');
                $buffer .= $this->section7700bd879a46a204d3a722d60abcf2ad($context, $indent, $value);
                $value = $context->find('loginlayoutvertical');
                $buffer .= $this->section7e5d8b37fc0bfa16eea45496d4a998dc($context, $indent, $value);
                $buffer .= '">
';
                $value = $context->find('loginlayoutvertical');
                $buffer .= $this->section05a7d03bf72f328a11d4a35936a251c7($context, $indent, $value);
                $value = $context->find('loginlayoutvertical');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                    ';
                    $value = $context->find('idploginintrotext');
                    $buffer .= $this->section1d7e133d341e9a7df6da597ffa391a12($context, $indent, $value);
                    $value = $context->find('idploginintrotext');
                    if (empty($value)) {
                        
                        $value = $context->find('str');
                        $buffer .= $this->sectionE384f0e9b1fcc321a1a78dba1d43f63f($context, $indent, $value);
                    }
                    $buffer .= '
';
                }
                $buffer .= $indent . '            </h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5b019f6a7b4bdc1b48506e76694f7eee(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="login-instructions login-instructions-idp mb-3">
                {{{idplogininstructions}}}
            </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="login-instructions login-instructions-idp mb-3">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->find('idplogininstructions'), $context);
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

    private function sectionD31741d8dd00efc948e143ff0b95a27f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{#idplogininstructionsbetween}}
            <div class="login-instructions login-instructions-idp mb-3">
                {{{idplogininstructions}}}
            </div>
            {{/idplogininstructionsbetween}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('idplogininstructionsbetween');
                $buffer .= $this->section5b019f6a7b4bdc1b48506e76694f7eee($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7d58571b6ce5144d4bee924af2b0581d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'auth_shibboleth_select_organization, auth_shibboleth';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'auth_shibboleth_select_organization, auth_shibboleth';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8ea1ca99abfe7d82fd4f2cf8e8ac4274(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'auth_shibboleth_select_member, auth_shibboleth';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'auth_shibboleth_select_member, auth_shibboleth';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9e2875c627d2dbad7c957250bbb623f7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'selected';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'selected';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCa7a15ece9ba27459c9aa5516e9cd637(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <option value="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $value = $context->find('selected');
                $buffer .= $this->section9e2875c627d2dbad7c957250bbb623f7($context, $indent, $value);
                $buffer .= '>';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section30a469d2425e0db62c56c709f51ef2eb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'auth_shib_contact_administrator, auth_shibboleth, ';
                $value = $this->resolveValue($context->find('adminemail'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4893e60b5feb5df5cdeeb722dfa26a7e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <form class="login-shibboleth-wayf-form mb-2" action="{{shibbolethloginurl}}" method="post" id="{{wayfformid}}">
                        <div class="mb-3">
                            <label for="{{wayfformid}}-idp" class="form-label">{{#str}}auth_shibboleth_select_organization, auth_shibboleth{{/str}}</label>
                            <select id="{{wayfformid}}-idp" name="idp" class="form-control w-100">
                                <option value="-">{{#str}}auth_shibboleth_select_member, auth_shibboleth{{/str}}</option>
                                {{#shibbidps}}
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                {{/shibbidps}}
                            </select>
                        </div>
                        <button type="submit" class="btn {{idploginbtnclass}} w-100" accesskey="s">
                            {{#str}}login{{/str}}
                        </button>
                        <p class="form-text text-muted mt-2 mb-0 text-break">
                            {{#str}}auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}{{/str}}
                        </p>
                    </form>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <form class="login-shibboleth-wayf-form mb-2" action="';
                $value = $this->resolveValue($context->find('shibbolethloginurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" method="post" id="';
                $value = $this->resolveValue($context->find('wayfformid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                        <div class="mb-3">
';
                $buffer .= $indent . '                            <label for="';
                $value = $this->resolveValue($context->find('wayfformid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-idp" class="form-label">';
                $value = $context->find('str');
                $buffer .= $this->section7d58571b6ce5144d4bee924af2b0581d($context, $indent, $value);
                $buffer .= '</label>
';
                $buffer .= $indent . '                            <select id="';
                $value = $this->resolveValue($context->find('wayfformid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-idp" name="idp" class="form-control w-100">
';
                $buffer .= $indent . '                                <option value="-">';
                $value = $context->find('str');
                $buffer .= $this->section8ea1ca99abfe7d82fd4f2cf8e8ac4274($context, $indent, $value);
                $buffer .= '</option>
';
                $value = $context->find('shibbidps');
                $buffer .= $this->sectionCa7a15ece9ba27459c9aa5516e9cd637($context, $indent, $value);
                $buffer .= $indent . '                            </select>
';
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                        <button type="submit" class="btn ';
                $value = $this->resolveValue($context->find('idploginbtnclass'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' w-100" accesskey="s">
';
                $buffer .= $indent . '                            ';
                $value = $context->find('str');
                $buffer .= $this->sectionB15dee8971ab065bf4d6402b60d852be($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </button>
';
                $buffer .= $indent . '                        <p class="form-text text-muted mt-2 mb-0 text-break">
';
                $buffer .= $indent . '                            ';
                $value = $context->find('str');
                $buffer .= $this->section30a469d2425e0db62c56c709f51ef2eb($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </p>
';
                $buffer .= $indent . '                    </form>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section27616e9c5b7b72845decb91e5625f6be(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <img src="{{iconurl}}" alt="" width="24" height="24"/>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <img src="';
                $value = $this->resolveValue($context->find('iconurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" alt="" width="24" height="24"/>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section96ea37cb2b9882c5634dde00ca93875a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{! Use internal WAYF for Shibboleth }}
                    {{#useinternalwayf}}
                    <form class="login-shibboleth-wayf-form mb-2" action="{{shibbolethloginurl}}" method="post" id="{{wayfformid}}">
                        <div class="mb-3">
                            <label for="{{wayfformid}}-idp" class="form-label">{{#str}}auth_shibboleth_select_organization, auth_shibboleth{{/str}}</label>
                            <select id="{{wayfformid}}-idp" name="idp" class="form-control w-100">
                                <option value="-">{{#str}}auth_shibboleth_select_member, auth_shibboleth{{/str}}</option>
                                {{#shibbidps}}
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                {{/shibbidps}}
                            </select>
                        </div>
                        <button type="submit" class="btn {{idploginbtnclass}} w-100" accesskey="s">
                            {{#str}}login{{/str}}
                        </button>
                        <p class="form-text text-muted mt-2 mb-0 text-break">
                            {{#str}}auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}{{/str}}
                        </p>
                    </form>
                    {{/useinternalwayf}}
                    {{! Use standard IDP button }}
                    {{^useinternalwayf}}
                    <a class="btn {{idploginbtnclass}} btn-block " href="{{url}}">
                        {{#iconurl}}
                            <img src="{{iconurl}}" alt="" width="24" height="24"/>
                        {{/iconurl}}
                        {{name}}
                    </a>
                    {{/useinternalwayf}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('useinternalwayf');
                $buffer .= $this->section4893e60b5feb5df5cdeeb722dfa26a7e($context, $indent, $value);
                $value = $context->find('useinternalwayf');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                    <a class="btn ';
                    $value = $this->resolveValue($context->find('idploginbtnclass'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= ' btn-block " href="';
                    $value = $this->resolveValue($context->find('url'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '">
';
                    $value = $context->find('iconurl');
                    $buffer .= $this->section27616e9c5b7b72845decb91e5625f6be($context, $indent, $value);
                    $buffer .= $indent . '                        ';
                    $value = $this->resolveValue($context->find('name'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '
';
                    $buffer .= $indent . '                    </a>
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section24809743299e697c8b09a7dfeaa41fd2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{{shibbolethembeddedwayfcode}}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->find('shibbolethembeddedwayfcode'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB614bf3811e03a18ec11064cf3664ba2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-idp mt-3">
            {{{idplogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-idp mt-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('idplogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section74e7cdd3e8b0003d722b2f0233e77713(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#idplogininstructionsbelow}}
        <div class="login-instructions login-instructions-idp mt-3">
            {{{idplogininstructions}}}
        </div>
        {{/idplogininstructionsbelow}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('idplogininstructionsbelow');
                $buffer .= $this->sectionB614bf3811e03a18ec11064cf3664ba2($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE01a4a0c046b659ad753a044d0416884(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-identityproviders">
            {{! Intro heading (For the vertical layout with split IDP, this is the provider name. Otherwise, it is the intro text) }}
            {{#showidploginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}{{#loginlayoutvertical}}{{#idpsplit}} text-break w-100{{/idpsplit}}{{/loginlayoutvertical}}">
                {{#loginlayoutvertical}}
                    {{#idpsplit}}
                        {{label}}
                    {{/idpsplit}}
                    {{^idpsplit}}
                        {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                    {{/idpsplit}}
                {{/loginlayoutvertical}}
                {{^loginlayoutvertical}}
                    {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                {{/loginlayoutvertical}}
            </h2>
            {{/showidploginintro}}

            {{! Instructions (between heading and form) }}
            {{#showidplogininstruction}}
            {{#idplogininstructionsbetween}}
            <div class="login-instructions login-instructions-idp mb-3">
                {{{idplogininstructions}}}
            </div>
            {{/idplogininstructionsbetween}}
            {{/showidplogininstruction}}

            {{! If not using embedded WAYF code. }}
            {{^showshibbolethembeddedwayfcode}}
                {{#identityproviders}}
                    {{! Use internal WAYF for Shibboleth }}
                    {{#useinternalwayf}}
                    <form class="login-shibboleth-wayf-form mb-2" action="{{shibbolethloginurl}}" method="post" id="{{wayfformid}}">
                        <div class="mb-3">
                            <label for="{{wayfformid}}-idp" class="form-label">{{#str}}auth_shibboleth_select_organization, auth_shibboleth{{/str}}</label>
                            <select id="{{wayfformid}}-idp" name="idp" class="form-control w-100">
                                <option value="-">{{#str}}auth_shibboleth_select_member, auth_shibboleth{{/str}}</option>
                                {{#shibbidps}}
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                {{/shibbidps}}
                            </select>
                        </div>
                        <button type="submit" class="btn {{idploginbtnclass}} w-100" accesskey="s">
                            {{#str}}login{{/str}}
                        </button>
                        <p class="form-text text-muted mt-2 mb-0 text-break">
                            {{#str}}auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}{{/str}}
                        </p>
                    </form>
                    {{/useinternalwayf}}
                    {{! Use standard IDP button }}
                    {{^useinternalwayf}}
                    <a class="btn {{idploginbtnclass}} btn-block " href="{{url}}">
                        {{#iconurl}}
                            <img src="{{iconurl}}" alt="" width="24" height="24"/>
                        {{/iconurl}}
                        {{name}}
                    </a>
                    {{/useinternalwayf}}
                {{/identityproviders}}
            {{/showshibbolethembeddedwayfcode}}
            {{! Otherwise, if using embedded WAYF code, display the configured JavaScript code. }}
            {{#showshibbolethembeddedwayfcode}}
                {{{shibbolethembeddedwayfcode}}}
            {{/showshibbolethembeddedwayfcode}}
        </div>

        {{! Instructions (below form) }}
        {{#showidplogininstruction}}
        {{#idplogininstructionsbelow}}
        <div class="login-instructions login-instructions-idp mt-3">
            {{{idplogininstructions}}}
        </div>
        {{/idplogininstructionsbelow}}
        {{/showidplogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-start')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $buffer .= $indent . '
';
                $buffer .= $indent . '        <div class="login-identityproviders">
';
                $value = $context->find('showidploginintro');
                $buffer .= $this->section20c0b1f36f7fcca4c8612d5de948d01f($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('showidplogininstruction');
                $buffer .= $this->sectionD31741d8dd00efc948e143ff0b95a27f($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('showshibbolethembeddedwayfcode');
                if (empty($value)) {
                    
                    $value = $context->find('identityproviders');
                    $buffer .= $this->section96ea37cb2b9882c5634dde00ca93875a($context, $indent, $value);
                }
                $value = $context->find('showshibbolethembeddedwayfcode');
                $buffer .= $this->section24809743299e697c8b09a7dfeaa41fd2($context, $indent, $value);
                $buffer .= $indent . '        </div>
';
                $buffer .= $indent . '
';
                $value = $context->find('showidplogininstruction');
                $buffer .= $this->section74e7cdd3e8b0003d722b2f0233e77713($context, $indent, $value);
                $buffer .= $indent . '
';
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-end')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3d94dd9afd6aa3b145ebfd4482bd95f7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#showidplogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-identityproviders">
            {{! Intro heading (For the vertical layout with split IDP, this is the provider name. Otherwise, it is the intro text) }}
            {{#showidploginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}{{#loginlayoutvertical}}{{#idpsplit}} text-break w-100{{/idpsplit}}{{/loginlayoutvertical}}">
                {{#loginlayoutvertical}}
                    {{#idpsplit}}
                        {{label}}
                    {{/idpsplit}}
                    {{^idpsplit}}
                        {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                    {{/idpsplit}}
                {{/loginlayoutvertical}}
                {{^loginlayoutvertical}}
                    {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                {{/loginlayoutvertical}}
            </h2>
            {{/showidploginintro}}

            {{! Instructions (between heading and form) }}
            {{#showidplogininstruction}}
            {{#idplogininstructionsbetween}}
            <div class="login-instructions login-instructions-idp mb-3">
                {{{idplogininstructions}}}
            </div>
            {{/idplogininstructionsbetween}}
            {{/showidplogininstruction}}

            {{! If not using embedded WAYF code. }}
            {{^showshibbolethembeddedwayfcode}}
                {{#identityproviders}}
                    {{! Use internal WAYF for Shibboleth }}
                    {{#useinternalwayf}}
                    <form class="login-shibboleth-wayf-form mb-2" action="{{shibbolethloginurl}}" method="post" id="{{wayfformid}}">
                        <div class="mb-3">
                            <label for="{{wayfformid}}-idp" class="form-label">{{#str}}auth_shibboleth_select_organization, auth_shibboleth{{/str}}</label>
                            <select id="{{wayfformid}}-idp" name="idp" class="form-control w-100">
                                <option value="-">{{#str}}auth_shibboleth_select_member, auth_shibboleth{{/str}}</option>
                                {{#shibbidps}}
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                {{/shibbidps}}
                            </select>
                        </div>
                        <button type="submit" class="btn {{idploginbtnclass}} w-100" accesskey="s">
                            {{#str}}login{{/str}}
                        </button>
                        <p class="form-text text-muted mt-2 mb-0 text-break">
                            {{#str}}auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}{{/str}}
                        </p>
                    </form>
                    {{/useinternalwayf}}
                    {{! Use standard IDP button }}
                    {{^useinternalwayf}}
                    <a class="btn {{idploginbtnclass}} btn-block " href="{{url}}">
                        {{#iconurl}}
                            <img src="{{iconurl}}" alt="" width="24" height="24"/>
                        {{/iconurl}}
                        {{name}}
                    </a>
                    {{/useinternalwayf}}
                {{/identityproviders}}
            {{/showshibbolethembeddedwayfcode}}
            {{! Otherwise, if using embedded WAYF code, display the configured JavaScript code. }}
            {{#showshibbolethembeddedwayfcode}}
                {{{shibbolethembeddedwayfcode}}}
            {{/showshibbolethembeddedwayfcode}}
        </div>

        {{! Instructions (below form) }}
        {{#showidplogininstruction}}
        {{#idplogininstructionsbelow}}
        <div class="login-instructions login-instructions-idp mt-3">
            {{{idplogininstructions}}}
        </div>
        {{/idplogininstructionsbelow}}
        {{/showidplogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showidplogin}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('showidplogin');
                $buffer .= $this->sectionE01a4a0c046b659ad753a044d0416884($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8ebf34e5f2e5eba6400b91e499293bb0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{selfregistrationloginintrotext}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('selfregistrationloginintrotext'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9e7e1656a410e28ad447bc910c287930(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' firsttime ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' firsttime ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section70dad71ae16f8d4d1f70a7c0b31aafaf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#selfregistrationloginintrotext}}{{{selfregistrationloginintrotext}}}{{/selfregistrationloginintrotext}}{{^selfregistrationloginintrotext}}{{#str}} firsttime {{/str}}{{/selfregistrationloginintrotext}}</h2>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <h2 class="login-heading ';
                $value = $context->find('loginlayoutaccordion');
                $buffer .= $this->section7700bd879a46a204d3a722d60abcf2ad($context, $indent, $value);
                $buffer .= '">';
                $value = $context->find('selfregistrationloginintrotext');
                $buffer .= $this->section8ebf34e5f2e5eba6400b91e499293bb0($context, $indent, $value);
                $value = $context->find('selfregistrationloginintrotext');
                if (empty($value)) {
                    
                    $value = $context->find('str');
                    $buffer .= $this->section9e7e1656a410e28ad447bc910c287930($context, $indent, $value);
                }
                $buffer .= '</h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9140c708ea60f4741cd18eaca1dd7aca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="login-instructions login-instructions-firsttimesignup mb-3">
                {{{selfregistrationlogininstructions}}}
            </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="login-instructions login-instructions-firsttimesignup mb-3">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->find('selfregistrationlogininstructions'), $context);
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

    private function sectionA152efab60bf028172f48da7a9a41aaf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{#selfregistrationlogininstructionsbetween}}
            <div class="login-instructions login-instructions-firsttimesignup mb-3">
                {{{selfregistrationlogininstructions}}}
            </div>
            {{/selfregistrationlogininstructionsbetween}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('selfregistrationlogininstructionsbetween');
                $buffer .= $this->section9140c708ea60f4741cd18eaca1dd7aca($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section47f819a53e4575a4e7767be1939ab3bf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'startsignup';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'startsignup';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEff753100431e4c8e6bbfa12921274a9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-firsttimesignup mt-3">
            {{{selfregistrationlogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-firsttimesignup mt-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('selfregistrationlogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section608154ebacb881987d6265ce2d509bee(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#selfregistrationlogininstructionsbelow}}
        <div class="login-instructions login-instructions-firsttimesignup mt-3">
            {{{selfregistrationlogininstructions}}}
        </div>
        {{/selfregistrationlogininstructionsbelow}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('selfregistrationlogininstructionsbelow');
                $buffer .= $this->sectionEff753100431e4c8e6bbfa12921274a9($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC9047cefd2d2d4730ef30be8bd519e2c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-signup-content">
            {{! Intro heading }}
            {{#showselfregistrationloginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#selfregistrationloginintrotext}}{{{selfregistrationloginintrotext}}}{{/selfregistrationloginintrotext}}{{^selfregistrationloginintrotext}}{{#str}} firsttime {{/str}}{{/selfregistrationloginintrotext}}</h2>
            {{/showselfregistrationloginintro}}

            {{! Instructions (between heading and form) }}
            {{#showselfregistrationlogininstruction}}
            {{#selfregistrationlogininstructionsbetween}}
            <div class="login-instructions login-instructions-firsttimesignup mb-3">
                {{{selfregistrationlogininstructions}}}
            </div>
            {{/selfregistrationlogininstructionsbetween}}
            {{/showselfregistrationlogininstruction}}
        </div>

        <div class="login-signup">
            <a class="btn {{selfregistrationloginbtnclass}}" href="{{signupurl}}">{{#str}}startsignup{{/str}}</a>
        </div>

        {{! Instructions (below form) }}
        {{#showselfregistrationlogininstruction}}
        {{#selfregistrationlogininstructionsbelow}}
        <div class="login-instructions login-instructions-firsttimesignup mt-3">
            {{{selfregistrationlogininstructions}}}
        </div>
        {{/selfregistrationlogininstructionsbelow}}
        {{/showselfregistrationlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-start')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $buffer .= $indent . '
';
                $buffer .= $indent . '        <div class="login-signup-content">
';
                $value = $context->find('showselfregistrationloginintro');
                $buffer .= $this->section70dad71ae16f8d4d1f70a7c0b31aafaf($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('showselfregistrationlogininstruction');
                $buffer .= $this->sectionA152efab60bf028172f48da7a9a41aaf($context, $indent, $value);
                $buffer .= $indent . '        </div>
';
                $buffer .= $indent . '
';
                $buffer .= $indent . '        <div class="login-signup">
';
                $buffer .= $indent . '            <a class="btn ';
                $value = $this->resolveValue($context->find('selfregistrationloginbtnclass'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" href="';
                $value = $this->resolveValue($context->find('signupurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $context->find('str');
                $buffer .= $this->section47f819a53e4575a4e7767be1939ab3bf($context, $indent, $value);
                $buffer .= '</a>
';
                $buffer .= $indent . '        </div>
';
                $buffer .= $indent . '
';
                $value = $context->find('showselfregistrationlogininstruction');
                $buffer .= $this->section608154ebacb881987d6265ce2d509bee($context, $indent, $value);
                $buffer .= $indent . '
';
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-end')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9577de55a3151931aedeadcfd611fad8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#showselfregistration}}
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-signup-content">
            {{! Intro heading }}
            {{#showselfregistrationloginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#selfregistrationloginintrotext}}{{{selfregistrationloginintrotext}}}{{/selfregistrationloginintrotext}}{{^selfregistrationloginintrotext}}{{#str}} firsttime {{/str}}{{/selfregistrationloginintrotext}}</h2>
            {{/showselfregistrationloginintro}}

            {{! Instructions (between heading and form) }}
            {{#showselfregistrationlogininstruction}}
            {{#selfregistrationlogininstructionsbetween}}
            <div class="login-instructions login-instructions-firsttimesignup mb-3">
                {{{selfregistrationlogininstructions}}}
            </div>
            {{/selfregistrationlogininstructionsbetween}}
            {{/showselfregistrationlogininstruction}}
        </div>

        <div class="login-signup">
            <a class="btn {{selfregistrationloginbtnclass}}" href="{{signupurl}}">{{#str}}startsignup{{/str}}</a>
        </div>

        {{! Instructions (below form) }}
        {{#showselfregistrationlogininstruction}}
        {{#selfregistrationlogininstructionsbelow}}
        <div class="login-instructions login-instructions-firsttimesignup mt-3">
            {{{selfregistrationlogininstructions}}}
        </div>
        {{/selfregistrationlogininstructionsbelow}}
        {{/showselfregistrationlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showselfregistration}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('showselfregistration');
                $buffer .= $this->sectionC9047cefd2d2d4730ef30be8bd519e2c($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1283b65d5312d8407e564b8037e3a938(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{guestloginintrotext}}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('guestloginintrotext'), $context);
                $buffer .= ($value === null ? '' : $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section93e4b62aaf677bf7878b06c5ac540671(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'someallowguest';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'someallowguest';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section37c3d99ee1d99395263cbc28b7f4033b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#guestloginintrotext}}{{{guestloginintrotext}}}{{/guestloginintrotext}}{{^guestloginintrotext}}{{#str}}someallowguest{{/str}}{{/guestloginintrotext}}</h2>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <h2 class="login-heading ';
                $value = $context->find('loginlayoutaccordion');
                $buffer .= $this->section7700bd879a46a204d3a722d60abcf2ad($context, $indent, $value);
                $buffer .= '">';
                $value = $context->find('guestloginintrotext');
                $buffer .= $this->section1283b65d5312d8407e564b8037e3a938($context, $indent, $value);
                $value = $context->find('guestloginintrotext');
                if (empty($value)) {
                    
                    $value = $context->find('str');
                    $buffer .= $this->section93e4b62aaf677bf7878b06c5ac540671($context, $indent, $value);
                }
                $buffer .= '</h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC17844b3797c2e471b8399da0c172431(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-guest mb-3">
            {{{guestlogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-guest mb-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('guestlogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDcef40d2bf447a3e5fedbd95f1639d29(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#guestlogininstructionsbetween}}
        <div class="login-instructions login-instructions-guest mb-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbetween}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('guestlogininstructionsbetween');
                $buffer .= $this->sectionC17844b3797c2e471b8399da0c172431($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section017c9686023b74877131737c59ff1162(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'loginguest';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'loginguest';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section40ddc688e1db3844eee7e738204620cc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="login-instructions login-instructions-guest mt-3">
            {{{guestlogininstructions}}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="login-instructions login-instructions-guest mt-3">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('guestlogininstructions'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF20bfdf39c97294c55997f907546feb7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#guestlogininstructionsbelow}}
        <div class="login-instructions login-instructions-guest mt-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbelow}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('guestlogininstructionsbelow');
                $buffer .= $this->section40ddc688e1db3844eee7e738204620cc($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section418c3389de6a5593f367667df0047e9e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showguestloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#guestloginintrotext}}{{{guestloginintrotext}}}{{/guestloginintrotext}}{{^guestloginintrotext}}{{#str}}someallowguest{{/str}}{{/guestloginintrotext}}</h2>
        {{/showguestloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbetween}}
        <div class="login-instructions login-instructions-guest mb-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbetween}}
        {{/showguestlogininstruction}}

        <form action="{{loginurl}}" method="post" id="guestlogin">
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <input type="hidden" name="username" value="guest" />
            <input type="hidden" name="password" value="guest" />
            <button class="btn {{guestloginbtnclass}}" type="submit" id="loginguestbtn">{{#str}}loginguest{{/str}}</button>
        </form>

        {{! Instructions (below form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbelow}}
        <div class="login-instructions login-instructions-guest mt-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbelow}}
        {{/showguestlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-start')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $buffer .= $indent . '
';
                $value = $context->find('showguestloginintro');
                $buffer .= $this->section37c3d99ee1d99395263cbc28b7f4033b($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('showguestlogininstruction');
                $buffer .= $this->sectionDcef40d2bf447a3e5fedbd95f1639d29($context, $indent, $value);
                $buffer .= $indent . '
';
                $buffer .= $indent . '        <form action="';
                $value = $this->resolveValue($context->find('loginurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" method="post" id="guestlogin">
';
                $buffer .= $indent . '            <input type="hidden" name="logintoken" value="';
                $value = $this->resolveValue($context->find('logintoken'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '            <input type="hidden" name="username" value="guest" />
';
                $buffer .= $indent . '            <input type="hidden" name="password" value="guest" />
';
                $buffer .= $indent . '            <button class="btn ';
                $value = $this->resolveValue($context->find('guestloginbtnclass'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" type="submit" id="loginguestbtn">';
                $value = $context->find('str');
                $buffer .= $this->section017c9686023b74877131737c59ff1162($context, $indent, $value);
                $buffer .= '</button>
';
                $buffer .= $indent . '        </form>
';
                $buffer .= $indent . '
';
                $value = $context->find('showguestlogininstruction');
                $buffer .= $this->sectionF20bfdf39c97294c55997f907546feb7($context, $indent, $value);
                $buffer .= $indent . '
';
                if ($partial = $this->mustache->loadPartial('theme_boost_union/loginform-partial-method-end')) {
                    $buffer .= $partial->renderInternal($context, $indent . '    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA4658e42f0fe1ea38f779300a0617c1a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#showguestlogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showguestloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#guestloginintrotext}}{{{guestloginintrotext}}}{{/guestloginintrotext}}{{^guestloginintrotext}}{{#str}}someallowguest{{/str}}{{/guestloginintrotext}}</h2>
        {{/showguestloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbetween}}
        <div class="login-instructions login-instructions-guest mb-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbetween}}
        {{/showguestlogininstruction}}

        <form action="{{loginurl}}" method="post" id="guestlogin">
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <input type="hidden" name="username" value="guest" />
            <input type="hidden" name="password" value="guest" />
            <button class="btn {{guestloginbtnclass}}" type="submit" id="loginguestbtn">{{#str}}loginguest{{/str}}</button>
        </form>

        {{! Instructions (below form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbelow}}
        <div class="login-instructions login-instructions-guest mt-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbelow}}
        {{/showguestlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showguestlogin}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('showguestlogin');
                $buffer .= $this->section418c3389de6a5593f367667df0047e9e($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD6e5a5cee86741981016c0d54e80cede(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '

    {{! --------------------------------------------------------------------------- }}
    {{! METHOD: Local Login }}
    {{! --------------------------------------------------------------------------- }}
    {{#islocal}}
    {{#showlocallogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showlocalloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#localloginintrotext}}{{{localloginintrotext}}}{{/localloginintrotext}}{{^localloginintrotext}}{{#str}} loginlocalintro, theme_boost_union {{/str}}{{/localloginintrotext}}</h2>
        {{/showlocalloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbetween}}
        <div class="login-instructions login-instructions-local mb-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbetween}}
        {{/showlocallogininstruction}}

        <form class="login-form" action="{{loginurl}}" method="post" id="login">
            <input id="anchor" type="hidden" name="anchor" value="">
            <script>document.getElementById(\'anchor\').value = location.hash;</script>
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <div class="login-form-username mb-3">
                <label for="username" class="sr-only">
                    {{^canloginbyemail}}
                        {{#str}} username {{/str}}
                    {{/canloginbyemail}}
                    {{#canloginbyemail}}
                        {{#str}} usernameemail {{/str}}
                    {{/canloginbyemail}}
                </label>
                <input type="text" name="username" id="username" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}value="{{username}}" {{!
                    !}}placeholder="{{^canloginbyemail}}{{#cleanstr}}username{{/cleanstr}}{{/canloginbyemail}}{{!
                    !}}{{#canloginbyemail}}{{#cleanstr}}usernameemail{{/cleanstr}}{{/canloginbyemail}}" {{!
                    !}}autocomplete="username">
            </div>
            <div class="login-form-password mb-3">
                <label for="password" class="sr-only">{{#str}} password {{/str}}</label>
                <input type="password" name="password" id="password" value="" {{!
                    !}}class="form-control form-control-lg" {{!
                    !}}placeholder="{{#cleanstr}}password{{/cleanstr}}" {{!
                    !}}autocomplete="current-password">
            </div>
            {{#recaptcha}}
                <div class="login-form-recaptcha mb-3">
                    {{{recaptcha}}}
                </div>
            {{/recaptcha}}
            <div class="login-form-submit mb-3">
                <button class="btn {{localloginbtnclass}}" type="submit" id="loginbtn">{{#str}}login{{/str}}</button>
            </div>
            <div class="login-form-forgotpassword mb-3">
                <a href="{{forgotpasswordurl}}">{{#str}}forgotaccount{{/str}}</a>
            </div>
        </form>

        {{! Instructions (below form) }}
        {{#showlocallogininstruction}}
        {{#locallogininstructionsbelow}}
        <div class="login-instructions login-instructions-local mt-3">
            {{{locallogininstructions}}}
        </div>
        {{/locallogininstructionsbelow}}
        {{/showlocallogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showlocallogin}}
    {{/islocal}}

    {{! --------------------------------------------------------------------------- }}
    {{! METHOD: Identity Providers (OAuth2, SAML, etc.) }}
    {{! --------------------------------------------------------------------------- }}
    {{#isidp}}
    {{#showidplogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-identityproviders">
            {{! Intro heading (For the vertical layout with split IDP, this is the provider name. Otherwise, it is the intro text) }}
            {{#showidploginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}{{#loginlayoutvertical}}{{#idpsplit}} text-break w-100{{/idpsplit}}{{/loginlayoutvertical}}">
                {{#loginlayoutvertical}}
                    {{#idpsplit}}
                        {{label}}
                    {{/idpsplit}}
                    {{^idpsplit}}
                        {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                    {{/idpsplit}}
                {{/loginlayoutvertical}}
                {{^loginlayoutvertical}}
                    {{#idploginintrotext}}{{{idploginintrotext}}}{{/idploginintrotext}}{{^idploginintrotext}}{{#str}} potentialidps, auth {{/str}}{{/idploginintrotext}}
                {{/loginlayoutvertical}}
            </h2>
            {{/showidploginintro}}

            {{! Instructions (between heading and form) }}
            {{#showidplogininstruction}}
            {{#idplogininstructionsbetween}}
            <div class="login-instructions login-instructions-idp mb-3">
                {{{idplogininstructions}}}
            </div>
            {{/idplogininstructionsbetween}}
            {{/showidplogininstruction}}

            {{! If not using embedded WAYF code. }}
            {{^showshibbolethembeddedwayfcode}}
                {{#identityproviders}}
                    {{! Use internal WAYF for Shibboleth }}
                    {{#useinternalwayf}}
                    <form class="login-shibboleth-wayf-form mb-2" action="{{shibbolethloginurl}}" method="post" id="{{wayfformid}}">
                        <div class="mb-3">
                            <label for="{{wayfformid}}-idp" class="form-label">{{#str}}auth_shibboleth_select_organization, auth_shibboleth{{/str}}</label>
                            <select id="{{wayfformid}}-idp" name="idp" class="form-control w-100">
                                <option value="-">{{#str}}auth_shibboleth_select_member, auth_shibboleth{{/str}}</option>
                                {{#shibbidps}}
                                <option value="{{value}}" {{#selected}}selected{{/selected}}>{{name}}</option>
                                {{/shibbidps}}
                            </select>
                        </div>
                        <button type="submit" class="btn {{idploginbtnclass}} w-100" accesskey="s">
                            {{#str}}login{{/str}}
                        </button>
                        <p class="form-text text-muted mt-2 mb-0 text-break">
                            {{#str}}auth_shib_contact_administrator, auth_shibboleth, {{adminemail}}{{/str}}
                        </p>
                    </form>
                    {{/useinternalwayf}}
                    {{! Use standard IDP button }}
                    {{^useinternalwayf}}
                    <a class="btn {{idploginbtnclass}} btn-block " href="{{url}}">
                        {{#iconurl}}
                            <img src="{{iconurl}}" alt="" width="24" height="24"/>
                        {{/iconurl}}
                        {{name}}
                    </a>
                    {{/useinternalwayf}}
                {{/identityproviders}}
            {{/showshibbolethembeddedwayfcode}}
            {{! Otherwise, if using embedded WAYF code, display the configured JavaScript code. }}
            {{#showshibbolethembeddedwayfcode}}
                {{{shibbolethembeddedwayfcode}}}
            {{/showshibbolethembeddedwayfcode}}
        </div>

        {{! Instructions (below form) }}
        {{#showidplogininstruction}}
        {{#idplogininstructionsbelow}}
        <div class="login-instructions login-instructions-idp mt-3">
            {{{idplogininstructions}}}
        </div>
        {{/idplogininstructionsbelow}}
        {{/showidplogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showidplogin}}
    {{/isidp}}

    {{! --------------------------------------------------------------------------- }}
    {{! METHOD: Signup / First Time User }}
    {{! --------------------------------------------------------------------------- }}
    {{#isfirsttimesignup}}
    {{#showselfregistration}}
    {{>theme_boost_union/loginform-partial-method-start}}

        <div class="login-signup-content">
            {{! Intro heading }}
            {{#showselfregistrationloginintro}}
            <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#selfregistrationloginintrotext}}{{{selfregistrationloginintrotext}}}{{/selfregistrationloginintrotext}}{{^selfregistrationloginintrotext}}{{#str}} firsttime {{/str}}{{/selfregistrationloginintrotext}}</h2>
            {{/showselfregistrationloginintro}}

            {{! Instructions (between heading and form) }}
            {{#showselfregistrationlogininstruction}}
            {{#selfregistrationlogininstructionsbetween}}
            <div class="login-instructions login-instructions-firsttimesignup mb-3">
                {{{selfregistrationlogininstructions}}}
            </div>
            {{/selfregistrationlogininstructionsbetween}}
            {{/showselfregistrationlogininstruction}}
        </div>

        <div class="login-signup">
            <a class="btn {{selfregistrationloginbtnclass}}" href="{{signupurl}}">{{#str}}startsignup{{/str}}</a>
        </div>

        {{! Instructions (below form) }}
        {{#showselfregistrationlogininstruction}}
        {{#selfregistrationlogininstructionsbelow}}
        <div class="login-instructions login-instructions-firsttimesignup mt-3">
            {{{selfregistrationlogininstructions}}}
        </div>
        {{/selfregistrationlogininstructionsbelow}}
        {{/showselfregistrationlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showselfregistration}}
    {{/isfirsttimesignup}}

    {{! --------------------------------------------------------------------------- }}
    {{! METHOD: Guest Login }}
    {{! --------------------------------------------------------------------------- }}
    {{#isguest}}
    {{#showguestlogin}}
    {{>theme_boost_union/loginform-partial-method-start}}

        {{! Intro heading }}
        {{#showguestloginintro}}
        <h2 class="login-heading {{#loginlayoutaccordion}}text-break w-100{{/loginlayoutaccordion}}">{{#guestloginintrotext}}{{{guestloginintrotext}}}{{/guestloginintrotext}}{{^guestloginintrotext}}{{#str}}someallowguest{{/str}}{{/guestloginintrotext}}</h2>
        {{/showguestloginintro}}

        {{! Instructions (between heading and form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbetween}}
        <div class="login-instructions login-instructions-guest mb-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbetween}}
        {{/showguestlogininstruction}}

        <form action="{{loginurl}}" method="post" id="guestlogin">
            <input type="hidden" name="logintoken" value="{{logintoken}}">
            <input type="hidden" name="username" value="guest" />
            <input type="hidden" name="password" value="guest" />
            <button class="btn {{guestloginbtnclass}}" type="submit" id="loginguestbtn">{{#str}}loginguest{{/str}}</button>
        </form>

        {{! Instructions (below form) }}
        {{#showguestlogininstruction}}
        {{#guestlogininstructionsbelow}}
        <div class="login-instructions login-instructions-guest mt-3">
            {{{guestlogininstructions}}}
        </div>
        {{/guestlogininstructionsbelow}}
        {{/showguestlogininstruction}}

    {{>theme_boost_union/loginform-partial-method-end}}
    {{/showguestlogin}}
    {{/isguest}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '
';
                $value = $context->find('islocal');
                $buffer .= $this->section001e4e46d4e851249d82aba08e85755d($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('isidp');
                $buffer .= $this->section3d94dd9afd6aa3b145ebfd4482bd95f7($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('isfirsttimesignup');
                $buffer .= $this->section9577de55a3151931aedeadcfd611fad8($context, $indent, $value);
                $buffer .= $indent . '
';
                $value = $context->find('isguest');
                $buffer .= $this->sectionA4658e42f0fe1ea38f779300a0617c1a($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7339a3c9b0530cc875c39a1527266311(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE8093273a32c613eb9fff83b35adaee4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div class="mb-5"></div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div class="mb-5"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFe71f21b427f54119c255b1e105ff3b5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div class="login-instructions login-instructions-below mt-4">
        {{{logininstructionsbelow}}}
    </div>
    <div class="login-divider"></div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div class="login-instructions login-instructions-below mt-4">
';
                $buffer .= $indent . '        ';
                $value = $this->resolveValue($context->find('logininstructionsbelow'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '    </div>
';
                $buffer .= $indent . '    <div class="login-divider"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF14d80a3e07dfc2666df87d17d3d8cc8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="login-languagemenu">
                {{>core/action_menu}}
            </div>
            <div class="divider border-start align-self-center mx-3"></div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="login-languagemenu">
';
                if ($partial = $this->mustache->loadPartial('core/action_menu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $buffer .= $indent . '            </div>
';
                $buffer .= $indent . '            <div class="divider border-start align-self-center mx-3"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFcb729cc74d31bce5e3746aa60b79a2e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'cookiesnotice';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'cookiesnotice';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0fe83b3d8d72be3762fd31c44308f1be(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            require([\'core_form/events\'], function(FormEvent) {
                function autoFocus() {
                    const userNameField = document.getElementById(\'username\');
                    const passwordField = document.getElementById(\'password\');
                    if (userNameField && userNameField.value.length == 0) {
                        userNameField.focus();
                    } else if (passwordField) {
                        passwordField.focus();
                    }
                }
                autoFocus();
                window.addEventListener(FormEvent.eventTypes.fieldStructureChanged, autoFocus);
            });
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            require([\'core_form/events\'], function(FormEvent) {
';
                $buffer .= $indent . '                function autoFocus() {
';
                $buffer .= $indent . '                    const userNameField = document.getElementById(\'username\');
';
                $buffer .= $indent . '                    const passwordField = document.getElementById(\'password\');
';
                $buffer .= $indent . '                    if (userNameField && userNameField.value.length == 0) {
';
                $buffer .= $indent . '                        userNameField.focus();
';
                $buffer .= $indent . '                    } else if (passwordField) {
';
                $buffer .= $indent . '                        passwordField.focus();
';
                $buffer .= $indent . '                    }
';
                $buffer .= $indent . '                }
';
                $buffer .= $indent . '                autoFocus();
';
                $buffer .= $indent . '                window.addEventListener(FormEvent.eventTypes.fieldStructureChanged, autoFocus);
';
                $buffer .= $indent . '            });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAf8ed2a653223b955509959d51115f73(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        require([\'core/togglesensitive\'], function(ToggleSensitive) {
            ToggleSensitive.init("password", {{smallscreensonly}});
        });
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        require([\'core/togglesensitive\'], function(ToggleSensitive) {
';
                $buffer .= $indent . '            ToggleSensitive.init("password", ';
                $value = $this->resolveValue($context->find('smallscreensonly'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ');
';
                $buffer .= $indent . '        });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF134befbc907907019eac63cfee377f3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            Submit.init("loginguestbtn");
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            Submit.init("loginguestbtn");
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCf32b69b4b46ec86593c52f5d8cb86e2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    (function() {
        if (window.location.hash && (window.location.hash.indexOf(\'login-method-\') !== -1)) {
            history.replaceState(null, null, window.location.pathname + window.location.search);
        }
    })();
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    (function() {
';
                $buffer .= $indent . '        if (window.location.hash && (window.location.hash.indexOf(\'login-method-\') !== -1)) {
';
                $buffer .= $indent . '            history.replaceState(null, null, window.location.pathname + window.location.search);
';
                $buffer .= $indent . '        }
';
                $buffer .= $indent . '    })();
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF109e33cbd41eb902f5e544b8a3b92b6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{^error}}
        {{#autofocusform}}
            require([\'core_form/events\'], function(FormEvent) {
                function autoFocus() {
                    const userNameField = document.getElementById(\'username\');
                    const passwordField = document.getElementById(\'password\');
                    if (userNameField && userNameField.value.length == 0) {
                        userNameField.focus();
                    } else if (passwordField) {
                        passwordField.focus();
                    }
                }
                autoFocus();
                window.addEventListener(FormEvent.eventTypes.fieldStructureChanged, autoFocus);
            });
        {{/autofocusform}}
    {{/error}}
    require([\'core/pending\'], function(Pending) {
        const errorMessageDiv = document.getElementById(\'loginerrormessage\');
        const infoMessageDiv = document.getElementById(\'logininfomessage\');
        const errorMessage = errorMessageDiv?.textContent.trim();
        const infoMessage = infoMessageDiv?.textContent.trim();
        if (errorMessage || infoMessage) {
            const pendingJS = new Pending(\'login-move-focus\');
            const usernameField = document.getElementById(\'username\');
            setTimeout(function() {
                // Focus on the username field on error.
                if (errorMessage && usernameField) {
                    usernameField.focus();
                }
                // Append a non-breaking space to the error/status message so screen readers will announce them after page load.
                if (errorMessage) {
                    errorMessageDiv.innerHTML += "&nbsp;";
                }
                if (infoMessage) {
                    infoMessageDiv.innerHTML += "&nbsp;";
                }
                pendingJS.resolve();
            }, 500);
        }
    });
    {{#togglepassword}}
        require([\'core/togglesensitive\'], function(ToggleSensitive) {
            ToggleSensitive.init("password", {{smallscreensonly}});
        });
    {{/togglepassword}}
    require([\'core_form/submit\'], function(Submit) {
        Submit.init("loginbtn");
        {{#showguestlogin}}
            Submit.init("loginguestbtn");
        {{/showguestlogin}}
    });

    {{! Tabs layout: Clear URL hash on page reload to prevent automatic tab switching by the browser based on the hash }}
    {{#loginlayouttabs}}
    (function() {
        if (window.location.hash && (window.location.hash.indexOf(\'login-method-\') !== -1)) {
            history.replaceState(null, null, window.location.pathname + window.location.search);
        }
    })();
    {{/loginlayouttabs}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('error');
                if (empty($value)) {
                    
                    $value = $context->find('autofocusform');
                    $buffer .= $this->section0fe83b3d8d72be3762fd31c44308f1be($context, $indent, $value);
                }
                $buffer .= $indent . '    require([\'core/pending\'], function(Pending) {
';
                $buffer .= $indent . '        const errorMessageDiv = document.getElementById(\'loginerrormessage\');
';
                $buffer .= $indent . '        const infoMessageDiv = document.getElementById(\'logininfomessage\');
';
                $buffer .= $indent . '        const errorMessage = errorMessageDiv?.textContent.trim();
';
                $buffer .= $indent . '        const infoMessage = infoMessageDiv?.textContent.trim();
';
                $buffer .= $indent . '        if (errorMessage || infoMessage) {
';
                $buffer .= $indent . '            const pendingJS = new Pending(\'login-move-focus\');
';
                $buffer .= $indent . '            const usernameField = document.getElementById(\'username\');
';
                $buffer .= $indent . '            setTimeout(function() {
';
                $buffer .= $indent . '                // Focus on the username field on error.
';
                $buffer .= $indent . '                if (errorMessage && usernameField) {
';
                $buffer .= $indent . '                    usernameField.focus();
';
                $buffer .= $indent . '                }
';
                $buffer .= $indent . '                // Append a non-breaking space to the error/status message so screen readers will announce them after page load.
';
                $buffer .= $indent . '                if (errorMessage) {
';
                $buffer .= $indent . '                    errorMessageDiv.innerHTML += "&nbsp;";
';
                $buffer .= $indent . '                }
';
                $buffer .= $indent . '                if (infoMessage) {
';
                $buffer .= $indent . '                    infoMessageDiv.innerHTML += "&nbsp;";
';
                $buffer .= $indent . '                }
';
                $buffer .= $indent . '                pendingJS.resolve();
';
                $buffer .= $indent . '            }, 500);
';
                $buffer .= $indent . '        }
';
                $buffer .= $indent . '    });
';
                $value = $context->find('togglepassword');
                $buffer .= $this->sectionAf8ed2a653223b955509959d51115f73($context, $indent, $value);
                $buffer .= $indent . '    require([\'core_form/submit\'], function(Submit) {
';
                $buffer .= $indent . '        Submit.init("loginbtn");
';
                $value = $context->find('showguestlogin');
                $buffer .= $this->sectionF134befbc907907019eac63cfee377f3($context, $indent, $value);
                $buffer .= $indent . '    });
';
                $buffer .= $indent . '
';
                $value = $context->find('loginlayouttabs');
                $buffer .= $this->sectionCf32b69b4b46ec86593c52f5d8cb86e2($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
