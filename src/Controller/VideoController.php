<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    #[Route('/video/create/{id}', name: 'app_video_create', methods: ['GET', 'POST'])]
    public function create(Request $request, Trick $trick, VideoRepository $videoRepository): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setTrick($trick);
            $videoPath = preg_replace('#watch\?v=#' ,  'embed/', $video->getPath());
            $video->setPath($videoPath);
            $videoRepository->add($video);
            $this->addFlash('success', 'Video ajoutée avec succès');
            $id = $video->getTrick()->getId();
            return $this->redirectToRoute('app_trick_show', ['id'=> $id,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('video/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/video/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoPath = preg_replace('#watch\?v=#' ,  'embed/', $video->getPath());
            $video->setPath($videoPath);
            $videoRepository->add($video);
            $this->addFlash('success', 'Video modifiée avec succès');
            $id = $video->getTrick()->getId();
            return $this->redirectToRoute('app_trick_show', ['id'=> $id,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST']) ]
    public function delete(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
            $videoRepository->remove($video);
            $this->addFlash('info', 'Video supprimée avec succès');
        }
        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
