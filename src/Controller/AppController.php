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
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

/**
 * Controlador de Aplicación
 *
 * Añade métodos que serán heredados por todos los controladores
 * de la aplicación.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Método de inicialización
     *
     * Usa este método para añadir código de inicialización común como cargar componentes.
     *
     * ej. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    /**
     * Método ejecutado antes de cada acción del controlador
     *
     * @param \Cake\Event\EventInterface $event El evento beforeFilter
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Omitir autorización para rutas de DebugKit
        if ($this->getRequest()->getParam('plugin') === 'DebugKit') {
            $this->Authorization->skipAuthorization();
        }
    }

    /**
     * Obtiene una instancia de tabla del modelo
     *
     * Método de utilidad para obtener una tabla del modelo de forma más sencilla
     *
     * @param string $name Nombre de la tabla (ej: 'Users', 'JornaticCore.Companies')
     * @return mixed Instancia de la tabla del modelo
     */
    protected function getTable(string $name): mixed
    {
        return $this->fetchTable($name);
    }
}
