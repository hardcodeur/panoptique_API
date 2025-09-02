<?php

namespace App\Dto\Customer;

use ApiPlatform\Metadata\ApiProperty;

class CustomerListDto{
    
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $name = null,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}