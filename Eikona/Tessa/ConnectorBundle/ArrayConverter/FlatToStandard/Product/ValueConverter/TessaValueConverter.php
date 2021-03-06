<?php
    /**
     * TessaValueConverter.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\ArrayConverter\FlatToStandard\Product\ValueConverter;

    use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\FieldSplitter;
    use Pim\Component\Connector\ArrayConverter\FlatToStandard\Product\ValueConverter\AbstractValueConverter;

    class TessaValueConverter extends AbstractValueConverter
    {

        public function __construct(FieldSplitter $fieldSplitter)
        {
            $this->supportedFieldType = ['eikona_catalog_tessa'];
            parent::__construct($fieldSplitter);
        }

        /**
         * Converts a value
         *
         * @param string $attributeCode
         * @param mixed  $data
         *
         * @return array
         */
        public function convert(array $attributeFieldInfo, $value)
        {
            if ('' !== $value) {
                $data = trim((string)$value);
            } else {
                $data = null;
            }

            $result = [
                    $attributeFieldInfo['attribute']->getCode() => [
                            [
                                    'locale' => $attributeFieldInfo['locale_code'],
                                    'scope'  => $attributeFieldInfo['scope_code'],
                                    'data'   => $data,
                            ],
                    ],
            ];

            $data = $result[$attributeFieldInfo['attribute']->getCode()][0]['data'];

            if (trim($data) === '') {
                return $result;
            }

            $convertedValues = array_map(
                    function ($url) {
                        $matches = null;
                        preg_match('/asset\_system\_id\=(\d+)/', $url, $matches);

                        return $matches[1];
                    },
                    explode(';', $data)
            );
            $result[$attributeFieldInfo['attribute']->getCode()][0]['data'] = implode(
                    ',',
                    $convertedValues
            );

            return $result;

        }

    }