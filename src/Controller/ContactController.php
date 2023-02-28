<?php

namespace App\Controller;

use App\Class\Mail;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest(($request));

        if ($form->isSubmitted() && $form->isValid()){

            
            $datas = $form->getData(); 
            
            $mail = new Mail();            
            $mail->send('laboutique2v3d@gmail.com', 'La Boutique 2V3D', 'Nouvelle demande de contact', $datas['message']);
            $this->addFlash('info', 'Merci de nous avoir contacté, notre équipe va vous répondre dans les meilleures délais.');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ] );
    }
}
