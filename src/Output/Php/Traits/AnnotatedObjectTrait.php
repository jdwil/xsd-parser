<?php
declare(strict_types=1);

namespace JDWil\Xsd\Output\Php\Traits;

trait AnnotatedObjectTrait
{
    /**
     * @var string
     */
    private $annotation;

    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation(string $annotation)
    {
        $this->annotation = $annotation;
    }
}
