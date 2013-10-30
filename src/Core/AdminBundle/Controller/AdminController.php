<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
########## ADMINISTRATOR ##########
    public function adminAction()
    {
        return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'error' => '00X0' ));
    }
    /***************************************************************************/
    public function loginAction()
    {
        $request = Request::createFromGlobals();
        $user = $request->request->get('user',NULL);
        $pass = $request->request->get('pass',NULL);
        if( $user == 'admin' ){
            if( $pass == '12345' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'error' => '00X1' )); }
        }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'error' => '00X2' )); }
    }
    /***************************************************************************/
    public function homeAction($session)
    {
        $s = base64_encode( md5('admin'.'12345'.date('Y-n-d') ) );
        return $this->render('CoreAdminBundle:admin:lpg.html.twig', array( 'session' => $session, 'session_id' => $s ));
    }
########## WEBSPOTS ##########
}
