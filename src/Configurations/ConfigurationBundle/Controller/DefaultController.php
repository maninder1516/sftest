<?php

namespace Configurations\ConfigurationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Unirest;
use Admin\UsersBundle\Messages;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('ConfigurationBundle:Default:index.html.twig');
    }

    // Get the email template settings
    public function email_template_settingsAction(Request $request) {
        // Get the logger
        $logger = $this->get('logger');
        try {
            $userid = 8;
            
            if ($request->isMethod('POST')) {
                $uploaded_file = $request->files->get('logo');
                $filename = '';
                if ($uploaded_file) {
                    $dir = $this->get('kernel')->getRootDir() . '/../web/uploads/files/';
                    $filename = time() . '.png';
                    $file = $uploaded_file->move($dir, $filename);
                }

                $color = $request->request->get('color');
                $fontsize = $request->request->get('font-size');
                $footer_text = $request->request->get('footer-text');
                $header_text = $request->request->get('header-text');
                $social_links = $request->request->get('social-links');
                if ($social_links == 'on') {
                    $social_links = 1;
                } else {
                    $social_links = 0;
                }

                $arr_email_template_settings = array(
                    'logo' => $filename,
                    'color' => $color,
                    'fontsize' => $fontsize,
                    'footer_text' => $footer_text,
                    'header_text' => $header_text,
                    'social_links' => $social_links,
                );
                
                // Sent data to API for Saving
                $headers = array('Accept' => 'application/json');
                $query = array('user_id' => $userid, 'arr_email_template_settings'=> json_encode($arr_email_template_settings) );

                // POST email settings data to the API link
                $api_link = $this->getParameter('api_link');                
                $response = Unirest\Request::post($api_link . 'post_email_template_settings', $headers, $query);
                if($response == true){
                   $this->addFlash('success', Messages::EML_TEMPLATE_SAVE_SUCCESS);
                }                
            }
            
            // Call the SDNA API to get the email template settings
            $headers = array('Accept' => 'application/json');
            $query = array('user_id' => $userid);

            // Get the API link
            $api_link = $this->getParameter('api_link');
            $response = Unirest\Request::get($api_link . 'get_email_template_settings', $headers, $query);

           
            if($response->body->success){                
                $eml_tpl_setting = $response->body->data;
            }

            //echo "Hello".$eml_tpl_setting->user_id;
            //dump($response);
//            var_dump($response->body);
            //var_dump($eml_tpl_setting);
            //die;

            return $this->render('ConfigurationBundle:Default:index.html.twig', array('eml_tpl_setting' => $eml_tpl_setting));
        } catch (\Exception $ex) {
            // Log the message here
            $logger->error('Error occured in ' . __METHOD__ . " in " . __FILE__ . " at " . __LINE__ . "\n  Error details are : " . $ex);
        }
    }

}
