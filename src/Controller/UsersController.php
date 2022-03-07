<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="app_users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }
    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnoncesType::class,$annonce);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);
            $em= $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
              return($this->redirectToRoute('app_home'));

        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/users/profil/modifier", name="users_profil_modifier")
     */
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
     
            $em= $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('editProfile', 'Félicitations ! Votre profil a bien été mis à jour . ');
              return $this->redirectToRoute('app_users');

        }

        return $this->render('users/editprofile.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/users/pass/modifier", name="users_pass_modifier")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if($request->isMethod('POST'))
        {
            $em= $this->getDoctrine()->getManager();
            $user = $this->getUser();
            // on verifie si les 2 mots de passe sont identiques
            if($request->get('pass') == $request->get('pass2')) 
            {
                $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('pass')));
                $em->flush();
                $this->addFlash('newPass', 'Bravo . Votre mot de passe a été mis à jour avec succès. ');
                return $this->redirectToRoute('app_users');
            }
            else
            {
                $this->addFlash('errorMdp','Vos deux(2) mots de passe ne correspondent pas. Merci de réessayer. ');
            }
        }
        return $this->render('users/editpass.html.twig');
    }
}
