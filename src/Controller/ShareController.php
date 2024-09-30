<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\WalletType2;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShareController extends AbstractController
{
    #[Route('/share', name: 'app_share')]
    public function index(Request $request, EntityManagerInterface $entityManager, WalletRepository $walletRepo): Response
    {
        $wallet = new Wallet();
        $wallet->setDate(new \DateTime());
        $user = $this->getUser();
        $wallet->setUsers($user);

        $form = $this->createForm(WalletType2::class, $wallet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $crypto_form = $wallet->getCryptos();
            $crypto_quantity_form = $wallet->getQuantity();
            $price_crypto = $crypto_form->getPrice();

            // Chercher le wallet du donneur
            $wallet_giver = $walletRepo->findOneBy([
                'users' => $user,
                'cryptos' => $crypto_form
            ]);

            if ($wallet_giver === null) {
                return new Response('The donor does not own this crypto.', Response::HTTP_BAD_REQUEST);
            }

            $giver_quantity = $wallet_giver->getQuantity();

            // Vérification de la quantité
            if ($giver_quantity < $crypto_quantity_form) {
                $this->addFlash('error', "You don't have enough quantity to share.");
              
            }

            // Chercher ou créer un wallet pour le récepteur
            $wallet_receiver = $walletRepo->findOneBy([
                'users' => $wallet->getUsers(),
                'cryptos' => $crypto_form
            ]);

            if ($wallet_receiver === null) {
                $wallet_receiver = new Wallet();
                $wallet_receiver->setUsers($wallet->getUsers());
                $wallet_receiver->setCryptos($crypto_form);
                $wallet_receiver->setQuantity($crypto_quantity_form);
                $wallet_receiver->setDate(new \DateTime());
                $wallet_receiver->setPricePurchase($price_crypto);
                $entityManager->persist($wallet_receiver);
            } else {
                // Mise à jour de la quantité du récepteur
                $wallet_receiver->setQuantity($wallet_receiver->getQuantity() + $crypto_quantity_form);
            }

            // Mise à jour de la quantité du donneur
            $wallet_giver->setQuantity($giver_quantity - $crypto_quantity_form);

            $entityManager->flush();

            return $this->redirectToRoute('app_share', [], Response::HTTP_SEE_OTHER);
        }else {
            $this->addFlash('error', "You don't have enough crypto.");
        }

        return $this->render('share/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
