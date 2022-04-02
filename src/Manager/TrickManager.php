<?php

namespace App\Manager;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickManager
{
    private TrickRepository $trickRepository ;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    public function create (Trick $trick, UserInterface $user = null)
    {
        if($user)
        {
            $trick->setUser($user);
        }
        $this->trickRepository->add($trick);
    }

    public function defineMainPicture($formData, Trick $trick)
    {
        $extension = $formData->guessExtension();
        if ($trick->getMainPicture()) {
            $formData->move('../public/uploads/main/', $trick->getMainPicture());
        } else {
            $setFileName = $trick->getSlug() . '_MAIN_' . rand(1, 999) . '.' . $extension;
            $trick->setMainPicture($setFileName);
            $formData->move('../public/uploads/main/', $trick->getMainPicture());
        }
    }

    public function delete(Trick $trick)
    {
        $fileName = $trick->getMainPicture();
        $this->trickRepository->remove($trick);
        if ($fileName !== null) {
            unlink('../public/uploads/main/' . $fileName);
        }
    }
}