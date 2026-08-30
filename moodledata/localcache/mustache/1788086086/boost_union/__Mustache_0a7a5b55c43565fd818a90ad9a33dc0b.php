<?php

class __Mustache_0a7a5b55c43565fd818a90ad9a33dc0b extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        if ($partial = $this->mustache->loadPartial('theme_boost/head')) {
            $buffer .= $partial->renderInternal($context);
        }
        $buffer .= $indent . '
';
        $buffer .= $indent . '<body ';
        $value = $this->resolveValue($context->find('bodyattributes'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '>
';
        if ($partial = $this->mustache->loadPartial('core/local/toast/wrapper')) {
            $buffer .= $partial->renderInternal($context);
        }
        $buffer .= $indent . '<div id="page-wrapper" class="d-print-block">
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    ';
        $value = $this->resolveValue($context->findDot('output.standard_top_of_body_html'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '
';
        if ($partial = $this->mustache->loadPartial('theme_boost/navbar')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $value = $context->find('courseindex');
        $buffer .= $this->section9710329be45fafd8954b98d344b9a098($context, $indent, $value);
        $value = $context->find('hasblocks');
        $buffer .= $this->section4aaed2b6f1ec12f9858540c0b87d8693($context, $indent, $value);
        $buffer .= '
';
        $value = $context->find('userisediting');
        if (empty($value)) {
            
            $value = $context->findDot('regions.offcanvas.hasblocks');
            $buffer .= $this->sectionC4632a015fd551c3fc1287460122464d($context, $indent, $value);
        }
        $buffer .= '
';
        $buffer .= $indent . '    <div id="page" data-region="mainpage" data-usertour="scroller" class="drawers ';
        $value = $context->find('courseindexopen');
        $buffer .= $this->section6b40e2748488de16028fd4032111cc9b($context, $indent, $value);
        $buffer .= ' ';
        $value = $context->find('blockdraweropen');
        $buffer .= $this->section05ab28b93094b30436e1224de5468efa($context, $indent, $value);
        $buffer .= ' drag-container">
';
        $value = $context->find('userisediting');
        $buffer .= $this->section38f4408ebfeb30403cbef2215e1339ce($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->findDot('regions.outsidetop.hasblocks');
        $buffer .= $this->sectionBc0380c0473ea097ff51173b91635869($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->findDot('regions.header.hasblocks');
        $buffer .= $this->section94e84bbd499055e813041e711cd90777($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <div class="main-inner-wrapper ';
        $value = $this->resolveValue($context->find('maininnerwrapperclass'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= ' ';
        $value = $this->resolveValue($context->find('outsideregionsplacement'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '">
';
        $value = $context->findDot('regions.outsideleft.hasblocks');
        $buffer .= $this->section8541085ff3d6b7749d0b6408e6643d4d($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <div id="topofscroll" class="main-inner">
';
        $buffer .= $indent . '            <div class="drawer-toggles d-flex">
';
        $value = $context->find('courseindex');
        $buffer .= $this->section0eadb161ff0a57ff31836e56223bc96c($context, $indent, $value);
        $value = $context->find('hasblocks');
        $buffer .= $this->section94c73faf1c9634654fc54727e5648ac4($context, $indent, $value);
        $buffer .= $indent . '            </div>
';
        if ($partial = $this->mustache->loadPartial('theme_boost_union/javascriptdisabledhint')) {
            $buffer .= $partial->renderInternal($context, $indent . '            ');
        }
        if ($partial = $this->mustache->loadPartial('theme_boost_union/infobanners')) {
            $buffer .= $partial->renderInternal($context, $indent . '            ');
        }
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->findDot('output.full_header'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('secondarymoremenu');
        $buffer .= $this->sectionD2b4dcb0cc83675dfa05902c4094b87d($context, $indent, $value);
        $buffer .= $indent . '            ';
        $value = $this->resolveValue($context->findDot('output.course_content_header_notifications'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->findDot('regions.contentupper.hasblocks');
        $buffer .= $this->sectionA7b9d559d4dd2caaa5c8cc8d5406d2af($context, $indent, $value);
        $buffer .= $indent . '            <div id="page-content" class="pb-3 d-print-block">
';
        $buffer .= $indent . '                <div id="region-main-box">
';
        $value = $context->find('hasregionmainsettingsmenu');
        $buffer .= $this->section4833668e3ab64a52de8319d62eeb9a0d($context, $indent, $value);
        $buffer .= $indent . '                    <div id="region-main">
';
        $buffer .= $indent . '
';
        $value = $context->find('hasregionmainsettingsmenu');
        $buffer .= $this->sectionBfb58dd1d13a295db43bf0266e3aa6ee($context, $indent, $value);
        $buffer .= $indent . '                        ';
        $value = $this->resolveValue($context->findDot('output.course_content_header_coursecontent'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('headercontent');
        $buffer .= $this->section72a93137fa43b727f40759a358d07ffb($context, $indent, $value);
        $value = $context->find('overflow');
        $buffer .= $this->sectionD1a531f5a89e5a3e9617c88ef195a6bf($context, $indent, $value);
        $buffer .= $indent . '                        ';
        $value = $this->resolveValue($context->find('courserelatedhints'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('sliderpositionbeforebefore');
        $buffer .= $this->sectionD78e4e3cfb7ce6939cde8fb3bfa876e0($context, $indent, $value);
        $value = $context->find('advtilespositionbefore');
        $buffer .= $this->section857f555142a8ad370cb5c32ca6200f1f($context, $indent, $value);
        $value = $context->find('sliderpositionbeforeafter');
        $buffer .= $this->sectionD78e4e3cfb7ce6939cde8fb3bfa876e0($context, $indent, $value);
        $buffer .= $indent . '                        ';
        $value = $this->resolveValue($context->findDot('output.main_content'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '                        ';
        $value = $this->resolveValue($context->findDot('output.activity_navigation'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '                        ';
        $value = $this->resolveValue($context->findDot('output.course_content_footer'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('sliderpositionafterbefore');
        $buffer .= $this->sectionD78e4e3cfb7ce6939cde8fb3bfa876e0($context, $indent, $value);
        $value = $context->find('advtilespositionafter');
        $buffer .= $this->section857f555142a8ad370cb5c32ca6200f1f($context, $indent, $value);
        $value = $context->find('sliderpositionafterafter');
        $buffer .= $this->sectionD78e4e3cfb7ce6939cde8fb3bfa876e0($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '                    </div>
';
        $buffer .= $indent . '                </div>
';
        $buffer .= $indent . '            </div>
';
        $value = $context->findDot('regions.contentlower.hasblocks');
        $buffer .= $this->section70056d5ae470794b9a62fde175b8a4d0($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '
';
        $value = $context->findDot('regions.outsideright.hasblocks');
        $buffer .= $this->sectionBdc1a0c18ccff05f7a53f5f07fea1e1f($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '
';
        $value = $context->findDot('regions.outsidebottom.hasblocks');
        $buffer .= $this->section5b3dacced2ba25ce23189ed13dcfc1f3($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->findDot('regions.footer.hasblocks');
        $buffer .= $this->sectionDd8e16072feef934e17b1b06de277f9d($context, $indent, $value);
        $buffer .= $indent . '
';
        if ($partial = $this->mustache->loadPartial('theme_boost/footer')) {
            $buffer .= $partial->renderInternal($context, $indent . '        ');
        }
        if ($partial = $this->mustache->loadPartial('theme_boost_union/footnote')) {
            $buffer .= $partial->renderInternal($context, $indent . '        ');
        }
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    ';
        $value = $this->resolveValue($context->findDot('output.standard_after_main_region_html'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '</body>
';
        $buffer .= $indent . '</html>
';
        $value = $context->find('js');
        $buffer .= $this->section270c14816285674f24fb1f7eb538f3ac($context, $indent, $value);

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

    private function sectionD8c059d8e564034fcd5a4fcfed7ed8eb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'closecourseindex, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'closecourseindex, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9710329be45fafd8954b98d344b9a098(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{< theme_boost/drawer }}
            {{$id}}theme_boost-drawers-courseindex{{/id}}
            {{$drawerheadercontent}}
                {{> theme_boost/courseindexdrawercontrols}}
            {{/drawerheadercontent}}
            {{$drawerclasses}}drawer drawer-left {{#courseindexopen}}show{{/courseindexopen}}{{/drawerclasses}}
            {{$drawercontent}}
                {{{courseindex}}}
            {{/drawercontent}}
            {{$drawerpreferencename}}drawer-open-index{{/drawerpreferencename}}
            {{$drawerstate}}show-drawer-left{{/drawerstate}}
            {{$tooltipplacement}}right{{/tooltipplacement}}
            {{$closebuttontext}}{{#str}}closecourseindex, core{{/str}}{{/closebuttontext}}
        {{/ theme_boost/drawer}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        ';
                if ($parent = $this->mustache->loadPartial('theme_boost/drawer')) {
                    $context->pushBlockContext(array(
                        'id' => array($this, 'blockEfc32b531594d5d62ec9c68e35a53dc3'),
                        'drawerheadercontent' => array($this, 'blockA515fe1a11236ac39d9f95662b1587b1'),
                        'drawerclasses' => array($this, 'block0dcbcdb48783bd0043cc25a3ecb99947'),
                        'drawercontent' => array($this, 'blockD56da21f9a4b20987f95cec76380b378'),
                        'drawerpreferencename' => array($this, 'block24fc4cc7410bc884a3b9fba5f26dc7b9'),
                        'drawerstate' => array($this, 'blockBd5099c9b82bf8a4037bbd56bb374a89'),
                        'tooltipplacement' => array($this, 'block2a81b037a416792e91b78e41049f49f9'),
                        'closebuttontext' => array($this, 'block9ac4e5859f44816b862545bc2c6e6a46'),
                    ));
                    $buffer .= $parent->renderInternal($context, $indent);
                    $context->popBlockContext();
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1cb47d3ed9b97c6d6d496d2358bec253(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' show';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' show';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1bd0cc4642e36d67e46c9dd550f1fa06(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '1';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '1';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section31618380a8d2d407a8b2acf35dd449a4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'closeblockdrawer, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'closeblockdrawer, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4aaed2b6f1ec12f9858540c0b87d8693(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{< theme_boost/drawer }}
            {{$id}}theme_boost-drawers-blocks{{/id}}
            {{$drawerclasses}}drawer drawer-right{{#blockdraweropen}} show{{/blockdraweropen}}{{/drawerclasses}}
            {{$drawercontent}}
                <div class="d-print-none">
                    {{{ addblockbutton }}}
                    {{{ sidepreblocks }}}
                </div>
            {{/drawercontent}}
            {{$drawerpreferencename}}drawer-open-block{{/drawerpreferencename}}
            {{$forceopen}}{{#forceblockdraweropen}}1{{/forceblockdraweropen}}{{/forceopen}}
            {{$drawerstate}}show-drawer-right{{/drawerstate}}
            {{$tooltipplacement}}left{{/tooltipplacement}}
            {{$drawercloseonresize}}1{{/drawercloseonresize}}
            {{$closebuttontext}}{{#str}}closeblockdrawer, core{{/str}}{{/closebuttontext}}
        {{/ theme_boost/drawer}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '        ';
                if ($parent = $this->mustache->loadPartial('theme_boost/drawer')) {
                    $context->pushBlockContext(array(
                        'id' => array($this, 'block4ca482bf2f4c0292197ede951a52b02e'),
                        'drawerclasses' => array($this, 'block874d06fc7c30c807dc0ee7226cad4ec8'),
                        'drawercontent' => array($this, 'block653705e011aee4da136ef3634b4ebefd'),
                        'drawerpreferencename' => array($this, 'block59146569a56c3d2642fa2e8224817be0'),
                        'forceopen' => array($this, 'block13847ba3219919ecdb2378620735177c'),
                        'drawerstate' => array($this, 'block0ea572ae0e89f9c5cc1dc5d7238a50d5'),
                        'tooltipplacement' => array($this, 'blockC945de95615645c65d6b2f192042e6ea'),
                        'drawercloseonresize' => array($this, 'blockE052079a625ca42b568ba24af19cc7eb'),
                        'closebuttontext' => array($this, 'blockC879444321d250421c3438099ae68173'),
                    ));
                    $buffer .= $parent->renderInternal($context, $indent);
                    $context->popBlockContext();
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section223b43cc9bde494013b19ef00adc7847(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'closeoffcanvas, theme_boost_union';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'closeoffcanvas, theme_boost_union';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC4632a015fd551c3fc1287460122464d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{< theme_boost/drawer }}
                {{$id}}theme_boost_union-drawers-offcanvas{{/id}}
                {{$drawerclasses}}drawer drawer-top{{/drawerclasses}}
                {{$drawercontent}}
                    {{> theme_boost_union/offcanvas}}
                {{/drawercontent}}
                {{$forceopen}}0{{/forceopen}}
                {{$drawerstate}}show-drawer-offcanvas{{/drawerstate}}
                {{$tooltipplacement}}top{{/tooltipplacement}}
                {{$drawercloseonresize}}1{{/drawercloseonresize}}
                {{$closebuttontext}}{{#str}}closeoffcanvas, theme_boost_union{{/str}}{{/closebuttontext}}
            {{/ theme_boost/drawer}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            ';
                if ($parent = $this->mustache->loadPartial('theme_boost/drawer')) {
                    $context->pushBlockContext(array(
                        'id' => array($this, 'blockF1a532bf39161d3bb9c2504cfb39dc93'),
                        'drawerclasses' => array($this, 'block3b42c5981e372fb5c6e715dba97a6870'),
                        'drawercontent' => array($this, 'block3b867521a7d9bd73c538404533b9e0dc'),
                        'forceopen' => array($this, 'blockE13f09ec51e6aa10c3789885dab1c67f'),
                        'drawerstate' => array($this, 'block2b233ccb4e40943bbdecd7d840edc96b'),
                        'tooltipplacement' => array($this, 'block1329ab84cc06a8d2ff96b01517f75e2c'),
                        'drawercloseonresize' => array($this, 'blockE052079a625ca42b568ba24af19cc7eb'),
                        'closebuttontext' => array($this, 'block4f93760f064190181b36ec394d25b135'),
                    ));
                    $buffer .= $parent->renderInternal($context, $indent);
                    $context->popBlockContext();
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6b40e2748488de16028fd4032111cc9b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'show-drawer-left';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'show-drawer-left';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section05ab28b93094b30436e1224de5468efa(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'show-drawer-right';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'show-drawer-right';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section38f4408ebfeb30403cbef2215e1339ce(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div id="theme-block-region-offcanvas-editing" class="collapse">
                <div id="theme-block-region-offcanvas-editing-inner">
                    {{> theme_boost_union/offcanvas}}
                </div>
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div id="theme-block-region-offcanvas-editing" class="collapse">
';
                $buffer .= $indent . '                <div id="theme-block-region-offcanvas-editing-inner">
';
                if ($partial = $this->mustache->loadPartial('theme_boost_union/offcanvas')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC14df02445cdd505a0208e8a56a5f32e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'blocks';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'blocks';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBc0380c0473ea097ff51173b91635869(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <section id="theme-block-region-outside-top" class="theme-block-region {{{outsidetopwidth}}}" aria-label="{{#str}}blocks{{/str}}">
                {{{ regions.outsidetop.addblockbutton }}}
                {{{ regions.outsidetop.regionhtml }}}
            </section>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <section id="theme-block-region-outside-top" class="theme-block-region ';
                $value = $this->resolveValue($context->find('outsidetopwidth'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsidetop.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsidetop.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section94e84bbd499055e813041e711cd90777(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <section id="theme-block-region-header" class="theme-block-region" aria-label="{{#str}}blocks{{/str}}">
                {{{ regions.header.addblockbutton }}}
                {{{ regions.header.regionhtml }}}
            </section>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <section id="theme-block-region-header" class="theme-block-region" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.header.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.header.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8541085ff3d6b7749d0b6408e6643d4d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <section id="theme-block-region-outside-left" class="theme-block-region" aria-label="{{#str}}blocks{{/str}}">
                    {{{ regions.outsideleft.addblockbutton }}}
                    {{{ regions.outsideleft.regionhtml }}}
                 </section>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <section id="theme-block-region-outside-left" class="theme-block-region" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.outsideleft.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.outsideleft.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                 </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAfaaa3dab86fd46a075d917e3ce891f0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'opendrawerindex, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'opendrawerindex, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3feaf801d4aa0a811fe41c3427e53335(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' t/index_drawer, moodle ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' t/index_drawer, moodle ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0eadb161ff0a57ff31836e56223bc96c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="drawer-toggler drawer-left-toggle open-nav d-print-none">
                        <button
                            class="btn icon-no-margin"
                            data-toggler="drawers"
                            data-action="toggle"
                            data-target="theme_boost-drawers-courseindex"
                            data-toggle="tooltip"
                            data-placement="right"
                            title="{{#str}}opendrawerindex, core{{/str}}"
                        >
                            <span class="sr-only">{{#str}}opendrawerindex, core{{/str}}</span>
                            {{#pix}} t/index_drawer, moodle {{/pix}}
                        </button>
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="drawer-toggler drawer-left-toggle open-nav d-print-none">
';
                $buffer .= $indent . '                        <button
';
                $buffer .= $indent . '                            class="btn icon-no-margin"
';
                $buffer .= $indent . '                            data-toggler="drawers"
';
                $buffer .= $indent . '                            data-action="toggle"
';
                $buffer .= $indent . '                            data-target="theme_boost-drawers-courseindex"
';
                $buffer .= $indent . '                            data-toggle="tooltip"
';
                $buffer .= $indent . '                            data-placement="right"
';
                $buffer .= $indent . '                            title="';
                $value = $context->find('str');
                $buffer .= $this->sectionAfaaa3dab86fd46a075d917e3ce891f0($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                        >
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionAfaaa3dab86fd46a075d917e3ce891f0($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                            ';
                $value = $context->find('pix');
                $buffer .= $this->section3feaf801d4aa0a811fe41c3427e53335($context, $indent, $value);
                $buffer .= '
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

    private function section245ba37b5a58f162a63e0d10700eb739(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'opendrawerblocks, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'opendrawerblocks, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0bf870995ed49a0bb4a766ed18e0f142(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/blocks_drawer, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/blocks_drawer, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD277bad9387573c58f79f77fc705e8dd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/blocks_drawer_rtl, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/blocks_drawer_rtl, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section94c73faf1c9634654fc54727e5648ac4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="drawer-toggler drawer-right-toggle ms-auto d-print-none">
                        <button
                            class="btn icon-no-margin"
                            data-toggler="drawers"
                            data-action="toggle"
                            data-target="theme_boost-drawers-blocks"
                            data-toggle="tooltip"
                            data-placement="right"
                            title="{{#str}}opendrawerblocks, core{{/str}}"
                        >
                            <span class="sr-only">{{#str}}opendrawerblocks, core{{/str}}</span>
                            <span class="dir-rtl-hide">{{#pix}}t/blocks_drawer, core{{/pix}}</span>
                            <span class="dir-ltr-hide">{{#pix}}t/blocks_drawer_rtl, core{{/pix}}</span>
                        </button>
                    </div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="drawer-toggler drawer-right-toggle ms-auto d-print-none">
';
                $buffer .= $indent . '                        <button
';
                $buffer .= $indent . '                            class="btn icon-no-margin"
';
                $buffer .= $indent . '                            data-toggler="drawers"
';
                $buffer .= $indent . '                            data-action="toggle"
';
                $buffer .= $indent . '                            data-target="theme_boost-drawers-blocks"
';
                $buffer .= $indent . '                            data-toggle="tooltip"
';
                $buffer .= $indent . '                            data-placement="right"
';
                $buffer .= $indent . '                            title="';
                $value = $context->find('str');
                $buffer .= $this->section245ba37b5a58f162a63e0d10700eb739($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                        >
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->section245ba37b5a58f162a63e0d10700eb739($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                            <span class="dir-rtl-hide">';
                $value = $context->find('pix');
                $buffer .= $this->section0bf870995ed49a0bb4a766ed18e0f142($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                            <span class="dir-ltr-hide">';
                $value = $context->find('pix');
                $buffer .= $this->sectionD277bad9387573c58f79f77fc705e8dd($context, $indent, $value);
                $buffer .= '</span>
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

    private function sectionD2b4dcb0cc83675dfa05902c4094b87d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <div class="secondary-navigation d-print-none">
                    {{> core/moremenu}}
                </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <div class="secondary-navigation d-print-none">
';
                if ($partial = $this->mustache->loadPartial('core/moremenu')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $buffer .= $indent . '                </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA7b9d559d4dd2caaa5c8cc8d5406d2af(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <section id="theme-block-region-content-upper" class="theme-block-region" aria-label="{{#str}}blocks{{/str}}">
                    {{{ regions.contentupper.addblockbutton }}}
                    {{{ regions.contentupper.regionhtml }}}
                </section>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <section id="theme-block-region-content-upper" class="theme-block-region" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.contentupper.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.contentupper.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4833668e3ab64a52de8319d62eeb9a0d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div id="region-main-settings-menu" class="d-print-none">
                        <div> {{{ regionmainsettingsmenu }}} </div>
                    </div>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div id="region-main-settings-menu" class="d-print-none">
';
                $buffer .= $indent . '                        <div> ';
                $value = $this->resolveValue($context->find('regionmainsettingsmenu'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= ' </div>
';
                $buffer .= $indent . '                    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBfb58dd1d13a295db43bf0266e3aa6ee(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div class="region_main_settings_menu_proxy"></div>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <div class="region_main_settings_menu_proxy"></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section72a93137fa43b727f40759a358d07ffb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            {{> core/activity_header }}
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/activity_header')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                            ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD1a531f5a89e5a3e9617c88ef195a6bf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <div class="container-fluid tertiary-navigation">
                                <div class="navitem">
                                    {{> core/url_select}}
                                </div>
                            </div>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <div class="container-fluid tertiary-navigation">
';
                $buffer .= $indent . '                                <div class="navitem">
';
                if ($partial = $this->mustache->loadPartial('core/url_select')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                    ');
                }
                $buffer .= $indent . '                                </div>
';
                $buffer .= $indent . '                            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD78e4e3cfb7ce6939cde8fb3bfa876e0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            {{> theme_boost_union/slider }}
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/slider')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                            ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section857f555142a8ad370cb5c32ca6200f1f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            {{> theme_boost_union/advertisementtiles }}
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('theme_boost_union/advertisementtiles')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                            ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section70056d5ae470794b9a62fde175b8a4d0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <section id="theme-block-region-content-lower" class="theme-block-region" aria-label="{{#str}}blocks{{/str}}">
                    {{{ regions.contentlower.addblockbutton }}}
                    {{{ regions.contentlower.regionhtml }}}
                </section>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <section id="theme-block-region-content-lower" class="theme-block-region" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.contentlower.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                    ';
                $value = $this->resolveValue($context->findDot('regions.contentlower.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBdc1a0c18ccff05f7a53f5f07fea1e1f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <section id="theme-block-region-outside-right" class="theme-block-region" aria-label="{{#str}}blocks{{/str}}">
                {{{ regions.outsideright.addblockbutton }}}
                {{{ regions.outsideright.regionhtml }}}
            </section>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <section id="theme-block-region-outside-right" class="theme-block-region" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsideright.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsideright.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5b3dacced2ba25ce23189ed13dcfc1f3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <section id="theme-block-region-outside-bottom" class="theme-block-region {{{outsidebottomwidth}}}" aria-label="{{#str}}blocks{{/str}}">
                {{{ regions.outsidebottom.addblockbutton }}}
                {{{ regions.outsidebottom.regionhtml }}}
            </section>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <section id="theme-block-region-outside-bottom" class="theme-block-region ';
                $value = $this->resolveValue($context->find('outsidebottomwidth'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsidebottom.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->findDot('regions.outsidebottom.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '            </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section786631a1a3895f17d69e68c3c0f9c237(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <section id="theme-block-region-footer-left" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footerleft.addblockbutton }}}
                            {{{ regions.footerleft.regionhtml }}}
                        </section>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <section id="theme-block-region-footer-left" class="theme-block-region ';
                $value = $this->resolveValue($context->findDot('regions.footer.class'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footerleft.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footerleft.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC56f4c511bf6b020d1dbba4c1ef60650(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <section id="theme-block-region-footer-center" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footercenter.addblockbutton }}}
                            {{{ regions.footercenter.regionhtml }}}
                        </section>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <section id="theme-block-region-footer-center" class="theme-block-region ';
                $value = $this->resolveValue($context->findDot('regions.footer.class'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footercenter.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footercenter.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA7dc559a0948bf96b8aff9a1c4fb1c39(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <section id="theme-block-region-footer-right" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footerright.addblockbutton }}}
                            {{{ regions.footerright.regionhtml }}}
                        </section>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <section id="theme-block-region-footer-right" class="theme-block-region ';
                $value = $this->resolveValue($context->findDot('regions.footer.class'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionC14df02445cdd505a0208e8a56a5f32e($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footerright.addblockbutton'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                            ';
                $value = $this->resolveValue($context->findDot('regions.footerright.regionhtml'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </section>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDd8e16072feef934e17b1b06de277f9d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div id="theme-block-region-footer" class="{{{footerwidth}}}">
                <div class="row">
                     {{#regions.footerleft.hasblocks}}
                        <section id="theme-block-region-footer-left" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footerleft.addblockbutton }}}
                            {{{ regions.footerleft.regionhtml }}}
                        </section>
                    {{/regions.footerleft.hasblocks}}
                    {{#regions.footercenter.hasblocks}}
                        <section id="theme-block-region-footer-center" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footercenter.addblockbutton }}}
                            {{{ regions.footercenter.regionhtml }}}
                        </section>
                    {{/regions.footercenter.hasblocks}}
                    {{#regions.footerright.hasblocks}}
                        <section id="theme-block-region-footer-right" class="theme-block-region {{regions.footer.class}}" aria-label="{{#str}}blocks{{/str}}">
                            {{{ regions.footerright.addblockbutton }}}
                            {{{ regions.footerright.regionhtml }}}
                        </section>
                    {{/regions.footerright.hasblocks}}
                </div>
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div id="theme-block-region-footer" class="';
                $value = $this->resolveValue($context->find('footerwidth'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '">
';
                $buffer .= $indent . '                <div class="row">
';
                $value = $context->findDot('regions.footerleft.hasblocks');
                $buffer .= $this->section786631a1a3895f17d69e68c3c0f9c237($context, $indent, $value);
                $value = $context->findDot('regions.footercenter.hasblocks');
                $buffer .= $this->sectionC56f4c511bf6b020d1dbba4c1ef60650($context, $indent, $value);
                $value = $context->findDot('regions.footerright.hasblocks');
                $buffer .= $this->sectionA7dc559a0948bf96b8aff9a1c4fb1c39($context, $indent, $value);
                $buffer .= $indent . '                </div>
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8a6bed6331d56ed2792e6d846377970d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ',\'theme_boost_union/offcanvas\'';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ',\'theme_boost_union/offcanvas\'';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9894a46a7c69579f6beb2569df38276a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ', \'theme_boost_union/smartmenu\'';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ', \'theme_boost_union/smartmenu\'';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section754ff135f56ff61db05ea660c2adeac1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ',OffCanvas';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ',OffCanvas';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section282b068ffd15884d847c5999e1bfe11d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ', SmartMenu';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ', SmartMenu';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section343108e4671aea00311d71b0b076974b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'OffCanvas.init();';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'OffCanvas.init();';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDb7b367a62f032e41ce052d7bae5cdfb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'SmartMenu.init();';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'SmartMenu.init();';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section270c14816285674f24fb1f7eb538f3ac(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
M.util.js_pending(\'theme_boost/loader\');
require([\'theme_boost/loader\', \'theme_boost/drawer\'{{#regions.offcanvas.hasblocks}},\'theme_boost_union/offcanvas\'{{/regions.offcanvas.hasblocks}}{{#includesmartmenu}}, \'theme_boost_union/smartmenu\'{{/includesmartmenu}}], function(Loader, Drawer{{#regions.offcanvas.hasblocks}},OffCanvas{{/regions.offcanvas.hasblocks}}{{#includesmartmenu}}, SmartMenu{{/includesmartmenu}}) {
    Drawer.init();
    M.util.js_complete(\'theme_boost/loader\');
{{#regions.offcanvas.hasblocks}}OffCanvas.init();{{/regions.offcanvas.hasblocks}}
{{#includesmartmenu}}SmartMenu.init();{{/includesmartmenu}}
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'M.util.js_pending(\'theme_boost/loader\');
';
                $buffer .= $indent . 'require([\'theme_boost/loader\', \'theme_boost/drawer\'';
                $value = $context->findDot('regions.offcanvas.hasblocks');
                $buffer .= $this->section8a6bed6331d56ed2792e6d846377970d($context, $indent, $value);
                $value = $context->find('includesmartmenu');
                $buffer .= $this->section9894a46a7c69579f6beb2569df38276a($context, $indent, $value);
                $buffer .= '], function(Loader, Drawer';
                $value = $context->findDot('regions.offcanvas.hasblocks');
                $buffer .= $this->section754ff135f56ff61db05ea660c2adeac1($context, $indent, $value);
                $value = $context->find('includesmartmenu');
                $buffer .= $this->section282b068ffd15884d847c5999e1bfe11d($context, $indent, $value);
                $buffer .= ') {
';
                $buffer .= $indent . '    Drawer.init();
';
                $buffer .= $indent . '    M.util.js_complete(\'theme_boost/loader\');
';
                $value = $context->findDot('regions.offcanvas.hasblocks');
                $buffer .= $this->section343108e4671aea00311d71b0b076974b($context, $indent, $value);
                $buffer .= '
';
                $value = $context->find('includesmartmenu');
                $buffer .= $this->sectionDb7b367a62f032e41ce052d7bae5cdfb($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function blockEfc32b531594d5d62ec9c68e35a53dc3($context)
    {
        $indent = $buffer = '';
        $buffer .= 'theme_boost-drawers-courseindex';
    
        return $buffer;
    }

    public function blockA515fe1a11236ac39d9f95662b1587b1($context)
    {
        $indent = $buffer = '';
        if ($partial = $this->mustache->loadPartial('theme_boost/courseindexdrawercontrols')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
    
        return $buffer;
    }

    public function block0dcbcdb48783bd0043cc25a3ecb99947($context)
    {
        $indent = $buffer = '';
        $buffer .= 'drawer drawer-left ';
        $value = $context->find('courseindexopen');
        $buffer .= $this->section14c724f5a6859d4cc56d9befdffaeac5($context, $indent, $value);
    
        return $buffer;
    }

    public function blockD56da21f9a4b20987f95cec76380b378($context)
    {
        $indent = $buffer = '';
        $buffer .= '                ';
        $value = $this->resolveValue($context->find('courseindex'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
    
        return $buffer;
    }

    public function block24fc4cc7410bc884a3b9fba5f26dc7b9($context)
    {
        $indent = $buffer = '';
        $buffer .= $indent . 'drawer-open-index';
    
        return $buffer;
    }

    public function blockBd5099c9b82bf8a4037bbd56bb374a89($context)
    {
        $indent = $buffer = '';
        $buffer .= 'show-drawer-left';
    
        return $buffer;
    }

    public function block2a81b037a416792e91b78e41049f49f9($context)
    {
        $indent = $buffer = '';
        $buffer .= 'right';
    
        return $buffer;
    }

    public function block9ac4e5859f44816b862545bc2c6e6a46($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->sectionD8c059d8e564034fcd5a4fcfed7ed8eb($context, $indent, $value);
    
        return $buffer;
    }

    public function block4ca482bf2f4c0292197ede951a52b02e($context)
    {
        $indent = $buffer = '';
        $buffer .= 'theme_boost-drawers-blocks';
    
        return $buffer;
    }

    public function block874d06fc7c30c807dc0ee7226cad4ec8($context)
    {
        $indent = $buffer = '';
        $buffer .= 'drawer drawer-right';
        $value = $context->find('blockdraweropen');
        $buffer .= $this->section1cb47d3ed9b97c6d6d496d2358bec253($context, $indent, $value);
    
        return $buffer;
    }

    public function block653705e011aee4da136ef3634b4ebefd($context)
    {
        $indent = $buffer = '';
        $buffer .= '                <div class="d-print-none">
';
        $buffer .= $indent . '                    ';
        $value = $this->resolveValue($context->find('addblockbutton'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '                    ';
        $value = $this->resolveValue($context->find('sidepreblocks'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $buffer .= $indent . '                </div>
';
    
        return $buffer;
    }

    public function block59146569a56c3d2642fa2e8224817be0($context)
    {
        $indent = $buffer = '';
        $buffer .= $indent . 'drawer-open-block';
    
        return $buffer;
    }

    public function block13847ba3219919ecdb2378620735177c($context)
    {
        $indent = $buffer = '';
        $value = $context->find('forceblockdraweropen');
        $buffer .= $this->section1bd0cc4642e36d67e46c9dd550f1fa06($context, $indent, $value);
    
        return $buffer;
    }

    public function block0ea572ae0e89f9c5cc1dc5d7238a50d5($context)
    {
        $indent = $buffer = '';
        $buffer .= 'show-drawer-right';
    
        return $buffer;
    }

    public function blockC945de95615645c65d6b2f192042e6ea($context)
    {
        $indent = $buffer = '';
        $buffer .= 'left';
    
        return $buffer;
    }

    public function blockE052079a625ca42b568ba24af19cc7eb($context)
    {
        $indent = $buffer = '';
        $buffer .= '1';
    
        return $buffer;
    }

    public function blockC879444321d250421c3438099ae68173($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->section31618380a8d2d407a8b2acf35dd449a4($context, $indent, $value);
    
        return $buffer;
    }

    public function blockF1a532bf39161d3bb9c2504cfb39dc93($context)
    {
        $indent = $buffer = '';
        $buffer .= 'theme_boost_union-drawers-offcanvas';
    
        return $buffer;
    }

    public function block3b42c5981e372fb5c6e715dba97a6870($context)
    {
        $indent = $buffer = '';
        $buffer .= 'drawer drawer-top';
    
        return $buffer;
    }

    public function block3b867521a7d9bd73c538404533b9e0dc($context)
    {
        $indent = $buffer = '';
        if ($partial = $this->mustache->loadPartial('theme_boost_union/offcanvas')) {
            $buffer .= $partial->renderInternal($context, $indent . '                    ');
        }
    
        return $buffer;
    }

    public function blockE13f09ec51e6aa10c3789885dab1c67f($context)
    {
        $indent = $buffer = '';
        $buffer .= '0';
    
        return $buffer;
    }

    public function block2b233ccb4e40943bbdecd7d840edc96b($context)
    {
        $indent = $buffer = '';
        $buffer .= 'show-drawer-offcanvas';
    
        return $buffer;
    }

    public function block1329ab84cc06a8d2ff96b01517f75e2c($context)
    {
        $indent = $buffer = '';
        $buffer .= 'top';
    
        return $buffer;
    }

    public function block4f93760f064190181b36ec394d25b135($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->section223b43cc9bde494013b19ef00adc7847($context, $indent, $value);
    
        return $buffer;
    }
}
