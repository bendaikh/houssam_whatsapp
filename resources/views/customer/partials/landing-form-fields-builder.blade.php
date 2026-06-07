@php
    $initialFieldsJson = isset($initialFields) ? json_encode($initialFields) : 'null';
@endphp

<div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6" x-data="formFieldsManager({{ $initialFieldsJson }})">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-white">حقول نموذج صفحة الهبوط</h3>
            <p class="text-sm text-gray-400 mt-1">تخصيص حقول نموذج الاتصال على صفحة الهبوط الخاصة بك</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="addPresetField('city')" :disabled="hasRole('city')"
                class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600/30 disabled:opacity-40 disabled:cursor-not-allowed text-cyan-300 rounded-lg text-sm transition">
                + المدينة
            </button>
            <button type="button" @click="addPresetField('address')" :disabled="hasRole('address')"
                class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600/30 disabled:opacity-40 disabled:cursor-not-allowed text-cyan-300 rounded-lg text-sm transition">
                + العنوان
            </button>
            <button type="button" @click="addField()"
                class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة حقل
            </button>
        </div>
    </div>

    <div class="space-y-4 mb-4">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 text-blue-300 text-sm">
            <strong>ملاحظة:</strong> استخدم <strong>دور الحقل</strong> لتحديد ما إذا كان الحقل يمثل المدينة أو العنوان أو ملاحظات. عند اختيار "المدينة"، سيتم حفظ القيمة في حقل المدينة في الطلبات وليس في الملاحظات.
        </div>
    </div>

    <input type="hidden" name="form_fields" x-model="formFieldsJson">

    <template x-if="fields.length === 0">
        <div class="text-center py-8 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>لم يتم إضافة حقول نموذج مخصصة بعد</p>
        </div>
    </template>

    <div class="space-y-4">
        <template x-for="(field, index) in fields" :key="field.id + '-' + index">
            <div class="bg-[#0a1628] border border-white/10 rounded-lg p-4" :class="field.field_role === 'city' ? 'border-cyan-500/40' : ''">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-semibold text-white" x-text="'الحقل ' + (index + 1)"></h4>
                        <span x-show="field.field_role && field.field_role !== 'custom'"
                            class="px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-500/20 text-cyan-300"
                            x-text="roleLabel(field.field_role)"></span>
                    </div>
                    <button type="button" @click="removeField(index)" class="text-red-400 hover:text-red-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">دور الحقل</label>
                        <select x-model="field.field_role" @change="applyRoleDefaults(field)"
                            class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="custom">حقل مخصص</option>
                            <option value="name">الاسم</option>
                            <option value="phone">الهاتف</option>
                            <option value="city">المدينة</option>
                            <option value="address">العنوان</option>
                            <option value="note">ملاحظات</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" x-show="field.field_role === 'city'">
                            سيتم حفظ هذا الحقل كـ "Ville" في الطلبات.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">نوع الحقل</label>
                        <select x-model="field.type"
                            class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="text">نص</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="tel">هاتف</option>
                            <option value="number">رقم</option>
                            <option value="textarea">نص متعدد الأسطر</option>
                            <option value="select">قائمة منسدلة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">مطلوب</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="field.required" class="sr-only peer" />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-sm text-gray-300">حقل مطلوب</span>
                        </label>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">التسمية (عربي)</label>
                        <input type="text" x-model="field.label_ar" placeholder="مثال: المدينة"
                            class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">النص التوضيحي (عربي)</label>
                        <input type="text" x-model="field.placeholder_ar" placeholder="مثال: اختر مدينتك"
                            class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>

                    <template x-if="field.type === 'select'">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">الخيارات (مفصولة بفاصلة)</label>
                            <input type="text" x-model="field.options_text"
                                @input="field.options = field.options_text.split(',').map(o => o.trim()).filter(o => o)"
                                placeholder="مثال: الدار البيضاء, الرباط, مراكش"
                                class="w-full px-4 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    if (window.__landingFormFieldsManagerRegistered) return;
    window.__landingFormFieldsManagerRegistered = true;

    const defaultFields = [
        { id: 'name', field_role: 'name', type: 'text', label_ar: 'الاسم', placeholder_ar: 'الاسم', required: true, is_system: true, options: [], options_text: '' },
        { id: 'phone', field_role: 'phone', type: 'tel', label_ar: 'الهاتف', placeholder_ar: 'الهاتف', required: true, is_system: true, options: [], options_text: '' },
        { id: 'note', field_role: 'note', type: 'textarea', label_ar: 'ملاحظات', placeholder_ar: 'ملاحظات', required: false, is_system: true, options: [], options_text: '' },
    ];

    const rolePresets = {
        city: { id: 'city', field_role: 'city', type: 'select', label_ar: 'المدينة', placeholder_ar: 'اختر مدينتك', required: true, is_system: true, options: ['الدار البيضاء', 'الرباط', 'مراكش', 'فاس', 'طنجة', 'أكادير'], options_text: 'الدار البيضاء, الرباط, مراكش, فاس, طنجة, أكادير' },
        address: { id: 'address', field_role: 'address', type: 'text', label_ar: 'العنوان', placeholder_ar: 'أدخل عنوانك', required: false, is_system: true, options: [], options_text: '' },
        name: { id: 'name', field_role: 'name', type: 'text', label_ar: 'الاسم', placeholder_ar: 'الاسم', required: true, is_system: true, options: [], options_text: '' },
        phone: { id: 'phone', field_role: 'phone', type: 'tel', label_ar: 'الهاتف', placeholder_ar: 'الهاتف', required: true, is_system: true, options: [], options_text: '' },
        note: { id: 'note', field_role: 'note', type: 'textarea', label_ar: 'ملاحظات', placeholder_ar: 'ملاحظات', required: false, is_system: true, options: [], options_text: '' },
    };

    const roleLabels = {
        name: 'الاسم', phone: 'الهاتف', city: 'المدينة', address: 'العنوان', note: 'ملاحظات', custom: 'مخصص',
    };

    Alpine.data('formFieldsManager', (initialFields = null) => ({
        fields: [],

        init() {
            this.fields = Array.isArray(initialFields) && initialFields.length > 0
                ? JSON.parse(JSON.stringify(initialFields))
                : JSON.parse(JSON.stringify(defaultFields));

            this.fields = this.fields.map(field => this.normalizeField(field));
        },

        normalizeField(field) {
            if (!field.field_role) {
                if (field.id === 'city' || field.id === 'ville') field.field_role = 'city';
                else if (field.id === 'address' || field.id === 'adresse') field.field_role = 'address';
                else if (field.id === 'name') field.field_role = 'name';
                else if (field.id === 'phone') field.field_role = 'phone';
                else if (field.id === 'note') field.field_role = 'note';
                else field.field_role = 'custom';
            }

            if (field.field_role !== 'custom' && rolePresets[field.field_role]) {
                field.id = rolePresets[field.field_role].id;
            }

            if (field.type === 'select' && field.options) {
                field.options_text = field.options.join(', ');
            } else {
                field.options = field.options || [];
                field.options_text = field.options_text || '';
            }

            return field;
        },

        get formFieldsJson() {
            return JSON.stringify(this.fields.map(field => {
                const copy = { ...field };
                if (copy.field_role !== 'custom' && rolePresets[copy.field_role]) {
                    copy.id = rolePresets[copy.field_role].id;
                }
                return copy;
            }));
        },

        roleLabel(role) {
            return roleLabels[role] || role;
        },

        hasRole(role) {
            return this.fields.some(field => field.field_role === role);
        },

        applyRoleDefaults(field) {
            if (field.field_role === 'custom') {
                field.is_system = false;
                return;
            }

            if (this.fields.filter(f => f !== field && f.field_role === field.field_role).length > 0) {
                alert('هذا الدور مستخدم بالفعل في حقل آخر.');
                field.field_role = 'custom';
                return;
            }

            const preset = rolePresets[field.field_role];
            if (!preset) return;

            field.id = preset.id;
            field.type = preset.type;
            field.is_system = true;
            if (!field.label_ar) field.label_ar = preset.label_ar;
            if (!field.placeholder_ar) field.placeholder_ar = preset.placeholder_ar;
            if (field.field_role === 'city' && (!field.options || field.options.length === 0)) {
                field.options = [...preset.options];
                field.options_text = preset.options_text;
            }
        },

        addPresetField(role) {
            if (this.hasRole(role)) return;
            this.fields.push(this.normalizeField(JSON.parse(JSON.stringify(rolePresets[role]))));
        },

        addField() {
            this.fields.push({
                id: 'field_' + Date.now(),
                field_role: 'custom',
                type: 'text',
                label_ar: '',
                placeholder_ar: '',
                required: false,
                is_system: false,
                options: [],
                options_text: '',
            });
        },

        removeField(index) {
            if (confirm('هل أنت متأكد من أنك تريد إزالة هذا الحقل؟')) {
                this.fields.splice(index, 1);
            }
        },
    }));
});
</script>
