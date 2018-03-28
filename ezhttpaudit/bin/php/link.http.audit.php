<?php
/**
 * File containing the link.http.audit.php script
 *
 * @copyright Copyright (C) Internethic. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
require_once 'autoload.php' ;

$cli = eZCLI::instance();

$scriptSettings = array();
$scriptSettings['description'] = 'Audit to list and count internal urls published in backoffice.' ;
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = false;
$scriptSettings['use-extensions'] = true;

$script = eZScript::instance( $scriptSettings );
$script->startup();

$config = '[term:][domain:][modify][newterm:]';
$argumentConfig = '';
$optionHelp = array(    'term' => 'Type the terme you are looking for in edited URL',
                        'domain' => 'Type the domain you want to search in edited URL',
                        'modify' => 'Dry run is default use, if you want to modify URL with https you have to use this option',
                        'newterm' => 'Type the new term you want to use in placeof current searched term. This option is only use in modify mode' ) ;
$arguments = false;
$useStandardOptions = true;

$options = $script->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
$script->initialize();

$cli->output( "Audit des liens internes ..." ) ;
$ezurl_params = array( 'only_published' => true ) ;

$iniAudit = eZINI::instance( 'audit.ini' ) ;

$term = $iniAudit->hasVariable( 'HTTPAuditSettings', 'Term' ) ?  $iniAudit->variable( 'HTTPAuditSettings', 'Term' ) : 'http' ;
if ( $options['term'] ){
    $term = $options['term'] ;
}
$cli->notice( "You are currently searching for the term $term in URLS published in backoffice" ) ;

$domain = $iniAudit->hasVariable( 'HTTPAuditSettings', 'Domain' ) ?  $iniAudit->variable( 'HTTPAuditSettings', 'Domain' ) : false ;
if ( $options['domain'] ){
    $domain = $options['domain'] ;
}
if( $domain === false ){
    $cli->error( "No domain we dont know what to look for, please configure audit or define domain thanks to option script (see help)" ) ;
    $script->shutdown( 0 ) ;
}
$cli->notice( "You are looking for domain $domain in URLS published in backoffice" ) ;

$hasToModify = $newterm = false ;
if( $options['modify'] ){
    $hasToModify = true ;
    $newterm = $iniAudit->hasVariable( 'HTTPAuditSettings', 'NewTerm' ) ?  $iniAudit->variable( 'HTTPAuditSettings', 'NewTerm' ) : 'https' ;
    if( $options['newterm'] ){
        $newterm = $options['newterm'] ;
    }
    $cli->notice( "You will modify all the URLs that match your term and your domain!" ) ;
    $cli->notice( "The replacement term you have chosen (audit.ini configuration or directly via this script, please consult the script help) is: $newterm" ) ;
}else{
    $cli->notice( "No URL in place will be changed, everything will have to be done manually in the backoffice!" ) ;
}

$db = eZDB::instance() ;

$count_urls = eZURL::fetchListCount( $ezurl_params ) ;
$urls = eZURL::fetchList( $ezurl_params ) ;
$total_url = $total_content = 0 ;
$array_content_id = $array_content_attr_id = array() ;
$array_csv = array( array( 'URL', 'Field to modify' ) ) ;
foreach ( $urls as $url ){
    $urlID = $url->attribute( 'id' ) ;
    $urlString = $url->attribute( 'url' ) ;
    // on filtre uniquement les urls internes poitant vers les sites du gouv et qui sont en http
    if ( preg_match("/^($term:)/i", $urlString ) && preg_match("/\b$domain\b/i", $urlString ) ){
        $total_url++;
        $cli->output( "audit-" . $cli->stylize( 'emphasize', $urlString ) . " ", true ) ;
        // on remonte l'objet pour indiquer dans le rapport
        // - le nombre d'objets au total à traiter
        // - la liste exhaustive des objets à traiter
        $urlObjectLinkList = eZPersistentObject::fetchObjectList(   eZURLObjectLink::definition(),
                                                                    null,
                                                                    array( 'url_id' => $urlID ),
                                                                    null,
                                                                    null,
                                                                    true ) ;
        foreach ( $urlObjectLinkList as $urlObjectLink ){
            $objectAttributeID = $urlObjectLink->attribute( 'contentobject_attribute_id' ) ;
            $objectAttributeVersion = $urlObjectLink->attribute( 'contentobject_attribute_version' ) ;
            $objectAttribute = eZContentObjectAttribute::fetch( $objectAttributeID, $objectAttributeVersion ) ;
            if (    $objectAttribute instanceof eZContentObjectAttribute ){
                if( $objectAttribute->objectVersion()->attribute( 'version' ) == $objectAttributeVersion ){
                    $objectID = $objectAttribute->attribute( 'contentobject_id' ) ;
                    $contentClassAttributeName = $objectAttribute->contentClassAttributeName() ;
                    $eZContentObject = eZContentObject::fetch( $objectID ) ;
                    $mainNode = $eZContentObject->mainNode() ;
                    if( $mainNode instanceof eZContentObjectTreeNode ){
                        // on place exprès dans le log des ; pour pouvoir transformer facilement le .lg en .csv (in fine pour fournir une matrice au client)
                        $urlAlias = $mainNode->urlAlias() ;
                    }
                    // on compte le nombre d'objets à modifier
                    if( !in_array( $objectID, $array_content_id ) ){
                        $array_csv[] = array( $urlAlias, $contentClassAttributeName ) ;
                        $array_content_id[] = $objectID ;
                        $total_content++ ;
                    }
                }
            }
        }
        // modification if needed
        if( $hasToModify && is_string( $newterm ) ){
            $newURL = str_replace( $term, $newterm, $urlString ) ;
            $cli->warning( "URL $urlString is updated to $newURL" ) ;
            $db->begin() ;
            $url->setAttribute( 'url', $newURL ) ;
            $url->store() ;
            $db->commit() ;
        }
    }
}
$cli->output( '#################################################################################################' ) ;
$cli->output( "Total URLS stored in the $count_urls link fields (any internal and external urls combined)" ) ;
$cli->output( "Total HTTP URL : $total_url" ) ;
$cli->output( "Total Content with Http link : $total_content" ) ;
$cli->output( "All links have been audited!" ) ;
// on génère le cssv à partir des données recueilles
$csvService = new CsvService() ;
$csvService->generate( $array_csv ) ;
$db->close() ;
$script->shutdown( 0 );
?>