<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Create a new user
     *
     * @param array $userData
     * @return User
     */
    public function createUser(array $userData): User
    {
        return User::create($userData);
    }

    /**
     * Add user media files
     *
     * @param User $user
     * @param UploadedFile|null $avatar
     * @param UploadedFile|null $idImage
     * @param UploadedFile|null $licenseImage
     * @return void
     */
    public function addUserMedia(User $user, ?UploadedFile $avatar, ?UploadedFile $idImage, ?UploadedFile $licenseImage): void
    {
        if ($avatar) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($avatar)->toMediaCollection('avatar');
        }

        if ($idImage) {
            $user->clearMediaCollection('id_image');
            $user->addMedia($idImage)->toMediaCollection('id_image');
        }

        if ($licenseImage) {
            $user->clearMediaCollection('license_image');
            $user->addMedia($licenseImage)->toMediaCollection('license_image');
        }
    }

    /**
     * Create provider record
     *
     * @param User $user
     * @param array $providerData
     * @return \App\Models\Provider
     */
    public function createProvider(User $user, array $providerData)
    {
        // Create a new provider instance
        $provider = new \App\Models\Provider();
        $provider->user_id = $user->id;

        // Set translatable fields
        if (isset($providerData['commercial_name'])) {
            $provider->setTranslations('commercial_name', $providerData['commercial_name']);
            unset($providerData['commercial_name']);
        }

        // Set other fields
        foreach ($providerData as $key => $value) {
            $provider->$key = $value;
        }

        $provider->save();
        return $provider;
    }

    /**
     * Add provider media files
     *
     * @param \App\Models\Provider $provider
     * @param UploadedFile|null $logo
     * @param UploadedFile|null $residenceImage
     * @param UploadedFile|null $commercialImage
     * @param array|null $salonImages
     * @param bool $isUpdate - if true, only update provided files, don't clear existing ones
     * @return void
     */
    public function addProviderMedia($provider, ?UploadedFile $logo, ?UploadedFile $residenceImage, ?UploadedFile $commercialImage, ?array $salonImages = null, bool $isUpdate = false): void
    {
        if ($logo) {
            $provider->clearMediaCollection('logo');
            $provider->addMedia($logo)->toMediaCollection('logo');
        }

        // Only update residence_image if provided and not in update mode (for registration)
        if ($residenceImage && !$isUpdate) {
            $provider->clearMediaCollection('residence_image');
            $provider->addMedia($residenceImage)->toMediaCollection('residence_image');
        }

        // Only update commercial_register_image if provided and not in update mode (for registration)
        if ($commercialImage && !$isUpdate) {
            $provider->clearMediaCollection('commercial_register_image');
            $provider->addMedia($commercialImage)->toMediaCollection('commercial_register_image');
        }

        if ($salonImages && is_array($salonImages)) {
            $provider->clearMediaCollection('salon_images');
            foreach ($salonImages as $salonImage) {
                if ($salonImage instanceof UploadedFile) {
                    $provider->addMedia($salonImage)->toMediaCollection('salon_images');
                }
            }
        }
    }

    /**
     * Create provider bank account
     *
     * @param \App\Models\Provider $provider
     * @param array $bankData
     * @return \App\Models\ProviderBankAccount
     */
    public function createBankAccount($provider, array $bankData)
    {
        return $provider->bankAccount()->create($bankData);
    }

    /**
     * Register a new provider with all related data
     *
     * @param array $userData
     * @param array $providerData
     * @param array $bankData
     * @param array $mediaFiles
     * @return User
     */
    public function registerProvider(array $userData, array $providerData, array $bankData, array $mediaFiles): User
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = $this->createUser($userData);



            // Create provider
            $provider = $this->createProvider($user, $providerData);

            // Add provider media
            $this->addProviderMedia(
                $provider,
                $mediaFiles['logo'] ?? null,
                $mediaFiles['residence_image'] ?? null,
                $mediaFiles['commercial_register_image'] ?? null,
                $mediaFiles['salon_images'] ?? null,
                false // isUpdate flag - false for registration
            );

            // Create bank account
            $this->createBankAccount($provider, $bankData);

            // Send verification code
            $user->sendVerificationCode();

            DB::commit();
            return $user->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update provider profile with all related data
     *
     * @param User $user
     * @param array $userData
     * @param array $providerData
     * @param array $bankData
     * @param array $mediaFiles
     * @return User
     */
    public function updateProviderProfile(User $user, array $userData, array $providerData, array $bankData, array $mediaFiles): User
    {
        try {
            DB::beginTransaction();

            // Update user data
            if (!empty($userData)) {
                $user->update($userData);
            }

            // Update provider data
            if (!empty($providerData) && $user->provider) {
                $this->updateProvider($user->provider, $providerData);
            }

            // Update bank account data
            if (!empty($bankData) && $user->provider && $user->provider->bankAccount) {
                $this->updateBankAccount($user->provider->bankAccount, $bankData);
            }

            // Update provider media
            if (!empty($mediaFiles) && $user->provider) {
                $this->addProviderMedia(
                    $user->provider,
                    $mediaFiles['logo'] ?? null,
                    $mediaFiles['residence_image'] ?? null,
                    $mediaFiles['commercial_register_image'] ?? null,
                    $mediaFiles['salon_images'] ?? null,
                    true // isUpdate flag
                );
            }

            DB::commit();
            return $user->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update provider record
     *
     * @param \App\Models\Provider $provider
     * @param array $providerData
     * @return \App\Models\Provider
     */
    public function updateProvider($provider, array $providerData)
    {
        // Handle translatable fields
        if (isset($providerData['commercial_name'])) {
            $provider->setTranslations('commercial_name', $providerData['commercial_name']);
            unset($providerData['commercial_name']);
        }

        // Update other fields
        foreach ($providerData as $key => $value) {
            if (in_array($key, $provider->getFillable())) {
                $provider->$key = $value;
            }
        }

        $provider->save();
        return $provider;
    }

    /**
     * Update provider bank account
     *
     * @param \App\Models\ProviderBankAccount $bankAccount
     * @param array $bankData
     * @return \App\Models\ProviderBankAccount
     */
    public function updateBankAccount($bankAccount, array $bankData)
    {
        $bankAccount->update($bankData);
        return $bankAccount;
    }
}
