<?php
namespace Panadas\Http;

interface DecoratedResponseInterface
{

    /**
     * @param  string $content
     * @return \Panadas\Http\Response
     */
    public function decorate($content);
}
