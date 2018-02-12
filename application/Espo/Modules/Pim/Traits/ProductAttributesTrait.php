<?php
declare(strict_types = 1);

namespace Espo\Modules\Pim\Traits;

/**
 * Trait for ProductAttributesTrait
 *
 * @author r.ratsun <r.ratsun@zinitsolutions.com>
 */
trait ProductAttributesTrait
{

    /**
     * Get Attributes from db
     *
     * @param string $productId
     * @return array
     */
    protected function getProductAttributes(string $productId): array
    {
        $result = [];

        // prepare where
        $where = '';
        $where .= $this->getAclWhereSql('Attribute', 'at');

        // prepare pdo
        $pdo = $this->getEntityManager()->getPDO();

        //array multiLang fields
        $multiLangFields     = $this->getMultiLangName('value');
        $multiLangTypeValues = $this->getMultiLangName('type_value');
        $values              = '';
        $typeValues          = '';

        //Add alias
        foreach ($multiLangFields as $key => $field) {
            $values     .= ', '.$field['db_field'].' AS '.$field['alias'];
            $typeValues .= ', '.$multiLangTypeValues[$key]['db_field']
                .' AS '
                .$multiLangTypeValues[$key]['alias'];
        }

        // prepare sql
        $sql = "SELECT
                  pav.id          AS productAttributeValueId,
                  at.id           AS attributeId,
                  at.name,
                  at.type,
                  pfa.is_required AS isRequired"
            .$typeValues
            .$values." ,
                  ag.id           AS attributeGroupId,
                  ag.name         AS attributeGroupName,
                  ag.sort_order   AS attributeGroupOrder,
                  IF(pal.id IS NOT NULL AND pfa.id IS NULL, 1, 0) AS isCustom
                FROM attribute AS at
                  JOIN product AS p ON p.id = ".$pdo->quote($productId)."
                  LEFT JOIN product_attribute_linker AS pal
                    ON at.id = pal.attribute_id AND p.id = pal.product_id AND pal.deleted = 0
                  LEFT JOIN product_family AS pf ON pf.id = p.product_family_id AND pf.deleted = 0
                  LEFT JOIN product_family_attribute AS pfa
                    ON pfa.attribute_id = at.id
                    AND pfa.product_family_id = p.product_family_id
                    AND pfa.deleted = 0
                    AND pf.deleted = 0
                  LEFT JOIN product_attribute_value AS pav
                    ON pav.product_id = p.id AND pav.attribute_id = at.id AND pav.deleted = 0
                  LEFT JOIN attribute_group AS ag ON ag.id = at.attribute_group_id AND ag.deleted = 0
                WHERE at.deleted = 0 AND (pal.id IS NOT NULL OR pfa.id IS NOT NULL)".$where;

        // execute
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // get multilang config
        $isMultilangActive = $this->getConfig()->get('isMultilangActive');
        $multilangConfig = $this->getConfig()->get('modules')['multilangFields'];

        foreach ($sth->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            // change attribute type if disable multilang
            if (!$isMultilangActive && !empty($multilangConfig[$row['type']])) {
                $row['type'] = $multilangConfig[$row['type']]['fieldType'];
            }

            $result[] = $row;
        }
        return $result;
    }

    /**
     * Return multiLang fields name in DB and alias
     *
     * @param $fieldName
     *
     * @return array
     */
    public function getMultiLangName(string $fieldName): array
    {
        // all fields
        $valueMultiLang = [];
        // prepare field name
        if (preg_match_all('/[^_]+/', $fieldName, $fieldParts, PREG_PATTERN_ORDER) > 1) {
            foreach ($fieldParts[0] as $key => $value) {
                $fieldAlias[] = $key > 0 ? ucfirst($value) : $value;
            }
            $fieldAlias = implode($fieldAlias);
        } else {
            $fieldAlias = $fieldName;
        }

        $fields['db_field'] = $fieldName;
        $fields['alias']    = $fieldAlias;
        $valueMultiLang[]   = $fields;
        if ($this->getConfig()->get('isMultilangActive')) {
            $languages = $this->getConfig()->get('inputLanguageList');
            foreach ($languages as $language) {
                $language = strtolower($language);
                $fields['db_field'] = $fieldName . '_' . $language;

                $alias = preg_split('/_/', $language);
                $alias = array_map('ucfirst', $alias);
                $alias = implode($alias);
                $fields['alias'] = $fieldAlias . $alias;
                $valueMultiLang[] = $fields;
                unset($fields);
            }
        }

        return $valueMultiLang;
    }
}