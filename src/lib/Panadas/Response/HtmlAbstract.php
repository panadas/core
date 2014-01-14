<?php
namespace Panadas\Response;

abstract class HtmlAbstract extends \Panadas\ResponseAbstract
{

    private $title;

    public function __construct(\Panadas\App $app, $encoding = "UTF-8")
    {
        parent::__construct($app, $encoding);

        $this->setContentType("text/html");
    }

    public function __toArray()
    {
        return (
            parent::__toArray()
            + [
	            "title" => $this->getTitle()
            ]
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function hasTitle()
    {
        return !is_null($this->getTitle());
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function removeTitle()
    {
        return $this->setTitle(null);
    }

}
