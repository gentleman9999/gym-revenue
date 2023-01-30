<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="title" value="Title" />
                <input
                    id="title"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.title"
                    autofocus
                />
                <jet-input-error :message="form.errors.title" class="mt-2" />
            </div>
            <div class="col-span-6">
                <jet-label for="note" value="Note" />
                <input
                    :id="note"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.note"
                />
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>
            <div class="col-span-6">
                <jet-label for="active" value="Active" />
                <input :id="active" type="checkbox" v-model="form.active" />
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="$emit('close')"
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
                <!--this goes in disabled:  || !form.isDirty" -->
                {{ operation }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import * as _ from "lodash";
import { computed, ref } from "vue";
import { useGymRevForm } from "@/utils";
import { toastError, toastSuccess } from "@/utils/createToast";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

const emit = defineEmits(["close", "refresh"]);

const props = defineProps({
    note: {
        type: Object,
        default: {
            title: "",
            note: "",
            active: false,
        },
    },
});

const { mutate: createNote } = useMutation(mutations.note.create);
const { mutate: updateNote } = useMutation(mutations.note.update);

const operation = computed(() => {
    return props.note?.id ? "Update" : "Create";
});

const operFn = computed(() => {
    return operation.value === "Update" ? updateNote : createNote;
});

const note = _.cloneDeep(props.note);

const form = useGymRevForm({
    ...note,
});

let handleSubmit = async () => {
    try {
        let inputData = {
            title: form?.title,
            note: form?.note,
            active: form?.active,
            id: note?.id,
        };

        if (operation.value === "Create") delete inputData["id"];
        await operFn.value({
            input: {
                ...inputData,
            },
        });
        toastSuccess("Note" + operation.value + "d!");
        emit("close");
        emit("refresh");
    } catch (error) {
        toastError("There was a problem");
        console.log("Error saving note:", error);
    }
};
</script>
