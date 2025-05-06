<?php

namespace App\Dto\LocationNote;

use ApiPlatform\Metadata\ApiProperty;


class LocationNoteDto
{   

    private const FULL_FORMAT = "d/m/Y H:i";

    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $title = null,
        private ?string $note = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }


    public function getCreatedAt(): ?string
    {   
        if($this->createdAt){
            $date = $this->createdAt->setTimezone(new \DateTimeZone('Europe/Paris'));
            return $date->format(self::FULL_FORMAT);
        }

        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {   
        if($this->updatedAt){
            $date = $this->updatedAt->setTimezone(new \DateTimeZone('Europe/Paris'));
            return $date->format(self::FULL_FORMAT);
        }

        return $this->updatedAt;
    }
}