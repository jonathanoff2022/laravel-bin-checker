<?php

use BinChecker\BinChecker;
use PHPUnit\Framework\TestCase;

final class BinCheckerTest extends TestCase
{
    public function testNatixisGold(): void
    {
        $bin = '499010';
        $result = BinChecker::checkBin($bin);
        $this->assertEquals($bin, $result['bin'], 'Invalid bin received');
        $this->assertEquals('NATIXIS', $result['bank'], 'Invalid bank received');
        $this->assertEquals('GOLD', $result['level'], 'Invalid level received');
        $this->assertEquals('FR', $result['country'], 'Invalid country received');
    }

    public function testLaBanquePostaleClassic(): void
    {
        $bin = '497040';
        $result = BinChecker::checkBin($bin);
        $this->assertEquals($bin, $result['bin'], 'Invalid bin received');
        $this->assertEquals('LA BANQUE POSTALE', $result['bank'], 'Invalid bank received');
        $this->assertEquals('CLASSIC', $result['level'], 'Invalid level received');
        $this->assertEquals('FR', $result['country'], 'Invalid country received');
    }
}
