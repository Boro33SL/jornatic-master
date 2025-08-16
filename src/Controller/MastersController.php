<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Masters Controller
 *
 * Handles authentication for master users
 *
 * @property \App\Model\Table\MastersTable $Masters
 */
class MastersController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Load logging component for audit trail
        $this->loadComponent('Logging');

        // Allow unauthenticated access to login only
        $this->Authorization->skipAuthorization(['login', 'add']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Allow unauthenticated access to login
        $this->Authentication->allowUnauthenticated(['login', 'add']);
    }

    /**
     * Login action
     *
     * @return \Cake\Http\Response|null|void
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);

        $result = $this->Authentication->getResult();

        // If user is already logged in, redirect to dashboard
        if ($result && $result->isValid()) {
            $redirect = $this->request->getQuery('redirect', '/');

            return $this->redirect($redirect);
        }

        // If login form was submitted
        if ($this->request->is('post')) {
            if ($result && $result->isValid()) {
                // Login exitoso - registrar en auditoría
                $master = $result->getData();
                $this->Logging->logLogin([
                    'master_id' => $master->id,
                    'master_name' => $master->name,
                    'login_method' => 'form',
                ]);
            } elseif ($this->request->is('post')) {
                // Login fallido - registrar en auditoría
                $email = $this->request->getData('email', '');
                $this->Logging->logLoginFailed($email, 'Invalid credentials');
                $this->Flash->error(__('_EMAIL_O_PASSWORD_INCORRECTOS'));
            }
        }
    }

    /**
     * Logout action
     *
     * @return \Cake\Http\Response|null|void
     */
    public function logout()
    {
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            // Registrar logout antes de cerrar sesión
            $this->Logging->logLogout();

            $this->Authentication->logout();
            $this->Flash->success(__('_SESION_CERRADA_CORRECTAMENTE'));
        }

        return $this->redirect(['action' => 'login']);
    }

    /**
     * Add action - Create new master user
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $this->Authorization->skipAuthorization();

        $master = $this->Masters->newEmptyEntity();

        if ($this->request->is('post')) {
            $master = $this->Masters->patchEntity($master, $this->request->getData());

            if ($this->Masters->save($master)) {
                // Registrar creación de usuario master
                $this->Logging->logCreate('masters', $master->id, [
                    'master_name' => $master->name,
                    'master_email' => $master->email,
                    'is_active' => $master->is_active,
                ]);

                $this->Flash->success(__('_USUARIO_MASTER_CREADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'dashboard']);
            }

            $this->Flash->error(__('_ERROR_AL_CREAR_USUARIO_MASTER'));
        }

        $this->set(compact('master'));
    }

    /**
     * Dashboard action
     *
     * @return void
     */
    public function dashboard()
    {
        $this->Authorization->skipAuthorization();

        // Registrar acceso al dashboard
        $this->Logging->logView('dashboard');

        $master = $this->Authentication->getIdentity();
        $this->set(compact('master'));
    }
}
