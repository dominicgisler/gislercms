-- Einfügen der Standardseiten

INSERT INTO `cms__page` (`page_id`, `name`, `enabled`, `fk_language_id`)
VALUES (1, 'Startseite', 1, 1),
       (2, 'Error 404', 0, 1),
       (3, 'Impressum', 1, 1),
       (4, 'Datenschutzerklärung', 1, 1);

INSERT INTO `cms__page_translation` (`page_translation_id`, `fk_page_id`, `fk_language_id`, `name`, `title`, `content`, `enabled`)
VALUES (1, 1, 1, 'startseite', 'Startseite', '<h1>Willkommen auf deiner Webseite!</h1><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.</p>', 1),
       (2, 1, 2, 'home', 'Home', '<h1>Welcome to your Website!</h1>', 1),
       (3, 2, 1, 'error-404', 'Error 404', '<p>Seite nicht gefunden!</p>', 1),
       (4, 2, 2, 'error-404', 'Error 404', '<p>Page not found!</p>', 1),
       (5, 3, 1, 'impressum', 'Impressum', '<h3>Kontakt-Adresse</h3>
<p>Max Muster<br />Musterweg 33<br />1234 Muster</p>
<p>E-Mail:<br />info@example.com</p>
<h4>Haftungsausschluss</h4>
<p>Der Autor &uuml;bernimmt keinerlei Gew&auml;hr hinsichtlich der inhaltlichen Richtigkeit, Genauigkeit, Aktualit&auml;t, Zuverl&auml;ssigkeit und Vollst&auml;ndigkeit der Informationen.</p>
<p>Haftungsanspr&uuml;che gegen den Autor wegen Sch&auml;den materieller oder immaterieller Art, welche aus dem Zugriff oder der Nutzung bzw. Nichtnutzung der ver&ouml;ffentlichten Informationen, durch Missbrauch der Verbindung oder durch technische St&ouml;rungen entstanden sind, werden ausgeschlossen.</p>
<p>Alle Angebote sind unverbindlich. Der Autor beh&auml;lt es sich ausdr&uuml;cklich vor, Teile der Seiten oder das gesamte Angebot ohne besondere Ank&uuml;ndigung zu ver&auml;ndern, zu erg&auml;nzen, zu l&ouml;schen oder die Ver&ouml;ffentlichung zeitweise oder endg&uuml;ltig einzustellen.</p>
<h4>Haftungsausschluss f&uuml;r Links</h4>
<p>Verweise und Links auf Webseiten Dritter liegen ausserhalb unseres Verantwortungsbereichs. Es wird jegliche Verantwortung f&uuml;r solche Webseiten abgelehnt. Der Zugriff und die Nutzung solcher Webseiten erfolgen auf eigene Gefahr des jeweiligen Nutzers.</p>
<h4>Urheberrechte</h4>
<p>Die Urheber- und alle anderen Rechte an Inhalten, Bildern, Fotos oder anderen Dateien auf dieser Website, geh&ouml;ren ausschliesslich <strong>Max Muster</strong> oder den speziell genannten Rechteinhabern. F&uuml;r die Reproduktion jeglicher Elemente ist die schriftliche Zustimmung des Urheberrechtstr&auml;gers im Voraus einzuholen.</p>
<!--ACHTUNG: Wenn Sie die Quelle ohne Erlaubnis von SwissAnwalt entfernen, dann begehen Sie eine Urheberrechtsverletzung welche in jedem Fall geahndet wird.-->
<p>Quelle: <a href="https://www.swissanwalt.ch" target="_blank" rel="noopener">SwissAnwalt</a></p>
<!--Bitte beachten Sie die AGB von SwissAnwalt betreffend allf&auml;llig anfallenden Kosten bei Weglassen der Quelle!-->', 1),
       (6, 4, 1, 'datenschutz', 'Datenschutzerklärung', '<h2>Datenschutzerkl&auml;rung</h2>
<p>Verantwortlich im Sinne des Datenschutzgesetzes:</p>
<p>Max Muster<br />Musterweg 33<br />1234 Muster</p>
<h4>Erfassung von Daten</h4>
<p>W&auml;hrend Sie auf unsere Webseiten zugreifen, erfassen wir automatisch Daten von allgemeiner Natur. Diese Daten (Server-Logfiles) umfassen zum Beispiel die Art ihres Browsers, ihr Betriebssystem, den Domainnamen Ihres Internetanbieters sowie weitere &auml;hnliche allgemeine Daten. Diese Daten sind absolut Personen unabh&auml;ngig und werden genutzt, um Ihnen die Webseiten korrekt darzustellen und werden bei jeder Nutzung des Internets abgerufen. Die absolut anonymen Daten werden statistisch ausgewertet um unseren Service f&uuml;r Sie zu verbessern.</p>
<h4>Anmeldung auf unserer Webseite</h4>
<p>Bei der Anmeldung f&uuml;r spezifische Angebote erfragen wir einige pers&ouml;nliche Daten wie Name, Anschrift, Kontakt, Telefonnummer oder E-Mail-Adresse. Angemeldete Nutzer k&ouml;nnen auf bestimmte Zusatzleistungen zugreifen. Angemeldete Nutzer haben die M&ouml;glichkeit, alle angegebenen pers&ouml;nlichen Daten zu jedem Zeitpunkt zu &auml;ndern oder l&ouml;schen zu lassen. Sie k&ouml;nnen auch jederzeit die von ihnen gespeicherten Daten bei uns erfragen. Soweit gesetzlich keine Frist f&uuml;r die Aufbewahrung der Daten besteht, k&ouml;nnen diese ge&auml;ndert oder gel&ouml;scht werden. Bitte kontaktieren Sie uns dazu &uuml;ber unsere Kontaktseite.</p>
<h4>Kommentare</h4>
<p>Wenn Sie auf unserer Webseite Kommentare abgeben werden neben dem reinen Text auch Angaben zum Zeitpunkt der Erstellung sowie ihr Nutzername gespeichert. Auf diese Art garantieren wir die Sicherheit unseres Blog, rechtswiedrige Beitr&auml;ge von Benutzern k&ouml;nnen nachverfolgt werden.</p>
<h4>Kontaktformular</h4>
<p>Wenn Sie uns &uuml;ber unsere Kontaktseite kontaktieren oder uns eine E-Mail senden werden die entsprechenden Daten zur Bearbeitung gespeichert.</p>
<h4>L&ouml;schung von Daten</h4>
<p>Ihre pers&ouml;nlichen Daten werden nur so lange gespeichert, wie dies absolut notwendig ist um die angegebenen Dienste zu leisten und es vom Gesetz vorgeschrieben ist. Nach Ablauf dieser Fristen werden die pers&ouml;nlichen Daten der Nutzer regelm&auml;&szlig;ig gel&ouml;scht.</p>
<h4>Analyse der Aufrufe</h4>
<p>Unsere Webseite erhebt Daten von Besuchern f&uuml;r statistische Zwecke, dies erfolgt &uuml;ber "Cookies".</p>
<p>Die dadurch erhobenen Daten werden gespeichert, alle IP-Adressen direkt nach der Verarbeitung und vor der Speicherung anonymisiert. &Uuml;ber ihre Browser-Einstellungen k&ouml;nnen Sie die Verwendung von Cookies einstellen. Eventuell k&ouml;nnen diese Einstellungen Einfluss auf die korrekte Funktion unserer Website nehmen.</p>
<h4>Cookies</h4>
<p>Unsere Webseite verwendet "Cookies". Cookies sind Textdateien, die vom Server einer Webseite auf Ihren Rechner &uuml;bertragen werden. Bestimmte Daten wie IP-Adresse, Browser, Betriebssystem und Internet Verbindung werden dabei &uuml;bertragen.</p>
<p>Cookies starten keine Programme und &uuml;bertragen keine Viren. Die durch Cookies gesammelten Informationen dienen dazu, Ihnen die Navigation zu erleichtern und die Anzeige unserer Webseiten zu optimieren.</p>
<p>Daten, die von uns erfasst werden, werden niemals ohne Ihre Einwilligung an Daten an Dritte weitergegeben oder mit personenbezogenen Daten verkn&uuml;pft.</p>
<p>Die Verwendung von Cookies kann &uuml;ber Einstellungen in ihrem Browser verhindert werden. In den Erl&auml;uterungen zu Ihrem Internetbrowsers finden Sie Informationen dar&uuml;ber, wie man diese Einstellungen ver&auml;ndern kann. Einzelne Funktionen unserer Website k&ouml;nnen unter Umst&auml;nden nicht richtig funktionieren, wenn die Verwendung von Cookies desaktiviert ist.</p>
<h4>Auskunft, Berichtigung, L&ouml;schung und Widerspruch</h4>
<p>Sie k&ouml;nnen zu jedem Zeitpunkt Informationen &uuml;ber Ihre bei uns gespeicherten Daten erbitten. Diese k&ouml;nnen auch berichtigt oder, sofern die vorgeschriebene Zeitr&auml;ume der Datenspeicherung zur Gesch&auml;ftsabwicklung abgelaufen sind, gel&ouml;scht werden. Gerne helfen wir Ihnen bei allen entsprechenden Fragen.</p>
<p>&Auml;nderungen oder Widerruf von Einwilligungen k&ouml;nnen durch eine Mitteilung an uns vorgenommen werden. Dies ist auch f&uuml;r zuk&uuml;nftige Aktionen m&ouml;glich.</p>
<h4>&Auml;nderung der Datenschutzbestimmungen</h4>
<p>Unsere Datenschutzerkl&auml;rung kann in unregelm&auml;&szlig;igen Abst&auml;nden angepasst werden, damit sie den aktuellen rechtlichen Anforderungen entspricht oder um &Auml;nderungen unserer Dienstleistungen umzusetzen, z. B. bei der Einf&uuml;gung neuer Angebote. F&uuml;r Ihren n&auml;chsten Besuch gilt dann automatisch die neue Datenschutzerkl&auml;rung.</p>
<h4>Kontakt</h4>
<p>F&uuml;r Fragen zum Datenschutz senden Sie uns bitte eine Nachricht an info@example.com mit dem Betreff "Datenschutz".</p>
<p>Diese Datenschutzerkl&auml;rung wurde bei <a href="https://xn--datenschutzerklrunggenerator-knc.de/" target="_blank" rel="noopener">datenschutzerkl&auml;runggenerator.de</a>&nbsp;erstellt.</p>', 1);