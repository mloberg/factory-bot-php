<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

use Faker\Generator as Faker;

/** @var \Mlo\FactoryBot\Factory $factory */
$factory->define(\Mlo\FactoryBot\Test\Model\Bar::class, function (Faker $faker) {
    return [
        'foo' => $faker->word,
        'bar' => $faker->word,
        'baz' => $faker->word,
    ];
});
