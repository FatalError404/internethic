<?php
/**
 * reCAPTCHA extension for eZ Publish
 * Written by Bruce Morrison <bruce@stuffandcontent.com>
 * Copyright (C) 2008. Bruce Morrison.  All rights reserved.
 * http://www.stuffandcontent.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

// Include the super class file
include_once( "kernel/classes/ezdatatype.php" );
// Include reCAPTCHA lib
include_once( "extension/recaptcha/classes/recaptchalib.php" );

// Define the name of datatype string
define( "EZ_DATATYPESTRING_RECAPTCHA", "recaptcha" );


class recaptchaType extends eZDataType
{
  /**
   * feature regarding version to apply different behaviour
   *    - constant default version defined to 2 because of last upgrade (the new feature is revertable, carefull ini conf is main)
   *    - recaptcha ini to use recaptcha ini configuration
   *    - version to define the current used version
   */
  const DEFAULT_VERSION = 2 ;
  private $ini;
  private $version;
  /*!
   Construction of the class, note that the second parameter in eZDataType 
   is the actual name showed in the datatype dropdown list.
  */
  function recaptchaType()
  {
    $this->eZDataType( EZ_DATATYPESTRING_RECAPTCHA, "reCAPTCHA", 
                           array( 'serialize_supported' => false,
                                  'translation_allowed' => false ) );
    $this->setConfiguration() ;
  }

  /**
   * @fn void setConfiguration()
   */
  private function setConfiguration(){
    $this->ini = eZINI::instance( 'recaptcha.ini' ) ;
    $this->version = $this->ini->hasVariable( 'VersionSettings', 'Current' ) ? (int)$this->ini->variable( 'VersionSettings','Current' ) : self::DEFAULT_VERSION ;
  }

  /*!
    Validates the input and returns true if the input was
    valid for this datatype.
  */
  function validateObjectAttributeHTTPInput( $http, $base, $objectAttribute ){
    $classAttribute = $objectAttribute->contentClassAttribute();
    $newOjbectsOnly = $this->ini->variable( 'PublishSettings', 'NewObjectsOnly' ) == 'true';
    if ( $newOjbectsOnly && $objectAttribute->attribute( 'object' )->attribute( 'status' ) ){
      return eZInputValidator::STATE_ACCEPTED;
    }
    /**
     * new feature regarding recaptcha version 2
     *    - apply specific validation according to used version
     */
    switch ( $this->version ) {
      case 1:
        if ( $classAttribute->attribute( 'is_information_collector' ) or $this->reCAPTCHAValidate($http) ){
          return eZInputValidator::STATE_ACCEPTED;
        }
        break;
      case 2:
        if ( $classAttribute->attribute( 'is_information_collector' ) or $this->reCAPTCHAV2Validate($http) ){
          return eZInputValidator::STATE_ACCEPTED;
        }
        break;
    }
    $objectAttribute->setValidationError(ezpI18n::tr( 'extension/recaptcha', "The reCAPTCHA wasn't entered correctly. Please try again."));
    return eZInputValidator::STATE_INVALID;
  }
  /**
   *
   */
  function validateCollectionAttributeHTTPInput( $http, $base, $objectAttribute ){
    /**
     * new feature regarding recaptcha version 2
     *    - add default value to return (invalid)
     *    - apply specific validation according to used version
     */
    $result = eZInputValidator::STATE_INVALID ;
    switch ( $this->version ) {
      case 1:
        if ( $this->reCAPTCHAValidate( $http ) ){
          $result = eZInputValidator::STATE_ACCEPTED ;
        }else{
          $objectAttribute->setValidationError( ezpI18n::tr( 'extension/recaptcha', "The reCAPTCHA wasn't entered correctly. Please try again." ) ) ;
        }
        break ;
      case 2:
        if ( $this->reCAPTCHAV2Validate( $http ) ){
          $result = eZInputValidator::STATE_ACCEPTED ;
        }else{
          $objectAttribute->setValidationError( ezpI18n::tr( 'extension/recaptcha', "The reCAPTCHA wasn't entered correctly. Please try again." ) ) ;
        }
        break ;
    }
    return $result ;
  }

  function isIndexable()
  {
    return false;
  }

  function isInformationCollector()
  {
    return true;
  }

  function hasObjectAttributeContent( $contentObjectAttribute )
  {
    return false;
  }

  static function reCAPTCHAValidate( $http )
  {
    // check if the current user is able to bypass filling in the captcha and
    // return true without checking if so
    $currentUser = eZUser::currentUser();
    $accessAllowed = $currentUser->hasAccessTo( 'recaptcha', 'bypass_captcha' );
    if ($accessAllowed["accessWord"] == 'yes')
      return true;

    $ini = eZINI::instance( 'recaptcha.ini' );
    // If PrivateKey is an array try and find a match for the current host
    $privatekey = $ini->variable( 'Keys', 'PrivateKey' );
    if ( is_array($privatekey) )
    {
      $hostname = eZSys::hostname();
      if (isset($privatekey[$hostname]))
        $privatekey = $privatekey[$hostname];
      else
        // try our luck with the first entry
        $privatekey = array_shift($privatekey);
    }
    $recaptcha_challenge_field = $http->postVariable('recaptcha_challenge_field');
    $recaptcha_response_field = $http->postVariable('recaptcha_response_field');
    $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $recaptcha_challenge_field,
                                $recaptcha_response_field);
    return $resp->is_valid;
  }

  /**
   * @fn public reCAPTCHAV2Validate( $http )
   */
  public function reCAPTCHAV2Validate( $http ){
    // check if the current user is able to bypass filling in the captcha and
    // return true without checking if so
    $currentUser = eZUser::currentUser() ;
    $accessAllowed = $currentUser->hasAccessTo( 'recaptcha', 'bypass_captcha' ) ;
    if ( $accessAllowed['accessWord'] == 'yes' )
      return true;
    // If PrivateKey is an array try and find a match for the current host
    $secret = $this->ini->variable( 'Keys', 'PrivateKey' ) ;
    if ( is_array( $secret ) ){
      $hostname = eZSys::hostname();
      if ( isset( $secret[$hostname] ) ){
        $secret = $secret[$hostname] ;
      }else{
        // try our luck with the first entry
        $secret = array_shift($secret);
      }
    }
    // post variable sent by the recaptcha v2 component
    $response = $http->postVariable( 'g-recaptcha-response' ) ;
    // get compliant client IP from ezsystem framework
    $remoteip = eZSys::clientIP() ;
    // use a class to handle the new recaptcha v2 api
    $apiRecaptcha2 = new APIRecaptcha2( $secret, $response, $remoteip ) ;
    return $apiRecaptcha2->isSuccess() ;
  }

}
eZDataType::register( EZ_DATATYPESTRING_RECAPTCHA, "recaptchaType" );
