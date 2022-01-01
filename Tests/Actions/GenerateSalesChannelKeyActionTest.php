<?php declare(strict_types=1);

namespace SwagGraphQL\Tests\Actions;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use SwagGraphQL\Api\ApiController;
use SwagGraphQL\Resolver\QueryResolver;
use SwagGraphQL\Schema\SchemaFactory;
use SwagGraphQL\Schema\TypeRegistry;
use SwagGraphQL\Tests\Traits\GraphqlApiTest;

class GenerateSalesChannelKeyActionTest extends TestCase
{
    use GraphqlApiTest;

    /** @var ApiController */
    private $apiController;

    /**
     * @var Context
     */
    private $context;

    public function setUp(): void
    {
        $registry = $this->getContainer()->get(DefinitionInstanceRegistry::class);
        $schema = SchemaFactory::createSchema($this->getContainer()->get(TypeRegistry::class));

        $this->apiController = new ApiController($schema, new QueryResolver($this->getContainer(), $registry));
        $this->context = Context::createDefaultContext();
    }

    public function testGenerateUserKey()
    {
        $query = "
            query {
	            generateSalesChannelKey
	        }
        ";

        $request = $this->createGraphqlRequestRequest($query);
        $response = $this->apiController->query($request, $this->context);
        static::assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        static::assertArrayNotHasKey('errors', $data, print_r($data, true));
        static::assertNotNull(
            $data['data']['generateSalesChannelKey'],
            print_r($data['data'], true)
        );
    }
}
