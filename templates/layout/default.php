<?php
/**
 * Master Application Layout
 * Layout principal para la aplicación de gestión master
 * 
 * @var \App\View\AppView $this
 */

$appTitle = 'Jornatic Master';
$master = $this->getRequest()->getAttribute('identity');
$isAuthenticated = !empty($master);
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $appTitle ?>
        <?php if ($this->fetch('title')): ?>
            - <?= $this->fetch('title') ?>
        <?php endif; ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <!-- Estilos principales -->
    <?= $this->Html->css(['dist/master']) ?>

    <!-- Scripts - Alpine.js con defer, Chart.js -->
    <script src="<?= $this->Url->webroot('js/general/alpine.js') ?>" defer></script>
    <script src="<?= $this->Url->webroot('js/general/chart.js') ?>"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="min-h-screen bg-base-200">

    <?php if ($isAuthenticated): ?>
        <!-- Navbar principal para usuarios autenticados -->
        <nav class="navbar navbar-master shadow-lg">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                        </svg>
                    </div>
                </div>
                <a class="btn btn-ghost text-xl text-white" href="<?= $this->Url->build('/') ?>">
                    <strong>Jornatic</strong> Master
                </a>
            </div>
            
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 text-white">
                    <li><a href="<?= $this->Url->build('/') ?>" class="hover:bg-white/10">Dashboard</a></li>
                    <li><a href="#" class="hover:bg-white/10">Companies</a></li>
                    <li><a href="#" class="hover:bg-white/10">Subscriptions</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'MasterAccessLogs', 'action' => 'index']) ?>" class="hover:bg-white/10">Logs Auditoría</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Masters', 'action' => 'add']) ?>" class="hover:bg-white/10">Crear Master</a></li>
                </ul>
            </div>
            
            <div class="navbar-end">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar text-white">
                        <div class="w-10 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="text-sm font-bold"><?= strtoupper(substr(h($master->name ?? 'M'), 0, 1)) ?></span>
                        </div>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li class="menu-title">
                            <span><?= h($master->name ?? 'Master User') ?></span>
                        </li>
                        <li><a href="#">Profile</a></li>
                        <li><a href="#">Settings</a></li>
                        <li><hr class="my-2"></li>
                        <li>
                            <?= $this->Form->postLink(
                                'Logout',
                                ['controller' => 'Masters', 'action' => 'logout'],
                                ['class' => 'text-error', 'confirm' => 'Are you sure you want to logout?']
                            ) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main class="<?= $isAuthenticated ? 'p-6' : '' ?>">
        <?php if ($isAuthenticated): ?>
            <div class="max-w-7xl mx-auto">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
            </div>
        <?php else: ?>
            <!-- Layout para páginas sin autenticación (login) -->
            <div class="min-h-screen flex items-center justify-center">
                <div class="w-full max-w-md">
                    <?= $this->Flash->render() ?>
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer (opcional) -->
    <?php if ($isAuthenticated): ?>
        <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-auto">
            <div>
                <p class="text-sm">© 2025 Jornatic Master. Gestión integral del ecosistema.</p>
            </div>
        </footer>
    <?php endif; ?>

</body>
</html>
