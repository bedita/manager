<?php
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

use App\Model\API\BEditaClient;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * BEdita4 API client 
     *
     * @var [type]
     */
    protected $apiClient = null;

    /**
     * available modules 
     *
     * @var array
     */
    protected $modules = null;

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');

        $opts = Configure::read('API');
        $this->apiClient = new BEditaClient($opts['apiBaseUrl'], $opts['apiKey']);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        $this->set('baseUrl', Router::url('/'));
        $this->modules = $this->apiClient->get('/model/object_types', ['enabled' => true]);
        $this->modules = Hash::extract($this->modules, 'data.{n}.attributes');
        $this->set('modules', $this->modules);
        // TODO: read from /home
        $this->set('project', 'BE4 Test');
        $this->set('version', '4.0.0-beta');
        $this->set('colophon', '');
    }
}
