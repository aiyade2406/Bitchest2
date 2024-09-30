<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExchangeController extends AbstractController
{
    #[Route('/profile//exchange', name: 'app_exchange')]
    public function index(): Response
 
    {
        $user = $this->getUser();
        $trade=$user->getTrades();

        return $this->render('exchange/index.html.twig', [
            'trades'=>$trade,
            
        ]);
    }
}
