![alt text](tessa_logo.jpg "TESSA")
# TESSA Connector Bundle
[![build status](https://git.eikona-server.de/tessa/akeneo-connector/badges/master/build.svg)](https://git.eikona-server.de/tessa/akeneo-connector/commits/master)
[![coverage report](https://git.eikona-server.de/tessa/akeneo-connector/badges/master/coverage.svg)](https://git.eikona-server.de/tessa/akeneo-connector/commits/master)

Also available on the Akeneo marketplace: https://marketplace.akeneo.com/

## Description
After installing the connector, you will be able to directly access assets from the TESSA. So you can reference own attributes to the assets and save them as images, videos or documents.As we focused on user friendly usage it should all work easily.
 
For more informationen are available at our [website](http://tessa-dam.de/connector_en.html). 

## Requirements

| TESSA Connector Bundle   | Akeneo PIM Community Edition |
|:--------------------:|:----------------------------:|
| v1.2.*               | v1.7.*                       |
| v1.1.*               | v1.6.*                       |
| v1.0.*               | v1.5.*                       |


## Installation
- Befolge die ANleitung für Custom Entities von Akeneo
- Füge das Bundle dem AppKernel hinzu
```php
protected function registerProjectBundles()
{
    return [
        // ...
        new \Eikona\Tessa\ReferenceDataBundle\EikonaTessaReferenceDataBundle()
    ];
}
```
- Tessa Assets kann im Edit-Formular der Reference Daten implementiert werden
Hierzu muss in der edit.yml des entsprechenden CustomEntity der Eintrag etwa so aussehen
```yaml
    pim-color-edit-hex:
        module: eikona/tessa/connector/reference-data/form/field
        parent: pim-color-edit-form-properties-common
        targetZone: content
        position: 100
        config:
            fieldName: hex
            label: acme_custom.color.field.label.hex
            customEntityName: color
```
Wichtig ist vor allem der Wert bei *module* und die Config *customEntityName*
