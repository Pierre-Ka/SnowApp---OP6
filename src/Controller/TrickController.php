<?php

namespace App\Controller;

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

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC'], 12);
        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'tricks' => $tricks,
            'isIndex' => true,
        ]);
    }

    /*
    Redirection :
    // generating a URL with a fragment (/all_tricks#tricks)
        $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
    */

    #[Route('/all_tricks', name: 'app_all_tricks', methods: ['GET'])]
    public function list(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC']);
        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'tricks' => $tricks,
            'isIndex' => false,
        ]);
    }

//    #[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode: 404)]
//    #[Route('/create', name: 'app_trick_create', methods: ['GET', 'POST'])]
//    public function create(Request $request, TrickRepository $trickRepository): Response
//    {
//        $trick = new Trick();
//        $form = $this->createForm(TrickType::class, $trick);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $trick->setSlug();
//            if (($form['setPicture'])->getData() !== null) {
//                $extension = $form['setPicture']->getData()->guessExtension();
//                $setFileName = $trick->getSlug() . '_MAIN_' . rand(1, 999) . '.' . $extension;
//                $form['setPicture']->getData()->move('../public/uploads/main/', $setFileName);
//                $trick->setMainPicture($setFileName);
//            }
//            $user = $this->getUser();
//            $trick->setUser($user);
//            $trickRepository->add($trick);
//            $this->addFlash('success', 'Figure créée avec succès');
//            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
//        }
//        return $this->renderForm('trick/new.html.twig', [
//            'trick' => $trick,
//            'form' => $form,
//        ]);
//    }

//    #[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode: 404)]
//    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
//    {
//        $form = $this->createForm(TrickType::class, $trick);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $trick->setSlug();
//            if (($form['setMainPicture'])->getData() !== null) {
//                $extension = $form['setMainPicture']->getData()->guessExtension();
//                $files = $form['setMainPicture']->getData();
//                if ($trick->getMainPicture()) {
//                    $files->move('../public/uploads/main/', $trick->getMainPicture());
//                } else {
//                    $setFileName = $trick->getSlug() . '_MAIN_' . rand(1, 999) . '.' . $extension;
//                    $trick->setMainPicture($setFileName);
//                    $files->move('../public/uploads/main/', $trick->getMainPicture());
//                }
//            }
//            $trickRepository->add($trick);
//            $this->addFlash('success', 'Figure modifiée avec succès');
//            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug(), 'page' => 1]);
//        }
//        return $this->renderForm('trick/edit.html.twig', [
//            'trick' => $trick,
//            'form' => $form,
//        ]);
//    }

    #[Route('/{slug}/{page<[0-9]+>}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, ?int $page): Response
    {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $user = $this->getUser();
            $comment->setUser($user);
            $commentRepository->add($comment);
            $this->addFlash('success', 'Commentaire rajouté avec succès');
        }

        if ($trick->getMainPicture()) {
            $pictureName = preg_replace('/\'/', '\\\'', $trick->getMainPicture());
        } else {
            $pictureName = false;
        }

        $maxPage = $commentRepository->totalPaginationPages($trick);
        $actualPage = $page ?? 1;
        if (0 > $actualPage || $maxPage < $actualPage) {
            $actualPage = 1;
        }
        $comments = $commentRepository->findBy(
            ['trick' => $trick->getId()],
            ['createDate' => 'DESC'],
            4,
            4 * ($actualPage - 1)
        );
        return $this->render('trick/show.html.twig', [
            'pictureName' => $pictureName,
            'trick' => $trick,
            'comments' => $comments,
            'max_page' => $maxPage,
            'actual_page' => $actualPage,
            'form' => $form->createView(),
        ]);
    }

//    #[Security("is_granted('ROLE_USER') && user.getIsVerified() === true", message: 'Page Introuvable', statusCode: 404)]
//    #[Route('/{id<[0-9]+>}', name: 'app_trick_delete', methods: ['POST'])]
//    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
//            $fileName = $trick->getMainPicture();
//            $trickRepository->remove($trick);
//            if ($fileName !== null) {
//                unlink('../public/uploads/main/' . $fileName);
//            }
//            $this->addFlash('info', 'La figure a été supprimée avec succès');
//        }
//        return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
//    }
}
