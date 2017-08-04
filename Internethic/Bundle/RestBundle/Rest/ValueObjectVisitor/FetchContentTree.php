<?php
namespace Internethic\Bundle\RestBundle\Rest\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\Visitor;

class FetchContentTree extends ValueObjectVisitor
{
    public function visit( Visitor $visitor, Generator $generator, $data )
    {
        $generator->startObjectElement( 'FetchContentTree' ) ;
        $visitor->setHeader( 'Content-Type', $generator->getMediaType( 'FetchContentTree' ) ) ;
        $visitor->setHeader( 'Accept-Patch', false ) ;
        $offset = $data->offset ;
        $limit = $data->limit ;
        $sort_target = $data->sort_target ;
        $sort_direction = $data->sort_direction ;
        $sort_data = http_build_query( array( 'sort_data' => $data->sort_data ) ) ;
        $depth_value = $data->depth_value ;
        $depth_operator = $data->depth_operator ;
        $type = http_build_query( array( 'type' => $data->type ) ) ;
        $field_filter_identifier = $data->field_filter_identifier ;
        $field_filter_operator = $data->field_filter_operator ;
        $field_filter_value = $data->field_filter_value ;
        // todo : delegate get parameter url build to a service
        $generator->startAttribute( 'href', $this->router->generate( 'ezpublish_rest_fetch_content_tree', array( 'locationId' => $data->locationId ) ) . "?offset=$offset&limit=$limit&$type&sort_target=$sort_target&sort_direction=$sort_direction&$sort_data&depth_value=$depth_value&depth_operator=$depth_operator&field_filter_identifier=$field_filter_identifier&field_filter_operator=$field_filter_operator&field_filter_value=$field_filter_value" ) ;
        $generator->endAttribute( 'href' ) ;
        $generator->startAttribute( 'locationId', $data->locationId ) ;
        $generator->endAttribute( 'locationId' ) ;
        $generator->startAttribute( 'type', $data->type ) ;
        $generator->endAttribute( 'type' ) ;
        $generator->startAttribute( 'offset', $offset ) ;
        $generator->endAttribute( 'offset' ) ;
        $generator->startAttribute( 'limit', $limit ) ;
        $generator->endAttribute( 'limit' ) ;
        $generator->startAttribute( 'sort_target', $sort_target ) ;
        $generator->endAttribute( 'sort_target' ) ;
        $generator->startAttribute( 'sort_direction', $sort_direction ) ;
        $generator->endAttribute( 'sort_direction' ) ;
        $generator->startAttribute( 'sort_data', $data->sort_data ) ;
        $generator->endAttribute( 'sort_data' ) ;
        $generator->startAttribute( 'depth_value', $depth_value ) ;
        $generator->endAttribute( 'depth_value' ) ;
        $generator->startAttribute( 'depth_operator', $depth_operator ) ;
        $generator->endAttribute( 'depth_operator' ) ;
        $generator->startAttribute( 'field_filter_identifier', $field_filter_identifier ) ;
        $generator->endAttribute( 'field_filter_identifier' ) ;
        $generator->startAttribute( 'field_filter_operator', $field_filter_operator ) ;
        $generator->endAttribute( 'field_filter_operator' ) ;
        $generator->startAttribute( 'field_filter_value', $field_filter_value ) ;
        $generator->endAttribute( 'field_filter_value' ) ;
        $generator->startAttribute( 'count', count( $data->contents ) ) ;
        $generator->endAttribute( 'count' ) ;
        $generator->startAttribute( 'total_count', $data->total_count ) ;
        $generator->endAttribute( 'total_count' ) ;
        $generator->startList( 'ContentList' ) ;
        foreach ( $data->contents as $content ) {
            $visitor->visitValueObject( $content ) ;
        }
        $generator->endList( 'ContentList' ) ;
        $generator->endObjectElement( 'FetchContentTree' ) ;
    }
}