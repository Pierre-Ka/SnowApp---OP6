<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ?UserPasswordHasherInterface $encoder;
    public function __construct(?UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $trick_array = [];
        $group_array = [];

        $category1 = new Category() ;
        $category1
            ->setName('Straight airs');
        $group_array[] = $category1;
        $manager->persist($category1);
        $manager->flush();

        $category2 = new Category() ;
        $category2
            ->setName('Grabs');
        $group_array[] = $category2;
        $manager->persist($category2);
        $manager->flush();

        $category3 = new Category() ;
        $category3
            ->setName('Spins');
        $group_array[] = $category3;
        $manager->persist($category3);
        $manager->flush();

        $category4 = new Category() ;
        $category4
            ->setName('Flips');
        $group_array[] = $category4;
        $manager->persist($category4);
        $manager->flush();

        $category5 = new Category() ;
        $category5
            ->setName('Slides');
        $group_array[] = $category5;
        $manager->persist($category5);
        $manager->flush();

        $category6 = new Category() ;
        $category6
            ->setName('Stalls');
        $group_array[] = $category6;
        $manager->persist($category6);
        $manager->flush();

        $category7 = new Category() ;
        $category7
            ->setName('Tweak');
        $group_array[] = $category7;
        $manager->persist($category7);
        $manager->flush();

        $category8 = new Category() ;
        $category8
            ->setName('Autres');
        $group_array[] = $category8;
        $manager->persist($category8);
        $manager->flush();



        for ( $i=0; $i<=100; $i++ )
        {
            shuffle($group_array);
            $trick = new Trick() ;
            $trick
                ->setName($faker->sentence(3))
                ->setDescription($faker->sentence(40))
                ->setCategory($group_array[0])
                ->setLevel($faker->numberBetween(1,5))
                ->setCreateDate($faker->dateTimeThisDecade());

            $trick_array[] = $trick;

            $manager->persist($trick);
            $manager->flush();
        }

        for ( $i=0; $i<=1000; $i++ )
        {
            $body= '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>';
            shuffle($trick_array);
            $comment = new Comment() ;
            $comment
                ->setTrick($trick_array[0])
                ->setContent($body)
                ->setCreateDate($faker->dateTimeThisDecade());

            $manager->persist($comment);
            $manager->flush();
        }

    }
}

/*
				'{$faker->email()}',
				'{$faker->password()}',
				'{$faker->name()}',
				'{$faker->sentence(15)}',
				'{$faker->date()}',
        $body= '<p>' . implode('</p><p>', $this->faker->paragraphs(20)) . '</p>';
				'{$this->faker->sentence(6)}',
				'{$this->faker->numberBetween(1,8)}',
				'{$this->faker->numberBetween(1,5)}',
				'{$this->faker->sentence(15)}',
				'$body',
				'{$this->faker->date()}')
    $password = $this->>encoder->hashPassword($user, 'secret');
    $user->setPassword($password) ;
*/
