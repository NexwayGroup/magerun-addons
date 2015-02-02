<?php

namespace Tests\Nexway\SetupManager\Util\Helper;

use Nexway\SetupManager\Util\Helper\Utils;

/**
 * Class UtilsTest
 * @package Tests\Nexway\SetupManager\Util\Helper
 *
 * @author     Michał Rudnicki <mrudnicki@nexway.com>
 * @copyright  Copyright (c) 2015 Nexway
 *
 * @coversDefaultClass Nexway\SetupManager\Util\Helper\Utils
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @covers ::generateRandomOrderPrefix
     */
    public function generateRandomOrderPrefix()
    {
        $utils = new Utils();
        $generated = $utils->generateRandomOrderPrefix();

        $counter1 = time() - Utils::TEST_ORDER_PREFIX_DATE;
        $counter2 = $counter1 - 1; // also check a second ago in case the clock has ticked
        $counter1Base36 = strtoupper(base_convert($counter1, 10, 36));
        $counter2Base36 = strtoupper(base_convert($counter2, 10, 36));
        $match1 = 0 === strpos($generated, $counter1Base36);
        $match2 = 0 === strpos($generated, $counter2Base36);

        $this->assertTrue($match1 xor $match2);
        $this->assertEquals(Utils::TEST_ORDER_PREFIX_LENGTH, strlen($generated));
    }

}
