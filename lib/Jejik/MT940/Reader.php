<?php

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940;

/**
 * Read and parse MT940 documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class Reader
{
    // Properties {{{

    /**
     * @var array A class map of bank parsers
     */
    private $parsers = array();

    /**
     * @var array All the parsers shipped in this package
     */
    private $defaultParsers = array(
        'ABN-AMRO' => 'Jejik\MT940\Parser\AbnAmro',
        'ING'      => 'Jejik\MT940\Parser\Ing',
        'Rabobank' => 'Jejik\MT940\Parser\Rabobank',
        'Triodos'  => 'Jejik\MT940\Parser\Triodos'
    );

    // }}}

    // Parser management {{{

    /**
     * Get a list of default parsers shippen in this package
     *
     * @return array
     */
    public function getDefaultParsers()
    {
        return $this->defaultParsers;
    }

    /**
     * Get the current list of parsers
     *
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * Add a parser type to the list of parsers
     *
     * Some parsers can conflict with each other so order is important. Use
     * the $before parameter in insert a parser in a specific place.
     *
     * @param string $name Name of the parser
     * @param mixed $class Classname of the parser
     * @param mixed $before Insert the new parser before this parser
     * @return $this
     * @throws \RuntimeException if the $before parser does not exist
     */
    public function addParser($name, $class, $before = null)
    {
        if ($before === null) {
            $this->parsers[$name] = $class;
            return $this;
        }

        if ($offset = array_search($before, array_keys($this->parsers))) {
            array_splice($this->parsers, $offset, 0, array($name => $class));
            return $this;
        }

        throw new \RuntimeException(sprintf('Parser "%s" does not exist.', $before));
    }

    /**
     * Add multiple parsers in one step
     *
     * @param array $parsers Associative array of parser names and classes
     * @return $this
     */
    public function addParsers($parsers)
    {
        foreach ($parsers as $name => $class) {
            $this->addParser($name, $class);
        }

        return $this;
    }

    /**
     * Remove a parser
     *
     * @param string $name Parser to remove
     * @return $this
     */
    public function removeParser($name)
    {
        unset($this->parsers[$name]);
    }

    /**
     * Set the list of parsers
     *
     * @param array $parsers Associative array of 'name' => 'class'
     * @return $this
     */
    public function setParsers(array $parsers = array())
    {
        $this->parsers = $parsers;
        return $this;
    }

    // }}}

    /**
     * Get MT940 statements from the input text
     *
     * @param string $text
     * @return array An array of \Jejik\MT940\Statement
     * @throws \RuntimeException if no suitable parser is found
     */
    public function getStatements($text)
    {
        if (!$this->parsers) {
            $this->addParsers($this->getDefaultParsers());
        }

        foreach ($this->parsers as $class) {
            $parser = new $class();
            if ($parser->accept($text)) {
                return $parser->parse($text);
            }
        }

        throw new \RuntimeException('No suitable parser found.');
    }
}
