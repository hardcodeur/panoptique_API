<?php

namespace App\State\Customer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\Customer\CustomerListDto;
use App\Repository\CustomerRepository;

class CustomerListProvider implements ProviderInterface
{   
    public function __construct(
        private CustomerRepository $repository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $customers = $this->repository->getActiveCustomers();
        
        return array_map(function ($customer) {                                        
            return new CustomerListDto(
                $customer->getId(),
                $customer->getName()
            );
        }, $customers);
    }
}