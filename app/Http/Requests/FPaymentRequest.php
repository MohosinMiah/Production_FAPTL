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

class FPaymentRequest extends BaseRequest
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
						// 'unit_id'=> 'required', 
						'tenant_id'=> 'required', 
						'payment_purpose'=> 'required', 
						'payment_amount'=> 'required', 
						'payment_date'=> 'required', 					
					];

					break;
				}
			case 'PUT':
			case 'PATCH':
				{
					$rules = [
						'property_id'=> 'required', 
						// 'unit_id'=> 'required', 
						'tenant_id'=> 'required', 
						'payment_purpose'=> 'required', 
						'payment_amount'=> 'required', 
						'payment_date'=> 'required', 					
					];
					break;
				}
			default:
				break;
		}

		return $rules;

	}
}
