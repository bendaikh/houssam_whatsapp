# Custom Landing Page Form Fields

## Overview
You can now customize the contact form on your product landing pages by adding custom fields like address, city, email, or any other information you need from customers.

## How to Use

### 1. Edit Your Product
Go to **Products** > **Edit Product** and scroll down to the **"Landing Page Form Fields"** section.

### 2. Add Custom Fields
Click the **"Add Field"** button to create a new form field.

### 3. Configure Each Field

For each field, you can set:

- **Field Type**: Choose from:
  - Text (single line input)
  - Email (validates email format)
  - Phone (for phone numbers)
  - Number (numeric input only)
  - Textarea (multi-line text)
  - Dropdown (select from options)

- **Required**: Toggle whether the field is mandatory

- **Labels**: Add labels in French, English, and Arabic
  - Example: "Adresse" (FR), "Address" (EN), "العنوان" (AR)

- **Placeholders**: Add placeholder text for each language

- **Options** (for Dropdown only): Add comma-separated options
  - Example: "Casablanca, Rabat, Marrakech, Tanger"

### 4. Save Your Product
Click **"Update Product"** to save your changes.

### 5. View on Landing Page
Visit your product's landing page - the custom fields will appear in the contact form between the Phone field and Note field.

## Default Fields

The following fields are always present and cannot be removed:
- **Name** (required)
- **Phone** (required)
- **Note** (optional)

## Examples

### Example 1: Add City Field
- Type: Dropdown
- Label (FR): Ville
- Label (EN): City
- Label (AR): المدينة
- Required: Yes
- Options: Casablanca, Rabat, Marrakech, Fès, Tanger

### Example 2: Add Address Field
- Type: Text
- Label (FR): Adresse
- Label (EN): Address
- Label (AR): العنوان
- Placeholder (FR): Entrez votre adresse complète
- Required: Yes

### Example 3: Add Email Field
- Type: Email
- Label (FR): Email
- Label (EN): Email
- Label (AR): البريد الإلكتروني
- Placeholder (FR): exemple@email.com
- Required: No

## Viewing Submitted Data

All custom field data is stored in the lead's `custom_fields` JSON column. You can view it in your leads/orders dashboard.

## Notes

- All custom fields support multi-language (French, English, Arabic)
- The form automatically validates based on the field type
- Custom fields work with both landing page themes (Theme 1 and Theme 2)
- You can add unlimited custom fields
- Fields can be reordered by removing and re-adding them in your desired order
