<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Core\AdminBundle\Entity\Users;
use Core\AdminBundle\Entity\radcheck;
use Core\AdminBundle\Entity\ssidmacauth;

class LoginController extends Controller
{
########## LOGIN ##########
    /***************************************************************************/
    public function startAction(Request $request)
    {
        if( $request->query->all() ){
            $params = '?';
            foreach ($request->query->all() as $key => $value) {
                $params .= $key.'='.$value.'&';
            }
        }
        return $this->redirect( "/web/login/".$params );
    }
    /***************************************************************************/
    public function loginAction(Request $request)
    {

        if( $request->query->all() ){
            $params = '?';
            foreach ($request->query->all() as $key => $value) {
                $params .= $key.'='.$value.'&';
            }
            $url = explode('&', $params);
        }else{
            $params = $request->request->get('params',NULL);
            $url = explode('&', $params);
        }

        foreach ($url as $value) {
            $pos = strpos($value, "auth");
            if ($pos === false) {  }
            else {
                $auth = explode('=', $value);
            }
        }

        if ( isset($auth) && $auth[1] == "failed" ) {
            return $this->redirect( "/web/login/error/" );
        }

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

            $em = $this->getDoctrine()->getManager();

            /***** VERIFICA USUARIO *****/
            $raduser1 = $em->getRepository('CoreAdminBundle:radcheck')->findOneBy(
                array(
                    'username'  => $user,
                    'value'  => $pass
                )
            );

            if (count($raduser1) < 1) {
                $msg = 'Usuario y/o contraseña inválidos';
                return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg, 'params' => $params ));
            }

            /***** VERIFICA SSID *****/
            foreach ($url as $value) {
                $pos = strpos($value, "ssid");
                if ($pos === false) {  }
                else {
                    $ssid = explode('=', $value);
                }
            }
            $raduser2 = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'username'  => $user,
                    'ssid'  => urldecode( $ssid[1] )
                )
            );

            if (count($raduser2) < 1) {
                $user_data = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                    array(
                        'username'  => $user
                    )
                );
                $msg = 'A este usuario le corresponde la red "'.$user_data->getSsid().'" ( Conectado a: "'.urldecode( $ssid[1] ).'" ). Por favor cambie de red WiFi e intente de nuevo.';
                return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg, 'params' => $params ));
            }

            /***** VERIFICA MACADDRESS *****/
            foreach ($url as $value) {
                $pos = strpos($value, "client_mac");
                if ($pos === false) {  }
                else {
                    $mac = explode('=', $value);
                }
            }
            $raduser3 = $em->getRepository('CoreAdminBundle:ssidmacauth')->findOneBy(
                array(
                    'macaddress'  => $this->formatMac( $mac[1] )
                )
            );

            $exist_mac = 0;

            if (count($raduser3)) {
                if ($raduser3->getUsername() == $user ) {
                    $exist_mac = 1;
                }else{
                    $exist_mac = 2;
                    $msg = 'Ese dispositivo ya está registrado en otra cuenta. No puede registrarse el mismo dispositivo en cuentas diferentes. Favor de utilizar las credenciales de la primer cuenta con la que se conectó el dispositivo.';
                }
            }else{
                $user_mac = $em->getRepository('CoreAdminBundle:ssidmacauth')->findBy(
                    array(
                        'username'  => $user,
                    )
                );
                if (count($user_mac) > 1) {
                    $exist_mac = 3;
                    $msg = 'Ya existen dos dispositivos registrados con esta cuenta, lo cual es el máximo permitido. Por favor espera a mañana para poder registrar este dispositivo';
                }
            }
            if ($exist_mac == 0 || $exist_mac == 1) {
                foreach ($url as $value) {
                    $pos = strpos($value, "sip");
                    if ($pos === false) {  }
                    else {
                        $sip = explode('=', $value);
                    }
                }
                $url = "http://".$sip[1].":9997/login";
                return $this->render('CoreAdminBundle:login:layout_zd.html.twig', array( 'user' => $user, 'pass' => $pass, 'url' => $url ));
            }

            return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg, 'params' => $params ));

        }

        $user = '';
        $pass = '';

        $request = $this->get('request');
        if($request->cookies->has('usu') && $request->cookies->has('pass') ){
            $user = $request->cookies->get('usu');
            $pass = $request->cookies->get('pass');
            $chk = "checked";
        }

        return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg, 'params' => $params ));
    }
    /***************************************************************************/
    public function errorAction()
    {
        return $this->render('CoreAdminBundle:login:error.html.twig', array());
    }
    /***************************************************************************/
    public function welcomeAction()
    {
        return $this->render('CoreAdminBundle:login:bienvenida.html.twig', array());
    }
    /***************************************************************************/
    public function registerAction(Request $request)
    {
        if( $request->query->all() ){
            $params = '?';
            foreach ($request->query->all() as $key => $value) {
                $params .= $key.'='.$value.'&';
            }
        }else{
            $form = $request->request->get('form',NULL);
            $params = $form['genpass'];
        }

        $usuario = new Users();
        $form = $this->createFormBuilder($usuario);
        $msg = '';

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $usuario->setFecha( new \DateTime('today') );
            $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('portal_register'))
                    ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                    ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                    ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                    ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                    ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
            ->getForm();
            
            ##### VALIDATE #####
            if ( $data['form']['username'] == '' ) {
                $msg = "El campo de usuario no debe estar vacío";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif(preg_match('/[^a-z_\-0-9]/i', $data['form']['username'])) {
                $msg = "El campo de usuario debe ser alfanumérico";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif ( $data['form']['newpass'] == '' ) {
                $msg = "El campo de password no debe estar vacío";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif(preg_match('/[^a-z_\-0-9]/i', $data['form']['newpass'])) {
                $msg = "El campo de password debe ser alfanumérico";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif ( $data['form']['newpasssecond'] == '' ) {
                $msg = "El campo de password secundario no debe estar vacío";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif(preg_match('/[^a-z_\-0-9]/i', $data['form']['newpasssecond'])) {
                $msg = "El campo de password secundario debe ser alfanumérico";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            elseif ( $data['form']['newpass'] !== $data['form']['newpasssecond'] ) {
                $msg = "Los campos de password deben coincidir.";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
            #####  #####

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'matricula' => $data['form']['matricula'],
                )
            );
            if ($user) {
                $user3 = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                    array(
                        'matricula' => $data['form']['matricula'],
                        'newpass' => '0'
                    )
                );
                if ($user3) {
                    $user2 = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                        array(
                            'username'  => $data['form']['username']
                        )
                    );
                    if (!$user2) {
                        if ($data['form']['newpass'] != $data['form']['newpasssecond']) {
                            $usuario->setFecha( new \DateTime('today') );
                            $form = $this->createFormBuilder($usuario)
                                ->setAction($this->generateUrl('portal_register'))
                                ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                                ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                                ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                                ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                                ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                                ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                                ->add('enviar', 'submit')
                            ->getForm();
                            $msg = "Los campos de contraseña no coinciden - Verifica que ambas sean iguales e intenta de nuevo.";
                            return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
                        }
                        $user->setNewpass($data['form']['newpass']);
                        $user->setUsername($data['form']['username']);
                        $user->setEmail($data['form']['email']);

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

                        $message = \Swift_Message::newInstance()
                            ->setSubject('Portal UVM :: Registro exitoso !')
                            ->setFrom(array('soporte@uvm.com' => 'Soporte'))
                            ->setTo($data['form']['email'])
                            ->setBody(
                                $this->renderView(
                                    'CoreAdminBundle:login:mailTamplateReg.html.twig',
                                    array('user' => $user->getUsername(),'pass' => $data['form']['newpass'] )
                                ), 'text/html'
                            )
                        ;
                        $this->get('mailer')->send($message);

                        return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg, 'params' => $params ));
                    }else{
                        $usuario->setFecha( new \DateTime('today') );
                        $form = $this->createFormBuilder($usuario)
                            ->setAction($this->generateUrl('portal_register'))
                            ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                            ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                            ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                            ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                            ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                            ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                            ->add('enviar', 'submit')
                        ->getForm();
                        $msg = "El nombre de usuario seleccionado ya está utilizado por otra cuenta. Favor de elegir otro.";
                        return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
                    }
                }else{
                    $usuario->setFecha( new \DateTime('today') );
                    $form = $this->createFormBuilder($usuario)
                        ->setAction($this->generateUrl('portal_register'))
                        ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                        ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                        ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                        ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                        ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                        ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                        ->add('enviar', 'submit')
                    ->getForm();
                    $msg = "Esta matrícula ya fue registrada con anterioridad - Utiliza el usuario y password que registraste para unirte a la red. Si el problema persiste, contacta al Centro de Cómputo de tu campus.";
                    return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
                }
            }else{
                $usuario->setFecha( new \DateTime('today') );
                $form = $this->createFormBuilder($usuario)
                    ->setAction($this->generateUrl('portal_register'))
                    ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                    ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                    ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                    ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                    ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
                ->getForm();
                $msg = "Esta mátrícula no existe en el sistema. Favor de acudir al Centro de Cómputo para recibir asistencia.";
                return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
            }
        }else{
            $usuario->setFecha( new \DateTime('today') );
            $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('portal_register'))
                    ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                    ->add('email', 'email', array('label' => 'E-mail','attr' => array('placeholder' => 'correo electronico')))
                    ->add('username', 'text', array('label' => 'Usuario (elije un nombre de usuario de por lo menos 5 caracteres)','attr' => array('placeholder' => 'Mínimo de 5 caracteres.', 'pattern' => '.{5,}')))
                    ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                    ->add('newpass', 'password', array('label' => 'Password (mínimo 6 caracteres, no se diferencian mayúsculas de minúsculas y utiliza solo caracteres alfanuméricos.)','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                    ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                    ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:register.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'errors' => '', 'params' => $params ));
        }
    }
    /***************************************************************************/
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

                    $msg = "Tu contraseña se ha cambiado con éxito, ya puedes ingresar.";
                    return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg, 'params' => 0 ));
                }else{
                    $form = $this->createFormBuilder($usuario)
                        ->setAction($this->generateUrl('portal_change_pass'))
                        ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Usuario', 'pattern' => '.{5,}')))
                        ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                        ->add('genpass', 'text', array('label' => 'Ingresa el password que te enviamos por mail','attr' => array('placeholder' => '8 caracteres.', 'pattern' => '.{6,}')))
                        ->add('newpass', 'password', array('label' => 'Nuevo password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                        ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                        ->add('enviar', 'submit')
                    ->getForm();
                    $msg = "Datos Inválidos - Valida que tus datos sean correctos. Si el problema persiste, contacta al Centro de Cómputo de tu plantel.";
                    return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
                }
            }else{
                $form = $this->createFormBuilder($usuario)
                    ->setAction($this->generateUrl('portal_change_pass'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'Usuario', 'pattern' => '.{5,}')))
                    ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
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
                ->add('matricula', 'text', array('label' => 'Matrícula','attr' => array('placeholder' => 'Matrícula')))
                ->add('genpass', 'text', array('label' => 'Ingresa el password que te enviamos por mail','attr' => array('placeholder' => '8 caracteres.', 'pattern' => '.{6,}')))
                ->add('newpass', 'password', array('label' => 'Nuevo password','attr' => array('placeholder' => 'Mínimo de 6 caracteres.', 'pattern' => '.{6,}')))
                ->add('newpasssecond', 'password', array('label' => 'Ingresa de nuevo el password','attr' => array('placeholder' => 'Reingresa el password', 'pattern' => '.{6,}')))
                ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
        }
    }
    /***************************************************************************/
    public function resetAction(Request $request)
    {

        if( $request->query->all() ){
            $params = '?';
            foreach ($request->query->all() as $key => $value) {
                $params .= $key.'='.$value.'&';
            }
        }else{
            $form = $request->request->get('form',NULL);
            $params = $form['genpass'];
        }

        $form = $this->createFormBuilder();
        $msg = '';
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'username' => $data['form']['username'],
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
                    ->setFrom(array('soporte@uvm.com' => 'Soporte'))
                    ->setTo($data['form']['email'])
                    ->setBody(
                        $this->renderView(
                            'CoreAdminBundle:login:mailTamplate.html.twig',
                            array('user' => $user->getUsername(),'pass' => $newpass, 'hostname' => $_SERVER['HTTP_HOST'] )
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                $msg = 'Se ha enviado exitosamente un correo a su cuenta registrada. Favor de esperar 3 minutos para recibirlo y buscar en la carpeta de Correos NO Deseados (SPAM) en caso de no verlo en su carpeta de entrada (INBOX)';
                return $this->render('CoreAdminBundle:login:plantilla.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg, 'params' => $params ));
            }else{
                $msg = 'La cuenta de correo y/o el usuario no se encuentran registrados en nuestro sistema';
                $defaultData = array('message' => '');
                $form = $this->createFormBuilder($defaultData)
                    ->setAction($this->generateUrl('portal_reset_pass'))
                    ->add('username', 'text', array('label' => 'Verifica tu nombre de usuario','attr' => array('placeholder' => 'username')))
                    ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                    ->add('email', 'email', array('label' => 'Ingresa la dirección de correo con la cual te registraste para poder recibir tu nueva contraseña.','attr' => array('placeholder' => 'correo electronico')))
                    ->add('enviar', 'submit')
                ->getForm();
                return $this->render('CoreAdminBundle:login:reset.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'params' => $params ));
            }
        }else{
            $defaultData = array('message' => 'Type your message here');
            $form = $this->createFormBuilder($defaultData)
                ->setAction($this->generateUrl('portal_reset_pass'))
                ->add('username', 'text', array('label' => 'Verifica tu nombre de usuario','attr' => array('placeholder' => 'username')))
                ->add('genpass', 'hidden', array('attr' => array('value' => $params)))
                ->add('email', 'email', array('label' => 'Ingresa la dirección de correo con la cual te registraste para poder recibir tu nueva contraseña.','attr' => array('placeholder' => 'correo electronico')))
                ->add('enviar', 'submit')
            ->getForm();
            return $this->render('CoreAdminBundle:login:reset.html.twig', array( 'form' => $form->createView(), 'msg' => $msg, 'params' => $params ));
        }
    }
    /***************************************************************************/
    public function formatMac($mac)
    {
        $longitud = strlen($mac);
            $macaddress = '';
            for($i = 0; $i < $longitud; ++$i)
                if($i%2==0)
                    if($i == 0)
                        $macaddress .= $mac[$i].$mac[$i+1];
                    else
                        $macaddress .= '-'.$mac[$i].$mac[$i+1];
        return $macaddress;
    }
##########  ##########
}
