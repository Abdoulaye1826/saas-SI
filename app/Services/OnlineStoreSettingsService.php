<?php

namespace App\Services;

use App\Models\OnlineStoreSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Gestion des réglages de la boutique en ligne (singleton), y compris
 * l'upload des images (logo, favicon, bannière, image Open Graph) — même
 * convention que ProductService::storeImage()/deleteImage().
 */
class OnlineStoreSettingsService
{
    private const IMAGE_FIELDS = [
        'logo' => 'logo_path',
        'favicon' => 'favicon_path',
        'hero_image' => 'hero_image_path',
        'og_image' => 'og_image_path',
    ];

    public function update(OnlineStoreSettings $settings, array $data, array $files = []): OnlineStoreSettings
    {
        foreach (self::IMAGE_FIELDS as $field => $column) {
            /** @var UploadedFile|null $file */
            $file = $files[$field] ?? null;

            if (! empty($data["remove_{$field}"]) && $settings->{$column}) {
                $this->deleteImage($settings->{$column});
                $data[$column] = null;
            }

            if ($file instanceof UploadedFile) {
                if ($settings->{$column}) {
                    $this->deleteImage($settings->{$column});
                }
                $data[$column] = $this->storeImage($file);
            }

            unset($data["remove_{$field}"], $data[$field]);
        }

        $settings->update($data);

        return $settings->fresh();
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('boutique', 'public');
    }

    private function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
