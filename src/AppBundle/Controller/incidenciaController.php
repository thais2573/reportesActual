<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\backup;
use AppBundle\Entity\bocinas;
use AppBundle\Entity\componente;
use AppBundle\Entity\cpuchasis;
use AppBundle\Entity\departamento;
use AppBundle\Entity\deuda;
use AppBundle\Entity\equipo;
use AppBundle\Entity\equipoRepository;
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
use AppBundle\Entity\ram;
use AppBundle\Entity\taller;
use AppBundle\Entity\teclado;
use AppBundle\Entity\tipo;
use DateTime;
use DH\DoctrineAuditBundle\Helper\AuditHelper;
use DH\DoctrineAuditBundle\Reader\AuditReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDOConnection;
use PDO;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\SwiftmailerBundle\Command\NewEmailCommand;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class incidenciaController extends Controller
{

    private $filters = [];

    private $pagination = [];

    /**
     * @Route("incidencia/{id}/{id_equipo}/movimiento",name="incidencia_movimiento")
     */
    public function movimientoAction(Request $request, $id, $id_equipo)
    {
        $tipoMov = 'Envio a taller';
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Envio a taller');
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('Taller TECUN');
        $movimiento->setTecnico($this->getUser());
        $movimiento->setDireccionDestino('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
        $tipoForm = $this->createForm('AppBundle\Form\movimientoFormType', $movimiento);

        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);
        //$id_invetario = $incidencia->getInventario();
        //$inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());

        //dump($id);die();

        $tiene = '';
        $asesorio = '';
        // dump($id_invetario->getChasis());die();
        $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
        $inventario = $entityManager->getRepository(inventario::class)->findBy(['id' => $asesorio[0]->getEstacion()->getId()]);
        // dump($inventario);die();
        if ($asesorio[0]->getTipoEquipo() == 'CPU-Chasis' || $asesorio[0]->getTipoEquipo() == 'cpuchasis') {
            $fuente = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';

        } else {

            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
            //dump($asesorio);die();
            $tiene = 'no';
        }

        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);

            // dump($this->getUser());die();
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setResumen('Equipo que se envia a  taller');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setTipoMov('Envio a taller');
            $incidencia->setEstado('Reparacion');
            $incidencia->setAsesorio($asesorio[0]->getTipoEquipo());
            $incidencia->setIdE($asesorio[0]->getId());
            $incidencia->setFechaA($fecha_actual);


            $tipo = $tipoForm->getData();
            $tipo->setTipoMovimiento($incidencia->getTipoMov());
            $tipo->setInventario(null);
            $tipo->setPeriferico($asesorio[0]->getTipoEquipo());

            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia);

            //  dump($incidencia->getAsesorio());
            //die();

            $asesorio[0]->setEstado('En taller');


            $taller = new taller();
            $taller->setIdPeriferico($asesorio[0]->getId());
            //dump($incidencia->getAsesorio());
            $taller->setTipoPeriferico($incidencia->getAsesorio());
            $taller->setDpto($incidencia->getDpto());
            $taller->setModelo($asesorio[0]->getModelo());
            // dump($taller);die();
            if ($incidencia->getAsesorio() == 'bocinas' || $incidencia->getAsesorio() == 'fuente' || $incidencia->getAsesorio() == 'board' || $incidencia->getAsesorio() == 'hdd' || $incidencia->getAsesorio() == 'lector' ||
                $incidencia->getAsesorio() == 'microprocesador' || $incidencia->getAsesorio() == 'ram' || $incidencia->getAsesorio() == 'scanner' || $incidencia->getAsesorio() == 'teclado' || $incidencia->getAsesorio() == 'mouse') {
                //dump($asesorio[0]->getCpu()->getNumInventario());die();
                if (is_null($asesorio[0]->getCpu())) {
                    $taller->setNumInventario(0);
                } else {
                    $taller->setNumInventario('' . $asesorio[0]->getCpu()->getNumInventario());
                }

                // $taller->setNumInventario($equipo_incidencia->getCpu()->getNumInventario());
            } else {
                $taller->setNumInventario('' . $asesorio[0]->getNumInventario());
            }

            $taller->setFechaSalida(new \DateTime("now"));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipo);
            $entityManager->persist($taller);
            $entityManager->flush();

            $this->addFlash('success', 'Movimiento Creada Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        if ($tiene == 'no') {
            // if()

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('AppBundle:Administracion\Usuario')->findAllUserUms();


            return $this->render('incidencia/movimiento.html.twig', ['movimientoForm' => $tipoForm->createView(), 'tipoMov' => $tipoMov, 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'usuarios' => $usuarios]);
        } else {

            return $this->render('incidencia/movimiento2.html.twig', ['movimientoForm' => $tipoForm->createView(), 'inventario' => $inventario, 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother, 'usuarios' => $usuarios,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'tipoMov' => $tipoMov]);
        }
    }

    /**
     * @Route("incidencia/{id_equipo}/movimiento_taller",name="incidencia_movimientoATaller")
     */
    public function movimientoTallerAction(Request $request, $id_equipo, \Swift_Mailer $mailer)
    {
        $pendiente = false;
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Envio a taller');
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('Taller TECUN');
        $movimiento->setDireccionDestino('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
        $tipoForm = $this->createForm('AppBundle\Form\movimientoFormType', $movimiento);
        $entityManager = $this->getDoctrine()->getManager();
        $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
        $incidencia1 = new incidencia();
        $incidencia1->setTipo('Reparacion PC');
        $incidencia1->setEstado('Reparación');
        $incidencia1->setTipoMov('Envio a taller');
        $estacion = $asesorio[0]->getEstacion();
        $iddpto = $estacion->getCentroCosto();
        $dpto = $entityManager->getRepository(departamento::class)->findBy(['id' => $iddpto]);
        //dump($dpto);die();
        $incidencia1->setDpto($dpto[0]->getName());
        $incidencia1->setAsesorio($asesorio[0]->getTipoEquipo());
        // dump($this->getUser());die();
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        //dump($tipoIncidencia);die();
        $incidencia1->setUser($this->getUser()->getUsername());
        $incidencia1->setTipo($tipoIncidencia);
        // $incidencia = $entityManager->getRepository(incidencia::class)->find($id);
        //$id_invetario = $incidencia->getInventario();
        //$inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());


        $tiene = '';
        $asesorio = '';
        // dump($id_invetario->getChasis());die();
        $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
        $inventario = $entityManager->getRepository(inventario::class)->findBy(['id' => $asesorio[0]->getEstacion()->getId()]);
        $inventarioTaller = $entityManager->getRepository(inventario::class)->findBy(['id' => 3889]);
        $departamentoTaller = $entityManager->getRepository(departamento::class)->findBy(['id' => 71]);
        //dump($inventarioTaller);die();
        if ($asesorio[0]->getTipoEquipo() == 'CPU-Chasis' || $asesorio[0]->getTipoEquipo() == 'cpuchasis') {
            $fuente = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';
        } else {
            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
            // dump($asesorio);die();
            $tiene = 'no';
        }
        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {

            // dump($request);
            if ($request->get('pendiente')) {
                $pendiente = true;
            }
            // dump($pendiente);
            // die();
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);
            $incidencia1->setDpto($inventario[0]->getCentroCosto());
            $incidencia1->setAsunto('Incidencia propia');
            $incidencia1->setResumen('Equipo que se envia a  taller');
            $incidencia1->setFecha(new \DateTime("now"));
            $incidencia1->setRespuesta('Equipo que se envia a taller');
            $incidencia1->setInventario($inventario[0]);


            $incidencia1->setIdE($asesorio[0]->getId());
            $incidencia1->setTecAsignado($this->getUser());
            $incidencia1->setCorreo($this->getUser()->getEmail());
            $incidencia1->setFechaA($fecha_actual);
            $incidencia1->setNumInventario($asesorio[0]->getNumInventario());
            $tipo = $tipoForm->getData();
            $tipo->setTipoMovimiento($incidencia1->getTipoMov());
            $tipo->setInventario($inventario[0]);
            $tipo->setTecnico($this->getUser());
            $tipo->setPeriferico($asesorio[0]->getTipoEquipo());
            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia1);

            $asesorio[0]->setEstacion($inventarioTaller[0]);
            if ($pendiente == true) {
                $incidencia1->setEstado('Pendiente a taller');
                $asesorio[0]->setEstado('Pendiente a taller');
                // dump('aqui');
            } else {
                $incidencia1->setEstado('Reparacion');
                $asesorio[0]->setEstado('En taller');
                $asesorio[0]->setDepartamento($departamentoTaller[0]);
                $taller = new taller();
                //  dump($asesorio[0]);die();

                $taller->setIdPeriferico($asesorio[0]->getId());

                $taller->setTipoPeriferico($asesorio[0]->getTipoEquipo());
                $taller->setDpto($incidencia1->getDpto());
                $taller->setModelo($asesorio[0]->getModelo());
                $taller->setNumInventario($asesorio[0]->getNumInventario());
                $taller->setFechaSalida(new \DateTime("now"));
                $entityManager->persist($taller);
                $existe_accesorio_taller = $entityManager->getRepository('AppBundle:taller')->findBy(['numInventario' => $asesorio[0]->getNumInventario()]);
                if (!$existe_accesorio_taller) {
                    $entityManager->persist($tipo);
                }
                //  dump('aqui no');
            }
            //    dump($incidencia1);die();
//          dump($tipo);die();
            // dump($asesorio[0]);dump($taller);die();
            $entityManager = $this->getDoctrine()->getManager();
            // dump($taller);die();
//      if ($incidencia1->getAsesorio() == 'bocinas' || $incidencia1->getAsesorio() == 'fuente' || $incidencia1->getAsesorio() == 'board' || $incidencia1->getAsesorio() == 'hdd' || $incidencia1->getAsesorio() == 'lector' ||
//        $incidencia1->getAsesorio() == 'microprocesador' || $incidencia1->getAsesorio() == 'ram' || $incidencia1->getAsesorio() == 'scanner' || $incidencia1->getAsesorio() == 'teclado' || $incidencia1->getAsesorio() == 'mouse') {
//        //dump($asesorio[0]->getCpu()->getNumInventario());die();
//        if (is_null($asesorio[0]->getEstacion())) {
//          $taller->setNumInventario(0);
//        } else {
//          $taller->setNumInventario('' . $asesorio[0]->getCpu()->getNumInventario());
//        }
//
//        // $taller->setNumInventario($equipo_incidencia->getCpu()->getNumInventario());
//      } else {
//        $taller->setNumInventario('' . $asesorio[0]->getNumInventario());
//      }
            $existe_accesorio_taller = $entityManager->getRepository('AppBundle:taller')->findBy(['numInventario' => $asesorio[0]->getNumInventario()]);
            if (!$existe_accesorio_taller) {
                $entityManager->persist($tipo);
            }

            // dump($asesorio);die();

            $entityManager->persist($incidencia1);
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
                    ['estado' => $incidencia1->getEstado(), 'tipo' => $incidencia1->getTipo(), 'incidencia' => $incidencia1]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Movimiento Creada Correctamente');
            // dump($incidencia1);die();
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia1->getId()]);
        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $dep = $entityManager->getRepository('AppBundle:departamento')->findAll();
        if ($tiene == 'no') {
            // if()
            $em = $this->getDoctrine()->getManager();
            return $this->render('incidencia/movimiento.html.twig', ['movimientoForm' => $tipoForm->createView(), 'dep' => $dep, 'incidencia' => $incidencia1, 'asesorio' => $asesorio, 'usuarios' => $usuarios, 'tipoMov' => 'Enviar a taller', 'inventario' => $inventario]);
        } else {
            return $this->render('incidencia/movimiento2.html.twig', ['movimientoForm' => $tipoForm->createView(), 'inventario' => $inventario, 'incidencia' => $incidencia1, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'tipoMov' => $movimiento->getTipoMovimiento(), 'usuarios' => $usuarios, 'dep' => $dep]);
        }
    }


    /**
     * @Route("incidencia/{id_equipo}/{equipo}/movimiento2/{id}",name="incidencia_movimientoSI")
     */
    public function movimientoSI2Action(Request $request, $id_equipo, $equipo, $id, \Swift_Mailer $mailer)
    {
        $tipoMov = 'Envio a taller';
        $usuario = $this->getUser();
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Envio a taller');
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('Taller TECUN');
        $movimiento->setTecnico($usuario);
        $movimiento->setDireccionDestino('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
        $tipoForm = $this->createForm('AppBundle\Form\movimientoFormType', $movimiento);
        $tipoMov = 'Enviar a taller';
        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);
        //$id_invetario = $incidencia->getInventario();
        //$inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        //dump($id);die();

        $tiene = '';
        $asesorio = '';
        // dump($id_invetario->getChasis());die();


        if ($equipo == 'CPU-Chasis' || $equipo == 'cpuchasis') {

            // $i = $entityManager->getRepository(cpuchasis::class)->findBy(['id' => $id_equipo]);
            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
            // dump($asesorio[0]);
            // die();
            // $asesorio=$id_invetario->getChasis();
            // dump($asesorio);
            //  die();
            $fuente = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';

        } else {

            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
            //dump($asesorio);die();
            $tiene = 'no';
        }

        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $pendiente = false;
            if ($request->get('pendiente')) {
                $pendiente = true;
            }

            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);

            $incidencia1 = new incidencia();
            $incidencia1->setTipo('Reparacion PC');
            $incidencia1->setEstado('Reparación');
            // dump($this->getUser());die();
            $incidencia1->setUser($this->getUser()->getUsername());
            $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $asesorio[0]->getId()]);
            $incidencia1->setDpto('');
            $incidencia1->setAsunto('Incidencia propia');
            $incidencia1->setResumen('Equipo que se envia a  taller');
            $incidencia1->setFecha($fecha_actual);
            $incidencia1->setRespuesta('Activo de nuevo');
            $incidencia1->setInventario(null);
            $incidencia1->setTipoMov($tipoMov);
            $incidencia1->setAsesorio($equipo);
            $incidencia1->setIdE($asesorio[0]->getId());

            $incidencia1->setNumInventario($asesorio[0]->getNumInventario());

            $incidencia1->setTecAsignado($this->getUser());
            $incidencia1->setCorreo($this->getUser()->getEmail());
            $incidencia1->setTipo($tipoIncidencia);
            // dump($fecha_actual->format("d/m/Y"));die();
            $incidencia1->setFechaA($fecha_actual);

            if ($pendiente == true) {
                $incidencia1->setEstado('Pendiente a taller');
                $asesorio[0]->setEstado('Pendiente a taller');
            } else {
                $incidencia1->setEstado('Reparacion');
                $taller = new taller();
                $asesorio[0]->setEstado('En taller');
                $taller->setIdPeriferico($asesorio[0]->getId());
                //dump($incidencia->getAsesorio());
                $taller->setTipoPeriferico($incidencia1->getAsesorio());
                $taller->setDpto($incidencia1->getDpto());
                // $taller->setNumInventario($asesorio[0]->getNumInventario());
                $taller->setModelo($asesorio[0]->getModelo());
                // dump($taller);die();
                if ($incidencia1->getAsesorio() == 'bocinas' || $incidencia1->getAsesorio() == 'fuente' || $incidencia1->getAsesorio() == 'board' || $incidencia1->getAsesorio() == 'hdd' || $incidencia1->getAsesorio() == 'lector' ||
                    $incidencia1->getAsesorio() == 'microprocesador' || $incidencia1->getAsesorio() == 'ram' || $incidencia1->getAsesorio() == 'scanner' || $incidencia1->getAsesorio() == 'teclado' || $incidencia->getAsesorio() == 'mouse') {
                    //dump($asesorio[0]->getCpu()->getNumInventario());die();
                    if (is_null($asesorio[0]->getCpu())) {
                        $taller->setNumInventario(0);
                    } else {
                        $taller->setNumInventario('' . $asesorio[0]->getCpu()->getNumInventario());
                    }

                    // $taller->setNumInventario($equipo_incidencia->getCpu()->getNumInventario());
                } else {
                    $taller->setNumInventario('' . $asesorio[0]->getNumInventario());
                }

                $taller->setFechaSalida($fecha_actual);
                $entityManager->persist($taller);
            }

           // dump($incidencia1);die();
            $tipo = $tipoForm->getData();
            $tipo->setTipoMovimiento($incidencia1->getTipoMov());
            $tipo->setInventario(null);
            $tipo->setPeriferico($equipo);
            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia1);
            $inventarioTaller = $entityManager->getRepository(inventario::class)->findBy(['id' => 3889]);
            //  dump($incidencia->getAsesorio());
            //die();


            $asesorio[0]->setEstacion($inventarioTaller[0]);
            $asesorio[0]->setDepartamento($inventarioTaller[0]->getCentroCosto());


            $entityManager = $this->getDoctrine()->getManager();
            $existe_accesorio_taller = $entityManager->getRepository('AppBundle:taller')->findBy(['numInventario' => $asesorio[0]->getNumInventario()]);
            if (!$existe_accesorio_taller) {
                $entityManager->persist($tipo);
            }
