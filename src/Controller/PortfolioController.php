<?php

namespace App\Controller;

use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PortfolioController extends AbstractController
{
    #[Route('/profile/portfolio', name: 'app_portfolio')]
    public function index(WalletRepository $wallets): Response
    {
        $user=$this->getUser();
        $user_wallet=$wallets->findBy(['users'=>$user]);
   
    
    

        return $this->render('portfolio/index.html.twig', [
            'user_wallet' => $user_wallet,
        ]);
    }
}
