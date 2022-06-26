<?php
/**
 * Created by VSCODE.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * Date: 10/05/2022
 * Time: 14:20
 */

namespace App\Http\Requests;

class PropertyUnitImageRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
                break;
            }
            case 'POST':
            {
                $rules = [
                    'unit_id' => 'required',
                    'file_name' => 'required',
                ];

                break;
            }
            case 'PUT':
            case 'PATCH':
            
            default:
                break;
        }

        return $rules;

    }
}
