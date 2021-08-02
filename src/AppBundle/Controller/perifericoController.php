<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\backup;
use AppBundle\Entity\bocinas;
use AppBundle\Entity\componente;
use AppBundle\Entity\cpuchasis;
use AppBundle\Entity\equipo;
use AppBundle\Entity\estabilizador;
use AppBundle\Entity\fuente;
use AppBundle\Entity\hdd;
use AppBundle\Entity\impresora;
use AppBundle\Entity\incidencia;
use AppBundle\Entity\inventario;
use AppBundle\Entity\lector;
use AppBundle\Entity\microprocesador;
use AppBundle\Entity\monitor;
use AppBundle\Entity\motherboard;
use AppBundle\Entity\mouse;
use AppBundle\Entity\movimiento;
use AppBundle\Entity\movimientoI;
use AppBundle\Entity\ram;
use AppBundle\Entity\scanner;
use AppBundle\Entity\taller;
use AppBundle\Entity\tarjeta_video;
use AppBundle\Entity\teclado;
use AppBundle\Entity\tipo;
use AppBundle\Form\fechaSalidaTaller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Faker\Provider\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class perifericoController extends Controller
{
    private $tipo = '';
    private $comp = '';
    private $filters = [];
    private $componente_seleccionado = '';
    //private $pagination = [];


    public $se_guardo = false;
    public $idactual = '';
    public $nombreE = '';
    public $idE = '';
    public $pagination = '';

    private $incidencias = '';

    public $lista_componentes = [];
    public $coleccion = '';

    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/reportes/nuevo_equipo/{tipo}",name="nuevo_equipo")
     */
    public function nuevoEquipoAction(Request $request, $tipo)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\equipoFomType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        // $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setTipoEquipo($tipo);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }

        return $this->render('perifericos/nuevo_equipo.html.twig', array(
            'form' => $form->createView(), 'tipo' => $tipo));
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_bocina/{id}", name="editar_bocina")
     * @return Response
     */
    public function editarBocinaAction(Request $request, $id)
    {
        $bocina = $this->getDoctrine()->getRepository('AppBundle:bocinas')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $bocinaform->add('Guardar', SubmitType::class, array('label' => 'Editar Plan'));

        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $bocinaform->handleRequest($request);

        if ($bocinaform->isSubmitted() && $bocinaform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $bocinaform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $bocinaform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_impresora/{id}", name="editar_impresora")
     * @return Response
     */
    public function editarimpresoraAction(Request $request, $id)
    {
        $bocina = $this->getDoctrine()->getRepository('AppBundle:impresora')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        /* if (!$inventario) {
       return $this->redirectToRoute('lista_estaciones');
     }*/

        $equipoform = $this->createForm(\AppBundle\Form\impresoraFormType::class, $bocina);

        $equipoform->add('Guardar', SubmitType::class, array('label' => 'Editar ', 'class' => 'btn btn-sm btn-success'));

        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $equipoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_impresora2/{id}/{tipo}", name="editar_impresora2")
     * @return Response
     */
    public function editarimpresora2Action(Request $request, $id, $tipo)
    {
        $impresora = $this->getDoctrine()->getRepository('AppBundle:impresora')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\impresoraFormType::class, $impresora);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);


            //ump($impresora);die();
            if (is_null($impresora->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $incidencia->setDpto($impresora->getCpu()->getDpto());
            }

            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setResumen('---');
            if (is_null($impresora->getCpu())) {
                $incidencia->setInventario(null);
            } else {
                $incidencia->setInventario($impresora->getCpu()());
            }


            $date = new \DateTime('now');
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            //$incidencia->setFechaA(date$date);
            $incidencia->setFechaA(new \DateTime("now"));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'impresora']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_componente/{id}", name="editar_componente")
     * @return Response
     */
    public function editarComponenteAction(Request $request, $id)
    {
        $equipo = $this->getDoctrine()->getRepository('AppBundle:componente')->find($id);
        $em = $this->getDoctrine()->getManager();

        $equipoform = $this->createForm(\AppBundle\Form\componentesFormType::class, $equipo);
        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fa fa-save')));

        /**
         * Guardar los componentes origines con el k viene el equipo
         */

        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            $componente = $equipoform->getData();
            $componente->setEstado('Activo');
           // dump($componente);die();
            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()
            );
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setResumen('---');
            if (is_null($equipo->getEstacion2())) {
                $incidencia->setInventario(null);
            } else {
                $incidencia->setInventario($equipo->getEstacion2());
            }
            $date = new \DateTime('now');
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($equipo->getTipoComponente());
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            //$incidencia->setFechaA(date$date);
            $incidencia->setFechaA(new \DateTime("now"));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
          //  dump($componente);exit();
            $entityManager->persist($incidencia);
            $entityManager->persist($componente);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_componente', ['id' => $id, 'tipo' => $equipo->getTipoComponente()]);
        }
        //dump($equipo);die();
        return $this->render('estacion_trabajo/editar_datos_componente.html.twig', ["form" => $equipoform->createView(), "tipo" => $equipo->getTipoComponente(),'componente'=>$equipo]);
    }
    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_componenteAjax", name="editar_componenteAjax")
     * @return Response
     */
    public function editarComponenteAjaxAction(Request $request)
    {

        $id=$request->get('id');
        $componente = $this->getDoctrine()->getRepository('AppBundle:componente')->find($id);
       // dump($request);die();
        $entityManager = $this->getDoctrine()->getManager();
         if($request->get('marca')){
             $componente->setMarca($request->get('marca'));
         }
        if($request->get('sata')){
            $componente->setSata($request->get('sata'));
        }
        if($request->get('watts')){
            $componente->setWatts($request->get('watts'));
        }
        if($request->get('modelo')){
            $componente->setModelo($request->get('modelo'));
        }
        if($request->get('lga')){
            $componente->setLga($request->get('lga'));
        }
        if($request->get('ram')){
            $componente->setRam($request->get('ram'));
        }
        if($request->get('ranuraVideo')){
            $componente->setRanuraVideo($request->get('ranuraVideo'));
        }
        if($request->get('velocidad')){
            $componente->setVelocidad($request->get('velocidad'));
        }
        if($request->get('capacidad')){
            $componente->setCapacidad($request->get('capacidad'));
        }
        if($request->get('tipo')){
            $componente->setTipo($request->get('tipo'));
        }
        if($request->get('conector')){
            $componente->setConector($request->get('conector'));
        }
        if($request->get('optico')){
        $componente->setOptico($request->get('optico'));
        }

      //  dump($componente);die();
        $entityManager->persist($componente);
        $entityManager->flush();

        $atributosComponente = array(
            'marca' => $componente->getMarca(),
            'sata'=>$componente->getSata(),
            'optico'=>$componente->getOptico(),
            'conector'=>$componente->getConector(),
            'tipo'=>$componente->getTipo(),
            'capacidad'=>$componente->getCapacidad(),
            'velocidad'=>$componente->getVelocidad(),
            'ranuraVideo'=>$componente->getRanuraVideo(),
            'ram'=>$componente->getRam(),
            'lga'=>$componente->getLga(),
            'modelo'=>$componente->getModelo(),
            'watts'=>$componente->getWatts()
        );

        return  new JsonResponse($atributosComponente);
    }

//    /**
//     * Matches /
//     *
//     * @Route("reportes/editar_equipo/{id}", name="editar_equipo")
//     * @Method({"GET", "POST"})
//     *
//     */
//    public function editarEquipoAction(Request $request, equipo $equipo)
//    {
//        // $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($id);
//        $em = $this->getDoctrine()->getManager();
//
//
//        // $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
//
//
//        /**
//         * Guardar los componentes origines con el k viene el equipo
//         */
//        $originalComponentes = new ArrayCollection();
//        foreach ($equipo->getComponente() as $item) {
//            $originalComponentes->add($item);
//        }
//        $equipoform = $this->createForm(\AppBundle\Form\equipo2FormType::class, $equipo);
//        //$equipoform = $this->createForm(\AppBundle\Form\equipoFomType::class, $equipo);
//         //dump($equipoform);die();
//        $equipoform->handleRequest($request);
//
//        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
//            // dump($equipoform);die();
//            foreach ($originalComponentes as $itemE) {
//                if (false === $equipo->getComponente()->contains($itemE)) {
//                    $em->remove($itemE);
//                    $em->flush();
//                }
//            }
//
//            foreach ($equipo->getComponente() as $item) {
//                if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
//                    $em->remove($item);
//
//                } else {
//                    //dump($item->getTipoComponente());die();
//                   // $item->setEstado('Activo');
//                    $item->setTipoComponente($item->getTipoComponente());
//                    $item->setCpu($equipo);
//
//                }
//                $em->persist($item);
//            }
//            // $form->getData() holds the submitted values
//            // but, the original `$task` variable has also been updated
//            $pagoSNEU = $equipoform->getData();
//            //$pagoSNEU->setEstado('Activo');
//
//
//            //Creando el historial de edicion
//            $incidencia = new incidencia();
//            $incidencia->setTipo('Edicion');
//            $incidencia->setUser($this->getUser()->getUsername());
//            $incidencia->setEstado('Edicion realizada');
//            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
//
//
//            //ump($impresora);die();
//            if (is_null($equipo->getEstacion())) {
//                $incidencia->setDpto("Sin estacion de trabajo");
//            } else {
//                $incidencia->setDpto($equipo->getEstacion()->getCentroCosto());
//            }
//
//            $incidencia->setAsunto('---');
//            $incidencia->setRespuesta('' .
//                'serie:' . $equipoform->getData()->getSerie() .
//                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()
//
//            );
//            $incidencia->setFecha(new \DateTime("now"));
//            $incidencia->setResumen('---');
//            if (is_null($equipo->getEstacion())) {
//                $incidencia->setInventario(null);
//            } else {
//                $incidencia->setInventario($equipo->getEstacion());
//            }
//
//
//            $date = new \DateTime('now');
//            $incidencia->setTipoMov('---');
//            $incidencia->setEstado('---');
//            $incidencia->setAsesorio($equipo->getTipoEquipo());
//            $incidencia->setTecAsignado($this->getUser());
//            $incidencia->setCorreo($this->getUser()->getEmail());
//            //$incidencia->setFechaA(date$date);
//            $incidencia->setFechaA(new \DateTime("now"));
//
//            // ... perform some action, such as saving the task to the database
//            // for example, if Task is a Doctrine entity, save it!
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($incidencia);
//            $entityManager->persist($pagoSNEU);
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Equipo Editado Correctamente');
//            return $this->redirectToRoute('ver_datos_periferico', ['id' => $equipo->getId(), 'tipo' => $equipo->getTipoEquipo()]);
//        }
//        $areas = $em->getRepository('AppBundle:area')->findAll();
////    $centro_costoActual=$em->getRepository('AppBundle:inventario')->findBy(['id'=>$equipo->getEstacion()->getId()])[0]->getCentroCosto();
//        // $departamento_actual=$centro_costoActual->getArea();
////    dump($centro_costoActual);
////    dump($equipo);
////    die();
////        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', [
////            "form" => $equipoform->createView(), "tipo" => $equipo->getTipoEquipo(), "lista_componentes" => $equipo->getComponente()
////            , "departamento" => $equipo->getDepartamento(), "estacion" => $equipo->getEstacion(), 'areas' => $areas, 'equipo' => $equipo, 'departamentoActual' => '', 'centroCosto' => '',
////        ]);
//                return $this->render('perifericos/editar_equipo.html.twig', [
//            "form" => $equipoform->createView(), "tipo" => $equipo->getTipoEquipo(), "lista_componentes" => ''
//            , "departamento" => $equipo->getDepartamento(), "estacion" => $equipo->getEstacion(), 'areas' => $areas, 'equipo' => $equipo, 'departamentoActual' => '', 'centroCosto' => '',
//        ]);
//    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_bocina2/{id}/{tipo}", name="editar_bocina2")
     * @return Response
     */
    public function editarbocina2Action(Request $request, $id, $tipo)
    {
        $bocina = $this->getDoctrine()->getRepository('AppBundle:bocinas')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\bocinaType::class, $bocina);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();

            //Creando el historial de edicion
            $incidencia = new incidencia();

            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($bocina->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $bocina->getCpu()]);
                // dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }

            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            // $incidencia->setInventario($bocina->getCpu()-);
            if (is_null($bocina->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $bocina->getCpu()]);
                // dump($inv);die();
                $incidencia->setInventario($inv[0]);
                // dump($incidencia);die();
            }

            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));
            // dump($incidencia);die();
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);

            $entityManager->persist($pagoSNEU);
            $entityManager->flush();
            //dump($incidencia);die();
            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $bocina->getId(), 'tipo' => 'bocinas']);
        }
        dump($equipoform);
        die();
        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_hdd2/{id}/{tipo}", name="editar_hdd2")
     * @return Response
     */
    public function editarhdd2Action(Request $request, $id, $tipo)
    {
        $hdd = $this->getDoctrine()->getRepository('AppBundle:hdd')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\hddType::class, $hdd);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();

            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($hdd->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $hdd->getCpu()]);
                // dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'marca:' . $equipoform->getData()->getMarca() . 'capacidad:' . $equipoform->getData()->getCapacidad() . 'sata' . $equipoform->getData()->getSata()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $hdd->getCpu()]);
            $incidencia->setInventario($inv[0]);
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_perifericoSI', ['id' => $hdd->getId(), 'tipo' => 'hdd']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_lector2/{id}/{tipo}", name="editar_lector2")
     * @return Response
     */
    public function editarlector2Action(Request $request, $id, $tipo)
    {
        $lector = $this->getDoctrine()->getRepository('AppBundle:lector')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\lectorType::class, $lector);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($lector->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $lector->getCpu()]);
                // dump($inv);die();
                if ($inv == null) {
                    //dump("holaa");die();
                    $incidencias = '';
                } else {
                    // dump("hhh");die();
                    $incidencia->setDpto($inv[0]->getDpto());
                }
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'tipo:' . $equipoform->getData()->getTipo() . 'marca:' . $equipoform->getData()->getMarca()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $lector->getCpu()]);
            if ($inv == null) {
                //dump("holaa");die();
                $incidencia->setInventario(null);
            } else {
                // dump("hhh");die();
                $incidencia->setInventario($inv[0]);
            }

            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'lector']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_board2/{id}/{tipo}", name="editar_board2")
     * @return Response
     */
    public function editarboard2Action(Request $request, $id, $tipo)
    {
        $board = $this->getDoctrine()->getRepository('AppBundle:motherboard')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\motherboardType::class, $board);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($board->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $board->getCpu()]);
                if ($inv == null) {
                    //dump("holaa");die();
                    $incidencias = '';
                } else {
                    // dump("hhh");die();
                    $incidencia->setDpto($inv[0]->getDpto());
                }


            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $board->getCpu()]);
            if ($inv == null) {
                //dump("holaa");die();
                $incidencia->setInventario(null);
            } else {
                // dump("hhh");die();
                $incidencia->setInventario($inv[0]);
            }

            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'motherboard']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_micro2/{id}/{tipo}", name="editar_micro2")
     * @return Response
     */
    public function editarmicro2Action(Request $request, $id, $tipo)
    {
        $micro = $this->getDoctrine()->getRepository('AppBundle:microprocesador')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\microprocesadorType::class, $micro);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($micro->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $micro->getCpu()]);
                //dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'tipo:' . $equipoform->getData()->getTipo() . 'velocidad:' . $equipoform->getData()->getVelicidad() . 'lga:' . $equipoform->getData()->getLGA()


            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $micro->getCpu()]);
            $incidencia->setInventario($inv[0]);
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'microprocesador']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_mouse2/{id}/{tipo}", name="editar_mouse2")
     * @return Response
     */
    public function editarmouse2Action(Request $request, $id, $tipo)
    {
        $mouse = $this->getDoctrine()->getRepository('AppBundle:mouse')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\mouseType::class, $mouse);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($mouse->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $mouse->getCpu()]);
                //dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $mouse->getCpu()]);
            $incidencia->setInventario($inv[0]);
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'mouse']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_ram2/{id}/{tipo}", name="editar_ram2")
     * @return Response
     */
    public function editarRam2Action(Request $request, $id, $tipo)
    {
        $ram = $this->getDoctrine()->getRepository('AppBundle:ram')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\ramType::class, $ram);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($ram->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $ram->getCpu()]);
                //dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'marca:' . $equipoform->getData()->getMarca() . 'Capacidad:' . $equipoform->getData()->getCapacidad()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $ram->getCpu()]);
            $incidencia->setInventario($inv[0]);
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incidencia);
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'ram']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_teclado/{id}/{tipo}", name="editar_teclado2")
     * @return Response
     */
    public function editarteclado2Action(Request $request, $id, $tipo)
    {
        $teclado = $this->getDoctrine()->getRepository('AppBundle:teclado')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\tecladoType::class, $teclado);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();

            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($teclado->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $teclado->getCpu()]);
                //dump($inv);die();
                $incidencia->setDpto($inv[0]->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $teclado->getCpu()]);
            $incidencia->setInventario($inv[0]);
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'teclado']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_fuente2/{id}/{tipo}", name="editar_fuente2")
     * @return Response
     */
    public function editarfuente2Action(Request $request, $id, $tipo)
    {
        $fuente = $this->getDoctrine()->getRepository('AppBundle:fuente')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\fuenteType::class, $fuente);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();

            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($fuente->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $fuente->getCpu()]);
                // dump($inv);die();
                if ($inv == null) {
                    //dump("holaa");die();
                    $incidencia->setDpto(null);
                } else {
                    // dump("hhh");die();
                    $incidencia->setDpto($inv[0]->getDpto());
                }

            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'marca:' . $equipoform->getData()->getMarca() . 'watts:' . $equipoform->getData()->getWatts() . 'sata' . $equipoform->getData()->getSata()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $inv = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['chasis' => $fuente->getCpu()]);

            if ($inv == null) {
                //dump("holaa");die();
                $incidencia->setInventario(null);
            } else {
                // dump("hhh");die();
                $incidencia->setInventario($inv);
            }

            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'fuente']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_backup2/{id}/{tipo}", name="editar_backup2")
     * @return Response
     */
    public function editarbackup42Action(Request $request, $id, $tipo)
    {
        $impresora = $this->getDoctrine()->getRepository('AppBundle:backup')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\backupType::class, $impresora);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();

            $normalizers = new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer();
            //  $norm = $normalizers->normalize($equipoform->getData());
            //  dump( $equipoform->getData());die();

            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($impresora->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $incidencia->setDpto($impresora->getCpu()->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $incidencia->setInventario($impresora->getCpu());
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'backup']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_monitor2/{id}/{tipo}", name="editar_monitor2")
     * @return Response
     */
    public function editarmonitor2Action(Request $request, $id, $tipo)
    {
        $impresora = $this->getDoctrine()->getRepository('AppBundle:monitor')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\monitorType::class, $impresora);

        $equipoform->add('Guardar', SubmitType::class, array('label' => ' Editar ', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($impresora->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $incidencia->setDpto($impresora->getInventario()->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime('now'));
            $incidencia->setResumen('---');
            $incidencia->setInventario($impresora->getCpu());
            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime('now'));


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'monitor']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_estabilizador2/{id}/{tipo}", name="editar_estabilizador2")
     * @return Response
     */
    public function editarEst2Action(Request $request, $id, $tipo)
    {
        $impresora = $this->getDoctrine()->getRepository('AppBundle:estabilizador')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();


        $equipoform = $this->createForm(\AppBundle\Form\estabilizadorFormType::class, $impresora);

        $equipoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));


        $equipoform->handleRequest($request);

        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $equipoform->getData();


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setEstado('Edicion de equipo');
            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            if (is_null($impresora->getCpu())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $incidencia->setDpto($impresora->getCpu()->getDpto());
            }
            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $date = new \DateTime('now');
            // dump(date_format($date,'d/m/Y'));die();
            $incidencia->setFecha($date);
            $incidencia->setResumen('---');
            if ($impresora->getCpu() == '') {
                $incidencia->setInventario(null);
            } else {
                $incidencia->setInventario($impresora->getCpu());
            }

            $incidencia->setTipoMov('---');
            $incidencia->setEstado('---');
            $incidencia->setAsesorio($tipo);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA($date);


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $id, 'tipo' => 'estabilizador']);
        }

        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', ["form" => $equipoform->createView(), "tipo" => $tipo]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_backup/{id}", name="editar_backup")
     * @return Response
     */
    public function editar_backupAction(Request $request, $id)
    {
        $backup = $this->getDoctrine()->getRepository('AppBundle:backup')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $backupform = $this->createForm(\AppBundle\Form\impresoraFormType::class, $backup);

        $backupform->add('Guardar', SubmitType::class, array('label' => 'Editar Plan', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));

        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $backupform->handleRequest($request);

        if ($backupform->isSubmitted() && $backupform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $backupform->getData();


            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["backupform" => $backupform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_chasis/{id}", name="editar_chasis")
     * @return Response
     */
    public function editar_chasisAction(Request $request, $id)
    {

        $chasis = $this->getDoctrine()->getRepository('AppBundle:cpuchasis')->find($id);
        //dump($chasis);
        // die();
        $entityManager2 = $this->getDoctrine()->getManager();

        $inventario = $entityManager2->getRepository(inventario::class)->Todo($chasis->getId());
        // dump($inventario);die();
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $chasisform = $this->createForm(\AppBundle\Form\chasisFormType::class, $chasis);

        $chasisform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));

        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $chasisform->handleRequest($request);

        if ($chasisform->isSubmitted() && $chasisform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $chasisform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $chasisform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_estabilizador/{id}", name="editar_estabilizador")
     * @return Response
     */
    public function editar_estabilizadorAction(Request $request, $id)
    {
        $estabilizador = $this->getDoctrine()->getRepository('AppBundle:estabilizador')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $estabilizadorform = $this->createForm(\AppBundle\Form\estabilizadorFormType::class, $estabilizador);

        $estabilizadorform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));

        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $estabilizadorform->handleRequest($request);

        if ($estabilizadorform->isSubmitted() && $estabilizadorform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $estabilizadorform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $estabilizadorform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_fuente/{id}", name="editar_fuente")
     * @return Response
     */
    public function editar_fuenteAction(Request $request, $id)
    {
        $fuente = $this->getDoctrine()->getRepository('AppBundle:fuente')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $fuenteform = $this->createForm(\AppBundle\Form\fuenteType::class, $fuente);

        $fuenteform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $fuenteform->handleRequest($request);

        if ($fuenteform->isSubmitted() && $fuenteform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $fuenteform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $fuenteform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_hdd/{id}", name="editar_hdd")
     * @return Response
     */
    public function editar_hddAction(Request $request, $id)
    {
        $hdd = $this->getDoctrine()->getRepository('AppBundle:hdd')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $hddform = $this->createForm(\AppBundle\Form\hddType::class, $hdd);

        $hddform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $hddform->handleRequest($request);

        if ($hddform->isSubmitted() && $hddform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $hddform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $hddform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_lector/{id}", name="editar_lector")
     * @return Response
     */
    public function editar_lectorAction(Request $request, $id)
    {
        $lector = $this->getDoctrine()->getRepository('AppBundle:lector')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\lectorType::class, $lector);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_micro/{id}", name="editar_micro")
     * @return Response
     */
    public function editar_microAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:microprocesador')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\microprocesadorType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_monitor/{id}", name="editar_monitor")
     * @return Response
     */
    public function editar_monitorAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:monitor')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\monitorType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_board/{id}", name="editar_board")
     * @return Response
     */
    public function editar_boardAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:motherboard')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\motherboardType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_mouse/{id}", name="editar_mouse")
     * @return Response
     */
    public function editar_mouseAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:mouse')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\mouseType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }


    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_ram/{id}", name="editar_ram")
     * @return Response
     */
    public function editar_ramAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:ram')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\ramType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_scanner/{id}", name="editar_scanner")
     * @return Response
     */
    public function editar_scannerAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:scanner')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\scannerType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_tarjetaV/{id}", name="editar_tarjetaV")
     * @return Response
     */
    public function editar_tarjetaVAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:tarjeta_video')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\tarjeta_videoType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_teclado/{id}", name="editar_teclado")
     * @return Response
     */
    public function editar_tecladoAction(Request $request, $id)
    {
        $periferico = $this->getDoctrine()->getRepository('AppBundle:teclado')->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id);
        // $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        if (!$inventario) {
            return $this->redirectToRoute('lista_estaciones');
        }

        $perifericoform = $this->createForm(\AppBundle\Form\tecladoType::class, $periferico);

        $perifericoform->add('Guardar', SubmitType::class, array('label' => 'Editar', 'attr' => array('class' => 'btn btn-sm btn-success   fas fa-save')));
        $estabilizador = $entityManager2->getRepository(estabilizador::class)->findBy(['inventario' => $id]);
        $bocina = $entityManager2->getRepository(bocinas::class)->findBy(['inventario' => $id]);
        $chasis = $entityManager2->getRepository(cpuchasis::class)->findBy(['inventario' => $id]);
        $fuente = $entityManager2->getRepository(fuente::class)->findBy(['inventario' => $id]);
        $mother = $entityManager2->getRepository(motherboard::class)->findBy(['inventario' => $id]);
        $micro = $entityManager2->getRepository(microprocesador::class)->findBy(['inventario' => $id]);
        $ram = $entityManager2->getRepository(ram::class)->findBy(['inventario' => $id]);
        $hdd = $entityManager2->getRepository(hdd::class)->findBy(['inventario' => $id]);
        $lector = $entityManager2->getRepository(lector::class)->findBy(['inventario' => $id]);
        $mouse = $entityManager2->getRepository(mouse::class)->findBy(['inventario' => $id]);
        $teclado = $entityManager2->getRepository(teclado::class)->findBy(['inventario' => $id]);
        // $backup = $entityManager2->getRepository(backup::class)->findBy(['inventario' => $id]);
        $impresora = $entityManager2->getRepository(impresora::class)->findBy(['inventario' => $id]);
        $monitor = $entityManager2->getRepository(monitor::class)->findBy(['inventario' => $id]);


        $perifericoform->handleRequest($request);

        if ($perifericoform->isSubmitted() && $perifericoform->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $pagoSNEU = $perifericoform->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pagoSNEU);
            $entityManager->flush();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
        }

        return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ["form1" => $perifericoform->createView(), "inventario" => $inventario, 'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'estabilizador' => $estabilizador]);
    }


    /**
     * @Route("/reportes/agregar_fuente",name="agregar_fuente")
     */
    public function agregarFuenteAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\fuenteType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            //$this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('adicionar_componentes');
        }

        return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
            'form' => $form->createView()));
    }


    /**
     * @Route("/reportes/nuevo_backup",name="nuevo_backup")
     */
    public function agregarBackupAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\backupType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Nuevo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_backup.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'backup'));
    }

    /**
     * @Route("/reportes/nuevo_backup_a_inventario/{id}",name="nuevo_backupI")
     */
    public function agregarBackupIAction(Request $request, $id)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\backupType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setCpu($estacion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_backup.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'backup'));
    }

    /**
     * @Route("/reportes/nuevo_estabilizador",name="nuevo_estabilizador")
     */
    public function agregarestabilizadorAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\estabilizadorFormType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        // $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }

        return $this->render('perifericos/nuevo_estabilizador.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'estabilizador'));
    }

    /**
     * @Route("/reportes/nuevo_estabilizador_a_inventario/{id}",name="nueva_estabilizadorI")
     */
    public function agregarEstabilizadorIAction(Request $request, $id)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\estabilizadorFormType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setCpu($estacion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_estabilizador.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'estabilizador'));
    }

    /**
     * @Route("/reportes/nuevo_impresora",name="nuevo_impresora")
     */
    public function agregarImpresoraAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\impresoraFormType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_impresora.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'impresora'));
    }

    /**
     * @Route("/reportes/nuevo_impresora_a_inventario/{id}",name="nueva_impresoraI")
     */
    public function agregarImpresoraIAction(Request $request, $id)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\impresoraFormType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setCpu($estacion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_impresora.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'impresora'));
    }

    /**
     * @Route("/reportes/nuevo_scanner",name="nuevo_scanner")
     */
    public function agregarScannerAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\scannerType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('ver_datos_periferico');
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_scanner.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'scanner'));
    }


    /**
     * @Route("/reportes/nuevo_mouse",name="nuevo_mouse")
     */
    public function agregarMouseAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\mouseType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('ver_datos_perifericoSI', ['id' => $plan->getId()]);
            //return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_mouse.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'mouse'));
    }

    /**
     * @Route("/reportes/nuevo_scanner_a_inventario/{id}",name="nueva_scannerI")
     */
    public function agregarScannerIAction(Request $request, $id)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\scannerType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setCpu($estacion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_scanner.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'scanner'));
    }

    /**
     * @Route("/reportes/nuevo_monitor",name="nuevo_monitor")
     */
    public function agregarMonitorAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\monitorType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated

            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_monitor.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'monitor'));
    }

    /**
     * @Route("/reportes/nuevo_monitor_a_inventario/{id}",name="nueva_monitorI")
     */
    public function agregarMonitorIAction(Request $request, $id)
    {
        //$private_message = new PlanEconomiaUniv();
        $form = $this->createForm('AppBundle\Form\monitorType');
        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $plan = $form->getData();
            $plan->setEstado('Activo');
            $plan->setCpu($estacion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_monitor.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'monitor'));
    }

    /**
     * @Route("/reportes/nuevo_chasis",name="nuevo_chasis")
     */
    public function agregarChasisAction(Request $request)
    {
        //$private_message = new PlanEconomiaUniv();

        $bocinas = new bocinas();
        $fuente = new fuente();
        $hdd = new hdd();
        $lector = new lector();
        $micro = new microprocesador();
        $board = new motherboard();
        $mouse = new mouse();
        $ram = new ram();
        $scanner = new scanner();
        $tVideo = new tarjeta_video();
        $teclado = new teclado();
        $chasis = new cpuchasis();

        $chasis->getBocina()->add($bocinas);
        $chasis->getFuente()->add($fuente);
        $chasis->getHdd()->add($hdd);
        $chasis->getLector()->add($lector);
        $chasis->getMicro()->add($micro);
        $chasis->getMouse()->add($mouse);
        $chasis->getBoard()->add($board);
        $chasis->getRam()->add($ram);
        $chasis->getScanner()->add($scanner);
        $chasis->getTVideo()->add($tVideo);
        $chasis->getTeclado()->add($teclado);


        $form = $this->createForm('AppBundle\Form\chasisFormType', $chasis);

        //  ,$private_message, array('user' => $this->getUser())
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated

            $plan = $form->getData();
            $plan->setEstado('Activo');
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($plan);

            $entityManager->flush();
            $this->addFlash('success', 'Los datos han sido insertados correctamente');
            return $this->redirectToRoute('lista_equipos');
        }
        $form->add('Guardar', SubmitType::class, array('label' => 'Nuevo'));
        return $this->render('perifericos/nuevo_chasis.html.twig', array(
            'form' => $form->createView(), 'tipo' => 'chasis'));
    }



    ///A continuacion 4 metodos de incidenciaController sin los datos de incidencia ,estos se usaran en la lista de equipos
    /// y dentro de ellos se crearan las incidencias




    /**
     * @Route("reportes/equipos/{id}/{equipo}/recibir2", name="solucionar_sin_incidenciaSI")
     */
    public function recibirSIAction(Request $request, $id, $equipo)
    {

        $entityManager = $this->getDoctrine()->getManager();


        $asesorio = $entityManager->getRepository('AppBundle:equipo')->findBy(['id' => $id]);
//    dump($id);
//    die();
        $i = null;

        return $this->render('incidencia/recibir2.html.twig', ['inventario' => $i, 'nombre' => $equipo, 'equipo' => $asesorio[0]]);
    }



    ///Metodo para equipo que regresa de taller

    /**
     * @Route("reportes/reparacion_equipo/{id_inve}/{id_equi}/{equipo}/{dpto}/reparaciones/", name="equipo_reparaciones")
     */
    public function reparadoAction($id_inve, $id_equi, $equipo, $dpto)
    {
        $entityManager = $this->getDoctrine()->getManager();
        // $query = '';
        $incidencia = '';
        // $id_invetario = $incidencia->getInventario();
        $inventario = $entityManager->getRepository(inventario::class)->Todo($id_inve);
        //$incidencia = $entityManager->getRepository(incidencia::class)->findBy(['inventario'=>$inventario]);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        try {
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.inventario = :ide')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('ide', $id_inve)
                ->andWhere('tabla.asesorio = :asesorio')
                ->setParameter('asesorio', $equipo)
                ->andWhere('tabla.estado = :estado')
                ->setParameter('estado', 'Reparación')
                ->orWhere('tabla.estado=:estado2')
                ->setParameter('estado2', 'Reparacion')
                ->andWhere('tabla.idE=:idEquipo')
                ->setParameter('idEquipo', $id_equi)
//          ->orWhere('tabla.estado = :estado')
//          ->setParameter('estado', 'Envio a taller')
                ->setMaxResults(1)
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            //  ->getSingleResult();
            //     dump($query);die();
            $incidencia = $query;

        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }

