<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use App\Models\BannerAgent;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use AuthorizedCheck;

    /**
     * Display a listing of the resource.
     */
    use ImageUpload;

    public function index()
    {
        $auth = auth()->user();
        $this->OwnerAgentRoleCheck();
        $banners = $auth->hasPermission('owner_access') ?
            Banner::query()->owner()->latest()->get() :
            Banner::query()->agent()->latest()->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->OwnerAgentRoleCheck();

        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->OwnerAgentRoleCheck();
        $user = Auth::user();
        $isOwner = $user->hasRole('Owner');

        // Validate the request
        $request->validate([
            'mobile_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'desktop_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isOwner ? 'required' : 'nullable',
            'agent_id' => ($isOwner && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);

        $type = $request->type ?? 'single';
        $mobile_image = $this->handleImageUpload($request->mobile_image, 'banners');
        $desktop_image = $this->handleImageUpload($request->desktop_image, 'banners');

        if ($type === 'single') {
            $agentId = $isOwner ? $request->agent_id : $user->id;

            $banner = Banner::create([
                'mobile_image' => $mobile_image,
                'desktop_image' => $desktop_image,
            ]);
            BannerAgent::create([
                'banner_id' => $banner->id,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === 'all') {
            $banner = Banner::create([
                'mobile_image' => $mobile_image,
                'desktop_image' => $desktop_image,
            ]);
            foreach ($user->agents as $agent) {
                BannerAgent::create([
                    'banner_id' => $banner->id,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $this->OwnerAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);

        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        $this->OwnerAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }

        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $this->OwnerAgentRoleCheck();
        $user = Auth::user();
        $isOwner = $user->hasRole('Owner');

        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $request->validate([
            'mobile_image' => 'image|max:2048',
            'desktop_image' => 'image|max:2048',
        ]);

        $this->deleteImagesIfProvided($banner, $request);

        $this->UpdateData($request, $banner);

        if ($request->type === 'single') {
            $agentId = $isOwner ? $request->agent_id : $user->id;
            $banner->bannerAgents()->delete();
            BannerAgent::create([
                'agent_id' => $agentId,
                'banner_id' => $banner->id,
            ]);

        } elseif ($request->type === 'all') {
            foreach ($user->agents as $agent) {
                $banner->bannerAgents()->updateOrCreate(
                    ['agent_id' => $agent->id],
                    ['banner_id' => $banner->id]
                );
            }
        }

        return redirect(route('admin.banners.index'))->with('success', 'Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $this->OwnerAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);
        $this->handleImageDelete($banner->mobile_image, 'banners');
        $this->handleImageDelete($banner->desktop_image, 'banners');
        $banner->delete();

        return redirect()->back()->with('success', 'Banner Deleted.');
    }

    /**
     * Delete images if new ones are provided in the request.
     */
    private function deleteImagesIfProvided(Banner $banner, Request $request): void
    {
        if ($request->hasFile('mobile_image')) {
            $this->handleImageDelete($banner->mobile_image, 'banners');
        }

        if ($request->hasFile('desktop_image')) {
            $this->handleImageDelete($banner->desktop_image, 'banners');
        }
    }

    /**
     * Prepare data for updating the banner.
     */
    private function UpdateData(Request $request, Banner $banner): Banner
    {
        $updateData = ['description' => $request->input('description')];

        if ($request->hasFile('mobile_image')) {
            $updateData['mobile_image'] = $this->handleImageUpload($request->file('mobile_image'), 'banners');
        } else {
            $updateData['mobile_image'] = $banner->mobile_image;
        }

        if ($request->hasFile('desktop_image')) {
            $updateData['desktop_image'] = $this->handleImageUpload($request->file('desktop_image'), 'banners');
        } else {
            $updateData['desktop_image'] = $banner->desktop_image;
        }
        $banner->update($updateData);

        return $banner;
    }
}
