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

        $usuario_radchek = $em->getRepository('CoreAdminBundle:radcheck')->find($id);
        $usuario_ssidmacauth = $em->getRepository('CoreAdminBundle:ssidmacauth')->findBy(array('username' => $usuario_radchek->getUsername()));
        $usuario_users = $em->getRepository('CoreAdminBundle:Users')->findOneBy(array('username' => $usuario_radchek->getUsername()));


        if($confirm){
            $em->remove($usuario_radchek);
            $em->flush();

            foreach ($usuario_ssidmacauth as $user) {
                $em->remove($user);
                $em->flush();
            }

            $usuario_users->setUsername('---');
            $usuario_users->setGenpass(0);
            $usuario_users->setNewpass(0);
            $usuario_users->setEmail('');

            $em->persist($usuario_users);
            $em->flush();


            $mensaje = 'Usuario eliminado con éxito !';
            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
            return $this->redirect( $this->generateUrl('admin_usuarios_listar_reg', array( 'session' => $session, 'offset' => '1', 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios )) );
        }else{
            $mensaje = '¿Seguro que desea eliminar al usuario?';
        }

        return $this->render('CoreAdminBundle:users:del.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuario' => $usuario_radchek ));
    }
    /***************************************************************************/
    public function listregAction($session,$offset)
    {
        $em = $this->getDoctrine()->getManager();

        $request = Request::createFromGlobals();
        $q = $request->request->get('q',NULL);
        $where_search = '';

        $campus = unserialize( $this->get('cache')->fetch('session_admin') );

        switch ( $campus ) {
            case $campus:
                $where_campus = " u.campus = '".$campus."' AND ";
            break;

            default:
                $where_campus = "";
            break;
        }

        if ($q != NULL) {
            $where_search = " ( u.username LIKE '%".$q."%' OR u.firstname LIKE '%".$q."%' OR u.secondname LIKE '%".$q."%' OR u.matricula LIKE '%".$q."%' ) AND ";
        }
        $num_usuarios = $em->createQuery(
            "SELECT COUNT(r.id),r.username,r.value,u.firstname,u.secondname,u.campus,u.tipo FROM CoreAdminBundle:radcheck r,CoreAdminBundle:Users u WHERE ".$where_search." ".$where_campus." r.username != '' AND r.username = u.username ORDER BY u.firstname,u.secondname"
        );

        $num_usuarios = $num_usuarios->getResult();
        $users_total = $num_usuarios[0][1];
        $items_per_page = 100;
        $total_pages = round($users_total/$items_per_page,0);

        $usuarios = $em->createQuery(
            "SELECT r.id,r.username,r.value,u.firstname,u.secondname,u.campus,u.tipo FROM CoreAdminBundle:radcheck r,CoreAdminBundle:Users u WHERE ".$where_search." ".$where_campus." r.username != '' AND r.username = u.username AND u.campus = 'TOL' ORDER BY u.firstname,u.secondname"
        );
        $usuarios->setMaxResults($items_per_page);
        $usuarios->setFirstResult(($offset-1)*$items_per_page);
        $usuarios = $usuarios->getResult();
        
        foreach ($usuarios as $usuario => $value) {
            $dql = "SELECT a.macaddress FROM CoreAdminBundle:ssidmacauth a WHERE a.username='".$value['username']."'";
            $query = $em->createQuery($dql);
            $usuarios[$usuario]['value'] = $query->getResult();
        }

        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios, 'offset' => $offset, 'total_pages' => $total_pages ));
    }
    /***************************************************************************/
    public function listunregAction($session,$offset)
    {
        $em = $this->getDoctrine()->getManager();

        $request = Request::createFromGlobals();
        $q = $request->request->get('q',NULL);
        $where_search = '';

        $campus = unserialize( $this->get('cache')->fetch('session_admin') );

        switch ( $campus ) {
            case $campus:
                $where_campus = " u.campus = '".$campus."' AND ";
            break;

            default:
                $where_campus = "";
            break;
        }

        if ($q != NULL) {
            $where_search = " ( u.firstname LIKE '%".$q."%' OR u.secondname LIKE '%".$q."%' OR u.matricula LIKE '%".$q."%' ) AND ";
        }
        $num_usuarios = $em->createQuery(
            "SELECT COUNT(u.id),u.username,u.firstname,u.secondname,u.campus,u.tipo,u.matricula,u.fecha FROM CoreAdminBundle:Users u WHERE ".$where_search." ".$where_campus." u.username != '' AND u.username = '---' ORDER BY u.firstname,u.secondname"
        );

        $num_usuarios = $num_usuarios->getResult();
        $users_total = $num_usuarios[0][1];
        $items_per_page = 100;
        $total_pages = round($users_total/$items_per_page,0);

        $usuarios = $em->createQuery(
            "SELECT u.id,u.username,u.firstname,u.secondname,u.campus,u.tipo,u.matricula,u.fecha FROM CoreAdminBundle:Users u WHERE ".$where_search." ".$where_campus." u.username != '' AND u.username = '---' ORDER BY u.firstname,u.secondname"
        );
        $usuarios->setMaxResults($items_per_page);
        $usuarios->setFirstResult(($offset-1)*$items_per_page);
        $usuarios = $usuarios->getResult();

        //var_dump($usuarios);
        
        /*foreach ($usuarios as $usuario => $value) {
            $dql = "SELECT a.macaddress FROM CoreAdminBundle:ssidmacauth a WHERE a.username='".$value['username']."'";
            $query = $em->createQuery($dql);
            $usuarios[$usuario]['value'] = $query->getResult();
        }*/

        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listunreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios, 'offset' => $offset, 'total_pages' => $total_pages ));
    }
    /***************************************************************************
    public function listregAction($session,$offset)
    {
        $em = $this->getDoctrine()->getManager();

        $request = Request::createFromGlobals();
        $q = $request->request->get('q',NULL);

        if ($q != NULL) {

            switch ( unserialize( $this->get('cache')->fetch('session_admin') ) ) {
                case 'all':
                    $usuarios = $em->getRepository("CoreAdminBundle:radcheck")->createQueryBuilder('r')
                       ->where('r.username LIKE :user')
                       ->setParameter('user', '%'.$q.'%')
                       ->getQuery()
                       ->getResult();

                    $users_total = count($usuarios);
                    $items_per_page = 50;
                    $total_pages = 0;
                    
                    foreach ($usuarios as $usuario => $value) {
                        $dql = "select a.macaddress from CoreAdminBundle:ssidmacauth a where a.username='".$value->getUsername()."'";
                        $query = $em->createQuery($dql);
                        $value->setValue($query->getResult());
                    }
                    break;
                
                case 'toluca':
                    $usuarios = $em->getRepository("CoreAdminBundle:radcheck")->createQueryBuilder('r')
                       ->where('r.username LIKE :user')
                       ->setParameter('user', '%'.$q.'%')
                       ->getQuery()
                       ->getResult();

                    $users_total = count($usuarios);
                    $items_per_page = 50;
                    $total_pages = 0;
                    
                    foreach ($usuarios as $usuario => $value) {
                        $dql = "select a.macaddress from CoreAdminBundle:ssidmacauth a where a.username='".$value->getUsername()."'";
                        $query = $em->createQuery($dql);
                        $value->setValue($query->getResult());
                    }
                    break;
            }

        }else{
            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findAll();
            $users_total = count($usuarios);
            $items_per_page = 50;
            $total_pages = $users_total/$items_per_page;

            $usuarios = $em->getRepository('CoreAdminBundle:radcheck')->findBy(array(), array(), $items_per_page, ($offset-1)*$items_per_page);
            foreach ($usuarios as $usuario => $value) {
                $dql = "select a.macaddress from CoreAdminBundle:ssidmacauth a where a.username='".$value->getUsername()."'";
                $query = $em->createQuery($dql);
                $value->setValue($query->getResult());
            }
        }

        

        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios, 'offset' => $offset, 'total_pages' => $total_pages ));
    }
    /***************************************************************************
    public function listunregAction($session,$offset)
    {
        $em = $this->getDoctrine()->getManager();

        $request = Request::createFromGlobals();
        $q = $request->request->get('q',NULL);

        if ($q != NULL) {
            $usuarios = $em->getRepository("CoreAdminBundle:Users")->createQueryBuilder('u')
               ->where('u.username LIKE :user')
               //->where('u.firstname LIKE :fname')
               //->orwhere('u.secondname LIKE :q')
               ->setParameter('user', '%'.$q.'%')
               //->setParameter('fname', '%'.$q.'%')
               ->getQuery()
               ->getResult();

            $users_total = count($usuarios);
            $items_per_page = 1500;
            $total_pages = 0;
        }else{
            $items_per_page = 1500;
            $usuarios = $em->getRepository('CoreAdminBundle:Users')->findBy(array('username'  => '---'), array(), $items_per_page, ($offset-1)*$items_per_page);
            $users_total = count($usuarios);
            $items_per_page = 50;
            $total_pages = $users_total/$items_per_page;

            $usuarios = $em->getRepository('CoreAdminBundle:Users')->findBy(array('username'  => '---'), array(), $items_per_page, ($offset-1)*$items_per_page);
        }
        //var_dump($usuarios);
        $mensaje = '';

        return $this->render('CoreAdminBundle:users:listunreg.html.twig', array( 'session' => $session, 'session_id' => $session, 'mensaje' => $mensaje, 'usuarios' => $usuarios, 'offset' => $offset, 'total_pages' => $total_pages ));
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
