<?php

namespace AuthTest\Model;

use Auth\Model\LoggedUserSettings;
use PHPUnit\Framework\TestCase;

class LoggedUserSettingTest extends TestCase
{
    public function testGetters(): void
    {
        $payday = 10;
        $months = 12;
        $provisioning = true;
        $loggedUserSetting = new LoggedUserSettings($payday, $months, $provisioning);

        self::assertSame($payday, $loggedUserSetting->getPayday());
        self::assertSame($months, $loggedUserSetting->getMonths());
        self::assertSame($provisioning, $loggedUserSetting->hasProvisioning());
    }
}
