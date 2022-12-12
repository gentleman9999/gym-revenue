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
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

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
        departments: [],
    };
    operation = "Create";
} else {
    position.departments = position.departments.map((dep) => dep.id);
}

const form = useGymRevForm(position);

const modal = useModal();
const { mutate: createPosition } = useMutation(mutations.position.create);
const { mutate: updatePosition } = useMutation(mutations.position.update);
let handleSubmit = async () => {
    if (operation === "Create") {
        await createPosition({
            name: form.name,
            departments: form.departments,
        });
        handleCancel();
    } else {
        await updatePosition({
            id: position.id,
            name: form.name,
            departments: form.departments,
        });
        handleCancel();
    }
};

const emit = defineEmits(["close"]);

const handleCancel = () => {
    emit("close");
};
</script>
