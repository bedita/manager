<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Model\TagsController as ModelTagsController;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Tags Controller
 */
class TagsController extends ModelTagsController
{
    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        parent::index();
        $this->viewBuilder()->setTemplate('/Pages/Model/Tags/index');
        $this->set('hideSidebar', true);
        $this->set('redirTo', ['_name' => 'tags:index']);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        parent::beforeRender($event);
        $this->set('moduleLink', ['_name' => 'tags:index']);

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * This to avoid extra perms check for "admin" role.
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        return null;
    }
}
