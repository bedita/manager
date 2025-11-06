<?php
declare(strict_types=1);

namespace App\Controller;

use BEdita\WebTools\ApiClientProvider;
use Cake\Utility\Hash;
use Exception;

/**
 * SendMail Controller
 */
class SendMailController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->FormProtection->setConfig('unlockedActions', ['index']);
    }

    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $this->getRequest()->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');
        try {
            $payload = $this->getRequest()->getData();
            foreach ($payload['data'] as $key => $value) {
                if (strpos($key, '.') !== false) {
                    unset($payload['data'][$key]);
                    $payload['data'] = Hash::insert($payload['data'], $key, $value);
                }
            }
            ApiClientProvider::getApiClient()->post('/placeholders/send', (string)json_encode($payload));
            $response = ['message' => 'Email sent successfully'];
            $this->set('response', $response);
            $this->setSerialize(['response']);
        } catch (Exception $e) {
            $this->set('error', $e->getMessage());
            $this->setSerialize(['error']);
        }
    }
}
