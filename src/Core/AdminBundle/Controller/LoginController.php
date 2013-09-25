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
        return $this->render('CoreAdminBundle:login:index.html.twig', array( 'user' => $user, 'pass' => $pass, 'chk' => $chk, 'msg' => $msg ));
    }

    public function changeAction(Request $request)
    {
        $usuario = new Users();
        $form = $this->createFormBuilder($usuario);
        $msg = '';

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('CoreAdminBundle:Users')->findOneBy(
                array(
                    'username'  => $data['form']['username'],
                    'genpass' => $data['form']['genpass'],
                    'fecha' => new \DateTime($data['form']['fecha']["year"]."-".$data['form']['fecha']["month"]."-".$data['form']['fecha']["day"]),
                    'newpass' => ''
                )
            );
            if ($user) {
                $user->setNewpass(1);
                $em->persist($user);
                $em->flush();

                $raduser = new Radcheck();
                $raduser->setUsername($data['form']['username']);
                $raduser->setAttribute('MD5-Password');
                $raduser->setOp(':=');
                $raduser->setValue($data['form']['newpass']);
                $em->persist($raduser);
                $em->flush();

                $msg = "Tu contraseña se ha cambiado con éxito, ya puedes ingresar.";
                return $this->render('CoreAdminBundle:login:index.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg ));
            }else{
                $usuario->setFecha( new \DateTime('today') );
                $form = $this->createFormBuilder($usuario)
                    ->setAction($this->generateUrl('portal_change_pass'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'nombre de usuario')))
                    ->add('genpass', 'password', array('label' => 'Password (Actual)','attr' => array('placeholder' => 'contraseña actual')))
                    ->add('newpass', 'password', array('label' => 'Password (Nuevo)','attr' => array('placeholder' => 'contraseña nueva')))
                    ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
                    ->add('enviar', 'submit')
                ->getForm();
                $msg = "Usuario no válido, favor de verificar sus datos.";
                return $this->render('CoreAdminBundle:login:change.html.twig', array( 'form' => $form->createView(), 'msg' => $msg ));
            }
        }else{
            $usuario->setFecha( new \DateTime('today') );
            $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('portal_change_pass'))
                    ->add('username', 'text', array('label' => 'Usuario','attr' => array('placeholder' => 'nombre de usuario')))
                    ->add('genpass', 'password', array('label' => 'Password (Actual)','attr' => array('placeholder' => 'contraseña actual')))
                    ->add('newpass', 'password', array('label' => 'Password (Nuevo)','attr' => array('placeholder' => 'contraseña nueva')))
                    ->add('fecha', 'date', array('years' => range(date('Y') -60, date('Y')),'label' => 'Fecha de nacimiento'))
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
                $user->setNewpass('');
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Portal UVM :: Reseteo de contraseña')
                    ->setFrom('soporte@uvm.com')
                    ->setTo($data['form']['email'])
                    ->setBody(
                        $this->renderView(
                            'CoreAdminBundle:login:mailTamplate.html.twig',
                            array('name' => $user->getNombre(),'user' => $user->getUsername(),'pass' => $newpass )
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                $msg = 'Se ha enviado un correo con tu nueva contraseña a: '.$data['form']['email'];
                return $this->render('CoreAdminBundle:login:index.html.twig', array( 'user' => '', 'pass' => '', 'chk' => '', 'msg' => $msg ));
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
