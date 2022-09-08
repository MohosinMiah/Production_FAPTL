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

class FTenantRequest extends BaseRequest
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
					'name'          => 'required',
					'date_of_birth'          => 'required',
					'gender'          => 'required',
					'tenant_number'          => 'required',
					'id_number'          => 'required',
					'phone'          => 'required',
					'email'          => 'required',

					'country'          => 'required',
					'state'          => 'required',
					'city'          => 'required',
					'postal_code'          => 'required',

					'emergency_contact_name'          => 'required',
					'emergency_contact_phone'          => 'required',
					'emergency_contact_email'          => 'required',

					'created_by'=> '',
					'updated_by'=> '',
					'deleted_by'=> ''
				];

				break;
			}
		case 'PUT':
		case 'PATCH':
			{
				$rules = [
					//  'agent_id' => 'exists:agents,id',
					'name'          => 'required',
					'date_of_birth'          => 'required',
					'gender'          => 'required',
					'tenant_number'          => 'required',
					'phone'          => 'required',

					'created_by'=> '',
					'updated_by'=> '',
					'deleted_by'=> ''
				];

				break;
			}
		default:
			break;
	}

	return $rules;

}
}
