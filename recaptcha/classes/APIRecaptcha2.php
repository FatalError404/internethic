<?php
    /**
     * @class APIRecaptcha2
     * @file APIRecaptcha2.php
     * @author Julien MORIAUX
     * @company Internethic
     * @date 18/03/18
     * @copyright Internethic 2018-2021
     * @version 1.0
     */
     class APIRecaptcha2{
        const API_DEFAULT_URL = 'https://www.google.com/recaptcha/api/siteverify' ;
        /**
         * private variables to handle http post parameters
         */
        private $secret ;
        private $response ;
        private $remoteip ;
        /**
         * private variables to handle api configuration / response
         */
        private $endpoit ;
        private $api_response ;
        /**
         * global private variables
         */
        private $ini ;
        /**
         * @fn __construct( $secret, $response, $remoteip )
         * @param $secret
         * @param $response
         * @param $remoteip
         */
        public function __construct( $secret, $response, $remoteip ){
            $this->ini = eZINI::instance( 'recaptcha.ini' ) ;
            $this->secret = $secret ;
            $this->response = $response ;
            $this->remoteip = $remoteip ;
            /**
             * default settings
             */
            $this->setDefaultSettings() ;
        }
        /**
         * @fn void setDefaultSettings()
         */
        protected function setDefaultSettings(){
            $this->setEndpoint() ;
            $this->setApiResponse() ;
        }
        /**
         * @fn protected void setEndpoint()
         */
        protected function setEndpoint(){
            $this->endpoint = $this->ini->hasVariable( 'APISettings', 'URL' ) ? $this->ini->variable( 'APISettings', 'URL' ) : self::API_DEFAULT_URL ;
        }
        /**
         * @fn protected void setApiResponse()
         */
        protected function setApiResponse(){
            /**
             * Expecteed params to send to the recaptcha API version 2
             *  - <string>secret : private recaptcha key
             *  - <string>response : post sent by the recaptcha v2 component (g-recaptcha-response)
             *  - <string>remoteip : (optionnal) the client IP
             */
            $params = [ 'secret' => $this->secret, 'response' => $this->response, 'remoteip' => $this->remoteip ] ;
            /**
             * Curl Options
             *  - endpoint : the recaptcha API V2 endpoint
             *  - post : enable post in curl exec
             *  - postfields : array of expected post parameters
             *  - returntransfer : to get the response from the API endpoint
             */
            $options = [ CURLOPT_URL => $this->endpoint, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $params, CURLOPT_RETURNTRANSFER => true ] ;
            $ch = curl_init() ;
            curl_setopt_array( $ch, $options ) ;
            $this->api_response = curl_exec( $ch ) ;
        }
        /**
         * @fn boolean isSuccess()
         */
        public function isSuccess(){
            $result = false ;
            if( !is_null( $this->api_response ) ){
                $array_response = json_decode( $this->api_response, true ) ;
                if( isset( $array_response['success'] ) ){
                    $result = (bool)$array_response['success'] ;
                }
            }
            return $result ;
        }
     }