[![Tests](https://github.com/mlocati/comuni-italiani/actions/workflows/tests.yml/badge.svg)](https://github.com/mlocati/comuni-italiani/actions/workflows/tests.yml)
[![Coverage](https://coveralls.io/repos/github/mlocati/comuni-italiani/badge.svg?branch=main)](https://coveralls.io/github/mlocati/comuni-italiani?branch=main)

## Comuni Italiani

This PHP library contains the whole list of Italian Regions (regioni), Provinces (province/UTS), and Municipalities (comuni).

The data comes from the official [Situas](https://situas.istat.it) service of the Istituto nazionale di statistica (Istat), that publishes its data with the [Creative Commons  versione 4.0 Deed](https://creativecommons.org/licenses/by/4.0/deed) license.

### Installation

You can install this package with [Composer](https://getcomposer.org/):

```sh
composer require mlocati/comuni-italiani
```

### Data Structure

This library provides the following Italian Administrative divisions:

- **Geographical Subdivisions** (*ripartizioni geografiche*)  
  For example "Nord-ovest", "Centro", "Sud"
- **Regions** (*regioni*)
  For example "Piemonte", "Lazio", "Campania"
- **Provinces** (*province*) and **Supra-Municipal Territorial Units** (**UTS** - *unità territoriale sovracomunale*)
- **Municipalities** (*comuni*)

Where:
- every Geographical Subdivision contains one or more Regions
- every Region contains one or more Provinces/UTS
- every Province/UTS contains one or more Municipalities

### Data Available

This library provides the following data:

- for the **Geographical Subdivisions**
  - the Istat ID (example: `1`)
  - the name (example: `'Nord-ovest'`)
  - the first level of the current Nomenclature of Territorial Units for Statistics (NUTS1) (example: `'ITC'`)
  - the list of Regions
- for the **Regions**
  - the parent Geographical Subdivision
  - the Istat ID (example: `'01'`)
  - the name (example: `'Piemonte'`)
  - the type:
    - Ordinary Status (*regione a statuto ordinario*)
    - Special Status (*regione a statuto speciale*)
  - the Fiscal Code (*codice fiscale*) assigned by the Agenzia delle Entrate (example: `'80087670016'`)
  - the second level of the current Nomenclature of Territorial Units for Statistics (NUTS2) (example: `'ITC1'`)
  - the Capital Municipality of this Region (*capoluogo di regione*) (example: `Torino`)
  - the list of Provinces/UTS
- for the **Provinces** / **UTS**
  - the parent Region
  - the Istat ID (example: `'201'`)
  - the name (example: `'Torino'`)
  - the historical Province code (example: `'001'`)
  - the type:
    - Province (*provincia*)
    - Autonomous Province (*provincia autonoma*)
    - Metropolitan City (*città metropolitana*)
    - Free Consortium of Municipalities (*libero consorzio di comuni*)
    - Non-Administrative Unit (*unità non amministrativa*) - former Provinces of Friuli-Venezia Giulia
  - the vehicle code (*sigla automobilistica*) (example: `'TO'`)
  - the Fiscal Code (*codice fiscale*) assigned by the Agenzia delle Entrate (example: `'01907990012'`)
  - the third level of the current Nomenclature of Territorial Units for Statistics (NUTS3) (example: `'ITC11'`)
  - the Capital Municipalities of this Province/UTS (*capoluogo di provincia*) (example: `Torino`)
  - the list of Municipalities
- for the **Municipalities**
  - the parent Province/UTS
  - the Istat ID (example: `'001272'`)
  - the name (example: `'Torino'`)
  - the Italian and Foreign names for bilingual municipalities (for example: `'Bolzano'` and `'Bozen'`)
  - if the Municipality is the Capital Municipality of its Region (*capoluogo di regione*)
  - if the Municipalities is a Capital Municipalities of its Province/UTS (*capoluogo di provincia*)
  - the Cadastral Code (*codice catastale*) (example: `'L219'`)
  - the Fiscal Code (*codice fiscale*) assigned by the Agenzia delle Entrate (example: `'00514490010'`)
  - the first level of the current Nomenclature of Territorial Units for Statistics (NUTS1) (example: `'ITC'`)
  - the second level of the current Nomenclature of Territorial Units for Statistics (NUTS2) (example: `'ITC1'`)
  - the third level of the current Nomenclature of Territorial Units for Statistics (NUTS3) (example: `'ITC11'`)

### Retrieving territories

You can have a list of all the Geographical Subdivisions, Regions, Provinces/UTS, and Municipalities using the `Factory` class.

For example:

```php

use MLocati\ComuniItaliani\Factory;

$factory = new Factory();

$allGeographicalSubdivisions = $factory->getGeographicalSubdivisions();
$allRegions = $factory->getRegions();
$allProvinces = $factory->getProvinces();
$allMunicipalities = $factory->getMunicipalities();
```

If you want to retrieve a territory given its ID, you can use the `Finder` class:

```php

use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

$geographicalSubdivision = $finder->getGeographicalSubdivisionByID(1);
$region = $finder->getRegionByID('01');
$province = $finder->getProvinceByID('201');
$municipality = $finder->getMunicipalityByID('001272');
```
