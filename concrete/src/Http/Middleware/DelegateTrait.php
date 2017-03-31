<?php

namespace Concrete\Core\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;

trait DelegateTrait
{

    /**
     * Pass the ->next call onto $this->process
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function next(Request $request)
    {
        if (!$this instanceof DelegateInterface) {
            throw new \RuntimeException('This class must implement DelegateInterface');
        }

        return $this->process($request);
    }

}
