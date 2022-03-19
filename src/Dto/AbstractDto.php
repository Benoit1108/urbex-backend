<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Axel Brionne
 */
abstract class AbstractDto
{

    private Request $request;
    private bool $isBuild;

    public function __construct(RequestStack $requestStack)
    {
        $this->isBuild = false;
        $this->request = $requestStack->getCurrentRequest();
        if (!empty($this->getRequestContent())) {
            $this->buildByRequest();
            $this->isBuild = true;
        }
    }

    /**
     * Return an array of request data
     * @return array
     */
    protected function getRequestContent(): array
    {
        $object = [];
        if ($this->request->request->count() !== 0 || $this->request->getContent() !== '') {
            if ($this->request->request->count() === 0) {
                $object = json_decode($this->request->getContent(), true);
            } else {
                $object = $this->request->request->all();
            }
        }
        return $object;
    }

    /**
     * @return bool 
     * @Ignore()
     */
    public function isBuild(): bool
    {
        return $this->isBuild;
    }

    protected abstract function buildByRequest(): void;
}
