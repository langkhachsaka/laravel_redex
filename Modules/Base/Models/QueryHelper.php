<?php
/**
 * Created by: TriNQ
 * Date: 11-04-2018
 * Time: 16:43 PM
 */

namespace Modules\Base\Models;

/**
 * @method filterWhere($column, $operator, $value)
 * @method whereFullLike($name = null, $value = null)
 */
trait QueryHelper
{
    public function scopeWhereFullLike($query, $name = null, $value = null)
    {
        return $this->scopeFilterWhere($query, $name, 'like', '%'.$value.'%');
    }

    /**
     * @param $query
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function scopeFilterWhere($query, $column, $operator, $value)
    {
        /*
         * Neu $value != null va $perator khong phai where like => ok
         * Hoac
         * Neu $value != null va $value != '%%' => 0k (tranh truong hop $value cua scopeWhereFullLike null)
         * */
        if ((!is_null($value) && $operator != 'like')||(!is_null($value) && $value != '%%')) {
            return $query->where($column, $operator, $value);
        }
        return $query;
    }

    public function scopeFilterOrWhere($query, $column, $operator, $value)
    {
        /*
         * Neu $value != null va $perator khong phai where like => ok
         * Hoac
         * Neu $value != null va $value != '%%' => 0k (tranh truong hop $value cua scopeWhereFullLike null)
         * */
        if ((!is_null($value) && $operator != 'like')||(!is_null($value) && $value != '%%')) {
            return $query->orWhere($column, $operator, $value);
        }
        return $query;
    }
}