//        $incidencia = $query;
//        dump($equipo);
//        dump($incidencia->execute());
//        die();
//
        $tiene = '';
        $asesorio = '';


        $equipo_incidencia = '';
        //$applicationRepository = $entityManager->getRepository('AppBundle:incidencia');


        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);


        $this->addFlash('success', 'Respuesta Procesada Correctamente');
        // return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        return $this->redirectToRoute('incidencia_movimientoR', ['id' => $id_equi, 'id_inci' => $incidencia->execute()[0]->getId()]);


    }


    /**
     * @Route("reportes/reparacion_equipo/{id_equi}/{equipo}/reparaciones1/", name="equipo_reparacionesSI")
     */
    public function reparadoSIAction($id_equi, $equipo)
    {


        $entityManager = $this->getDoctrine()->getManager();


        // $id_invetario = $incidencia->getInventario();
        // $inventario = $entityManager->getRepository(inventario::class)->Todo($id_inve);
        //$incidencia = $entityManager->getRepository(incidencia::class)->findBy(['inventario'=>$inventario]);


        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $periferico = $this->getDoctrine()
            ->getRepository('AppBundle:equipo')->find($id_equi);

        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.idE = :ide')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('ide', $id_equi)
            ->andWhere('tabla.asesorio = :asesorio')
            ->setParameter('asesorio', $equipo)
            ->andWhere('tabla.num_inventario = :numeroI')
            ->setParameter('numeroI', $periferico->getId())
            ->andWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparación')
            ->orWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparacion')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();


        $incidencia = $query;

        //dump($incidencia);die();

        $tiene = '';
        $asesorio = '';


        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);

        $this->addFlash('success', 'Respuesta Procesada Correctamente');
        // return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        return $this->redirectToRoute('movimientoSI', ['id' => $id_equi, 'equipo' => $equipo, 'id_inci' => $incidencia->getId()]);


    }

    /**
     * @Route("reportes/baja_tecnica/{id_equi}/{equipo}/", name="baja_equipo")
     */
    public function bajaTecnicaIAction($id_equi, $equipo)
    {


        $entityManager = $this->getDoctrine()->getManager();


        // $id_invetario = $incidencia->getInventario();
        // $inventario = $entityManager->getRepository(inventario::class)->Todo($id_inve);
        //$incidencia = $entityManager->getRepository(incidencia::class)->findBy(['inventario'=>$inventario]);


        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $periferico = $this->getDoctrine()
            ->getRepository('AppBundle:equipo')->find($id_equi);

//        $query = $repository->createQueryBuilder('tabla')
//            ->where('tabla.idE = :ide')
//            // ->andWhere('tabla.inventario =: idE')
//            ->setParameter('ide', $id_equi)
//            ->andWhere('tabla.asesorio = :asesorio')
//            ->setParameter('asesorio', $equipo)
//            ->andWhere('tabla.num_inventario = :numeroI')
//            ->setParameter('numeroI', $periferico->getId())
//            ->andWhere('tabla.estado = :estado')
//            ->setParameter('estado', 'Reparación')
//            ->orWhere('tabla.estado = :estado')
//            ->setParameter('estado', 'Reparacion')
//            ->setMaxResults(1)
//            ->getQuery()->getOneOrNullResult();

        $periferico->setEstado('Baja Tecnica');
        $periferico->setEstacion(null);
        $periferico->setDepartamento(null);
        $incidencia = new incidencia();
        $incidencia->setEstado('Solucionado');
        $incidencia->setNumInventario($periferico->getNumInventario());
        $incidencia->setIdE($periferico->getId());
        $incidencia->setAsesorio($periferico->getTipoEquipo());
        $incidencia->setAsunto('Baja Tecnica');
        $zonaHoraria = new \DateTimeZone('America/Cuiaba');
        $fecha_actual = new \DateTime('now', $zonaHoraria);
        $incidencia->setFecha($fecha_actual);
        $incidencia->setTipoMov('Baja Tecnica');
        $incidencia->setTipo('Baja Tecnica');
        $incidencia->setTecAsignado($this->getUser());
        $incidencia->setUser($this->getUser());
        $entityManager->persist($periferico);
        $entityManager->persist($incidencia);
        $entityManager->flush();
        // dump($incidencia);die();
        //    $incidencia = $query;

        //dump($incidencia);die();

        $tiene = '';
        $asesorio = '';


        //  $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);

        //     $this->addFlash('success', 'Respuesta Procesada Correctamente');
        return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        // return $this->redirectToRoute('movimientoSI', ['id' => $id_equi, 'equipo' => $equipo, 'id_inci' => $incidencia->getId()]);


    }



    /**
     * @Route("equipo/{id}/{equipo}/resp2",name="equipo_respuestaSI")
     */

    public function respuestaaaSIAction(Request $request, $id, \Swift_Mailer $mailer, $equipo)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $equipo_incidencia = '';
        //$applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        $tipoMov = 'Enviar a taller';
        // dump($equipo);die();

        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id);


        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');


        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.asesorio = :asesorio')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('asesorio', $equipo)
            ->andWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparacion')
            ->setMaxResults(1)
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery()->getOneOrNullResult();


        if ($query == null) {
            $incidencia = '';
            return $this->redirectToRoute('incidencia_movimientoSI43', ['id_equipo' => $equipo_incidencia->getId(), 'equipo' => $equipo]);

        } else {
            $incidencia = $query;
            return $this->redirectToRoute('incidencia_movimientoSI', ['id_equipo' => $equipo_incidencia->getId(), 'equipo' => $equipo, 'id' => $incidencia->getId()]);

        }

    }


    /**
     * @Route("reportes/equipo/{id_inve}/{id_equi}/{name}/{dpto}/{equipo}/reposicion", name="equipo_reposicion")
     * @throws NonUniqueResultException
     */
    public function reposicionAction(Request $request, $id_inve, $id_equi, $name, $dpto, $equipo, \Swift_Mailer $mailer)
    {
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();
        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $idCosto = $inventario->getCentroCosto()->getId();
        $dep = $entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id' => $idCosto]);
        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.idE = :ide')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('ide', $id_equi)
            ->andWhere('tabla.asesorio = :asesorio')
            ->setParameter('asesorio', $equipo)
            ->andWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparación')
            ->orWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparacion')
            ->andWhere('tabla.dpto = :dpto')
            ->setParameter('dpto', $dep[0]->getName())
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if ($query == null) {
            $incidencia = new incidencia();
        } else {
            $incidencia = $query;
        }


        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);
        //dump($eqipo);die();
        $incidencia->setIdE($eqipo->getId());
        $incidencia->setNumInventario($eqipo->getNumInventario());
        $incidencia->setTipo($tipoIncidencia);
        $idCosto = $inventario->getCentroCosto()->getId();
        $incidencia->setInventario($inventario);
        $dep = $entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id' => $idCosto]);
        $incidencia->setDpto($dep[0]->getName());
        $equipoForm = $this->createForm('AppBundle\Form\equipoFomType', $eqipo);
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Reposicion');
        $movimiento->setIncidencia($incidencia);
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setPeriferico($equipo_min);
        $zonaHoraria = new \DateTimeZone('America/Cuiaba');
        $fecha_actual = new \DateTime('now', $zonaHoraria);
        $movimiento->setFechaEntrega($fecha_actual);
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\incidenciaReposicionFormType', $movimiento);
        $equipoForm->handleRequest($request);
        $tipoForm->handleRequest($request);

        //dump($movimiento);die();
        if ($equipoForm->isSubmitted() && $equipoForm->isValid()) {
            $tipo = $entityManager->getRepository('AppBundle:tipo')->find(3);
            $incidencia->setEstado('Reposicion');
            $incidencia->setNumInventario($eqipo->getNumInventario());
            $eqipo = $equipoForm->getData();
            $eqipo->setEstado('Activo');
            $departamentoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $request->request->get('app_bundleincidencia_reposicion_form_type_')['areaDestino']]);
            $estacionDestino = $entityManager->getRepository('AppBundle:inventario')->find($request->request->get('estaciones')[0]);

