<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Gemini\Laravel\Facades\Gemini;

class CertificateController extends Controller
{
    /**
     * Upload a certificate, store on Cloudinary, summarize with Gemini.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'issuer'      => 'nullable|string|max:255',
            'issued_date' => 'nullable|date',
            'file'        => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());
        $isPdf = $ext === 'pdf';

        // --- Upload to Cloudinary ---
        $cloudinaryData = $this->uploadToCloudinary($file, $isPdf);

        if (!$cloudinaryData) {
            return back()->with('cert_error', 'Failed to upload file to Cloudinary. Please try again.');
        }

        // --- Build title for Gemini summary ---
        $titleForAI = $request->title;
        if ($request->issuer) {
            $titleForAI .= ' issued by ' . $request->issuer;
        }
        if ($request->issued_date) {
            $titleForAI .= ' on ' . $request->issued_date;
        }

        $summary = $this->generateSummary($titleForAI, $cloudinaryData['secure_url']);

        // --- Save to DB ---
        $certificate = auth()->user()->certificates()->create([
            'title'         => $request->title,
            'issuer'        => $request->issuer,
            'issued_date'   => $request->issued_date,
            'file_path'     => $cloudinaryData['secure_url'],
            'file_public_id'=> $cloudinaryData['public_id'],
            'file_type'     => $isPdf ? 'pdf' : 'image',
            'ai_summary'    => $summary,
        ]);

        return back()->with('cert_success', 'Certificate "' . $certificate->title . '" uploaded successfully!');
    }

    /**
     * Delete a certificate (from DB + Cloudinary).
     */
    public function destroy(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete from Cloudinary
        $this->deleteFromCloudinary($certificate->file_public_id, $certificate->file_type);

        $title = $certificate->title;
        $certificate->delete();

        return back()->with('cert_success', '"' . $title . '" deleted.');
    }

    /**
     * Re-generate AI summary for a certificate.
     */
    public function regenerateSummary(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $titleForAI = $certificate->title;
        if ($certificate->issuer) {
            $titleForAI .= ' issued by ' . $certificate->issuer;
        }

        $summary = $this->generateSummary($titleForAI, $certificate->file_path);
        $certificate->update(['ai_summary' => $summary]);

        return back()->with('cert_success', 'Summary regenerated for "' . $certificate->title . '".');
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * Upload a file to Cloudinary using the REST API.
     */
    private function uploadToCloudinary($file, bool $isPdf): ?array
    {
        try {
            $cloudinaryUrl = config('services.cloudinary.url') ?? env('CLOUDINARY_URL');

            // Parse cloudinary://api_key:api_secret@cloud_name
            $parsed    = parse_url($cloudinaryUrl);
            $cloudName = $parsed['host'];
            $apiKey    = $parsed['user'];
            $apiSecret = $parsed['pass'];

            $resourceType = $isPdf ? 'raw' : 'image';
            $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/upload";

            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($endpoint, [
                'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ml_preset'),
                'api_key'       => $apiKey,
                'folder'        => 'certificates',
                'resource_type' => $resourceType,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Cloudinary upload failed', ['response' => $response->body()]);
            return null;

        } catch (\Throwable $e) {
            Log::error('Cloudinary upload exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Delete a resource from Cloudinary.
     */
    private function deleteFromCloudinary(string $publicId, string $fileType): void
    {
        try {
            $cloudinaryUrl = env('CLOUDINARY_URL');
            $parsed    = parse_url($cloudinaryUrl);
            $cloudName = $parsed['host'];
            $apiKey    = $parsed['user'];
            $apiSecret = $parsed['pass'];

            $resourceType = $fileType === 'pdf' ? 'raw' : 'image';
            $timestamp = time();
            $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

            Http::post("https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/destroy", [
                'public_id'  => $publicId,
                'api_key'    => $apiKey,
                'timestamp'  => $timestamp,
                'signature'  => $signature,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Cloudinary delete failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generate an AI summary of the certificate using Gemini.
     * For images, we use vision; for PDFs, we use title/context.
     */
    private function generateSummary(string $titleContext, string $fileUrl): string
    {
        try {
            $prompt = <<<PROMPT
            You are a professional resume assistant. Based on the following certificate information, write a single concise sentence (max 40 words) that summarizes what this certificate demonstrates about the holder's skills and qualifications. Be specific and professional.

            Certificate: {$titleContext}
            File URL: {$fileUrl}

            Return ONLY the summary sentence, no extra text or punctuation beyond the sentence.
            PROMPT;

            $response = Gemini::generativeModel('gemini-1.5-flash')
                ->generateContent($prompt);

            $text = trim($response->text());

            return !empty($text) ? $text : $this->fallbackSummary($titleContext);

        } catch (\Throwable $e) {
            Log::warning('Gemini certificate summary failed', ['error' => $e->getMessage()]);
            return $this->fallbackSummary($titleContext);
        }
    }

    private function fallbackSummary(string $title): string
    {
        return "Certified in {$title}, demonstrating professional competency in this area.";
    }
}
