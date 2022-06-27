<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\CloneComponent;
use App\Test\TestCase\Controller\BaseControllerTest;
use BEdita\SDK\BEditaClient;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\Component\CloneComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\CloneComponent
 */
class CloneComponentTest extends BaseControllerTest
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\CloneComponent
     */
    protected $Clone;

    /**
     * Controller for test
     *
     * @var \Cake\Controller\Controller
     */
    protected $controller;

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->Clone);
        parent::tearDown();
    }

    /**
     * Data provider for testRelations and testQueryCloneRelations.
     *
     * @return array
     */
    public function relationsProvider(): array
    {
        return [
            'do not clone relations' => [false],
            'clone relations' => [true],
        ];
    }

    /**
     * Test `queryCloneRelations`
     *
     * @param bool $expected The expected bool
     * @return void
     * @dataProvider relationsProvider()
     * @covers ::queryCloneRelations()
     */
    public function testQueryCloneRelations(bool $expected): void
    {
        $this->prepareClone($expected);
        $this->setupApi();
        $actual = $this->Clone->queryCloneRelations();
        static::assertSame($expected, $actual);
    }

    /**
     * Test `relations`.
     *
     * @param bool $cloneRelations
     * @return void
     * @dataProvider relationsProvider()
     * @covers ::relations()
     */
    public function testRelations(bool $cloneRelations): void
    {
        $this->prepareClone($cloneRelations);
        $this->setupApi();
        $response = $this->client->save('documents', ['title' => 'doc 1']);
        $doc1 = $response['data'];
        $response = $this->client->save('documents', ['title' => 'doc 2']);
        $doc2 = $response['data'];
        $destinationId = (string)$doc2['id'];
        $this->Clone->relations($doc1, $destinationId);
        $response = $this->client->getObject($destinationId, 'documents');
        $doc2 = $response['data'];

        $expected = array_keys((array)Hash::extract($doc1, 'relationships'));
        $expected = array_filter(
            $expected,
            function ($relationship) {
                return !in_array($relationship, ['children', 'parents', 'translations']);
            }
        );

        $actual = array_keys((array)Hash::extract($doc2, 'relationships'));
        $actual = array_filter(
            $actual,
            function ($relationship) {
                return !in_array($relationship, ['children', 'parents', 'translations']);
            }
        );

        static::assertSame($expected, $actual);
    }

    /**
     * Test `relation`
     *
     * @return void
     * @covers ::relation()
     */
    public function testRelation(): void
    {
        $this->prepareClone(true);
        $this->setupApi();
        $type = 'documents';
        $response = $this->client->save($type, ['title' => 'doc 1']);
        $doc1 = $response['data'];
        $sourceId = (string)$doc1['id'];
        $response = $this->client->save($type, ['title' => 'doc 2']);
        $doc2 = $response['data'];
        $destinationId = (string)$doc2['id'];
        // empty relation case: parents
        $this->Clone->startup();
        $this->Clone->relation($sourceId, $type, 'parents', $destinationId);

        $expected = array_keys((array)Hash::extract($doc1, 'relationships'));
        $expected = array_filter(
            $expected,
            function ($relationship) {
                return !in_array($relationship, ['children', 'parents', 'translations']);
            }
        );

        $actual = array_keys((array)Hash::extract($doc2, 'relationships'));
        $actual = array_filter(
            $actual,
            function ($relationship) {
                return !in_array($relationship, ['children', 'parents', 'translations']);
            }
        );

        static::assertSame($expected, $actual);
    }

    /**
     * Test `relations` and `relation`.
     *
     * @return void
     * @covers ::relations()
     */
    public function testRelationsWithMock(): void
    {
        $this->prepareClone(true);
        $this->setupApi();
        $response = $this->client->save('documents', ['title' => 'doc 1']);
        $doc1 = $response['data'];
        $response = $this->client->save('documents', ['title' => 'doc 2']);
        $doc2 = $response['data'];
        $destinationId = (string)$doc2['id'];
        $this->Clone = $this->createPartialMock(CloneComponent::class, ['filterRelations', 'queryCloneRelations']);
        $this->Clone
            ->method('filterRelations')
            ->willReturn(['parents']);
        $this->Clone
            ->method('queryCloneRelations')
            ->willReturn(true);
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('getRelated')
            ->willReturn(['data' => []]);
        $property = new \ReflectionProperty(get_class($this->Clone), 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Clone, $apiClient);
        $result = $this->Clone->relations($doc1, $destinationId);
        static::assertTrue($result);
    }

    /**
     * Test `relation`
     *
     * @return void
     * @covers ::relation()
     */
    public function testRelationWithMock(): void
    {
        $this->prepareClone(true);
        $this->setupApi();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('getRelated')
            ->willReturn(['data' => [
                ['id' => 991, 'type' => 'dummies'],
                ['id' => 992, 'type' => 'dummies'],
                ['id' => 993, 'type' => 'dummies'],
            ]]);
        $apiClient->method('addRelated')
                ->willReturn([]);
        $property = new \ReflectionProperty(get_class($this->Clone), 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Clone, $apiClient);
        $result = $this->Clone->relation('123', 'dummies', 'whatever', '456');
        static::assertTrue($result);
    }

    /**
     * Data provider for testFilterRelations
     *
     * @return array
     */
    public function filterRelationsProvider(): array
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'various relations' => [
                ['a', 'parents', 'b', 'children', 'c', 'd', 'translations', 'e'],
                ['a', 'b', 'c', 'd', 'e'],
            ],
        ];
    }

    /**
     * Test `filterRelations`
     *
     * @param array $relationships
     * @param array $expected
     * @return void
     * @dataProvider filterRelationsProvider()
     * @covers ::filterRelations()
     */
    public function testFilterRelations(array $relationships, array $expected): void
    {
        $this->prepareClone(true);
        $actual = $this->Clone->filterRelations($relationships);
        static::assertSame($expected, $actual);
    }

    private function prepareClone(bool $cloneRelations): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'query' => compact('cloneRelations'),
        ];
        $request = new ServerRequest($config);
        $this->controller = new Controller($request);
        $registry = $this->controller->components();
        /** @var \App\Controller\Component\CloneComponent $cloneComponent */
        $cloneComponent = $registry->load(CloneComponent::class);
        $this->Clone = $cloneComponent;
    }
}
