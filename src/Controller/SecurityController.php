<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SecurityController extends Controller
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        

        return $this->render('security/login.html.twig', [
            'error'         => $error,
            'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, AuthenticationUtils $utils){
        $error = $utils->getLastAuthenticationError();
        $user = new User();

        $form = $this->createFormBuilder($user)
        ->add('username', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('email', EmailType::class, array('attr' => array('class' => 'form-control')))
        ->add('password', PasswordType::class, array('attr' => array('class' => 'form-control')))
        ->add('save', SubmitType::class, array('label'=> 'Register', 'attr' => array('class'=> 'btn btn-primary mt-5')))
        ->getForm();

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $username = $form['username'] -> getData();
            $email = $form['email'] -> getData();
            $password = $form['password'] -> getData();

            $user = new User();
            $user->setUsername($username);
            $user->setPassword(
                $this->encoder->encodePassword($user, $password)
            );
            $user->setEmail($email);

            $EntityManager = $this -> getDoctrine() -> getManager();
            $EntityManager -> persist($user);
            $EntityManager -> flush();

            return $this -> redirectToRoute('rooms_list');
        }        

        return $this->render('security/register.html.twig', [
            'error'         => $error,
            'user'          => $user,
            'form' => $form -> createView()
        ]);
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {

    }
}
