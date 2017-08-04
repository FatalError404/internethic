<?php

namespace Internethic\Bundle\RestBundle\Controller;

use eZ\Publish\Core\REST\Server\Controller as BaseController;
use eZ\Publish\API\Repository\Values\Content\Query ;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion ;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause ;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator ;
use Internethic\Bundle\RestBundle\Rest\Values ;
use eZ\Publish\Core\REST\Server\Values\RestContent ;
use Symfony\Component\HttpFoundation\Request;
/**
 * class RestController
 * author JMO Internethic
 */
class RestController extends BaseController
{
	/**
	 * fn fetchContentTreeAction( $locationId, Request $request )
	 * return Internethic\Bundle\RestBundle\Rest\Values\FetchContentTree
	 */
    public function fetchContentTreeAction( $locationId, Request $request )
    {
        $contents = array() ;
        $repository = $this->container->get( 'ezpublish.api.repository' ) ;
        // todo : delegate get parameter settings to a service
        $type = $request->query->has( 'type' ) ? $request->query->get( 'type' ) : null ;
        $offset = $request->query->has( 'offset' ) ? (int)$request->query->get( 'offset' ) : 0 ;
        $limit = $request->query->has( 'limit' ) ? (int)$request->query->get( 'limit' ) : 50 ;
        $sort_target = $request->query->has( 'sort_target' ) ? $request->query->get( 'sort_target' ) : 'date_modified' ;
        $sort_direction = $request->query->has( 'sort_direction' ) ? $request->query->get( 'sort_direction' ) : Query::SORT_DESC ;
        $sort_data = $request->query->has( 'sort_data' ) ? $request->query->get( 'sort_data' ) : null ;
        $depth_value = $request->query->has( 'depth_value' ) ? $request->query->get( 'depth_value' ) : 2 ;
        $depth_operator = $request->query->has( 'depth_operator' ) ? $request->query->get( 'depth_operator' ) : Operator::GTE ;
        $field_filter_identifier = $request->query->has( 'field_filter_identifier' ) ? $request->query->get( 'field_filter_identifier' ) : null ;
        $field_filter_operator = $request->query->has( 'field_filter_operator' ) ? $request->query->get( 'field_filter_operator' ) : Operator::EQ ;
        $field_filter_value = $request->query->has( 'field_filter_value' ) ? $request->query->get( 'field_filter_value' ) : null ;
        // build the query
        $query = new Query() ;
        // build offset and limit
        $query->offset = $offset ;
        $query->limit = $limit ;
        // build sort clause (for moment only published and modified)
        switch ( $sort_target ) {
        	case 'date_published':
        		$query->sortClauses = array( new SortClause\DatePublished( $sort_direction ) ) ;
        		break;
        	default:
        		$query->sortClauses = array( new SortClause\DateModified( $sort_direction ) ) ;
        		break;
        }
        // default criterion based on Subtree
        $locationService = $repository->getLocationService() ;
        $criterions[] = new Criterion\Subtree( $locationService->loadLocation( $locationId )->pathString ) ;
        $criterions[] = new Criterion\Depth( $depth_operator, $depth_value ) ;
        // if type is defined : include a ContentTypeIdentifier Criterion
        if( !is_null( $type ) ){
        	$criterions[] = new Criterion\ContentTypeIdentifier( $type ) ;
        }
        // if field_filter is defined : include a Field Criterion
        if( !is_null( $field_filter_identifier ) ){
        	$criterions[] = new Criterion\Field(  $field_filter_identifier, $field_filter_operator, $field_filter_value ) ;
        }
        $query->criterion = new Criterion\LogicalAnd( $criterions ) ;
        $searchService = $repository->getSearchService() ;
        $contentTypeService = $repository->getContentTypeService() ;
        $result = $searchService->findContent( $query ) ;
        $total_count = $result->totalCount ;
        // browse search hits / parse Content to RestContent /build a RestContent Array
        foreach ( $result->searchHits as $searchHit ) {
            $content = $searchHit->valueObject ;
            $contentInfo = $content->__get( 'contentInfo' ) ;
            $mainLocation = $locationService->loadLocation( $contentInfo->mainLocationId ) ;
            $contentType = $contentTypeService->loadContentType( $contentInfo->contentTypeId ) ;
            $contents[] = new RestContent( $contentInfo, $mainLocation, $content, $contentType, array() ) ;
        }
	    return new Values\FetchContentTree( $contents, $locationId, $type, $offset, $limit, $sort_target, $sort_direction, $sort_data, $depth_value, $depth_operator, $total_count, $field_filter_identifier, $field_filter_operator, $field_filter_value ) ;
    }
}