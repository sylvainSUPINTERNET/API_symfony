<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('age', TextType::class)
            ->add('email', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();

        $form->handleRequest($request);



        //Get data forward controller POST creat euser de l'api et creer

        //FORWARD
        // https://symfony.com/doc/current/controller/forwarding.html

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($task);
            // $em->flush();

            return $this->redirectToRoute('task_success');
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

        $user = $this->getDoctrine()->getRepository(User::class)->findBy(array(
            'username' => $userToSearch,
        ));

        $formatted = [];

        foreach ($user as $userFind) {
            $formatted[] = [
                'id' => $userFind->getId(),
                'username' => $userFind->getUsername(),
                'email' => $userFind->getEmail(),
                'age' => $userFind->getAge(),
            ];
        }

        return new JsonResponse($formatted);

    }


    //POST
}