//            dump($movimiento);
//            die();
            //$entityManager->persist($movimiento);
//            dump($asesorio[0]);
//            dump($movimiento);
//            dump($tipo);
//            dump($incidencia1);dump($tipo);dump($asesorio);
//           die();

            $entityManager->persist($tipo);
            $entityManager->persist($incidencia1);
            $entityManager->flush();

            $this->addFlash('success', 'Movimiento Creada Correctamente');
            //Enviar correo
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    ['estado' => $incidencia1->getEstado(), 'tipo' => $incidencia1->getTipo(), 'incidencia' => $incidencia1]
                ),
                    'text/html'

                );

            $mailer->send($message);
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia1->getId()]);
        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $dep = $entityManager->getRepository('AppBundle:departamento')->findAll();
        if ($tiene == 'no') {
            // if()

            $em = $this->getDoctrine()->getManager();


            return $this->render('incidencia/movimiento.html.twig', ['movimientoForm' => $tipoForm->createView(), 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'usuarios' => $usuarios, 'tipoMov' => $tipoMov]);
        } else {

            return $this->render('incidencia/movimiento2.html.twig', ['movimientoForm' => $tipoForm->createView(), 'usuarios' => $usuarios, 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'tipoMov' => $tipoMov, 'dep' => $dep]);
        }
    }


    /**
     * @Route("incidencia/{id_equipo}/{equipo}/movimiento2/",name="incidencia_movimientoSI43")
     */
    public function movimientoSI234Action(Request $request, $id_equipo, $equipo, \Swift_Mailer $mailer)
    {

        $tipoMov = 'Enviar a taller';
        $movimiento = new movimiento();
        $movimiento->setEntidadEntrega('Taller TECUN');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\movimientoFormType', $movimiento);

        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = '';
        $tiene = '';

        $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id_equipo]);
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
//        dump($tipoIncidencia);
//        die();
        if ($equipo == 'CPU-Chasis' || $equipo == 'cpuchasis') {

            $fuente = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';

        } else {

            $tiene = 'no';

        }


        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);
            // dump($asesorio);die();
            $incidencia1 = new incidencia();
            $incidencia1->setTipo('Reparacion PC');
            $incidencia1->setEstado('Reparación');
            // dump($this->getUser());die();
            $incidencia1->setUser($this->getUser()->getUsername());
            $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $asesorio[0]->getId()]);
            $incidencia1->setDpto('');
            $incidencia1->setAsunto('Incidencia propia');
            $incidencia1->setResumen('Equipo que se envia a  taller');
            $incidencia1->setFecha($fecha_actual);
            $incidencia1->setRespuesta('Activo de nuevo');
            $incidencia1->setInventario(null);
            $incidencia1->setTipoMov('Envio a taller');
            $incidencia1->setEstado('Reparacion');
            $incidencia1->setIdE($asesorio[0]->getId());
            $incidencia1->setNumInventario($asesorio[0]->getNumInventario());
            $incidencia1->setAsesorio($equipo);
            $incidencia1->setTecAsignado($this->getUser());
            $incidencia1->setCorreo($this->getUser()->getEmail());
            $incidencia1->setTipo($tipoIncidencia);
            // dump($fecha_actual->format("d/m/Y"));die();
            $incidencia1->setFechaA($fecha_actual);


            $tipo = $tipoForm->getData();
            $tipo->setTecnico($this->getUser());
            $tipo->setTipoMovimiento($incidencia1->getTipoMov());
            $tipo->setInventario(null);
            $tipo->setPeriferico($equipo);
            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia1);

//      dump($request);
//      die();

            $asesorio[0]->setEstado('En taller');


            $entityManager->persist($incidencia1);
            $taller = new taller();
            $taller->setIdPeriferico($asesorio[0]->getId());
            //dump($incidencia1->getAsesorio());die();
            $taller->setTipoPeriferico($incidencia1->getAsesorio());
            $taller->setDpto($incidencia1->getDpto());
            $taller->setModelo($asesorio[0]->getModelo());
            //  dump($equipo);die();
            if ($equipo == 'bocinas' || $equipo == 'fuente' || $equipo == 'board' || $equipo == 'hdd' || $equipo == 'lector' ||
                $equipo == 'microprocesador' || $equipo == 'ram' || $equipo == 'teclado' || $equipo == 'mouse') {
                //dump($asesorio[0]->getCpu()->getNumInventario());die();
                if (is_null($asesorio[0]->get())) {
                    $taller->setNumInventario(0);
                } else {
                    $taller->setNumInventario('' . $asesorio[0]->getCpu()->getNumInventario());
                }

                // $taller->setNumInventario($equipo_incidencia->getCpu()->getNumInventario());
            } else {
                $taller->setNumInventario('' . $asesorio[0]->getNumInventario());
            }


            //$fecha_actual->format('d/m/Y');
            // dump($fecha_actual);die();
            $taller->setFechaSalida(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($tipo);
            $entityManager->persist($incidencia1);
            $entityManager->persist($taller);
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
                    ['estado' => $incidencia1->getEstado(), 'tipo' => $incidencia1->getTipo(), 'incidencia' => $incidencia1]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Movimiento Creada Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia1->getId()]);
        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        if ($tiene == 'no') {
            // if()
            //  dump($usuarios);die();
            //dump($usuarios);die();
            return $this->render('incidencia/movimiento.html.twig', ['movimientoForm' => $tipoForm->createView(), 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'tipoMov' => $tipoMov, 'usuarios' => $usuarios]);
        } else {

            return $this->render('incidencia/movimiento2.html.twig', ['movimientoForm' => $tipoForm->createView(), 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother, 'tipoMov' => $tipoMov, 'usuarios' => $usuarios,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector]);
        }
    }


    /**
     * @Route("incidencia/{id}/{equipo}/{id_equipo}/movimientoSI1",name="movimiento1SI")
     */
    public function movimiento1RSIAction(Request $request, $id, $equipo, $id_equipo)
    {
        $incidencia = '';
        $tipoForm = $this->createForm('AppBundle\Form\movimientoReparadoFormType');

        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);

        $id_invetario = $incidencia->getInventario();
        $inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());

        $tiene = '';
        $asesorio = '';

        if ($equipo == 'Monitor' || $equipo == 'monitor') {

            $asesorio = $entityManager->getRepository(monitor::class)->findBy(['id' => $id_equipo]);

            $tiene = 'no';
        }
        if ($equipo == 'Teclado' || $equipo == 'teclado') {

            $asesorio = $entityManager->getRepository(teclado::class)->findBy(['id' => $id_equipo]);
            $tiene = 'no';
        }
        if ($equipo == 'Mouse' || $equipo == 'mouse') {

            $asesorio = $entityManager->getRepository(mouse::class)->findBy(['id' => $id_equipo]);
            $tiene = 'no';

        }
        if ($equipo == 'Impresora' || $equipo == 'impresora') {


            $tiene = 'no';
            //dump($asesorio);die();

            //$repository = $this->getDoctrine()->getRepository('AppBundle:impresora' );
            /*
                  if(is_iterable($asesorio)){
                    $query = $repository->createQueryBuilder('tabla')
                      ->where('tabla.estado = :estado')
                      ->setParameter('estado', 'En taller')
                      ->setMaxResults(1)
                      // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                      ->getQuery()->getSingleResult();
                    $asesorio = $query;
                    //dump($incidencia);die();
                  }else{
                    $asesorio = $entityManager->getRepository(impresora::class)->findBy(['id' => $id]);
                  }*/
            $asesorio = $entityManager->getRepository(impresora::class)->findBy(['id' => $id_equipo]);
        }
        if ($equipo == 'Backup' || $equipo == 'backup') {

            $asesorio = $entityManager->getRepository(backup::class)->findBy(['id' => $id_equipo]);
            $tiene = 'no';
            //dump($asesorio);
            // die();
        }
        if ($equipo == 'Bocinas' || $equipo == 'bocinas') {

            $asesorio = $entityManager->getRepository(bocinas::class)->findBy(['id' => $id_equipo]);
            $tiene = 'no';

        }
        if ($equipo == 'Estabilizador' || $equipo == 'estabilizador') {

            $asesorio = $entityManager->getRepository(estabilizador::class)->findBy(['id' => $id_equipo]);
            // dump($asesorio);die();
            $tiene = 'no';

        }

        if ($equipo == 'CPU-Chasis' || $equipo == 'cpuchasis') {

            // dump("hola");die();
            //$asesorio = $id_invetario->getChasis();
            $asesorio = $entityManager->getRepository(cpuchasis::class)->findBy(['id' => $id]);
            $fuente = $entityManager->getRepository(fuente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(motherboard::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(microprocesador::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(ram::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(hdd::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(lector::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';


            //  dump($asesorio);die();
        }


        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {


            $incidencia = new incidencia();
            $incidencia->setTipo('Reparacion PC');
            $incidencia->setEstado('Reparacion');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setDpto('');
            $incidencia->setAsunto('Reparacion');

            $incidencia->setResumen('Equipo que regresa de  taller');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Taller');
            // dump($inventario[0]);die();
            $incidencia->setInventario(null);
            $incidencia->setTipoMov('Reparacion');
            $incidencia->setAsesorio($equipo);
            $incidencia->setTecAsignado($this->getUser()->getUsername());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setIdE($id);


            //$entityManager->persist($asesorio[0]);
            $entityManager->persist($incidencia);
            $entityManager->flush();


            $tipo = $tipoForm->getData();
            $tipo->setTipoMovimiento("Regreso de taller");
            $tipo->setInventario(null);
            $tipo->setPeriferico($equipo);
            //$tipo->setsetRespEntrega($respEntrega);
            $fecha_actual = date("d/m/Y g:ia");
            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia);

            //  dump($asesorio);die();

            if (is_iterable($asesorio)) {

                $asesorio[0]->setEstado('Reparado');
                $entityManager->persist($asesorio[0]);
                // dump($asesorio[0]->getEstado());die();
            } else {
                //dump("objeto");die();
                $asesorio->setEstado('Reparado');
                $entityManager->persist($asesorio);
            }


            //dump($asesorio[0]->getEstado());
            // die();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipo);
            $entityManager->flush();

            //$equipo = strtolower($incidencia->getAsesorio());
            //  dump(strtolower($incidencia->getAsesorio()));
            // die();


            if (is_iterable($asesorio)) {

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio[0]->getId())
                    ->getQuery();
                //dump($query);die();
            } else {
                // dump("entre");die();
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio->getId())
                    ->getQuery();
            }


            $query->execute();
            //  dump("no entre a ningun lado ni mier...");die();
            $this->addFlash('success', 'Movimiento Creado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }

        if ($tiene == 'no') {
            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'incidencia' => $incidencia, 'inventario' => '', 'asesorio' => $asesorio, 'equipo' => $equipo]);
        } else {

            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'incidencia' => $incidencia, 'inventario' => $inventario, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'equipo' => $equipo]);
        }
    }


    /**
     * @Route("incidencia/{id}/{id_inci}/movimientoR",name="incidencia_movimientoR")
     */
    public function movimientoRAction(Request $request, $id, $id_inci, \Swift_Mailer $mailer)
    {
        $movimiento = new movimiento();
        $movimiento->setEntidadEntrega('Taller TECUN');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\movimientoReparadoFormType', $movimiento);
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository(incidencia::class)->find($id_inci);
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        $incidencia->setTipo($tipoIncidencia);
        //  dump($incidencia->getAsesorio());die();
        $id_invetario = $incidencia->getInventario();
 //dump($incidencia);die();
        $inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());
        // $inventario='';
        $tiene = '';
        $asesorio = '';
        if ($incidencia->getAsesorio() == 'CPU-Chasis' || $incidencia->getAsesorio() == 'cpuchasis') {
            // dump("hola");die();
            //$asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.id = :idP')
                ->setParameter('idP', $id)
                ->getQuery();
            $asesorio = $query->getResult();
            $fuente = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['estacion2' => $id_invetario]);
            $tiene = 'si';
            //  dump($asesorio);die();
        } else {
            //$asesorio = $entityManager->getRepository(equipo::class)->findBy(['estacion' => $inventario[0]->getId()]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.id = :idP')
                ->setParameter('idP', $id)
                ->getQuery();
            $asesorio = $query->getResult();
            $tiene = 'no';
            // dump($asesorio);die;
        }

        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $tipo = $tipoForm->getData();
            $departamento_Destino = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $tipo->getAreaDestino()]);
            $inventario_Destino = $entityManager->getRepository('AppBundle:inventario')->find($request->request->get('estaciones')[0]);

            //  dump($tipo);dump($inventario_Destino);dump($id_invetario);die();
            $tipo->setTipoMovimiento('Regreso de taller');
            $tipo->setPeriferico($incidencia->getAsesorio());
            //$tipo->setsetRespEntrega($respEntrega);
            // $fecha_actual = date("d/m/Y g:ia");
            $movimiento->setAreaDestino($departamento_Destino[0]);
            $movimiento->setInventario($inventario_Destino);
            $tipo->setFechaEntrega(new \DateTime("now"));
            $tipo->setIncidencia($incidencia);
            $tipo->setTecnico($this->getUser()->getUsername());
            if (is_iterable($asesorio)) {
                //dump("arreglo");die();
                $asesorio[0]->setEstado('Reparado');
                $asesorio[0]->setDepartamento($departamento_Destino[0]);
                $asesorio[0]->setEstacion($inventario_Destino);
                // dump($asesorio[0]->getEstado());die();
            } else {
                //dump("objeto");die();
                $asesorio->setDepartamento($departamento_Destino[0]);
                $asesorio->setEstacion($inventario_Destino);
                $asesorio->setEstado('Reparado');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipo);
            $equipo = strtolower($incidencia->getAsesorio());
            //  dump(strtolower($incidencia->getAsesorio()));
            // die();
            $incidencia->setEstado('Reparado');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setDpto($inventario[0]->getCentroCosto());
            $incidencia->setIdE($asesorio[0]->getId());
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Equipo reparado');
            // dump($inventario[0]);die();
            $incidencia->setInventario($inventario[0]);
            $incidencia->setTipoMov('Reparacion');
            $incidencia->setAsesorio($equipo);
            $incidencia->setResumen('Equipo que regresa de taller');
            $incidencia->setTecAsignado($this->getUser()->getUsername());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));
