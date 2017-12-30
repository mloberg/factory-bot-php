<?php

use Mlo\FactoryBot\Facade as Factory;
use Faker\Generator as Faker;

Factory::define(\Mlo\FactoryBot\Test\Model\User::class, function (Faker $faker) {
    return [
        'username' => $faker->unique()->userName,
        'password' => password_hash('foobar', PASSWORD_BCRYPT),
        'email' => $faker->unique()->safeEmail
    ];
});

Factory::state(\Mlo\FactoryBot\Test\Model\User::class, 'name', function (Faker $faker) {
    return [
        'name.first' => $faker->firstName,
        'name.last' => $faker->lastName,
    ];
});
