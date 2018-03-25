<?php

use Faker\Generator as Faker;

$logos = [
    'https://images-platform.99static.com/wABi1iPlaT-1DXeEzQWTf6H5JWY=/0x0:960x960/500x500/top/smart/99designs-contests-attachments/81/81493/attachment_81493304',
    'https://images-platform.99static.com/Vm45O2ZHw5fCsYdXyhijfSy3Oko=/0x0:960x960/500x500/top/smart/99designs-contests-attachments/81/81505/attachment_81505886',
    'https://images-platform.99static.com/4uXeSKWH4TVDBWrmohm2ZcATVN8=/257x0:1257x1000/500x500/top/smart/99designs-contests-attachments/81/81513/attachment_81513780',
    'https://images-platform.99static.com/UgdKn4A3JITJS6oEtJFkn5S8bA0=/0x0:1299x1299/500x500/top/smart/99designs-contests-attachments/81/81505/attachment_81505034',
    'https://images-platform.99static.com/-5-XtbwtBDwPCWz8kBBd7_zLqx8=/12x2:1035x1025/500x500/top/smart/99designs-contests-attachments/81/81523/attachment_81523973',
    'https://images-platform.99static.com/R8NlQBSLOYrxe3nYLzRj7aK6uSE=/0x0:1720x1720/500x500/top/smart/99designs-contests-attachments/81/81474/attachment_81474447',
    'https://images-platform.99static.com/o-2mhlucRunRv5Hnjm-MrGq1Bd8=/0x0:1875x1875/500x500/top/smart/99designs-contests-attachments/81/81445/attachment_81445716',
    'http://content.sportslogos.net/news/2017/12/TampaTarpons-Header.jpg',
    'https://i.pinimg.com/564x/3d/5b/8d/3d5b8d62e3f3e1242c1b1daeb73a38ac.jpg',
    'https://i.pinimg.com/564x/db/e0/a6/dbe0a6440a74b8456e6cb45bd56b6604.jpg',
    'https://i.pinimg.com/564x/8d/23/c4/8d23c4a3ab64796ca63bc3e9d69da742.jpg',
];

$factory->define(App\Team::class, function (Faker $faker) use ($logos) {
    return [
        'name' => $faker->company,
        'short' => $faker->companySuffix,
        'logo' => $faker->randomElement($logos),
        'created_by' => 4,
        'created_by_name' => 'team.manager',
    ];
});
