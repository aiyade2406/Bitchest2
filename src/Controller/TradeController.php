<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Entity\Trade;
use App\Entity\User;
use App\Entity\Wallet;
use App\Form\WalletType;
use App\Repository\CryptoRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CoinMarketCap;

class TradeController extends AbstractController
{
    #[Route('/profile/trade', name: 'app_trade', methods: ['GET', 'POST'])]
    public function index(Request $request, CryptoRepository $cryptoRepository, EntityManagerInterface $entityManager, WalletRepository $walletRepo): Response
    {
        $cmc = new CoinMarketCap\Api('357648bd-4a62-4ffd-abc3-fb64ed2bb77c');
        $cryptos = [
            'Bitcoin' => 'BTC',
            'Ethereum' => 'ETH',
            'Ripple' => 'XRP',
            'Bitcoin Cash' => 'BCH',
            'Cardano' => 'ADA',
            'Litecoin' => 'LTC',
            'NEM' => 'XEM',
            'Stellar' => 'XLM',
            'IOTA' => 'IOTA',
            'Dash' => 'DASH'
        ];

        $allcrypto = $cryptoRepository->findAll();
        $results = [];

        foreach ($cryptos as $name => $symbol) {
            $response = $cmc->cryptocurrency()->quotesLatest(['symbol' => $symbol, 'convert' => 'EUR']);

            if (isset($response->data->$symbol->quote->EUR)) {
                $percentChange1h = $response->data->$symbol->quote->EUR->percent_change_1h;
                $results[$name] = number_format($percentChange1h, 2, '.', '');
            }
        }

        $bitcoin = $cmc->cryptocurrency()->quotesLatest(['symbol' => 'BTC', 'convert' => 'EUR']);
        $market_cap = $bitcoin->data->BTC->quote->EUR->market_cap;
        $volume_24 = $bitcoin->data->BTC->quote->EUR->volume_24h;
        $max_supply = $bitcoin->data->BTC->max_supply;
        $circulating_supply = $bitcoin->data->BTC->circulating_supply;
        $bitcoinPrice = $bitcoin->data->BTC->quote->EUR->price;
        $symbolBitcoin = $bitcoin->data->BTC->symbol;

        $decimaBitcoin = number_format($bitcoinPrice, 2, '.', '');
        $bitcoinchangement7days = $bitcoin->data->BTC->quote->EUR->percent_change_7d;
        $bitcoinchangement24h = $bitcoin->data->BTC->quote->EUR->percent_change_24h;
        $percent_change_1h = $bitcoin->data->BTC->quote->EUR->percent_change_1h;
        $bitcoinchangement30days = $bitcoin->data->BTC->quote->EUR->percent_change_30d;
        $bitcoinchangement60days = $bitcoin->data->BTC->quote->EUR->percent_change_60d;
        $bitcoinchangement90days = $bitcoin->data->BTC->quote->EUR->percent_change_90d;

        $data = [
            'labels' => ['90 Days', '60 Days', '30 Days', '7 Days', '1 Day', '1 Hour'],
            'values' => [
                $bitcoinchangement90days,
                $bitcoinchangement60days,
                $bitcoinchangement30days,
                $bitcoinchangement7days,
                $bitcoinchangement24h,
                $percent_change_1h
            ]
        ];

        $entityManager->flush();

        $wallet = new Wallet();
        $wallet->setDate(new \DateTime());
        $user = $this->getUser();
        $wallet->setUsers($user);
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);
        $quantity = $wallet->getQuantity();
        $solde = $user->getSolde();

