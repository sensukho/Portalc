<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class AdminController extends Controller
{
########## ADMINISTRATOR ##########
    public function adminAction()
    {
        $sesssion = $this->getRequest()->getSession();
        $sesssion->invalidate();

        return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => '' ));
    }
    /***************************************************************************/
    public function loginAction()
    {
        $sesssion = new Session();
        $sesssion->start();

        $request = Request::createFromGlobals();
        $user = $request->request->get('user',NULL);
        $pass = $request->request->get('pass',NULL);
        if( $user == 'admin' ){
            if( $pass == '12345' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    //$this->get('cache')->save('session_admin', serialize('all'));
                    $sesssion->set('session_admin', 'all');
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmtoluca' ){
            if( $pass == 't01uc4' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    //$this->get('cache')->save('session_admin', serialize('TOL'));
                    $sesssion->set('session_admin', 'TOL');
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmcumbres' ){
            if( $pass == 'cumbr3s' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    //$this->get('cache')->save('session_admin', serialize('CUM'));
                    $sesssion->set('session_admin', 'CUM');
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' ));
        }
    }
    /***************************************************************************/
    public function homeAction($session)
    {
        $s1 = base64_encode( md5('admin'.'12345'.date('Y-n-d') ) );
        $s2 = base64_encode( md5('uvmtoluca'.'t01uc4'.date('Y-n-d') ) );
        $s3 = base64_encode( md5('uvmcumbres'.'cumbr3s'.date('Y-n-d') ) );
        if ($s1 === $session ) {
            return $this->render('CoreAdminBundle:admin:lpg.html.twig', array( 'session' => $session, 'session_id' => $s1 ));
        }elseif ($s2 === $session ) {
            return $this->render('CoreAdminBundle:admin:lpg.html.twig', array( 'session' => $session, 'session_id' => $s2 ));
        }
        elseif ($s3 === $session ) {
            return $this->render('CoreAdminBundle:admin:lpg.html.twig', array( 'session' => $session, 'session_id' => $s3 ));
        }else{
            return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Su sesión ha caducado, ingrese de nuevo por favor.' ));
        }
    }
########## WEBSPOTS ##########
}
