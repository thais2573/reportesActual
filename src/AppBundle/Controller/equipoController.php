<?php

namespace AppBundle\Controller;

use AppBundle\Entity\equipo;
use AppBundle\Entity\incidencia;
use AppBundle\Entity\inventario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\equipoFomType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Tests\Fixtures\Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class equipoController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
    /**
     * @Route("/estacion/ver_datos_periferico/{id}",name="ver_datos_periferico")
     */
    public function show_DatosPerifericoAction($id, Request $request)
    {
        $compo = $request->get('componente');
        $tipo = $request->get('tipo');

        $entityManager = $this->getDoctrine()->getManager();
        $equipo = $entityManager->getRepository(equipo::class)->find($id);
        $inventario = $entityManager->getRepository(inventario::class)->findBy(['id' => $equipo->getEstacion()]);

        $datos = '';
        $incidencias = '';
        //  dump($inventario);die();
        if ($equipo->getTipoEquipo() == 'cpuchasis' or $equipo->getTipoEquipo() == 'laptop') {
            $incidencias = $this->getDoctrine()->getRepository('AppBundle:incidencia')->findBy(['inventario' => $equipo->getEstacion()]);


            $componentes = $this->getDoctrine()
                ->getRepository('AppBundle:componente')
                ->findBy(['estacion2' => $equipo->getEstacion()]);
            $chasis = null;
            $fuente = null;
            $mother = null;
            $lector = null;
            $teclado = null;
            $bocina = null;
            $micro = null;
            $mouse = null;
            $backup = null;
            $impresora = null;
            $monitor = null;
            $ram = null;
            $hdd = null;
            $tarjetaV = null;
            foreach ($inventario[0]->getComponente() as $c) {
                switch ($c->getTipoComponente()) {
                    case 'fuente':
                        $fuente = $c;
                        break;
                    case 'motherboard':
                        $mother = $c;
                        break;
                    case 'mouse':
                        $mouse = $c;
                        break;
                    case 'lector':
                        $lector = $c;
                        break;
                    case 'teclado':
                        $teclado = $c;
                        break;
                    case 'bocina':
                        $bocina = $c;
                        break;
                    case 'microprocesador':
                        $micro = $c;
                        break;
                    case 'ram':
                        $ram = $c;
                        break;
                    case 'hdd':
                        $hdd = $c;
                        break;
                    case 'tarjeta_video':
                        $tarjetaV = $c;
                        break;
                }

            }

            return $this->render('estacion_trabajo/ver_datos_cpuchasis.html.twig', ['chasis' => $equipo, 'id' => $id, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro,
                'ram' => $ram, 'hdd' => $hdd, 'mouse' => $mouse, 'teclado' => $teclado, 'bocina' => $bocina, 'tarjeta_video' => $tarjetaV, 'lector' => $lector, 'historial' => $incidencias]);
        } else {

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
//            $idCosto=$inventario->getCentroCosto()->getId();
//
//            $dep=$entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id'=>$idCosto]);
            $incidencias = $repository->createQueryBuilder('tabla')
                ->where('tabla.idE = :ide')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('ide', $equipo->getId())
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getArrayResult();
            // $incidencias = $this->getDoctrine()->getRepository('AppBundle:incidencia')->findBy(['idE' => $equipo->getId()]);
            //dump($incidencias);die();

        }
        if (!$equipo) {
            throw $this->createNotFoundException(
                'No existe ningun equipo guardado con este id' . $id
            );
        }
//        dump($incidencias);
//        die();
        return $this->render('estacion_trabajo/ver_datos_periferico.html.twig', ['datos' => $equipo, 'id' => $id, 'tipo' => $equipo->getTipoEquipo(), 'historial' => $incidencias]);
    }
    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_equipo/{id}", name="editar_equipo")
     * @Method({"GET", "POST"})
     *
     */
    public function editarEquipoAction(Request $request, equipo $equipo1)
    {
        // $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($id);
        $em = $this->getDoctrine()->getManager();
        $componentes=null;
        $originalComponentes = new ArrayCollection();
        if ($equipo1->getEstacion() != null){
            $inventario = $em->getRepository('AppBundle:inventario')->findBy(['id' => $equipo1->getEstacion()->getId()]);
//        $inventarioTemp = new ArrayCollection();
//        $inventarioTemp->add($inventario[0]->getResponsable());
//        $inventarioTemp->add($inventario[0]->getPassSetup());
//        $inventarioTemp->add($inventario[0]->getIp());
//        $inventarioTemp->add($inventario[0]->getNombreRed());
//        $inventarioTemp->add($inventario[0]->getAntivirus());
//        $inventarioTemp->add($inventario[0]->getCentroCosto());
//      dump($inventarioTemp);
//      dump($inventario[0]);die();

            foreach ($inventario[0]->getComponente() as $item) {
                $originalComponentes->add($item);
            }
            //  $equipoform = $this->createForm(\AppBundle\Form\equipo2FormType::class, $equipo);
            $equipoform = $this->createForm(\AppBundle\Form\equipoFomType::class, $equipo1);
            $inventarioForm = $this->createForm(\AppBundle\Form\EstacionForm::class, $inventario[0]);
        } else{
            $equipoform = $this->createForm(\AppBundle\Form\equipoFomType::class, $equipo1);
            $inventarioForm = $this->createForm(\AppBundle\Form\EstacionForm::class);
        }
        // dump($equipoform);die();
        $equipoform->handleRequest($request);
        $inventarioForm->handleRequest($request);
        if ($equipoform->isSubmitted() && $equipoform->isValid()) {
            // $datosComponentes = $inventarioForm->getData();
            $inventario = $em->getRepository('AppBundle:inventario')->findBy(['id' => $request->get('app_bundleequipo_fom_type')['estacion']])[0];
            // dump($request->get('app_bundleequipo_fom_type')['estacion']);die();
            //   dump($inventarioForm->getData());die();
//            $datosComponentes->setResponsable($inventario->getResponsable());
//            $datosComponentes->setPassSetup($inventario->getPassSetup());
//            $datosComponentes->setIp($inventario->getIp());
//            $datosComponentes->setnombreRed($inventario->getNombreRed());
//            $datosComponentes->setAntivirus($inventario->getAntivirus());
//            $datosComponentes->setCentroCosto($inventario->getCentroCosto());
            $equipo1 = $equipoform->getData();
            foreach ($originalComponentes as $itemE) {
                if (false === $inventario->getComponente()->contains($itemE)) {
                    $em->remove($itemE);
                    $em->flush();
                }
            }

            foreach ($inventario->getComponente() as $item) {
                if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
                    $em->remove($item);

                } else {
                    //dump($item->getTipoComponente());die();
                    // $item->setEstado('Activo');
                    $item->setTipoComponente($item->getTipoComponente());
                    $item->setEstacion2($inventario);

                }
                $em->persist($item);
            }
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated

            //$pagoSNEU->setEstado('Activo');


            //Creando el historial de edicion
            $incidencia = new incidencia();
            $incidencia->setTipo('Edicion');
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setEstado('Edicion realizada');
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);


            //ump($impresora);die();
            if (is_null($equipo1->getEstacion())) {
                $incidencia->setDpto("Sin estacion de trabajo");
            } else {
                $incidencia->setDpto($equipo1->getEstacion()->getCentroCosto());
            }

            $incidencia->setAsunto('---');
            $incidencia->setRespuesta('' .
                'serie:' . $equipoform->getData()->getSerie() .
                'modelo:' . $equipoform->getData()->getModelo() . 'dato2:' . $equipoform->getData()->getModelo()

            );
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setResumen('---');

            if (is_null($equipo1->getEstacion())) {
                $incidencia->setInventario(null);
            } else {
                $incidencia->setInventario($equipo1->getEstacion());
                $componentes=$inventario->getComponente();
            }


            $date = new \DateTime('now');
            $incidencia->setTipoMov(null);
            $incidencia->setEstado('Edicion realizada');
            $incidencia->setAsesorio($equipo1->getTipoEquipo());
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setIdE($equipo1->getId());
            $incidencia->setNumInventario($equipo1->getNumInventario());
            //$incidencia->setFechaA(date$date);
            $incidencia->setFechaA(new \DateTime("now"));

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
//           dump($incidencia);
//            dump($datosComponentes);
//            die();
            $entityManager->persist($equipo1);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Equipo Editado Correctamente');
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $equipo1->getId(), 'tipo' => $equipo1->getTipoEquipo()]);
        }
        $areas = $em->getRepository('AppBundle:area')->findAll();
