# Free Shipping Bar (`wg_freeshippingbar`)

Darmowy moduł PrestaShop, który pokazuje klientom, ile brakuje im do darmowej dostawy.

**Wersja:** 1.0.0 · **PrestaShop:** 1.7.6 – 9.x · **Licencja:** EUPL-1.2 · **Autor:** [Webski Gość](https://webskigosc.com)

## Co robi moduł

Moduł odczytuje próg darmowej dostawy ustawiony w sklepie (Wysyłka → Preferencje → _Darmowa wysyłka od_), przelicza go na walutę klienta i wyświetla pasek z brakującą kwotą: _„Brakuje 50,00 zł do darmowej dostawy”_. Gdy koszyk osiągnie próg, tekst zmienia się na _„Masz darmową dostawę!”_.

Pasek aktualizuje się na żywo przy każdej zmianie koszyka (dodanie produktu, zmiana ilości, usunięcie) — bez przeładowania strony. Sam wybierasz, gdzie ma się pojawić, w jakim motywie i ewentualnie w jakich kolorach.

## Funkcje

- Kwota brakująca do darmowej dostawy, liczona w aktualnej walucie
- Opcjonalny animowany pasek postępu
- Aktualizacja na żywo przy każdej zmianie koszyka (zdarzenie `updateCart`)
- Pokazywanie lub ukrywanie paska po osiągnięciu darmowej dostawy
- Pokazywanie lub ukrywanie paska przy pustym koszyku
- Obliczanie z podatkiem lub bez, opcjonalna etykieta brutto/netto
- 10 miejsc wyświetlania do wyboru (karta produktu, koszyk, modal koszyka, checkout, górny baner…)
- 4 wbudowane motywy: Basic, Classic, Minimal, Modern
- Własne kolory (tło, tekst, pasek postępu, tło paska)
- Wielojęzyczność: EN, ES, FR, PL
- Opcjonalne automatyczne sprawdzanie aktualizacji z powiadomieniem w panelu

## Instalacja

Moduły → Menedżer modułów → **Załaduj moduł** → wybierz ZIP → **Zainstaluj** → **Konfiguruj**.

Możesz też skopiować katalog `wg_freeshippingbar` do `modules/` i zainstalować moduł z Menedżera modułów.

Upewnij się, że próg darmowej dostawy jest ustawiony: **Wysyłka → Preferencje → Darmowa wysyłka od** (cena). Jeśli wynosi 0, pasek nie ma do czego odliczać.

## Konfiguracja

### Ustawienia

| Opcja                      | Opis                                                                                 | Domyślnie                                          |
| -------------------------- | ------------------------------------------------------------------------------------ | -------------------------------------------------- |
| Pasek postępu              | Wyświetlanie paska postępu                                                           | Tak                                                |
| Darmowa dostawa osiągnięta | Pokazać czy ukryć pasek po osiągnięciu darmowej dostawy                              | Pokaż                                              |
| Pusty koszyk               | Pokazać czy ukryć pasek, gdy koszyk jest pusty                                       | Ukryj                                              |
| Z podatkiem                | Liczenie brakującej kwoty z podatkiem lub bez (nieaktywne, gdy podatki są wyłączone) | wg ustawień podatków sklepu                        |
| Etykieta podatku           | Dopisanie sklepowej etykiety „brutto/netto” do kwoty                                 | wg ustawień sklepu                                 |
| Sprawdzaj aktualizacje     | Sprawdzanie nowej wersji modułu (raz na godzinę) i komunikat w ustawieniach modułu   | Tak                                                |
| Przypisz hooki             | Gdzie pasek ma być wyświetlany — wymagany co najmniej jeden                          | Product Additional Info, Checkout Subtotal Details |

### Miejsce wyświetlania (Przypisz hooki)

| Etykieta w panelu           | Hook                              | Gdzie się pokazuje                                 |
| --------------------------- | --------------------------------- | -------------------------------------------------- |
| Top Banner                  | `displayBanner`                   | Baner w nagłówku na każdej stronie                 |
| Cart Modal Content          | `displayCartModalContent`         | Modal „Dodano do koszyka”, treść                   |
| Cart Modal Footer           | `displayCartModalFooter`          | Modal „Dodano do koszyka”, stopka                  |
| Cart Reassurance            | `displayReassurance`              | Tylko strona koszyka (blok reassurance)            |
| Checkout Subtotal Details   | `displayCheckoutSubtotalDetails`  | Podsumowanie koszyka, pod sumami częściowymi       |
| Cross Selling Shopping Cart | `displayCrossSellingShoppingCart` | Strona koszyka, sekcja cross-selling               |
| Product Additional Info     | `displayProductAdditionalInfo`    | Karta produktu, pod przyciskiem dodania do koszyka |
| Product Footer              | `displayFooterProduct`            | Karta produktu, stopka                             |
| Shopping Cart               | `displayShoppingCart`             | Strona koszyka, góra                               |
| Shopping Cart Footer        | `displayShoppingCartFooter`       | Strona koszyka, dół                                |

### Wygląd

| Opcja                                           | Opis                                                                                   |
| ----------------------------------------------- | -------------------------------------------------------------------------------------- |
| Motyw                                           | Basic, Classic (domyślny), Minimal lub Modern — każdy to jeden plik CSS w `views/css/` |
| Własne kolory                                   | Włącz, aby nadpisać kolory motywu                                                      |
| Tło / Tekst / Pasek postępu / Tło paska postępu | Wybór kolorów, widoczny po włączeniu własnych kolorów                                  |

Własne kolory są zapisywane przy **Zapisz** jako zmienne CSS do pliku `views/css/custom.css`, ładowanego po pliku motywu. Plik musi być zapisywalny przez serwer WWW; w przeciwnym razie w panelu pojawi się błąd.

## Wymagania

- PrestaShop 1.7.6.0 – 9.2.x
- PHP 7.1+ (zalecane 8.x)
- Ustawiony próg darmowej dostawy (Wysyłka → Preferencje)
- Zapisywalny plik `modules/wg_freeshippingbar/views/css/custom.css`, jeśli używasz własnych kolorów
- Rozszerzenie PHP cURL, jeśli sprawdzanie aktualizacji ma być włączone

## Zrzuty ekranu

<!-- ![Panel – ustawienia](docs/screenshot-settings.png) -->
<!-- ![Panel – wygląd](docs/screenshot-design.png) -->
<!-- ![Front – karta produktu](docs/screenshot-product.png) -->
<!-- ![Front – koszyk](docs/screenshot-cart.png) -->

## Zgodność

- ✅ PrestaShop 1.7.6, 8.x i 9.x
- ✅ EN, ES, FR, PL
- ✅ Darmowy i otwarty (licencja EUPL-1.2)

## Pobieranie

Najnowsze wydanie: **1.0.0**

- [Strona modułu na webskigosc.com](https://webskigosc.com/prestashop/freeshippingbar)
- [Pobierz z GitHub](https://github.com/YOUR_GITHUB_USER/wg_freeshippingbar) <!-- TODO: podmień na właściwy adres repozytorium -->

## Opinie i zgłoszenia

Zgłoszenia błędów i pomysły są mile widziane. Napisz na [marcin@webskigosc.com](mailto:marcin@webskigosc.com) lub [ask@webskigosc.com](mailto:ask@webskigosc.com).

Podoba Ci się moduł? [Postaw mi kawę](https://tip.webskigosc.com).

## Licencja

Na licencji [European Union Public Licence v1.2](https://joinup.ec.europa.eu/software/page/eupl) lub nowszej. Zobacz [LICENSE.txt](LICENSE.txt).
