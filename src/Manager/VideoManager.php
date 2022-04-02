<?php

namespace App\Manager;

use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoManager
{
    private VideoRepository $videoRepository ;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function edit(Video $video, Trick $trick = null)
    {
        if($trick)
        {
            $video->setTrick($trick);
        }
        $videoPath = preg_replace('#watch\?v=#' ,  'embed/', $video->getPath());
        $video->setPath($videoPath);
        $this->videoRepository->add($video);
    }

    public function delete(Video $video)
    {
        $this->videoRepository->remove($video);
    }
}