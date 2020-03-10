<p align="center">
  <a href="https://www.tessa-dam.com/" target="_blank" rel="noopener noreferrer">
    <img src="tessa-logo.svg" width=250 alt="TESSA Logo"/>
  </a>
</p>

<p>&nbsp;</p>

<h1 align="center">
  TESSA Connector for Akeneo 2.3
</h1>

<p>&nbsp;</p>

With this Connector Bundle you seamlessly connect Akeneo with the Digital Asset Management solution "TESSA" (https://www.tessa-dam.com).
This provides you with a professional and fully integrated DAM solution for Akeneo to centrally store,
manage and use all additional files for your products (e.g. images, videos, documents, etc.) in all channels.

More informationen is available at our [website](https://www.tessa-dam.com/). 

## Requirements

| Akeneo                        | Version |
|:-----------------------------:|:-------:|
| Akeneo PIM Community Edition  | ~2.3.0  |
| Akeneo PIM Enterprise Edition | ~2.3.0  |

<span style="color:red">__IMPORTANT!__</span> Ensure, that your Akeneo API ist working. Tessa needs an API connection to your Akeneo.
In some cases Apache is configured wrong, see https://api.akeneo.com/documentation/troubleshooting.html#apache-strip-the-authentication-header.

## Installation


1) Install the bundle with composer
```bash
composer require eikona-media/akeneo2-3-tessa-connector
```

2) Then add the following lines **at the end** of your app/config/routing.yml :
```yaml
tessa_media:
    resource: "@EikonaTessaConnectorBundle/Resources/config/routing.yml"
```

3) Enable the bundle in the `app/AppKernel.php` file in the `registerProjectBundles()` method:
```php
protected function registerProjectBundles()
{
    return [
        // ...
        new Eikona\Tessa\ConnectorBundle\EikonaTessaConnectorBundle(),
    ];
}

```

4) Run the following commands in your project root:
```bash
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
php bin/console pim:installer:dump-require-paths --env=prod
php bin/console pim:installer:assets --env=prod
yarn run webpack
```

5) Configure the Tessa Connector in your Akeneo System Settings.