//
//            $incidenciaR=new incidencia();
//            $incidenciaR->setTipo($incidencia->getTipoMov());
//            $incidenciaR->setEstado('Reparado');
//            $incidenciaR->setAsunto('Equipo reparado');
//            $incidenciaR->setRespuesta('Equipo que regresa de taller');
//            $incidenciaR->setUser($this->getUser()->getUsername());
//            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
//            $incidenciaR->setDpto($inventario[0]->getCentroCosto());
//            $incidenciaR->setIdE($asesorio[0]->getId());
//            $incidenciaR->setNumInventario($asesorio[0]->getNumInventario());
//            $incidenciaR->setFecha(new \DateTime("now"));
//            $incidenciaR->setRespuesta('Equipo reparado');
//            // dump($inventario[0]);die();
//            $incidenciaR->setInventario($inventario[0]);
//            $incidenciaR->setTipoMov('Reparacion');
//            $incidenciaR->setAsesorio($equipo);
//            $incidenciaR->setTecAsignado($this->getUser()->getUsername());
//            $incidenciaR->setCorreo($this->getUser()->getEmail());
//            $incidenciaR->setFechaA(new \DateTime("now"));
//  dump($tipo);
//      dump($inventario_Destino);
//      dump($asesorio);
//      die();
            $entityManager->persist($asesorio[0]);
            $entityManager->persist($incidencia);
            $entityManager->persist($tipo);
//            dump($tipo);
//            die();
            $entityManager->flush();
            if (is_iterable($asesorio)) {
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio[0]->getId())
                    ->getQuery();
                //dump($asesorio);die();
                // dump("holaa");die();
            } else {
                //dump("entre");die();
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio->getId())
                    ->getQuery();
            }
            $query->execute();
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
            //  dump("no entre a ningun lado ni mier...");die();
            $this->addFlash('success', 'Movimiento Creada Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();


        if ($tiene == 'no') {
            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'areas' => $areas, 'usuarios' => $usuarios, 'incidencia' => $incidencia, 'inventario' => $inventario, 'asesorio' => $asesorio, 'equipo' => $asesorio[0]->getTipoEquipo()]);
        } else {

            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'areas' => $areas, 'incidencia' => $incidencia, 'inventario' => $inventario, 'asesorio' => $asesorio, 'equipo' => $asesorio[0]->getTipoEquipo(), 'fuente' => $fuente, 'mother' => $mother,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'usuarios' => $usuarios]);
        }
    }


    /**
     * @Route("incidencia/{id}/{equipo}/{id_inci}/movimiento23",name="movimientoSI")
     */
    public function movimientoRSIAction(Request $request, $id, $equipo, $id_inci, \Swift_Mailer $mailer)
    {
        $incidencia = '';

        $movimiento = new movimiento();
        $movimiento->setEntidadEntrega('Taller TECUN');
        $movimiento->setDireEntrega('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $movimiento->setTipoMovimiento('Regreso de taller');
        $movimiento->setTecnico('');
        $movimiento->setInventario('');
        $movimiento->setPeriferico($equipo);
        $tipoForm = $this->createForm('AppBundle\Form\movimientoReparadoFormType', $movimiento);

        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository(incidencia::class)->find($id_inci);
        $tipoIncidencia = $entityManager->getRepository(tipo::class)->find('3');
        //$id_invetario = $incidencia->getInventario();
        // $inventario = $entityManager->getRepository(inventario::class)->Todo($id_invetario->getId());

        $tiene = '';
        $asesorio = '';


        if ($equipo == 'CPU-Chasis' || $equipo == 'cpuchasis') {

            // dump("hola");die();
            //$asesorio = $id_invetario->getChasis();
            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id]);
            $fuente = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $mother = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $micro = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $ram = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $hdd = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $lector = $entityManager->getRepository(componente::class)->findBy(['cpu' => $asesorio]);
            $tiene = 'si';


            //  dump($asesorio);die();
        } else {

            $asesorio = $entityManager->getRepository(equipo::class)->findBy(['id' => $id]);
            $tiene = 'no';

        }
        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
//    dump($equipo);
//    dump($tipoForm);
//    die();
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {

            // $incidencia = new incidencia();
            $incidencia->setTipo($tipoIncidencia);
            $incidencia->setEstado('Reparado');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setAsunto('Reparacion');
            $incidencia->setResumen('Equipo reparado que regresa de taller');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Equipo reparado que regresa de taller');
            $id_inventario = $request->request->get('estaciones');

            $inventario = $entityManager->getRepository(inventario::class)->Todo($id_inventario);
            $idCosto = $request->request->get('app_bundlemovimientoR_form_type')['areaDestino'];
            $departam = $entityManager->getRepository(departamento::class)->findBy(['idCosto' => $idCosto]);
            // dump($request);dump($departam);die();
            $incidencia->setInventario($inventario[0]);
            $incidencia->setTipoMov('Regreso de taller');
            $incidencia->setAsesorio($equipo);
            $incidencia->setTecAsignado($this->getUser()->getUsername());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setIdE($id);
            $incidencia->setDpto($departam[0]);
            //  dump($incidencia);die();
            //$entityManager->persist($asesorio[0]);
            $entityManager->persist($incidencia);


            $tipo = $tipoForm->getData();

            $tipo->setTipoMovimiento($incidencia->getTipoMov());
            $tipo->setInventario($inventario[0]);
            $tipo->setPeriferico($equipo);
            $tipo->setAreaDestino($departam[0]);
            //$tipo->setsetRespEntrega($respEntrega);
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);
            $tipo->setTecnico($this->getUser());
            $tipo->setFechaEntrega($fecha_actual);
            $tipo->setIncidencia($incidencia);

//      dump($tipo);
//      die();
            if (is_iterable($asesorio)) {

                $asesorio[0]->setEstado('Activo');
                $asesorio[0]->setDepartamento($departam[0]);
                $asesorio[0]->setEstacion($inventario[0]);
                $entityManager->persist($asesorio[0]);
                // dump($asesorio[0]->getEstado());die();
            } else {
                //dump("objeto");die();
                $asesorio->setEstado('Activo');
                $asesorio->setDepartamento($departam[0]);
                $asesorio->setEstacion($inventario[0]);
                $entityManager->persist($asesorio);
            }


//      dump($tipo);
//      dump( $asesorio[0]);
//      dump($incidencia);
//      die();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipo);
            //$entityManager->persist($incidencia);
            $entityManager->flush();

            //$equipo = strtolower($incidencia->getAsesorio());
            //  dump(strtolower($incidencia->getAsesorio()));
            // die();


            if (is_iterable($asesorio)) {

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio[0]->getId())
                    ->getQuery();
                //dump($query);die();
            } else {
                // dump("entre");die();
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $asesorio->getId())
                    ->getQuery();
            }


            $query->execute();
            //  dump("no entre a ningun lado ni mier...");die();
            $this->addFlash('success', 'Movimiento Creada Correctamente');
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

            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        if ($tiene == 'no') {
            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'areas' => $areas, 'usuarios' => $usuarios, 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'equipo' => $equipo]);
        } else {

            return $this->render('incidencia/movimientoReparado.html.twig', ['movimientoForm2' => $tipoForm->createView(), 'incidencia' => $incidencia, 'asesorio' => $asesorio, 'fuente' => $fuente, 'mother' => $mother,
                'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'equipo' => $equipo, 'usuarios' => $usuarios, 'areas' => $areas]);
        }
    }


    /**
     * @Route("/incidencia/list", name="incidencia_list")
     * @Method("GET")
     * @return Response
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');


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
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        if (empty($this->filters) != true) {

            $paginator = $this->get('knp_paginator');

            $incidencias = $applicationRepository->findByFiltersAndPaginate(
                $this->filters,
                null,
                $this->pagination
            );

            return $this->render(
                'incidencia/list.html.twig',
                [
                    'areas' => $area,
                    'incidencias' => $incidencias,
                    "filters" => $this->filters,
                    "pagination" => $this->pagination
                ]
            );
        }
        $incidencias = $applicationRepository->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $incidencias,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render(
            'incidencia/list.html.twig', array('pagination' => $pagination, 'incidencias' => $incidencias, 'areas' => $area,
                "filters" => $this->filters)

        );
    }

    /**
     * @Route("/incidencia/filter",name="incidencia_filter")
     * @Method("POST")
     * @param Request $request
     *
     * @return Response
     */
    public function filterAction(Request $request)
    {
        if ($request->request->get('reset') != 1) {
            $this->filters = [
                'tipo' => $request->request->get('tipo'),
                'dpto' => $request->request->get('dpto'),
                'estado' => $request->request->get('estado')

            ];
        } else $this->filters = [];

        if ($request->request->has('limit')) {
            $this->pagination = [
                'limit' => $request->request->get('limit'),
                'offset' => $request->request->get('offset'),
            ];
        }

        return $this->listAction();
    }

    /**
     * @Route("/reportes/estaciones/filtrar_incidencias",name="incidencia_filter2")
     */
    public function filtraIncidAction(Request $request)
    {
//        dump($request);
//         die();
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


        $id_estacion = $request->get('estaciones');
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($id_estacion == '') {
            $this->addFlash('alerta', 'Usted debe seleccionar el departamento, para buscar');
        }
        if ($id_estacion != '') {
            $entityManager = $this->getDoctrine()->getManager();
            $applicationRepository = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);
            //  $estacion = $applicationRepository;
            $em = $this->get('doctrine.orm.entity_manager');
            // dump($applicationRepository);
            // die();
            $dql = "SELECT a FROM AppBundle:incidencia a WHERE a.inventario = " . $applicationRepository->getId() . "";
            $query = $em->createQuery($dql);
//            dump($id_estacion);
//            dump($query->execute());
//            die();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)

            );
            $lista = $query->execute();

//             dump( $lista);
//              die();
            // dump($estacion);
            //  die();
            return $this->render('incidencia/list.html.twig', ['pagination' => $pagination, 'areas' => $area, 'lista' => $lista]);
        } else
            $this->addFlash('alert', 'No existen incidencias reportadas para la estación seleccionada');
        return $this->redirectToRoute('lista_inci');
    }

    /**
     * @Route("reportes/lista_incidencias/{maxItemPerPage}", name="lista_inci")
     */
    public function listaInciAction(Request $request, $maxItemPerPage = 10)
    {
//        if($request->query->get('estados')){
//            $estados=$request->query->get('estados');
//        }
//        dump($estados);
//        die();
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        $mov = 'Movimiento Activo Fijo';
        $mov2 = 'Edicion';
        $usuario_actual = $this->getUser()->getUsername();
        $inventarios = '';
        //dump($usuario_actual);die();
        //if ($this->getUser()->getRol() == 'ROLE_USER' or $this->getUser()->getRol() == 'ROLE_JEFE_DEP' or $this->getUser()->getRol() == 'ROLE_AFT') {
        if ($this->isGranted('ROLE_USER')or $this->isGranted('ROLE_JEFE_DEP') or $this->isGranted('ROLE_AFT') ){
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
            $query = $repository->createQueryBuilder('i')
                ->where("i.user = '$usuario_actual'")
                ->andWhere("i.tipo !='$mov2'")
                ->andWhere("i.tipo !='$mov'")
                ->orderBy('i.fecha', 'desc')
//        ->distinct('!tabla.tipoEquipo', !'tabla.fecha', !'tabla.tipo=Edicion')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))

                ->getQuery();
            $products = $query->getResult();

        } else {
            $inventarios = $applicationRepository->findAll();
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
            $query = $repository->createQueryBuilder('i')
                ->select('i')
                ->where("i.tipo !='$mov'")
                ->andWhere("i.tipo !='$mov2'")
                ->orderBy('i.fecha', 'desc')
                ->distinct('!i.tipoEquipo', !'i.fecha', !'i.tipo=Edicion')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            // dump($query->execute());die();

            $products = $query->getResult();
            // dump($query);die();
        }
        // dump($query->getArrayResult());die();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
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

            $entityManager = $this->getDoctrine()->getManager();
            //$chasis = $entityManager->getRepository('AppBundle:cpuchasis')->findOneBy(['inventario'=>$inventarios[0]->getId()]);
            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }

        $dql = "SELECT e.numInventario FROM AppBundle:equipo e INNER JOIN AppBundle:incidencia i WHERE i.idE = e.id ORDER BY i.id";
        $query = $entityManager->createQuery($dql);
        $numeros = $query->execute();

        $listaDatosMov = [];
        $cont = 0;
        foreach ($products as $p) {
            // if($p->getEstado()=='Traslado' or $p->getEstado()=='Reparacion'or $p->getEstado()=='Reparación'or $p->getEstado()=='Reparado'or $p->getEstado()=='Reposicion'){
            if ($p->getEstadoMovimiento() == "Pendiente") {
                $listaDatosMov[$cont]['numeroI'] = $p->getNumInventario();
                $listaDatosMov[$cont]['fecha'] = $p->getFecha();
                $listaDatosMov[$cont]['idIncidencia'] = $p->getId();
                $listaDatosMov[$cont]['tipoMovimiento'] = $p->getTipoMov();
                $listaDatosMov[$cont]['estadoActual'] = $p->getEstado();
            }

            $cont = $cont + 1;
        }
