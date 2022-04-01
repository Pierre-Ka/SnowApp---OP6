<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode: 404)]
class TrickController extends AbstractController
{
    #[Route('/create', name: 'app_trick_create', methods: ['GET', 'POST'])]
    public function create(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug();
            if (($form['setPicture'])->getData() !== null) {
                $extension = $form['setPicture']->getData()->guessExtension();
                $setFileName = $trick->getSlug() . '_MAIN_' . rand(1, 999) . '.' . $extension;
                $form['setPicture']->getData()->move('../public/uploads/main/', $setFileName);
                $trick->setMainPicture($setFileName);
            }
            $user = $this->getUser();
            $trick->setUser($user);
            $trickRepository->add($trick);
            $this->addFlash('success', 'Figure créée avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug();
            if (($form['setMainPicture'])->getData() !== null) {
                $extension = $form['setMainPicture']->getData()->guessExtension();
                $files = $form['setMainPicture']->getData();
                if ($trick->getMainPicture()) {
                    $files->move('../public/uploads/main/', $trick->getMainPicture());
                } else {
                    $setFileName = $trick->getSlug() . '_MAIN_' . rand(1, 999) . '.' . $extension;
                    $trick->setMainPicture($setFileName);
                    $files->move('../public/uploads/main/', $trick->getMainPicture());
                }
            }
            $trickRepository->add($trick);
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug(), 'page' => 1]);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $fileName = $trick->getMainPicture();
            $trickRepository->remove($trick);
            if ($fileName !== null) {
                unlink('../public/uploads/main/' . $fileName);
            }
            $this->addFlash('info', 'La figure a été supprimée avec succès');
        }
        return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
    }
}
