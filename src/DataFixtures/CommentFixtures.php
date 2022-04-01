<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= 1000; $i++) {

            dd($this->getReference('trick_2'));
            //$trickKey = rand(0, (count(TrickFixtures::TRICKS) - 1);


            /** @var Trick $trick */
            $trick = $this->getReference('trick_' . $trickKey);

            $userKey = rand(0, count(UserFixtures::USERS) - 1);
            /** @var User $user */
            $user = $this->getReference('user_' . $userKey);

            $body = '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>';
            $comment = new Comment();
            $comment
                ->setTrick($trick)
                ->setContent($body)
                ->setUser($user)
                ->setCreateDate($faker->dateTimeThisDecade());

            $manager->persist($comment);
            $manager->flush();
        }

    }

    public function getDependencies(): array
    {
        return [
            TrickFixtures::class,
            UserFixtures::class,
        ];
    }
}
