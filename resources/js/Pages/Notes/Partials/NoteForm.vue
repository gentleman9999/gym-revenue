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

            <!--            <input id="client_id" type="hidden" v-model="form.client_id" />-->
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
                @click="handleSubmit"
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
import { Inertia } from "@inertiajs/inertia";
import { useModal } from "@/Components/InertiaModal";

export default {
    components: {
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: {
        clientId: {
            type: String,
            required: true,
        },
        note: {
            type: Object,
        },
    },
    setup(props, context) {
        let note = props.note;
        let operation = "Update";
        if (!note) {
            note = {
                title: "",
                note: "",
                active: false,
                id: "",
                client_id: props.clientId,
            };
            operation = "Create";
        }

        const form = useGymRevForm({
            title: note.title,
            note: note.note,
            active: note.active,
            id: note.id,
            client_id: props.clientId,
        });

        let handleSubmit = () =>
            form.dirty().put(route("notes.update", note.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("notes.store"));
        }

        const modal = useModal();

        const handleClickCancel = () => {
            console.log("modal", modal.value);
            if (modal.value.close) {
                modal.value.close();
            } else {
                Inertia.visit(route("notes"));
            }
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
