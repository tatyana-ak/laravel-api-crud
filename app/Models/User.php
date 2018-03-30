<?php
/**
 * Created by PhpStorm.
 * User: tanchik
 * Date: 3/30/18
 * Time: 7:32 PM
 */
namespace App\Models;

use Elasticquent\ElasticquentTrait;
use Watson\Validating\ValidatingTrait;

/**
 * Class User
 * @package App\Models
 */
/**
 * @SWG\Definition(
 *     definition="user",
 *     required={
 *          "name",
 *          "email",
 *          "description",
 *          "status"
 *      },
 *     @SWG\Property(property="id",type="integer"),
 *     @SWG\Property(property="name",type="string"),
 *     @SWG\Property(property="email",type="string"),
 *     @SWG\Property(property="description",type="string"),
 *     @SWG\Property(property="status",type="integer", default=0, enum={0,1}),
 *     @SWG\Property(property="created_at",type="string"),
 *     @SWG\Property(property="updated_at",type="string"),
 *     @SWG\Property(property="deleted_at",type="string")
 * )
 */
class User extends AppModel
{
    use ElasticquentTrait;
    use ValidatingTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'description',
        'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Whether the model should inject it's identifier to the unique
     * validation rules before attempting validation. If this property
     * is not set in the model it will default to true.
     *
     * @var boolean
     */
    protected $injectUniqueIdentifier = true;

    protected $rules = [
        "name" => "required|string:50",
        "email" => "required|email|unique:users,email",
        "description" => "required|string:3500",
        "status" => "integer|in:0,1",
    ];

    /**
     * The elasticsearch settings.
     *
     * @var array
     */
    protected $indexSettings = [
        'analysis' => [
            'char_filter' => [
                'replace' => [
                    'type' => 'mapping',
                    'mappings' => [
                        '&=> and '
                    ],
                ],
            ],
            'filter' => [
                'word_delimiter' => [
                    'type' => 'word_delimiter',
                    'split_on_numerics' => false,
                    'split_on_case_change' => true,
                    'generate_word_parts' => true,
                    'generate_number_parts' => true,
                    'catenate_all' => true,
                    'preserve_original' => true,
                    'catenate_numbers' => true,
                ]
            ],
            'analyzer' => [
                'default' => [
                    'type' => 'custom',
                    'char_filter' => [
                        'html_strip',
                        'replace',
                    ],
                    'tokenizer' => 'whitespace',
                    'filter' => [
                        'lowercase',
                        'word_delimiter',
                    ],
                ],
            ],
        ],
    ];

    protected $mappingProperties = [
        'email' => [
            'type' => 'text',
            'analyzer' => 'standard',
        ],
        'description' => [
            'type' => 'text',
            'analyzer' => 'standard',
        ],
        'status' => [
            'type' => 'text',
            'analyzer' => 'standard',
        ]
    ];

    public function getRules()
    {
        return $this->rules;
    }
}
