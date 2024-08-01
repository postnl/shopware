# 4.0.1
- Behebung eines Problems bei der Neuinstallation des Plugins nach dem Update auf 4.x

# 4.0.0
#### Update der Shopware-Kompatibilität
- Diese Version ist kompatibel mit Shopware 6.6.0 und höher.

#### Neue Funktionen
- Es wurden zusätzliche Header zu den API-Aufrufen hinzugefügt. Dies ermöglicht uns eine bessere Unterstützung durch Überwachung der verwendeten Shopware- und Plugin-Versionen.

# 3.1.2
- Es wurde eine Inkompatibilität mit der Retourenverwaltung des Shopware Commercial Plugins entdeckt, die dazu führte, dass die PostNL Bestellübersicht nicht mehr funktionierte. Ein Workaround wurde implementiert
- (Minor) Fehlende Bilder in der Plugin-Konfiguration behoben

# 3.1.1
- Probleme bei der Bearbeitung von Bestellungen in der Verwaltung behoben

# 3.1.0
- Neue Produkte für Belgien zu den Niederlanden für Versand- und Abholstellen hinzugefügt
- Es wurde eine Option zur Überprüfung von ID/Alter für Versand- und Abholstellen in den Niederlanden hinzugefügt.
- Es wurde ein Problem behoben, bei dem der Code der Abholstelle nicht in der Bestellung gespeichert wurde. Dies betraf alle 3.0-Versionen.
- Behebt ein Problem, bei dem beim Ändern der Versandmethode in der Verwaltung immer NL als Land des Absenders ausgewählt wurde
- Unterstützung für MariaDB-Versionen älter als 10.5.2 wurde hinzugefügt.
  - Wenn Sie bereits versucht haben, das Plugin auf einer älteren MariaDB-Version zu installieren, führen Sie diese Schritte aus, um die Plugin-Daten zu entfernen, bevor Sie versuchen, die neue Version zu installieren:
    - Entfernen Sie alle Datenbanktabellen, die mit `postnl_` beginnen, aus der Datenbank
    - Entfernen Sie alle Einträge aus der Datenbanktabelle `migration`, bei denen das Feld `class` mit `PostNL\Shopware6` beginnt
    - Optional: Entfernen Sie zuerst die alten Plugin-Dateien und aktualisieren Sie die Plugin-Liste in der Verwaltung

# 3.0.2
- Behebung eines Problems, bei dem ausgewählte Lieferdaten oder Abholpunkte nicht gespeichert wurden.
- Das Lieferdatum und das Versanddatum wurden auf der administrativen Bestellungsdetailseite wieder hinzugefügt.

# 3.0.1
- Mehrere kleine Fehlerbehebungen

# 3.0.0
#### Update der Shopware-Kompatibilität
- Diese Version ist kompatibel mit Shopware 6.5.2 und höher.

#### Korrekturen
- Es wurde ein Problem behoben, bei dem während der Kaufabwicklung kein Standardprodukt ausgewählt wurde.
- Ein Problem wurde behoben, bei dem das Versanddatum und das gewählte Lieferdatum nicht in der Verwaltung angezeigt wurden.

#### Bekannte Probleme
- In Shopware-Versionen kleiner als 6.5.5.0 sind die PostNL-Symbole in der Administration nicht verfügbar. Dies ist nur ein kosmetisches Problem, das die Funktionalität nicht beeinträchtigt.

# 2.0.0
#### Neue Funktionen
- Auswahl des Lieferdatums in der Kaufabwicklung hinzugefügt, einschließlich Abendlieferung.
  - Konfigurieren Sie diese in den Einstellungen des Plugins.
- Neue europäische und internationale Versandoptionen wurden hinzugefügt.
  - GlobalPack wurde ersetzt.

# 1.2.3
- Es wurde ein Problem behoben, bei dem nicht obligatorische Felder obligatorisch wurden, wenn bei der Registrierung ein anderes Land als die Niederlande ausgewählt wurde.
- Ein Fehler, bei dem bestimmte Karten nicht auf der Bestellungsdetailseite in der Verwaltung angezeigt wurden, wenn eine Nicht-PostNL-Bestellung geöffnet wurde, ist behoben worden.

# 1.2.2
- Behebt ein Problem mit Adressen, wenn die Lieferadresse nicht mit der Rechnungsadresse übereinstimmt.
- Behebt ein Problem, bei dem E-Mails nicht gesendet werden konnten, wenn keine Bestelldaten vorlagen (z. B. Passwort-Wiederherstellung).

# 1.2.1
- Behebt ein Problem mit benutzerdefinierten Feldern auf die ausgewählte Lieferadresse (Vielen Dank Mitchel van Vliet und Robbert de Smit @ DutchDrops)

# 1.2.0
- Adressen von Abholstellen werden jetzt als Lieferadresse gespeichert, wenn eine Abholstelle ausgewählt wird
  - Die ursprünglich gewählte Lieferadresse ist immer noch auf der Bestellung verfügbar

# 1.1.0
#### Belgische Release
- Es wurden Produktcodes für den Versand aus Belgien hinzugefügt.

#### Fehlerbehebungen
- Behebt ein Problem, bei dem nach Eingabe einer ungültigen niederländischen Adresse und anschließendem Wechsel in ein anderes Land die Kundenregistrierung nicht abgeschlossen werden konnte.

# 1.0.0
#### Erstes Release
- Melden Sie Ihre Sendungen einfach bei PostNL an.
- Drucken Sie die Versandetiketten ganz einfach aus.
- Verwenden Sie eine der vielen PostNL-Versandarten (z.B. Briefkastenpaket, versicherter Versand).
- Versenden Sie Ihre Pakete ganz einfach nach Belgien, Europa und in den Rest der Welt.
- Adressüberprüfung für niederländische Adressen.
- Lassen Sie Ihre Kunden wählen, ob sie das Paket zu Hause in Empfang nehmen oder in einer PostNL-Stelle in ihrer Nähe abholen wollen.
- Geben Sie das Rücksendeetikett ganz einfach an Ihre Kunden weiter.
- Wählen Sie das Format des zu druckenden Versandetiketts (A4 oder A6).
- Aktivieren Sie die alternative Liefermethode ab einer bestimmten Bestellmenge.
