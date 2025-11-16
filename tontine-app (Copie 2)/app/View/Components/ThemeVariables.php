<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThemeVariables extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.theme-variables');
    }

    /**
     * Éclaircir une couleur hexadécimale
     */
    public function lighten($hex, $percent)
    {
        $rgb = $this->hexToRgb($hex);
        
        $rgb['r'] = min(255, $rgb['r'] + (255 - $rgb['r']) * $percent);
        $rgb['g'] = min(255, $rgb['g'] + (255 - $rgb['g']) * $percent);
        $rgb['b'] = min(255, $rgb['b'] + (255 - $rgb['b']) * $percent);
        
        return $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Assombrir une couleur hexadécimale
     */
    public function darken($hex, $percent)
    {
        $rgb = $this->hexToRgb($hex);
        
        $rgb['r'] = max(0, $rgb['r'] * (1 - $percent));
        $rgb['g'] = max(0, $rgb['g'] * (1 - $percent));
        $rgb['b'] = max(0, $rgb['b'] * (1 - $percent));
        
        return $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Convertir hex en RGB
     */
    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Convertir RGB en hex
     */
    private function rgbToHex($r, $g, $b)
    {
        return sprintf("#%02x%02x%02x", round($r), round($g), round($b));
    }
}
