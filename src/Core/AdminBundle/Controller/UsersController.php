<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\AdminBundle\Entity\radcheck;

class UsersController extends Controller
{
########## USERS ##########
    public function newAction($session)
    {
        $request = Request::createFromGlobals();
        $user = $request->request->get('user',NULL);
        $pass = $request->request->get('pass',NULL);

        if($user && $pass){
            $usuario = new Radcheck();

            $usuario->setUsername($user);
            $usuario->setAttribute('MD5-Password');
            $usuario->setOp(':=');
            $usuario->setValue($pass);

            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            $mensaje = 'Usuario ingresado con éxito !';
        }else{
            $mensaje = '';
        }

        return $this->render('CoreAdminBundle:users:new.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje ));
    }

    public function editAction($session,$id)
    {
        $request = Request::createFromGlobals();
        $user = $request->request->get('user',NULL);
        $pass = $request->request->get('pass',NULL);

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('CoreAdminBundle:radcheck')->find($id);

        if($user && $pass){
            $usuario->setUsername($user);
            $usuario->setValue($pass);

            $em->persist($usuario);
            $em->flush();

            $mensaje = 'Usuario ingresado con éxito !';
            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
            return $this->redirect( $this->generateUrl('admin_usuarios_listar', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios )) );
        }else{
            $mensaje = '';
        }

        return $this->render('CoreAdminBundle:users:edit.html.twig', array( 'session' => $session, 'session_id' => $session, 'usuario' => $usuario ));
    }

    public function delAction($session,$id)
    {
        $request = Request::createFromGlobals();
        $confirm = $request->request->get('confirm',NULL);

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('CoreAdminBundle:radcheck')->find($id);

        if($confirm){
            $em->remove($usuario);
            $em->flush();

            $mensaje = 'Usuario eliminado con éxito !';
            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
            return $this->redirect( $this->generateUrl('admin_usuarios_listar', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios )) );
        }else{
            $mensaje = '¿Seguro que desea eliminar al usuario?';
        }

        return $this->render('CoreAdminBundle:users:del.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuario' => $usuario ));
    }

    public function listAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
        foreach ($usuarios as $usuario => $value) {
            $dql = "select a.macaddress from CoreAdminBundle:ssidmacauth a where a.username='".$value->getUsername()."'";
            $query = $em->createQuery($dql);
            $value->setValue($query->getResult());
        }

        $mensaje = '';

        return $this->render('CoreAdminBundle:users:list.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios ));
    }

    public function activeAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        //$usuarios = $em->getRepository('CoreAdminBundle:radacct')->findAll();

        $query = $em->createQuery(
            'SELECT r.username,r.id,r.framedipaddress,r.callingstationid,r.acctinputoctets,r.acctoutputoctets
            FROM CoreAdminBundle:radacct r
            GROUP BY r.username
            ORDER BY r.username ASC'
        );

        $usuarios = $query->getResult();

        $i=0;
        foreach ($usuarios as $usuario) {
            $usuarios[$i]['acctinputoctets'] = round($usuario['acctinputoctets'] * 0.000000953674316,1);
            $usuarios[$i]['acctoutputoctets'] = round($usuario['acctoutputoctets'] * 0.000000953674316,1);
            $i++;
        }

        return $this->render('CoreAdminBundle:users:active.html.twig', array( 'session' => $session, 'session_id' => $session, 'usuarios' => $usuarios ));
    }
##########  ##########
}
