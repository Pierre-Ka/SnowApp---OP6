<?php

namespace App\Manager;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\PictureRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class PictureManager
{
    private PictureRepository $pictureRepository ;

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    public function create($formData, Picture $picture, Trick $trick) : void
    {
        $extension = $formData->guessExtension();
        $setFileName = $trick->getSlug() . '_COLLECTION_' . rand(1, 99999) . '.' . $extension;
        $formData->move('../public/uploads/pictures/', $setFileName);
        $picture->setTrick($trick);
        $picture->setPath($setFileName);
        $this->pictureRepository->add($picture);
    }

    public function edit($formData, Picture $picture) : void
    {
        $formData->move('../public/uploads/pictures/', $picture->getPath());
    }

    public function delete($picture) : void
    {
        $picturePath = $picture->getPath();
        $this->pictureRepository->remove($picture);
        unlink('../public/uploads/pictures/'.$picturePath);
    }
}