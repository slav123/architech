<?php

namespace slav\architech\Test;

use PHPUnit\Framework\TestCase;
use slav\architech\Basket;

final class BasketTest extends TestCase {

	protected $basket;

	public function setUp(): void {

		$this->basket = new Basket();
	}

	public function testloadProducts(): void
	{
		$result = $this->basket->LoadProducts('data/products.json');

		$this->assertTrue($result, 'success with load products');
		$this->assertIsArray($this->basket->products, 'result is array');
	}

	public function testLoadDeliveryRules(): void
	{
		$result = $this->basket->LoadDeliveryRules('data/deliveryRules.json');

		$this->assertTrue($result, 'success with rules');
		$this->assertIsArray($this->basket->rules, 'result is array');
	}

	public function testDeliveryAmount(): void {

		$this->basket->LoadDeliveryRules('data/deliveryRules.json');

		$tests = [[40, 4.95], [50, 2.95], [90, 0]];
		foreach ($tests as $test) {
			$this->assertEquals($test[1], $this->basket->CalcDeliveryAmount($test[0]), "test if {$test[0]} gives {$test[1]}");
		}

	}

	public function testCalcSummary(): void {

		$this->basket->LoadDeliveryRules('data/deliveryRules.json');
		$this->basket->LoadProducts('data/products.json');

		$this->basket->Empty();
		$this->basket->Add('B01');
		$this->basket->Add('G01');
		$result = $this->basket->CalcSummary();
		$this->assertEquals(37.85, $result);

		$this->basket->Empty();
		$this->basket->Add('R01');
		$this->basket->Add('R01');
		$result = $this->basket->CalcSummary();
		$this->assertEquals(54.37, $result);
		$this->basket->Empty();

		$this->basket->Empty();
		$this->basket->Add('R01');
		$this->basket->Add('G01');
		$result = $this->basket->CalcSummary();
		$this->assertEquals(60.85, $result);

		$this->basket->Empty();
		$this->basket->Add('B01');
		$this->basket->Add('B01');
		$this->basket->Add('R01');
		$this->basket->Add('R01');
		$this->basket->Add('R01');
		$result = $this->basket->CalcSummary();
		$this->assertEquals(98.27, $result);




	}
}
