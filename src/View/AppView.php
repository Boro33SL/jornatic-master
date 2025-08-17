<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use App\View\Helper\IconHelper;
use Cake\View\View;

/**
 * Vista de Aplicación
 *
 * Clase de vista por defecto de tu aplicación
 *
 * @link https://book.cakephp.org/5/en/views.html#the-app-view
 * @property \App\View\Helper\IconHelper $Icon
 */
class AppView extends View
{
    private IconHelper $Icon;

    /**
     * Función de inicialización
     *
     * Usa esta función para agregar código de inicialización común como añadir helpers
     *
     * ej. `$this->addHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadHelper('Icon');
    }
}
