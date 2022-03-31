<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Entity\Trick;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode:404)]
class VideoController extends AbstractController
{
    #[Route('/video/create/{id<[0-9]+>}', name: 'app_video_create', methods: ['GET', 'POST'])]
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
            $slug = $video->getTrick()->getSlug();
            return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('video/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/video/{id<[0-9]+>}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoPath = preg_replace('#watch\?v=#' ,  'embed/', $video->getPath());
            $video->setPath($videoPath);
            $videoRepository->add($video);
            $this->addFlash('success', 'Video modifiée avec succès');
            $slug = $video->getTrick()->getSlug();
            return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/video/{id<[0-9]+>}/delete', name: 'app_video_delete', methods: ['POST']) ]
    public function delete(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
            $slug = $video->getTrick()->getSlug();
            $videoRepository->remove($video);
            $this->addFlash('info', 'Video supprimée avec succès');
        }
        return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
    }
}
