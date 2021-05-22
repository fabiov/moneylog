<?php

namespace ApplicationTest\Entity;

use Application\Entity\Provision;
use Laminas\InputFilter\InputFilter;
use PHPUnit\Framework\TestCase;

class ProvisionTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $provision = new Provision();

        self::expectException(\Exception::class);
        $provision->setInputFilter(new InputFilter());
    }
}
