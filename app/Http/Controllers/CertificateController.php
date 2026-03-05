<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'issuer'    => 'nullable|string|max:255',
            'issued_at' => 'nullable|date',
            'file'      => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
        ]);

        $url = $this->cloudinary->uploadCertificate(
            $request->file('file'),
            auth()->id(),
            $request->input('name')
        );

        auth()->user()->certificates()->create([
            'name'      => $request->input('name'),
            'issuer'    => $request->input('issuer'),
            'issued_at' => $request->input('issued_at'),
            'file_url'  => $url,
        ]);

        return redirect()->route('profile.edit')
            ->with('status', 'certificate-uploaded');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        abort_if($certificate->user_id !== auth()->id(), 403);

        // Delete from Cloudinary
        $resourceType = str_contains($certificate->file_url, '/raw/') ? 'raw' : 'image';
        $this->cloudinary->deleteByUrl($certificate->file_url, $resourceType);

        $certificate->delete();

        return redirect()->route('profile.edit')
            ->with('status', 'certificate-deleted');
    }
}
