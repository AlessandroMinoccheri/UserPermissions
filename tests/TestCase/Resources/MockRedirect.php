<?php
namespace App\Test\TestCase\Resources;

class MockRedirect
{
	public $passedData = null;
	
	public function redirect($data) {
		$this->passedData = $data;
	}
}
