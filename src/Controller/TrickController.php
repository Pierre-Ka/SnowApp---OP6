<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC' ], 12);
        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'tricks' => $tricks,
            'isIndex' => true,
        ]);
    }

    #[Route('/all_tricks', name: 'app_all_tricks', methods: ['GET'])]
    public function list(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC' ]);
        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'tricks' => $tricks,
            'isIndex' => false,
        ]);
    }

    #[Route('/create', name: 'app_trick_create', methods: ['GET', 'POST'])]
    public function create(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick);
            $id = $trick->getId();
            $this->addFlash('success', 'Figure créée avec succès');
            return $this->redirectToRoute('app_trick_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick);
            $id = $trick->getId();
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('app_trick_show', ['id'=> $id,'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{page}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, ?int $page): Response
    {
        $comment = new Comment ;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $commentRepository->add($comment);
        }

        $maxPage = $commentRepository->totalPaginationPages($trick);
        $actualPage = $page ?? 1;
        if (0 > $actualPage || $maxPage < $actualPage) {
            $actualPage = 1;
        }
        $comments = $commentRepository->findBy(
            ['trick' => $trick->getId()],
            ['createDate' => 'DESC' ],
            4,
            4 * ($actualPage - 1)
        );
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'max_page' => $maxPage,
            'actual_page' => $actualPage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: ['POST']) ]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick);
            $this->addFlash('info', 'Pin successfully deleted');
        }
        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
