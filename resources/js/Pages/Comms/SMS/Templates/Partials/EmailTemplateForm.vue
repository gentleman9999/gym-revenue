<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <div class="form-control">
                    <label for="template" class="label">Template</label>
                    <textarea
                        v-model="form.markup"
                        id="template"
                        class="form-control w-full"
                        rows="4"
                        cols="40"
                    />
                    <jet-input-error
                        :message="form.errors.markup"
                        class="mt-2"
                    />
                </div>
            </div>
            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('comms.email-templates'))"
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
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
    },
    props: ["clientId", "template"],
    setup(props, context) {
        let template = props.template;
        let operation = "Update";
        if (!template) {
            template = {
                markup: null,
                name: null,
                // client_id: props.clientId
            };
            operation = "Create";
        }

        const form = useForm(template);

        let handleSubmit = () =>
            form.put(route("comms.email-templates.update", template.id));
        if (operation === "Create") {
            handleSubmit = () =>
                form.post(route("comms.email-templates.store"));
        }

        return { form, buttonText: operation, handleSubmit };
    },
};
</script>
