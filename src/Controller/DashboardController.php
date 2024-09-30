<?php

namespace App\Controller;

use App\Repository\CryptoRepository;
use App\Repository\TradeRepository;
use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use CoinMarketCap;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/profile/dashboard', name: 'app_dashboard')]

    public function index(WalletRepository $wallets,CryptoRepository $crypto,TradeRepository $tradeRepo):Response
    {
        $cmc = new CoinMarketCap\Api('357648bd-4a62-4ffd-abc3-fb64ed2bb77c');
        $bitcoin = $cmc->cryptocurrency()->quotesLatest(['symbol' => "BTC", 'convert' => 'EUR']);
        $ethereum = $cmc->cryptocurrency()->quotesLatest(['symbol' => "ETC", 'convert' => 'EUR']);
        
        $bitcoinchangement7days = $bitcoin->data->BTC->quote->EUR->percent_change_7d;
        $bitcoinchangement24h = $bitcoin->data->BTC->quote->EUR->percent_change_24h;
        $bitcoinchangement30days = $bitcoin->data->BTC->quote->EUR->percent_change_30d;
        
        $ethereumchangement24h = $ethereum->data->ETC->quote->EUR->percent_change_24h;
        $ethereumchangement7days = $ethereum->data->ETC->quote->EUR->percent_change_7d;
        $ethereumchangement30days = $ethereum->data->ETC->quote->EUR->percent_change_30d;
        
        $dataB = [
            $bitcoinchangement30days,
            $bitcoinchangement7days,
            $bitcoinchangement24h
        ];
        
        $dataE = [
            $ethereumchangement30days,
            $ethereumchangement7days,
            $ethereumchangement24h
        ];
        
        $user = $this->getUser();
        $user_wallet = $wallets->findBy(['users' => $user]);
        
        $total_investit = 0;
        $price_update = 0;
        
        $profit = 0;
        $quantity = 0;
        $user = $this->getUser();
        foreach ($user_wallet as $valeur) {
            $total_instant_t = $valeur->getPricePurchase();
            $quantity = $valeur->getQuantity();
            $total_investit += $total_instant_t * $quantity;
        }
        
        foreach ($user_wallet as $valeur) {
            $price2 = $valeur->getCryptos()->getPrice();
            $quantity = $valeur->getQuantity();
            $price_update += $price2 * $quantity;
        }
        
        $profit = $total_investit - $price_update;
        $profit = number_format($profit, 2);
        
        $allCrypto = $crypto->findAll();
        $lastTrade = $tradeRepo->findOneBy(['users' => $user, 'type' => 'Purchase'], ['date' => 'DESC']);
        
        return $this->render('dashboard/index.html.twig', [
            'user'=>$user,
            'user_wallet' => $user_wallet,
            'invested'=>  $total_investit,
            'profit'=>$profit,
     

            'allCrypto'=>$allCrypto,
            'dataB'=>$dataB,
            'dataE'=>$dataE,
            'lastTrade' => $lastTrade,
            
            

            
        ]);
    }
}
