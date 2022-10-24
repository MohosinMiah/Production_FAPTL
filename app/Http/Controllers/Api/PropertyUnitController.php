<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyUnitRequest;
use App\Http\Resources\PropertyUnitResource;
use App\Rental\Repositories\Contracts\PropertyUnitInterface;


use App\Http\Resources\PeriodResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PropertyUnitController extends ApiController
{
	/**
	 * @var PropertyInterface
	 */
	protected $propertyUnitRepository; //  $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository

	/**
	 * PropertyController constructor.
	 * @param PropertyInterface $propertyInterface
	 * @param UnitInterface $unitRepository
	 * @param LandlordInterface $landlordRepository
	 * @param InvoiceInterface $invoiceRepository
	 */
	public function __construct(PropertyUnitInterface $propertyUnitInterface, UnitInterface $unitRepository,
		LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
	{
		
		
		$this->propertyUnitRepository = $propertyUnitInterface;
		$this->landlordRepository = $landlordRepository;
		$this->unitRepository = $unitRepository;
		$this->invoiceRepository = $invoiceRepository;
		$this->load = [
			'landlord',
			'payment_methods',
			'extra_charges',
			'late_fees',
			'utility_costs'
		];
	}

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
	
		$data = PropertyUnitResource::collection( DB::table('faptl_property_units')->where( 'deleted_at' , '=', NULL )->orderBy('name', 'ASC')->get() );

		return $this->respondWithData( $data );
	}

	/**
	 * @param PropertyUnitRequest $request
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function store(PropertyUnitRequest $request)
	{
		$data = $request->all();
		$save = $this->propertyUnitRepository->create( $data );

		if( !is_null( $save ) && $save['error'] )
		{
            return $this->respondNotSaved( $save['message'] );
        }
		else
		{
            return $this->respondWithSuccess('Success !! Property Unit has been created.');
        }
	}

	/**
	 * @param $uuid
	 * @return mixed
	 */
	public function show($uuid)
	{
		$property = $this->propertyUnitRepository->getById( $uuid, $this->load);
		if(!$property)
			return $this->respondNotFound('Property Unit not found.');

		return $this->respondWithData(new PropertyUnitResource($property));
	}

	/**
	 * @param PropertyUnitRequest $request
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function updatePropertyUnit(Request $request, $uuid)
	{
	
		$save = $this->propertyUnitRepository->update($request->all(), $uuid);
		if (is_null($save) && $save['error']) {
			return $this->respondNotSaved($save['message']);
		} else

			return $this->respondWithSuccess('Success !! Property has been updated.');
	}

	/**
	 * @param $uuid
	 * @return array
	 * @throws \Exception
	 */
	public function destroy($uuid)
	{
		
		if ($this->propertyUnitRepository->delete($uuid)) {
			return $this->respondWithSuccess('Success !! Property Unit has been deleted');
		}
		return $this->respondNotFound('Property Unit Unit not deleted');
	}

	/**
	 * @param Request $request
	 */
	public function uploadPhoto(Request $request) {
		$data = $request->all();
		$fileNameToStore = '';
		// Upload logo
		if($request->hasFile('Property Unit_photo')) {
			$filenameWithExt = $request->file('Property Unit_photo')->getClientOriginalName();
			// Get just filename
			$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
			// Get just ext
			$extension = $request->file('Property Unit_photo')->getClientOriginalExtension();
			// Filename to store
			$fileNameToStore = $filename.'_'.time().'.'.$extension;
			$path = $request->file('Property Unit_photo')->storeAs('photos', $fileNameToStore);
			$data['Property Unit_photo'] = $fileNameToStore;

			$local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $fileNameToStore;

			// Update the property
			$this->propertyUnitRepository->update(
				[
					'Property Unit_photo' => $fileNameToStore
				], $data['Property Unit_id']);
		}
		return json_encode($fileNameToStore);
		// also, delete previous image file from server
	// $this->memberRepository->update(array_filter($data), $data['id']);
	}

	/**
	 * @param Request $request
	 */
	public function profilePic(Request $request)
	{
		$data = $request->all();
		if( array_key_exists('file_path', $data) ) {
			$file_path = $data['file_path'];
			$local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $file_path;
			return response()->file($local_path);
		}
		return $this->respondNotFound('file_path not provided');
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function search(Request $request) {
		$data = $request->all();
		if (array_key_exists('filter', $data)) {
			$filter = $data['filter'];

			$data = $this->propertyUnitRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return PropertyUnitResource::collection($data);

		// return $this->propertyUnitRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
		}
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function periods(Request $request) {
		$data = $request->all();
		if (array_key_exists('id', $data)) {
			$property = $this->propertyUnitRepository->getById($data['id']);
			return PeriodResource::collection($property->periods);
		}
		return [];
	}
}

