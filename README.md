# Solution

One simple class to manage basket operations, and loading input data.

## Assumptions
Wwe are loading data coming from JSON sources - for the purposes of this exercise from JSON files located in `data` folder. 

*Products* are super simple, unique ID, name & price as a float numbrer

```
{
    "id": "R01",
    "name": "Red Widget",
    "price": 32.95
}
```

*Delivery* rules are described in ascending (brackets) order in another JSON file `deliveryRules.json`:
```
[   {
        "amountMax": 50,
        "cost": 4.95
    },
    {
        "amountMin": 50,
        "amountMax": 90,
        "cost": 2.95
    },
    {
        "amountMin": 90,
        "cost": 0
    }
]
```

*Offer rules* are in `promoRules.json` file:

```
{
    "desc" : "buy one red widget, get the second half price",
    "productId": "R01",
    "priceFactor": 0.5,
    "qty" : 1
}
```

To get actual efficiency we have to do the math inside PHP code. So in production environment we do have set of possible types of offers (fe: get x-amount and get, y-discount on next product, reach x-amount and get y discount on others and so on)

To be more flexible we could:
1. allow users to define calc functions in JSON (source) file and use sth. like EVAL to execute calc (not very fast, but supper flexible, no code changes are required if new promo comes in)
2. work out some kind of parser, to use Excel formulas or other query language
3. offload promotions to separate microservice - bit of overkill, but I've seen that done that in bigger e-commerce solutions. 

## code structure

Source code is in `src/Basket.php` tests are available by running `./vendor/bin/phpunit` or with code coverage `--coverage-text . --whitelist=./src --no-configuration tests`.

## 

## final comment

I found small bug, not sure if intended but price of `R01` product is 32.95 if we give 50% discount price goes to `16.475` - if we round this up it is `16.48` - so `2 x R01` with promo offer goes to `54.38` with shipping not `54.37`. To match given in example values, we had to use `floor` to round up numbers instead of regular round. 

# TASK: Acme Widget Co 

Acme Widget Co are the leading provider of made up widgets and they’ve contracted you to create a proof of concept for their new sales system. 

They sell three products 

|Product|Code|Price
|---|---|---|
|Red Widget|R01|$32.95|
|Green Widget|G01|$24.95|
|Blue Widget|B01|$7.95|

To incentivise customers to spend more, delivery costs are reduced based on the amount spent. Orders under $50 cost $4.95. For orders under $90, delivery costs $2.95. Orders of $90 or more have free delivery. 

They are also experimenting with special offers. The initial offer will be “buy one red widget, get the second half price”. 

Your job is to implement the basket which needs to have the following interface –
* It is initialised with the product catalogue, delivery charge rules, and offers (the format of how these are passed it up to you) 
* It has an add method that takes the product code as a parameter. 
* It has a total method that returns the total cost of the basket, taking into account the delivery and offer rules.

Here are some example baskets and expected totals to help you check your implementation. 

|Products|Total|
|---|---|
|B01, G01|$37.85|
|R01, R01|$54.37|
|R01, G01|$60.85|
|B01, B01, R01, R01, R01|$98.27|

What we expect to see –
* A solution written in easy to understand and update PHP 
* A README file explaining how it works and any assumptions you’ve made 
* Pushed to a public Github repo