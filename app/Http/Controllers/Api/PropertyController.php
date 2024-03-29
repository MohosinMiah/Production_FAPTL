<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Rental\Repositories\Contracts\PropertyInterface;


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

class PropertyController extends ApiController
{
    /**
     * @var PropertyInterface
     */
    protected $propertyRepository, $propertyUnitRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

    /**
     * PropertyController constructor.
     * @param PropertyInterface $propertyInterface
     * @param UnitInterface $unitRepository
     * @param LandlordInterface $landlordRepository
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(PropertyInterface $propertyInterface, PropertyUnitInterface $propertyUnitInterface, UnitInterface $unitRepository,
                                LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
    {
        $this->propertyRepository = $propertyInterface;
		$this->propertyUnitRepository = $propertyUnitInterface;

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
        $data = DB::table('faptl_properties')->where( 'deleted_at' , '=', NULL )->orderBy('name', 'ASC')->get();
        return  PropertyResource::collection( $data );

    }

    /**
     * @param PropertyRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(PropertyRequest $request)
    {
        
        
        $data = $request->all();
        $save = $this->propertyRepository->create($data);
        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Property has been created.');
        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $property = $this->propertyRepository->getById($uuid, $this->load);
        if(!$property)
            return $this->respondNotFound('Property not found.');

        return $this->respondWithData(new PropertyResource($property));
    }

    /**
     * @param PropertyRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function updateProperty(Request $request, $uuid)
    {
        $save = $this->propertyRepository->update($request->all(), $uuid);

        if (is_null($save)) {
            return $this->respondNotSaved('Error !! Property has been not updated.');
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
        
        if ($this->propertyRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Property has been deleted');
        }
        return $this->respondNotFound('Property not deleted');
    }



    /**
     * @param $uuid
     * @return mixed
     */
    public function propertyDetails( $uuid )
    {
        $propertyUnitByUnit = DB::table( 'faptl_property_units' )->where( 'property_id', $uuid )->get();
        if( !$propertyUnitByUnit )
        {
            return $this->respondNotFound('Property Unit not found.');
        }

        return PropertyUnitResource::collection( $propertyUnitByUnit );
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
            $this->propertyRepository->update(
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

			$data = $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
			return PropertyResource::collection($data);

           // return $this->propertyRepository->search($filter, ['extra_charges', 'units', 'late_fees', 'utility_costs', 'payment_methods']);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function periods(Request $request) {
        $data = $request->all();
        if (array_key_exists('id', $data)) {
            $property = $this->propertyRepository->getById($data['id']);
            return PeriodResource::collection($property->periods);
        }
        return [];
    }
}

