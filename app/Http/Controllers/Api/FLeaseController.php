<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FLeaseRequest;
use App\Http\Resources\FLeaseResource;
use App\Rental\Repositories\Contracts\FLeaseInterface;


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
    protected $fLeaseRepository, $load, $accountRepository, $unitRepository, $landlordRepository, $invoiceRepository;

    /**
     * FLeaseController constructor.
     * @param FLeaseInterface $fLeaseInterface
     * @param UnitInterface $unitRepository
     * @param LandlordInterface $landlordRepository
     * @param InvoiceInterface $invoiceRepository
     */
    public function __construct(FLeaseInterface $fLeaseInterface, UnitInterface $unitRepository,
                                LandlordInterface $landlordRepository, InvoiceInterface $invoiceRepository)
    {
        $this->fLeaseRepository = $fLeaseInterface;
        $this->landlordRepository = $landlordRepository;
        $this->unitRepository = $unitRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->load = [
            'fLease_type',
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
        if(!$fLease)
            return $this->respondNotFound('FLease not found.');

        return $this->respondWithData(new FLeaseResource($fLease));
    }

    /**
     * @param FLeaseRequest $request
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function updateFLease(Request $request, $uuid)
    {
        $save = $this->fLeaseRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! FLease has been updated.');
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        
        if ($this->fLeaseRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! FLease has been deleted');
        }
        return $this->respondNotFound('FLease not deleted');
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request) {
        $data = $request->all();
        $fileNameToStore = '';
        // Upload logo
        if($request->hasFile('fLease_photo')) {
            $filenameWithExt = $request->file('fLease_photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('fLease_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('fLease_photo')->storeAs('photos', $fileNameToStore);
            $data['fLease_photo'] = $fileNameToStore;

            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'photos'.DIRECTORY_SEPARATOR. $fileNameToStore;

            // Update the fLease
            $this->fLeaseRepository->update(
                [
                    'fLease_photo' => $fileNameToStore
                ], $data['fLease_id']);
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

