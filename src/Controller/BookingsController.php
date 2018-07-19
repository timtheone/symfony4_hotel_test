<?php 
    namespace App\Controller;

    use App\Entity\Room;
    use App\Entity\Booking;


    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\FormType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;

    class BookingsController extends Controller {
         /**
         * @Route("/bookings", name="booking_list")
         */
        public function listAction(){
            $bookings = $this->getDoctrine()->getRepository(Booking::class)->findAll();
            
            return $this->render('bookings/index.html.twig', array(
                'bookings' => $bookings,
            ));
        }

         /**
         * @Route("/book/{id}", name="book")
         */
        public function new(Request $request, $id){
            $booking = new Booking();
            $room = $this->getDoctrine()->getRepository(Room::class)->find($id);;

            $form = $this->createFormBuilder($booking)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off', 'placeholder' => '2018-06-01'],
                'format' => 'yyyy-mm-dd',
                'html5' => false
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off', 'placeholder' => '2018-06-11'],
                'format' => 'yyyy-mm-dd',
                'html5' => false
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Book this room',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

            $form->handleRequest($request);
            if($form -> isSubmitted() && $form -> isValid()){
                $booking = $form->getData();
                $booking->setRoom($room);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($booking);
                $entityManager->persist($room);
                $entityManager->flush();

                return $this->redirectToRoute('booking_list');
            }    
            
            return $this->render('bookings/book.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }