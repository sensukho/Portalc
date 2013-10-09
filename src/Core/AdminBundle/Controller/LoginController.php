<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Core\AdminBundle\Entity\Users;
use Core\AdminBundle\Entity\radcheck;

class LoginController extends Controller
{
########## LOGIN ##########
    public function loginAction()
    {
        $request = Request::createFromGlobals();
        $user = $request->request->get('username',NULL);
        $pass = $request->request->get('password',NULL);
        $chk_rec = $request->request->get('chk_rec',NULL);
        $chk = '';
        $msg = '';

        $response = new Response();

        if ($user != NULL && $pass != NULL) {
            if ($chk_rec == "chk_rec"){
                $user = htmlspecialchars(trim($user));
                $pass = trim($pass);

                $cookieU = new Cookie('usu', $user, time()+60*60*24*30);
                $cookieP = new Cookie('pass', $pass, time()+60*60*24*30);

                $response->headers->setCookie($cookieU);
                $response->headers->setCookie($cookieP);

                $response->send();


            }else{
                $response->headers->clearCookie('usu');
                $response->headers->clearCookie('pass');
                $response->send();
            }
        }

        $user = '';
        $pass = '';

        $request = $this->get('request');
        if($request->cookies->has('usu') && $request->cookies->has('pass') ){
            $user = $request->cookies->get('usu');
            $pass = $request->cookies->get('pass');
            $chk = "checked";
        }
        return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg, ));
    }

    public function welcomeAction()
    {
        return $this->render('CoreAdminBundle:login:bienvenida.html.twig', array());
    }
    
    public function registerAction(Request $request)
    {
        $usuario = new Users();
        $form = $this->createFormBuilder($usuario);
        $msg = '';

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'username'  => $data['form']['username']
                )
            );
            if (!$user) {
                $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                    array(
                        'name'  => $data['form']['name'],
                        'firstname'  => $data['form']['firstname'],
                        'secondname'  => $data['form']['secondname'],
                        'matricula' => $data['form']['matricula'],
                        'fecha' => new \DateTime($data['form']['fecha']["year"]."-".$data['form']['fecha']["month"]."-".$data['form']['fecha']["day"]),
                        'newpass' => '0'
                    )
                );
                if ($user) {
                    $user->setNewpass($data['form']['newpass']);
                    $user->setUsername($data['form']['username']);
                    $em->persist($user);
                    $em->flush();

                    $raduser = new Radcheck();
                    $raduser->setUsername($data['form']['username']);
                    $raduser->setAttribute('MD5-Password');
                    $raduser->setOp(':=');
                    $raduser->setValue($data['form']['newpass']);
                    $em->persist($raduser);
                    $em->flush();

                    $msg = "Tu registro se ha completado con éxito, ya puedes ingresar.";
                    return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg ));
                }else{
                    $usuario->setFecha( new \DateTime('today') );
                    $form = $this->createFormBuilder($usuario)
                        ->setAction($this->generateUrl('portal_register'))
                        ->add('name', 'text', array('label' => 'Nombre','attr' => array('placeholder' => 'Nombre')))
                        ->add('firstname', 'text', array('label' => 'Apellido paterno','attr' => array('placeholder' => 'Apellido paterno')))
                        ->add('secondname', 'text', array('label' => 'Apellido materno','attr' => array('placeholder' => 'Apellido materno')))
                        ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                        ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
                        ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                        ->add('newpass', 'password', array('label' => 'Password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                        ->add('enviar', 'submit')
                    ->getForm();
                    $msg = "Datos Inválidos - favor de contactar la oficina de soporte UVM.";
                    return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
                }
            }else{
                $usuario->setFecha( new \DateTime('today') );
                $form = $this->createFormBuilder($usuario)
                    ->setAction($this->generateUrl('portal_register'))
                    ->add('name', 'text', array('label' => 'Nombre','attr' => array('placeholder' => 'Nombre')))
                    ->add('firstname', 'text', array('label' => 'Apellido paterno','attr' => array('placeholder' => 'Apellido paterno')))
                    ->add('secondname', 'text', array('label' => 'Apellido materno','attr' => array('placeholder' => 'Apellido materno')))
                    ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                    ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                    ->add('newpass', 'password', array('label' => 'Password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
                ->getForm();
                $msg = "El nombre de usuario ya esta registrado, por favor eliga otro para continuar.";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
            }
        }else{
            $usuario->setFecha( new \DateTime('today') );
            $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('portal_register'))
                    ->add('name', 'text', array('label' => 'Nombre','attr' => array('placeholder' => 'Nombre')))
                    ->add('firstname', 'text', array('label' => 'Apellido paterno','attr' => array('placeholder' => 'Apellido paterno')))
                    ->add('secondname', 'text', array('label' => 'Apellido materno','attr' => array('placeholder' => 'Apellido materno')))
                    ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                    ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                    ->add('newpass', 'password', array('label' => 'Password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
        }
    }

    public function changeAction(Request $request)
    {
        $usuario = new Users();
        $form = $this->createFormBuilder($usuario);
        $msg = '';

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            if( $data['form']['newpass'] === $data['form']['newpasssecond'] ){
                $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                    array(
                        'username'  => $data['form']['username'],
                        'matricula' => $data['form']['matricula'],
                        'genpass' => $data['form']['genpass'],
                        'newpass' => '0'
                    )
                );
                if ($user) {
                    $user->setGenpass('0');
                    $user->setNewpass($data['form']['newpass']);
                    $em->persist($user);
                    $em->flush();

                    $raduser = $em->getRepository('CoreAdminBundle:radcheck')->findOneBy(
                        array(
                            'username'  => $data['form']['username']
                        )
                    );

                    if ($raduser) {
                        $raduser->setValue($data['form']['newpass']);
                        $em->persist($raduser);
                        $em->flush();
                    }

                    $msg = "Tu password se ha cambiado con éxito, ya puedes ingresar.";
                    return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg ));
                }else{
                    $form = $this->createFormBuilder($usuario)
                        ->setAction($this->generateUrl('portal_change_pass'))
                        ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Usuario', 'pattern' => '.{5,}')))
                        ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                        ->add('genpass', 'text', array('label' => 'Ingresa el password que te enviamos por mail','attr' => array('placeholder' => '8 caracteres.', 'pattern' => '.{6,}')))
                        ->add('newpass', 'password', array('label' => 'Nuevo password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                        ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                        ->add('enviar', 'submit')
                    ->getForm();
                    $msg = "Datos Inválidos - verifica e intenta de nuevo.";
                    return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
                }
            }else{
                $form = $this->createFormBuilder($usuario)
                    ->setAction($this->generateUrl('portal_change_pass'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Usuario', 'pattern' => '.{5,}')))
                    ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                    ->add('genpass', 'text', array('label' => 'Ingresa el password que te enviamos por mail','attr' => array('placeholder' => '8 caracteres.', 'pattern' => '.{6,}')))
                    ->add('newpass', 'password', array('label' => 'Nuevo password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
                ->getForm();
                $msg = "Los campos de contraseña no coinciden, verifica e intenta de nuevo.";
                return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
            }
        }else{
            $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('portal_change_pass'))
                ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Usuario', 'pattern' => '.{5,}')))
                ->add('matricula', 'text', array('label' => 'Matricula','attr' => array('placeholder' => 'Matricula')))
                ->add('genpass', 'text', array('label' => 'Ingresa el password que te enviamos por mail','attr' => array('placeholder' => '8 caracteres.', 'pattern' => '.{6,}')))
                ->add('newpass', 'password', array('label' => 'Nuevo password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
        }
    }

    public function resetAction(Request $request)
    {
        $form = $this->createFormBuilder();
        $msg = '';
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'email' => $data['form']['email']
                )
            );
            if ($user) {
                $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                $longitudCadena=strlen($cadena);
                $newpass = "";
                $longitudPass=8;
                for($i=1 ; $i<=$longitudPass ; $i++){
                    $pos=rand(0,$longitudCadena-1);
                    $newpass .= substr($cadena,$pos,1);
                }

                $user->setGenpass($newpass);
                $user->setNewpass('0');
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Portal UVM :: Reseteo de contraseña')
                    ->setFrom('soporte@uvm.com')
                    ->setTo($data['form']['email'])
                    ->setBody(
                        $this->renderView(
                            'CoreAdminBundle:login:mailTamplate.html.twig',
                            array('name' => $user->getName(),'firstname' => $user->getFirstname(),'user' => $user->getUsername(),'pass' => $newpass, 'hostname' => $_SERVER['HTTP_HOST'] )
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                $msg = 'Se ha enviado un correo con tu nueva contraseña a: '.$data['form']['email'];
                return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg ));
            }else{
                $msg = 'La cuenta de correo: '.$data['form']['email']." no se encuentra registrada en nuestro sistema.";
                $defaultData = array('message' => 'Type your message here');
                $form = $this->createFormBuilder($defaultData)
                    ->setAction($this->generateUrl('portal_reset_pass'))
                    ->add('email', 'email', array('label' => 'Ingresa la dirección de correo a la cual quieres que se envíe tu nueva contraseña.','attr' => array('placeholder' => 'correo electronico')))
                    ->add('enviar', 'submit')
                ->getForm();
                return $this->render('CoreAdminBundle:login:reset.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
            }
        }else{
            $defaultData = array('message' => 'Type your message here');
            $form = $this->createFormBuilder($defaultData)
                ->setAction($this->generateUrl('portal_reset_pass'))
                ->add('email', 'email', array('label' => 'Ingresa la dirección de correo a la cual quieres que se envíe tu nueva contraseña.','attr' => array('placeholder' => 'correo electronico')))
                ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:reset.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
        }
    }
##########  ##########
}
