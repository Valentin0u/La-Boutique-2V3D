<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')){
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user){
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(Uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);                

                $content = "Bonjour ".$user->getFullName()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur La Boutique 2V3D.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='http://127.0.0.1:8000".$url."'>mettre à jour votre mot de passe</a>.";
                
                $mail = new Mail();                
                $mail->send($user->getEmail(), $user->getFullName(), 'Réinitialiser votre mot de passe sur La Boutique 2V3D', $content);
                $this->addFlash('info', 'Vous allez recevoir dans quelques secondes, un mail avec la procédure pour réinitialiser votre mot de passe. Le lien est valable 1h.');
            } else {
            $this->addFlash('info', 'Cette adresse email est inconnue');

            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function update(Request $request, UserPasswordHasherInterface $hasher, $token)
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }

        $now = new DateTimeImmutable(); 

        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour')){
            $this->addFlash('info', 'Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData(); 
            
            $password = $hasher->hashPassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);            

            $this->entityManager->flush();


            $this->addFlash('info', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('login');
            
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);

    }
}