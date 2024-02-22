# 3.1.0
- Nieuwe België naar Nederland-producten toegevoegd voor verzend- en afhaalpunten
- Een ID/leeftijdscontroleoptie toegevoegd voor verzend- en afhaalpunten in Nederland
- Probleem verholpen waarbij de locatiecode van het afhaalpunt niet werd opgeslagen in de bestelling. Dit betrof alle 3.0 versies.
- Verhelpt een probleem waarbij bij het wijzigen van de verzendmethode in de administratie altijd NL werd geselecteerd als het land van de afzender
- Ondersteuning toegevoegd voor MariaDB-versies ouder dan 10.5.2
  - Als je al geprobeerd hebt de plugin te installeren op een oudere MariaDB versie, neem dan deze stappen om de plugin gegevens te verwijderen voordat je probeert de nieuwe versie te installeren:
    - Verwijder elke database tabel die begint met `postnl_` uit de database
    - Verwijder alle items uit de `migratie` database tabel waar het `klasse` veld begint met `PostNL\Shopware6`.
    - Optioneel: Verwijder eerst de oude plugin bestanden en ververs de plugin lijst in de admin

# 3.0.2
- Probleem verholpen waarbij geselecteerde afleverdata of afhaalpunten niet werden opgeslagen.
- Afleverdatum en verzenddatum weer toegevoegd aan de admin order detailpagina.

# 3.0.1
- Meerdere kleine bugfixes

# 3.0.0
#### Shopware compatibiliteit update
- Deze versie is compatibel met Shopware 6.5.2 en hoger.

#### Oplossingen
- Probleem verholpen waarbij geen standaardproduct werd geselecteerd tijdens het afrekenen.
- Probleem verholpen waarbij de verzenddatum en de gekozen leverdatum niet werden weergegeven in de administratie.

#### Bekende problemen
- In Shopware versies lager dan 6.5.5.0 zijn de PostNL iconen niet beschikbaar in de administratie. Dit is alleen een cosmetisch probleem en heeft geen invloed op de functionaliteit.

# 2.0.0
#### Nieuwe functies
- Bezorgdatumselectie toegevoegd in de checkout, inclusief avondlevering.
  - Configureer deze in de instellingen van de plugin.
- Nieuwe Europese en internationale verzendopties toegevoegd.
  - GlobalPack is vervangen.

# 1.2.3
- Lost een probleem op waarbij niet-verplichte velden verplicht worden bij het selecteren van een ander land dan Nederland tijdens de registratie.
- Een bug waarbij bepaalde kaarten niet worden getoond op de order detail pagina in de administratie bij het openen van een niet-PostNL order is verholpen.

# 1.2.2
- Lost een probleem op met adressen wanneer het verzendadres verschilt van het factuuradres.
- Lost een probleem op waarbij e-mails niet konden worden verzonden als er geen bestelgegevens waren (bijv. wachtwoordherstel)

# 1.2.1
- Lost een probleem op met custom fields op het geselecteerde verzendadres (Met dank aan Mitchel van Vliet en Robbert de Smit @ DutchDrops)

# 1.2.0
- Adressen van afhaalpunten worden nu opgeslagen als verzendadres bij het selecteren van een afhaalpunt
  - Het oorspronkelijk geselecteerde afleveradres is nog steeds beschikbaar op de bestelling
# 1.1.0
#### Belgische release
- Productcodes voor het verzenden vanuit België zijn toegevoegd.

#### Bugfixes
- Lost een probleem op waarbij, na het invoeren van een ongeldig Nederlands adres en vervolgens overschakelen naar een ander land, de klantenregistratie niet kon worden voltooid.

# 1.0.0
#### Eerste release
- Gemakkelijk zendingen aanmelden bij PostNL.
- Eenvoudig de verzendlabels afdrukken.
- Gebruik één van de vele verzendmethodes van PostNL (o.a. brievenbuspakje, verzekerd verzenden).
- Verstuur je pakketten gemakkelijk naar België, Europa en de rest van de wereld.
- Adresvalidatie voor Nederlandse adressen.
- Laat jouw klanten kiezen of zij het pakket thuis willen ontvangen of ophalen bij een PostNL-punt in de buurt.
- Deel eenvoudig het retourlabel met jouw klanten.
- Kies in welk formaat verzendlabel geprint worden (A4 of A6).
- Activeer alternatieve verzendmethode boven een bepaald orderbedrag.
