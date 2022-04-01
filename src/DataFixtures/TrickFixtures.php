<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/*  DOCUMENTATION OFFICIELLE SYMFONY ************************* :
Splitting Fixtures into Separate Files. In most applications, creating all your fixtures in just one class is fine.
This class may end up being a bit long, but it's worth it because having one file helps keeping things simple.
If you do decide to split your fixtures into separate files, Symfony helps you solve the two most common issues:
sharing objects between fixtures and loading the fixtures in order. Sharing Objects between Fixtures. When using
multiple fixtures files, you can reuse PHP objects across different files thanks to the object references.
Use the addReference() method to give a name to any object and then, use the getReference() method to get the exact
same object via its name:

src/DataFixtures/UserFixtures.php

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User('admin', 'pass_1234');
        $manager->persist($userAdmin);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }
}
/************* IMPLEMENTATION DANS MA CLASSE ****************************************/

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRICKS = [];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i <= 100; $i++) {
            $categoryKey = rand(0, count(CategoryFixtures::CATEGORIES) - 1);
            /** @var Category $category */
            $category = $this->getReference('category_' . $categoryKey);

            $userKey = rand(0, count(UserFixtures::USERS) - 1);
            /** @var User $user */
            $user = $this->getReference('user_' . $userKey);

            $trick = new Trick();
            $trick->setName($faker->sentence(3));
            $trick->setDescription($faker->sentence(40));
            $trick->setCategory($category);
            $trick->setUser($user);
            $trick->setSlug();
            $trick->setLevel($faker->numberBetween(1, 5));
            $trick->setCreateDate($faker->dateTimeThisDecade());
            $manager->persist($trick);
            $this->addReference(self::TRICKS, $trick);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}

/* ************* Ici nous avons :
        $this->addReference(self::TRICKS, $trick);
         ====> Illegal offset type in isset or empty

         $this->addReference(self::TRICKS[], $trick);
         ====> Cannot use [] for reading

         $this->addReference(self::TRICKS['trick_'.$i], $trick);
         ====> Warning: Undefined array key "trick_0"

****************************** ARRET DE L'essai de réécriture de fixtures ici ***********************






 */

