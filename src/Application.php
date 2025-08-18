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
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\OrmResolver;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Clase de configuración de la aplicación
 *
 * Define la lógica de arranque y las capas de middleware que
 * quieres usar en tu aplicación.
 *
 * @extends \Cake\Http\BaseApplication<\App\Application>
 */
class Application extends BaseApplication implements
    AuthenticationServiceProviderInterface,
    AuthorizationServiceProviderInterface
{
    /**
     * Cargar toda la configuración de la aplicación y lógica de arranque
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        // Cargar plugins ANTES de configurar TableLocator para evitar conflictos
        $this->addPlugin('JornaticCore');
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');

        // Configurar TableLocator DESPUÉS de cargar plugins
        if (PHP_SAPI !== 'cli') {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false),
            );
        }
    }

    /**
     * Configurar la cola de middleware que usará tu aplicación
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance.
            // See https://github.com/CakeDC/cakephp-cached-routing
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/5/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Authentication middleware - debe ir antes de Authorization
            ->add(new AuthenticationMiddleware($this))

            // Authorization middleware - después de Authentication
            ->add(new AuthorizationMiddleware($this))

            // Cross Site Request Forgery (CSRF) Protection Middleware
            // https://book.cakephp.org/5/en/security/csrf.html#cross-site-request-forgery-csrf-middleware
            ->add(new CsrfProtectionMiddleware([
                'httponly' => true,
            ]));

        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/5/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
    }

    /**
     * Returns an authorization service instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authorization\AuthorizationServiceInterface
     */
    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        // Usar ORM resolver para entidades y tablas
        $resolver = new OrmResolver();

        return new AuthorizationService($resolver);
    }

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        // Configuración moderna para cakephp/authentication 3.3.x
        // Cada autenticador tiene su propio identifier configurado
        $service = new AuthenticationService([
            'unauthenticatedRedirect' => Router::url('/masters/login'),
            'queryParam' => 'redirect',
            'logoutRedirect' => '/masters/login',
            'authenticators' => [
                'Authentication.Session' => [
                    'identifier' => [
                        'Authentication.Password' => [
                            'fields' => [
                                'username' => 'email',
                                'password' => 'password',
                            ],
                            'resolver' => [
                                'className' => 'Authentication.Orm',
                                'userModel' => 'Masters',
                                'finder' => 'auth',
                            ],
                        ],
                    ],
                ],
                'Authentication.Form' => [
                    'identifier' => [
                        'Authentication.Password' => [
                            'fields' => [
                                'username' => 'email',
                                'password' => 'password',
                            ],
                            'resolver' => [
                                'className' => 'Authentication.Orm',
                                'userModel' => 'Masters',
                                'finder' => 'auth',
                            ],
                        ],
                    ],
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password',
                    ],
                    'loginUrl' => Router::url('/masters/login'),
                ],
            ],
        ]);

        return $service;
    }
}
