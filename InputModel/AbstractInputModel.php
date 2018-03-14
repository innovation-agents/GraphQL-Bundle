<?php

namespace InnovationAgents\GraphQLBundle\InputModel;

abstract class AbstractInputModel implements InputModelInterface
{
    /** @var array */
    protected $data;

    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @param array $data
     * @return InputModelInterface
     */
    public static function create(array $data)
    {
        $self = new static();
        return $self->setData($data);
    }

    public function setData(array $data): InputModelInterface
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $key, $default=null)
    {
        return array_key_exists($key, $this->data)
            ? $this->data[$key]
            : $default;
    }

    public function getArray($key)
    {
        return $this->get($key, []);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getDate($key)
    {
        $date = $this->get($key);

        if(empty($date)) {
            return null;
        }

        if(!$date instanceof \DateTimeInterface) {
            $this->set($key, new \DateTime($date));
        }

        return $this->data[$key];
    }
}