<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\User;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $currentUser = $request->getUser();
        var_dump($currentUser);
        var_dump("Welcom to summoners rift !");


        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }


    //FORM exemple

    /**
     * @Route("/user/add", name="user")
     */
    public function addUserAction(Request $request)
    {

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('age', NumberType::class)
            ->add('email', EmailType::class)
            ->add('job', EntityType::class, array(
                'class' => 'AppBundle:Job',
                'choice_label' => 'name',
            ))
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();

        $form->handleRequest($request);


        //Get data forward controller POST creat euser de l'api et creer

        //FORWARD
        // https://symfony.com/doc/current/controller/forwarding.html


        if ($form->isSubmitted() && $form->isValid()) {


            $newUser = $form->getData();


            $this->addUserByFormAction($newUser);

            return $this->redirectToRoute('user_show', array('last_user_created' => $newUser));


        }

        return $this->render('UserFormBundle:AddUserForm:form_add_user.html.twig', array(
            'param1' => "test",
            'form' => $form->createView(),
        ));

    }







    //API

    /**
     * @Route("/user/getUsers", name="user_get_users")
     */
    public function getUsersAction(Request $request)
    {

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $formatted = [];

        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'age' => $user->getAge(),
            ];
        }

        return new JsonResponse($formatted);


    }


    //GET

    /**
     * @Route("/user/getUser/{username}", name="user_get_user_by_username")
     */
    public function getUserByUsername(Request $request, $username)
    {

        $userToSearch = $username;

        //Return tableau d'objet donc foreach pour acceder au objet et au relation
        $user = $this->getDoctrine()->getRepository(User::class)->findBy(array(
            'username' => $userToSearch,
        ));



        $formatted = [];

        if($user == null){
            $formatted[] = [
                "erreur" => $username. " existe pas ! ",
            ];
        }

        foreach ($user as $userFind) {
            $formatted[] = [
                'id' => $userFind->getId(),
                'username' => $userFind->getUsername(),
                'email' => $userFind->getEmail(),
                'age' => $userFind->getAge(),
                'job' => $userFind->getJob()->getName() //OneToMany
            ];
        }

        return new JsonResponse($formatted);

    }


    //POST

    /**
     * @Route("/user/addUser", name="user_add_user_by_form")
     */

    public function addUserByFormAction($newUser)
    {


        $em = $this->getDoctrine()->getManager();
        $em->persist($newUser);
        $em->flush();


        return new JsonResponse($newUser);
    }


    /**
     * @Route("/user/show", name="user_show")
     */

    public function showAllUser(Request $request)
    {


        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $formatted = [];


        foreach($users as $data){
            if($data->getJob() == null ){
                $formatted[] = [
                    'name' => $data->getUsername(),
                    'email' => $data->getEmail(),
                    'age' => $data->getAge(),
                    'job' => "Aucun emploi",
                ];
            }else{
                $formatted[] = [
                    'name' => $data->getUsername(),
                    'email' => $data->getEmail(),
                    'age' => $data->getAge(),
                    'job' => $data->getJob()->getName(),
                ];
            }

        }


        return new JsonResponse($formatted);

    }


}
