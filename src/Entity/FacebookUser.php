<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacebookUserRepository")
 */
class FacebookUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fbid;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFbid(): ?string
    {
        return $this->fbid;
    }

    public function setFbid(string $fbid): self
    {
        $this->fbid = $fbid;

        return $this;
    }

    public function getLastActive(): ?\DateTimeInterface
    {
        return $this->lastActive;
    }

    public function setLastActive(\DateTimeInterface $lastActive): self
    {
        $this->lastActive = $lastActive;

        return $this;
    }
}