//            $incidenciaR = new incidencia();
            $incidencia->setTipo($tipo);
            $incidencia->setAsesorio($incidencia->getAsesorio());
            $incidencia->setDpto($departamentoDestino[0]);
            $incidencia->setCorreo($incidencia->getCorreo());
            $incidencia->setFechaA($fecha_actual);
            $incidencia->setFecha($fecha_actual);
            $incidencia->setResumen('Equipo que se repone por uno nuevo');
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setAsunto('Reposicion');
            $incidencia->setEstado('Reposicion');
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setInventario($estacionDestino);
            $incidencia->setIdE($incidencia->getidE());
            $incidencia->setTipoMov('Reposicion');
            $incidencia->setAsesorio($eqipo->getTipoEquipo());
            $incidencia->setNumInventario($incidencia->getNumInventario());
            $incidencia->setRespuesta("Nuevo por reposicion");
            $movimiento->setAreaDestino($departamentoDestino[0]);
            $movimiento->setInventario($estacionDestino);
            $movimiento->setTecnico($this->getUser());
            $eqipo->setDepartamento($departamentoDestino[0]);
//            dump($incidencia);
//
//            dump($dep[0]->getName());
//            dump($incidenciaR);
//            die();
            $eqipo->setEstacion($estacionDestino);
//      dump($eqipo);dump($request->request->get('app_bundleincidencia_reposicion_form_type_')['areaDestino']);
//      dump($departamentoDestino);
//      dump($movimiento);
//    //  dump($request);
//      die();
            $entityManager->persist($incidencia);
            $entityManager->persist($eqipo);
            $entityManager->persist($movimiento);
            $entityManager->flush();


//      if (is_iterable($eqipo)) {
//
//        $em = $this->getDoctrine()->getManager();
//        $qb = $em->createQueryBuilder();
//        $query = $qb->delete('AppBundle:taller', 't')
//          ->where('t.tipo_periferico = :tipo')
//          ->setParameter('tipo', $equipo)
//          ->andWhere('t.id_periferico = :id')
//          ->setParameter('id', $eqipo[0]->getId())
//          ->getQuery();
//        //dump($query);die();
//      } else {
//        // dump("entre");die();
//        $em = $this->getDoctrine()->getManager();
//        $qb = $em->createQueryBuilder();
//        $query = $qb->delete('AppBundle:taller', 't')
//          ->where('t.tipo_periferico = :tipo')
//          ->setParameter('tipo', $equipo)
//          ->andWhere('t.id_periferico = :id')
//          ->setParameter('id', $eqipo->getId())
//          ->getQuery();
//      }
//
//
//      $query->execute();

            //Enviar correo
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Equipo Actualizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        /**
         * End "Post only" section
         */

        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        return $this->render('perifericos/edit.html.twig', ['equipoForm' => $equipoForm->createView(), 'areas' => $areas, 'nombre' => $name, 'equipo' => $eqipo, 'movimientoForm' => $tipoForm->createView(), 'inventario' => $inventario, 'lista_componentes' => $eqipo->getComponente(), 'usuarios' => $usuarios]);
    }


    /**
     * @Route("reportes/equipo/{id_equi}/{name}/{equipo}/reposicion", name="equipo_reposicionSI")
     */
    public function reposicionSIAction(Request $request, $id_equi, $name, $equipo, \Swift_Mailer $mailer)
    {
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();


//    $incidencia = new incidencia();
//    $incidencia->setTipo('Reparacion PC');
//    $incidencia->setEstado('Reposicion');
//    $incidencia->setUser($this->getUser()->getUsername());
//    // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
//    $incidencia->setDpto('');
//    $incidencia->setAsunto('Incidencia propia');
//    $incidencia->setResumen('Equipo que se repone por uno nuevo');
//    $incidencia->setFecha(new \DateTime("now"));
//    $incidencia->setRespuesta('Activo de nuevo');
//    $incidencia->setInventario(null);
//    $incidencia->setTipoMov('Reposicion');
//    $incidencia->setAsesorio($equipo);
//    $incidencia->setTecAsignado($this->getUser());
//    $incidencia->setCorreo($this->getUser()->getEmail());
//    $incidencia->setFechaA(new \DateTime("now"));

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');


        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.idE = :ide')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('ide', $id_equi)
            ->andWhere('tabla.asesorio = :asesorio')
            ->setParameter('asesorio', $equipo)
            ->andWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparacion')
            ->orWhere('tabla.estado = :estado')
            ->setParameter('estado', 'Reparación')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();


        $incidencia = $query;
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');

        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);
        //dump($eqipo);die();
        $incidencia->setNumInventario($eqipo->getNumInventario());
        $incidencia->setTipo($tipoIncidencia);
        $equipoForm = $this->createForm('AppBundle\Form\equipoFomType', $eqipo);
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Reposicion');
        $movimiento->setIncidencia($incidencia);
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setPeriferico($equipo_min);
        $zonaHoraria = new \DateTimeZone('America/Cuiaba');
        $fecha_actual = new \DateTime('now', $zonaHoraria);
        $movimiento->setFechaEntrega($fecha_actual);
        $movimiento->setTecnico($this->getUser());

        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\incidenciaReposicionFormType', $movimiento);
        $equipoForm->handleRequest($request);
        $tipoForm->handleRequest($request);


        if ($equipoForm->isSubmitted() && $equipoForm->isValid()) {
            $incidencia->setEstado('Reposicion');
            $incidencia->setTipoMov('Reposicion');
            $incidencia->setResumen('Equipo que se repone por uno nuevo');
            $departamentoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $request->request->get('app_bundleincidencia_reposicion_form_type_')['areaDestino']]);
            $estacionDestino = $entityManager->getRepository('AppBundle:inventario')->find($request->request->get('estaciones')[0]);
            $eqipo = $equipoForm->getData();
            $eqipo->setEstado('Activo');
            $movimiento->setAreaDestino($departamentoDestino[0]);
            $movimiento->setInventario($estacionDestino);
            $incidencia->setInventario($estacionDestino);
            $eqipo->setDepartamento($departamentoDestino[0]);
            $eqipo->setEstacion($estacionDestino);
            $entityManager->persist($incidencia);
            $entityManager->persist($eqipo);
            $entityManager->persist($movimiento);
            $entityManager->flush();
