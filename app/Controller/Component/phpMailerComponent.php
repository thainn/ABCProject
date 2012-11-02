<?php
class phpMailerComponent extends component{
  function mail_smtp($from,$to,$subject,$body,$html=1) 
    {
             App::import('vendor', 'Mailer', array('file' => 'mailer/smtp.php'));
            $smtp=new smtp_class;
            $smtp->host_name="thienduongweb.com";       /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
            $smtp->localhost="localhost";       /* Your computer address */
            $smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
            $smtp->timeout=10;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
            $smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
                                                                                       Set to 0 to use the same defined in the timeout variable */
            $smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
            $smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */

            $mailHost		= 'thienduongweb.com';
            $mailOutUser	= 'test@thienduongweb.com';
            $mailOutPassword	= '5gakvM7q';

            $smtp->pop3_auth_host = $mailHost;           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
            $smtp->user=$mailOutUser;                     /* Set to the user name if the server requires authetication */
            $smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
            $smtp->password=$mailOutPassword;                 /* Set to the authetication password */
            $smtp->workstation="";              /* Workstation name for NTLM authentication */
            $smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
                                                                                       Leave it empty to make the class negotiate if necessary */
            /*
             * If you need to use the direct delivery mode and this is running under
             * Windows or any other platform that does not have enabled the MX
             * resolution function GetMXRR() , you need to include code that emulates
             * that function so the class knows which SMTP server it should connect
             * to deliver the message directly to the recipient SMTP server.
             */
            if($smtp->direct_delivery)
            {
                    if(!function_exists("GetMXRR"))
                    {
                            /*
                            * If possible specify in this array the address of at least on local
                            * DNS that may be queried from your network.
                            */
                            $_NAMESERVERS=array();
                            include("getmxrr.php");
                    }
                    /*
                    * If GetMXRR function is available but it is not functional, to use
                    * the direct delivery mode, you may use a replacement function.
                    */
                    /*
                    else {
                            $_NAMESERVERS=array();
                            if(count($_NAMESERVERS)==0)
                                    Unset($_NAMESERVERS);
                            include("rrcompat.php");
                            $smtp->getmxrr="_getmxrr";
                    }
                    */
            }

            $header="";
            if ($html==0)
                    $header=array(
                            "From: $from",
                            "To: $to",
                            "Subject: $subject",
                            "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z"));
            else
                    $header=array(
                            "MIME-Version: 1.0",
                            "Content-type: text/html; charset=utf-8",
                            "From: $from",
                            "To: $to",
                            "Subject: $subject",
                            "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z"));
            $ret=$smtp->SendMessage($from,array($to),$header,$body);
            return $ret;
    }
}