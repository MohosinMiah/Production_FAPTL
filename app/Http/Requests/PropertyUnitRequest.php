<?php
/**
 * Created by VSCode.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * WhatsApp: ++8801773193256
 * Date: 25/06/2022
 * Time: 06:26
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PropertyUnitRequest extends BaseRequest
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
						'property_id'          => 'required',
						'type'                 => 'required',  
						'floor'                => 'required',  
						'rent'                 => 'required',  
						'total_room'           => 'required',
						'created_by'           => '',
						'updated_by'           => '',
						'deleted_by'           => ''
					];

					break;
				}
			case 'PUT':
			case 'PATCH':
				{
					$rules = [
						'property_id'          => 'required',
						'type'                 => 'required',  
						'floor'                => 'required',  
						'rent'                 => 'required',  
						'total_room'           => 'required',
						'created_by'           => '',
						'updated_by'           => '',
						'deleted_by'           => ''
					];
					break;
				}
			default:
				break;
		}

		return $rules;

	}
}
