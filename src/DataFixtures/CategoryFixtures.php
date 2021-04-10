<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            ['Books', '#ed2d2d'],
            ['Cinema', '#e88127'],
            ['Food', '#f5dd05'],
            ['Politics', '#14b82f'],
            ['Travels', '#00a6e3'],
        ];

        foreach ($categories as $category)
            $manager->persist((new Category())->setName($category[0])->setColor($category[1]));
        
        $manager->flush();
    }
}