//      dump($eqipo);dump($request->request->get('app_bundleincidencia_reposicion_form_type_')['areaDestino']);
//      dump($departamentoDestino);
//      dump($movimiento);
//      dump($request);
//      die();
            //Enviar correo
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Equipo Actualizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        /**
         * End "Post only" section
         */
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        // dump($areas);die();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('perifericos/edit.html.twig', ['equipoForm' => $equipoForm->createView(), 'areas' => $areas, 'nombre' => $name, 'equipo' => $eqipo, 'movimientoForm' => $tipoForm->createView(), 'lista_componentes' => $eqipo->getComponente(), 'usuarios' => $usuarios]);


    }


    /**
     * @Route("equipo/{id_inve}/{id_equi}/{name}/{dpto}/traslado", name="equipo_traslado")
     */
    public function trasladoEEquipoAction(Request $request, $id_inve, $id_equi, $name, $dpto, \Swift_Mailer $mailer)
    {
        //dump($dpto);die();
        $entityManager = $this->getDoctrine()->getManager();
        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);
        $movimiento = new movimientoI();
        $movimiento->setRespEntrega($inventario->getResponsable());
        $movimiento->setAreaEntrega($dpto);
        $movimiento->setTecnico($this->getUser());
        $tipoForm = $this->createForm('AppBundle\Form\movimiento3FormType', $movimiento);
        $equipo_min = strtolower($name);

        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);

        //$incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);


        //Creando la incidencia
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        $incidencia = new incidencia();
        $incidencia->setTipo($tipoIncidencia);
        $incidencia->setEstado('Reparacion');
        $incidencia->setUser($this->getUser()->getUsername());
        // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
        $incidencia->setDpto($dpto);
        $incidencia->setAsunto('Traslado Interno');
        $incidencia->setResumen('Equipo que se traslada de departamento');
        $incidencia->setFecha(new \DateTime("now"));
        $incidencia->setRespuesta('Traslado Interno');
        $incidencia->setInventario($entityManager->getRepository('AppBundle:inventario')->find($id_inve));
        $incidencia->setTipoMov('Traslado Interno');
        $incidencia->setEstado('Traslado');
        $incidencia->setAsesorio($equipo_min);
        $incidencia->setIdE($eqipo->getId());
        $incidencia->setNumInventario($eqipo->getNumInventario());
        $incidencia->setTecAsignado($this->getUser());
        $incidencia->setCorreo($this->getUser()->getEmail());
        $incidencia->setFechaA(new \DateTime("now"));
        $movimiento->setIncidencia($incidencia);
        $incidencia->setEstadoMovimiento('Pendiente');

        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        // dump($request);die();
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $tipo = $tipoForm->getData();
            //  dump($request->request->get('estaciones'));
            $id_inventarioDestino = $request->request->get('estaciones');
            $inventarioDestino = $entityManager->getRepository('AppBundle:inventario')->find($id_inventarioDestino[0]);
            //  dump($inventarioDestino);
            $areaEntrega = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $dpto]);
            $nombre_area = $request->request->get('usuarios')[0];
            $id_area = $entityManager->getRepository('AppBundle:area')->findBy(['nombre' => $nombre_area])[0]->getId();
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:departamento');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.area = :Are')
                ->setParameter('Are', $id_area)
                ->andWhere('tabla.idCosto =:costo')
                ->setParameter('costo', $tipo->getAreaDestino())
                ->getQuery();
            $departamentoDestino = $query->getResult();
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $tipo->setAreaDestino($departamentoDestino[0]);
            $tipo->setInventario($inventario);
            // $tipo->setTipoMovimiento("Traslado Interno");
            //  $tipo->setIncidencia($incidencia);
            $tipo->setPeriferico($name);
            $fecha_actual = new \DateTime("now", $zonaHoraria);
            //$id_area_entrega=$tipo->getAreaEntrega();
            //$area_entrega = $entityManager->getRepository('AppBundle:area')->find($tipo->getAreaEntrega()->getId());
            $eqipo->setEstacion($inventarioDestino);
            $eqipo->setDepartamento($departamentoDestino[0]);
            $tipo->setFecha($fecha_actual);
            $tipo->setRespEntrega($inventario->getResponsable());
            $tipo->setAreaEntrega($inventario->getCentroCosto());
            $departamentoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $tipo->getAreaDestino()->getId()]);
            $estacionDestino = $entityManager->getRepository('AppBundle:inventario')->find($request->request->get('estaciones')[0]);
//      dump($tipo);
//      dump($eqipo);
// //     dump($tipo->getAreaDestino());
//      die();

            $eqipo->setEstado('Activo');
            $eqipo->setDepartamento($departamentoDestino);
            $eqipo->setEstacion($estacionDestino);
            $eqipo->setDepartamento($tipo->getAreaDestino());
            //$incidencia->setEstado('Solucionado');
            // dump($eqipo);die();
            $entityManager->persist($tipo);
            $entityManager->persist($movimiento);
            $entityManager->persist($incidencia);
            $entityManager->flush();
            //Enviar correo
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Traslado Interno Realizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);

        }
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        // dump($areas);die();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('incidencia/traslado.html.twig', ['movimientoForm' => $tipoForm->createView(), 'areas' => $areas, 'nombre' => $name, 'asesorio' => $eqipo, 'inventario' => $inventario, 'usuarios' => $usuarios]);

    }


    /**
     * @Route("equipo/{id_equi}/{name}/{equipo}/traslado", name="equipo_trasladoSI")
     */
    public function trasladoEEquipoSIAction(Request $request, $id_equi, $name, $equipo, \Swift_Mailer $mailer)
    {
        $movimiento = new movimientoI();
        $movimiento->setTecnico($this->getUser());
        $tipoForm = $this->createForm('AppBundle\Form\trasladoSIFormType', $movimiento);
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();
        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);
        $departamento = $entityManager->getRepository('AppBundle:departamento')->findAll();

        $tipoForm->handleRequest($request);

        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $tipo = $tipoForm->getData();
            //Creando la incidencia
            $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
            $incidencia = new incidencia();
            $incidencia->setTipo($tipoIncidencia);
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            // $incidencia->setDpto($dpto);
            $incidencia->setAsunto('Incidencia propia');
            $incidencia->setResumen('Equipo que se traslada de departamento');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Traslado Interno');
            $incidencia->setTipoMov('Traslado Interno');
            $incidencia->setEstado('Traslado');
            $incidencia->setAsesorio($equipo);
            $incidencia->setEstadoMovimiento('Pendiente');
            $incidencia->setNumInventario($eqipo->getNumInventario());
            $incidencia->setIdE($eqipo->getId());
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $incidencia->setFechaA(new \DateTime("now", $zonaHoraria));


            $nombre_area = $request->request->get('usuarios')[0];
            $id_area = $entityManager->getRepository('AppBundle:area')->findBy(['nombre' => $nombre_area])[0]->getId();
            //$departamentoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $tipo->getAreaDestino()]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:departamento');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.area = :Are')
                ->setParameter('Are', $id_area)
                ->andWhere('tabla.idCosto =:costo')
                ->setParameter('costo', $tipo->getAreaDestino())
                ->getQuery();
            $departamentoDestino = $query->getResult();
            $tipo->setIncidencia($incidencia);
            $tipo->setAreaDestino($departamentoDestino[0]);
            $tipo->setPeriferico($name);
            $fecha_actual = new \DateTime("now", $zonaHoraria);
            $tipo->setFecha($fecha_actual);
            $eqipo->setEstado('Activo');
            $estacionDestino = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $request->request->get('estaciones')[0]]);
            $incidencia->setInventario($estacionDestino[0]);
            $incidencia->setDpto($departamentoDestino[0]);
            $tipo->setInventario($estacionDestino[0]);
            $eqipo->setDepartamento($departamentoDestino[0]);
            $eqipo->setEstacion($estacionDestino[0]);
