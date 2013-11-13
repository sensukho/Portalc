<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\AdminBundle\Entity\radcheck;
use Core\AdminBundle\Entity\Users;
use Doctrine\Common\Collections;

class UsersController extends Controller
{
########## USERS ##########
    public function newAction(Request $request,$session)
    {
        $usuario = new Users();
        $usuario->setFecha( new \DateTime('today') );
        $msg = '';
        $form = $this->createFormBuilder($usuario)
            ->setAction($this->generateUrl('admin_usuarios_crear', array('session' => $session)))
            ->add('firstname', 'text', array('label' => 'Nombre','attr' => array('placeholder' => 'Nombre')))
            ->add('secondname', 'text', array('label' => 'Apellidos','attr' => array('placeholder' => 'Apellidos')))
            ->add('username', 'hidden', array('attr' => array('value' => '---')))
            ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'matricula')))
            ->add('genpass', 'hidden', array('attr' => array('value' => '0')))
            ->add('newpass', 'hidden', array('attr' => array('value' => '0')))
            ->add('newpasssecond', 'hidden', array('attr' => array('value' => '0')))
            ->add('email', 'hidden', array('attr' => array('value' => '0')))
            ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
            ->add('enviar', 'submit')
        ->getForm();
        $formreq = $form;

        if ($request->isMethod('POST')) {
            $formreq->bind($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            $usuario = new Users();
            $msg = 'El usuario se ha agregado con éxito.';
        }

        return $this->render('CoreAdminBundle:users:new.html.twig', array( 'session' => $session, 'session_id' => $session, 'form' => $form->createView(), 'msg' => $msg ));
    }
    /***************************************************************************/
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

            $mensaje = 'Usuario modificado con éxito !';
            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
            return $this->redirect( $this->generateUrl('admin_usuarios_listar', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios )) );
        }else{
            $mensaje = '';
        }

        return $this->render('CoreAdminBundle:users:edit.html.twig', array( 'session' => $session, 'session_id' => $session, 'usuario' => $usuario ));
    }
    /***************************************************************************/
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
    /***************************************************************************/
    public function listregAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
        foreach ($usuarios as $usuario => $value) {
            $dql = "select a.macaddress from CoreAdminBundle:ssidmacauth a where a.username='".$value->getUsername()."'";
            $query = $em->createQuery($dql);
            $value->setValue($query->getResult());
        }

        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios ));
    }
    /***************************************************************************/
    public function listunregAction($session)
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('CoreAdminBundle:Users')->findBy(
            array(
                'username'  => '---'
            )
        );
        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listunreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios ));
    }
    /***************************************************************************/
    public function resetmacsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('CoreAdminBundle:ssidmacauth')->findAll();

        foreach ($usuarios as $usuario => $value) {
            $user = $em->getRepository('CoreAdminBundle:ssidmacauth')->find( $value->getId() );
            $em->remove($user);
            $em->flush();
        }
        return $this->render('CoreAdminBundle:users:resetmacs.html.twig', array( 'fecha' => date('d-M-Y H:m a') ));
    }

##########  ##########
}
