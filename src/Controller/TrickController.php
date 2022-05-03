<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Manager\CommentManager;
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
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC'], 12);
        $tricksCount = $trickRepository->count([]);
        $pageCount = round($tricksCount / 12);

        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'page' => 1,
            'tricks' => $tricks,
            'is_index' => true,
            'page_count' => $pageCount,
        ]);
    }

    #[Route('/reload_tricks/{page}')]
    public function listReload(TrickRepository $trickRepository, ?int $page): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC'], 12, ($page-1) * 12);

        return $this->render('partials/index/_list.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /*
        Redirection :  // generating a URL with a fragment (/all_tricks#tricks)
        $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
    */

    #[Route('/all_tricks', name: 'app_all_tricks', methods: ['GET'])]
    public function list(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findBy([], ['createDate' => 'DESC']);

        return $this->render('trick/index.html.twig', [
            'all_tricks' => $trickRepository->findAll(),
            'tricks' => $tricks,
            'is_index' => false,
        ]);
    }

    #[Route('/{slug}/{page<[0-9]+>}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, CommentManager $commentManager, ?int $page): Response
    {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
            $user = $this->getUser();
            $commentManager->create($comment, $trick, $user);
            $this->addFlash('success', 'Commentaire rajouté avec succès');
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
            'trick' => $trick,
            'comments' => $comments,
            'max_page' => $maxPage,
            'actual_page' => $actualPage,
            'form' => $form->createView(),
        ]);
    }
}