//        dump($listaDatosMov);
//        die();
        //dump($query->execute());die();
        return $this->render(
            'incidencia/list.html.twig', array('pagination' => $pagination, 'inventarios' => $inventarios, 'numeros' => $numeros,
            'areas' => $area, 'lista' => $listaDatosMov

        ));
    }


    /**
     * @Route("reportes/incidencias_historial/{maxItemPerPage}/{idequipo}", name="lista_incidencias_historial")
     */
    public function listaHistorialInciMovAction(Request $request, $idequipo, $maxItemPerPage = 10)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        //$mov='Movimiento Activo Fijo';
        // $inventarios = $applicationRepository->findAll();
        $equipo = $entityManager->getRepository('AppBundle:equipo')->find($idequipo);
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $products = $repository->createQueryBuilder('tabla')
            ->where('tabla.idE = :ide')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('ide', $idequipo)
            ->orderBy('tabla.id', 'DESC')
            ->getQuery()->getArrayResult();

//    dump($products);
//    die();
        $page = (int)$request->query->get('page', 1);

        $entity = AuditHelper::paramToNamespace(equipo::class);
        // dump($entity);die();
        $reader = $this->container->get('dh_doctrine_audit.reader');
        $entries = $reader->getAuditsPager($entity, $equipo->getId(), $page, AuditReader::PAGE_SIZE);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        $dql = "SELECT e.numInventario FROM AppBundle:equipo e INNER JOIN AppBundle:incidencia i WHERE i.idE = e.id ORDER BY i.id";
        $query = $entityManager->createQuery($dql);
        $numeros = $query->execute();
        //dump($query->execute());die();
        return $this->render(
            'estacion_trabajo/historial_incidencias.html.twig', array('pagination' => $pagination, 'entries' => $entries, 'entity' => $entity, 'id' => $equipo->getId(), 'inventarios' => $products, 'numeros' => $numeros

        ));
    }

    /**
     * @Route("reportes/incidencias_historial_estacion/{maxItemPerPage}/{idEstacion}", name="lista_incidencias_estacion")
     */
    public function listaHistorialEstMovAction(Request $request, $idEstacion, $maxItemPerPage = 10)
    {

        $entityManager = $this->getDoctrine()->getManager();
        // $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        //$mov='Movimiento Activo Fijo';
        // $inventarios = $applicationRepository->findAll();

        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($idEstacion);
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $products = $repository->createQueryBuilder('tabla')
            ->where('tabla.inventario = :ide')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('ide', $inventario)
            ->orderBy('tabla.id', 'DESC')
            ->getQuery()->getArrayResult();

        $page = (int)$request->query->get('page', 1);

        $entity = AuditHelper::paramToNamespace(inventario::class);
        // dump($entity);die();
        $reader = $this->container->get('dh_doctrine_audit.reader');
        $entries = $reader->getAuditsPager($entity, $inventario->getId(), $page, AuditReader::PAGE_SIZE);

        // dump($entries);die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        $dql = "SELECT e.numInventario FROM AppBundle:equipo e INNER JOIN AppBundle:incidencia i WHERE i.idE = e.id ORDER BY i.id";
        $query = $entityManager->createQuery($dql);
        $numeros = $query->execute();
        //dump($query->execute());die();
        return $this->render(
            'estacion_trabajo/historial_incidencias.html.twig', array('pagination' => $pagination, 'inventarios' => $products, 'entries' => $entries, 'id' => $inventario->getId(),
            'entity' => $entity, 'numeros' => $numeros

        ));
    }

    /**
     * @Route("reportes/auditoria_estacion/{maxItemPerPage}/{idEstacion}", name="auditoria_estacion")
     */
    public function listaAuditoriaEstacionAction(Request $request, $idEstacion, $maxItemPerPage = 10)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($idEstacion);
        $estacion = 'AppBundle-Entity-inventario';
        $page = (int)$request->query->get('page', 1);
        $entity = AuditHelper::paramToNamespace($estacion);
        $reader = $this->container->get('dh_doctrine_audit.reader');
        $entries = $reader->getAuditsPager($entity, $idEstacion, $page, AuditReader::PAGE_SIZE);

        //  dump($entries);die();

        return $this->render('historial_incidencias/auditorias_estacion.twig', [
            'id' => $idEstacion,
            'entity' => $entity,
            'entries' => $entries,
        ]);

    }


    /**
     * @Route("/incidencia/filter/periferico",name="incidencia_filter_peridefico")
     * @Method("POST")
     * @param Request $request
     *
     * @return Response
     */
    public function filterPAction(Request $request)
    {
        if ($request->request->get('reset') != 1) {
            $this->filters = [
                'marca' => $request->request->get('marca'),
                'serie' => $request->request->get('serie'),

            ];
        } else $this->filters = [];

        if ($request->request->has('limit')) {
            $this->pagination = [
                'limit' => $request->request->get('limit'),
                'offset' => $request->request->get('offset'),
            ];
        }

        return $this->listPAction();
    }

    /**
     * @Route("/incidencia/listP", name="incidencia_listP")
     * @Method("GET")
     * @return Response
     */
    public function listPAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:monitor');

        $incidencias = $applicationRepository->findAll();

        return $this->render(
            'incidencia/list.html.twig',
            [
                'incidencias' => $incidencias,
                "filters" => $this->filters,
                "pagination" => $this->pagination
            ]
        );
    }

    /**
     * @Route("incidencia/new", name="incidencia_new")
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $incidencia = new incidencia();
        $incidencia->setTecAsignado($this->getUser());
        $incidenciaForm = $this->createForm('AppBundle\Form\incidenciaForm', $incidencia);
//
        $entityManager = $this->getDoctrine()->getManager();
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        //$toners=$entityManager->getRepository('AppBundle:toner')->findAll();

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:toner');
        $toners = $repository->createQueryBuilder('tabla')
            ->where('tabla.cantidad >:cant')
            ->setParameter('cant', 0)
            ->getQuery()->getResult();
        //dump($toners);die();

        $incidenciaForm->handleRequest($request);

        $estacion = null;

        if ($incidenciaForm->isSubmitted() && $incidenciaForm->isValid()) {
            //  dump($request);die();
            $entityManager = $this->getDoctrine()->getManager();
          //  $rol = $this->getUser()->getRol();
            $incidencia = $incidenciaForm->getData();

            $departamento = $request->get('usuarios')[0];
            $id_estacion = $request->get('e')[0];
            //  dump($id_estacion);die();
            if ($id_estacion != null && $departamento != null) {
                $estacion = $entityManager->getRepository('AppBundle:inventario')->find($id_estacion);
            }
            $numeroInventario = $request->request->get('numInv');
//            dump($request);
//           // dump($rol);
//            die();
            $id_categoria = $request->get('app_bundleincidencia_form')['tipo'];
            $categoria = $entityManager->getRepository('AppBundle:tipo')->findBy(['id' => $id_categoria]);
         //   if ($rol == 'ROLE_USER' or $rol == 'ROLE_AFT' or $rol == 'ROLE_JEFE_DEP') {
                if($this->isGranted('ROLE_USER')or $this->isGranted('ROLE_AFT')or $this->isGranted('ROLE_JEFE_DEP')){
                $user = $this->getUser()->getUsername();

                $fecha_actual = new \DateTime("now");
                $incidencia->setUser($user);
                $incidencia->setEstado('En Espera');
                $incidencia->setInventario($estacion);
                $incidencia->setFecha($fecha_actual);
                $incidencia->setDpto($departamento);
                $incidencia->setIdE(0);

                $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
                $email = $this->getUser()->getEmail();

                $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);

                $incidencia->setInventario($inventario);
                $incidencia->setCorreo($email);


                $entityManager->persist($incidencia);
                $entityManager->flush();

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
//        dump("Soy usuario basico");
//        die();
                $mailer->send($message);
                $this->addFlash('success', 'Incidencia Procesada Correctamente, le mantendremos informado vía correo ');
                return $this->render('incidencia/mostrar.html.twig', ['incidencia' => $incidencia]);
            } elseif ($id_categoria == 6 && ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_TECNICO') )) {
                // dump($request);die();

                //Realizar incidencia con las piezas seleccionadas,como deuda del chasis del inventario seleccionado
                $piezas = $request->request->get('partesP');
                if ($piezas != null) {
                    $zonaHoraria = new \DateTimeZone('America/Cuiaba');
                    $fecha_actual = new \DateTime("now", $zonaHoraria);
                    $user = $this->getUser()->getUsername();
                    $incidenciaDeuda = new incidencia();
                    $incidenciaDeuda->setUser($user);
                    $incidenciaDeuda->setEstado('Activa');
                    $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:equipo');

                    if ($estacion != null) {
                        $chasisDeuda = $repository->createQueryBuilder('tabla')
                            ->where('tabla.estacion = :estacion')
                            ->setParameter('estacion', $estacion)
                            ->andWhere('tabla.tipoEquipo=:tipo')
                            ->setParameter('tipo', 'cpuchasis')
                            ->getQuery()->getResult()[0];

                        $incidenciaDeuda->setInventario($estacion);
                        $incidenciaDeuda->setFecha($fecha_actual);
                        $incidenciaDeuda->setFechaA($fecha_actual);
                        $incidenciaDeuda->setDpto($departamento);
                        $incidenciaDeuda->setIdE($chasisDeuda->getId());
                        $incidenciaDeuda->setAsesorio($chasisDeuda->getTipoEquipo());
                        $incidenciaDeuda->setNumInventario($numeroInventario);
                        $incidenciaDeuda->setRespuesta($categoria[0]->getName());
                        $incidenciaDeuda->setTipoMov('Pc defectuosa');
                        $incidenciaDeuda->setTipo('Pc defectuosa');
                        $incidenciaDeuda->setTecAsignado($this->getUser());
                        $incidenciaDeuda->setAsunto('Incidencia de PC defectuosa');

                        $total = sizeof($piezas);
                        $piezasN = '';


                        for ($i = 0; $i < $total; $i++) {
                            $elemento = $piezas[$i];
                            $repository2 = $this->getDoctrine()
                                ->getRepository('AppBundle:componente');
                            $piezaDeuda = $repository2->createQueryBuilder('tabla')
                                ->where('tabla.estacion2 = :inv')
                                ->setParameter('inv',$estacion)
                                ->andWhere('tabla.tipoComponente=:tipo')
                                ->setParameter('tipo', $elemento)
                                ->getQuery()->getResult();
                          //  dump($piezaDeuda);die();
                            if ($piezaDeuda != []) {
                                $piezaDeuda[0]->setDeuda('Si');
                                $deudaComponente = new deuda();
                                $deudaComponente->setTipoComponente($elemento);
                                $deudaComponente->setEstacion($estacion);
                                $entityManager->persist($deudaComponente);
                              // dump($piezaDeuda);die();
                            }

                            $piezasN = $piezasN . ' ' . $elemento;
                        };
                    } else {

                        $chasisDeuda = $repository->createQueryBuilder('tabla')
                            ->where('tabla.numInventario = :numero')
                            ->setParameter('numero', $numeroInventario)
                            ->getQuery()->getResult()[0];
                        //  dump($equipoDeuda);die();
                        $incidenciaDeuda->setInventario(null);
                        $incidenciaDeuda->setFecha($fecha_actual);
                        $incidenciaDeuda->setFechaA($fecha_actual);
                        $incidenciaDeuda->setDpto($departamento);
                        $incidenciaDeuda->setIdE($chasisDeuda->getId());
                        $incidenciaDeuda->setAsesorio($chasisDeuda->getTipoEquipo());
                        $incidenciaDeuda->setNumInventario($numeroInventario);
                        $incidenciaDeuda->setRespuesta($categoria[0]->getName());
                        $incidenciaDeuda->setTipoMov('Pc defectuosa');
                        $incidenciaDeuda->setTipo('Pc defectuosa');
                        $incidenciaDeuda->setTecAsignado($this->getUser());
                        $incidenciaDeuda->setAsunto('Incidencia de PC defectuosa');

                        $total = sizeof($piezas);
                        $piezasN = '';


                        for ($i = 0; $i < $total; $i++) {
                            $elemento = $piezas[$i];
                            $repository2 = $this->getDoctrine()
                                ->getRepository('AppBundle:componente');
                            $piezaDeuda = $repository2->createQueryBuilder('tabla')
                                ->where('tabla.estacion2 = :inv')
                                ->setParameter('inv', $estacion)
                                ->andWhere('tabla.tipoComponente=:tipo')
                                ->setParameter('tipo', $elemento)
                                ->getQuery()->getResult();
                            if ($piezaDeuda != []) {
                                $piezaDeuda[0]->setDeuda('Si');
                                $deudaComponente = new deuda();
                                $deudaComponente->setTipoComponente($elemento);
                                $deudaComponente->setEstacion($estacion);
                                $entityManager->persist($deudaComponente);
                                // dump($piezaDeuda);
                            } else {
                                $deudaComponente = new deuda();
                                $deudaComponente->setTipoComponente($elemento);
                                $deudaComponente->setEstacion($estacion);
                                $entityManager->persist($deudaComponente);
                                //   dump($deudaComponente);
                            }
                        }

                        $piezasN = $piezasN . ' ' . $elemento;
                    };


                    //    dump($incidenciaDeuda);die();
                    // die();
                    $incidenciaDeuda->setResumen(
                        'Total de piezas con defectos:' . sizeof($piezas) . '   ' .
                        'Piezas:' . $piezasN
                    );
//                dump($piezas);
//                dump($incidenciaDeuda);
//                die();
                    $entityManager->persist($incidenciaDeuda);
                }

                $user = $this->getUser()->getUsername();
                $fecha_actual = new \DateTime("now");
                $incidencia->setUser($user);
                $incidencia->setEstado('Solucionado');
                $incidencia->setInventario($estacion);
                $incidencia->setFecha($fecha_actual);
                $incidencia->setFechaA($fecha_actual);
                $incidencia->setDpto($departamento);
                $incidencia->setRespuesta($categoria[0]->getName());
                $incidencia->setTipoMov('Instalacion');
                $incidencia->setIdE(0);
                // dump($categoria);
                // $incidencia->setRespuesta($request->get('app_bundleincidencia_form')['name']);
                $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
                $email = $this->getUser()->getEmail();
              //  $rol = $this->getUser()->getRol();
                $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);

                $incidencia->setInventario($inventario);
                $incidencia->setCorreo($email);
//
//        dump($incidencia);
//        die();
                // $entityManager->persist($incidencia);
                $entityManager->flush();
                //dump($incidencia);die();
//        dump("Soy usuario admin");
//        die();
                // return $this->render('estacion_trabajo/nuevosEquiposAInventario.html.twig');
                return $this->redirectToRoute('annadirAInv', ['nombre_estacion' => $estacion->getNombreRed(), 'asunto' => $incidencia->getAsunto(), 'resumen' => $incidencia->getResumen(), 'piezas' => $piezas]);
            } elseif ($id_categoria == 3 && ($this->isGranted('ROLE_ADMIN')or $this->isGranted('ROLE_TECNICO'))) {
                $user = $this->getUser()->getUsername();
                $zonaHoraria = new \DateTimeZone('America/Cuiaba');
                $fecha_actual = new \DateTime("now", $zonaHoraria);
                $incidencia->setUser($user);
                $incidencia->setEstado('Solucionado');
                $incidencia->setEstadoMovimiento(null);
                $incidencia->setInventario($estacion);
                $incidencia->setFecha($fecha_actual);
                $incidencia->setFechaA($fecha_actual);
                $incidencia->setDpto($departamento);
                $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);
                 $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:equipo');
                        $chasis = $repository->createQueryBuilder('tabla')
                            ->where('tabla.estacion = :est')
                            ->setParameter('est', $inventario)
                            ->andWhere('tabla.tipoEquipo=:tipo')
                            ->setParameter('tipo', 'cpuchasis')
                            ->getQuery()->getResult()[0];
                $incidencia->setNumInventario($chasis->getNumInventario());
                $incidencia->setRespuesta($categoria[0]->getName());
                $incidencia->setTipoMov('Instalacion');
                $incidencia->setIdE($chasis->getId());
                // dump($categoria);
                // $incidencia->setRespuesta($request->get('app_bundleincidencia_form')['name']);
                $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
                $email = $this->getUser()->getEmail();
                //$rol = $this->getUser()->getRol();


                $incidencia->setInventario($inventario);
                $incidencia->setCorreo($email);
//                   dump($chasis);die();

                $entityManager->persist($incidencia);
                $entityManager->flush();

                return $this->redirectToRoute('incidencia_respuesta', ['id' => $incidencia->getId()]);
            } elseif ($id_categoria == 9 && ($this->isGranted('ROLE_ADMIN')or $this->isGranted('ROLE_TECNICO'))) {
                //  dump($request);die();
                $piezas = $request->request->get('partesP');
                $zonaHoraria = new \DateTimeZone('America/Cuiaba');
                $fecha_actual = new \DateTime("now", $zonaHoraria);
                $user = $this->getUser()->getUsername();
                $incidencia->setUser($user);
                $incidencia->setEstado('Activa');
                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:equipo');


                if ($estacion != null) {

//                    if($estacion->getNombreRed()=='Almacen de Computacion'){
//
//                    }else{
//                        $chasisDeuda = $repository->createQueryBuilder('tabla')
//                            ->where('tabla.estacion = :estacion')
//                            ->setParameter('estacion', $estacion)
//                            ->andWhere('tabla.tipoEquipo=:tipo')
//                            ->setParameter('tipo', 'cpuchasis')
//                            ->getQuery()->getResult()[0];
//                    }

                    $chasisDeuda = $repository->createQueryBuilder('tabla')
                        ->where('tabla.numInventario = :numero')
                        ->setParameter('numero', $numeroInventario)
                        ->andWhere('tabla.tipoEquipo=:tipo')
                        ->setParameter('tipo', 'cpuchasis')
                        ->getQuery()->getResult()[0];
                    $incidencia->setInventario($estacion);
                    $incidencia->setFecha($fecha_actual);
                    $incidencia->setFechaA($fecha_actual);
                    $incidencia->setDpto($departamento);
                    $incidencia->setIdE($chasisDeuda->getId());
                    $incidencia->setAsesorio($chasisDeuda->getTipoEquipo());
                    $incidencia->setNumInventario($numeroInventario);
                    $incidencia->setRespuesta($categoria[0]->getName());
                    $incidencia->setTipoMov('Pc defectuosa');
                    $incidencia->setAsunto('Incidencia de PC defectuosa');
                    //   dump($incidencia);dump($request);die();
                    $total = sizeof($piezas);
                    $piezasN = '';


                    for ($i = 0; $i < $total; $i++) {
                        $elemento = $piezas[$i];
                        $repository2 = $this->getDoctrine()
                            ->getRepository('AppBundle:componente');
                        $piezaDeuda = $repository2->createQueryBuilder('tabla')
                            ->where('tabla.estacion2 = :inv')
                            ->setParameter('inv', $estacion)
                            ->andWhere('tabla.tipoComponente=:tipo')
                            ->setParameter('tipo', $elemento)
                            ->getQuery()->getResult();


                        if ($piezaDeuda != []) {
                            $piezaDeuda[0]->setDeuda('Si');
                            $deudaComponente = new deuda();
                            $deudaComponente->setTipoComponente($elemento);
                            $deudaComponente->setEstacion($estacion);
                            $entityManager->persist($deudaComponente);
                        } else {
                            $deudaComponente = new deuda();
                            $deudaComponente->setTipoComponente($elemento);
                            $deudaComponente->setEstacion($estacion);
                            $entityManager->persist($deudaComponente);
                        }
                        $piezasN = $piezasN . ' ' . $elemento;
                    }
                } else {
                    $chasisDeuda = $repository->createQueryBuilder('tabla')
                        ->where('tabla.numInventario = :numero')
                        ->setParameter('numero', $numeroInventario)
                        ->andWhere('tabla.tipoEquipo=:tipo')
                        ->setParameter('tipo', 'cpuchasis')
                        ->getQuery()->getResult()[0];


                    $incidencia->setInventario(null);
                    $incidencia->setFecha($fecha_actual);
                    $incidencia->setFechaA($fecha_actual);
                    $incidencia->setDpto(null);
                    $incidencia->setIdE($chasisDeuda->getId());
                    $incidencia->setAsesorio($chasisDeuda->getTipoEquipo());
                    $incidencia->setNumInventario($numeroInventario);
                    $incidencia->setRespuesta($categoria[0]->getName());
                    $incidencia->setTipoMov('Pc defectuosa');
                    $incidencia->setAsunto('Incidencia de PC defectuosa');
                    //   dump($incidencia);dump($request);die();
                    $total = sizeof($piezas);
                    $piezasN = '';


                    for ($i = 0; $i < $total; $i++) {
                        $elemento = $piezas[$i];
                        $repository2 = $this->getDoctrine()
                            ->getRepository('AppBundle:componente');
                        $piezaDeuda = $repository2->createQueryBuilder('tabla')
                            ->where('tabla.estacion2 = :inv')
                            ->setParameter('inv', $estacion)
                            ->andWhere('tabla.tipoComponente=:tipo')
                            ->setParameter('tipo', $elemento)
                            ->getQuery()->getResult();
                        if ($piezaDeuda != []) {
                            $piezaDeuda[0]->setDeuda('Si');
                            $deudaComponente = new deuda();
                            $deudaComponente->setTipoComponente($elemento);
                            $deudaComponente->setEstacion($estacion);
                            $entityManager->persist($deudaComponente);
                            // dump($piezaDeuda);
                        } else {
                            $deudaComponente = new deuda();
                            $deudaComponente->setTipoComponente($elemento);
                            $deudaComponente->setEstacion($estacion);
                            $entityManager->persist($deudaComponente);
                            //   dump($deudaComponente);
                        }

                        $piezasN = $piezasN . ' ' . $elemento;
                    }
                }

                // die();
                $incidencia->setResumen(
                    'Total de piezas con defectos:' . sizeof($piezas) . '   ' .
                    'Piezas:' . $piezasN
                );
                $entityManager->persist($incidencia);
                $entityManager->flush();

//                dump($piezasN);
//                dump($request);die();


                //  dump($request);die();
                return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
                //dump($piezasN);dump($incidencia);die();
            } elseif ($id_categoria == 1 && ($this->isGranted('ROLE_ADMIN')or  $this->isGranted('ROLE_TECNICO'))) {
                $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);
                //dump($inventario); dump($request);dump($request->request->get('app_bundleincidencia_form')['fecha']);die();
                $user = $this->getUser()->getUsername();
                $zonaHoraria = new \DateTimeZone('America/Cuiaba');
                $fecha_actual = new \DateTime("now", $zonaHoraria);
                $incidencia->setUser($user);
                $incidencia->setEstado('Mantenimiento PC');
                $incidencia->setInventario($estacion);
                $incidencia->setFecha($fecha_actual);
                $incidencia->setFechaA($fecha_actual);
                $incidencia->setDpto($departamento);
                $incidencia->setRespuesta($categoria[0]->getName());
                $incidencia->setTipoMov('Mantenimiento PC');
                $incidencia->setAsunto('Mantenimiento PC');
                $fechaM = $request->request->get('app_bundleincidencia_form')['fecha'];

                $fechaH = new DateTime($fechaM, $zonaHoraria);
                $inventario->setfechaMantenimiento($fechaH);
                //  $inventario->setfechaMantenimiento($fechaM);

                $entityManager->persist($incidencia);
                $entityManager->flush();
                return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
            } else if ($id_categoria == 10 && ($this->isGranted('ROLE_ADMIN')or $this->isGranted('ROLE_TECNICO'))) {
                $cantidadT = $request->request->get('cantidadToner');
                $modelo = $request->request->get('modelo');
                //   dump($request);die();
                $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $id_estacion]);
                //dump($inventario); dump($request);dump($request->request->get('app_bundleincidencia_form')['fecha']);die();
                $user = $this->getUser()->getUsername();
                $zonaHoraria = new \DateTimeZone('America/Cuiaba');
                $fecha_actual = new \DateTime("now", $zonaHoraria);
                $incidencia->setUser($user);
                $incidencia->setEstado('Solucionado');
                $incidencia->setInventario($estacion);
                $incidencia->setFecha($fecha_actual);
                $incidencia->setFechaA($fecha_actual);
                $incidencia->setDpto($departamento);
                $incidencia->setRespuesta($categoria[0]->getName());
                $incidencia->setTipoMov('Instalacion de toner');
                $incidencia->setAsunto('Instalacion de toner');
                $incidencia->setResumen('Se instalo el toner ' . $modelo . " " . "Cantidad:" . " " . $cantidadT);
                $fechaM = $request->request->get('app_bundleincidencia_form')['fecha'];

                $fechaH = new DateTime($fechaM, $zonaHoraria);
                $inventario->setfechaMantenimiento($fechaH);
                //  $inventario->setfechaMantenimiento($fechaM);
                $toner = $entityManager->getRepository('AppBundle:toner')->findBy(['modelo' => $modelo])[0];
                $cantidadTipoToner = $toner->getCantidad();
                $cantidadActual = $cantidadTipoToner - $cantidadT;
                $toner->setCantidad($cantidadActual);
//                dump('Cantidad actual: '.$cantidadTipoToner);
//                dump('Cantidad despues de asignacion: '.$cantidadActual);
//                dump($toner);
//                dump($incidencia);die();
                $entityManager->persist($toner);
                $entityManager->persist($incidencia);
                $entityManager->flush();
                return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);


            }

        }
        return $this->render('incidencia/new.html.twig', ['incidenciaForm' => $incidenciaForm->createView(), 'areas' => $areas, 'toners' => $toners]);

    }

    /**
     * @Route("incidencia/{id}/respuesta",name="incidencia_respuesta")
     */

    public function respAction(Request $request, $id, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);

        $inventario_id = $incidencia->getInventario()->getId();

        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($inventario_id);
        $estacion = $inventario;


        //$chasis = $entityManager2->getRepository(equipo::class)->findBy(['estacion' => $inventario]);
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');


        $fuente = null;
        $mother = null;
        $lector = null;
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $ram = null;
        $hdd = null;
        $tarjetaV = null;

        $chasis = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipoEquipo = :tipo')
            ->setParameter('tipo', 'cpuchasis')
            ->andWhere('tabla.estacion= :estacion')
            ->setParameter('estacion', $inventario)
            ->setMaxResults(1)
            ->getQuery()->getResult();
        foreach ($chasis[0]->getComponente() as $c) {
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


        $backupquery = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipoEquipo = :tipo')
            ->setParameter('tipo', 'backup')
            ->andWhere('tabla.estacion= :estacion')
            ->setParameter('estacion', $inventario)
            ->setMaxResults(1)
            ->getQuery()->getResult();
        if ($backupquery == null) {
            $backup = null;
        } else {
            $backup = $backupquery[0];
        }
        $impresoraquery = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipoEquipo = :tipo')
            ->setParameter('tipo', 'impresora')
            ->andWhere('tabla.estacion= :estacion')
            ->setParameter('estacion', $inventario)
            ->setMaxResults(1)
            ->getQuery()->getResult();
        //->getQuery()->getResult();
        if ($impresoraquery == null) {
            $impresora = null;
        } else {
            $impresora = $impresoraquery[0];
        }
        $monitorquery = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipoEquipo = :tipo')
            ->setParameter('tipo', 'monitor')
            ->andWhere('tabla.estacion= :estacion')
            ->setParameter('estacion', $inventario)
            ->setMaxResults(1)
            ->getQuery()->getResult()[0];
        if ($monitorquery == null) {
            $monitor = null;
        } else {
            $monitor = $monitorquery;
        }
        $incidencianForm = $this->createForm('AppBundle\Form\respuestaFromType', $incidencia);

        /**
         *Star "Post only" section
         */
        $incidencianForm->handleRequest($request);

        if ($incidencianForm->isSubmitted() && $incidencianForm->isValid()) {


            $incidencia = $incidencianForm->getData();
            $tipoacesorio = $request->request->get('app_bundlerespuesta_from_type_asesorio')['asesorio'];
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.estacion = :ide')
                ->setParameter('ide', $inventario)
                ->andWhere('tabla.tipoEquipo = :asesorio')
                ->setParameter('asesorio', $tipoacesorio)
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery()->getResult();
            $acesorio = $query[0];
            // dump($acesorio);die();
            $incidencia->setAsesorio($acesorio->getTipoEquipo());

            $avanza = 1;
            if ($incidencia->getTipoMov() == '') {
                $avanza = 0;
                $this->addFlash('error', 'Error, Tiene que seleccionar la acción a realizar');
            }

            if ($incidencia->getAsesorio() == null) {
                //dump($incidencia->getAsesorio());die();
                $avanza = 0;
                $this->addFlash('error', 'Error, Tiene que seleccionar el periférico');
            }

            if ($avanza == 1) {
                $user = $this->getUser()->getUsername();
                $fecha_actual = new \DateTime("now");
                $incidencia->setTecAsignado($user);
                $incidencia->setFechaA(new \DateTime());
                $incidencia->setIdE($acesorio->getId());
                $incidencia->setnumInventario($acesorio->getNumInventario());
                $acesorio->setEstado('En taller');
                // dump($fecha_actual);die();

                if ($incidencia->getTipoMov() == 'Solucionado') {

                    $incidencia->setEstado('Solucionado');
                    if ($incidencia->getRespuesta() == null) {

                        $incidencia->setRespuesta("Se ha solucionado el problema");
                    }

                    $entityManager->persist($incidencia);
                    $entityManager->flush();


                    $email = $this->getUser()->getEmail();
                    $message = (new \Swift_Message('Sistema Control Reportes'))
                        ->setFrom('reportes@retina.sld.cu')
                        ->setTo([$email, $incidencia->getCorreo()])
                        ->setCc('informatica@listas.retina.sld.cu')
                        ->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'email/registration2.html.twig',
                            ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                        ),
                            'text/html'

                        );

                    $mailer->send($message);


                    $this->addFlash('success', 'Respuesta Procesada');
                    return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);

                }

                if ($incidencia->getTipoMov() == 'Reparación') {

                    $id_accesorio = $entityManager->getRepository('AppBundle:equipo')->findBy(['id' => $acesorio->getId()]);
                    //   dump($incidencia->getAsesorio()->getName());die();
                    $incidencia->setEstado('Reparación');
                    $incidencia->setIdE($acesorio->getId());
                    //$applicationRepository2 = $entityManager->getRepository('AppBundle:taller');

                    //[0]->setEstado('En taller');
                    if ($incidencia->getRespuesta() == null) {

                        $incidencia->setRespuesta("Se ha enviado el equipo a taller");
                    }


                    $entityManager->persist($incidencia);

                    $entityManager->flush();

                    $email = $this->getUser()->getEmail();
                    $message = (new \Swift_Message('Sistema Control Reportes'))
                        ->setFrom('reportes@retina.sld.cu')
                        ->setTo([$email, $incidencia->getCorreo()])
                        ->setCc('lisandra.hernandez@retina.sld.cu')
                        ->setBody($this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                            'email/registration2.html.twig',
                            ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia]
                        ),
                            'text/html'

                        );

                    $mailer->send($message);


                    return $this->redirectToRoute('incidencia_movimiento', ['id' => $incidencia->getId(), 'id_equipo' => $acesorio->getId()]);
                }


            }

        }

        $equiposEnEstacion = $entityManager->getRepository('AppBundle:equipo')->findTiposEquipo($inventario[0]);

        return $this->render('incidencia/respuesta.html.twig', ['incidenciaForm' => $incidencianForm->createView(), 'incidencia' => $incidencia, 'inventario' => $inventario,
            'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor, 'equipos' => $equiposEnEstacion]);

    }

    /**
     * @Route("incidencia/{id}/edit", name="incidencia_edit")
     */
    public function editAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);

        $incidencianForm = $this->createForm('AppBundle\Form\inciEditFormType', $incidencia);
        $movimientoForm = '';
        $movimiento = '';
        // dump($incidencia);die();
        if ($incidencia->getTipoMov() == 'Instalacion') {
            return $this->redirectToRoute('incidencia_edit_solucionado', ['id' => $id]);
        }
        if ($incidencia->getTipoMov() == 'Traslado Interno') {
            // $movimiento = $entityManager->getRepository('AppBundle:movimientoI')->findBy(['incidencia' => $incidencia]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimientoI');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :incidencia')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('incidencia', $incidencia)
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getArrayResult();
            // $movimiento[0]->setTipoMovimiento('Traslado');
            $movimientoForm = $this->createForm('AppBundle\Form\movimiento3FormType', $movimiento[0]);
            // dump($movimiento);die();
        } elseif ($incidencia->getTipoMov() == 'Reparacion' || $incidencia->getTipoMov() == 'Envio a taller') {
            //$movimiento = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $incidencia]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :incidencia')
                ->setParameter('incidencia', $incidencia)
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getResult();
            //$movimiento[0]->setTipoMovimiento($incidencia->getTipoMov());
            //dump($movimiento[0]);dump($incidencia);die();
            /* $movimiento[0]->setEntidadEntrega('Clínica Internacional Camilo Cienfuegos');
             $movimiento[0]->setDireEntrega('Calle L Esq. 13 Vedado');
             $movimiento[0]->setEntidadDestino('Taller TECUN');
             $movimiento[0]->setDireccionDestino('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');*/

            if (is_iterable($movimiento)) {
                $movimientoForm = $this->createForm('AppBundle\Form\movimientoReparadoFormType', $movimiento[0]);
            } else {
                $movimientoForm = $this->createForm('AppBundle\Form\movimientoReparadoFormType', $movimiento);
            }

//      dump($movimientoForm);
//      die();
        } elseif ($incidencia->getTipoMov() == 'Regreso de taller' || $incidencia->getTipoMov() == 'Reparado') {
            // $movimiento = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $incidencia]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :incidencia')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('incidencia', $incidencia)
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getResult();
//        dump($movimiento);
//        dump($incidencia);
//        die();
            //   $movimiento[0]->setTipoMovimiento($incidencia[0]->getTipoMov());
            /* $movimiento[0]->setEntidadEntrega('Taller TECUN');
             $movimiento[0]->setDireEntrega('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
             $movimiento[0]->setEntidadDestino('Clínica Internacional Camilo Cienfuegos');
             $movimiento[0]->setDireccionDestino('Calle L Esq. 13 Vedado');*/

            $movimientoForm = $this->createForm('AppBundle\Form\movimientoFormType', $movimiento[0]);
        } elseif ($incidencia->getTipoMov() == 'Reposicion') {
            // $movimiento = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $incidencia]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :incidencia')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('incidencia', $incidencia)
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getArrayResult();

            $movimiento[0]->setTipoMovimiento($incidencia->getTipoMov());
            /* $movimiento[0]->setEntidadEntrega('Taller TECUN');
             $movimiento[0]->setDireEntrega('Calle 42 #1103 e/ 7ma y 38 Miramar Playa');
             $movimiento[0]->setEntidadDestino('Clínica Internacional Camilo Cienfuegos');
             $movimiento[0]->setDireccionDestino('Calle L Esq. 13 Vedado');*/

            $movimientoForm = $this->createForm('AppBundle\Form\incidenciaReposicionFormType', $movimiento[0]);
        }

        /**
         *Star "Post only" section
         */
        $incidencianForm->handleRequest($request);
        $movimientoForm->handleRequest($request);
        //dump($movimientoForm);
        // die();

        // dump($movimientoForm);die();
        if ($incidencianForm->isSubmitted() && $incidencianForm->isValid()) {

            $incidencia = $incidencianForm->getData();
            $mov = $movimientoForm->getData();
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $incidencia->setFecha(new \DateTime('now', $zonaHoraria));
            $mov->setTecnico($this->getUser()->getUsername());
            dump($mov);
            die();

            $entityManager->persist($incidencia);
            $entityManager->persist($mov);
            $entityManager->flush();
            $this->addFlash('success', 'Incidencia Editada Correctamente');

            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        $tipoM = $incidencia->getTipoMov();
        /**
         * End "Post only" section
         */
        //dump($movimiento);die();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('incidencia/edit.html.twig', ['incidenciaForm' => $incidencianForm->createView(), 'movForm' => $movimientoForm->createView()
            , 'tipoMov' => $tipoM, 'usuarios' => $usuarios,
            'movimiento' => $movimiento[0], 'incidencia' => $incidencia
        ]);


    }


    /**
     * @Route("incidencia/{id}/edit2", name="incidencia_edit_solucionado")
     */
    public function edit2Action(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository(incidencia::class)->find($id);

        $incidencianForm = $this->createForm('AppBundle\Form\inciEditFormType', $incidencia);
        /**
         *Star "Post only" section
         */
        $incidencianForm->handleRequest($request);
        //dump($movimientoForm);
        // die();

        // dump($movimientoForm);die();
        if ($incidencianForm->isSubmitted() && $incidencianForm->isValid()) {

            $incidencia = $incidencianForm->getData();
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $incidencia->setFecha(new \DateTime('now', $zonaHoraria));
            // dump($mov);die();

            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('success', 'Incidencia Editada Correctamente');

            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId()]);
        }
        $tipoM = $incidencia->getTipoMov();
        /**
         * End "Post only" section
         */
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('incidencia/edit.html.twig', ['incidenciaForm' => $incidencianForm->createView()
            , 'tipoMov' => $tipoM, 'usuarios' => $usuarios, 'movForm' => null
        ]);


    }

    /**
     * @Route("incidencia/{id}/reporte", name="incidencia_reporte")
     */
    public function reporteAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reporte = $entityManager->getRepository('AppBundle:incidencia')->find($id);


        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('incidencia/reporte.html.twig', ['reporte' => $reporte]);

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

