<?php

namespace Biig\Component\Domain\Tests\Model;

use Biig\Component\Domain\Model\DomainModel;
use Biig\Component\Domain\Model\ModelInterface;
use PHPUnit\Framework\TestCase;

class DomainModelTest extends TestCase
{
    public function testItIsInstanceOfModelInterface()
    {
        $model = new class() extends DomainModel {
        };

        $this->assertInstanceOf(ModelInterface::class, $model);
    }
}
