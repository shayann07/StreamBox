<?php

namespace App\Libraries;

use Config\App;

class Purify
{
    protected $config;
    
    public function __construct()
    {
        $this->config = config('App');
    }
    
    /**
     * HTML purification with basic CSS support
     */
    public function purifyHtml($dirtyHtml)
    {
        if (empty($dirtyHtml)) {
            return '';
        }
        
        // Remove script tags completely
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $dirtyHtml);
        
        // Remove event handlers (onclick, onload, etc.)
        $clean = preg_replace('/ on\w+=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace("/ on\w+=\s*'[^']*'/i", '', $clean);
        $clean = preg_replace('/ on\w+=\s*\S*/i', '', $clean);
        
        // Remove dangerous protocols
        $clean = preg_replace('/href="\s*javascript:[^"]*"/i', 'href="#"', $clean);
        $clean = preg_replace("/href='\s*javascript:[^']*'/i", "href='#'", $clean);
        $clean = preg_replace('/src="\s*javascript:[^"]*"/i', 'src="#"', $clean);
        $clean = preg_replace("/src='\s*javascript:[^']*'/i", "src='#'", $clean);
        
        // Remove potentially dangerous tags
        $dangerousTags = ['iframe', 'object', 'embed', 'base', 'meta', 'link'];
        foreach ($dangerousTags as $tag) {
            $clean = preg_replace('/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '>/is', '', $clean);
            $clean = preg_replace('/<' . $tag . '\b[^>]*>/is', '', $clean);
        }
        
        // Allow safe tags including style tag (with restrictions)
        $allowedTags = '<p><br><strong><em><b><i><u><span><div><a><img><ul><ol><li><h1><h2><h3><h4><h5><h6><table><tr><td><th><thead><tbody><tfoot><blockquote><code><pre><style>';
        $clean = strip_tags($clean, $allowedTags);
        
        // Clean up attributes and styles
        $clean = $this->cleanAttributes($clean);
        $clean = $this->cleanStyleTags($clean);
        $clean = $this->cleanStyleAttributes($clean);
        
        return trim($clean);
    }
    
    /**
     * Clean style tags and their content
     */
    protected function cleanStyleTags($html)
    {
        // Remove style tags that contain dangerous content
        $html = preg_replace_callback('/<style[^>]*>(.*?)<\/style>/is', function($matches) {
            $cssContent = $matches[1];
            $cleanCss = $this->purifyCss($cssContent);
            return "<style>{$cleanCss}</style>";
        }, $html);
        
        return $html;
    }
    
    /**
     * Clean style attributes
     */
    protected function cleanStyleAttributes($html)
    {
        // Clean style attributes in all tags
        $html = preg_replace_callback('/style="([^"]*)"/i', function($matches) {
            $css = $matches[1];
            $cleanCss = $this->purifyCss($css);
            return 'style="' . $cleanCss . '"';
        }, $html);
        
        return $html;
    }
    
    /**
     * Purify CSS content - allow only safe properties
     */
    public function purifyCss($css)
    {
        if (empty($css)) {
            return '';
        }
        
        // List of allowed CSS properties
        $allowedProperties = [
            // Text and font properties
            'color', 'font-size', 'font-family', 'font-weight', 'font-style', 'text-align',
            'text-decoration', 'line-height', 'letter-spacing', 'text-transform',
            
            // Layout properties
            'width', 'height', 'max-width', 'max-height', 'min-width', 'min-height',
            'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
            'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
            
            // Positioning
            'position', 'top', 'right', 'bottom', 'left', 'float', 'clear',
            'display', 'visibility', 'overflow', 'z-index',
            
            // Background
            'background-color', 'background-image', 'background-position', 
            'background-repeat', 'background-size', 'background',
            
            // Border
            'border', 'border-width', 'border-style', 'border-color',
            'border-top', 'border-right', 'border-bottom', 'border-left',
            'border-radius', 'box-shadow',
            
            // Flexbox
            'flex', 'flex-direction', 'flex-wrap', 'justify-content', 'align-items',
            
            // Other safe properties
            'opacity', 'cursor', 'transition', 'animation', 'transform'
        ];
        
        // Remove dangerous CSS patterns
        $css = preg_replace('/expression\(/i', '', $css); // Remove expression()
        $css = preg_replace('/javascript:/i', '', $css); // Remove javascript:
        $css = preg_replace('/data:/i', '', $css); // Remove data:
        $css = preg_replace('/behavior:/i', '', $css); // Remove behavior:
        $css = preg_replace('/binding:/i', '', $css); // Remove binding:
        
        // Remove @import rules (could load external resources)
        $css = preg_replace('/@import[^;]*;/i', '', $css);
        
        // Remove @keyframes and @font-face (could be used for fingerprinting)
        $css = preg_replace('/@keyframes[^{]*\{[^}]*\}/i', '', $css);
        $css = preg_replace('/@font-face[^{]*\{[^}]*\}/i', '', $css);
        
        // Filter properties - only allow safe ones
        $css = preg_replace_callback('/([a-zA-Z\-]+)\s*:\s*([^;]*)(;|$)/', function($matches) use ($allowedProperties) {
            $property = strtolower(trim($matches[1]));
            $value = trim($matches[2]);
            
            if (in_array($property, $allowedProperties)) {
                // Additional value validation for certain properties
                $value = $this->validateCssValue($property, $value);
                return "{$property}: {$value};";
            }
            
            return ''; // Remove disallowed properties
        }, $css);
        
        return trim($css);
    }
    
    /**
     * Validate CSS values for specific properties
     */
    protected function validateCssValue($property, $value)
    {
        switch ($property) {
            case 'background-image':
                // Only allow simple gradients and safe URLs
                if (preg_match('/url\([^)]*\)/', $value)) {
                    // Remove background-image if it contains url() - or restrict to data URIs if needed
                    return 'none';
                }
                return $value;
                
            case 'z-index':
                // Only allow numbers
                if (preg_match('/^-?\d+$/', $value)) {
                    return $value;
                }
                return 'auto';
                
            case 'position':
                // Only allow safe position values
                $safePositions = ['static', 'relative', 'absolute', 'fixed', 'sticky'];
                if (in_array($value, $safePositions)) {
                    return $value;
                }
                return 'static';
                
            default:
                return $value;
        }
    }
    
    /**
     * Clean attributes from allowed tags
     */
    protected function cleanAttributes($html)
    {
        $allowedAttributes = [
            'a' => ['href', 'title', 'target', 'rel', 'class', 'id', 'style'],
            'img' => ['src', 'alt', 'title', 'width', 'height', 'class', 'id', 'style'],
            'div' => ['class', 'id', 'style'],
            'span' => ['class', 'id', 'style'],
            'p' => ['class', 'id', 'style'],
            'h1' => ['class', 'id', 'style'],
            'h2' => ['class', 'id', 'style'],
            'h3' => ['class', 'id', 'style'],
            'h4' => ['class', 'id', 'style'],
            'h5' => ['class', 'id', 'style'],
            'h6' => ['class', 'id', 'style'],
            'table' => ['class', 'id', 'style', 'border', 'cellpadding', 'cellspacing'],
            'tr' => ['class', 'id', 'style'],
            'td' => ['class', 'id', 'style', 'colspan', 'rowspan'],
            'th' => ['class', 'id', 'style', 'colspan', 'rowspan'],
            'style' => ['type'], // Allow type attribute for style tags
        ];
        
        $pattern = '/<(\w+)(\s+[^>]*)?>/i';
        $html = preg_replace_callback($pattern, function($matches) use ($allowedAttributes) {
            $tag = strtolower($matches[1]);
            $attributes = $matches[2] ?? '';
            
            if (isset($allowedAttributes[$tag])) {
                $cleanedAttributes = $this->filterAttributes($attributes, $allowedAttributes[$tag]);
                return "<{$tag}{$cleanedAttributes}>";
            }
            
            return "<{$tag}>";
        }, $html);
        
        return $html;
    }
    
    /**
     * Filter attributes to only allow specific ones
     */
    protected function filterAttributes($attributesString, $allowedAttrs)
    {
        if (empty($attributesString)) {
            return '';
        }
        
        preg_match_all('/(\w+)\s*=\s*("[^"]*"|\'[^\']*\'|[^"\'][^\\s>]*)/i', $attributesString, $matches, PREG_SET_ORDER);
        
        $cleanAttributes = [];
        foreach ($matches as $match) {
            $attrName = strtolower($match[1]);
            $attrValue = $match[2];
            
            if (in_array($attrName, $allowedAttrs)) {
                $attrValue = $this->cleanAttributeValue($attrName, $attrValue);
                $cleanAttributes[] = "{$attrName}={$attrValue}";
            }
        }
        
        return $cleanAttributes ? ' ' . implode(' ', $cleanAttributes) : '';
    }
    
    /**
     * Clean specific attribute values
     */
    protected function cleanAttributeValue($attrName, $attrValue)
    {
        $value = trim($attrValue, '"\'');
        
        switch ($attrName) {
            case 'href':
            case 'src':
                if (preg_match('/^(javascript:|data:|vbscript:)/i', $value)) {
                    return '"#"';
                }
                $value = filter_var($value, FILTER_SANITIZE_URL);
                break;
                
            case 'style':
                $value = $this->purifyCss($value);
                break;
                
            case 'class':
            case 'id':
                $value = preg_replace('/[^a-zA-Z0-9\-_]/', '', $value);
                break;
        }
        
        return '"' . $value . '"';
    }
    
    /**
     * Purify only CSS (for separate CSS content)
     */
    public function purifyCssOnly($css)
    {
        return $this->purifyCss($css);
    }
    
    /**
     * Strict purification - removes all HTML tags and CSS
     */
    public function purifyText($text)
    {
        if (empty($text)) {
            return '';
        }
        
        $clean = strip_tags($text);
        $clean = htmlspecialchars($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $clean;
    }
}