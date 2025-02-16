<?php declare(strict_types=1);

namespace SwagGraphQL\Tests\Actions;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\Pathname\UrlGeneratorInterface;
use Shopware\Core\Content\Test\Media\MediaFixtures;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use SwagGraphQL\Api\ApiController;
use SwagGraphQL\Factory\InflectorFactory;
use SwagGraphQL\Resolver\QueryResolver;
use SwagGraphQL\Schema\SchemaFactory;
use SwagGraphQL\Schema\TypeRegistry;
use SwagGraphQL\Tests\Traits\GraphqlApiTest;

class RenameMediaActionTest extends TestCase
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

    public function testRenameMedia()
    {
        $media = $this->getJpg();

        $urlGenerator = $this->getContainer()->get(UrlGeneratorInterface::class);
        $mediaPath = $urlGenerator->getRelativeMediaUrl($media);

        $this->getPublicFilesystem()->put($mediaPath, 'test file');

        $query = sprintf('
            mutation {
	            renameMedia(
	                mediaId: "%s"
	                fileName: "new Name"
	            ) {
	                id
	                fileName
	            }
            }
        ', $media->getId());

        $request = $this->createGraphqlRequestRequest($query);
        $response = $this->apiController->query($request, $this->context);
        static::assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        static::assertArrayNotHasKey('errors', $data, print_r($data, true));
        static::assertEquals(
            $data['data']['renameMedia']['id'],
            $media->getId(),
            print_r($data['data'], true)
        );
        static::assertEquals(
            $data['data']['renameMedia']['fileName'],
            'new Name',
            print_r($data['data'], true)
        );

        /** @var MediaEntity $updatedMedia */
        $updatedMedia = $this->repository
            ->search(new Criteria([$media->getId()]), $this->context)
            ->get($media->getId());
        static::assertEquals('new Name', $updatedMedia->getFileName());

        static::assertFalse($this->getPublicFilesystem()->has($mediaPath));
        static::assertTrue($this->getPublicFilesystem()->has($urlGenerator->getRelativeMediaUrl($updatedMedia)));
    }
}
