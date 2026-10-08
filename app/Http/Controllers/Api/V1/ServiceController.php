<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    /**
     * Every service, in the order the site lists them.
     */
    public function index(): AnonymousResourceCollection
    {
        $services = Service::query()->ordered()->with('items')->get();

        return ServiceResource::collection($services);
    }

    /**
     * A single service, resolved by its slug.
     */
    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service->load('items'));
    }
}
