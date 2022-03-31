<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Straight airs',
        'Grabs',
        'Spins',
        'Flips',
        'Slides',
        'Stalls',
        'Tweak',
        'Autres',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $key => $CATEGORY) {
            $category = new Category();
            $manager->persist($category);
            $this->addReference('category_'.$key, $category);
        }
        $manager->flush();
    }
}

