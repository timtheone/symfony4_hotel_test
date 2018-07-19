<?php 
    namespace App\Controller;

    use App\Entity\Room;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class RoomsController extends Controller {
         /**
         * @Route("/", name="rooms_list")
         */
        public function listAction(){
            $rooms = $this->getDoctrine()->getRepository(Room::class)->findAll();
            return $this->render('rooms/index.html.twig', array('rooms' => $rooms));
        }

        /**
         * @Route("rooms/{id}", name="room_show")
         */
        public function showRoom($id){
            $room = $this->getDoctrine()->getRepository(Room::class)->find($id);

            return $this->render('rooms/show.html.twig', array(
                'room' => $room
            ));
        }
    }