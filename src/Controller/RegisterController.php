<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{    

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;

        $user = new User;
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email){
                $password = $hasher->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = new Mail();
                $content = "Bonjour ".$user->getFullName()."<br>Bienvenue sur La Boutique 2V3D.";
                $mail->send($user->getEmail(), $user->getFullName(), 'Bienvenue sur La Boutique 2V3D', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez vous connectez à votre compte.";            
                
            } else {                
                $notification = "L'email que vous avez renseigné est déjà utilisé.";   
            }
        }        

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
