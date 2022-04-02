<?php

namespace App\Manager;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Repository\CommentRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentManager
{
    private CommentRepository $commentRepository;
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    public function create(Comment $comment, Trick $trick, UserInterface $user)
    {
        $comment->setTrick($trick);
        $comment->setUser($user);
        $this->commentRepository->add($comment);
    }
}