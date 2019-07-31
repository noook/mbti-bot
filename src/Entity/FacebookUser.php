<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MbtiTest", mappedBy="user", orphanRemoval=true)
     */
    private $mbtiTests;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    public function __construct()
    {
        $this->mbtiTests = new ArrayCollection();
    }

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

    /**
     * @return Collection|MbtiTest[]
     */
    public function getMbtiTests(): Collection
    {
        return $this->mbtiTests;
    }

    public function addMbtiTest(MbtiTest $mbtiTest): self
    {
        if (!$this->mbtiTests->contains($mbtiTest)) {
            $this->mbtiTests[] = $mbtiTest;
            $mbtiTest->setAaa($this);
        }

        return $this;
    }

    public function removeMbtiTest(MbtiTest $mbtiTest): self
    {
        if ($this->mbtiTests->contains($mbtiTest)) {
            $this->mbtiTests->removeElement($mbtiTest);
            if ($mbtiTest->getUser() === $this) {
                $mbtiTest->setUser(null);
            }
        }

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
