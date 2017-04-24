<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php;

interface AnnotatedObjectInterface
{
    /**
     * @return mixed
     */
    public function getAnnotation();

    /**
     * @param string $annotation
     * @return mixed
     */
    public function setAnnotation(string $annotation);
}
