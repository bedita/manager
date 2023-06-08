<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ParentsComponent;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\ParentsComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ParentsComponent
 */
class ParentsComponentTest extends TestCase
{
    /**
     * @var \App\Controller\Component\ParentsComponent
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
            ->method('replaceRelated')
            ->willReturn(['replace response']);
        ApiClientProvider::setApiClient($apiClient);
        $registry = new ComponentRegistry();
        $this->component = new ParentsComponent($registry);
        $result = $this->component->addRelated('9990', $children);
        $this->assertEquals([['replace response'], ['replace response'], ['replace response']], $result);
        ApiClientProvider::setApiClient($safeClient);
    }
}
