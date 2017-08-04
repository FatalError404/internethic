<?php

namespace Internethic\Bundle\RestBundle\Rest\Values ;

use eZ\Publish\Core\REST\Common\Value as RestValue;

class FetchContentTree extends RestValue
{
	/**
	 * @var \eZ\Publish\Core\REST\Server\Values\RestContent[]
	 */
    public $contents;
    /**
     * @var integer
     */
    public $locationId ;
    /**
     * @var array
     */
    public $type ;
    /**
     * @var integer
     */
    public $offset ;
    /**
     * @var integer
     */
    public $limit ;
    /**
     * @var string
     */
    public $sort_target ;
    /**
     * @var string
     */
    public $sort_direction ;
    /**
     * @var mixed
     */
    public $sort_data ;
    /**
     * @var integer
     */
    public $depth_value ;
    /**
     * @var string
     */
    public $depth_operator ;
    /**
     * @var integer
     */
    public $total_count ;
    /**
     * @var string
     */
    public $field_filter_identifier ;
    /**
     * @var string
     */
    public $field_filter_operator ;
    /**
     * @var string
     */
    public $field_filter_value ;
    /**
     * Construct.
     * @param \eZ\Publish\Core\REST\Server\Values\RestContent[] $contents
     * @param string $type
     */
    public function __construct( $contents, $locationId, $type, $offset, $limit, $sort_target, $sort_direction, $sort_data, $depth_value, $depth_operator, $total_count, $field_filter_identifier, $field_filter_operator, $field_filter_value )
    {
        $this->contents = $contents ;
        $this->locationId = $locationId ;
        $this->type = $type ;
        $this->offset = $offset ;
        $this->limit = $limit ;
        $this->sort_target = $sort_target ;
        $this->sort_direction = $sort_direction ;
        $this->sort_data = $sort_data ;
        $this->depth_value = $depth_value ;
        $this->depth_operator = $depth_operator ;
        $this->total_count = $total_count ;
        $this->field_filter_identifier = $field_filter_identifier ;
        $this->field_filter_operator = $field_filter_operator ;
        $this->field_filter_value = $field_filter_value ;
    }
}