//    return $this->render('incidencia/reporte.html.twig', ['reporte' => $reporte]);
    }

    /**
     * @Route("incidencia/{id}/reporte2", name="incidencia_reporte2")
     */
    public function reporte2Action(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reporte = $entityManager->getRepository('AppBundle:incidencia')->find($id);
        //$chasis = $entityManager->getRepository('AppBundle:cpuchasis')->findBy(['inv' => $reporte->getInventario()->getId()]);
        $inventario = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $reporte->getInventario()]);
        $chasis = '';
        $equipo = null;
        if ($inventario == null) {
            $equipo_min = strtolower($reporte->getAsesorio());
            if ($reporte->getAsesorio() == 'CPU-Chasis' or $reporte->getAsesorio() == 'cpuchasis') {
                $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['id' => $reporte->getIdE()]);
            } elseif ($reporte->getAsesorio() == 'impresora' or $reporte->getAsesorio() == 'estabilizador' or $reporte->getAsesorio() == 'monitor' or $reporte->getAsesorio() == 'backup' or $reporte->getAsesorio() == 'mouse' or $reporte->getAsesorio() == 'teclado') {
                $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $reporte->getNumInventario()]);
                // dump($equipo);
            }
        } else {
            // dump();
            // die();
            $equipo_min = strtolower($reporte->getAsesorio());
            //dump($reporte);
            if ($reporte->getAsesorio() == 'CPU-Chasis' or $reporte->getAsesorio() == 'cpuchasis') {
                $chasis = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $reporte->getNumInventario()]);

                $equipo = $chasis;
            } elseif ($reporte->getAsesorio() == 'impresora' or $reporte->getAsesorio() == 'estabilizador' or $reporte->getAsesorio() == 'monitor' or $reporte->getAsesorio() == 'backup' or $reporte->getAsesorio() == 'laptop') {
                $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['numInventario' => $reporte->getNumInventario()]);
            } else {
                $equipo = $entityManager->getRepository('AppBundle:componente')->findOneBy(['cpu' => $chasis]);
                dump('aqui');
            }
        }
        if ($reporte->getInventario() == null) {
            $codigoArea = '';
            $area = '';
        } else {
            $codigoArea = $reporte->getInventario()->getCentroCosto()->getId();
            $area = $reporte->getInventario()->getCentroCosto()->getArea()->getIdArea();
        }
        // dump($reporte->getNumInventario());
        //  dump($equipo);die();
        return $this->render('incidencia/reporte2.html.twig', ['reporte' => $reporte, 'equipo' => $equipo, 'codigoArea' => $codigoArea, 'area' => $area]);
    }

    /**
     * @Route("incidencia/{id}/reporte3", name="incidencia_reporte3")
     */
    public function reporte3Action(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reporte = $entityManager->getRepository('AppBundle:incidencia')->find($id);
        $equipo_min = strtolower($reporte->getAsesorio());
        $inventario = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $reporte->getInventario()]);
        if ($reporte->getTipoMov() == 'Traslado Interno') {
            $movimiento = $entityManager->getRepository('AppBundle:movimientoI')->findBy(['incidencia' => $reporte]);
        } else {
            //$movimiento = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $reporte]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');


            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :i')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('i', $reporte)
                ->orderBy('tabla.id', 'DESC')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            $movimiento = $query->getResult();
            //  dump($movimiento);
        }

        //  dump($movimiento);die();
        $equipo = '';

        if (!$inventario) {
            $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['id' => $reporte->getIdE()]);
        } else {
            $chasis = $inventario[0]->getCentroCosto();
            if ($reporte->getAsesorio() == 'CPU-Chasis' or $reporte->getAsesorio() == 'cpuchasis') {
                $equipo = $chasis;

            } elseif ($reporte->getAsesorio() == 'impresora' or $reporte->getAsesorio() == 'estabilizador' or $reporte->getAsesorio() == 'monitor' or $reporte->getAsesorio() == 'backup' or $reporte->getAsesorio() == 'mouse' or $reporte->getAsesorio() == 'teclado') {
                // $equipo = $entityManager->getRepository('AppBundle:equipo')->findOneBy(['estacion' => $inventario[0]->getId()]);
                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:equipo');
                $query = $repository->createQueryBuilder('tabla')
//          ->andWhere('tabla.estacion =:e')
//          ->setParameter('e', $inventario[0]->getId())
//            ->andWhere('tabla.user=:usuario_actual')
//            ->setParameter('usuario_actual', $this->getUser()->getUsername())
                    ->andWhere('tabla.id=:idEquipo')
                    ->setParameter('idEquipo', $reporte->getIdE())
                    // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                    ->getQuery();
                $equipo = $query->execute();
            } else {
                $equipo = $entityManager->getRepository('AppBundle:componente')->findOneBy(['cpu' => $chasis]);
                //   dump($equipo);
                //  die();
            }
        }
