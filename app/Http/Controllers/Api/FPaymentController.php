<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FPaymentRequest;
use App\Http\Resources\FPaymentResource;
use App\Rental\Repositories\Contracts\FPaymentInterface;

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
use Carbon\Carbon;


class FPaymentController extends ApiController
{
	/**  FLease
	 * @var FPaymentInterface
	 */
	protected $fPaymentRepository, $load, $propertyRepository, $propertyUnitRepository, $landlordRepository, $invoiceRepository;

	/**
	 * FPaymentController constructor.
	 * @param FPaymentInterface $fLeaseInterface
	 * @param UnitInterface $unitRepository
	 * @param LandlordInterface $landlordRepository
	 * @param InvoiceInterface $invoiceRepository
	 */
	public function __construct(FPaymentInterface $fLeaseInterface, PropertyInterface $propertyInterface, PropertyUnitInterface $propertyUnitInterface
	// , UnitInterface $unitRepository,
	//                             LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository
								)
	{
		$this->fPaymentRepository = $fLeaseInterface;
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
		$data = DB::table('faptl_payments')->where( 'deleted_at' , '=', NULL )->get();
		return  FPaymentResource::collection( $data );

	}

	/**
	 * @param FPaymentRequest $request
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function store(FPaymentRequest $request)
	{
		
		
		$data = $request->all();
		$save = $this->fPaymentRepository->create($data);
		if (!is_null($save) && $save['error']) {
			return $this->respondNotSaved($save['message']);
		} else {
			return $this->respondWithSuccess('Success !! FPayment has been created.');
		}

	}

	/**
	 * @param $uuid
	 * @return mixed
	 */
	public function show($uuid)
	{
		$fLease = $this->fPaymentRepository->getById($uuid, $this->load);
		if( !$fLease )
		{
			return $this->respondNotFound('FPayment not found.');
		}

		return $this->respondWithData(new FPaymentResource($fLease));
	}

	/**
	 * @param FPaymentRequest $request
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function updatePayment( Request $request, $uuid )
	{
		$save = $this->fPaymentRepository->update( $request->all(), $uuid );

		
			return $this->respondWithSuccess('Success !! FPayment has been updated.');
		
	}


		/**
	 * @param $uuid
	 * @return array
	 * @throws \Exception
	 * Get Property unit list based on property ID 
	 */
	
	public function getPropertyUnitsByPropertyID( $uuid )
	{
		$data = [];
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
		if( $this->fPaymentRepository->delete( $uuid ) )
		{
			return $this->respondWithSuccess('Success !! FPayment has been deleted');
		}
		return $this->respondNotFound('FPayment not deleted');
	}



	public function filterList( Request $request)
	{
		$payments = DB::table('faptl_payments')->select('*');

		$property_id         =  $request->property_id;
		if( !empty( $property_id ) || $property_id != NUll || $property_id != null || $property_id != '' )
		{
			// $propertySql = ' property_id = ' . $property_id;
			$payments->where('property_id', $property_id);
		}
		$unit_id             =  $request->unit_id;
		if( !empty( $unit_id ) || $unit_id != NUll || $unit_id != null || $unit_id != '' )
		{
			// $unitSql = ' AND unit_id = ' . $unit_id;
			$payments->where('unit_id', $unit_id);

		}

		$paymentStartDate     =  $request->paymentStartDate;
		$paymentEndDate       =  $request->paymentEndDate;

		// $paymentStartDate = Carbon::createFromFormat( 'Y-m-d', $paymentStartDate );
        // $paymentEndDate = Carbon::createFromFormat( 'Y-m-d', $paymentEndDate );
		// die( date('Y-m-d',Carbon::now()) );
		if( !empty( $paymentStartDate ) || $paymentStartDate != NUll || $paymentStartDate != null || $paymentStartDate != '' )
		{	
			// $paymentStartDate = $paymentStartDate . 
			$payments->where('payment_date', '>=', $paymentStartDate);
		}

		if( !empty( $paymentEndDate ) || $paymentEndDate != NUll || $paymentEndDate != null || $paymentEndDate != '' )
		{
						// $paymentStartDate = $paymentStartDate . 

			$payments->where('payment_date', '<=', $paymentEndDate);

		}
		else{
			$payments->where('payment_date', '<=', date( 'Y-m-d', strtotime( Carbon::now() ) ));
		}

die( $payments->get() );
		$payments->get();




	}


	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function search(Request $request) {
		$data = $request->all();
		if (array_key_exists('filter', $data)) {
			$filter = $data['filter'];

			$data = $this->fPaymentRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return FPaymentResource::collection($data);

		// return $this->fPaymentRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
		}
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function periods(Request $request) {
		$data = $request->all();
		if (array_key_exists('id', $data)) {
			$fLease = $this->fPaymentRepository->getById($data['id']);
			return PeriodResource::collection($fLease->periods);
		}
		return [];
	}
}