//      dump($tipo);
//      dump($incidencia);
//      dump($eqipo);
////      dump($departamentoDestino);
//      die();
            $entityManager->persist($incidencia);
            $tipo->setIncidencia($incidencia);
            $entityManager->persist($tipo);
            $entityManager->flush();
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Traslado Interno Realizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId(), 'tipo' => $equipo_min]);

        }
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        //dump($areas);die();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('incidencia/trasladoSI.html.twig', ['movimientoForm' => $tipoForm->createView(),
            'nombre' => $name, 'areas' => $areas,
            'asesorio' => $eqipo, 'departamento' => $departamento,
            'entity' => $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllUserUms(),
            'usuarios' => $usuarios]);

    }


    /**
     * @Route("/reportes/equipos/filtrar_equipos",name="filtrar_equipos")
     */
    public function filtraEquiposAction(Request $request)
    {
        //dump($request);
        //die();
        ///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }


            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        $num_inventario = $request->get('inventario');
        $id_estacion = $request->get('estaciones');
        $select = $request->request->get('componente');
        $tabla = '';

        if ($select == 1) {
            $tabla = 'backup';
            if ($id_estacion != '' || $num_inventario == '') {
                // $entityManager = $this->getDoctrine()->getManager();
                //  $applicationRepository = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_estacion]);
                //  $estacion = $applicationRepository;
                $em = $this->get('doctrine.orm.entity_manager');
                $dql = "SELECT MAX(a.id),a.marca,a.serie,a.estado,a.numInventario,a.modelo FROM AppBundle:backup a WHERE a.inventario = " . $id_estacion[0] . "";
                $query = $em->createQuery($dql);

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $query,
                    $request->query->getInt('page', 1), 1
                );
                // dump($pagination);
                //die();
                // dump($estacion);
                //  die();
                return $this->render('estacion_trabajo/lista_equipos.html.twig', ['lista' => $pagination, 'areas' => $area]);
            } elseif ($id_estacion = '' || $num_inventario != '') {
                $em = $this->get('doctrine.orm.entity_manager');
                $dql = "SELECT a.id,a.dpto,a.nombreRed,a.estado,a.centroCosto,a.responsable FROM AppBundle:backup a WHERE a.inventario = " . $num_inventario . "";
                $query = $em->createQuery($dql);

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $query,
                    $request->query->getInt('page', 1), 1
                );

                // dump($estacion);
                //  die();
                return $this->render('estacion_trabajo/lista_equipos.html.twig', ['lista' => $pagination, 'areas' => $area, 'componente' => $tabla]);
            }

        } else if ($select == 2) {
            $tabla = 'estabilizador';

        } else if ($select == 3) {
            $tabla = 'cpuchasis';

        } else if ($select == 4) {
            $tabla = 'impresora';

        } else if ($select == 5) {
            $tabla = 'monitor';


        } else
            $this->addFlash('success', 'Usted debe seleccionar un periférico');


        // dump($estacion);
        //  die();
        return $this->addFlash('alert', 'No existe inventario con los datos seleccionados');
    }

    /**
     * @Route("reportes/equipos_salida_a_taller/{maxItemPerPage}",name="equipos_pendientesATaller")
     * @param Request $request
     * @param int $maxItemPerPage
     *
     */
    public function listaPendientesTallerAction(Request $request, $maxItemPerPage = 10)
    {

//    $lista = $this->getDoctrine()
//      ->getRepository('AppBundle:taller')
//      ->findAll();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
        $taller = $this->getDoctrine()
            ->getRepository('AppBundle:inventario')->findBy(['nombreRed' => 'Taller Tecun']);
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.estacion=:estacion')
            ->setParameter('estacion', $taller)
            ->andWhere('tabla.estado=:estado')
            ->setParameter('estado', "Pendiente a taller")
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
//        dump($lista->execute());
//        die();
        $datos = $lista->execute();
        $lista_imprimir = [];
        $cont = 0;
        //   dump($datos);die();
        foreach ($datos as $d) {
            // dump($d->getEstacion()->getCentroCosto());die();
            $lista_imprimir[$cont]['dpto'] = $d->getEstacion()->getCentroCosto();
            $lista_imprimir[$cont]['modelo'] = $d->getModelo();
            $lista_imprimir[$cont]['numI'] = $d->getNumInventario();
            $lista_imprimir[$cont]['tipoEquipo'] = $d->getTipoEquipo();
            $cont = $cont + 1;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $lista->execute(),
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        // dump($lista_imprimir);die();
        return $this->render('reportes/equipos_pendientes_taller.html.twig', array('pagination' => $pagination, 'lista' => $lista, 'inventario' => $taller, 'lista_imprimir' => $lista_imprimir));
    }

    /**
     * @Route("reportes/equipos_de_baja/{maxItemPerPage}",name="equiposDBaja")
     * @param Request $request
     * @param int $maxItemPerPage
     *
     */
    public function listaEquiposEnBajaAction(Request $request, $maxItemPerPage = 10)
    {

//    $lista = $this->getDoctrine()
//      ->getRepository('AppBundle:taller')
//      ->findAll();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
//        $taller = $this->getDoctrine()
//            ->getRepository('AppBundle:inventario')->findBy(['nombreRed' => 'Taller Tecun']);
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.estado=:est')
            ->setParameter('est', 'Baja tecnica')
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
//        dump($lista->execute());
//        die();
        $datos = $lista->execute();
        $lista_imprimir = [];
        $cont = 0;
        //   dump($datos);die();
//        foreach ($datos as $d) {
//            // dump($d->getEstacion()->getCentroCosto());die();
//            $lista_imprimir[$cont]['dpto'] = $d->getEstacion()->getCentroCosto();
//            $lista_imprimir[$cont]['modelo'] = $d->getModelo();
//            $lista_imprimir[$cont]['numI'] = $d->getNumInventario();
//            $lista_imprimir[$cont]['tipoEquipo'] = $d->getTipoEquipo();
//            $cont = $cont + 1;
//        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $lista->execute(),
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        // dump($lista_imprimir);die();
        return $this->render('reportes/equiposBaja.html.twig', array('pagination' => $pagination, 'lista' => $lista));
    }

    /**
     * @Route("reportes/equipos_en_taller/{maxItemPerPage}",name="lista_equipos_taller")
     */
    public function listaEntallerAction(Request $request, $maxItemPerPage = 5)
    {

//
//    $lista = $this->getDoctrine()
//      ->getRepository('AppBundle:taller')
//      ->findAll();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:taller');
        $lista = $repository->createQueryBuilder('tabla')
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $lista,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );

        $fecha = $tipoForm = $this->createForm('AppBundle\Form\fechaSalidaTaller');
        //  $estacionForm = $this->createForm('AppBundle\Form\EstacionForm');
        $fecha->handleRequest($request);
        // dump($fecha);die();
        if ($fecha->isSubmitted() && $fecha->isValid()) {
            //  dump($request);die();


            // $estacion = $fecha->getData();
            //dump($fecha->getData());die();
//      $estacion = $this->getDoctrine()
//        ->getRepository('AppBundle:taller')
//        ->findOneBy(['id' => $fecha->getData()->getidPeriferico()])
//        ->orderby('desc');
//      //     // dump($lista[1]->getId());die();

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:taller');
            $estacion = $repository->createQueryBuilder('tabla')
                ->andWhere('tabla.id =:id')
                ->setParameter('id', $fecha->getData()->getidPeriferico())
                ->orderBy('tabla.fecha', 'desc')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();

            $entityManager = $this->getDoctrine()->getManager();

            // dump($fecha->getData()->getidPeriferico());die();
            $estacion->setFechaSalida($fecha->getData()->getfechaSalida());

            $entityManager->persist($estacion);
            $entityManager->flush();


            return $this->redirectToRoute('lista_equipos_taller');
            /* return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
         //'estacionForm' => $estacionForm->createView(),
         'id'=>$this->idactual

       ));*/

        } else
            //dump($lista_nombres);die();

            return $this->render('reportes/equipos_en_taller.html.twig', array('pagination' => $pagination, 'lista' => $lista, 'form' => $fecha->createView()));
    }


    /**
     * @Route("/reportes/estacion/filter2",name="equipo_filter2")
     */
    public function filter2EquipoAction(Request $request)
    {
        // dump($request);
        // die();
        ///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }


            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        $area1 = $request->request->get('usuarios');
        $dep = $request->request->get('departamentos');
        $estacion = $request->request->get('estaciones');
        $numInv = $request->request->get('tipo');
        //dump($numInv);die();
        $entityManager = $this->getDoctrine()->getManager();
        $equipo = $request->request->get('componente');


        $entityManager = $this->getDoctrine()->getManager();


        if ($estacion == null or $numInv == null or $dep == null or $equipo == 0) {
            $this->addFlash('alerta', 'Usted debe seleccionar uno de los filtros para buscar');
        } elseif ($estacion == '' && $numInv != '' && $dep == '' && $equipo != 0) {

            dump('entr');
            die();
            $id_estacionT = $entityManager->getRepository('AppBundle:inventario')->find($estacion[0])->getId();


            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:' . $equipo);


            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.inventario = :numI')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('numI', $id_estacionT)
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            $products = $query->getResult();


            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                10
            );


            return $this->render(
                'estacion_trabajo/lista_estaciones.html.twig', ['areas' => $area,
                'lista' => $products,
                'componente' => $this->tipo,
                'pagination' => $pagination,

            ]);

        } else

            $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:backup');
        $inventarios = $applicationRepository->findAll();


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $inventarios,
            $request->query->getInt('page', 1),
            10
        );
        //dump($pagination);
        // die();
        return $this->render(
            'estacion_trabajo/lista_equipos.html.twig', [
                "filters" => $this->filters,
                'areas' => $area,

                'inventarios' => '',
                'incidencias' => $inventarios,
                "pagination" => $pagination,
            ]

        );


    }


    /**
     * @Route("/reportes/estacion/filter56",name="equipo_filter3")
     */
    public function filter56EquipoAction(Request $request)
    {

        ///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }


            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        $area2 = $request->request->get('usuarios');
        $dep = $request->request->get('departamentos');
        $estacion = $request->request->get('estaciones');
        $numInv = $request->request->get('tipo');
        //dump($numInv);die();
        $entityManager = $this->getDoctrine()->getManager();
        $select = $request->request->get('componente');


        if ($select != 0 && $dep != '' && $estacion != '' && $numInv == '') {

            return $this->redirectToRoute('filtro_equipo1', ['select' => $select, 'numInv' => $numInv]);

        } elseif ($select != 0 && $dep != '' && $estacion != '') {

            return $this->redirectToRoute('filtro_equipo2', ['select' => $select, 'numInv' => $numInv]);
        }


    }

    /**
     * @Route("/reportes/equipo/filtro_equipoeInv",name="filtro_equipo1")
     */
    public function filtro1Equipo($select, $numInv)
    {


        //  $estacion = $applicationRepository;
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = "SELECT a.dpto,a.num_inventario,a.marca,a.modelo,a.estado FROM AppBundle: . $select a WHERE a.num_inventario = " . $numInv . "";
        $query = $em->createQuery($dql);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            //$request->query->getInt('page', 1),
            10
        );


        return $this->render('estacion_trabajo/lista_equipos.html.twig', [
            "areas" => '',
            'lista' => '',
            'componente' => $select,
            "pagination" => $pagination,
        ]);


    }


    /**
     * @Route("/reportes/equipo/filtro_equipoyestacion",name="filtro_equipo1")
     */
    public function filtro2Equipo($select, $numInv)
    {


        $id_estacionT = $entityManager->getRepository('AppBundle:inventario')->find($estacion[0])->getId();

        if ($this->tipo == 'board') {
            $this->tipo = 'motherboard';
        }

        if ($this->tipo == 'micro') {
            $this->tipo = 'microprocesador';
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:' . $this->tipo);


        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.inventario = :numI')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('numI', $id_estacionT)
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
        $products = $query->getResult();


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render(
            'estacion_trabajo/lista_estaciones.html.twig', array("areas" => $area,
            'lista' => $products,
            'componente' => $this->tipo,
            "pagination" => $pagination,

        ));


    }


    /**
     * @Route("/reportes/equipos/filtrar_equipos2",name="filtra_equiposNI")
     */
    public function filtrarEquipoNIAction(Request $request)
    {

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $em = $this->getDoctrine()->getManager();
            //  $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            //  $estacion = $applicationRepository;
//            $dql = "SELECT a FROM AppBundle:equipo a WHERE a.numInventario = " .$num . "";
//            $query = $em->createQuery($dql);


            $qb = $em->createQueryBuilder();
            $result = $qb->select('e')->from('AppBundle\Entity\equipo', 'e')
                ->where($qb->expr()->like('e.numInventario', $qb->expr()->literal('%' . $num . '%')))
                ->getQuery();

//       dump($equipos->getTipoEquipo());
//       die();
            // dump($applicationRepository);
            //  die();
            /* $paginator = $this->get('knp_paginator');
       $pagination = $paginator->paginate(
         $equipos,
         $request->query->getInt('page', 1),1
       );*/
            // dump( $applicationRepository->getId());
            //  die();
//       dump($equipos);
//        die();

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $result,
                $request->query->getInt('page', 1), 1
            );
            //     dump($pagination);die();
            return $this->render('estacion_trabajo/lista_equipos.html.twig', ['lista' => '', 'areas' => $area, 'pagination' => $pagination, 'dir' => '', 'orden' => '']);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('lista_equipos');
    }

    /**
     * @Route("/reportes/equipos/filtrar_estacion/",name="filtraEstacionXNI")
     */
    public function filtrarEstacionNIAction(Request $request)
    {

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
        $inventarios = $applicationRepository->findAll();
        $estacionesVacias = new ArrayCollection();
        $componentesBasicos = ['cpuchasis', 'monitor', 'backup'];
        foreach ($inventarios as $i) {
            $contChasis = 0;
//            $contBackup=0;
//            $contMonitor=0;
            foreach ($i->getEquipos() as $equipo) {
                // dump($equipo);
                if ($equipo->getTipoEquipo() == $componentesBasicos[0]) {
                    $contChasis = $contChasis + 1;
                }
//                if($equipo->getTipoEquipo()==$componentesBasicos[1]){
//                    $contMonitor=$contMonitor+1;
//                }   if($equipo->getTipoEquipo()==$componentesBasicos[2]){
//                    $contBackup=$contBackup+1;
//                }
            }
            if ($contChasis == 0) {
                $estacionesVacias->add($i);
                //   $estacionesVacias->add('chasis');
            }
//           if ($contBackup==0){
//                $estacionesVacias->add($i);
//                $estacionesVacias->add('backup');
//            }
//            if($contMonitor==0){
//                $estacionesVacias->add($i);
//                $estacionesVacias->add('monitor');
//            }
            // die();
        }

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {

            $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            if ($equipo != null) {
                $estacion = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $equipo->getEstacion()]);
                if ($estacion != null) {
                    $paginator = $this->get('knp_paginator');
                    $pagination = $paginator->paginate(
                        $estacion,
                        $request->query->getInt('page', 1),
                        10
                    );
                    $areas = $entityManager->getRepository('AppBundle:area')->findAll();
                    return $this->render('estacion_trabajo/lista_estaciones.html.twig', ['lista' => $estacion[0], 'pagination' => $pagination, 'areas' => $areas, 'estacionesSinChasis' => $estacionesVacias]);

                } else {
                    $this->addFlash('alerta', 'Ninguna estacion contiene el equipo con el numero de inventario especificado');
                }
            } else {
                $this->addFlash('alerta', 'No existe el numero especificado');
            }

        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('lista_estaciones');
    }


    /**
     * @param Request $request
     * @Route("/reportes/equipos/deudaequipo",name="deudaEquipo")
     */
    public function declararDeudaEquipoAction(Request $request)
    {
        dump($request);
        die();
        return $this->redirectToRoute('deudaEquipo');
    }


    /**
     * @param inventario $inventario
     * @Route("/reportes/equipos/buscar_equipo/{inventario}",name="buscar_equipo")
     */
    public function buscarparaAnnadirEquipoEditarAction(Request $request,  $inventario)
    {

        $num = $request->get('numI');
//        dump($inventario);
//        die();
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            // dump($inventario);die();
            if ($equipos == null) {
                $this->addFlash('error', 'No se ha registrado ningun equipo con este numero de inventario');
                return $this->redirectToRoute('editar_inventario', ['id' => $inventario, 'nuevo' => '']);
            } else {

                return $this->redirectToRoute('editar_inventario', ['id' => $inventario, 'nuevo' => $equipos->getNumInventario()]);
            }


            //  return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ['lista' => $equipos,'inventario'=>$inventario]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('editar_inventario');
    }

    /**
     * @param inventario $inventario
     * @Route("/reportes/equipos/buscar_equipo_nuevo_inventario/{inventario}",name="buscar_equipo_nuevo_inventario")
     */
    public function buscarparaAnnadirEquipoNuevoInventarioAction(Request $request, inventario $inventario)
    {

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            // dump($inventario);die();
            if ($equipos == null) {
                $this->addFlash('error', 'No se ha registrado ningun equipo con este numero de inventario');
                return $this->redirectToRoute('nuevo_inventario_estacion', ['id' => $inventario->getId(), 'nuevo' => '']);
            } else {
                return $this->redirectToRoute('nuevo_inventario_estacion', ['id' => $inventario->getId(), 'nuevo' => $equipos->getNumInventario()]);
            }


            //  return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ['lista' => $equipos,'inventario'=>$inventario]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('nuevo_inventario_estacion');
    }

    /**
     * @param inventario $inventario
     * @Route("/reportes/equipos/buscar_equipo_nuevo_inventario2/",name="buscar_equipo_nuevo_inventario2")
     */
    public function buscarparaAnnadirEquipoNuevoInventario2Action(Request $request)
    {

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            // dump($inventario);die();
            if ($equipos == null) {
                $this->addFlash('error', 'No se ha registrado ningun equipo con este numero de inventario');
                return $this->render('estacion_trabajo/nueva_estacion.html.twig', ['nuevo' => '']);
                //  return $this->redirectToRoute('nuevo_inventario_estacion', ['id' => $inventario->getId(), 'nuevo' => '']);
            } else {
                return $this->render('estacion_trabajo/nueva_estacion.html.twig', ['nuevo' => '']);
                // return $this->redirectToRoute('nuevo_inventario_estacion', ['id' => $inventario->getId(), 'nuevo' => $equipos->getNumInventario()]);
            }


            //  return $this->render('estacion_trabajo/editar_datos_estacion.html.twig', ['lista' => $equipos,'inventario'=>$inventario]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('nuevo_inventario_estacion');
    }


    /**
     * @Route("/reportes/equipos/filtrar_equipostaller",name="filtra_equipostaller")
     */
    public function filtrarEquipoTallerAction(Request $request)
    {

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:taller')->findOneBy(['numInventario' => $num]);
            //  dump($equipos);die();
            if ($equipos != null) {
                return $this->render('reportes/equipos_en_taller.html.twig', ['lista' => $equipos]);
            } else {
                $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
            }
        }


        return $this->redirectToRoute('lista_equipos_taller');
    }

    /**
     * @Route("/reportes/equipos/filtraEquiposBaja",name="filtraEquiposBaja")
     */
    public function filtrarEquipoBajaAction(Request $request)
    {

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            //    $equipos = $entityManager->getRepository('AppBundle:taller')->findOneBy(['numInventario' => $num]);

            $repository2 = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $equiposDBaja = $repository2->createQueryBuilder('tabla')
                ->where('tabla.numInventario = :numI')
                ->setParameter('numI', $num)
                ->andWhere('tabla.estado=:est')
                ->setParameter('est', 'Baja Tecnica')
                ->getQuery();

            //   dump($equiposDBaja->execute());die();
            if ($equiposDBaja != []) {
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $equiposDBaja,
                    $request->query->getInt('page', 1)
                );
                return $this->render('reportes/equiposBaja.html.twig', ['lista' => $equiposDBaja->getResult(), 'pagination' => $pagination]);
            } else {
                $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
            }
        }


        return $this->redirectToRoute('equiposDBaja');
    }

    /**
     * @Route("/reportes/equipos/filtrar_equiposDeudataller",name="filtra_equipos_deudataller")
     */
    public function filtrarEquipoDeudaTallerAction(Request $request)
    {

        $num = $request->get('numI');
        // dump($num);die();
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();// $equipos = $entityManager->getRepository('AppBundle:taller')->findOneBy(['numInventario' => $num]);

            $chasisActual = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $num]);

            if ($chasisActual != null) {

                $repository2 = $this->getDoctrine()
                    ->getRepository('AppBundle:componente');
                $piezasDeuda = $repository2->createQueryBuilder('tabla')
                    ->where('tabla.cpu = :chasis')
                    ->setParameter('chasis', $chasisActual)
                    ->andWhere('tabla.deuda=:tiene')
                    ->setParameter('tiene', 'Si')
                    ->getQuery();

                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:incidencia');
                $lista = $repository->createQueryBuilder('tabla')
                    ->where('tabla.tipo=:tipo')
                    ->setParameter('tipo', 'Pc defectuosa')
                    ->andWhere('tabla.respuesta=:resp')
                    ->setParameter('resp', 'Pc defectuosa')
                    ->andWhere('tabla.estado=:estadoIncidencia')
                    ->setParameter('estadoIncidencia', 'Activa')
                    ->andWhere('tabla.num_inventario=:numero')
                    ->setParameter('numero', $num)
                    ->getQuery();
                //   dump($piezasDeuda);die();
                if ($piezasDeuda != null) {
                    $paginator = $this->get('knp_paginator');
                    $pagination = $paginator->paginate(
                        $lista,
                        $request->query->getInt('page', 1)
                    );
                    return $this->render('reportes/deudas_taller.html.twig', ['lista' => $lista->getResult(), 'pagination' => $pagination]);
                } else {
                    $this->addFlash('nohay', 'Este chasis no tiene deudas');
                }

            } else {
                $this->addFlash('nohay', 'No existe equipo con el numero de inventario especificado');
            }
        }

        return $this->redirectToRoute('lista_deudas_taller');
    }


    /**
     * @Route("/reportes/equipos/filtrar_equiposAlmacen",name="filtra_equiposAlmacen")
     */
    public function filtrarEquipoAlmacenAction(Request $request)
    {
        $num = $request->get('numI');
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '' or $num == null) {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.numInventario = :numI')
                ->setParameter('numI', $num)
                ->andWhere('tabla.estacion=:inventario')
                ->setParameter('inventario', 3861)
                ->getQuery();
            $equipos = $query->execute();
            //   dump($equipos);die();
            // $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            if ($equipos != null) {
                return $this->render('reportes/equipos_en_almacen.html.twig', ['lista' => $equipos[0]]);
            } else {
                $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
            }

        }

        return $this->redirectToRoute('lista_equipos_en_almacen');
    }

    /**
     * @Route("/reportes/equipos/filtrar_equipos_pendientesTaller",name="filtra_equipo_pendientesTaller")
     */
    public function filtrarEquipoPendientesTallerAction(Request $request)
    {
        $num = $request->get('numI');
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '' or $num == null) {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.numInventario = :numI')
                ->setParameter('numI', $num)
                ->andWhere('tabla.estacion=:inventario')
                ->setParameter('inventario', 3889)
                ->andWhere('tabla.estado=:estadoPendiente')
                ->setParameter('estadoPendiente', "Pendiente a taller")
                ->getQuery();
            $equipos = $query->execute();
            $lista_imprimir = [];
            $cont = 0;
            foreach ($equipos as $d) {
                $lista_imprimir[$cont]['dpto'] = $d->getDepartamento()->getName();
                $lista_imprimir[$cont]['modelo'] = $d->getModelo();
                $lista_imprimir[$cont]['numI'] = $d->getNumInventario();
                $lista_imprimir[$cont]['tipoEquipo'] = $d->getTipoEquipo();
                $cont = $cont + 1;
            }
            // dump($equipos);die();
            // $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            if ($equipos != null) {
                return $this->render('reportes/equipos_pendientes_taller.html.twig', ['lista' => $equipos[0], 'lista_imprimir' => $lista_imprimir]);
            } else {
                $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
            }

        }

        return $this->redirectToRoute('equipos_pendientesATaller');
    }

    /**
     * @Route("/reportes/equipos/filtrar_equipos_incidencia",name="filtra_equiposNI_Incidencia")
     */
    public function filtrarEquipoNI_IncidenciaAction(Request $request)
    {
        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            dump($equipos);
            die();
            return $this->render('incidencia/new.html.twig', ['lista' => $equipos]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('incidencia_new');
    }


    /**
     * @Route("/reportes/equipos/filtrar_equiposPA/{ideEst}",name="filtra_equiposxAdicionar")
     */
    public function filtrarEquipoNIPAAction(Request $request, $ideEst)
    {
//
/////Obtener departamentos
//        /**
//         * Configurar conexion al assets desde Windows (Liuben)
//         */
//        $serverName = "premium.cicc.cu";
//        $database = "RETINOSIS";
//        $uid = 'user_assetsp';
//        $pwd = '2020*Fuerza';
//        try {
//            $coneccionAssets = new \PDO(
//                "sqlsrv:server=$serverName;Database=$database",
//                $uid,
//                $pwd,
//                array(
//                    //PDO::ATTR_PERSISTENT => true,
//                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
//                )
//            );
//
//            /**
//             * Consulta para obtener los centros de costos
//             */
//            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
//            $query = $coneccionAssets->query($sql);
//            $idcosto = 0;
//            if ($query) {
//                $area = array();
//
//                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
//                    $area[] = $var;
//                    $idcosto = $area[0]['Id_Ccosto'];
//                }
//            }
//
//            // dump($dep);die();
//        } catch (\PDOException $e) {
//            die("No se conecta con el servidor! - " . $e->getMessage());
//        }
//

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            //  $estacion = $applicationRepository;
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            try {
                $query = $repository->createQueryBuilder('tabla')
                    ->where('tabla.numInventario = :ide')
                    // ->andWhere('tabla.inventario =: idE')
                    ->setParameter('ide', $num)
                    ->andWhere('tabla.estacion = :estacion')
                    ->setParameter('estacion', null)
                    ->setMaxResults(1)
                    // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                    ->getQuery()->getSingleResult();
            } catch (NoResultException $e) {

            } catch (NonUniqueResultException $e) {
            }
            // dump($equipos);
            // die();
            // dump($applicationRepository);
            //  die();
            /* $paginator = $this->get('knp_paginator');
       $pagination = $paginator->paginate(
         $equipos,
         $request->query->getInt('page', 1),1
       );*/
            // dump( $applicationRepository->getId());
            //  die();
            // dump($estacion);
            //  die();
            $inventario = $entityManager->getRepository('AppBundle:inventario')->find($ideEst);
            $area = $entityManager->getRepository('AppBundle:area')->findAll();
            // dump($equipos);die();
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig', ['incidencias' => $equipos, 'areas' => $area, 'nombre_estacion' => $inventario->getNombreRed(), 'idestacion' => $inventario->getId(), 'lista']);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('adicionar_componentes');
    }


    /**
     * @Route("/reportes/equipos/filtrar_equiposPA2/{ideEst}/{incidencia_id}",name="filtra_equiposxAdicionarNI")
     */
    public function filtrarEquipoNI2PAAction(Request $request, $ideEst, $incidencia_id)
    {

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        // dump($incidencia);die();

        $num = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $num]);
            //  $estacion = $applicationRepository;
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            try {
                $query = $repository->createQueryBuilder('tabla')
                    ->where('tabla.numInventario = :ide')
                    // ->andWhere('tabla.inventario =: idE')
                    ->setParameter('ide', $num)
                    ->andWhere('tabla.estacion = :estacion')
                    ->setParameter('estacion', null)
                    ->setMaxResults(1)
                    // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                    ->getQuery()->getSingleResult();
            } catch (NoResultException $e) {

            } catch (NonUniqueResultException $e) {
            }
            // dump($equipos);
            // die();
            // dump($applicationRepository);
            //  die();
            /* $paginator = $this->get('knp_paginator');
       $pagination = $paginator->paginate(
         $equipos,
         $request->query->getInt('page', 1),1
       );*/
            // dump( $applicationRepository->getId());
            //  die();
            // dump($estacion);
            //  die();
            $inventario = $entityManager->getRepository('AppBundle:inventario')->find($ideEst);
            $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($incidencia_id);
            $lista_equipos = $entityManager->getRepository('AppBundle:equipo')->findBy(['estacion' => $inventario->getId()]);
            // dump($equipos);die();
            return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['incidencias' => $equipos, 'areas' => $area, 'nombre_estacion' => $inventario->getNombreRed(), 'idestacion' => $inventario->getId(), 'lista', 'lista_equipos' => $lista_equipos, 'incidencia' => $incidencia]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('adicionar_componentes_NI');
    }

    /**
     * @Route("/reportes/equipos/filtrar_equiposAdicionar",name="filtra_equiposxAdicionarF")
     * @param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function filtrarEquipoAdionarAction(Request $request)
    {
        //  dump($request);
        //  dump($request);die();
///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */

        // dump($incidencia);die();

        $num = $request->query->get('numI');
        $responseArray = array();
        $idEstacion = $request->query->get('idEst');
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
//        if ($num == '') {
//            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
//        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            //  $equipos = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $num]);
            // $responseArray = null;
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $equipos = $repository->createQueryBuilder('tabla')
                ->where('tabla.numInventario = :ide')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('ide', $num)
                ->andWhere('tabla.estacion is Null')
                //  ->setParameter('estacionE', '')
                ->getQuery()->getResult();
            //  ->getResult();
