<?php
namespace Astrology\Extension;

class DOM
{
    public $str = null;
    public $doc = null;
    public $html = null;
    
    public function __construct($str = null, $charset = null, $entity = null)
    {
        if ($str) {
            $this->init($str, $charset, $entity);
        }
    }
    
    public function init($str, $charset = null, $entity = null)
    {
        $this->str = $str;
        if ($charset) {
            $str = '<!doctype html>
<html>
<head><meta charset="' . $charset . '"></head>
<body>' . $str . '<body>
</html>';
            $this->html = $str;
        }

        $doc = new \DOMDocument();
        if ($entity) {
            $str =  mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8');
        }
        @$doc->loadHTML($str);
        return $this->doc = $doc;
    }
    
    /**
     * 获取内部html
     * @param  object $node dom节点
     * @return string       html
     */
    public function innerHTML($node)
    { 
        $html = ''; 
        if (!empty($node->childNodes)) {
            $children = $node->childNodes;
            foreach ($children as $child) { 
                $html .= $child->ownerDocument->saveXML($child); 
            }
        }
        return $html; 
    }
    
    /**
     * 去掉标签及其内容
     * @param  string $str           html源
     * @param  array  $allowed_tags  允许的标签
     * @param  array  $allowed_attrs 允许的属性
     * @return string                处理后的html
     */
    public function stripTagsContent($str, $allowed_tags = [], $allowed_attrs = [])
    {
        # $doc = new \DOMDocument();
        # @$doc->loadHTML($str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $doc = $this->init($str, 'utf-8');
        $body = $doc->getElementsByTagName('body')->item(0);
        $tags = $body->getElementsByTagName('*');
        foreach ($tags as $tag) {
            # print_r($tag->tagName);
            if (!in_array($tag->tagName, $allowed_tags)){
                $tag->parentNode->removeChild($tag);
            }else{
                foreach ($tag->attributes as $attr){
                    if (!in_array($attr->nodeName, $allowed_attrs)){
                        $tag->removeAttribute($attr->nodeName);
                    }
                }
            }
        }
        $html = $this->innerHTML($body);
        $html = html_entity_decode($html);
        return trim($html);
        $html = $doc->saveHTML();
        
    }
}
