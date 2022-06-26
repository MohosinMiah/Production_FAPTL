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
     * @var PropertyInterface
     */
    protected $fTenantRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

    /**
     * PropertyController constructor.
     * @param PropertyInterface $propertyInterface
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
            'property_type',
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
        $newProperty = $this->fTenantRepository->create($data);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $property = $this->fTenantRepository->getById($uuid, $this->load);
        if(!$property)
            return $this->respondNotFound('Property not found.');

        return $this->respondWithData(new FTenantResource($property));
    }

    /**
     * @param FTenantRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function updateProperty(Request $request, $uuid)
    {
        $save = $this->fTenantRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
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
        
        if ($this->fTenantRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Property has been deleted');
        }
        return $this->respondNotFound('Property not deleted');
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request) {
        $data = $request->all();
        $fileNameToStore = '';
        // Upload logo
        if($request->hasFile('property_photo')) {
            $filenameWithExt = $request->file('property_photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('property_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('property_photo')->storeAs('photos', $fileNameToStore);
            $data['property_photo'] = $fileNameToStore;

            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $fileNameToStore;

            // Update the property
            $this->fTenantRepository->update(
                [
                    'property_photo' => $fileNameToStore
                ], $data['property_id']);
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
            $property = $this->fTenantRepository->getById($data['id']);
            return PeriodResource::collection($property->periods);
        }
        return [];
    }
}

