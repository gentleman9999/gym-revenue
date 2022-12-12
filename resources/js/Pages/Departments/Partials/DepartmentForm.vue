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
                <jet-label for="position" value="Position" />
                <multiselect
                    v-model="form.positions"
                    class="py-2"
                    id="positions"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        positions.data.map((position) => ({
                            label: position.name,
                            value: position.id,
                        }))
                    "
                    :classes="getDefaultMultiselectTWClasses()"
                />
                <jet-input-error :message="form.errors.position" class="mt-2" />
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
    department: {
        type: Object,
    },
    positions: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);
let department = _.cloneDeep(props.department);
let operation = "Update";
if (!department) {
    department = {
        name: "",
        id: null,
        positions: [],
    };
    operation = "Create";
} else {
    department.positions = department.positions.map((pos) => pos.id);
}

const form = useGymRevForm(department);

const modal = useModal();
const { mutate: createDepartment } = useMutation(mutations.department.create);
const { mutate: updateDepartment } = useMutation(mutations.department.update);
let handleSubmit = async () => {
    if (operation === "Create") {
        await createDepartment({
            name: form.name,
            positions: form.positions,
        });
        handleCancel();
    } else {
        await updateDepartment({
            id: department.id,
            name: form.name,
            positions: form.positions,
        });
        handleCancel();
    }
};

const handleCancel = () => {
    emit("close");
};
</script>
