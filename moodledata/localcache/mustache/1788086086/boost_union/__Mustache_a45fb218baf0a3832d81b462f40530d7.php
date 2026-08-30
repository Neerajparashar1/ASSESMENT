<?php

class __Mustache_a45fb218baf0a3832d81b462f40530d7 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $value = $context->find('showfootnote');
        $buffer .= $this->section19172460a7cb75854b5876ad9e0d42ad($context, $indent, $value);

        return $buffer;
    }

    private function sectionEb70bb93f9d385174514011e7c780c87(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' mt-1';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' mt-1';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section607901142d64265e3e3e60d5f6619f73(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_aboutuslink">
                        <a href="{{ aboutuslink }}">{{ aboutuspagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_aboutuslink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('aboutuslink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('aboutuspagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4a6ef55c0b09650caee9dfb1f968a645(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_offerslink">
                        <a href="{{ offerslink }}">{{ offerspagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_offerslink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('offerslink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('offerspagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE1ea55564e6229b1795b3357b2ea6d96(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_imprintlink">
                        <a href="{{ imprintlink }}">{{ imprintpagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_imprintlink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('imprintlink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('imprintpagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section23cba6c819be955e4d5b0f8d6aceff96(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_contactlink">
                        <a href="{{ contactlink }}">{{ contactpagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_contactlink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('contactlink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('contactpagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section36841540ea04596a908863edf51b68a1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_helplink">
                        <a href="{{ helplink }}">{{ helppagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_helplink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('helplink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('helppagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section515584f8e90345747d9ac41497368097(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_maintenancelink">
                        <a href="{{ maintenancelink }}">{{ maintenancepagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_maintenancelink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('maintenancelink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('maintenancepagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7813a56faeade76320a54967de575024(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitydeclarationlink">
                        <a href="{{ accessibilitydeclarationlink }}">{{ accessibilitydeclarationpagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitydeclarationlink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('accessibilitydeclarationlink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('accessibilitydeclarationpagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section62c214026c49a42b661e28d1cc9d457c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitysupportlink">
                        <a href="{{ accessibilitysupportlink }}">{{ accessibilitysupportpagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitysupportlink">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('accessibilitysupportlink'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('accessibilitysupportpagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6572384cd5fe90fae6af248a993da39c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page1link">
                        <a href="{{ page1link }}">{{ page1pagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page1link">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('page1link'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('page1pagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF5a326dff0ecf1a7ae79b43be9399310(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page2link">
                        <a href="{{ page2link }}">{{ page2pagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page2link">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('page2link'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('page2pagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5ba422f6a2b4e591cfff256de2f8c9c4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page3link">
                        <a href="{{ page3link }}">{{ page3pagetitle }}</a>
                    </span>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page3link">
';
                $buffer .= $indent . '                        <a href="';
                $value = $this->resolveValue($context->find('page3link'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('page3pagetitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '                    </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section054b7b0d1a41ba122c0d638885e614af(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{! This container must be a flex container. Otherwise, the whitespace between the link spans in this template
                would collapse into an additional space character next to the divider and the divider would not be
                surrounded by equal spacing anymore. }}
            <div class="container-fluid px-0 d-flex flex-wrap{{# footnotesetting }} mt-1{{/ footnotesetting }}">
                {{# aboutuslinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_aboutuslink">
                        <a href="{{ aboutuslink }}">{{ aboutuspagetitle }}</a>
                    </span>
                {{/ aboutuslinkpositionfootnote }}
                {{# offerslinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_offerslink">
                        <a href="{{ offerslink }}">{{ offerspagetitle }}</a>
                    </span>
                {{/ offerslinkpositionfootnote }}
                {{# imprintlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_imprintlink">
                        <a href="{{ imprintlink }}">{{ imprintpagetitle }}</a>
                    </span>
                {{/ imprintlinkpositionfootnote }}
                {{# contactlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_contactlink">
                        <a href="{{ contactlink }}">{{ contactpagetitle }}</a>
                    </span>
                {{/ contactlinkpositionfootnote }}
                {{# helplinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_helplink">
                        <a href="{{ helplink }}">{{ helppagetitle }}</a>
                    </span>
                {{/ helplinkpositionfootnote }}
                {{# maintenancelinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_maintenancelink">
                        <a href="{{ maintenancelink }}">{{ maintenancepagetitle }}</a>
                    </span>
                {{/ maintenancelinkpositionfootnote }}
                {{# accessibilitydeclarationlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitydeclarationlink">
                        <a href="{{ accessibilitydeclarationlink }}">{{ accessibilitydeclarationpagetitle }}</a>
                    </span>
                {{/ accessibilitydeclarationlinkpositionfootnote }}
                {{# accessibilitysupportlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitysupportlink">
                        <a href="{{ accessibilitysupportlink }}">{{ accessibilitysupportpagetitle }}</a>
                    </span>
                {{/ accessibilitysupportlinkpositionfootnote }}
                {{# page1linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page1link">
                        <a href="{{ page1link }}">{{ page1pagetitle }}</a>
                    </span>
                {{/ page1linkpositionfootnote }}
                {{# page2linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page2link">
                        <a href="{{ page2link }}">{{ page2pagetitle }}</a>
                    </span>
                {{/ page2linkpositionfootnote }}
                {{# page3linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page3link">
                        <a href="{{ page3link }}">{{ page3pagetitle }}</a>
                    </span>
                {{/ page3linkpositionfootnote }}
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="container-fluid px-0 d-flex flex-wrap';
                $value = $context->find('footnotesetting');
                $buffer .= $this->sectionEb70bb93f9d385174514011e7c780c87($context, $indent, $value);
                $buffer .= '">
';
                $value = $context->find('aboutuslinkpositionfootnote');
                $buffer .= $this->section607901142d64265e3e3e60d5f6619f73($context, $indent, $value);
                $value = $context->find('offerslinkpositionfootnote');
                $buffer .= $this->section4a6ef55c0b09650caee9dfb1f968a645($context, $indent, $value);
                $value = $context->find('imprintlinkpositionfootnote');
                $buffer .= $this->sectionE1ea55564e6229b1795b3357b2ea6d96($context, $indent, $value);
                $value = $context->find('contactlinkpositionfootnote');
                $buffer .= $this->section23cba6c819be955e4d5b0f8d6aceff96($context, $indent, $value);
                $value = $context->find('helplinkpositionfootnote');
                $buffer .= $this->section36841540ea04596a908863edf51b68a1($context, $indent, $value);
                $value = $context->find('maintenancelinkpositionfootnote');
                $buffer .= $this->section515584f8e90345747d9ac41497368097($context, $indent, $value);
                $value = $context->find('accessibilitydeclarationlinkpositionfootnote');
                $buffer .= $this->section7813a56faeade76320a54967de575024($context, $indent, $value);
                $value = $context->find('accessibilitysupportlinkpositionfootnote');
                $buffer .= $this->section62c214026c49a42b661e28d1cc9d457c($context, $indent, $value);
                $value = $context->find('page1linkpositionfootnote');
                $buffer .= $this->section6572384cd5fe90fae6af248a993da39c($context, $indent, $value);
                $value = $context->find('page2linkpositionfootnote');
                $buffer .= $this->sectionF5a326dff0ecf1a7ae79b43be9399310($context, $indent, $value);
                $value = $context->find('page3linkpositionfootnote');
                $buffer .= $this->section5ba422f6a2b4e591cfff256de2f8c9c4($context, $indent, $value);
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section19172460a7cb75854b5876ad9e0d42ad(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div id="footnote" class="py-3">
        <div class="container-fluid px-0">
            {{{ footnotesetting }}}
        </div>
        {{#anystaticpagelinkedfromfootnote}}
            {{! This container must be a flex container. Otherwise, the whitespace between the link spans in this template
                would collapse into an additional space character next to the divider and the divider would not be
                surrounded by equal spacing anymore. }}
            <div class="container-fluid px-0 d-flex flex-wrap{{# footnotesetting }} mt-1{{/ footnotesetting }}">
                {{# aboutuslinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_aboutuslink">
                        <a href="{{ aboutuslink }}">{{ aboutuspagetitle }}</a>
                    </span>
                {{/ aboutuslinkpositionfootnote }}
                {{# offerslinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_offerslink">
                        <a href="{{ offerslink }}">{{ offerspagetitle }}</a>
                    </span>
                {{/ offerslinkpositionfootnote }}
                {{# imprintlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_imprintlink">
                        <a href="{{ imprintlink }}">{{ imprintpagetitle }}</a>
                    </span>
                {{/ imprintlinkpositionfootnote }}
                {{# contactlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_contactlink">
                        <a href="{{ contactlink }}">{{ contactpagetitle }}</a>
                    </span>
                {{/ contactlinkpositionfootnote }}
                {{# helplinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_helplink">
                        <a href="{{ helplink }}">{{ helppagetitle }}</a>
                    </span>
                {{/ helplinkpositionfootnote }}
                {{# maintenancelinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_maintenancelink">
                        <a href="{{ maintenancelink }}">{{ maintenancepagetitle }}</a>
                    </span>
                {{/ maintenancelinkpositionfootnote }}
                {{# accessibilitydeclarationlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitydeclarationlink">
                        <a href="{{ accessibilitydeclarationlink }}">{{ accessibilitydeclarationpagetitle }}</a>
                    </span>
                {{/ accessibilitydeclarationlinkpositionfootnote }}
                {{# accessibilitysupportlinkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_accessibilitysupportlink">
                        <a href="{{ accessibilitysupportlink }}">{{ accessibilitysupportpagetitle }}</a>
                    </span>
                {{/ accessibilitysupportlinkpositionfootnote }}
                {{# page1linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page1link">
                        <a href="{{ page1link }}">{{ page1pagetitle }}</a>
                    </span>
                {{/ page1linkpositionfootnote }}
                {{# page2linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page2link">
                        <a href="{{ page2link }}">{{ page2pagetitle }}</a>
                    </span>
                {{/ page2linkpositionfootnote }}
                {{# page3linkpositionfootnote }}
                    <span class="theme_boost_union_footnote_link theme_boost_union_footnote_page3link">
                        <a href="{{ page3link }}">{{ page3pagetitle }}</a>
                    </span>
                {{/ page3linkpositionfootnote }}
            </div>
        {{/anystaticpagelinkedfromfootnote}}
    </div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div id="footnote" class="py-3">
';
                $buffer .= $indent . '        <div class="container-fluid px-0">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('footnotesetting'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $value = $context->find('anystaticpagelinkedfromfootnote');
                $buffer .= $this->section054b7b0d1a41ba122c0d638885e614af($context, $indent, $value);
                $buffer .= $indent . '    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
