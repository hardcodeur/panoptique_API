<?php
namespace App\Dto\Notification;

use ApiPlatform\Metadata\ApiProperty;

class NotificationDetailDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?string $id = null,
        private ?string $text = null,

    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
    
}                                                                                                               