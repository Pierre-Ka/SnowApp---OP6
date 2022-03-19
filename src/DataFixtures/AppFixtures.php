<?php

namespace App\DataFixtures;

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
        for ( $i=0; $i<=50; $i++ )
        {
            $trick = new Trick() ;
            $trick
                ->setName($faker->sentence(4))
                ->setDescription($faker->sentence(35))
                ->setLevel($faker->numberBetween(1,5))
                ->setCreateDate($faker->dateTimeThisDecade());

            $trick_array[] = $trick;

            $manager->persist($trick);
            $manager->flush();
        }

        for ( $i=0; $i<=250; $i++ )
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
