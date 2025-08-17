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
 * @since         3.3.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Controlador de Manejo de Errores
 *
 * Controlador usado por ExceptionRenderer para renderizar respuestas de error.
 */
class ErrorController extends AppController
{
    /**
     * Método de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        // Only add parent::initialize() if you are confident your `AppController` is safe.
    }

    /**
     * Callback beforeFilter
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Evento
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
    }

    /**
     * Callback beforeRender
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Evento
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setTemplatePath('Error');
    }

    /**
     * Callback afterFilter
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Evento
     * @return void
     */
    public function afterFilter(EventInterface $event): void
    {
    }
}
