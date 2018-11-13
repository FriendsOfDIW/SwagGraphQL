<?php
namespace GraphQL\Tests\Executor;

use GraphQL\Executor\ExecutionResult;
use PHPUnit\Framework\TestCase;

class ExecutionResultTest extends TestCase
{
    public function testToArrayWithoutExtensions()
    {
        $executionResult = new ExecutionResult();

        $this->assertEquals([], $executionResult->toArray());
    }

    public function testToArrayExtensions()
    {
        $executionResult = new ExecutionResult(null, [], ['foo' => 'bar']);

        $this->assertEquals(['extensions' => ['foo' => 'bar']], $executionResult->toArray());

        $executionResult->extensions = ['bar' => 'foo'];

        $this->assertEquals(['extensions' => ['bar' => 'foo']], $executionResult->toArray());
    }
}