//            dump($num);
//            dump($equipos->execute());
//            die();

            if ($equipos != []) {
                foreach ($equipos as $equipo) {
                    $responseArray[] = array(
                        "numI" => $equipo->getNumInventario(),
                        "modelo" => $equipo->getModelo(),
                        "tipoEquipo" => $equipo->getTipoEquipo()
                    );
                }
            }

        }
        //  dump($responseArray[0]);die();
        return new JsonResponse($responseArray[0]);
        // return $this->render('estacion_trabajo/nuevosEquiposAInventario.html.twig', ['incidencias' => $equipos, 'areas' => $area, 'nombre_estacion' => $inventario->getNombreRed(), 'idestacion' => $inventario->getId(), 'lista', 'lista_equipos' => $lista_equipos]);
//        } else
//            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
//        return $this->redirectToRoute('annadirAInv');

    }

    /**
     * @Route("/reportes/equipos/fecha_salidaTaller",name="cambiarfechaTaller")
     */
    public function cambiarSalidaEquipoTallerAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
//        die();
        $fechaSalidaTaller = $request->query->get('fechaS');
        $id = $request->query->get('idEquipo');
        $equipo = $this->getDoctrine()
            ->getRepository('AppBundle:equipo')->findBy(['id' => $id]);
        $taller = $this->getDoctrine()
            ->getRepository('AppBundle:inventario')->findBy(['nombreRed' => 'Taller Tecun'])[0];
        $equipo[0]->setEstado('En taller');
        $equipo[0]->setEstacion($taller);

        $existe = $this->getDoctrine()
            ->getRepository('AppBundle:taller')->findBy(['numInventario' => $equipo[0]->getNumInventario()]);

        if (!$existe) {
            $aTaller = new taller();
            $aTaller->setNumInventario($equipo[0]->getNumInventario());
            $aTaller->setDpto($equipo[0]->getDepartamento());
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime($fechaSalidaTaller, $zonaHoraria);
            $aTaller->setFechaSalida($fecha_actual);
            $aTaller->setTipoPeriferico($equipo[0]->getTipoEquipo());
            $aTaller->setIdPeriferico($equipo[0]->getId());
            $aTaller->setDpto($equipo[0]->getEstacion()->getCentroCosto());
            $aTaller->setModelo($equipo[0]->getModelo());
            //  dump($aTaller);
            $entityManager->persist($aTaller);
        }

        $entityManager->persist($equipo[0]);

        //  dump($aTaller);die();
        //   var_dump($fechaSalidaTaller);
        $entityManager->flush();

        $responseArray = array();

        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/reportes/equipos/filtrar_componentes",name="filtrar_componentesNS")
     */
    public function filtrarComponentesNIAction(Request $request)
    {

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        $serie = $request->get('numI');

        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($serie == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($serie != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $equipos = $entityManager->getRepository('AppBundle:componente')->findOneBy(['serie' => $serie]);
            //  $estacion = $applicationRepository;

            //  dump($equipos);
            //  die();
            // dump($applicationRepository);
            //  die();
            /* $paginator = $this->get('knp_paginator');
       $pagination = $paginator->paginate(
         $equipos,
         $request->query->getInt('page', 1),1
       );*/
            // dump( $applicationRepository->getId());
            //  die();
            // dump($estacion);
            //  die();
            return $this->render('estacion_trabajo/lista_componentes.html.twig', ['lista' => $equipos, 'areas' => $area]);
        } else

            $this->addFlash('alert', 'No existe componente con el numero de serie especificado');
        return $this->redirectToRoute('lista_componentes');
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/salida_almacenE/{numI}", name="salida_almacenE")
     * @return Response
     */
    public function salidaAlmacenAction(Request $request, $numI)
    {
        $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
        $entityManager2 = $this->getDoctrine()->getManager();


//dump($equipo);die();
        $equipo[0]->setEstacion(null);
        $equipo[0]->setEstado('Activo');
        $entityManager2->persist($equipo[0]);
        $entityManager2->flush();

//        $em = $this->getDoctrine()->getManager();
//        $qb = $em->createQueryBuilder();
//        $query = $qb->delete('AppBundle:taller', 't')
//            ->where('t.numInventario = :numIn')
//            ->setParameter('numIn', $equipo[0]->getNumInventario())
//            ->getQuery();
//        $query->execute();
//        dump($query->execute());
//        die();


        return $this->redirectToRoute('lista_equipos_en_almacen');
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/salida_taller/{numI}", name="salidaTaller")
     * @return Response
     */
    public function salidaTallerAction(Request $request, $numI)
    {
        $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
        $entityManager2 = $this->getDoctrine()->getManager();


//dump($equipo);die();
        $equipo[0]->setEstacion(null);
        $equipo[0]->setEstado('Activo');
        $entityManager2->persist($equipo[0]);
        $entityManager2->flush();

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->delete('AppBundle:taller', 't')
            ->where('t.numInventario = :numIn')
            ->setParameter('numIn', $equipo[0]->getNumInventario())
            ->getQuery();
        $query->execute();
//        dump($query->execute());
//        die();


        return $this->redirectToRoute('lista_equipos_taller');
    }

    /**
     * @Route("reportes/informes/partes_piezas",name="reportePP")
     */
    public function reportePartesPiezasAction()
    {

        return $this->render('reportes/partes_piezas.html.twig');
    }

    /**
     * @Route("reportes/informes/reportePartesPiezas",name="generar_reportePP")
     */
    public function reportePiezasPDFAction(Request $request)
    {
//        dump($request);
//        die();
        //  $form = " FROM AppBundle:equipo as eq INNER JOIN AppBundle:inventario as i ON eq.estacion_id = i.id";
        $form = " FROM inventario i ";
        $select = " i.nombre_red";
        $where = "";
        $columnas = array('Nombre Red', 'Numero de  inventario');
        $columSelect = array('nombre_red');
        $em = $this->get('doctrine.orm.entity_manager');
        ///Buscar equipos
        /// Chasis
        if (array_key_exists("cpuChasisPartesInventario", $_POST)) {
            $form .= " INNER JOIN equipo eq ON eq.estacion_id = i.id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= "eq.tipo_equipo='cpuchasis'";
            $select .= ",eq.num_inventario as numInventarioCPU";

            //    $where .= "eq.tipo_equipo='cpuchasis'";
            //  if (array_key_exists("cpuChasisPartesInventario", $request)) {
            $modeloDatosCPUChasis = $_POST["modeloDatosCPUChasis"];
            $selloSegDatosCPUChasis = $_POST["selloSegDatosCPUChasis"];
            $colorDatosCPUChasis = $_POST["colorDatosCPUChasis"];
            if ($_POST["fechaMantDatosCPUChasis"] === "")
                $fechaMantDatosCPUChasis = "";
            else {
                $fechaMantDatosCPUChasis = new DateTime($_POST["fechaMantDatosCPUChasis"]);
                $fechaMantDatosCPUChasis = $fechaMantDatosCPUChasis->format('Y-m-d');
            }

            if ($modeloDatosCPUChasis !== "Seleccione") {
                $select .= ",eq.modelo as modeloCPU";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= " eq.modelo = '$modeloDatosCPUChasis'";
                $columnas[] = 'Modelo CPU';
                $columSelect[] = 'modelo';
            }
            if ($colorDatosCPUChasis !== "") {
                $select .= ",eq.color as colorCPU";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "eq.color LIKE " . "'%" . $colorDatosCPUChasis . "%'";
                $columnas[] = 'Color CPU';
                $columSelect[] = 'color';
            }
            if ($selloSegDatosCPUChasis !== "") {
                $select .= ",eq.sello as selloCPU";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "eq.sello LIKE " . "'%" . $selloSegDatosCPUChasis . "%'";
                $columnas[] = 'Sello CPU';
                $columSelect[] = 'sello';
            }
            if ($fechaMantDatosCPUChasis !== "") {
                $select .= ",eq.fecha_mantenimiento as fechaMantenimientoCPU";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "eq.fecha_mantenimiento = '$fechaMantDatosCPUChasis'";
                $columnas[] = 'Fecha Mtto.';
                $columSelect[] = 'fecha_mantenimiento';
            }
        }


        ///Monitor
        if (array_key_exists("monitorPartesInventario", $_POST)) {
            // dump("monitor");
            $form .= " INNER JOIN equipo as m ON i.id = m.estacion_id";
            $where = 'm.tipo_equipo="monitor"';
            $select .= ",m.num_inventario as numInventarioM";
            $marcaDatosMonitor = $_POST["marcaDatosMonitor"];
            $serieDatosMonitor = $_POST["serieDatosMonitor"];
            $modeloDatosMonitor = $_POST["modeloDatosMonitor"];
            $inventarioDatosMonitor = $_POST["inventarioDatosMonitor"];
            $LCDDatosMonitor = $_POST["LCDDatosMonitor"];

            if ($marcaDatosMonitor !== "Seleccione") {
                $select .= ",m.marca as marcaMonitor";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "m.marca = '$marcaDatosMonitor'";
                $columnas[] = 'Marca Monitor';
            }
            if ($serieDatosMonitor !== "") {
                $select .= ",m.serie as serieMonitor";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "m.serie LIKE " . "'%" . $serieDatosMonitor . "%'";
                $columnas[] = 'Serie Monitor';
            }
            if ($modeloDatosMonitor !== "") {
                $select .= ",m.modelo as modeloMonitor";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "m.modelo LIKE " . "'%" . $modeloDatosMonitor . "%'";
                $columnas[] = 'Modelo Monitor';
            }
            if ($inventarioDatosMonitor !== "") {
                $select .= ",m.num_inventario as numIMonitor";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "m.num_inventario LIKE " . "'%" . $inventarioDatosMonitor . "%'";
                $columnas[] = 'No. Inv. Monitor';
            }
            if ($LCDDatosMonitor !== "Seleccione") {
                $select .= ",m.lcd as lcdMonitor";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "m.lcd = '$LCDDatosMonitor'";
                $columnas[] = 'Monitor LCD';
            }
        }
//Backup
        if (array_key_exists("backupPartesInventario", $_POST)) {
            $form .= " INNER JOIN `equipo` as b ON i.id = `b`.estacion_id";
            $where = 'b.tipo_equipo="backup"';
            $select .= ",b.num_inventario as numInventarioB";
            $marcaDatosBackup = $_POST["marcaDatosBackup"];
            $serieDatosBackup = $_POST["serieDatosBackup"];
            $modeloDatosBackup = $_POST["modeloDatosBackup"];
            $inventarioDatosBackup = $_POST["inventarioDatosBackup"];
            $selloDatosBackup = $_POST["selloDatosBackup"];
            $capacidadDatosBackup = $_POST["capacidadDatosBackup"];

            if ($marcaDatosBackup !== "Seleccione") {
                $select .= ",`b`.marca as marcaBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.marca = '$marcaDatosBackup'";
                $columnas[] = 'Marca Backup';
            }
            if ($serieDatosBackup !== "") {
                $select .= ",`b`.serie as serieBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.serie LIKE " . "'%" . $serieDatosBackup . "%'";
                $columnas[] = 'Serie Backup';
            }
            if ($modeloDatosBackup !== "") {
                $select .= ",`b`.modelo as modeloBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.modelo LIKE " . "'%" . $modeloDatosBackup . "%'";
                $columnas[] = 'Modelo Backup';
            }
            if ($inventarioDatosBackup !== "") {
                $select .= ",`b`.num_inventario as numIBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.num_inventario LIKE " . "'%" . $inventarioDatosBackup . "%'";
                $columnas[] = 'No. Inv. Backup';
            }
            if ($selloDatosBackup !== "") {
                $select .= ",`b`.sello as selloBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.sello LIKE " . "'%" . $selloDatosBackup . "%'";
                $columnas[] = 'Sello Backup';
            }
            if ($capacidadDatosBackup !== "") {
                $select .= ",`b`.capacidad as capacidadBackup";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "`b`.capacidad LIKE " . "'%" . $capacidadDatosBackup . "%'";
                $columnas[] = 'Capacidad Backup';
            }
        }
//Estabilizador

        if (array_key_exists("estabilizadorPartesInventario", $_POST)) {
            $form .= " INNER JOIN equipo as e ON i.id = e.estacion_id";
            $where = 'e.tipo_equipo="estabilizador"';
            $select .= ",e.num_inventario as numInventarioEst";
            $marcaDatosEstabilizador = $_POST["marcaDatosEstabilizador"];
            $serieDatosEstabilizador = $_POST["serieDatosEstabilizador"];
            $modeloDatosEstabilizador = $_POST["modeloDatosEstabilizador"];
            $inventarioDatosEstabilizador = $_POST["inventarioDatosEstabilizador"];
            $capacidadDatosEstabilizador = $_POST["capacidadDatosEstabilizador"];

            if ($marcaDatosEstabilizador !== "Seleccione") {
                $select .= ",e.marca as marcaEstabilizador";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "e.marca = '$marcaDatosEstabilizador'";
                $columnas[] = 'Marca Estabilizador';
            }
            if ($serieDatosEstabilizador !== "") {
                $select .= ",e.serie as serieEstabilizador";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "e.serie LIKE " . "'%" . $serieDatosEstabilizador . "%'";
                $columnas[] = 'Serie Estabilizador';
            }
            if ($modeloDatosEstabilizador !== "") {
                $select .= ",e.modelo as modeloEstabilizador";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "e.modelo LIKE " . "'%" . $modeloDatosEstabilizador . "%'";
                $columnas[] = 'Modelo Estabilizador';
            }
            if ($inventarioDatosEstabilizador !== "") {
                $select .= ",e.num_inventario as numIEstab";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "e.num_inventario LIKE " . "'%" . $inventarioDatosEstabilizador . "%'";
                $columnas[] = 'No. Inv. Estabilizador';
            }
            if ($capacidadDatosEstabilizador !== "") {
                $select .= ",e.capacidad as capacidadEst";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "e.capacidad LIKE " . "'%" . $capacidadDatosEstabilizador . "%'";
                $columnas[] = 'Capacidad Estabilizador';
            }
        }
//Scanner
        if (array_key_exists("scannerPartesInventario", $_POST)) {
            $form .= " INNER JOIN equipo as sc ON i.id = sc.estacion_id";
            $where = 'sc.tipo_equipo="scanner"';
            $select .= ",sc.num_inventario as numInventarioSca";
            $marcaDatosScanner = $_POST["marcaDatosScanner"];
            $serieDatosScanner = $_POST["serieDatosScanner"];
            $modeloDatosScanner = $_POST["modeloDatosScanner"];
            $inventarioDatosScanner = $_POST["inventarioDatosScanner"];
            $tipoDatosScanner = $_POST["tipoDatosScanner"];

            if ($marcaDatosScanner !== "Seleccione") {
                $select .= ",sc.marca as marcaScan";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "sc.marca = '$marcaDatosScanner'";
                $columnas[] = 'Marca Scanner';
            }
            if ($serieDatosScanner !== "") {
                $select .= ",sc.serie as serieScan";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "sc.serie LIKE " . "'%" . $serieDatosScanner . "%'";
                $columnas[] = 'Serie Scanner';
            }
            if ($modeloDatosScanner !== "") {
                $select .= ",sc.modelo as modeloScan";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "sc.modelo LIKE " . "'%" . $modeloDatosScanner . "%'";
                $columnas[] = 'Modelo Scanner';
            }
            if ($inventarioDatosScanner !== "") {
                $select .= ",sc.num_inventario as numInventarioScan";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "sc.num_inventario LIKE " . "'%" . $inventarioDatosScanner . "%'";
                $columnas[] = 'No. Inv. Scanner';
            }
            if ($tipoDatosScanner !== "") {
                $select .= ",sc.tipo as tipoScan";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "sc.tipo LIKE " . "'%" . $tipoDatosScanner . "%'";
                $columnas[] = 'Tipo Scanner';
            }
        }
        //Impresora
        if (array_key_exists("impresoraPartesInventario", $_POST)) {
            $form .= " INNER JOIN equipo as imp ON i.id = imp.estacion_id";
            $where = 'imp.tipo_equipo="impresora"';
            $select .= ",imp.num_inventario as numInventarioImp";
            $marcaDatosImpresora = $_POST["marcaDatosImpresora"];
            $serieDatosImpresora = $_POST["serieDatosImpresora"];
            $modeloDatosImpresora = $_POST["modeloDatosImpresora"];
            $inventarioDatosImpresora = $_POST["inventarioDatosImpresora"];
            $tipoDatosImpresora = $_POST["tipoDatosImpresora"];
            $tonerCartuchoDatosImpresora = $_POST["tonerCartuchoDatosImpresora"];
            $tipoTonerCartuchoDatosImpresora = $_POST["tipoTonerCartuchoDatosImpresora"];

            if ($marcaDatosImpresora !== "") {
                $select .= ",imp.marca as marcaImpresora";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.marca LIKE " . "'%" . $marcaDatosImpresora . "%'";
                $columnas[] = 'Marca Impresora';
            }
            if ($serieDatosImpresora !== "") {
                $select .= ",imp.serie as serieImpresora";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.serie LIKE " . "'%" . $serieDatosImpresora . "%'";
                $columnas[] = 'Serie Impresora';
            }
            if ($modeloDatosImpresora !== "") {
                $select .= ",imp.modelo as modeloImp";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.modelo LIKE " . "'%" . $modeloDatosImpresora . "%'";
                $columnas[] = 'Modelo Impresora';
            }
            if ($inventarioDatosImpresora !== "") {
                $select .= ",imp.num_inventario as numInventarioImp";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.num_inventario LIKE " . "'%" . $inventarioDatosImpresora . "%'";
                $columnas[] = 'No. Inv. Impresora';
            }
            if ($tipoDatosImpresora !== "Seleccione") {
                $select .= ",imp.tipo as tipoImp";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.tipo = '$tipoDatosImpresora'";
                $columnas[] = 'Tipo Impresora';
            }
            if ($tonerCartuchoDatosImpresora !== "Seleccione") {
                $select .= ",imp.toner_cartucho as tonerC";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.toner_cartucho = '$tonerCartuchoDatosImpresora'";
                $columnas[] = 'Toner o Cartucho Impresora';
            }
            if ($tipoTonerCartuchoDatosImpresora !== "") {
                $select .= ",imp.tipo_toner_cartucho as tipoCart";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "imp.tipoTonerCartucho LIKE " . "'%" . $tipoTonerCartuchoDatosImpresora . "%'";
                $columnas[] = 'Tipo Toner o Cartucho Impresora';
            }
        }

        ///Componentes
        //Fuente
        if (array_key_exists("fuentePartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as f ON i.id= f.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " f.tipo_componente='fuente'";
            $marcaDatosFuente = $_POST["marcaDatosFuente"];
            $serieDatosFuente = $_POST["serieDatosFuente"];
            $sataDatosFuente = $_POST["sataDatosFuente"];
            $wattsDatosFuente = $_POST["wattsDatosFuente"];

            if ($marcaDatosFuente !== "") {
                $select .= ",f.marca as marcaFuente";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "f.marca LIKE " . "'%" . $marcaDatosFuente . "%'";
                $columnas[] = 'Marca Fuente';
            }
            if ($serieDatosFuente !== "") {
                $select .= ",f.serie as serieFuente";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "f.serie LIKE " . "'%" . $serieDatosFuente . "%'";
                $columnas[] = 'Serie Fuente';
            }
            if ($sataDatosFuente !== "Seleccione") {
                $select .= ",f.sata as sataFuente";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "f.sata = '$sataDatosFuente'";
                $columnas[] = 'Sata Fuente';
            }
            if ($wattsDatosFuente !== "") {
                $select .= ",f.watts as wattsFuente";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "f.watts LIKE " . "'%" . $wattsDatosFuente . "%'";
                $columnas[] = 'Watts';
            }
        }
//Motherboard
        if (array_key_exists("motherboardPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as mother ON i.id = mother.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " mother.tipo_componente='motherboard'";
            $marcaDatosMotherBoard = $_POST["marcaDatosMotherBoard"];
            $modeloDatosMotherBoard = $_POST["modeloDatosMotherBoard"];
            $serieDatosMotherBoard = $_POST["serieDatosMotherBoard"];
            $lgaDatosMotherBoard = $_POST["lgaDatosMotherBoard"];
            $ramDatosMotherBoard = $_POST["ramDatosMotherBoard"];
            $ranuraVideoDatosMotherBoard = $_POST["ranuraVideoDatosMotherBoard"];

            if ($marcaDatosMotherBoard !== "Seleccione") {
                $select .= ",mother.marca as marcaMother";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mother.marca = '$marcaDatosMotherBoard'";
                $columnas[] = 'Marca Board';
                $columSelect[] = 'marca';
            }
            if ($modeloDatosMotherBoard !== "") {
                $select .= ",mother.modelo as modeloMother";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mother.modelo LIKE " . "'%" . $modeloDatosMotherBoard . "%'";
                $columnas[] = 'Modelo Board';
                $columSelect[] = 'modelo';
            }
            if ($serieDatosMotherBoard !== "") {
                $select .= ",mother.serie as serieMother";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mother.serie LIKE " . "'%" . $serieDatosMotherBoard . "%'";
                $columnas[] = 'Serie Board';
                $columSelect[] = 'serie';
            }
            if ($lgaDatosMotherBoard !== "Seleccione") {
                $select .= ",mother.lga as lgaMother";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mother.lga = '$lgaDatosMotherBoard'";
                $columnas[] = 'LGA';
                $columSelect[] = 'lga';
            }
            if ($ramDatosMotherBoard !== "Seleccione") {
                $select .= ",mother.ram as ramMother";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mother.ram = '$ramDatosMotherBoard'";
                $columnas[] = 'Tipo RAM';
                $columSelect[] = 'lga';
            }

        }
        //Microprocesador
        if (array_key_exists("microprocesadorPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as micro ON i.id = micro.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " micro.tipo_componente='microprocesador'";
            $tipoDatosMicroprocesador = $_POST["tipoDatosMicroprocesador"];
            $lgaDatosMicroprocesador = $_POST["lgaDatosMicroprocesador"];
            $velocidadDatosMicroprocesador = $_POST["velocidadDatosMicroprocesador"];
            $serieDatosMicroprocesador = $_POST["serieDatosMicroprocesador"];

            if ($tipoDatosMicroprocesador !== "Seleccione") {
                $select .= ",micro.tipo as tipoMicro";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "micro.tipo = '$tipoDatosMicroprocesador'";
                $columnas[] = 'Tipo Micro';
            }
            if ($lgaDatosMicroprocesador !== "Seleccione") {
                $select .= ",micro.lga as lgaMicro";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "micro.lga = '$lgaDatosMicroprocesador'";
                $columnas[] = 'LGA Micro';
            }
            if ($velocidadDatosMicroprocesador !== "") {
                $select .= ",micro.velocidad as velocidadMicro";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "micro.velocidad LIKE " . "'%" . $velocidadDatosMicroprocesador . "%'";
                $columnas[] = 'MHz';
            }
            if ($serieDatosMicroprocesador !== "") {
                $select .= ",micro.serie as serieMicro";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "micro.serie LIKE " . "'%" . $serieDatosMicroprocesador . "%'";
                $columnas[] = 'Serie Micro';
            }
        }

        if (array_key_exists("ramPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as r ON i.id = r.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " r.tipo_componente='ram'";
            $marcaDatosRAM = $_POST["marcaDatosRAM"];
            $capacidadDatosRAM = $_POST["capacidadDatosRAM"];
            $serieDatosRAM = $_POST["serieDatosRAM"];

            if ($marcaDatosRAM !== "") {
                $select .= ",r.marca as marcaRam";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "r.marca LIKE " . "'%" . $marcaDatosRAM . "%'";
                $columnas[] = 'Marca RAM';
            }
            if ($capacidadDatosRAM !== "") {
                $select .= ",r.capacidad as capacidadRam";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "r.capacidad LIKE " . "'%" . $capacidadDatosRAM . "%'";
                $columnas[] = 'Capacidad RAM';
            }
            if ($serieDatosRAM !== "") {
                $select .= ",r.serie as serieRam";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "r.serie LIKE " . "'%" . $serieDatosRAM . "%'";
                $columnas[] = 'Serie RAM';
            }
        }

        if (array_key_exists("tarjetaVideoPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as tv ON i.id = tv.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " tv.tipo_componente='tarjeta_video'";
            $ranuraVideoDatosTarjetaVideo = $_POST["ranuraVideoDatosTarjetaVideo"];
            $marcaDatosTarjetaVideo = $_POST["marcaDatosTarjetaVideo"];
            $velocidadDatosTarjetaVideo = $_POST["velocidadDatosTarjetaVideo"];
            $serieDatosTarjetaVideo = $_POST["serieDatosTarjetaVideo"];

//            if($ranuraVideoDatosTarjetaVideo !== "Seleccione") {
//                $select .= ",tv.ranuraVideo";
//                if(strlen($where) > 1)
//                    $where .= ' AND ';
//                $where .= "tv.ranuraVideo = '$ranuraVideoDatosTarjetaVideo'";
//                $columnas[] = 'Tipo Ranura Targ.Video';
//            }
            if ($marcaDatosTarjetaVideo !== "Seleccione") {
                $select .= ",tv.marca as marcaTV";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "tv.marca = '$marcaDatosTarjetaVideo'";
                $columnas[] = 'Marca Targ.Video';
            }
            if ($velocidadDatosTarjetaVideo !== "") {
                $select .= ",tv.velocidad as velocidadTV";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "tv.velocidad LIKE " . "'%" . $velocidadDatosTarjetaVideo . "%'";
                $columnas[] = 'Velocidad Targ.Video';
            }
            if ($serieDatosTarjetaVideo !== "") {
                $select .= ",tv.serie as serieTV";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "tv.serie LIKE " . "'%" . $serieDatosTarjetaVideo . "%'";
                $columnas[] = 'Serie Tarjeta.Video';
            }
        }

        if (array_key_exists("HDDPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as hdd ON i.id = hdd.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " hdd.tipo_componente='hdd'";
            $marcaDatosHDD = $_POST["marcaDatosHDD"];
            $serieDatosHDD = $_POST["serieDatosHDD"];
            $capacidadDatosHDD = $_POST["capacidadDatosHDD"];
            $sataDatosHDD = $_POST["sataDatosHDD"];

            if ($marcaDatosHDD !== "Seleccione") {
                $select .= ",hdd.marca as marcaHdd";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "hdd.marca = '$marcaDatosHDD'";
                $columnas[] = 'Marca HDD';
            }
            if ($serieDatosHDD !== "") {
                $select .= ",hdd.serie as serieHdd";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "hdd.serie LIKE " . "'%" . $serieDatosHDD . "%'";
                $columnas[] = 'Serie HDD';
            }
            if ($capacidadDatosHDD !== "") {
                $select .= ",hdd.capacidad as capacidadHDD";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "hdd.capacidad LIKE " . "'%" . $capacidadDatosHDD . "%'";
                $columnas[] = 'Capacidad HDD';
            }
            if ($sataDatosHDD !== "Seleccione") {
                $select .= ",hdd.sata as sataHdd";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "hdd.sata = '$sataDatosHDD'";
                $columnas[] = 'Sata HDD';
            }
        }

        if (array_key_exists("lectorPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as lector ON i.id = lector.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " lector.tipo_componente='lector'";
            $tipoDatosLector = $_POST["tipoDatosLector"];
            $marcaDatosLector = $_POST["marcaDatosLector"];
            $serieDatosLector = $_POST["serieDatosLector"];

            if ($tipoDatosLector !== "Seleccione") {
                $select .= ",lector.tipo as tipoLector";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "lector.tipo = '$tipoDatosLector'";
                $columnas[] = 'Tipo Lector';
            }
            if ($marcaDatosLector !== "Seleccione") {
                $select .= ",lector.marca as marcaLector";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "lector.marca = '$marcaDatosLector'";
                $columnas[] = 'Marca Lector';
            }
            if ($serieDatosLector !== "") {
                $select .= ",lector.serie as serieLector";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "lector.serie LIKE " . "'%" . $serieDatosLector . "%'";
                $columnas[] = 'Serie Lector';
            }
        }

        if (array_key_exists("mousePartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as mouse ON i.id = mouse.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " mouse.tipo_componente='mouse'";
            $marcaDatosMouse = $_POST["marcaDatosMouse"];
            $serieDatosMouse = $_POST["serieDatosMouse"];
            $modeloDatosMouse = $_POST["modeloDatosMouse"];
            $opticoDatosMouse = $_POST["opticoDatosMouse"];
            $conectorDatosMouse = $_POST["conectorDatosMouse"];

            if ($marcaDatosMouse !== "Seleccione") {
                $select .= ",mouse.marca as marcaMouse";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mouse.marca = '$marcaDatosMouse'";
                $columnas[] = 'Marca Mouse';
            }
            if ($serieDatosMouse !== "") {
                $select .= ",mouse.serie as serieMouse";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mouse.serie LIKE " . "'%" . $serieDatosMouse . "%'";
                $columnas[] = 'Serie Mouse';
            }
            if ($modeloDatosMouse !== "") {
                $select .= ",mouse.modelo as modeloMouse";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mouse.modelo LIKE " . "'%" . $modeloDatosMouse . "%'";
                $columnas[] = 'Modelo Mouse';
            }
            if ($opticoDatosMouse !== "Seleccione") {
                $select .= ",mouse.optico as opticoMouse";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mouse.optico = '$opticoDatosMouse'";
                $columnas[] = 'Mouse Optico';
            }
            if ($conectorDatosMouse !== "Seleccione") {
                $select .= ",mouse.conector as conectorMouse";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "mouse.conector = '$conectorDatosMouse'";
                $columnas[] = 'Tipo Conector Mouse';
            }
        }

        if (array_key_exists("tecladoPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as teclado ON i.id = teclado.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " teclado.tipo_componente='teclado'";
            $marcaDatosTeclado = $_POST["marcaDatosTeclado"];
            $serieDatosTeclado = $_POST["serieDatosTeclado"];
            $modeloDatosTeclado = $_POST["modeloDatosTeclado"];
            $conectorDatosTeclado = $_POST["conectorDatosTeclado"];

            if ($marcaDatosTeclado !== "Seleccione") {
                $select .= ",teclado.marca as marcaTeclado";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "teclado.marca = '$marcaDatosTeclado'";
                $columnas[] = 'Marca Teclado';
            }
            if ($serieDatosTeclado !== "") {
                $select .= ",teclado.serie as serieTeclado";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "teclado.serie LIKE " . "'%" . $serieDatosTeclado . "%'";
                $columnas[] = 'Serie Teclado';
            }
            if ($modeloDatosTeclado !== "") {
                $select .= ",teclado.modelo as modeloTeclado";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "teclado.modelo LIKE " . "'%" . $modeloDatosTeclado . "%'";
                $columnas[] = 'Modelo Teclado';
            }
            if ($conectorDatosTeclado !== "") {
                $select .= ",teclado.conector as conectorTeclado";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "teclado.conector = '.$conectorDatosTeclado.'";

                $columnas[] = 'Tipo Conector Teclado';
            }
        }

        if (array_key_exists("bocinasPartesInventario", $_POST)) {
            $form .= " INNER JOIN tb_componente as bocinas ON i.id = bocinas.estacion_id";
            if (strlen($where) > 1)
                $where .= ' AND ';
            $where .= " bocinas.tipo_componente='bocina'";
            $marcaDatosBocinas = $_POST["marcaDatosBocinas"];
            $serieDatosBocinas = $_POST["serieDatosBocinas"];
            $modeloDatosBocinas = $_POST["modeloDatosBocinas"];

            if ($marcaDatosBocinas !== "Seleccione") {
                $select .= ",bocinas.marca as marcaBocinas";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "bocinas.marca = '$marcaDatosBocinas'";
                $columnas[] = 'Marca Bocinas';
            }
            if ($serieDatosBocinas !== "") {
                $select .= ",bocinas.serie as serieBocina";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "bocinas.serie LIKE " . "'%" . $serieDatosBocinas . "%'";
                $columnas[] = 'Serie Bocinas';
            }
            if ($modeloDatosBocinas !== "") {
                $select .= ",bocinas.modelo as modeloBocinas";
                if (strlen($where) > 1)
                    $where .= ' AND ';
                $where .= "bocinas.modelo LIKE " . "'%" . $modeloDatosBocinas . "%'";
                $columnas[] = 'Modelo Bocinas';
            }
        }

        $dql = "SELECT DISTINCT " . $select . $form . " WHERE " . $where . " ORDER BY i.nombre_red";
        //  var_dump($request->get('monitorPartesInventario'));
        // dump($where);
        // dump($dql);

        $em = $this->getDoctrine()->getEntityManager();
        $db = $em->getConnection();
        $stmt = $db->prepare($dql);
        $params = array();
        $stmt->execute($params);
        $po = $stmt->fetchAll();
//        dump($po);
//        dump($columnas);
//        die();

        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/reporte_piezas.html.twig', ['reporte' => $po, 'columnas' => $columnas]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );

    }

    /**
     * @Route("reportes/informes/mantenimientoAnual",name="sin_mantenimientoAnno")
     */
    public function reporteMantenimientoAnualAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = "SELECT i FROM AppBundle:inventario as i WHERE i.fechaMantenimiento is null";
        $query = $em->createQuery($dql);
        $estaciones = $query->execute();
        dump($estaciones);
        die();
        return $this->render('reportes/mantenimientoAnual.html.twig');
    }

    /**
     * @Route("reportes/existencia_equipos/",name="existencia_equipos")
     *
     * @param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     *
     */
    public function existenciaEquiposAction(Request $request)
    {
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        //$idProducto=0;

//        $serverName = "192.168.107.20";
//        $database = "retinosis";
//        $uid = 'user_assetsp';
//        $pwd = '2020*Fuerza';
//        try {
//            $coneccionAssets = new \PDO(
//                "sqlsrv:server=$serverName;Database=$database",
//                $uid,
//                $pwd,
//                array(
//                    //PDO::ATTR_PERSISTENT => true,
//                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
//                )
//            );
//
//            /**
//             * Consulta para obtener los productos
//             */
        $numI = $request->query->get('numI');
        $entityManager = $this->getDoctrine()->getManager();
        $equipo = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
        // $result = mssql_query("SELECT Existencia_Actual AS existencia FROM [dbo].[Existencia] WHERE Id_Producto='".$cond."';", $coneccionPremium);
//            $sql = "SELECT e.Existencia_Actual as total,e.Id_Producto as id  FROM  Existencia as e
//                    WHERE  e.Existencia_Actual>0 and e.Id_Producto='" . $id_productoEnAssets . "'";
//            $query = $coneccionAssets->query($sql);
        $responseArray = array();
        $inv=null;
        foreach ($equipo as $eq) {
            if($eq->getEstacion()==[] or $eq==null){
              $inv=null;
            }else{
                $inv=$eq->getEstacion()->getNombreRed();
            }
            //     dump($inv);die();
            $responseArray[] = array(
                "modelo" => $eq->getModelo(),
                "numI" => $eq->getNumInventario(),
                "tipoEquipo" => $eq->getTipoEquipo(),
                "serie" => $eq->getSerie(),
                "inventario"=>$inv);
        }
//            if ($query) {
//                $result = array();
//                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
//                    $result[] = $var;
//                }
//                $responseArray = array();
//              //  foreach ($result as $r) {
//                    $responseArray[] = array(
//                        "tipo" => $equipo->getTipoEquipo(),
//                        "id_producto" => $r['id']
//                    );
        // }
        // dump($inv);die();
        return new JsonResponse($responseArray);
        // }
//        } catch (\PDOException $e) {
//            echo 'No se conecta con el servidor! - ' . $e->getMessage();
//        }
        //dump($responseArray);die();
        // Return array with structure of the neighborhoods of the providen city id
//
    }
    /**
     * @Route("reportes/componentes/agregarComponente",name="agregarComponente")
     */

    public function agregarComponente(Request $request){
       //dump($request);die();

        $componente=new componente();
        $entityManager = $this->getDoctrine()->getManager();
        if($request->get('serie')){
            $componente->setSerie($request->get('serie'));
        }
        if($request->get('marca')){
            $componente->setMarca($request->get('marca'));
        }
        if($request->get('sata')){
            $componente->setSata($request->get('sata'));
        }
        if($request->get('watts')){
            $componente->setWatts($request->get('watts'));
        }
        if($request->get('modelo')){
            $componente->setModelo($request->get('modelo'));
        }
        if($request->get('lga')){
            $componente->setLga($request->get('lga'));
        }
        if($request->get('ram')){
            $componente->setRam($request->get('ram'));
        }
        if($request->get('ranuraVideo')){
            $componente->setRanuraVideo($request->get('ranuraVideo'));
        }
        if($request->get('velocidad')){
            $componente->setVelocidad($request->get('velocidad'));
        }
        if($request->get('capacidad')){
            $componente->setCapacidad($request->get('capacidad'));
        }
        if($request->get('tipo')){
            $componente->setTipo($request->get('tipo'));
        }
        if($request->get('conector')){
            $componente->setConector($request->get('conector'));
        }
        if($request->get('optico')){
            $componente->setOptico($request->get('optico'));
        }
        if($request->get('tipoComponente')){
            $componente->setTipoComponente($request->get('tipoComponente'));
        }
        $idEstacion=$request->get('iEstacion');
        $estacion=$entityManager->getRepository('AppBundle:inventario')->find($idEstacion);
        $componente->setEstacion2($estacion);
        $estacion->addComponente($componente);
        //  dump($componente);die();
        $entityManager->persist($componente);
        $entityManager->flush();

        $responseArray[] = array(

            "inventario"=>$estacion);

        return new JsonResponse($responseArray);
    }
    /**
     * @Route("/reportes/desvincularComponenteAjax",name="desvincularComponenteAjax")
     */
    public function desvincularComponenteAjax(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $componente=null;

        if($request->get('id')){
            $componente=$entityManager->getRepository('AppBundle:componente')->find($request->get('id'));
        }elseif($request->get('serie')){
            $componente=$entityManager->getRepository('AppBundle:componente')->findBy(['serie'=>$request->get('serie')])[0];
        }
       //  dump($componente);die();
        $estacion=$entityManager->getRepository('AppBundle:inventario')->find($request->get('iEstacion'));
        $estacion->removeComponente($componente);
        $componente->setEstacion2(null);
        $entityManager->persist($estacion);
        $entityManager->persist($componente);
        $entityManager->flush();
        $responseArray[] = array(

            "inventario"=>$estacion);

          return new JsonResponse($responseArray);
    }

/**
 * @Route("/reportes/desvincularEquipoAjax",name="desvincularEquipoAjax")
 */
public function desvincularEquipoAjax(Request $request){
    $entityManager = $this->getDoctrine()->getManager();
    $equipo=null;

        $numI=explode( ' ', $request->get('numInventario') )[0];
        $equipo=$entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario'=>$numI])[0];


  //  dump($equipo);die();
    $estacion=$entityManager->getRepository('AppBundle:inventario')->find($request->get('iEstacion'));
    $estacion->removeEquipo($equipo);
    $equipo->setEstacion(null);
    $entityManager->persist($estacion);
    $entityManager->persist($equipo);
    $entityManager->flush();
    $responseArray[] = array(

        "inventario"=>$estacion);

    return new JsonResponse($responseArray);
}
}