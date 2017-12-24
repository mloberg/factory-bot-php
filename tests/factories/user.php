<?php

use Faker\Generator as Faker;

/** @var \Mlo\FactoryBot\Factory $factory */
$factory->define(\Mlo\FactoryBot\Test\Model\User::class, function (Faker $faker) {
    return [
        'username' => $faker->unique()->userName,
        'password' => password_hash('foobar', PASSWORD_BCRYPT),
        'email' => $faker->unique()->safeEmail
    ];
});

$factory->state(\Mlo\FactoryBot\Test\Model\User::class, 'name', function (Faker $faker) {
    return [
        'name.first' => $faker->firstName,
        'name.last' => $faker->lastName,
    ];
});
