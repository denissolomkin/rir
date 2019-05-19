<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserByAdminForm;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Controller used to manage users.
 *
 * @Route("/admin/users")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="admin_user_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $list = $userRepository->findAll();

        return $this->render('admin/user/list.html.twig', [
            'list' => $list,
        ]);

    }

    /**
     * @Route("/new", methods={"GET"}, name="admin_user_new")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserByAdminForm::class, $user);
        $form
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('Submit', SubmitType::class)
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.updated_successfully');


            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user)
    {

        $form = $this->createForm(UserByAdminForm::class, $user);
        $form->add('Submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.updated_successfully');


            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
