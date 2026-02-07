<?php
class ImageGenerator {
    private $width = 1200;
    private $height = 675;

    public function generateFeaturedImage($text, $bgColor, $textColor, $outputPath, $logoPath = null) {
        list($r, $g, $b) = $this->hexToRgb($bgColor);
        
        $darkerBg = $this->darkenColor($bgColor, 0.3);
        list($dr, $dg, $db) = $this->hexToRgb($darkerBg);

        $svg = $this->createSVG($text, $r, $g, $b, $dr, $dg, $db, $textColor, $logoPath);
        
        if (extension_loaded('imagick')) {
            return $this->convertSVGtoJPG($svg, $outputPath);
        } else {
            $svgPath = str_replace('.jpg', '.svg', $outputPath);
            file_put_contents($svgPath, $svg);
            return str_replace('.jpg', '.svg', '/uploads/featured/' . basename($outputPath));
        }
    }

    private function createSVG($text, $r, $g, $b, $dr, $dg, $db, $textColor, $logoPath = null) {
        $lines = $this->wrapText($text, 50);
        
        // Logo varsa yazıyı aşağı kaydır
        $logoHeight = 150;
        $logoPadding = 40;
        $startYBase = $this->height / 2;
        
        if ($logoPath) {
            // Logo varsa yazı daha aşağıda
            $startY = $startYBase + 50;
        } else {
            // Logo yoksa yazı ortada
            $lineHeight = 80;
            $totalHeight = count($lines) * $lineHeight;
            $startY = ($this->height - $totalHeight) / 2 + 60;
        }

        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="{$this->width}" height="{$this->height}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:rgb({$r},{$g},{$b});stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb({$dr},{$dg},{$db});stop-opacity:1" />
        </linearGradient>
        <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
            <feOffset dx="0" dy="2" result="offsetblur"/>
            <feComponentTransfer>
                <feFuncA type="linear" slope="0.5"/>
            </feComponentTransfer>
            <feMerge>
                <feMergeNode/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>
    
    <rect width="{$this->width}" height="{$this->height}" fill="url(#grad)"/>
    
SVG;

        // Logo ekle (Sol üst)
        if ($logoPath && file_exists(__DIR__ . '/../../public' . $logoPath)) {
            $logoX = $logoPadding;
            $logoY = $logoPadding;
            
            // Logo'yu base64'e çevir
            $logoFullPath = __DIR__ . '/../../public' . $logoPath;
            $logoData = base64_encode(file_get_contents($logoFullPath));
            $logoExt = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoMime = $logoExt === 'png' ? 'image/png' : 'image/jpeg';
            
            $svg .= <<<LOGO
    <image x="{$logoX}" y="{$logoY}" width="{$logoHeight}" height="{$logoHeight}" 
           xlink:href="data:{$logoMime};base64,{$logoData}" 
           opacity="0.95"/>

LOGO;
        }

        // Yazı ekle (Orta)
        $lineHeight = 80;
        foreach ($lines as $index => $line) {
            $y = $startY + ($index * $lineHeight);
            $svg .= <<<LINE
    <text x="50%" y="{$y}" 
          font-family="Arial, sans-serif" 
          font-size="60" 
          font-weight="bold" 
          fill="{$textColor}" 
          text-anchor="middle"
          filter="url(#shadow)">
        {$this->escapeXML($line)}
    </text>

LINE;
        }

        $svg .= '</svg>';
        
        return $svg;
    }

    private function wrapText($text, $maxLength) {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxLength) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    private function convertSVGtoJPG($svg, $outputPath) {
        try {
            $imagick = new Imagick();
            $imagick->readImageBlob($svg);
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(90);
            $imagick->writeImage($outputPath);
            $imagick->clear();
            $imagick->destroy();
            
            return '/uploads/featured/' . basename($outputPath);
        } catch (Exception $e) {
            $svgPath = str_replace('.jpg', '.svg', $outputPath);
            file_put_contents($svgPath, $svg);
            return str_replace('.jpg', '.svg', '/uploads/featured/' . basename($outputPath));
        }
    }

    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }

    private function darkenColor($hex, $percent) {
        list($r, $g, $b) = $this->hexToRgb($hex);
        
        $r = max(0, $r - ($r * $percent));
        $g = max(0, $g - ($g * $percent));
        $b = max(0, $b - ($b * $percent));
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    private function escapeXML($text) {
        return htmlspecialchars($text, ENT_XML1, 'UTF-8');
    }
}
