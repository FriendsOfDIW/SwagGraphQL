<?php
namespace GraphQL\Tests;

use GraphQL\Utils\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testAssignThrowsExceptionOnMissingRequiredKey()
    {
        $object = new \stdClass();
        $object->requiredKey = 'value';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Key requiredKey is expected to be set and not to be null');
        Utils::assign($object, [], ['requiredKey']);
    }
}
