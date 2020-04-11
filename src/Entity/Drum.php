<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DrumRepository")
 */
class Drum
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
    private $charName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $keycode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clip;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCharName(): ?string
    {
        return $this->charName;
    }

    public function setCharName(string $charName): self
    {
        $this->charName = $charName;

        return $this;
    }

    public function getKeycode(): ?string
    {
        return $this->keycode;
    }

    public function setKeycode(string $keycode): self
    {
        $this->keycode = $keycode;

        return $this;
    }

    public function getClip(): ?string
    {
        return $this->clip;
    }

    public function setClip(string $clip): self
    {
        $this->clip = $clip;

        return $this;
    }
}
