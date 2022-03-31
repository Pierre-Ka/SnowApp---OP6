<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRICKS = [];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ( $i=0; $i<=100; $i++ )
        {
            $categoryKey = rand(0, count(CategoryFixtures::CATEGORIES) - 1);
            /** @var Category $category */
            $category = $this->getReference('category_' . $categoryKey);

            $userKey = rand(0, count(UserFixtures::USERS) - 1);
            /** @var User $user */
            $user = $this->getReference('user' . $userKey);

            $trick = new Trick();
            $trick->setName($faker->sentence(3));
            $trick->setDescription($faker->sentence(40));
            $trick->setCategory($category);
            $trick->setUser($user);
            $trick->setSlug();
            $trick->setLevel($faker->numberBetween(1,5));
            $trick->setCreateDate($faker->dateTimeThisDecade());
            $this->addReference('trick_'.$i, $trick);
            // self::TRICKS[] = $trick ;
            $manager->persist($trick);
            $manager->flush();
        }

        for ( $i=0; $i<=1000; $i++ )
        {
            $body= '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>';
            shuffle($trick_array);
            shuffle($user_array);
            $comment = new Comment() ;
            $comment
                ->setTrick($trick_array[0])
                ->setContent($body)
                ->setUser($user_array[0])
                ->setCreateDate($faker->dateTimeThisDecade());

            $manager->persist($comment);
            $manager->flush();
        }

    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}

