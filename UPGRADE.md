# Upgrading Jejik/MT940

This document list backwards-incompatible changes only.

## 0.2 to 0.3

* The `statementDelimiter` for parsers was dropped in favour of a more reliable
  statement splitter in the `AbstractParser` base class. Custom parsers should no
  longer use the `statementDelimiter`. If the abstract parser does not properly
  split the statements in your MT940 documents, override the `splitStatements`
  method instead.

## 0.1 to 0.2

* ING only provides book dates, not valuation dates. In 0.1.x this was parsed
  incorrectly so the book date ended up in the `valueDate` field and the `bookDate`
  field remained empty. This has been fixed.
