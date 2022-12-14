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
                @click="handleClickCancel"
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
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
//import { computed, ref } from "vue";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

export default {
    components: {
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: {
        note: {
            type: Object,
        },
    },
    setup(props, { emit }) {
        let note = props.note;
        let operation = "Update";
        if (!note) {
            note = {
                title: "",
                note: "",
                active: false,
                id: "",
            };
            operation = "Create";
        }

        const form = useGymRevForm({
            title: note.title,
            note: note.note,
            active: note.active,
            id: note.id,
        });

        const { mutate: createNote } = useMutation(mutations.note.create);
        const { mutate: updateNote } = useMutation(mutations.note.update);

        let handleSubmit = async () => {
            await updateNote({
                input: {
                    id: note.id,
                    title: form.title,
                    note: form.note,
                    active: form.active,
                },
            });
            handleClickCancel();
        };
        if (operation === "Create") {
            handleSubmit = async () => {
                await createNote({
                    input: {
                        title: form.title,
                        note: form.note,
                        active: form.active,
                    },
                });
                handleClickCancel();
            };
        }

        const handleClickCancel = () => {
            emit("close");
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            handleClickCancel,
        };
    },
};
</script>
