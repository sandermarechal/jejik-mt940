<?php

namespace Jejik\Tests\MT940\Parser;

use Jejik\MT940\Account;
use Jejik\MT940\Balance;
use Jejik\MT940\Parser\DeutscheBank;
use Jejik\MT940\Reader;
use Jejik\MT940\Statement;
use Jejik\MT940\Transaction;

class GermanBankTest extends \PHPUnit\Framework\TestCase
{
    public function erefParserDataProvider(): array
    {
        return [
            'PC-343@PC: Test Case 1' => [
                'expected' => 'A17102018.1000253.1000104.108709',
                'statement' => ':20:DEUTDEMMXXX
:25:70070010/300188000
:28C:00001/1
:60F:C000000EUR0,00
:61:1810231023D99,00NRTINONREF//97186/030                       
:86:109?00SEPA-DD SOLL RUECKBEL. CORE?1097186?20EREF+A17102018.10
00253.?211000104.108709?22MREF+crmitest-1000253-1000104-1?23CRED+DE74ZZZ0
0001117144?24KREF+SEPA-20181017122054-?2527079600-P1?30COBADEFFXXX
?31DE34200400612345533292?32Collab 45
?34912    
:62F:C180614EUR0,00
-'
            ],
            'PC-343@PC: Test Case 2' => [
                'expected' => 'SEPA-ABC678',
                'statement' => ':20:DEUTDEMMXXX
:25:70070010/300188000
:28C:00001/1
:60F:C000000EUR0,00
:61:1810231023C569,00 NTRFNONREF
:86:020?00SEPA-GUTSCHRIFT?109075/611?20EREF+A11062018.4.?21SVWZ+Vertrag Ohne UmlAute?30GENODE6XXXX?31DE34200400444445555552?32OHNE UMLaUTE
:61:1810231023C186,00 NTRFNONREF
:86:166?00SEPA-GUTSCHRIFT?109075/611?20EREF+SEPA-ABC123?21SVWZ+Mit Sonderzeichen äÄöÖüÜ ß TÄÄÄST?30GENODE6XXXX?31DE35664900001234123456?32VORNAME TÄST
:61:1810231023C585,00 NTRFNONREF
:86:166?00SEPA-GUTSCHRIFT?109075/611?20EREF+SEPA-ABC678?21SVWZ+Mit Scharfen ß?30GENODE6XXXX?31DE35664900008888654321?32Gaß Irgendwas
:62F:C180614EUR0,00
-'
            ],
            'PC-343@PC: PC-420 Additional Fix' => [
                'expected' => 'A17102018.1000253.1000104.108709',
                'statement' => ':20:DEUTDEMMXXX
:25:70070010/300188000
:28C:00001/1
:60F:C000000EUR0,00
:61:1810231023D99,00NRTINONREF//97186/030                      
:86:109
?00SEPA-DD SOLL RUECKBEL. CORE
?1097186
?20EREF+A17102018.1000253.
?211000104.108709
?22MREF+crmitest-1000253-1000104-1
?23CRED+DE74ZZZ00001117144
?24KREF+SEPA-20181017122054-
?2527079600-P1?COBADEFFXXX
?31DE34200400612345533292
?32Collab 45
?34912
:62F:C180614EUR0,00
-'
            ],
            'PC-343@PC: Final Test' => [
                'expected' => 'SEPA-ABC6789',
                'statement' => ':20:STARTUMSE
:25:10010010/1111111111
:28C:00001/001
:60F:C120131EUR8200,90
:61:1202020102DR400,62N033NONREF
:86:166
?00SEPA-GUTSCHRIFT
?109075/611
?20EREF+SEPA-ABC123456
?21SVWZ+Mit Sonderzeichen äÄöÖüÜ ß TÄÄÄST
?30GENODE6XXXX
?31DE35432100001234123456
?32VORNAME TÄST
:61:1810211021C789,00 NTRFNONREF
:86:166
?00SEPA-GUTSCHRIFT
?109075/611
?20EREF+SEPA-ABC6789
?21SVWZ+Mit Scharfen ß
?30GENODE6XXXX
?31DE35123900008888654321
?32Gaß Irgendwas
:62F:C180614EUR0,00
-'
            ],
            'PCK-161@PC: Final Test' => [
                'expected' => '12345678901234 S1234567891013',
                'statement' => ':20:STARTUMSE
:25:10010010/1111111111
:28C:00001/001
:60F:C120131EUR8200,90
:61:1202020102DR400,62N033NONREF
:86:166?00Transfer?101234?20Kd.-Nr 123456 S123456789101?213?22EREF+12
345678901234 S123456?237891013?30Lorem ipsum
-',
            ],
            'CA-1614@CA: Colon in statement' => [
                'expected' => 'A24052019.1234.4321.53124',
                'statement' => ':20:STARTUMS
:25:11111111/22222222
:28C:19009/00001
:60F:C190603EUR82,68
:61:1905290603DR5,30NRTINONREF
:86:109?00Retouren
?10531
?20EREF+A24052019.1234.4321.53124
?22MREF+TEST-1234-1234-1
?23OAMT+1,30
?24COAM+4,00
?25SVWZ+Retoure SEPA Lastschrift vom 29.05.2019, Rueckgabegrund: MD06 Lastschriftwiderspruch durch den Zahlungspflichtigen SVWZ: RETURN/REFUND, E-Mobility Abrechnung Nr.X zu Vertrag 1234
?30ABCDEFXXX
?31DE12345678901234567890
?32Stadtwerke 
?34912
:62F:C190603EUR82,68',
            ],
            'CA-1733@CA: Valid Statement' => [
                'expected' => 'STZV-EtE06042015-1113-1',
                'statement' => ':20:STARTUMS
:25:74061813/0100033626
:28C:15001/00001
:60F:C141201EUR21,68
:61:1504070407C0,12NMSCNONREF
:86:166?00GUTSCHRIFT?105699
?20EREF+STZV-EtE06042015-1113-?212
?22SVWZ+Zweite SEPA-Ueberweisu?23ng EREF: STZV-EtE06042015-1
?24113-2 IBAN: DE1474061813000?250033626 BIC: GENODEF1PFK AB
?26WE: Test?27ABWE+Test
?30GENODEF1PFK?31DE14740618130000033626
?32Schliffenbacher Josef
:61:1504070407C0,50NMSCNONREF
:86:166?00GUTSCHRIFT?105699
?20EREF+STZV-EtE06042015-1113-?211
?22SVWZ+Verwendungszweck EREF:?23 STZV-EtE06042015-1113-1 IB
?24AN: DE14740618130000033626 ?25BIC: GENODEF1PFK ABWE: Test
?26konto 2?27ABWE+Testkonto 2
?30GENODEF1PFK?31DE14740618130000033626
?32Schliffenbacher Josef
:62M:C150407EUR22,30
-',
            ],
            'CA-1733@CA: Missing EREF' => [
                'expected' => null,
                'statement' => ':20:STARTUMSE
:25:10010010/1111111111
:28C:00001/001
:60F:C120131EUR8200,90
:61:1202020102DR400,62N033NONREF
:86:077?00Überweisung beleglos?109310?20RECHNUNGSNR. 1210815 ?21K
UNDENNR. 01234 ?22DATUM 01.02.2012?3020020020?2222222222?32MARTHA 
MUELLER?34999
:61:1202030103DR1210,00N012NONREF
:86:008?00Dauerauftrag?107000?20MIETE GOETHESTR. 12?3030030030?31
3333333333?32ABC IMMOBILIEN GMBH?34997
:61:1202030103CR30,00N062NONREF
:86:051?00Überweisungseingang?109265?20RECHNUNG 20120188?21STEFAN
 SCHMIDT?23KUNDENR. 4711,?3040040040?4444444444?32STEFAN SCHMIDT
:61:1202030103CR89,97N060NONREF//000000000001
:86:052?00Überweisungseingang?109265?20RECHNUNG 20120165?21PETER
 PETERSEN?3050050050?315555555555?32PETER PETERSEN 
:62F:C120203EUR6710,50
-',
            ],
            'CA-1733@CA: Multiline EREF' => [
                'expected' => 'A11111111.2222.3333.44444',
                'statement' => ':20:STARTUMS
:25:11111111/22222
:28C:19018/00001
:60F:C190912EUR102,60
:61:1909090912DR5,50NRTINONREF
:86:109?00Retouren
?10531
?20EREF+A11111111.2222.3333.44
?21444
?22MREF+TEST-1111-2222-1
?23OAMT+1,50
?24COAM+4,00
?25SVWZ+Retoure SEPA Lastschri
?26ft vom 01.01.1970, Rueckgab
?27egrund: MD06 Lastschriftwid
?28erspruch durch den Zahlungs
?29pflichtigen SVWZ: RETURN/RE
?60FUND, E-Mobility Abrechnung
?61 Nr. X zu Vertrag Y, Kun
?30ABCDEF5FXXX
?31DE48513500250200510002
?32Stadtwerke 
?34912
:62F:C190912EUR97,10
-'
            ]
        ];
    }

    /**
     * @dataProvider erefParserDataProvider
     *
     * @param string $expected
     * @param string $statement
     * @throws \Exception
     */
    public function testErefParser(
        $expected,
        string $statement
    ) {
        $transaction = new Transaction();

        $accountMock = $this->getMockBuilder(Account::class)->getMock();
        $readerMock = $this->getMockBuilder(Reader::class)->getMock();

        $readerMock->method('createAccount')->willReturn($accountMock);
        $readerMock->method('createStatement')->willReturn(new Statement());
        $readerMock->method('createOpeningBalance')->willReturn(new Balance());
        $readerMock->method('createClosingBalance')->willReturn(new Balance());
        $readerMock->method('createTransaction')->willReturn($transaction);

        // Use DeutscheBank class to test GermanBank
        $sut = new DeutscheBank($readerMock);
        $sut->parse($statement);
        $this->assertSame($expected, $transaction->getEref());

        /*
         * TODO:
         *  - $this->assertSame($expected, $transaction->getKref());
         *  - $this->assertSame($expected, $transaction->getSvwz());
         *  - ...
         */
    }
}
