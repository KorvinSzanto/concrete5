<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/frontend/conversations
 * Namespace: Concrete\Controller\Frontend\Conversations
 */
$router->post('/add_file', 'AddFile::handle');