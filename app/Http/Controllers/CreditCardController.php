<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ICreditCardRepository;

class CreditCardController extends Controller
{
    public function __construct(ICreditCardRepository $repository)
    {
        $this->repository = $repository;
    }
}
