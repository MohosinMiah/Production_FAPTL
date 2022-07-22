<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FLeaseRequest;
use App\Http\Resources\FLeaseResource;
use App\Rental\Repositories\Contracts\FLeaseInterface;

use App\Http\Resources\PropertyResource;
use App\Rental\Repositories\Contracts\PropertyInterface;

use App\Rental\Repositories\Contracts\PropertyUnitInterface;
use App\Http\Resources\PropertyUnitResource;

use App\Http\Resources\PeriodResource;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FLeaseController extends ApiController
{
	/**  FLease
	 * @var FLeaseInterface
	 */
	protected $fLeaseRepository, $load, $propertyRepository, $propertyUnitRepository, $landlordRepository, $invoiceRepository;

	/**
	 * FLeaseController constructor.
	 * @param FLeaseInterface $fLeaseInterface
	 * @param UnitInterface $unitRepository
	 * @param LandlordInterface $landlordRepository
	 * @param InvoiceInterface $invoiceRepository
	 */
	public function __construct(FLeaseInterface $fLeaseInterface, PropertyInterface $propertyInterface, PropertyUnitInterface $propertyUnitInterface
	// , UnitInterface $unitRepository,
	//                             LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository
								)
	{
		$this->fLeaseRepository = $fLeaseInterface;
		$this->propertyRepository = $propertyInterface;
		$this->propertyUnitRepository = $propertyUnitInterface;
		// $this->landlordRepository = $landlordRepository;
		// $this->unitRepository = $unitRepository;
		// $this->invoiceRepository = $invoiceRepository;
		$this->load = [
		
		];
	}

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
		$data = DB::table('faptl_fleases')->get();
		return  FLeaseResource::collection( $data );

	}

	/**
	 * @param FLeaseRequest $request
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function store(FLeaseRequest $request)
	{
		
		
		$data = $request->all();
		$save = $this->fLeaseRepository->create($data);
		if (!is_null($save) && $save['error']) {
			return $this->respondNotSaved($save['message']);
		} else {
			return $this->respondWithSuccess('Success !! FLease has been created.');
		}

	}

	/**
	 * @param $uuid
	 * @return mixed
	 */
	public function show($uuid)
	{
		$fLease = $this->fLeaseRepository->getById($uuid, $this->load);
		if( !$fLease )
		{
			return $this->respondNotFound('FLease not found.');
		}

		return $this->respondWithData(new FLeaseResource($fLease));
	}

	/**
	 * @param FLeaseRequest $request
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function updateFLease( Request $request, $uuid )
	{
		$save = $this->fLeaseRepository->update( $request->all(), $uuid );

		if( !is_null( $save ) && $save['error'] )
		{
			return $this->respondNotSaved($save['message']);
		}
		else
		{
			return $this->respondWithSuccess('Success !! FLease has been updated.');
		}
	}


		/**
	 * @param $uuid
	 * @return array
	 * @throws \Exception
	 * Get Property unit list based on property ID 
	 */
	
	public function getPropertyUnitsByPropertyID( $uuid )
	{
		$data = PropertyUnitResource::collection( DB::table('faptl_property_units')->where( 'property_id', $uuid )->get() );
		return $data;

	}



	/**
	 * @param $uuid
	 * @return array
	 * @throws \Exception
	 */
	public function destroy( $uuid )
	{
		if( $this->fLeaseRepository->delete( $uuid ) )
		{
			return $this->respondWithSuccess('Success !! FLease has been deleted');
		}
		return $this->respondNotFound('FLease not deleted');
	}





	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function search(Request $request) {
		$data = $request->all();
		if (array_key_exists('filter', $data)) {
			$filter = $data['filter'];

			$data = $this->fLeaseRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return FLeaseResource::collection($data);

		// return $this->fLeaseRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
		}
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function periods(Request $request) {
		$data = $request->all();
		if (array_key_exists('id', $data)) {
			$fLease = $this->fLeaseRepository->getById($data['id']);
			return PeriodResource::collection($fLease->periods);
		}
		return [];
	}
}

