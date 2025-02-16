<?php declare(strict_types=1);

namespace SwagGraphQL\Tests\Actions;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Test\Media\MediaFixtures;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use SwagGraphQL\Api\ApiController;
use SwagGraphQL\Factory\InflectorFactory;
use SwagGraphQL\Resolver\QueryResolver;
use SwagGraphQL\Schema\SchemaFactory;
use SwagGraphQL\Schema\TypeRegistry;
use SwagGraphQL\Tests\Traits\GraphqlApiTest;

class ProvideFileNameActionTest extends TestCase
{
    use GraphqlApiTest, MediaFixtures;

    /** @var ApiController */
    private $apiController;

    /** @var Context */
    private $context;

    /** @var EntityRepositoryInterface */
    private $repository;

    public function setUp(): void
    {
        $registry = $this->getContainer()->get(DefinitionInstanceRegistry::class);
        $schema = SchemaFactory::createSchema($this->getContainer()->get(TypeRegistry::class));
        $inflector = new InflectorFactory();

        $this->apiController = new ApiController($schema, new QueryResolver($this->getContainer(), $registry, $inflector));
        $this->context = Context::createDefaultContext();
        $this->setFixtureContext($this->context);

        $this->repository = $this->getContainer()->get('media.repository');
    }

    public function testProvideFileName()
    {
        $media = $this->getJpg();

        $query = sprintf('
            mutation {
	            provideFileName(
	                fileName: "%s",
	                fileExtension: "jpg"
	            )
            }
        ', $media->getFileName());

        $request = $this->createGraphqlRequestRequest($query);
        $response = $this->apiController->query($request, $this->context);
        static::assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        static::assertArrayNotHasKey('errors', $data, print_r($data, true));
        static::assertEquals(
            $data['data']['provideFileName'],
            $media->getFileName() . '_(1)',
            print_r($data['data'], true)
        );
    }
}
