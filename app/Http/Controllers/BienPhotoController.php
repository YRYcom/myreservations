<?php

namespace App\Http\Controllers;

use App\Enums\ProfileType;
use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BienPhotoController extends Controller
{
    /**
     * Display the photo of a bien.
     */
    public function show(Bien $bien): Response
    {
        // Vérifier que l'utilisateur a le droit de voir ce bien
        if (!$this->userCanViewBien($bien)) {
            abort(403, 'Vous n\'avez pas accès à cette photo.');
        }

        // Vérifier que le bien a une photo
        if (!$bien->photo) {
            abort(404, 'Ce bien n\'a pas de photo.');
        }

        $path = storage_path('app/public/' . $bien->photo);

        // Vérifier que le fichier existe
        if (!file_exists($path)) {
            abort(404, 'Fichier non trouvé.');
        }

        // Générer ETag pour le cache
        $etag = md5_file($path);
        $lastModified = filemtime($path);

        // Vérifier si le client a déjà la version en cache
        $requestEtag = request()->header('If-None-Match');
        $requestModifiedSince = request()->header('If-Modified-Since');

        if ($requestEtag === $etag || 
            ($requestModifiedSince && strtotime($requestModifiedSince) >= $lastModified)) {
            return response('', 304);
        }

        // Déterminer le type MIME
        $mimeType = mime_content_type($path);

        // Retourner l'image avec headers de cache
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600', // Cache 1 heure
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
        ]);
    }

    /**
     * Check if the authenticated user can view this bien.
     */
    private function userCanViewBien(Bien $bien): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Admin peut tout voir (vérifier si l'utilisateur a le rôle admin)
        if ($user->hasRole('admin')) {
            return true;
        }

        // Gestionnaire peut voir ses biens
        if ($user->profile_type === ProfileType::Gestionnaire) {
            // Vérifier si l'utilisateur est gestionnaire de ce bien
            return $bien->users()
                ->wherePivot('profile', ProfileType::Gestionnaire->value)
                ->where('user_id', $user->id)
                ->exists();
        }

        // Utilisateur peut voir les biens de ses réservations
        return $user->reservations()
            ->where('bien_id', $bien->id)
            ->exists();
    }
}
