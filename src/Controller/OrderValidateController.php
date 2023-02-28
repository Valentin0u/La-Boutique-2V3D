<?php

namespace App\Controller;

use App\Class\Cart;
use App\Class\Mail;
use App\Entity\Order;
use App\Class\RetServ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0) {
            $cart->remove();
                      
            $order->setState(1);
            $this->entityManager->flush();


            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFullName()."<br>Merci pour votre commande, Cras ultricies ligula sed magna dictum porta. Nulla porttitor accumsan tincidunt. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Vivamus suscipit tortor eget felis porttitor volutpat. Quisque velit nisi, pretium ut lacinia in, elementum id enim.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullName(), 'Votre Commande sur La Boutique 2V3D est validée!', $content);//, $apikey);
        }
      
        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}