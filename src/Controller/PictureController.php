<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    #[Route('/picture/create/{id}', name: 'app_picture_create', methods: ['GET', 'POST'])]
    public function create(Request $request, Trick $trick, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = new Picture();
            $extension = $form['setCollectionPicture']->getData()->guessExtension();
            if (!$extension || !in_array($extension, ["jpg", "png", "jpeg"])) {
                throw new UploadException('Seuls les formats jpg, png et jpeg sont acceptés');
            }

            $nameTrickWithoutSpace = str_replace(" ", "", $trick->getName());
            $nameTrickLower = strtolower($nameTrickWithoutSpace);
            $setFileName = $nameTrickLower.'_COLLECTION_'.rand(1, 99999).'.'.$extension ;
            $form['setCollectionPicture']->getData()->move('../public/uploads/pictures/', $setFileName);

            $picture->setTrick($trick);
            $picture->setPath($setFileName);
            $pictureRepository->add($picture);
            $this->addFlash('success', 'Image ajoutée avec succès');
            $id = $picture->getTrick()->getId();
            return $this->redirectToRoute('app_trick_show', ['id'=> $id,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('picture/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/picture/{id}/edit', name: 'app_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $extension = $form['setCollectionPicture']->getData()->guessExtension();
            if (!$extension || !in_array($extension, ["jpg", "png", "jpeg"])) {
                throw new UploadException('Seuls les formats jpg, png et jpeg sont acceptés');
            }
            $files = $form['setCollectionPicture']->getData();
            $files->move('../public/uploads/pictures/', $picture->getPath());
            $this->addFlash('success', 'Image modifiée avec succès');
            $id = $picture->getTrick()->getId();
            return $this->redirectToRoute('app_trick_show', ['id'=> $id,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/picture/{id}/delete', name: 'app_picture_delete', methods: ['POST']) ]
    public function delete(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $idTrick = $picture->getTrick()->getId();
            $pictureRepository->remove($picture);
            $this->addFlash('info', 'Image supprimée avec succès');
        }
        return $this->redirectToRoute('app_trick_show', ['id'=> $idTrick,'page'=> 1], Response::HTTP_SEE_OTHER);
    }

}
