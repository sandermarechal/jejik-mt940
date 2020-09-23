# Upgrading Jejik/MT940

This document list backwards-incompatible changes only.

## 0.5
### 0.5.4
* added support for RDR fields in transactions.

### 0.5.3
* typo fix

### 0.5.2
* correct the check if 28C is null or is zero

### 0.5.1
* corrected getIBAN and getBIC

## 0.3 to 0.4

* Upgraded from Php 5.X to minimum Php 7.1

## 0.2 to 0.3

* The `statementDelimiter` for parsers was dropped in favour of a more reliable
  statement splitter in the `AbstractParser` base class. Custom parsers should no
  longer use the `statementDelimiter`. If the abstract parser does not properly
  split the statements in your MT940 documents, override the `splitStatements`
  method instead.
* The `contraAccount()` method for parsers should now return an object implementing
  `AccountInterface`. The easiest way to update your custom parsers is renaming the
  `contraAccount()` method to `contraAccountNumber()`.
* The `getAccount()` method on a `Statement` now returns an object implementing
  `AccountInterface` instead of a string.
* The `getContraAccount()` method on a `Transaction` now returns an object implementing
  `AccountInterface` instead of a string.

## 0.1 to 0.2

* ING only provides book dates, not valuation dates. In 0.1.x this was parsed
  incorrectly so the book date ended up in the `valueDate` field and the `bookDate`
  field remained empty. This has been fixed.
