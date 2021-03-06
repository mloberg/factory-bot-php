<?php

use Faker\Generator as Faker;

/** @var \Mlo\FactoryBot\Factory $factory */
$factory->define(\Mlo\FactoryBot\Test\Model\Foo::class, function (Faker $faker) {
    return [
        'foo' => $faker->word,
        'bar' => $faker->word,
        'baz' => $faker->word,
    ];
});