//    dump($inventario[0]->getId());
//    dump($equipo);
//    die();
        $datos_usuario = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findBy(['username' => $reporte->getUser()]);
        // dump($reporte->getUser()->getUsername());
        // die();

        //dump($movimiento[0]);die();

//    if($movimiento[0]->getTipoMovimiento()=='Envio a taller'){
        if ($reporte->getTipoMov() == 'Envio a taller' || $reporte->getTipoMov() == "Enviar a taller") {
            $codigoArea = '';
            $area = '';
        } else {

//      $codigoArea= $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getidCosto();
//
//      $area= $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getArea()->getIdArea();

            if (is_iterable($movimiento)) {
                //  if($re)
                $areaD = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()]);
                // dump($reporte);die();
                $area = $areaD[0]->getArea()->getIdArea();
                $codigoArea = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getidCosto();
            } else {
                $areaD = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()]);
                $codigoArea = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getidCosto();
            }
            // $area= $entityManager->getRepository('AppBundle:departamento')->findOneBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getArea()->getIdArea();


        }

        // dump($movimiento);die();
        return $this->render('incidencia/reporte3.html.twig', ['reporte' => $reporte, 'equipo' => $equipo, 'user_reporte' => $datos_usuario, 'mov' => $movimiento[0], 'codigoArea' => $codigoArea, 'area' => $area]);
    }

    /**
     * @Route("incidencia_movimiento/{id}/reporte3", name="incidencia_movimiento_reporte3")
     */
    public function movimiento_reporte3Action(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reporte = $entityManager->getRepository('AppBundle:incidencia')->find($id);
        $equipo_min = strtolower($reporte->getAsesorio());
        //$movimiento = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $reporte]);
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:movimientoI_AF');

        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.incidencia = :i')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('i', $reporte)
            ->orderBy('tabla.id', 'DESC')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
        $movimiento = $query->getResult();


        //  dump($movimiento);die();

        $equipo = $entityManager->getRepository('AppBundle:equipoAssets')->findOneBy(['id' => $reporte->getIdE()]);
        $datos_usuario = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findBy(['username' => $reporte->getUser()]);
        $area = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()]);
        $codigoArea = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimiento[0]->getAreaDestino()])[0]->getidCosto();

        //dump($movimiento);
        //die();
        return $this->render('incidencia/reporte3.html.twig', ['reporte' => $reporte, 'equipo' => $equipo, 'user_reporte' => $datos_usuario, 'mov' => $movimiento[0], 'codigoArea' => $codigoArea, 'area' => $area[0]]);
    }

    /**
     * @Route("incidencia/{id}/{idEquipo}/recibir", name="incidencia_recibir")
     */
    public function recibirAction(Request $request, $id, $idEquipo)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id);
        $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $incidencia->getInventario()->getId()]);
        $equipo_min = strtolower($incidencia->getAsesorio());


        $asesorio = $entityManager->getRepository('AppBundle:equipo')->find($idEquipo);
        // dump($asesorio);die();


        return $this->render('incidencia/recibir.html.twig', ['inventario' => $inventario, 'nombre' => $incidencia->getAsesorio(), 'equipo' => $asesorio, 'incidencia' => $incidencia]);
    }

    /**
     * @Route("incidencia/{id}/{idEquipo}/recibir_sin_inventario", name="incidencia_recibir_sin_inventario")
     */
    public
    function incidenciarecibi2rAction(Request $request, $id, $idEquipo)
    {
        // dump($request);die();
        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id);
        // $inventario = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id' => $incidencia->getInventario()->getId()]);
        $equipo_min = strtolower($incidencia->getAsesorio());


        $asesorio = $entityManager->getRepository('AppBundle:equipo')->find($idEquipo);
        // dump($asesorio);die();


        return $this->render('incidencia/recibir.html.twig', ['inventario' => null, 'nombre' => $incidencia->getAsesorio(), 'equipo' => $asesorio, 'incidencia' => $incidencia]);
    }

    /**
     * @Route("incidencia/{id_inve}/{id_equi}/{id_inci}/{equipo}/reparaciones", name="incidencia_reparaciones")
     */
    public
    function reparado1Action($id_inve, $id_equi, $id_inci, $equipo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);

        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);


        $this->addFlash('success', 'Respuesta Procesada Correctamente');
        // return $this->redirectToRoute('incidencia_ver', ['id' => $id_inci]);
        return $this->redirectToRoute('incidencia_movimientoR', ['id' => $id_equi, 'id_inci' => $id_inci]);
    }

    /**
     * @Route("incidencia/{id_equi}/{id_inci}/{equipo}/reparacionesSI", name="incidencia_reparaciones_SI")
     */
    public function reparado2Action($id_equi, $id_inci, $equipo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);

        $equipo_incidencia = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);


        $this->addFlash('success', 'Respuesta Procesada Correctamente');
        // return $this->redirectToRoute('incidencia_ver', ['id' => $id_inci]);
        return $this->redirectToRoute('movimientoSI', ['id' => $id_equi, 'equipo' => $equipo_incidencia->getTipoEquipo(), 'id_inci' => $incidencia->getId()]);
    }

    /**
     * @Route("incidencia/{id_inve}/{id_equi}/{id_inci}/{name}/reposicion", name="incidencia_reposicion")
     */
    public function reposicionAction(Request $request, $id_inve, $id_equi, $id_inci, $name)
    {
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager2 = $this->getDoctrine()->getManager();
        $incidencia = $entityManager2->getRepository('AppBundle:incidencia')->find($id_inci);

        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);

        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);
        $incidencia->setIdE($eqipo->getId());

        $equipoForm = $this->createForm('AppBundle\Form\equipoFomType', $eqipo);
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Reposicion');
        $movimiento->setIncidencia($incidencia);
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setPeriferico($equipo_min);
        $fecha_actual = date("d/m/Y g:ia");
        $movimiento->setFechaEntrega($fecha_actual);
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\incidenciaReposicionFormType', $movimiento);
        $equipoForm->handleRequest($request);
        $tipoForm->handleRequest($request);


        /**
         *Star "Post only" section
         */

        if ($equipoForm->isSubmitted() && $equipoForm->isValid()) {

            // dump($tipoForm);die();
            $eqipo = $equipoForm->getData();
            //dump($this->getUser()->getUsername());
            // die();
            $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Nuevo por reposicion');
            $incidencia->setTipoMov('Reposicion');
            $incidencia->setAsesorio($equipo_min);
            $incidencia->setTecAsignado($this->getUser()->getUsername());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setEstado('Reposicion');
            $incidencia->setIdE($eqipo->getId());
            $incidencia->setNumInventario($eqipo->getNumInventario());
            $eqipo->setEstado('Activo');
            $entityManager2->persist($incidencia);
            $entityManager->persist($eqipo);
            $entityManager->persist($movimiento);
            $entityManager->flush();
            // dump($id_inci);die();
            if (is_iterable($eqipo)) {

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo_min)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $eqipo[0]->getId())
                    ->getQuery();
                //dump($query);die();
            } else {
                // dump("entre");die();
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo_min)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $eqipo->getId())
                    ->getQuery();
            }
            $query->execute();
            $this->addFlash('success', 'Equipo Actualizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $id_inci]);
        }
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('perifericos/edit.html.twig', ['areas' => $areas, 'equipoForm' => $equipoForm->createView(), 'nombre' => $name, 'equipo' => $eqipo, 'movimientoForm' => $tipoForm->createView(), 'inventario' => $inventario, 'lista_componentes' => $eqipo->getComponente(), 'usuarios' => $usuarios]);
    }

    /**
     * @Route("incidencia/{id_equi}/{id_inci}/{name}/reposicionSI", name="incidencia_reposicionSI")
     */
    public
    function reposicionSIAction(Request $request, $id_equi, $id_inci, $name)
    {
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager2 = $this->getDoctrine()->getManager();
        $incidencia = $entityManager2->getRepository('AppBundle:incidencia')->find($id_inci);

        // $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);

        $eqipo = $entityManager->getRepository('AppBundle:equipo')->find($id_equi);
        $incidencia->setIdE($eqipo->getId());

        $equipoForm = $this->createForm('AppBundle\Form\equipoFomType', $eqipo);
        $movimiento = new movimiento();
        $movimiento->setTipoMovimiento('Reposicion');
        $movimiento->setIncidencia($incidencia);
        $movimiento->setEntidadEntrega('CICC');
        $movimiento->setDireEntrega('Calle L Esq. 13 Vedado');
        $movimiento->setEntidadDestino('CICC');
        $movimiento->setPeriferico($equipo_min);
        $fecha_actual = date("d/m/Y g:ia");
        $movimiento->setFechaEntrega($fecha_actual);
        $movimiento->setDireccionDestino('Calle L Esq. 13 Vedado');
        $tipoForm = $this->createForm('AppBundle\Form\incidenciaReposicionFormType', $movimiento);
        $equipoForm->handleRequest($request);
        $tipoForm->handleRequest($request);


        /**
         *Star "Post only" section
         */

        if ($equipoForm->isSubmitted() && $equipoForm->isValid()) {

            // dump($tipoForm);die();
            $eqipo = $equipoForm->getData();
            //dump($this->getUser()->getUsername());
            // die();
            $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Nuevo por reposicion');
            $incidencia->setTipoMov('Reposicion');
            $incidencia->setAsesorio($equipo_min);
            $incidencia->setTecAsignado($this->getUser()->getUsername());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setEstado('Reposicion');
            $incidencia->setIdE($eqipo->getId());
            $incidencia->setNumInventario($eqipo->getNumInventario());

            $eqipo->setEstado('Activo');
            $entityManager2->persist($incidencia);
            $entityManager->persist($eqipo);
            $entityManager->persist($movimiento);
            $entityManager->flush();
            // dump($id_inci);die();
            if (is_iterable($eqipo)) {

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo_min)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $eqipo[0]->getId())
                    ->getQuery();
                //dump($query);die();
            } else {
                // dump("entre");die();
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $query = $qb->delete('AppBundle:taller', 't')
                    ->where('t.tipo_periferico = :tipo')
                    ->setParameter('tipo', $equipo_min)
                    ->andWhere('t.id_periferico = :id')
                    ->setParameter('id', $eqipo->getId())
                    ->getQuery();
            }
            $query->execute();

            $this->addFlash('success', 'Equipo Actualizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $id_inci]);
        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('perifericos/edit.html.twig', ['equipoForm' => $equipoForm->createView(), 'nombre' => $name, 'equipo' => $eqipo, 'movimientoForm' => $tipoForm->createView(), 'lista_componentes' => $eqipo->getComponente(), 'usuarios' => $usuarios]);
    }

    /**
     * @Route("incidencia/{id_inve}/{id_equi}/{id_inci}/{name}/reposicion2", name="incidencia_reposicion2")
     */
    public
    function reposicion2Action(Request $request, $id_inve, $id_equi, $id_inci, $name)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);
        $eqipo = $entityManager->getRepository('AppBundle:cpuchasis')->find($id_equi);
        $chasisForm = $this->createForm('AppBundle\Form\chasisFormType', $eqipo);

        $ram = $entityManager->getRepository('AppBundle:ram')->findOneBy(['inventario' => $id_inve]);
        $ramForm = $this->createForm('AppBundle\Form\ramType', $ram);

        $mother = $entityManager->getRepository('AppBundle:motherboard')->findOneBy(['inventario' => $id_inve]);
        $motherForm = $this->createForm('AppBundle\Form\motherboardType', $mother);

        $fuente = $entityManager->getRepository('AppBundle:fuente')->findOneBy(['inventario' => $id_inve]);
        $fuenteForm = $this->createForm('AppBundle\Form\fuenteType', $fuente);


        $micro = $entityManager->getRepository('AppBundle:microprocesador')->findOneBy(['inventario' => $id_inve]);
        $microForm = $this->createForm('AppBundle\Form\microprocesadorType', $micro);

        $lector = $entityManager->getRepository('AppBundle:lector')->findOneBy(['inventario' => $id_inve]);
        $lectorForm = $this->createForm('AppBundle\Form\lectorType', $lector);


        /**
         *Star "Post only" section
         */
        $chasisForm->handleRequest($request);
        if ($chasisForm->isSubmitted() && $chasisForm->isValid()) {


            $eqipo = $chasisForm->getData();
            $eqipo->setEstado('Activo');

            //$applicationRepository = $entityManager->getRepository('AppBundle:incidencia');

            $incidencia->setUser($this->getUser());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setDpto($inventario->getDpto());
            $incidencia->setEstado('Solucionado');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Nuevo por reposicion');
            $incidencia->setTipoMov('Reposicion');
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setFechaA(new \DateTime("now"));

            //dump($incidencia);
            // die();

            $entityManager->persist($eqipo);
            $entityManager->persist($incidencia);
            $entityManager->flush();


            $this->addFlash('success', 'Equipo Actualizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId(), 'equipo' => $eqipo]);
        }
        /**
         * End "Post only" section
         */

        return $this->render('perifericos/edit2.html.twig', ['equipoForm' => $chasisForm->createView(), 'ramForm' => $ramForm, 'microForm' => $microForm, 'lectorForm' => $lectorForm
            , 'fuenteForm' => $fuenteForm, 'nombre' => $name, 'equipo' => $eqipo, 'inventario' => $inventario]);


    }

    /**
     * @Route("incidencia/{id_inve}/{id_equi}/{id_inci}/{name}/traslado", name="incidencia_traslado")
     */
    public function trasladoAction(Request $request, $id_inve, $id_equi, $id_inci, $name)
    {
        $tipoForm = $this->createForm('AppBundle\Form\movimiento3FormType');
        $equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();
        $eqipo = $entityManager->getRepository('AppBundle:' . $equipo_min)->find($id_equi);
        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);
        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $tipo = $tipoForm->getData();


            $tipo->setInventario($inventario);
            $tipo->setIncidencia($incidencia);
            $tipo->setPeriferico($name);
            $fecha_actual = new \DateTime("now");


            $tipo->setFecha($fecha_actual);
            $tipo->setRespEntrega($inventario->getResponsable());
            $tipo->setAreaEntrega($inventario->getDpto());

            // $ram = $entityManager->getRepository('AppBundle:ram')->findOneBy(['inventario' => $id_inve]);
