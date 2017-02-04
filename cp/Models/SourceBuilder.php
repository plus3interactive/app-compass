<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Builder;

class SourceBuilder
{
    /**
     * render
     *
     * @param      FieldSource  $field_data  The field data
     *
     * @return     array        ( description_of_the_return_value )
     */
    public static function render(FieldSource $field_data)
    {

        $instance = new static;

        // if both type and id are set we want a single record
        if (!is_null($field_data->sourceable_id) && !is_null($field_data->sourceable_type)) {

            return $field_data->sourceable->toArray();

        // when the type is set but not the id we resolve the query using fieldsource's criteria
        } else if (!is_null($field_data->sourceable_type) && is_null($field_data->sourceable_id)) {

            $source_instance = new $field_data->sourceable_type();

            $instance->builder = $source_instance->newQuery();

            $instance->table = $source_instance->getTable();

            $b = $instance->parseCriteria($field_data->criteria);

            return $b->get()->toArray();

        // do we return the data stored in the fieldData?
        } else if ($field_data->data) {

            return $field_data->data;

        }
    }

    /**
     * parseCriteria field and build a query
     *
     * @param      array                                  $criteria  The criteria
     * @param      \Illuminate\Database\Eloquent\Builder  $builder         The builder
     * @param      array                                  $builder_params  The builder
     *                                                                     parameters
     *
     * @return     \Illuminate\Database\Eloquent\Builder  ( description_of_the_return_value )
     */
    public function parseCriteria(array $criteria)
    {
        foreach($criteria as $method => $single_criteria) {

            call_user_func_array([$this, $method], (array) $single_criteria);

        }

        return $this->builder;
    }

    /**
     * sort
     *
     * @param      array                                  $order  The order
     * @param      \Illuminate\Database\Eloquent\Builder  $builder  The builder
     *
     * @return     \Illuminate\Database\Eloquent\Builder  ( description_of_the_return_value )
     */
    public function sort($key, $direction)
    {
        return $this->builder->orderBy($key, $direction);
    }

    /**
     * select
     *
     * @param      array   $columns  The columns
     * @param      \Illuminate\Database\Eloquent\Builder  $builder  The builder
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function select()
    {
        return $this->builder->select(func_get_args());
    }

    /**
     * join
     *
     * @param      <type>  $destination_table  The destination table
     * @param      <type>  $origin_field       The origin field
     * @param      <type>  $destination_field  The destination field
     * @param      \Illuminate\Database\Eloquent\Builder  $builder  The builder
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function join($destination_table, $origin_field, $destination_field)
    {
        $origin = $this->table . '.' . $origin_field;

        $destination = $destination_table . '.' . $destination_field;

        $this->builder->join($destination_table, $origin, '=', $destination);

    }

    /**
     * where
     *
     * @param      <type>  $field   The field
     * @param      <type>  $clause  The clause
     */
    public function where($field, $clause)
    {
        $this->builder->where($field, $clause);
    }

    /**
     * limit
     *
     * @param      \Illuminate\Database\Eloquent\Builder  $builder  The builder
     * @param      integer                                $limit    The limit
     *
     * @return     <type>                                 ( description_of_the_return_value )
     */
    public function limit(int $limit)
    {
        return $this->builder->limit($limit);
    }

    function __call($method, $args) {

        $this->$method($args);

    }

}