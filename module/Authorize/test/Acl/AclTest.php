<?php

declare(strict_types=1);

namespace AuthorizeTest\Acl;

use Authorize\Acl\Acl;
use PHPUnit\Framework\TestCase;

class AclTest extends TestCase
{
    public function testInvalidConfiguration(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid ACL Config found');
        $acl = new Acl([]);
    }
}