///poner el equipo como roto
            $eqipo->setEstado('Roto');


            $eqipo->setEstado('Activo');
            // dump($name);
            //die();
            //$applicationRepository = $entityManager->getRepository('AppBundle:incidencia');

            $incidencia->setEstado('Solucionado');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setDpto($inventario->getDpto());
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Traslado Interno');
            $incidencia->setInventario($inventario);
            $incidencia->setTipoMov('Traslado Interno');
            $incidencia->setAsesorio($name);
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));


            $entityManager->persist($tipo);
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Traslado Interno Realizado Correctamente');
            return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia->getId(), 'equipo' => $eqipo]);

        }

        return $this->render('incidencia/traslado.html.twig', ['movimientoForm' => $tipoForm->createView(), 'nombre' => $name, 'asesorio' => $eqipo, 'inventario' => $inventario]);

    }

    /**
     * @Route("/reportes/incidencias/filtrar_incidencias2",name="filtra_incidenciasNI")
     */
    public function filtrarIncidenciasNIAction(Request $request, $maxItemPerPage = 10)
    {
        $lista = $request->query->get('lista');

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
            $entityManager = $this->getDoctrine()->getManager();
            // $equipos = $entityManager->getRepository('AppBundle:incidencia')->findOneBy(['idE' => $num]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.num_inventario = :numI')
                ->setParameter('numI', $num)
                // ->orderBy('tabla.fecha', 'desc')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();


            $products = $query->getResult();
            // dump($query->getArrayResult());die();
            $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $maxItemPerPage
            );

