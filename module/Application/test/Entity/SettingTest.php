<?php

namespace ApplicationTest\Entity;

use Application\Entity\Setting;
use Application\Entity\User;
use PHPUnit\Framework\TestCase;

class SettingTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $payday = 2;
        $months = 12;
        $provisioning = true;
        $user = new User('', '', '', '', '', User::STATUS_CONFIRMED, '', '');
        $setting = new Setting($user, $payday, $months, $provisioning);

        self::assertSame($months, $setting->getMonths());
        self::assertSame($payday, $setting->getPayday());
        self::assertSame($provisioning, $setting->hasProvisioning());

        $payday = 4;
        $setting->setPayday($payday);
        self::assertSame($payday, $setting->getPayday());

        $months = 13;
        $setting->setMonths($months);
        self::assertSame($months, $setting->getMonths());

        $provisioning = false;
        $setting->setProvisioning($provisioning);
        self::assertSame($provisioning, $setting->hasProvisioning());

        self::expectException(\Exception::class);
        $setting->setPayday(29);
    }

    public function setPaydayException(): void
    {
        $setting = new Setting(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), 1, 2, true);

        self::expectException(\Exception::class);
        $setting->setPayday(29);
    }

    public function testInvalidPayDay(): void
    {
        $setting = new Setting(new User('', '', '', '', '', User::STATUS_CONFIRMED, '', ''), 1, 2, true);

        self::expectException(\InvalidArgumentException::class);
        $setting->setPayday(29);
    }
}
