<?php
namespace GraphQL\Tests\Utils;

use GraphQL\Language\Parser;
use GraphQL\Language\SourceLocation;
use GraphQL\Type\Definition\Type;
use GraphQL\Utils\Utils;
use GraphQL\Validator\DocumentValidator;
use PHPUnit\Framework\TestCase;

class IsValidLiteralValueTest extends TestCase
{
    // DESCRIBE: isValidLiteralValue

    /**
     * @see it('Returns no errors for a valid value')
     */
    public function testReturnsNoErrorsForAValidValue()
    {
        $this->assertEquals(
            [],
            DocumentValidator::isValidLiteralValue(Type::int(), Parser::parseValue('123'))
        );
    }

    /**
     * @see it('Returns errors for an invalid value')
     */
    public function testReturnsErrorsForForInvalidValue()
    {
        $errors = DocumentValidator::isValidLiteralValue(Type::int(), Parser::parseValue('"abc"'));

        $this->assertCount(1, $errors);
        $this->assertEquals('Expected type Int, found "abc".', $errors[0]->getMessage());
        $this->assertEquals([new SourceLocation(1, 1)], $errors[0]->getLocations());
        $this->assertEquals(null, $errors[0]->getPath());
    }
}
