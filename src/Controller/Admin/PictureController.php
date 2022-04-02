<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Form\PictureType;
use App\Manager\PictureManager;
use App\Repository\PictureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode:404)]
class PictureController extends AbstractController
{
    #[Route('/picture/create/{id<[0-9]+>}', name: 'app_picture_create', methods: ['GET', 'POST'])]
    public function create(Request $request, Trick $trick, PictureManager $pictureManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form['setCollectionPicture']->getData();
            $pictureManager->create($formData, $picture, $trick);
            $this->addFlash('success', 'Image ajoutée avec succès');
            $slug = $picture->getTrick()->getSlug();
            return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('picture/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/picture/{id<[0-9]+>}/edit', name: 'app_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, PictureManager $pictureManager): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form['setCollectionPicture']->getData();
            $pictureManager->edit($formData, $picture);
            $this->addFlash('success', 'Image modifiée avec succès');
            $slug = $picture->getTrick()->getSlug();
            return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/picture/{id<[0-9]+>}/delete', name: 'app_picture_delete', methods: ['POST']) ]
    public function delete(Request $request, Picture $picture, PictureManager $pictureManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $slug = $picture->getTrick()->getSlug();
            $pictureManager->delete($picture);
            $this->addFlash('info', 'Image supprimée avec succès');
        }
        return $this->redirectToRoute('app_trick_show', ['slug'=> $slug,'page'=> 1], Response::HTTP_SEE_OTHER);
    }
}
