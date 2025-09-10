<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class locationNoteUpdateCheckerDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $title = null,
        private ?string $note = null,
    ) {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
        
}