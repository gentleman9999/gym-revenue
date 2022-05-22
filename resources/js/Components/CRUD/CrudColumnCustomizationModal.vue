<template>
    <daisy-modal
        v-bind="$attrs"
        :id="`${modelKey}-edit-columns`"
        ref="customizationModal"
    >
        <h1 class="text-lg">{{ modelName }} CRUD Customization</h1>
        <div class="flex flex-col gap-2">
            <p class="text-base-content text-sm text-opacity-50">
                Edit Columns
            </p>
            <div
                class="form-control flex flex-row gap-2"
                v-for="field in fields"
            >
                <input
                    :id="field.name"
                    type="checkbox"
                    :value="field.name"
                    v-model="form.columns"
                />
                <label :for="field.name">{{ field.label }}</label>
            </div>
            <button
                type="button"
                @click="handleSubmit"
                class="btn btn-primary mt-4"
            >
                Save
            </button>
        </div>
    </daisy-modal>
</template>

<script>
import DaisyModal from "@/Components/DaisyModal";
import { getFields } from "@/Components/CRUD/helpers/getFields";
import { defineComponent, ref } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { getCrudConfig } from "@/utils/getCrudCustomization";

export default defineComponent({
    components: {
        DaisyModal,
    },
    props: {
        table: {
            type: String,
            required: true,
        },
        modelKey: {
            type: String,
            required: true,
        },
        modelName: {
            type: String,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
    },
    setup(props, {}) {
        const config = getCrudConfig(props.modelKey);
        const form = useForm({
            table: props.modelKey,
            columns: [...config.value],
        });
        const customizationModal = ref(null);

        const open = () => customizationModal.value.open();
        const close = () => customizationModal.value.close();

        const fields = getFields(props);

        const handleSubmit = () =>
            form.post(route("crud-customize"), { onSuccess: close });
        return { fields, customizationModal, open, close, form, handleSubmit };
    },
});
</script>
