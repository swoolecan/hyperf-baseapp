<?php

namespace backend\models;

class Managerlog extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_managerlog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function insert($runValidation = true, $attributes = null)
    {
        $values = $this->getDirtyAttributes($attributes);
        if (($primaryKeys = static::getDb()->schema->insert($this->tableName(), $values)) === false) {
            return false;
        }

        return true;
    }
}
