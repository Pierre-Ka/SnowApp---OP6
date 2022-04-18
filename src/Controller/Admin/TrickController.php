<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickAddType;
use App\Form\TrickEditType;
use App\Manager\PictureManager;
use App\Manager\TrickManager;
use App\Manager\VideoManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode: 404)]
class TrickController extends AbstractController
{
    #[Route('/create', name: 'app_trick_create', methods: ['GET', 'POST'])]
    public function create(Request $request, TrickManager $trickManager, VideoManager $videoManager, PictureManager $pictureManager): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickAddType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug();
            if (($form['setMainPicture'])->getData() !== null) {
                $formData = $form['setMainPicture']->getData();
                $trickManager->defineMainPicture($formData, $trick);
            }
            $user = $this->getUser();
            $trickManager->create($trick, $user);
            if (($form['videos'])->getData() !== null) {
                foreach ($form->get('videos') as $data) {
                    if ($data['path']->getData() !== null) {
                        $video = new Video();
                        $video->setPath($data['path']->getData());
                        $videoManager->edit($video, $trick);
                    }
                }
            }
            $pictures = $form->get('pictures') ?? null;
            if ($pictures) {
                foreach ($pictures as $dataPicture) {
                    $formData = $dataPicture['setCollectionPicture']->getData();
                    $picture = new Picture();
                    $pictureManager->create($formData, $picture, $trick);
                }
            }
            $this->addFlash('success', 'Figure créée avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickManager $trickManager): Response
    {
        $form = $this->createForm(TrickEditType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug();
            if (($form['setMainPicture'])->getData() !== null) {
                $formData = $form['setMainPicture']->getData();
                $trickManager->defineMainPicture($formData, $trick);
            }
            $trickManager->create($trick);
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug(), 'page' => 1]);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickManager $trickManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickManager->delete($trick);
            $this->addFlash('info', 'La figure a été supprimée avec succès');
        }
        return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
    }
}
