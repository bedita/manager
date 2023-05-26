<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ChildrenComponent;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\ChildrenComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ChildrenComponent
 */
class ChildrenComponentTest extends TestCase
{
    /**
     * @var \App\Controller\Component\ChildrenComponent
     */
    protected $component;

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->component);
        parent::tearDown();
    }

    /**
     * Test addRelated method with valid data.
     *
     * @return void
     * @covers ::addRelated()
     * @covers ::addRelatedChild()
     * @covers ::getClient()
     */
    public function testAddRelated(): void
    {
        $safeClient = ApiClientProvider::getApiClient();
        $children = [
            ['id' => '9991', 'type' => 'documents', 'meta' => ['title' => 'Document']],
            ['id' => '9992', 'type' => 'images', 'meta' => ['title' => 'Image']],
            ['id' => '9993', 'type' => 'folders', 'meta' => ['title' => 'Folder']],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->expects($this->exactly(3))
            ->method('addRelated')
            ->willReturn(['add response']);
        ApiClientProvider::setApiClient($apiClient);
        $registry = new ComponentRegistry();
        $this->component = new ChildrenComponent($registry);
        $result = $this->component->addRelated('9990', $children);
        $this->assertEquals([['add response'], ['add response'], ['add response']], $result);
        ApiClientProvider::setApiClient($safeClient);
    }

    /**
     * Test removeRelated method with valid data.
     *
     * @return void
     * @covers ::removeRelated()
     * @covers ::removeRelatedChild()
     * @covers ::getClient()
     */
    public function testRemoveRelated(): void
    {
        $safeClient = ApiClientProvider::getApiClient();
        $children = [
            ['id' => '9991', 'type' => 'documents', 'meta' => ['title' => 'Document']],
            ['id' => '9992', 'type' => 'images', 'meta' => ['title' => 'Image']],
            ['id' => '9993', 'type' => 'folders', 'meta' => ['title' => 'Folder']],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->expects($this->exactly(2))
            ->method('removeRelated')
            ->willReturn(['remove response']);
        $apiClient->expects($this->once())
            ->method('replaceRelated')
            ->willReturn(['replace response']);
        ApiClientProvider::setApiClient($apiClient);
        $registry = new ComponentRegistry();
        $this->component = new ChildrenComponent($registry);
        $result = $this->component->removeRelated('9990', $children);
        $this->assertEquals([['remove response'], ['remove response'], ['replace response']], $result);
        ApiClientProvider::setApiClient($safeClient);
    }
}
