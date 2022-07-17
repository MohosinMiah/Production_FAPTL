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

class FLeaseRequest extends BaseRequest
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
					
						'property_id'=> 'required', 
						'unit_id'=> 'required', 
						'lease_type'=> 'required', 
						'rent_amount'=> 'required', 
						'lease_start'=> 'required', 
						'isActive'=> 'required', 
					
					];

					break;
				}
			case 'PUT':
			case 'PATCH':
				{
					$rules = [
						'property_id'=> 'required', 
						'unit_id'=> 'required', 
						'lease_type'=> 'required', 
						'rent_amount'=> 'required', 
						'lease_start'=> 'required', 
						'isActive'=> 'required', 
						
					];
					break;
				}
			default:
				break;
		}

		return $rules;

	}
}
