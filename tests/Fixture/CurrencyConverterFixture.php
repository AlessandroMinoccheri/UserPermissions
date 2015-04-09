<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CurrencyConverterFixture extends TestFixture {
    public $connection = 'test';

    public $fields = [
        'id'        => ['type' => 'integer', 'key' => 'primary'],
        'from'      => ['type' => 'string','length' => 255, 'null' => false],
        'to'        => ['type' => 'string','length' => 255, 'null' => false],
        'rates'     => ['type' => 'string','length' => 255, 'null' => false],
        'created'   => ['type' => 'datetime'],
        'modified'  => ['type' => 'datetime'],
        '_constraints' => [
          'primary' => ['type' => 'primary', 'columns' => ['id']]
        ]
    ];
}
?>
