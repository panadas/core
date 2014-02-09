<?php
namespace Panadas\Framework\Action;

use Panadas\HttpMessage\Request;
use Panadas\HttpMessage\Response;

class Homepage extends AbstractAction
{

    protected function get(Request $request)
    {
        return Response::create()->setContent(__METHOD__);
    }
}