//    $centro_costoActual=$em->getRepository('AppBundle:inventario')->findBy(['id'=>$equipo->getEstacion()->getId()])[0]->getCentroCosto();
        // $departamento_actual=$centro_costoActual->getArea();
//    dump($centro_costoActual);

//        return $this->render('estacion_trabajo/editar_datos_periferico.html.twig', [
//            "form" => $equipoform->createView(), "tipo" => $equipo->getTipoEquipo(), "lista_componentes" => $equipo->getComponente()
//            , "departamento" => $equipo->getDepartamento(), "estacion" => $equipo->getEstacion(), 'areas' => $areas, 'equipo' => $equipo, 'departamentoActual' => '', 'centroCosto' => '',
//        ]);
        return $this->render('perifericos/editar_equipo.html.twig', [
            "form" => $equipoform->createView(), "tipo" => $equipo1->getTipoEquipo(), "lista_componentes" => $componentes, 'componentesForm' => $inventarioForm->createView()
            , "departamento" => $equipo1->getDepartamento(), "estacion" => $equipo1->getEstacion(), 'areas' => $areas, 'equipo' => $equipo1, 'departamentoActual' => '', 'centroCosto' => '',
        ]);
    }
    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_equipoAjax", name="editar_equipoAjax")
     * @Method({"GET", "POST"})
     *
     */
    public function editarEquipoAjaxAction(Request $request)
    {
        $id=$request->get('id');
        $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($id);
        //dump($equipo1);
        // dump($request->get('modelo'));die();
        $em = $this->getDoctrine()->getManager();
        if($request->get('modelo')){
            $equipo->setModelo($request->get('modelo'));
        }
        if($request->get('sello')) {
            $equipo->setSello($request->get('sello'));
        }
        if($request->get('color')) {
            $equipo->setColor($request->get('color'));
        }
        if($request->get('marca')) {
            $equipo->setMarca($request->get('marca'));
        }
        if($request->get('capacidad')) {
            $equipo->setCapacidad($request->get('capacidad'));
        }
        if($request->get('serie')) {
            $equipo->setSerie($request->get('serie'));
        }
        if($request->get('tipo')) {
            $equipo->setTipo($request->get('tipo'));
        }
        if($request->get('tonerCartucho')) {
            $equipo->setTonerCartucho($request->get('tonerCartucho'));
        }
        if($request->get('tipoTonerCartucho')) {
            $equipo->setTipoTonerCartucho($request->get('tipoTonerCartucho'));
        }
        if($request->get('lcd')) {
            $equipo->setLcd($request->get('lcd'));
        }
        //Creando el historial de edicion
        $incidencia = new incidencia();
        $incidencia->setTipo('Edicion');
        $incidencia->setUser($this->getUser()->getUsername());
        $incidencia->setEstado('Edicion realizada');


        $incidencia->setAsunto('---');
        $incidencia->setRespuesta('' .
            'serie:' . $equipo->getSerie() .
            'modelo:' . $equipo->getModelo() . 'dato2:' . $equipo->getModelo()

        );
        $incidencia->setFecha(new \DateTime("now"));
        $incidencia->setResumen('---');
        $incidencia->setInventario($equipo->getEstacion());


        $date = new \DateTime('now');
        $incidencia->setTipoMov(null);
        $incidencia->setEstado('Edicion realizada');
        $incidencia->setAsesorio($equipo->getTipoEquipo());
        $incidencia->setTecAsignado($this->getUser());
        $incidencia->setCorreo($this->getUser()->getEmail());
        $incidencia->setIdE($equipo->getId());
        $incidencia->setNumInventario($equipo->getNumInventario());
        //$incidencia->setFechaA(date$date);
        $incidencia->setFechaA(new \DateTime("now"));

        // ... perform some action, such as saving the task to the database
        // for example, if Task is a Doctrine entity, save it!
        $entityManager = $this->getDoctrine()->getManager();
//           dump($incidencia);
//            dump($equipo);
//            die();
        $entityManager->persist($equipo);
        $entityManager->persist($incidencia);
        $entityManager->flush();

        return new JsonResponse(array('equipo' => $equipo));
    }
    /**
     * @Route("reportes/equipos/{id}/{equipo}/recibir", name="solucionar_sin_incidencia")
     */
    //Movimiento de activos
    public function recibirAction(Request $request, $id, $equipo)
    {

        $entityManager = $this->getDoctrine()->getManager();


        $asesorio = $entityManager->getRepository('AppBundle:equipo')->findBy(['id' => $id]);
        $inventario = $asesorio[0]->getEstacion();

        return $this->render('incidencia/recibir2.html.twig', ['inventario' => $inventario, 'nombre' => $equipo, 'equipo' => $asesorio[0]]);
    }
    /**
     * @Route("equipo/{id}/{id_inve}/{equipo}/{dpto}/respuesta",name="equipo_respuesta")
     */

    public function resp2Action(Request $request, $id, \Swift_Mailer $mailer, $id_inve, $equipo, $dpto)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id);
        return $this->redirectToRoute('incidencia_movimientoATaller', ['id_equipo' => $equipo_incidencia->getId()]);
    }

}
