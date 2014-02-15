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
                    $sesssion->set('session_admin', 'all');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmtoluca' ){
            if( $pass == 't01uc4' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'TOL');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmzapopan' ){
            if( $pass == 'z4p0p4n' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'ZAP');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmhispano' ){
            if( $pass == 'hisp4n0' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'HIS');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmchapala' ){
            if( $pass == 'ch4p4l4' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'CHA');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmcumbres' ){
            if( $pass == 'cumbr3s' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'CUM');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmgdl' ){
            if( $pass == 'gu4d4l4j4r4' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'GDLSUR');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmhmo' ){
            if( $pass == 'h3rm0si110' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'HMO');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmlomas' ){
            if( $pass == 'l0m4sv3rd3s' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'LOM');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmmty' ){
            if( $pass == 'm0nt3rr3y' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'SN');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmpuebla' ){
            if( $pass == 'pu3b14' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'PUE');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmqro' ){
            if( $pass == 'qu3r3t4r0' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'QRO');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmsanr' ){
            if( $pass == 's4nr4f431' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'SAN');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmtlalpan' ){
            if( $pass == 't141p4n' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'TLA');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmcoyoacan' ){
            if( $pass == 'c0y04c4n' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'COY');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        elseif( $user == 'uvmmarina' ){
            if( $pass == 'm4rin4' ){
                $session = base64_encode( md5( $user.$pass.date('Y-n-d') ) );
                    $sesssion->set('session_admin', 'MAR');
                    $sesssion->set('session_id', $session);
                return $this->redirect( $this->generateUrl('admin_home', array( 'session' => $session )) );
            }else{ return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' )); }
        }

        else{
            return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Usuario y/o contraseña iválidos.' ));
        }
    }
    /***************************************************************************/
    public function homeAction($session)
    {
        $sesssion = $this->getRequest()->getSession();
        $session_id = $sesssion->get('session_id');
        $campus = $sesssion->get('session_admin');

        if ($session_id === $session ) {
            return $this->render('CoreAdminBundle:admin:lpg.html.twig', array( 'session' => $session, 'session_id' => $session_id, 'campus' => $campus ));
        }else{
            return $this->render('CoreAdminBundle:admin:index.html.twig', array( 'msg' => 'Su sesión ha caducado, ingrese de nuevo por favor.' ));
        }
    }
########## ##########
}