        if ($form->isSubmitted() && $form->isValid()) {
            $crypto_form = $wallet->getCryptos();
            $crypto_quantity_form = $wallet->getQuantity();
            $wallet_user = $walletRepo->findOneBy(['users' => $user, 'cryptos' => $crypto_form]);
            $price_crypto = $crypto_form->getPrice();

            if ($quantity * $price_crypto <= $solde) {
                if ($wallet_user === null) {
                    $wallet_user = new Wallet();
                    $wallet_user->setUsers($user);
                    $wallet_user->setCryptos($crypto_form);
                    $wallet_user->setQuantity($crypto_quantity_form);
                    $wallet_user->setDate(new \DateTime());
                    $wallet_user->setPricePurchase($price_crypto);
                    
                    $entityManager->persist($wallet_user);

                    $trade = new Trade();
                    $trade->setUsers($user);
                    $trade->setCrypto($crypto_form);
                    $trade->setType("Purchase");
                    $trade->setQuantity($crypto_quantity_form);
                    $trade->setDate(new \DateTime());

                    $entityManager->persist($trade);
                } else {
                    $wallet_user->setQuantity($wallet_user->getQuantity() + $crypto_quantity_form);

                    $trade = new Trade();
                    $trade->setUsers($user);
                    $trade->setCrypto($crypto_form);
                    $trade->setType("Purchase");
                    $trade->setQuantity($crypto_quantity_form);
                    $trade->setDate(new \DateTime());

                    $entityManager->persist($trade);
                }

                $user->setSolde($solde - $quantity * $price_crypto);
                $entityManager->flush();

                return $this->redirectToRoute('app_trade', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', "The price is higher than your balance.");
            }
        }

        return $this->render('trade/index.html.twig', [
            'wallet' => $wallet,
            'form' => $form,
            'user' => $user,
            'Bitcoin' => $decimaBitcoin,
            'data' => $data,
            'market_cap' => $market_cap,
            'volume_24' => $volume_24,
            'circulating_supply' => $circulating_supply,
            'bitcoinchangement24h' => $bitcoinchangement24h,
            'symbol' => $symbolBitcoin,
            'max_supply' => $max_supply,
            'allcrypto' => $allcrypto,
            'cour_crypto' => $results
        ]);
    }

    #[Route('/profile/send', name: 'app_trade_send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $entityManager, WalletRepository $walletRepo, CryptoRepository $cryptoRepository): Response
    {
        $wallet = new Wallet();
        $wallet->setDate(new \DateTime());
        $user = $this->getUser();
        $wallet->setUsers($user);
        $form = $this->createForm(WalletType::class, $wallet);
        $form->handleRequest($request);

        $cmc = new CoinMarketCap\Api('357648bd-4a62-4ffd-abc3-fb64ed2bb77c');
        $cryptos = [
            'Bitcoin' => 'BTC',
            'Ethereum' => 'ETH',
            'Ripple' => 'XRP',
            'Bitcoin Cash' => 'BCH',
            'Cardano' => 'ADA',
            'Litecoin' => 'LTC',
            'NEM' => 'XEM',
            'Stellar' => 'XLM',
            'IOTA' => 'IOTA',
            'Dash' => 'DASH'
        ];

        $allcrypto = $cryptoRepository->findAll();
        $results = [];

        foreach ($cryptos as $name => $symbol) {
            $response = $cmc->cryptocurrency()->quotesLatest(['symbol' => $symbol, 'convert' => 'EUR']);

            if (isset($response->data->$symbol->quote->EUR)) {
                $percentChange1h = $response->data->$symbol->quote->EUR->percent_change_1h;
                $results[$name] = number_format($percentChange1h, 2, '.', '');
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $crypto_form = $wallet->getCryptos();
            $crypto_quantity_form = $wallet->getQuantity();
            $wallet_user = $walletRepo->findOneBy(['users' => $user, 'cryptos' => $crypto_form]);
            $price_crypto = $crypto_form->getPrice();

            if ($wallet_user !== null && $wallet_user->getQuantity() >= $crypto_quantity_form) {
                $wallet_user->setQuantity($wallet_user->getQuantity() - $crypto_quantity_form);
                $user->setSolde($user->getSolde() + $crypto_quantity_form * $price_crypto);

                $trade = new Trade();
                $trade->setUsers($user);
                $trade->setCrypto($crypto_form);
                $trade->setType("Sale");
                $trade->setQuantity($crypto_quantity_form);
                $trade->setDate(new \DateTime());

                $entityManager->persist($trade);

                if ($wallet_user->getQuantity() <= 0) {
                    $entityManager->remove($wallet_user);
                }

                $entityManager->flush();

                return $this->redirectToRoute('app_trade', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', "You don't have enough crypto.");
            }
        }

        return $this->render('trade/send.html.twig', [
            'wallet' => $wallet,
            'form' => $form,
            'user' => $user,
            'allcrypto' => $allcrypto,
            'cour_crypto' => $results
        ]);
    }
}
