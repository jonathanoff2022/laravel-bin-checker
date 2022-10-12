<?php

use BinChecker\BinChecker;
use PHPUnit\Framework\TestCase;

final class BinCheckerTest extends TestCase
{
    public function testNatixis(): void
    {
        $natixisBin = '499010';
        $result = BinChecker::checkBin($natixisBin);
        $this->assertEquals('NATIXIS', $result['bank'], 'Invalid bank received');
    }
}
