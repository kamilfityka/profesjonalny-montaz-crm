<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Enums\DocumentFormat;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    private array $data = [
        'Reklamacja' => '<p style="text-align: center;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src="https://start3.cmvo.app/zalaczniki/logo superhurtownia_1.jpg" style="width: 230px; height: 115px;" />&nbsp; &nbsp; &nbsp; &nbsp;<em>pieczęć punktu</em></p>

<p style="text-align: center;">DRUK ZGŁOSZENIA REKLAMACJI/SERWISU</p>

<table border="1" cellpadding="5" cellspacing="0" style="width: 700px;font-size:12px">
	<tbody>
		<tr>
			<td style="width: 50px; text-align: center;">1</td>
			<td>Dane zgłaszającego<br />
			(imię i nazwisko/nazwa firmy, adres)</td>
			<td>Firma Morstenne</td>
		</tr>
		<tr>
			<td style="text-align: center;">2</td>
			<td>Telefon</td>
			<td>0048 234 123 1236</td>
		</tr>
		<tr>
			<td style="text-align: center;">3</td>
			<td>Adres e-mail</td>
			<td>reklamacje@firma_morstenne.com</td>
		</tr>
		<tr>
			<td style="text-align: center;">4</td>
			<td>Data powstania/ujawnienia wady</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">5</td>
			<td>Numer zam&oacute;wienia (nadany salonowi<br />
			sprzedaży); nr oferty z zam&oacute;wienia</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">6</td>
			<td>Pozycja z zam&oacute;wienia; ilość sztuk; strona:<br />
			lewa/prawa; wymiary pozycji</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">7</td>
			<td>Data zakupu</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">8</td>
			<td>Miejsce zakupu (adres salonu sprzedaży)</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">9</td>
			<td>Data i miejsce montażu<br />
			(dokładny adres, telefon kontaktowy)</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align: center;">10</td>
			<td>Rodzaj wady<br />
			(podkreślić właściwe)</td>
			<td>Produkcyjna, montażowa, materiałowa</td>
		</tr>
	</tbody>
</table>

<p style="font-size:12px">12.Dokładny opis zgłoszenia:<br />
&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</p>

<p style="font-size:12px"><br />
13.Ilość dodanych załącznik&oacute;w/ zdjęć/ innych: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</p>

<p style="font-size:11px;text-align: right;">&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.</p>

<p style="font-size:12px;text-align: right;">(Czytelny podpis osoby zgłaszającej usterkę)</p>

<p style="font-size:12px"><strong>Uwaga: <span style="color:red">Wypełnić obowiązkowo wszystkie punkty zgłoszenia- w innym przypadku rozpatrzenie reklamacji może być utrudnione oraz wydłużone. Po dostarczeniu w pełni wypełnionego zgłoszenia reklamacji oraz załączeniu zdjęć opisanych wad producent rozpatrzy zgłoszenie w ciągu 14 dni od daty otrzymania niniejszego dokumentu.</span></strong></p>

<p style="font-size:11px">Og&oacute;lne warunki składania i uznania reklamacji Klienta</p>

<ol style="font-size:11px">
	<li>Reklamacja winna być złożona wyłącznie na piśmie i wysłana/dostarczona na adres siedziby Twoja Superhurtownia (fax, e- mail, list)</li>
	<li>Reklamacja obejmuje wyłącznie wady powstałe z przyczyn tkwiących w zakupionym towarze, pod warunkiem przestrzegania przez klienta prawidłowych zasad użytkowania towaru, określonych w Warunkach Gwarancji, załączonych do zam&oacute;wienia.</li>
	<li>Sprzedający zobowiązuje się do rozpatrzenia reklamacji w ciągu 14 dni roboczych od dnia jej zgłoszenia oraz poinformowania Kupującego o wyniku rozparzenia reklamacji. W przypadkach skomplikowanych i złożonych termin ten może wydłużyć się do 30 dni roboczych, o czym, producent poinformuje Klienta.</li>
	<li>Prawo do składania przysługuje jedynie w okresie ochrony gwarancyjnej, wyznaczonym w Karcie Gwarancyjnej produktu.</li>
	<li>Niespełnienie powyższych warunk&oacute;w będzie skutkować odrzuceniem reklamacji.</li>
	<li>Koszty związane z nieuzasadnioną reklamacją pokrywa klient (3,60 zł/km + VAT dojazdu liczonego w obie strony oraz 100 zł/rg +VAT za każdą rozpoczęta godzinę pracy serwisanta oraz koszty użytych materiał&oacute;w).</li>
	<li>Koszty płatnego serwisu jak wyżej.</li>
</ol>',
        'Umowa' => '<table border="0" cellpadding="1" cellspacing="1" style="width: 700px;">
	<tbody>
		<tr>
			<td style="vertical-align: top;"><font size="3"><b><img alt="" src="https://start.cmvo.pl/zalaczniki/logo_morstenne_1.png" style="width:100px; height:59px" /></b></font></td>
			<td style="vertical-align: top;">
			<p><font size="3"><b>Morstenne Sp. z o.o.&nbsp;</b></font></p>

			<p><font size="3"><b>ul. Przykładowa 42</b></font></p>

			<p><font size="3"><b>52-100 Warszawa</b></font></p>
			</td>
			<td style="vertical-align: top;"><font size="3"><b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; E-mail: biuro@morstenne.pl</b></font></td>
		</tr>
	</tbody>
</table>

<p><font size="3"><b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</b></font></p>

<p>&nbsp;</p>

<h2>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; UMOWA<br />
<br />
<font size="2">Zawarta w dniu ......pomiędzy:</font></h2>

<p><font size="2">Morstenne Sp z o.o., reprezentowanym przez Adama Zaciętego, zwanym dalej Wykonawcą przedmiotu umowy,</font></p>

<p><font size="2">a .................................................................................................................................................................................................................................................................................................... </font></p>

<p><font size="2">zwanym dalej Zamawiającym.</font></p>

<p>&nbsp;</p>

<ol>
	<li>
	<p><font size="2">Zamawiający na podstawie złożonej oferty powierza, a Wykonawca przyjmuje do wykonania następujący zakres prac: .........................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................</font></p>
	</li>
	<li>
	<p><font size="2">Wynagrodzenie za wykonanie przedmiotu umowy wynosi: ......................................</font></p>
	</li>
</ol>

<p><font size="2">plus podatek VAT: .......................................... </font></p>

<p><font size="2">łącznie wartość wykonania usługi z podatkiem VAT: ................................................. </font></p>

<p><font size="2">słownie ................................................................................................................................</font></p>

<ol start="3">
	<li>
	<p><font size="2">Ewentualna zmiana zakresu umowy i wynagrodzenia musi być dokonana aneksem do umowy.</font></p>
	</li>
	<li>
	<p><font size="2">Planowany termin wykonania Umowy ........................................ . Termin ten może ulec zmianie w przypadku przesunięcia terminu dostawy przez producenta stolarki.</font></p>
	</li>
	<li>
	<p><font size="2">Zamawiający zobowiązany jest do dokonania odbioru ilościowego i jakościowego wyrob&oacute;w przed ich montażem.</font></p>
	</li>
	<li>
	<p><font size="2">Przy podpisaniu Umowy ustalono ..........................................................................................................................................................................................................................................................................................................................................................................</font></p>
	</li>
	<li>
	<p><font size="2">Pozostała kwota ........................................ zapłacona zostanie got&oacute;wką po wykonaniu usługi i złożeniu faktury.</font></p>
	</li>
</ol>

<p><font size="2">8. Umowa została sporządzona w 2 jednobrzmiących egzemplarzach.</font></p>

<p>&nbsp;</p>

<p><font size="2">Wykonawca Zamawiający</font></p>

<p>&nbsp;</p>

<p>&nbsp;</p>',
        'Wniosek pozwolenia na budowę' => '<p style="text-align: right;">................................................, dnia ................... r.<br />
<em>(miejscowość, data)</em></p>

<p style="text-align: right;">.............................................................................................</p>

<p style="text-align: right;">.............................................................................................</p>

<p style="text-align: right;">.............................................................................................<br />
<em>(nr rejestru organu właściwego do wydania pozwolenia)</em></p>

<p>&nbsp;</p>

<p style="text-align: center;"><strong>Wniosek o pozwolenie na budowę</strong></p>

<p>..........................................................................................................................................................................................</p>

<p><em>(nazwa organu właściwego do wydania pozwolenia)</em></p>

<p>Inwestor:</p>

<p>..........................................................................................................................................................................................</p>

<p><em>(imię i nazwisko lub nazwa oraz adres)</em></p>

<p>na podstawie art. 32 i 33 ustawy z dnia 7 lipca 1994 r. &ndash; Prawo budowlane (Dz. U. z 2000 r. Nr 106, poz. 1126, z p&oacute;źn. zm.) wnoszę o wydanie decyzji o pozwoleniu nabudowę:</p>

<p>..........................................................................................................................................................................................</p>

<p><em>(nazwa i rodzaj oraz adres całego zamierzenia budowlanego, rodzaj/-e obiektu/-&oacute;w bądź rob&oacute;t budowlanych,oznaczenie działki ewidencyjnej wg ewidencji grunt&oacute;w i budynk&oacute;w poprzez określenie obrębu ewidencyjnego oraz numeru działki ewidencyjnej)</em></p>

<p>&nbsp;</p>

<p>Do wniosku o pozwolenie na budowę dołączam:</p>

<ol>
	<li>cztery egzemplarze projektu budowlanego wraz z opiniami, uzgodnieniami, pozwoleniami i innymi dokumentami wymaganymi przepisami szczeg&oacute;lnymi oraz zaświadczeniem, o kt&oacute;rym mowa w art. 12 ust. 7 ustawy - Prawo budowlane,</li>
	<li>oświadczenie o posiadanym prawie do dysponowania nieruchomością na cele budowlane,</li>
	<li>decyzję o warunkach zabudowy i zagospodarowania terenu, jeżeli jest ona wymagana zgodnie z przepisami ustawy o planowaniu i zagospodarowaniu przestrzennym,</li>
	<li>specjalistyczną opinię, o kt&oacute;rej mowa w art. 33 ust. 3 ustawy - Prawo budowlane,</li>
	<li>postanowienie o uzgodnieniu, z właściwym organem administracji architektoniczno-budowlanej, projektowanych rozwiązań w zakresie, o kt&oacute;rym mowa w art. 33 ust. 2 pkt 4ustawy - Prawo budowlane,</li>
	<li>upoważnienie udzielone osobie działającej w moim imieniu</li>
</ol>

<p style="text-align: right;">.....................................................................</p>

<p style="text-align: right;"><em>(podpis inwestora lub osoby przez niego upoważnionej)</em></p>',
        'Wypowiedzenie umowy najmu' => '<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;, dnia &hellip;&hellip;&hellip;&hellip;&hellip; r.</p>

<p><em>(miejscowość, data)</em></p>

<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;</p>

<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</p>

<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</p>

<p><em><em><em><em><em><em><em><em>(dane Wynajmującego)</em></em></em></em></em></em></em></em></p>

<p><em><em>Do</em></em></p>

<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;</p>

<p>&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</p>

<p>&hellip;&hellip;&hellip;.&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;...</p>

<p><em><em><em><em><em><em><em><em>(dane Najemcy)</em></em></em></em></em></em></em></em></p>

<p>&nbsp;</p>

<p><strong>Wypowiedzenie umowy najmu</strong></p>

<p>&nbsp;</p>

<p>Zgodnie z art. 685 Kodeksu cywilnego oraz na podstawie&nbsp;&sect;&hellip;.......... umowy najmu lokalu mieszkalnego zawartej dnia &hellip;...................... r. pomiędzy &hellip;.........................................................., a Panem/Panią* &hellip;........................................................&nbsp;wypowiadam umowę najmu lokalu mieszkalnego położonego w &hellip;................................. przy ul. &hellip;..................................................... ze skutkiem od dnia &hellip;......................... r.&nbsp;Zgodnie z ww. umową, Najemca od dnia doręczenia wypowiedzenia umowy najmu, ma &hellip;.. dni na opuszczenie lokalu i uregulowanie wszystkich zaległych zobowiązań wobec Wynajmującego.</p>

<p>Uzasadnienie</p>

<p>&hellip;..........................................................................................................................................</p>

<p>.............................................................................................................................................</p>

<p>.............................................................................................................................................</p>

<p>.............................................................................................................................................</p>

<p>&nbsp;</p>

<p>Niniejszy dokument został sporządzony w dw&oacute;ch identycznych egzemplarzach po jednym dla każdej ze stron</p>

<p>.&hellip;.....................................................</p>

<p><em><em><em><em><em>Wynajmując</em></em></em></em></em><em>y</em></p>'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new DocumentType())->newQuery()->truncate();

        $this->command->info('Creating Document Types ...');

        foreach($this->data as $name => $text) {
            (new DocumentType())->newQuery()->insert(['name' => $name, 'text' => $text]);
        }

        $this->command->info('');
    }
}
