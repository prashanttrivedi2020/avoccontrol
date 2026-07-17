@extends('layouts.policy')

@section('title', 'Privacy Policy')
@section('page-title', 'Privacy Policy')

@section('content')
@php $locale = app()->getLocale(); @endphp

<div style="max-width: 1100px; margin: 0 auto; line-height: 1.7;">
    <div class="card" style="border-radius: 16px;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="margin-bottom: 6px; font-size: 24px;">FireKontrol 365</h2>
                <p style="color: var(--text-muted); margin: 0;">
                    @switch($locale)
                        @case('en')
                            Privacy policy, terms, and legal information.
                            @break
                        @case('tr')
                            Gizlilik politikası, kullanım koşulları ve yasal bilgiler.
                            @break
                        @default
                            Datenschutzerklärung, Nutzungsbedingungen und rechtliche Hinweise.
                    @endswitch
                </p>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('welcome') }}" class="btn btn-secondary btn-sm">
                    @switch($locale)
                        @case('en')
                            ← Back to welcome
                            @break
                        @case('tr')
                            ← Ana sayfaya dön
                            @break
                        @default
                            ← Zur Startseite
                    @endswitch
                </a>
                <a href="{{ route('lang.switch', 'de') }}" class="btn btn-secondary btn-sm">🇩🇪 Deutsch</a>
                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-secondary btn-sm">🇬🇧 English</a>
                <a href="{{ route('lang.switch', 'tr') }}" class="btn btn-secondary btn-sm">🇹🇷 Türkçe</a>
            </div>
        </div>

        @switch($locale)
            @case('en')
                <h3 style="margin-top: 8px; margin-bottom: 10px;">Privacy Policy and Legal Information</h3>
                <p>Avoc Systems GmbH takes the protection of your personal data very seriously. This page explains how FireKontrol 365 processes, stores, and uses data in the cloud-based software environment.</p>

                <h4>1. Imprint</h4>
                <p><strong>Provider:</strong><br>Avoc Systems GmbH<br>Saganer Str. 19a<br>90475 Nuremberg<br>Germany</p>
                <p><strong>Contact:</strong><br>E-mail: info@ekampanya.de<br>Internet: https://firecontrol.ekampanya.app</p>
                <p><strong>Commercial register:</strong><br>Amtsgericht Nürnberg<br>HRB 38608</p>
                <p><strong>VAT ID:</strong><br>DE343295982</p>

                <h4>2. Privacy Policy</h4>
                <p>The controller under the GDPR is Avoc Systems GmbH. FireKontrol 365 is hosted by Hetzner Online GmbH in Germany, and customer data is stored within the European Union.</p>

                <h5>2.1 Data stored</h5>
                <ul>
                    <li>Company data: company name, address, branches, contact persons, phone numbers, business information.</li>
                    <li>User data: username, e-mail address, encrypted password, user roles, permissions, access credentials.</li>
                    <li>Product data: product names, item numbers, barcodes, PLUs, categories, manufacturers, purchase prices, quantities, packaging units, shrinkage data.</li>
                    <li>Loss documentation: shrinkage quantity, reason, spoilage, expiry date, theft, damage, self-consumption, inventory differences, notes, images.</li>
                    <li>Technical data: browser type, operating system, IP address, date and time, access logs, device information.</li>
                </ul>

                <h5>2.2 Purpose of data processing</h5>
                <p>Data is processed for providing the software, user account management, loss documentation, product management, reports, statistics, software improvement, error analysis, IT security, and legal compliance.</p>

                <h5>2.3 Legal basis</h5>
                <p>Processing is based on Article 6(1)(b), (c), and (f) of the GDPR, depending on the purpose.</p>

                <h5>2.4 Storage period and security</h5>
                <p>Data is retained as long as necessary for contract execution, legal retention duties, or legitimate interests. Appropriate technical and organizational measures are used to protect data against loss, manipulation, destruction, misuse, and unauthorized access.</p>

                <h5>2.5 Sharing of personal data</h5>
                <p>Personal data is generally not shared except where required by law, with hosting providers, technical service providers, or authorities.</p>

                <h5>2.6 Rights of the affected person</h5>
                <p>You have the right to access, correct, delete, restrict processing, receive your data, object to processing, and lodge a complaint with a supervisory authority.</p>

                <h4>3. General Terms and Conditions (GTC)</h4>
                <p>These terms apply to contracts between Avoc Systems GmbH and its commercial customers for use of FireKontrol 365 as a SaaS solution. The software includes functions such as shrinkage recording, product management, barcode scanning, photo documentation, user management, search and filter functions, statistics, reports, exports, dashboards, and history management.</p>
                <p>The standard price is EUR 149.00 per month plus statutory VAT. Billing is monthly in advance. The contract runs indefinitely and may be terminated with one month’s notice to the end of the month.</p>

                <h4>4. Usage Conditions</h4>
                <p>FireKontrol 365 may only be used for business purposes. Users must keep credentials confidential, use strong passwords, and not grant third parties access to their accounts. The customer is responsible for all content stored or uploaded.</p>

                <h4>5. GoBD Notice</h4>
                <p>FireKontrol 365 supports the digital documentation of loss, spoilage, and theft cases. However, it does not replace tax advice, accounting software, or legal advice. The company remains solely responsible for proper bookkeeping, documentation completeness, and tax treatment.</p>

                <h4>6. Liability</h4>
                <p>Liability is governed by applicable law. Avoc Systems GmbH is liable in cases of intent, gross negligence, injury to life, body or health, and mandatory statutory liability. Otherwise, indirect, consequential, and lost-profit damages are excluded as far as permitted by law.</p>

                <h4>7. Copyright</h4>
                <p>FireKontrol 365 and its components are protected by copyright and trademark law. The customer receives only a non-exclusive, non-transferable right to use the software during the contract period.</p>

                <h4>8. Cookies and Storage Technologies</h4>
                <p>The application uses technically necessary cookies and local storage for authentication, session management, language settings, and platform stability. Marketing cookies are not currently used.</p>

                <h4>9. Contact</h4>
                <p>If you have questions about FireKontrol 365 or these legal notices, contact us at info@ekampanya.de or visit https://firecontrol.ekampanya.app.</p>
                @break

            @case('tr')
                <h3 style="margin-top: 8px; margin-bottom: 10px;">Gizlilik Politikası ve Yasal Bilgiler</h3>
                <p>Avoc Systems GmbH, kişisel verilerinizin korunmasına büyük önem verir. Bu sayfa, bulut tabanlı yazılım FireKontrol 365’in verileri nasıl işlediğini, sakladığını ve kullandığını açıklamaktadır.</p>

                <h4>1. Künye</h4>
                <p><strong>Sağlayıcı:</strong><br>Avoc Systems GmbH<br>Saganer Str. 19a<br>90475 Nürnberg<br>Almanya</p>
                <p><strong>İletişim:</strong><br>E-posta: info@ekampanya.de<br>İnternet: https://firecontrol.ekampanya.app</p>
                <p><strong>Ticaret sicili:</strong><br>Amtsgericht Nürnberg<br>HRB 38608</p>
                <p><strong>KDV numarası:</strong><br>DE343295982</p>

                <h4>2. Gizlilik Politikası</h4>
                <p>KVKK kapsamında sorumlu kişi Avoc Systems GmbH’dir. FireKontrol 365, Almanya’daki Hetzner Online GmbH sunucularında barındırılır ve müşteri verileri Avrupa Birliği içinde saklanır.</p>

                <h5>2.1 Saklanan veriler</h5>
                <ul>
                    <li>Şirket verileri: şirket adı, adres, şubeler, kişi bilgileri, telefon numaraları, şirket bilgileri.</li>
                    <li>Kullanıcı verileri: kullanıcı adı, e-posta adresi, şifre, kullanıcı rolleri, izinler, giriş bilgileri.</li>
                    <li>Ürün verileri: ürün adı, stok kodu, barkod, PLU, kategori, üretici, alış fiyatı, miktar, paketleme birimleri, fire verileri.</li>
                    <li>Fire belgeleri: fire miktarı, fire nedeni, bozulma, son kullanma tarihi, hırsızlık, hasar, kendi tüketim, envanter farkı, notlar, görseller.</li>
                    <li>Teknik veriler: tarayıcı türü, işletim sistemi, IP adresi, tarih ve saat, erişim logları, cihaz bilgileri.</li>
                </ul>

                <h5>2.2 Veri işleme amaçları</h5>
                <p>Veriler, yazılımın sunulması, kullanıcı hesabı yönetimi, fire dokümantasyonu, ürün yönetimi, raporlar, istatistikler, yazılım iyileştirme, hata analizi, BT güvenliği ve yasal yükümlülüklerin yerine getirilmesi için işlenir.</p>

                <h5>2.3 Hukuki dayanak</h5>
                <p>İşleme, amaca bağlı olarak KVKK’nın 6. maddesinin 1. fıkrasının (b), (c) ve (f) bentleri temel alınarak yapılır.</p>

                <h5>2.4 Saklama süresi ve güvenlik</h5>
                <p>Veriler, sözleşmenin yürütülmesi, yasal saklama yükümlülükleri veya meşru menfaatler nedeniyle gerekli olduğu sürece saklanır. Veri kaybı, manipülasyon, tahribat, kötüye kullanım ve yetkisiz erişime karşı uygun teknik ve organizasyonel önlemler alınır.</p>

                <h5>2.5 Kişisel verilerin paylaşılması</h5>
                <p>Kişisel veriler genel olarak paylaşılmaz; ancak yasal zorunluluklar, barındırma hizmet sağlayıcıları, teknik hizmet sağlayıcıları veya otoriteler söz konusu olduğunda paylaşılabilir.</p>

                <h5>2.6 İlgili kişilerin hakları</h5>
                <p>İlgili kişi olarak erişim, düzeltme, silme, işlenmenin sınırlanması, veri taşınabilirliği, itiraz ve denetim makamına şikâyet hakkına sahipsiniz.</p>

                <h4>3. Genel Hizmet Koşulları</h4>
                <p>Bu koşullar, Avoc Systems GmbH ile ticari müşterileri arasında FireKontrol 365’in SaaS olarak kullanımı için yapılan sözleşmelerde geçerlidir. Yazılım; fire kaydı, ürün yönetimi, barkod tarama, fotoğraf dokümantasyonu, kullanıcı yönetimi, arama ve filtreleme, istatistik, rapor, dışa aktarma, dashboard ve geçmiş yönetimi gibi işlevler sunar.</p>
                <p>Standart fiyat ayda 149,00 € olup, mevzuat gereği KDV eklenir. Faturalandırma aylık olarak peşin yapılır. Sözleşme süresizdir ve ay sonuna kadar bir ay önceden feshedilebilir.</p>

                <h4>4. Kullanım Koşulları</h4>
                <p>FireKontrol 365 yalnızca işletme amaçlı kullanılabilir. Kullanıcılar erişim bilgilerini gizli tutmalı, güçlü şifreler kullanmalı ve üçüncü kişilere hesabı açmamalıdır. Müşteri, sakladığı veya yüklediği tüm içerikten sorumludur.</p>

                <h4>5. GoBD Uyarısı</h4>
                <p>FireKontrol 365, fire, bozulma ve hırsızlık olaylarının dijital dokümantasyonunu destekler. Bununla birlikte vergi danışmanlığı, muhasebe yazılımı veya hukuki danışmanlık yerine geçmez. Şirketin doğru muhasebe, dokümantasyon eksiksizliği ve vergi işlemleri konusundaki sorumluluğu müşteriye aittir.</p>

                <h4>6. Sorumluluk</h4>
                <p>Sorumluluk yürürlükteki yasalara tabidir. Avoc Systems GmbH, kast, ağır ihmal, hayat, vücut veya sağlık zararları ve zorunlu yasal sorumluluk hallerinde sorumludur. Diğer durumlarda, yasalar izin verdiği ölçüde dolaylı, sonuç niteliğindeki ve kayıp kâr zararları hariç tutulur.</p>

                <h4>7. Telif Hakkı</h4>
                <p>FireKontrol 365 ve bileşenleri telif hakkı ve marka hukuku ile korunmaktadır. Müşteri, sözleşme süresince yazılımı kullanmak için yalnızca devredilemeyen, münhasır olmayan bir kullanım hakkı elde eder.</p>

                <h4>8. Çerezler ve Depolama Teknolojileri</h4>
                <p>Uygulama, kimlik doğrulama, oturum yönetimi, dil ayarları ve platform stabilitesi için teknik olarak gerekli çerezleri ve yerel depolama alanını kullanır. Pazarlama amaçlı çerezler şu anda kullanılmamaktadır.</p>

                <h4>9. İletişim</h4>
                <p>FireKontrol 365 veya bu yasal bilgiler hakkında sorularınız için info@ekampanya.de adresine başvurabilir veya https://firecontrol.ekampanya.app adresini ziyaret edebilirsiniz.</p>
                @break

            @default
                <h3 style="margin-top: 8px; margin-bottom: 10px;">Datenschutzerklärung und rechtliche Hinweise</h3>
                <p>Die Avoc Systems GmbH legt großen Wert auf den Schutz Ihrer personenbezogenen Daten. Diese Seite erklärt, wie FireKontrol 365 Daten in der cloudbasierten Software verarbeitet, speichert und nutzt.</p>

                <h4>1. Impressum</h4>
                <p><strong>Anbieter:</strong><br>Avoc Systems GmbH<br>Saganer Str. 19a<br>90475 Nürnberg<br>Deutschland</p>
                <p><strong>Kontakt:</strong><br>E-Mail: info@ekampanya.de<br>Internet: https://firecontrol.ekampanya.app</p>
                <p><strong>Handelsregister:</strong><br>Amtsgericht Nürnberg<br>HRB 38608</p>
                <p><strong>Umsatzsteuer-ID:</strong><br>DE343295982</p>

                <h4>2. Datenschutzerklärung</h4>
                <p>Verantwortlicher im Sinne der DSGVO ist Avoc Systems GmbH. FireKontrol 365 wird bei Hetzner Online GmbH gehostet und Kundendaten werden innerhalb der Europäischen Union gespeichert.</p>

                <h5>2.1 Gespeicherte Daten</h5>
                <ul>
                    <li>Unternehmensdaten, Ansprechpersonen, Telefonnummern, Unternehmensinformationen.</li>
                    <li>Benutzerdaten wie Benutzername, E-Mail-Adresse, verschlüsseltes Passwort, Rollen und Berechtigungen.</li>
                    <li>Produktdaten wie Produktname, Artikelnummer, Barcode, PLU, Kategorie, Hersteller, Einkaufspreis, Mengen, Verpackungseinheiten und Schwunddaten.</li>
                    <li>Dokumentationen von Verlusten, Verderb, Diebstahl, Inventurdifferenzen, Notizen und Bilder.</li>
                    <li>Technische Daten wie Browsertyp, Betriebssystem, IP-Adresse, Datum, Uhrzeit, Zugriffsprotokolle und Geräteinformationen.</li>
                </ul>

                <h5>2.2 Zwecke der Verarbeitung</h5>
                <p>Die Daten werden zur Bereitstellung der Software, Verwaltung von Benutzerkonten, Dokumentation von Schwund, Produktverwaltung, Erstellung von Berichten und Statistiken, Fehleranalyse, IT-Sicherheit und gesetzlichen Verpflichtungen verarbeitet.</p>

                <h5>2.3 Rechtsgrundlagen</h5>
                <p>Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b, c und f DSGVO.</p>

                <h5>2.4 Speicherdauer und Datensicherheit</h5>
                <p>Daten werden nur so lange gespeichert, wie dies für die Vertragsdurchführung, gesetzliche Aufbewahrungspflichten oder berechtigte Interessen erforderlich ist. Es werden technische und organisatorische Maßnahmen zum Schutz vor Verlust, Manipulation, Zerstörung, Missbrauch und unberechtigtem Zugriff umgesetzt.</p>

                <h5>2.5 Weitergabe</h5>
                <p>Personenbezogene Daten werden grundsätzlich nicht weitergegeben, außer aufgrund gesetzlicher Verpflichtungen, an Hosting- oder technische Dienstleister oder Behörden.</p>

                <h5>2.6 Rechte der betroffenen Personen</h5>
                <p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit, Widerspruch und Beschwerde bei einer Datenschutzaufsichtsbehörde.</p>

                <h4>3. Allgemeine Geschäftsbedingungen</h4>
                <p>Diese AGB gelten für Verträge zwischen Avoc Systems GmbH und gewerblichen Kunden zur Nutzung von FireKontrol 365 als SaaS-Lösung. Die Software umfasst Funktionen wie Schwunderfassung, Produktverwaltung, Barcode-Scanner, Fotodokumentation, Benutzerverwaltung, Suche, Filter, Statistiken, Berichte, Exporte, Dashboard und Historienverwaltung.</p>
                <p>Der Standardtarif beträgt 149,00 € pro Monat zzgl. gesetzlicher Umsatzsteuer. Die Abrechnung erfolgt monatlich im Voraus. Der Vertrag läuft auf unbestimmte Zeit und kann mit einer Frist von einem Monat zum Monatsende gekündigt werden.</p>

                <h4>4. Nutzungsbedingungen</h4>
                <p>FireKontrol 365 darf nur für betriebliche Zwecke verwendet werden. Nutzer müssen ihre Zugangsdaten geheim halten, sichere Passwörter verwenden und Dritten keinen Zugriff gewähren. Der Kunde ist für alle Inhalte verantwortlich, die gespeichert oder hochgeladen werden.</p>

                <h4>5. GoBD-Hinweis</h4>
                <p>FireKontrol 365 unterstützt die digitale Dokumentation von Schwund-, Verderb- und Diebstahlsfällen. Die Software ersetzt jedoch keine steuerliche Beratung, Buchhaltungssoftware oder rechtliche Beratung. Die Verantwortung für ordnungsgemäße Buchführung und steuerliche Behandlung liegt beim Unternehmen.</p>

                <h4>6. Haftung</h4>
                <p>Die Haftung richtet sich nach den gesetzlichen Vorschriften. Avoc Systems GmbH haftet bei Vorsatz, grober Fahrlässigkeit, Schäden an Leben, Körper oder Gesundheit und zwingender gesetzlicher Haftung. Sonstige mittelbare, Folgeschäden und entgangener Gewinn sind soweit zulässig ausgeschlossen.</p>

                <h4>7. Urheberrecht</h4>
                <p>FireKontrol 365 und Ihre Bestandteile sind urheberrechtlich und markenrechtlich geschützt. Der Kunde erhält nur ein nicht ausschließliches und nicht übertragbares Nutzungsrecht während der Vertragslaufzeit.</p>

                <h4>8. Cookies und Speichertechnologien</h4>
                <p>Die Anwendung verwendet technisch notwendige Cookies und Local Storage für Anmeldung, Sitzungsverwaltung, Spracheinstellungen und Stabilität. Marketing-Cookies werden derzeit nicht verwendet.</p>

                <h4>9. Kontakt</h4>
                <p>Bei Fragen zu FireKontrol 365 oder den rechtlichen Hinweisen wenden Sie sich an info@ekampanya.de oder besuchen Sie https://firecontrol.ekampanya.app.</p>
        @endswitch
    </div>
</div>
@endsection
