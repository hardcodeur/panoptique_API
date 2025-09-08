<?php
namespace App\Dto\Notification;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for User list representation in API responses
 */
class NotificationListDto
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