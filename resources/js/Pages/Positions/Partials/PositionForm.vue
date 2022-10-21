<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <jet-label for="departments" value="Department" />
                <multiselect
                    v-model="form.departments"
                    class="py-2"
                    id="departments"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        departments.data.map((department) => ({
                            label: department.name,
                            value: department.id,
                        }))
                    "
                    :classes="getDefaultMultiselectTWClasses()"
                />
                <jet-input-error
                    :message="form.errors.departments"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="handleCancel"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ operation }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";
import { useModal } from "@/Components/InertiaModal";
import { Inertia } from "@inertiajs/inertia";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import * as _ from "lodash";

const props = defineProps({
    clientId: {
        type: String,
        required: true,
    },
    position: {
        type: Object,
    },
    departments: {
        type: Object,
    },
});

let position = _.cloneDeep(props.position);
let operation = "Update";
if (!position) {
    position = {
        name: "",
        id: null,
        client_id: props.clientId,
        departments: [],
    };
    operation = "Create";
} else {
    position.departments = position.departments.map((dep) => dep.id);
}

const form = useGymRevForm(position);

let handleSubmit = () => {
    if (operation === "Create") {
        form.post(route("positions.store"));
    } else {
        form.put(route("positions.update", position.id));
    }
};

const modal = useModal();
const handleCancel = () => {
    if (modal?.value?.close) {
        modal.value.close();
        return;
    }
    Inertia.visit(route("positions"));
};
</script>
