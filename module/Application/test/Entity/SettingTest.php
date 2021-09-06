<?php

namespace ApplicationTest\Entity;

use Application\Entity\Setting;
use Application\Entity\User;
use Laminas\InputFilter\InputFilter;
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

        self::assertInstanceOf(InputFilter::class, $setting->getInputFilter());

        self::expectException(\Exception::class);
        $setting->setInputFilter(new InputFilter());
    }

    public function setPaydayException(): void
    {
        $user = new User();
        $setting = new Setting($user);

        self::expectException(\Exception::class);
        $setting->setPayday(29);
    }

    public function testArrayCopy(): void
    {
        $user = new User();
        $months = 54;
        $payday = 12;
        $provisioning = true;

        $setting = new Setting($user);
        $setting->setMonths($months);
        $setting->setPayday($payday);
        $setting->setProvisioning($provisioning);

        $copy = $setting->getArrayCopy();

        self::assertSame($months, $copy['months']);
        self::assertSame($payday, $copy['payday']);
        self::assertSame($provisioning, $copy['provisioning']);
    }

    public function testInvalidPayDay(): void
    {
        $user = new User();
        $setting = new Setting($user);

        self::expectException(\InvalidArgumentException::class);
        $setting->setPayday(29);
    }
}
