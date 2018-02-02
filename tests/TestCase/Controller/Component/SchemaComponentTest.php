<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\SchemaComponent;
use App\Model\API\BEditaClient;
use App\Model\API\BEditaClientException;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\SchemaComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\SchemaComponent
 */
class SchemaComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\SchemaComponent
     */
    public $Schema;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Auth');
        $this->Schema = $registry->load(SchemaComponent::class);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Schema);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetSchema` test case.
     *
     * @return array
     */
    public function getSchemaProvider()
    {
        return [
            'type as argument' => [
                [
                    'type' => 'object',
                ],
                [
                    'type' => 'object',
                ],
                'you-are-not-my-type',
            ],
            'type from config' => [
                [
                    'type' => 'object',
                ],
                [
                    'type' => 'object',
                ],
                null,
                [
                    'type' => 'you-are-not-my-type',
                ],
            ],
            'client exception' => [
                false,
                new BEditaClientException('I am a client exception'),
                'you-are-not-my-type',
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
                'you-are-not-my-type',
            ],
        ];
    }

    /**
     * Test `getSchema()` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param array|\Exception $schema Response to `/schema/:type` endpoint.
     * @param string|null $type Type to get schema for.
     * @param array $config Component configuration.
     * @return void
     *
     * @dataProvider getSchemaProvider()
     * @covers ::fetchSchema()
     * @covers ::getSchema()
     */
    public function testGetSchema($expected, $schema, $type, $config = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($schema instanceof \Exception) {
            $apiClient->method('schema')
                ->with($type ?: $config['type'])
                ->willThrowException($schema);
        } else {
            $apiClient->method('schema')
                ->with($type ?: $config['type'])
                ->willReturn($schema);
        }
        $this->Schema->setConfig($config + compact('apiClient'));

        $actual = $this->Schema->getSchema($type);

        static::assertSame($expected, $actual);
    }
}
