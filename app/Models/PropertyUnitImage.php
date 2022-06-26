<?php
/**
 * Created by VSCODE.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * Date: 10/05/2022
 * Time: 14:20
 */

namespace App\Models;

use App\Traits\SearchableTrait;

class PropertyUnitImage extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faptl_property_unit_images';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_id',
        'file_name',
        'alt_text',
        'isActive',
        'isFeatured',
        'created_by', 
        'updated_by',
        'deleted_by'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'faptl_property_unit_images.file_name' => 2,
            'faptl_property_unit_images.unit_id' => 1,
        ]
    ];
}
