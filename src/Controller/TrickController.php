<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\PictureType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
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
    /*
    Redirection :
    // generating a URL with a fragment (/all_tricks#tricks)
        $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks']);
    */

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
            if (($form['setPicture'])->getData() !== null)
            {
                $extension = $form['setPicture']->getData()->guessExtension();
                if (!$extension || !in_array($extension, ["jpg", "png", "jpeg"])) {
                    throw new UploadException('Seuls les formats jpg, png et jpeg sont acceptés');
                }

                $nameTrickWithoutSpace = str_replace(" ", "", $trick->getName());
                $nameTrickLower = strtolower($nameTrickWithoutSpace);
                $setFileName = $nameTrickLower.'_MAIN_'.rand(1, 999).'.'.$extension ;
                $form['setPicture']->getData()->move('../public/uploads/main/', $setFileName);
                $trick->setMainPicture($setFileName);
                $user = $this->getUser();
                $trick->setUser($user);
            }
            $trickRepository->add($trick);
            $this->addFlash('success', 'Figure créée avec succès');
            return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($form['setPicture'])->getData() !== null)
            {
                $extension = $form['setPicture']->getData()->guessExtension();
                if (!$extension || !in_array($extension, ["jpg", "png", "jpeg"])) {
                    throw new UploadException('Seuls les formats jpg, png et jpeg sont acceptés');
                }
                if ($trick->getMainPicture() === null)
                {
                    $nameTrickWithoutSpace = str_replace(" ", "", $trick->getName());
                    $nameTrickLower = strtolower($nameTrickWithoutSpace);
                    $setFileName = $nameTrickLower.'_MAIN_'.rand(1, 999).'.'.$extension ;
                    $trick->setMainPicture($setFileName);
                }
                $nameFiles = $trick->getMainPicture();
                $form['setPicture']->getData()->move('../public/uploads/main/', $nameFiles);
            }
            $trickRepository->add($trick);
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('app_trick_show', ['id'=> $trick->getId(),'page'=> 1], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}/{page<[0-9]+>}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, ?int $page): Response
    {
        $comment = new Comment ;
        $form = $this->createForm(CommentType::class, $comment, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $user = $this->getUser();
            $comment->setUser($user);
            $commentRepository->add($comment);
            $this->addFlash('success', 'Commentaire rajouté avec succès');
        }

        if ($trick->getMainPicture()){
            $pictureName = preg_replace('/\'/','\\\'', $trick->getMainPicture());
        } else { $pictureName = false; }

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
            'pictureName' => $pictureName,
            'trick' => $trick,
            'comments' => $comments,
            'max_page' => $maxPage,
            'actual_page' => $actualPage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'app_trick_delete', methods: ['POST']) ]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick);
            $this->addFlash('info', 'La figure a été supprimée avec succès');
        }
        return $this->redirectToRoute('app_all_tricks', ['_fragment' => 'tricks'], Response::HTTP_SEE_OTHER);
    }
}
