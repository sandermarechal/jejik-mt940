<?php

declare(strict_types=1);

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
    /**
     * Is an absolute file name of bank statement which is to be parsed
     * @var string
     */
    private $fileName;

    /**
     * @var array A class map of bank parsers
     */
    private $parsers = [];

    /**
     * @var array All the parsers shipped in this package
     */
    private $defaultParsers = array(
        'ABN-AMRO'    => Parser\AbnAmro::class,
        'BayerischeLandesbank' => Parser\BayerischeLandesbank::class,
        'Commerzbank' => Parser\Commerzbank::class,
        'DeutscheBank' => Parser\DeutscheBank::class,
        'ING'         => Parser\Ing::class,
        'Knab'        => Parser\Knab::class,
        'LandesBankBerlin' => Parser\LandesBankBerlin::class,
        'LandesBankHessen' => Parser\LandesBankHessen::class,
        'Lbbw' => Parser\Lbbw::class,
        'NuaPayBank'  => Parser\NuaPayBank::class,
        'OldenburgischeLandesbank' => Parser\OldenburgischeLandesbank::class,
        'PostFinance' => Parser\PostFinance::class,
        'Rabobank'    => Parser\Rabobank::class,
        'Raiffeisen' => Parser\Raiffeisen::class,
        'RaiffeisenKaernten' => Parser\RaiffeisenKaernten::class,
        'Sns'         => Parser\Sns::class,
        'Solaris' => Parser\Solaris::class,
        'Sparkasse'   => Parser\Sparkasse::class,
//        'SpecificGermanBank'   => Parser\SpecificGermanBankParser::class, TODO
        'StarMoney'   => Parser\StarMoney::class,
        'Triodos'     => Parser\Triodos::class,
        'UniCreditBank' => Parser\UniCreditBank::class,
        'Bil'    => Parser\Bil::class,
        'Paribas' => Parser\Paribas::class,
    );

    /**
     * @see setStatementClass()
     * @var string|callable
     */
    private $statementClass = Statement::class;

    /**
     * @see setAccountClass()
     * @var string|callable
     */
    private $accountClass = Account::class;

    /**
     * @see setContraAccountClass()
     * @var string|callable
     */
    private $contraAccountClass = Account::class;

    /**
     * @see setTransactionClass()
     * @var string|callable
     */
    private $transactionClass = Transaction::class;

    /**
     * @see setOpeningBalanceClass()
     * @var string|callable
     */
    private $openingBalanceClass = Balance::class;

    /**
     * @see setClosingBalanceClass()
     * @var string|callable
     */
    private $closingBalanceClass = Balance::class;

    // }}}

    // Parser management {{{

    /**
     * Get bank statement file name for this parser
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Set bank statement file name for this parser
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get a list of default parsers shippen in this package
     */
    public function getDefaultParsers(): array
    {
        return $this->defaultParsers;
    }

    /**
     * Get the current list of parsers
     */
    public function getParsers(): array
    {
        $output_array = [];
        foreach ($this->parsers as $name => $parser) {
            $output_array[$name] = $parser[0]; // get the classname
        }
        return $output_array;
    }

    /**
     * Add a parser type to the list of parsers
     * - Some parsers can conflict with each other so order is important. Use
     * -- the $before parameter in insert a parser in a specific place.
     * @param string $name Name of the parser
     * @param mixed $class Classname of the parser
     * @param mixed $before Insert the new parser before this parser
     * @param array $arguments An array of arguments. Its elements will be passed as individual parameters to the
     *                         constructor of the parser.
     * @return $this
     * @throws \RuntimeException if the $before parser does not exist
     */
    public function addParser(string $name, $class, $before = null, $arguments = []): self
    {
        if ($before === null) {
            $this->parsers[$name] = [$class, $arguments];
            return $this;
        }

        $offset = array_search($before, array_keys($this->parsers));
        if ($offset !== false) {
            $this->parsers = array_slice($this->parsers, $offset, 0, true)
                + array($name => $class)
                + array_slice($this->parsers, $offset, null, true);
            return $this;
        }
        throw new \RuntimeException(sprintf('Parser "%s" does not exist.', $before));
    }

    /**
     * Add multiple parsers in one step
     *
     * @param array $parsers Associative array of parser names and classes
     */
    public function addParsers(array $parsers): self
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
     */
    public function removeParser(string $name): void
    {
        unset($this->parsers[$name]);
    }

    /**
     * Set the list of parsers
     *
     * @param array $parsers Associative array of 'name' => 'class'
     */
    public function setParsers(array $parsers = []): self
    {
        $this->parsers = array_map(function ($className) {
            return [$className, []];
        }, $parsers);
        return $this;
    }

    // }}}

    // Class factories {{{

    /**
     * Getter for statementClass
     *
     * @return string|callable
     */
    public function getStatementClass()
    {
        return $this->statementClass;
    }

    /**
     * Set the classname of the statement class or callable that returns an object that
     * implements the StatementInterface.
     *
     * The callable is passed the account object and statement sequence number
     * as parameters. Example:
     *
     * $reader->setStatementClass(function (AccountInterface $account, $number) {
     *     return new My\Statement();
     * });
     *
     * If the callable returns null, the statement is skipped.
     *
     * @param string|callable $statementClass
     */
    public function setStatementClass($statementClass): self
    {
        if (!is_callable($statementClass) && !class_exists($statementClass)) {
            throw new \InvalidArgumentException('$statementClass must be a valid classname or a PHP callable');
        }

        $this->statementClass = $statementClass;
        return $this;
    }

    /**
     * Create a Statement object
     *
     * @param AccountInterface $account Account number
     * @param string $number  Statement sequence number
     */
    public function createStatement(
        AccountInterface $account,
        string $number
    ): ?StatementInterface {
        return $this->createObject(
            $this->statementClass,
            StatementInterface::class,
            [$account, $number]
        );
    }

    /**
     * Getter for accountClass
     *
     * @return string|callable
     */
    public function getAccountClass()
    {
        return $this->accountClass;
    }

    /**
     * Set the classname of the account class or callable that returns an object that
     * implements the AccountInterface.
     *
     * The callable is passed the account number as a parameter. Example:
     *
     * $reader->setAccountClass(function ($accountNumber) {
     *     return new My\Account();
     * });
     *
     * If the callable returns null, statements for the account will be skipped.
     *
     * @param string|callable $accountClass
     */
    public function setAccountClass($accountClass): self
    {
        if (!is_callable($accountClass) && !class_exists($accountClass)) {
            throw new \InvalidArgumentException('$accountClass must be a valid classname or a PHP callable');
        }

        $this->accountClass = $accountClass;
        return $this;
    }

    /**
     * Create a Account object
     * @return AccountInterface
     */
    public function createAccount(string $accountNumber)
    {
        /** @var Account $object */
        $object = $this->createObject(
            $this->accountClass,
            AccountInterface::class,
            [$accountNumber]
        );

        if (!empty($accountNumber)) {
            $object->setNumber($accountNumber);
        }

        return $object;
    }

    /**
     * Getter for contraAccountClass
     *
     * @return string|callable
     */
    public function getContraAccountClass()
    {
        return $this->contraAccountClass;
    }

    /**
     * Set the classname of the contraAccount class or callable that returns an object that
     * implements the AccountInterface.
     *
     * The callable is passed the account number as a parameter. Example:
     *
     * $reader->setContraAccountClass(function ($accountNumber) {
     *     return new My\ContraAccount();
     * });
     *
     * @param string|callable $contraAccountClass
     */
    public function setContraAccountClass($contraAccountClass): self
    {
        if (!is_callable($contraAccountClass) && !class_exists($contraAccountClass)) {
            throw new \InvalidArgumentException('$contraAccountClass must be a valid classname or a PHP callable');
        }

        $this->contraAccountClass = $contraAccountClass;
        return $this;
    }

    /**
     * Create a ContraAccount object
     *
     * @param string|null $accountNumber Contra account number
     */
    public function createContraAccount(?string $accountNumber): AccountInterface
    {
        return $this->createObject(
            $this->contraAccountClass,
            AccountInterface::class,
            [$accountNumber]
        );
    }

    /**
     * Getter for transactionClass
     *
     * @return string|callable
     */
    public function getTransactionClass()
    {
        return $this->transactionClass;
    }

    /**
     * Set the classname of the transaction class or callable that returns an object that
     * implements the StatementInterface.
     *
     * The callable is not passed any arguments.
     *
     * $reader->setTransactionClass(function () {
     *     return new My\Transaction();
     * });
     *
     * @param string|callable $transactionClass
     */
    public function setTransactionClass($transactionClass): self
    {
        if (!is_callable($transactionClass) && !class_exists($transactionClass)) {
            throw new \InvalidArgumentException('$transactionClass must be a valid classname or a PHP callable');
        }

        $this->transactionClass = $transactionClass;
        return $this;
    }

    /**
     * Create a Transaction object
     */
    public function createTransaction(): TransactionInterface
    {
        return $this->createObject(
            $this->transactionClass,
            TransactionInterface::class
        );
    }

    /**
     * Getter for openingBalanceClass
     *
     * @return string|callable
     */
    public function getOpeningBalanceClass()
    {
        return $this->openingBalanceClass;
    }

    /**
     * Set the classname of the opening balance class or callable that returns an object that
     * implements the BalanceInterface.
     *
     * The callable is not passed any arguments.
     *
     * $reader->setOpeningBalanceClass(function () {
     *     return new My\Balance();
     * });
     *
     * @param string|callable $openingBalanceClass
     */
    public function setOpeningBalanceClass($openingBalanceClass): self
    {
        if (!is_callable($openingBalanceClass) && !class_exists($openingBalanceClass)) {
            throw new \InvalidArgumentException('$openingBalanceClass must be a valid classname or a PHP callable');
        }

        $this->openingBalanceClass = $openingBalanceClass;
        return $this;
    }

    /**
     * Create an opening balance object
     */
    public function createOpeningBalance(): BalanceInterface
    {
        return $this->createObject(
            $this->openingBalanceClass,
            BalanceInterface::class
        );
    }

    /**
     * Getter for closingBalanceClass
     *
     * @return string|callable
     */
    public function getClosingBalanceClass()
    {
        return $this->closingBalanceClass;
    }

    /**
     * Set the classname of the closing balance class or callable that returns an object that
     * implements the BalanceInterface.
     *
     * The callable is not passed any arguments.
     *
     * $reader->setClosingBalanceClass(function () {
     *     return new My\Balance();
     * });
     *
     * @param string|callable $closingBalanceClass
     */
    public function setClosingBalanceClass($closingBalanceClass): self
    {
        if (!is_callable($closingBalanceClass) && !class_exists($closingBalanceClass)) {
            throw new \InvalidArgumentException('$closingBalanceClass must be a valid classname or a PHP callable');
        }

        $this->closingBalanceClass = $closingBalanceClass;
        return $this;
    }

    /**
     * Create an closing balance object
     */
    public function createClosingBalance(): BalanceInterface
    {
        return $this->createObject(
            $this->closingBalanceClass,
            BalanceInterface::class
        );
    }

    /**
     * Create an object of a specified interface
     *
     * @param string|callable $className Classname or a callable that returns an object instance
     * @param mixed $interface The interface the class must implement //TODO mixed is a workaround for StdClass
     * @param array $params Parameters to pass to the callable
     *
     * @return Account|Statement|Balance An object that implements the interface
     */
    protected function createObject($className, $interface, $params = [])
    {
        if (is_string($className) && class_exists($className)) {
            $object = new $className();
        } elseif (is_callable($className)) {
            $object = call_user_func_array($className, $params);
        } else {
            throw new \InvalidArgumentException('$className must be a valid classname or a PHP callable');
        }

        if (null !== $object && !($object instanceof $interface)) {
            throw new \InvalidArgumentException(sprintf('%s must implement %s', get_class($object), $interface));
        }

        return $object;
    }

    /**
     * Get MT940 statements from the input text
     *
     * @param string $text
     * @return Statement[]
     * @throws \RuntimeException if no suitable parser is found
     * @throws Exception\NoParserFoundException
     * @throws \Exception
     */
    public function getStatements(string $text = null): array
    {
        if ($text === null) {
            $text = $this->removeBom(file_get_contents($this->getFileName()));
        }
        if ($text === null || strlen(trim($text)) == 0) {
            throw new \Exception("No text is found for parsing.");
        }
        if (($pos = strpos($text, ':20:')) === false) {
            throw new \RuntimeException('Not an MT940 statement');
        }
        if (preg_match_all('/^[\n\r\s]+/', $text, $output_array) > 0) {
            throw new \Exception('The first line cannot be a blank line.');
        }
        if (!$this->parsers) {
            $this->addParsers($this->getDefaultParsers());
        }

        foreach ($this->parsers as [$class, $additionalConstructorArgs]) {
            $parser = new $class($this, ...$additionalConstructorArgs);
            if ($parser->accept($text)) {
                return $parser->parse($text);
            }
        }

        throw new Exception\NoParserFoundException();
    }

    /**
     * @param $text
     * @return string|string[]|null
     */
    private function removeBom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}