//       dump($products);
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
            // dump($estacion);
            //  die();
            return $this->render('incidencia/list.html.twig', ['pagination' => $pagination, 'incidencias' => $products, 'areas' => $area, 'lista' => $lista]);
        } else

            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        return $this->redirectToRoute('lista_inci');
    }

    /**
     * @Route("incidencia/{id}/ver", name="incidencia_ver")
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $personRepository = $entityManager->getRepository('AppBundle:incidencia');
        $incidencia = $personRepository->find($id);

        $movimiento = '';
        //    dump($incidencia->getTipoMov());die();
        if ($incidencia->getTipoMov() == 'Traslado Interno' || $incidencia->getTipoMov() == 'Traslado') {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimientoI');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :inci')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('inci', $incidencia)
                ->getQuery()->getArrayResult();
            //$movimiento = $entityManager->getRepository('AppBundle:movimientoI')->findBy(['incidencia' => $incidencia]);
            //dump($movimiento);die();
        } elseif ($incidencia->getTipoMov() == 'Reposicion') {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :inci')
                ->setParameter('inci', $incidencia)
                ->andWhere('tabla.tipoMovimiento = :mov')
                ->setParameter('mov', 'Reposicion')
                ->getQuery()->getArrayResult();
//            dump('aqui');
//            dump($movimiento);
//            die();
        } elseif ($incidencia->getTipoMov() == 'Solucionado') {
            $movimiento = '';
        } else {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:movimiento');
//      $repository2 = $this->getDoctrine()
//        ->getRepository('AppBundle:movimiento')->findBy(['tipoMovimiento'=>$incidencia->getTipoMov()]);
            $movimiento = $repository->createQueryBuilder('tabla')
                ->where('tabla.incidencia = :inci')
                ->setParameter('inci', $incidencia)
//                ->andWhere('tabla.tipoMovimiento = :mov')
//                ->setParameter('mov', $incidencia->getTipoMov())
                ->orderBy('tabla.id', 'DESC')
                ->getQuery()->getArrayResult();
//            dump($incidencia);
//      dump($movimiento);
//      die();
        }

        if (is_null($incidencia)) {
            $inventario = null;
        } else {

            $inventario = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $incidencia->getInventario()]);
        }

        if ($incidencia == null) {
            throw $this->createNotFoundException("Incidencia no encontrada");
        }
//       dump($incidencia);
//         dump($movimiento);die();
        if ($movimiento == [] or $movimiento == '') {
            return $this->render('incidencia/show.html.twig', ['incidencia' => $incidencia, 'inventario' => $inventario, 'mov' => '']);
        } else {
            return $this->render('incidencia/show.html.twig', ['incidencia' => $incidencia, 'inventario' => $inventario, 'mov' => $movimiento[0]]);
        }

    }

    /**
     * @Route("reportes/activos_fijos/{id}/ver", name="incidencia_movimientoAFT_ver")
     * @param $id
     *
     * @return Response
     */
    public
    function showIncidenciaMAFTAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $personRepository = $entityManager->getRepository('AppBundle:incidencia');
        $incidencia = $personRepository->find($id);
        $movimiento = '';
//
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:movimientoI_AF');
        $movimiento = $repository->createQueryBuilder('tabla')
            ->where('tabla.incidencia = :inci')
            ->setParameter('inci', $incidencia)
            ->getQuery()->getArrayResult();
//      dump($incidencia->getTipoMov());
//       die();
        // }
//        if (is_null($incidencia)) {
//            $inventario = null;
//        } else {
//            $inventario = $entityManager->getRepository('AppBundle:inventario')->findBy(['id' => $incidencia->getInventario()]);
//        }

        if ($incidencia == '') {
            throw $this->createNotFoundException("Incidencia no encontrada");
        }
        //dump($incidencia->getTipoMov());die();
        // dump($movimiento);die();
        return $this->render('incidencia/show.html.twig', ['incidencia' => $incidencia, 'inventario' => '', 'mov' => $movimiento[0]]);


    }


    /**
     * @Route("/reportes/incidencias/remove_incidencia/{id}", name="remove_incidencia")
     */
    public function removeIncidenciaAction($id)
    {
        $incidencia = $this->getDoctrine()->getRepository('AppBundle:incidencia')->find($id);
        $em = $this->getDoctrine()->getManager();

        if (!$incidencia) {
            throw $this->createNotFoundException(
                'No se ha encontrado ningun inventario con este ' . $id
            );
        }

        if ($incidencia->getTipo() == "Instalacion de nuevo equipamiento informatico") {
            // dump($incidencia);die();
            $em->remove($incidencia);
        } elseif ($incidencia->getTipo() == "Movimiento Activo Fijo") {
            $movimiento = $this->getDoctrine()->getRepository('AppBundle:movimientoI_AF')->findBy(['incidencia' => $incidencia->getId()]);
            //dump($movimiento);die();
            if ($movimiento != []) {
                if (sizeof($movimiento) > 1) {
                    foreach ($movimiento as $l) {
                        $em->remove($l);
                    }
                } else {
                    $em->remove($movimiento[0]);
                }
                $em->remove($incidencia);
            }
//                $em->remove($incidencia);
//                return $this->redirectToRoute('lista_incidencias_movimiento');
        } else {
            $movimiento = $this->getDoctrine()->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $incidencia->getId()]);
            $movimiento1 = $this->getDoctrine()->getRepository('AppBundle:movimientoI')->findBy(['incidencia' => $incidencia->getId()]);

            if ($movimiento != []) {
                if (sizeof($movimiento) > 1) {
                    foreach ($movimiento as $l) {
                        $em->remove($l);
                    }
                } else {
                    $em->remove($movimiento[0]);
                }
            } else {
                if (sizeof($movimiento1) > 1) {
                    foreach ($movimiento1 as $m) {
                        $em->remove($m);
                    }
                } else {
//        dump($movimiento1);
//        die();
                    $em->remove($movimiento1[0]);
                }

            }
            $em->remove($incidencia);
        }

        //$em->remove($incidencia);
        $em->flush();

        // return new Response("El plan con id {$id} ha sido eliminado");
        $this->addFlash('success', 'La incidencia con id :' . $id . ' ha sido eliminada');
        return $this->redirectToRoute('lista_inci');

    }


    /**
     * @Route("/reportes/incidencias/remove_incidenciaAF/{id}", name="remove_incidenciaAF")
     */
    public function removeIncidenciaAFAction($id)
    {
        $incidencia = $this->getDoctrine()->getRepository('AppBundle:incidencia')->find($id);
        $em = $this->getDoctrine()->getManager();

        if (!$incidencia) {
            throw $this->createNotFoundException(
                'No se ha encontrado ningun inventario con este ' . $id
            );
        }

        if ($incidencia->getTipo() == "Movimiento Activo Fijo") {
            $movimiento = $this->getDoctrine()->getRepository('AppBundle:movimientoI_AF')->findBy(['incidencia' => $incidencia->getId()]);
            //dump($movimiento);die();
            if ($movimiento != []) {
                if (sizeof($movimiento) > 1) {
                    foreach ($movimiento as $l) {
                        $em->remove($l);
                    }
                } else {
                    $em->remove($movimiento[0]);
                }
                $em->remove($incidencia);
            }
        }
        $em->flush();
        $this->addFlash('success', 'La incidencia con id :' . $id . ' ha sido eliminada');
        return $this->redirectToRoute('lista_incidencias_movimiento');
    }

}