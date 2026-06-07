<?php

namespace App\Support;

class LandingFormFields
{
    public const ROLE_CUSTOM = 'custom';

    public const ROLE_NAME = 'name';

    public const ROLE_PHONE = 'phone';

    public const ROLE_CITY = 'city';

    public const ROLE_ADDRESS = 'address';

    public const ROLE_NOTE = 'note';

    public const SYSTEM_ROLES = [
        self::ROLE_NAME => 'name',
        self::ROLE_PHONE => 'phone',
        self::ROLE_CITY => 'city',
        self::ROLE_ADDRESS => 'address',
        self::ROLE_NOTE => 'note',
    ];

    public static function inferRole(array $field): string
    {
        if (!empty($field['field_role']) && $field['field_role'] !== self::ROLE_CUSTOM) {
            return $field['field_role'];
        }

        $id = $field['id'] ?? '';

        foreach (self::SYSTEM_ROLES as $role => $roleId) {
            if ($id === $roleId) {
                return $role;
            }
        }

        if (in_array($id, ['ville'], true)) {
            return self::ROLE_CITY;
        }

        if (in_array($id, ['adresse'], true)) {
            return self::ROLE_ADDRESS;
        }

        return self::ROLE_CUSTOM;
    }

    public static function cleanFields(array $formFields): array
    {
        $cleanedFields = [];

        foreach ($formFields as $field) {
            if (empty($field['id']) && empty($field['field_role'])) {
                continue;
            }

            $role = self::inferRole($field);
            $fieldId = $field['id'] ?? ('field_' . uniqid());

            if ($role !== self::ROLE_CUSTOM && isset(self::SYSTEM_ROLES[$role])) {
                $fieldId = self::SYSTEM_ROLES[$role];
            }

            $cleanedField = [
                'id' => $fieldId,
                'field_role' => $role,
                'type' => $field['type'] ?? 'text',
                'label' => $field['label'] ?? '',
                'label_fr' => $field['label_fr'] ?? '',
                'label_en' => $field['label_en'] ?? '',
                'label_ar' => $field['label_ar'] ?? '',
                'placeholder_fr' => $field['placeholder_fr'] ?? '',
                'placeholder_en' => $field['placeholder_en'] ?? '',
                'placeholder_ar' => $field['placeholder_ar'] ?? '',
                'required' => !empty($field['required']),
                'is_system' => $role !== self::ROLE_CUSTOM,
            ];

            if ($cleanedField['type'] === 'select' && !empty($field['options'])) {
                $cleanedField['options'] = $field['options'];
            }

            $cleanedFields[] = $cleanedField;
        }

        return $cleanedFields;
    }

    /**
     * @return array{city: ?string, address: ?string}
     */
    public static function extractLocationData(array $formFields, array $validated): array
    {
        $city = null;
        $address = null;

        foreach ($formFields as $field) {
            $fieldId = $field['id'] ?? null;
            if (!$fieldId) {
                continue;
            }

            $role = self::inferRole($field);
            $value = $validated[$fieldId] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            if ($role === self::ROLE_CITY) {
                $city = $value;
            }

            if ($role === self::ROLE_ADDRESS) {
                $address = $value;
            }
        }

        return [
            'city' => $city ?? $validated['city'] ?? $validated['ville'] ?? null,
            'address' => $address ?? $validated['address'] ?? $validated['adresse'] ?? null,
        ];
    }

    public static function systemFieldIds(): array
    {
        return array_values(self::SYSTEM_ROLES);
    }
}
