<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Factory\MatcherFactory;

final class PHPMatcher
{
    private $matcher;

    public function match($value, $pattern) : bool
    {
        $this->matcher = null;

        return $this->getMatcher()->match($value, $pattern);
    }

    /**
     * Returns backtrace from last matching.
     * When called before PHPMatcher::match() function it will return instance where Backtrace::isEmpty() will return true
     *
     * @return Backtrace
     */
    public function backtrace() : Backtrace
    {
        return $this->getMatcher()->backtrace();
    }

    /**
     * Returns error from last matching.
     * If last matching was successful this function will return null.
     *
     * @return string|null
     */
    public function error() : ?string
    {
        return $this->getMatcher()->getError();
    }

    private function getMatcher() : Matcher
    {
        if (null === $this->matcher) {
            $this->matcher = (new MatcherFactory())->createMatcher();
        }

        return $this->matcher;
    }
}
