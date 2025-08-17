<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

/**
 * Clase de vista para respuestas AJAX
 *
 * Actualmente solo cambia el layout por defecto y establece el tipo de respuesta -
 * que se mapea a text/html por defecto
 */
class AjaxView extends AppView
{
    /**
     * El nombre del archivo de layout para renderizar la vista dentro de él. El nombre
     * especificado es el nombre del archivo del layout en /templates/Layout sin
     * la extensión .php
     *
     * @var string
     */
    protected string $layout = 'ajax';

    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->response = $this->response->withType('ajax');
    }
}
