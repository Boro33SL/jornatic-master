<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class IconHelper extends Helper
{
    /**
     * Renderiza un icono SVG desde el directorio de iconos.
     *
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function render(string $name, string $type, array $attrs = []): string
    {
        $file = WWW_ROOT . 'img' . DS . 'icons' . DS . $type . DS . $name . '.svg';
        if (!file_exists($file)) {
            return '';
        }
        $svg = file_get_contents($file);

        // Inyectar clases u otros atributos
        if (!empty($attrs['class']) && empty($attrs[':class'])) {
            $svg = preg_replace(
                '/<svg/',
                '<svg class="' . h($attrs['class'] . ' text-primary') . '" style="' . h($attrs['style'] ?? '') . '"',
                $svg,
                1,
            );
        } elseif (!empty($attrs[':class'])) {
            $svg = preg_replace(
                '/<svg/',
                '<svg class="' . h($attrs['class'] . ' text-primary') . '":class="' . $attrs[':class'] . '"  style="' . h($attrs['style'] ?? '') . '"',
                $svg,
                1,
            );
        }

        return $svg;
    }
}
