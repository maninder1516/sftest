<?php

namespace Admin\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Unirest;
use Admin\UsersBundle\Messages;

class DefaultController extends Controller {

    public function getLoginAction(Request $request) {
        return $this->render('UsersBundle:Default:login.html.twig');
    }

    // Login a user
    public function postLoginAction(Request $request) {
        // Get the logger
        $logger = $this->get('logger');
        try {
            if ($request->isMethod('POST')) {                
                // Get the user login details
                $username = $request->request->get('username');
                $password = $request->request->get('password');               
                
               // Sent data to API for Saving
                $headers = array('Accept' => 'application/json');
                $query = array('username' => $username, 'password'=> $password );

                // POST email settings data to the API link
                $api_link = $this->getParameter('api_link');                
                $response = Unirest\Request::post($api_link . 'login', $headers, $query);
                
                if($response->body->success){
                   $token= $response->body->data; // Get and Save token
                }
                return $this->render('UsersBundle:Default:index.html.twig');
            }
        } catch (\Exception $ex) {
            // Log the message here
            $logger->error('Error occured in ' . __METHOD__ . " in " . __FILE__ . " at " . __LINE__ . "\n  Error details are : " . $ex);
        }
    }

}
