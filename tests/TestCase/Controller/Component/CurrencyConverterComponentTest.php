<?php
namespace App\Test\TestCase\Controller\Component;

use CurrencyConverter\Controller\Component\CurrencyConverterComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;

class CurrencyConverterComponentTest extends TestCase {
    public $fixtures = ['app.currencyconverter'];
    public $CurrencyConverter = null;
    public $controller = null;

    public function setUp() {
        parent::setUp();

        $this->CurrencyConverter = new CurrencyConverterComponent(new ComponentRegistry(new Controller));
    }

    public function testAmountWithComma() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 0;
        $hourDifference = 0;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
            
        $this->assertGreaterThan((float)$result, (float)$amount);
    }

    public function testAmountWithPoint() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 0;
        $hourDifference = 0;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithCommaSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPointSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function tearDown()
    {
        parent::tearDown();
        // Clean up after we're done
        unset($this->CurrencyConverter, $this->controller);
    }
}