<?php
namespace App\Entity\Traits ;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/*
    Pour pouvoir utiliser ce trait :
    Specifier : use Timestampable; dans la classe voulue
    Declarer le namespace : use App\Entity\Traits\Timestampable;
    Declarer #[ORM\HasLifecycleCallbacks] avant la classe
*/
trait Timestampable
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
