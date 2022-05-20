<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\CloneComponent;
use App\Test\TestCase\Controller\BaseControllerTest;
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
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->Clone);
        parent::tearDown();
    }

    /**
     * Data provider for testRelations.
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
     * Test `relations` and `relation`.
     *
     * @return void
     * @dataProvider relationsProvider()
     * @covers ::relations()
     * @covers ::relation()
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

    private function prepareClone(bool $cloneRelations): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'query' => compact('cloneRelations'),
        ];
        $request = new ServerRequest($config);
        $controller = new Controller($request);
        $registry = $controller->components();
        /** @var \App\Controller\Component\CloneComponent $cloneComponent */
        $cloneComponent = $registry->load(CloneComponent::class);
        $this->Clone = $cloneComponent;
    }
}
