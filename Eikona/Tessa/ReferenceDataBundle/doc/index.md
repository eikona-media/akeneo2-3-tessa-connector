# Installation

#### 1. Integrate reference data into your Akeneo project.

We provide you a tutorial at: https://docs.akeneo.com/latest/cookbook/catalog_structure/creating_a_reference_data.html

This TESSA-Akeneo-Connector extension will alternatively also run with every other integration of reference data.

#### 2. Add a new field for the entity. It has to be a "string" type field.

_Example:_
```
<?php
# /src/Acme/Bundle/AppBundle/Entity/Color.php

namespace Acme\Bundle\AppBundle\Entity;

use Pim\Component\ReferenceData\Model\AbstractReferenceData;

/**
 * Acme Color entity
 */
class Color extends AbstractReferenceData
{
    //... other properties

    /** @var string */
    protected $assets;

    /**
     * @return string
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @param string $assets
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;
    }
}
```
   

_Example:_
```
 # /src/Acme/Bundle/AppBundle/Resources/config/doctrine/Color.orm.yml
    Acme\Bundle\AppBundle\Entity\Color:
        ...
        fields:
            ...
            assets:
                type: string
        lifecycleCallbacks: {  }
```

#### 3. Extend the form with the Tessa-Formular-Type
afterwards you have to tell the form that there has to be rendered a new field in the form. To achive this, please add to the from the following type of field:

`eikona_form_tessa`

_Example:_
```
# /src/Acme/Bundle/AppBundle/Form/Type.php

namespace Acme\AppBundle\Form\Type;

use Pim\Bundle\CustomEntityBundle\Form\Type\CustomEntityType;
use Symfony\Component\Form\FormBuilderInterface;

class ColorType extends CustomEntityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
                ->add(...)
                ->add(...)
                ->add(...)
                ->add('assets', 'eikona_form_tessa', array('maximumCount' => 9));
    }
}
```


The FormType has following options:

`maximumCount`
  - Type: `Integer`
  - Default: `null` (Unlimited) 
  - Amount of maximum assets for this field.
  
`allowedExtensions`
  - Type: `Array`
  - Default: `null` (Unrestricted)
  - Example: `['jpg','png']`
  - Restricts the possible collection of assets by file extension

If you decide to show thumbnails of the assets in the data grid, you have to extend the oro data grid. To do so, please attach the following at the end of the file Resources\config\datagrid\color.yml:

```
datagrid:
    color:
        ...
        columns:
            ...
            assets:
                label: TESSA Assets
                type: twig
                frontend_type: html
                template: EikonaTessaReferenceDataBundle:datagrid:thumbnail.html.twig
        ...
```

