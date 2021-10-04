<?php

namespace slav\architech;

/**
 *
 */
class Basket {

	/**
	 * Products array
	 * @var array
	 */
	public $products = [];

	/**
	 * Delivery rules array
	 * @var array
	 */
	public $rules = [];

	/**
	 * Products in the basket
	 * @var array
	 */
	public $items = [];

	/**
	 * Promo rules array
	 * @var array
	 */
	public $promos = [];

	/**
	 * load all products into assoc array
	 *
	 * @param string $fileName
	 *
	 * @return bool
	 */
	public function LoadProducts(string $fileName): bool
	{

		$result = file_get_contents($fileName);

		if ($result === FALSE)
		{
			return FALSE;
		}

		if ($jsonArray = json_decode($result, TRUE))
		{
			$this->makeAssoc($jsonArray, 'id');

			$this->products = $jsonArray;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * helper function - make assoc array from any array, by primary key
	 *
	 * @param array  $inputArray
	 * @param string $primaryKey
	 *
	 */
	private function makeAssoc(array &$inputArray, string $primaryKey)
	{

		$keys = array_column($inputArray, $primaryKey);

		$inputArray = array_combine($keys, $inputArray);

		ksort($inputArray);

	}

	/**
	 * Load delivery rules
	 *
	 * @param string $fileName
	 *
	 * @return bool
	 *
	 */
	public function LoadDeliveryRules(string $fileName): bool
	{
		$result = file_get_contents($fileName);

		if ($result === FALSE)
		{
			return FALSE;
		}

		if ($jsonArray = json_decode($result, TRUE))
		{
			$this->rules = $jsonArray;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Calculates delivery amount based on order amound
	 *
	 * @param float $orderAmount
	 *
	 * @return float|null
	 */
	public function CalcDeliveryAmount(float $orderAmount): ?float
	{

		if (is_null($this->rules))
		{
			return NULL;
		}

		for ($a = 0, $max = count($this->rules); $a < $max; $a ++)
		{
			if ( ! isset($this->rules[$a]['amountMin']) && isset($this->rules[$a]['amountMax']))
			{
				if ($orderAmount < $this->rules[$a]['amountMax'])
				{
					return $this->rules[$a]['cost'];
				}
			}

			if (isset($this->rules[$a]['amountMin']) && isset($this->rules[$a]['amountMax']))
			{
				if ($orderAmount >= $this->rules[$a]['amountMin'] && $orderAmount < $this->rules[$a]['amountMax'])
				{
					return $this->rules[$a]['cost'];
				}
			}

			if (isset($this->rules[$a]['amountMin']) && ! isset($this->rules[$a]['amountMax']))
			{
				if ($orderAmount >= $this->rules[$a]['amountMin'])
				{
					return $this->rules[$a]['cost'];
				}
			}
		}

		return NULL;
	}


	/**
	 * Add product to basket
	 *
	 * @param string $productId
	 */
	public function Add(string $productId)
	{
		if (key_exists($productId, $this->items))
		{
			$this->items[$productId] ++;
		}
		else
		{
			$this->items[$productId] = 1;
		}
	}

	/**
	 * Clear basket
	 */
	public function Empty()
	{
		$this->items = [];
	}

	/**
	 * Load promo rules
	 *
	 * @param string $fileName
	 *
	 * @return bool
	 */
	public function LoadPromo(string $fileName): bool
	{
		$result = file_get_contents($fileName);

		if ($result === FALSE)
		{
			return FALSE;
		}

		if ($jsonArray = json_decode($result, TRUE))
		{
			$this->promos = $jsonArray;

			return TRUE;
		}

		return FALSE;
	}


	/**
	 * Calc summary of order
	 * @return float
	 */
	public function CalcSummary(): float
	{

		$summary = 0;

		$this->LoadPromo('data/promoRules.json');

		// get extra array to list all product's id with promo
		$productsWithPromo = array_column($this->promos, 'productId');

		foreach ($this->items as $productID => $qty)
		{
			// no promo, just  go with regular math
			if ( ! in_array($productID, $productsWithPromo))
			{
				$summary += $qty * $this->products[$productID]['price'];
			}
			else
			{
				$summary += $this->calcSpecialPrice($productID, $qty);
			}
		}

		$summary += $this->CalcDeliveryAmount($summary);

		return $summary;

	}

	/**
	 * calculate special price
	 *
	 * @param string  $productId
	 * @param integer $qty
	 *
	 * @return float
	 */
	public function calcSpecialPrice(string $productId, int $qty) : float {
		$total = 0;
		foreach ($this->promos as  $promo) {
			if ($promo['productId'] === $productId) {
				if ($qty > $promo['qty']) {
					$promoPriceProducts = intdiv($qty, $promo['qty'] + 1);
					$regularPriceProducts = $qty - $promoPriceProducts;

					$total += $regularPriceProducts * $this->products[$productId]['price'];
					$total += $promoPriceProducts * floor($this->products[$productId]['price'] * $promo['priceFactor']*100)/100;
				} else {
					$total += $qty * $this->products[$productId]['price'];
				}
 			}
		}
		return round($total, 2);
	}
}