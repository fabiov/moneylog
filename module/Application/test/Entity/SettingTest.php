<?php

namespace ApplicationTest\Entity;

use Application\Entity\Setting;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class SettingTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();
        $setting = new Setting($user);

        $months = 12;
        $setting->setMonths($months);
        self::assertSame($months, $setting->getMonths());

        $payday = 2;
        $setting->setPayday($payday);
        self::assertSame($payday, $setting->getPayday());

        $provisioning = true;
        $setting->setProvisioning($provisioning);
        self::assertSame($provisioning, $setting->hasProvisioning());
    }

    public function setPaydayException(): void
    {
        $user = new User();
        $setting = new Setting($user);

        self::expectException(\Exception::class);
        $setting->setPayday(29);
    }

    public function testInvalidPayDay(): void
    {
        $user = new User();
        $setting = new Setting($user);

        self::expectException(\InvalidArgumentException::class);
        $setting->setPayday(29);
    }
}
