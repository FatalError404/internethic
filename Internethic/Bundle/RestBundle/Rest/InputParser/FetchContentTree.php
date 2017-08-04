<?php
namespace Internethic\Bundle\RestBundle\Rest\InputParser;

use eZ\Publish\Core\REST\Common\Input\BaseParser;
use eZ\Publish\Core\REST\Common\Input\ParsingDispatcher;
use Internethic\Bundle\RestBundle\Rest\Values\FetchContentTree as FetchContentTreeValue ;
use eZ\Publish\Core\REST\Common\Exceptions;


class FetchContentTree extends BaseParser
{
    /**
     * @return Internethic\Bundle\RestBundle\Rest\Values\FetchContentTree
     */
    public function parse( array $data, ParsingDispatcher $parsingDispatcher )
    {
        // re-using the REST exceptions will make sure that those already have a ValueObjectVisitor
        if ( !isset( $data['FetchContentTree'] ) )
            throw new Exceptions\Parser( "Missing or invalid 'FetchContentTree' element for FetchContentTree." );
        return new ContentListByTypeValue( $data['FetchContentTree'] );
    }
}