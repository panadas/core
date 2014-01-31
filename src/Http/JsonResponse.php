<?php
namespace Panadas\Http;

class JsonResponse extends \Panadas\Http\Response
{

    /**
     * @param \Panadas\Kernel\Kernel $kernel
     * @param string                 $charset
     * @param array                  $headers
     * @param mixed                  $data
     */
    public function __construct(\Panadas\Kernel\Kernel $kernel, $charset = null, array $headers = [], $data = null)
    {
        parent::__construct($kernel, $charset, $headers);

        $this
            ->setContentType("application/json")
            ->setData($data);
    }

    /**
     * @param  integer $options
     * @param  integer $depth
     * @return mixed
     */
    public function getData($options = null, $depth = 512)
    {
        return $this->decode($this->getContent(), $options, $depth);
    }

    /**
     * @return boolean
     */
    public function hasData()
    {
        return $this->hasContent();
    }

    /**
     * @param  mixed $data
     * @param  integer $options
     * @param  integer $depth
     * @return \Panadas\Http\JsonResponse
     */
    public function setData($data, $options = null, $depth = 512)
    {
        if (null === $data) {
            return $this->removeContent();
        }

        return $this->setContent($this->encode($data, $options, $depth));
    }

    /**
     * @return \Panadas\Http\JsonResponse
     */
    public function removeData()
    {
        return $this->setData(null);
    }

    /**
     * @param  string $content
     * @throws \RuntimeException
     */
    public function prependContent($content)
    {
        throw new \RuntimeException("Cannot prepend to JSON content");
    }

    /**
     * @param  string $content
     * @throws \RuntimeException
     */
    public function appendContent($content)
    {
        throw new \RuntimeException("Cannot append to JSON content");
    }

    /**
     * @param  mixed   $content
     * @param  integer $options
     * @param  integer $depth
     * @return string
     */
    public function encode($content, $options = null, $depth = 512)
    {
        return json_encode($content, $options, $depth);
    }

    /**
     * @param  string  $content
     * @param  integer $options
     * @param  integer $depth
     * @return mixed
     */
    public function decode($content, $options = null, $depth = 512)
    {
        return json_decode($content, true, $depth, $options);
    }
}
