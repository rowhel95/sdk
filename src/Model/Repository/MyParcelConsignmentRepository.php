<?php
/**
 * The repository of a MyParcel consignment
 *
 * LICENSE: This source file is subject to the Creative Commons License:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *
 * If you want to add improvements, please create a fork in our GitHub:
 * https://github.com/myparcelnl
 *
 * @author      Reindert Vetter <reindert@myparcel.nl>
 * @copyright   2010-2016 MyParcel
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US  CC BY-NC-ND 3.0 NL
 * @link        https://github.com/myparcelnl/sdk
 * @since       File available since release 0.1.0
 */
namespace MyParcel\sdk\Model\Repository;


use MyParcel\sdk\Model\MyParcelConsignment;

/**
 * The repository of a MyParcel consignment
 *
 * Class MyParcelConsignmentRepository
 * @package MyParcel\sdk\Model\Repository
 */
class MyParcelConsignmentRepository extends MyParcelConsignment
{
    /**
     * Regular expression used to split street name from house number.
     *
     * For the full description go to:
     * @link https://gist.github.com/reindert-vetter/a90fdffe7d452f92d1c65bbf759f6e38
     */
    const SPLIT_STREET_REGEX = '~(?P<street>.*?)\s?(?P<street_suffix>(?P<number>[\d]+)-?(?P<number_suffix>[a-zA-Z/\s]{0,5}$|[0-9/]{0,5}$|\s[a-zA-Z]{1}[0-9]{0,3}$))$~';

    /**
     * Get entire street
     *
     * @return string Entire street
     */
    public function getFullStreet()
    {
        $fullStreet = $this->getStreet();

        if ($this->getNumber())
            $fullStreet .= ' ' . $this->getNumber();

        if ($this->getNumberSuffix())
            $fullStreet .= ' ' . $this->getNumberSuffix();

        return trim($fullStreet);
    }

    /**
     * Splitting a full NL address and save it in this class
     *
     * Required: Yes for international shipment
     *
     * @param $fullStreet
     *
     * @throws \Exception
     */
    public function setFullStreet($fullStreet)
    {
        if ($this->getCc() === null) {
            throw new \Exception('First set the country code with setCc() before running setFullStreet()');
        }

        if ($this->getCc() == 'NL') {
            $streetData = $this->_splitStreet($fullStreet);
            $this->setStreet($streetData['street']);
            $this->setNumber($streetData['number']);
            $this->setNumberSuffix($streetData['number_suffix']);
        } else {
            $this->setStreet($fullStreet);
        }
    }


    /**
     * Splits street data into separate parts for street name, house number and extension.
     *
     * @param string $fullStreet The full street name including all parts
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function _splitStreet($fullStreet)
    {
        $street = '';
        $number = '';
        $number_suffix = '';

        $fullStreet = preg_replace("/[\n\r]/", "", $fullStreet);
        $result = preg_match(self::SPLIT_STREET_REGEX, $fullStreet, $matches);

        if (!$result || !is_array($matches) || $fullStreet != $matches[0]) {
            if ($fullStreet != $matches[0]) {
                // Characters are gone by preg_match
                throw new \Exception('Something went wrong with splitting up address ' . $fullStreet);
            } else {
                // Invalid full street supplied
                throw new \Exception('Invalid full street supplied: ' . $fullStreet);
            }
        }

        if (isset($matches['street'])) {
            $street = $matches['street'];
        }

        if (isset($matches['number'])) {
            $number = $matches['number'];
        }

        if (isset($matches['number_suffix'])) {
            $number_suffix = trim($matches['number_suffix']);
        }

        $streetData = array(
            'street' => $street,
            'number' => $number,
            'number_suffix' => $number_suffix,
        );

        return $streetData;
    }

    /**
     * The total weight for all items in whole grams
     *
     * @todo get weight of all items
     *
     * @return int
     */
    public function getTotalWeight()
    {
        return;
    }
}