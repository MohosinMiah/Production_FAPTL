<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:07
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
					//  'agent_id' => 'exists:agents,id',
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
