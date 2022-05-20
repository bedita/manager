<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\CloneComponent;
use App\Test\TestCase\Controller\BaseControllerTest;
use Cake\Controller\Controller;
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
    protected function setUp(): void
    {
        parent::setUp();
        $controller = new Controller();
        $registry = $controller->components();
        /** @var \App\Controller\Component\CloneComponent $cloneComponent */
        $cloneComponent = $registry->load(CloneComponent::class);
        $this->Clone = $cloneComponent;
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        unset($this->Clone);
        parent::tearDown();
    }

    /**
     * Test `relations` and `relation`.
     *
     * @return void
     * @covers ::relations()
     * @covers ::relation()
     */
    public function testRelations(): void
    {
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
}
