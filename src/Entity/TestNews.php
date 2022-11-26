<?php

namespace App\Entity;

use App\Repository\TestNewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="test_news", indexes={
 *     @ORM\Index(name="dates_at", columns={"created_at","deleted_at"}),
 * })
 * @ORM\Entity(repositoryClass=TestNewsRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class TestNews
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default":NULL})
     */
    private $deleted_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistCallback()
    {
        if(is_null($this->created_at)) {
            $this->created_at = new \DateTime("now");
        }

        exit('Needs prevent inserting duplicate value of `created_at`.<br> 1) alter index date_at to unique index date_at and not allow NULL for deleted_at<br>2) or other method');

        return $this;
    }
}
