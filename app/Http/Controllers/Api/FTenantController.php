<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FTenantRequest;
use App\Http\Resources\FTenantResource;
use App\Rental\Repositories\Contracts\FTenantInterface;


use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\UnitInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FTenantController extends ApiController
{
	/**
	 * @var TenantInterface
	 */
	protected $fTenantRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

	/**
	 * TenantController constructor.
	 * @param TenantInterface $TenantInterface
	 * @param UnitInterface $unitRepository
	 * @param LandlordInterface $landlordRepository
	 * @param InvoiceInterface $invoiceRepository
	 */
	public function __construct(FTenantInterface $ftenantInterface, UnitInterface $unitRepository,
								LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
	{
		$this->fTenantRepository = $ftenantInterface;
		$this->landlordRepository = $landlordRepository;
		$this->unitRepository = $unitRepository;
		$this->invoiceRepository = $invoiceRepository;
		$this->load = [
			// 'Tenant_type',
			// 'landlord',
			// 'payment_methods',
			// 'extra_charges',
			// 'late_fees',
			// 'utility_costs'
		];
	}

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{
		return  FTenantResource::collection( DB::table('faptl_tenants')->get());

	}

	/**
	 * @param FTenantRequest $request
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function store(FTenantRequest $request)
	{
		
		
		$data = $request->all();
		$save = $this->fTenantRepository->create($data);
		
		if (!is_null($save)) {
			return $this->respondNotSaved("Error !! Tenent Not Saved");
		} else {
			return $this->respondWithSuccess('Success !! Tenant has been created.');
		}
	}

	/**
	 * @param $uuid
	 * @return mixed
	 */
	public function show($uuid)
	{
		$Tenant = $this->fTenantRepository->getById($uuid, $this->load);
		if(!$Tenant)
			return $this->respondNotFound('Tenant not found.');

		return $this->respondWithData(new FTenantResource($Tenant));
	}

	/**
	 * @param FTenantRequest $request
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function updateTenant(Request $request, $uuid)
	{
		$save = $this->fTenantRepository->update($request->all(), $uuid);

		if (is_null($save) ) {
			return $this->respondNotSaved("Tenent did not update");
		} else

			return $this->respondWithSuccess('Success !! Tenant has been updated.');
	}

	/**
	 * @param $uuid
	 * @return array
	 * @throws \Exception
	 */
	public function destroy($uuid)
	{
		
		if ($this->fTenantRepository->delete($uuid)) {
			return $this->respondWithSuccess('Success !! Tenant has been deleted');
		}
		return $this->respondNotFound('Tenant not deleted');
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

			$data = $this->fTenantRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return FTenantResource::collection($data);

		// return $this->fTenantRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
		}
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function periods(Request $request) {
		$data = $request->all();
		if (array_key_exists('id', $data)) {
			$Tenant = $this->fTenantRepository->getById($data['id']);
			return PeriodResource::collection($Tenant->periods);
		}
		return [];
	}
}

