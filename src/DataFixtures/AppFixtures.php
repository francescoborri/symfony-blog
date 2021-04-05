<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 50; $i++) {
            $blog = new Post();
            $blog->setTitle("Post $i");
            $blog->setShortDescription("Short description for post $i");
            $blog->setBody("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec massa leo. Cras laoreet lobortis orci, ac pharetra diam sollicitudin posuere. Ut suscipit lorem eu nisi fringilla, sed pulvinar est suscipit. Etiam semper tincidunt justo, malesuada suscipit sapien tristique vel. Sed quam justo, porttitor at eros eget, aliquet ultrices massa. Fusce posuere, libero egestas vestibulum congue, elit magna feugiat orci, eu aliquet ex turpis id nibh. Praesent egestas iaculis mollis. Sed eget dignissim metus, vitae luctus turpis. Duis gravida lacus eu ipsum cursus, at porta dui faucibus.");
            $blog->setImage(null);
            $manager->persist($blog);
        }

        $manager->flush();
    }
}
