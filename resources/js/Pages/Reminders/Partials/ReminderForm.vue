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
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <jet-label for="description" value="Description" />
                <input
                    id="description"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.description"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.description"
                    class="mt-2"
                />
            </div>
            <div class="col-span-6">
                <jet-label for="remind_time" value="Reminder time" />
                <input
                    id="remind_time"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.remind_time"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.remind_time"
                    class="mt-2"
                />
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
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Inertia } from "@inertiajs/inertia";
import { useModal } from "@/Components/InertiaModal";

export default {
    components: {
        Button,
        defineComponent,
        JetFormSection,
        JetActionMessage,
        JetInputError,
        JetLabel,
        usePage,
    },
    props: {
        clientId: {
            type: String,
            required: true,
        },
        reminder: {
            type: Object,
        },
    },
    setup(props, context) {
        let reminder = props.reminder;
        let operation = "Update";
        if (!reminder) {
            reminder = {
                name: "",
                id: "",
                client_id: props.clientId,
                description: "",
                remind_time: 0,
            };
            operation = "Create";
        }

        const form = useGymRevForm({
            name: reminder.name,
            id: reminder.id,
            description: reminder.description,
            remind_time: reminder.remind_time,
            client_id: props.clientId,
        });

        let handleSubmit = () =>
            form.dirty().put(route("reminders.update", reminder.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("reminders.store"));
        }

        const modal = useModal();

        const handleClickCancel = () => {
            console.log("modal", modal.value);
            if (modal.value.close) {
                modal.value.close();
            } else {
                Inertia.visit(route("reminders"));
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
