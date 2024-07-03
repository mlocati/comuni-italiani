[![Tests](https://github.com/mlocati/comuni-italiani/actions/workflows/tests.yml/badge.svg)](https://github.com/mlocati/comuni-italiani/actions/workflows/tests.yml)
[![Coverage](https://coveralls.io/repos/github/mlocati/comuni-italiani/badge.svg?branch=main)](https://coveralls.io/github/mlocati/comuni-italiani?branch=main)

## Comuni Italiani

This PHP library contains the whole list of Italian Regions (regioni), Provinces (province/UTS), and Municipalities (comuni).

The data comes from the official [Situas](https://situas.istat.it) service of the Istituto nazionale di statistica (Istat), that publishes its data with the [CC-BY-4.0 license](https://creativecommons.org/licenses/by/4.0/) .

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
  - the Capital Municipality of this Region (*capoluogo di regione*) (example: `'Torino'`)
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
  - the Capital Municipalities of this Province/UTS (*capoluogo di provincia*) (example: `'Torino'`)
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

### Retrieving All Territories

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

### Finding Territories by ID

If you want to retrieve a territory given its ID, you can use the `Finder` class:

```php
use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

$geographicalSubdivision = $finder->getGeographicalSubdivisionByID(1);
$region = $finder->getRegionByID('01');
$province = $finder->getProvinceByID('201');
$municipality = $finder->getMunicipalityByID('001272');
```

### Finding Provinces by Vehicle Code

You can use the `getProvinceByVehicleCode` method of the `Finder` class:

```php
use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

echo $finder->getProvinceByVehicleCode('CO')->getName();
// prints Como
```

### Finding Territories by Name

You can use the `Finder` class to find Geographical Subdivisions, Regions, Provinces/UTS, and Municipalities by name.

The text to be searched will be split into words, and you'll get the territories whose names contain all the words.

For example, searching for `roma lombard` will return the `Romano di Lombardia (BG)` municipality.

Examples:

```php
use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

$geographicalSubdivisions = $finder->findGeographicalSubdivisionsByName('Nord');
$regions = $finder->findRegionsByName('Campa');
$provinces = $finder->findProvincesByName('Bozen');
$municipalities = $finder->findMunicipalitiesByName('Roma lombard');
```

By default, Finder will look for the beginning of words.
So, `ampania` won't match `Campania`.

If you want to allow searching in the middle of the words, specify `true` as the second parameter:

```php
use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

$geographicalSubdivisions = $finder->findGeographicalSubdivisionsByName('ord', true);
$regions = $finder->findRegionsByName('ampania', true);
$provinces = $finder->findProvincesByName('ozen', true);
$municipalities = $finder->findMunicipalitiesByName('oma ombard', true);
```

You can also restrict the search to specific territories:

```php
use MLocati\ComuniItaliani\Finder;

$finder = new Finder();

$province = $finder->getProvinceByVehicleCode('BG');
$municipalities = $finder->findMunicipalitiesByName('romano', false, $province);
// The same applies to findRegionsByName and findProvincesByName
```

### Testing Hierarchy

Given two territories, you can check if they are the same by using the `isSame()` method:

```php
if ($territory1->isSame($territory2)) {
    echo 'Same territory';
}
```

You can also check if a territory is contained in another territory:

```php
if ($territory1->isContainedIn($territory2)) {
    echo "{$territory1} is contained in {$territory2}";
}
if ($territory1->isSameOrContainedIn($territory2)) {
    echo "{$territory1} is contained in {$territory2} (or they are the same)";
}
```

For Geographical Subdivisions, Regions, and Provinces/UTS (which are containers of other territories) you can also use the `contains()` and `isSameOrContains()` methods.

```php
// $region is an instance of Region here
if ($region->contains($territory)) {
    echo "{$region} contains {$terrtory}";
}

if ($region->isSameOrContains($territory)) {
    echo "{$region} is same (or contains) {$terrtory}";
}
```
