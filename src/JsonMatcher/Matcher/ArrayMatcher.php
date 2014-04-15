<?php

namespace JsonMatcher\Matcher;

use JsonMatcher\Matcher\PropertyMatcher;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
class ArrayMatcher implements PropertyMatcher
{
    /**
     * @var Matcher\PropertyMatcher
     */
    private $propertyMatcher;

    public function __construct(PropertyMatcher $propertyMatcher)
    {
        $this->propertyMatcher = $propertyMatcher;
    }

    public function match($matcher, $pattern)
    {
        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $accessorBuilder->enableExceptionOnInvalidIndex();
        $accessor = $accessorBuilder->getPropertyAccessor();

        $paths = [];
        foreach ($matcher as $key => $element) {
            $path = sprintf("[%s]", $key);

            if (is_array($element)) {
                $this->buildPath($element, $path);
                continue;
            }

            $paths[] = $path;
        }

        foreach ($paths as $path) {
            $value = $accessor->getValue($matcher, $path);
            try {
                $patternValue = $accessor->getValue($pattern, $path);
            } catch (NoSuchIndexException $e) {
                return false;
            }

            if ($this->propertyMatcher->canMatch($patternValue)) {
                if (false === $this->propertyMatcher->match($value, $patternValue)) {
                    return false;
                }
            }

        }

        return true;
    }

    public function canMatch($pattern)
    {
        return is_array($pattern);
    }


    private function buildPath(array $array, $parentPath)
    {
        foreach ($array as $key => $element) {
            $path = sprintf("%s[%s]", $parentPath, $key);

            if (is_array($element)) {
                $this->buildPath($element, $path);
                continue;
            }

            $this->paths[] = $path;
        }
    }
}
